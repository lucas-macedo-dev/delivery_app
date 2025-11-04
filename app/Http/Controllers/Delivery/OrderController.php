<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Imports\OrdersImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Http\Resources\Delivery\OrderResource;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use App\Http\Resources\Delivery\ProductResource;
use App\Models\Product;

class OrderController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $products = Product::all();
        $products = ProductResource::collection($products);
        return view('delivery.orders', compact('products'));
    }

    public function showAll(): \Illuminate\Http\JsonResponse
    {
        try {
            $orders = Order::query()
                ->select(
                    ['id', 'ifood_order_number', 'order_date', 'total_amount_order', 'total_amount_received', 'status']
                )
                ->with(['items.product'])
                ->when(request()->filled('search'), function ($query) {
                    $query->where('id', 'like', '%' . request('search') . '%');
                })
                ->when(request()->filled('status') && request('status') !== 'all', function ($query) {
                    $query->where('status', request('status'));
                })
                ->when(request()->filled('origin') && request('origin') === 'ifood', function ($query) {
                    $query->where('ifood_id', '!=', null);
                })
                ->when(request()->filled('origin') && request('origin') === 'loja', function ($query) {
                    $query->where('ifood_id', null);
                })
                ->when(request()->filled('initial_date'), function ($query) {
                    $query->whereDate('order_date', '>=', request('initial_date'));
                })
                ->when(request()->filled('final_date'), function ($query) {
                    $query->whereDate('order_date', '<=', request('final_date'));
                })
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->withPath('/orders/showAll');

            $data = OrderResource::collection($orders);

            if ($data->isEmpty()) {
                return $this->response('Nenhum pedido encontrado', 200, $data);
            }

            $ordersResponse = [
                'orders' => $data,
                'links'  => [
                    'first' => $orders->url(1),
                    'last'  => $orders->url($orders->lastPage()),
                    'prev'  => $orders->previousPageUrl(),
                    'next'  => $orders->nextPageUrl(),
                ],
                'meta'   => [
                    'current_page' => $orders->currentPage(),
                    'from'         => $orders->firstItem(),
                    'last_page'    => $orders->lastPage(),
                    'path'         => $orders->path(),
                    'per_page'     => $orders->perPage(),
                    'last_item'    => $orders->lastItem(),
                    'total'        => $orders->total(),
                ]
            ];

            return $this->response('Pedidos encontrados', 200, $ordersResponse);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro ao buscar os pedidos.', 500, [
                'description' => 'Não foi possível recuperar os pedidos. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::with(['items.product'])->find($id);

        if (!$order) {
            return $this->error('Pedido não encontrado', 404, [
                'description' => 'O pedido com o ID especificado não existe.',
            ]);
        }

        return $this->response('Pedido encontrado', 200, new OrderResource($order));
    }

    public function import(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new OrdersImport, $request->file('file'));
            return $this->response('Arquivo importado com sucesso', 200);
        } catch (ExcelValidationException $e) {
            return $this->error('Erro na importação de arquivos.', 422, [
                'description' => 'O arquivo possui linhas inválidas. Verifique os dados e tente novamente.',
                'teste' => $e->getMessage()
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                return $this->error(
                    'Erro na importação de arquivos.',
                    409,
                    [
                        'description' => 'Alguns pedidos já estão cadastrados no sistema.',
                        'teste' => $e->getMessage()
                    ]
                );
            }

            return $this->error('Ocorreu um erro inesperado durante a importação.', 500, [
                'description' => 'Erro ao salvar no banco de dados.',
                'teste' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro inesperado durante a importação.', 500, [
                'description' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'ifoodOrderNumber'        => 'nullable|unique:orders,ifood_order_number',
            'ifoodId'                 => 'nullable|unique:orders,ifood_id',
            'orderDate'               => 'required|date',
            'customerAmount'          => 'required|numeric',
            'receivedAmount'          => 'required|numeric',
            'orderStatus'             => 'required|string',
            'orderItems'              => 'required|array',
            'orderItems.*.product_id' => 'required|exists:products,id',
            'orderItems.*.quantity'   => 'required|integer|min:1',
            'orderItems.*.price'      => 'required|numeric|min:0',
        ]);

        try {
            $order = Order::create([
                'ifood_order_number'    => $request->ifoodOrderNumber,
                'ifood_id'              => $request->ifoodId,
                'order_date'            => $request->orderDate,
                'total_amount_received' => $request->customerAmount,
                'total_amount_order'    => $request->receivedAmount,
                'status'                => $request->orderStatus,
            ]);

            if ($request->has('orderItems')) {
                foreach ($request->orderItems as $item) {
                    $order->items()->create([
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['price'],
                        'total_price' => $item['total'],
                    ]);
                }
            }

            return $this->response('Pedido criado com sucesso', 201, new OrderResource($order));
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                return $this->error(
                    'Erro ao criar pedido.',
                    409,
                    [
                        'description' => 'O número do pedido já está cadastrado no sistema.',
                    ]
                );
            }

            return $this->error('Ocorreu um erro ao criar o pedido.', 500, [
                'description' => 'Não foi possível salvar o pedido. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro ao criar o pedido.', 500, [
                'description' => 'Não foi possível salvar o pedido. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error('Pedido não encontrado', 404, [
                'description' => 'O pedido com o ID especificado não existe.',
            ]);
        }

        $request->validate([
            'ifoodOrderNumber'        => 'nullable|unique:orders,ifood_order_number,' . $id,
            'ifoodId'                 => 'nullable|unique:orders,ifood_id,' . $id,
            'orderDate'               => 'required|date',
            'customerAmount'          => 'required|numeric',
            'receivedAmount'          => 'required|numeric',
            'orderStatus'             => 'required|string',
            'orderItems'              => 'required|array',
            'orderItems.*.product_id' => 'required|exists:products,id',
            'orderItems.*.quantity'   => 'required|integer|min:1',
            'orderItems.*.price'      => 'required|numeric|min:0',
        ]);

        try {
            $order->update([
                'ifood_order_number'    => $request->ifoodOrderNumber,
                'ifood_id'              => $request->ifoodId,
                'order_date'            => $request->orderDate,
                'total_amount_received' => $request->customerAmount,
                'total_amount_order'    => $request->receivedAmount,
                'status'                => $request->orderStatus,
            ]);

            // Remove existing items
            $order->items()->delete();

            // Add new items
            if ($request->has('orderItems')) {
                foreach ($request->orderItems as $item) {
                    $order->items()->create([
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['price'],
                        'total_price' => $item['total'],
                    ]);
                }
            }

            return $this->response('Pedido atualizado com sucesso', 200, new OrderResource($order));
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                return $this->error(
                    'Erro ao atualizar pedido.',
                    409,
                    [
                        'description' => 'O número do pedido já está cadastrado no sistema.',
                    ]
                );
            }

            return $this->error('Ocorreu um erro ao atualizar o pedido.', 500, [
                'description' => 'Não foi possível salvar o pedido. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro ao atualizar o pedido.', 500, [
                'description' => 'Não foi possível salvar o pedido. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error('Pedido não encontrado', 404, [
                'description' => 'O pedido com o ID especificado não existe.',
            ]);
        }

        try {
            $order->items()->delete();
            $order->delete();

            return $this->response('Pedido excluído com sucesso', 200);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro ao excluir o pedido.', 500, [
                'description' => 'Não foi possível excluir o pedido. Por favor, tente novamente mais tarde.',
                'error'       => $e->getMessage(),
            ]);
        }
    }
}

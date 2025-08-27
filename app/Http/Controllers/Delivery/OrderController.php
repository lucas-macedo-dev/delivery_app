<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Imports\OrdersImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Http\Resources\Delivery\OrderResource;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class OrderController extends Controller
{
    public function index()
    {
        return view('delivery.orders');
    }


    public function showAll(): \Illuminate\Http\JsonResponse
    {
        try {
            $orders = Order::query()
                ->select(['id', 'ifood_order_number', 'order_date', 'total_amount_order', 'total_amount_received', 'status'])
                ->when(request()->filled('search'), function ($query) {
                    $query->where('id', 'like', '%' . request('search') . '%');
                })
                ->when(request()->filled('status') && request('status') !== 'all', function ($query) {
                    $query->where('status', request('status'));
                })
                ->orderBy('id', 'asc')
                ->paginate(20)
                ->withPath('/delivery/orders/showAll');

            $data = OrderResource::collection($orders);

            if ($data->isEmpty()) {
                return $this->response('Nenhum pedido encontrado', 404, $data);
            }

            $ordersResponse = [
                'orders' => $data,
                'links' => [
                    'first' => $orders->url(1),
                    'last' => $orders->url($orders->lastPage()),
                    'prev' => $orders->previousPageUrl(),
                    'next' => $orders->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'from' => $orders->firstItem(),
                    'last_page' => $orders->lastPage(),
                    'path' => $orders->path(),
                    'per_page' => $orders->perPage(),
                    'last_item' => $orders->lastItem(),
                    'total' => $orders->total(),
                ]
            ];

            return $this->response('Pedidos encontrados', 200, $ordersResponse);
        } catch (\Exception $e) {
            return $this->error('Ocorreu um erro ao buscar os pedidos.', 500, [
                'description' => 'Não foi possível recuperar os pedidos. Por favor, tente novamente mais tarde.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error('Pedido não encontrado', 404, [
                'description' => 'O pedido com o ID especificado não existe.',
            ]);
        }

        return $this->response('Pedido encontrado', 200, new OrderResource($order));
    }

    public function import(Request $request)
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
            ]);
        } catch (QueryException $e) {

            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                return $this->error(
                    'Erro na importação de arquivos.',
                    409,
                    [
                        'description' => 'Alguns pedidos já estão cadastrados no sistema.',
                    ]
                );
            }

            return $this->error('Ocorreu um erro inesperado durante a importação.', 500, [
                'description' => 'Erro ao salvar no banco de dados.',
            ]);
        } catch (\Exception $e) {

            return $this->error('Ocorreu um erro inesperado durante a importação.', 500, [
                'description' => $e->getMessage(),
            ]);
        }
    }
}

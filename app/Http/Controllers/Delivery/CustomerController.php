<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Customer;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\Delivery\CustomerResource;

class CustomerController extends Controller
{
    use HttpResponse;

    public function index()
    {
        return view('delivery.customers');
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->error('Cliente não encontrado', 404);
        }

        return $this->response('Cliente encontrado', 200, new CustomerResource($customer));
    }

    public function showAll(): \Illuminate\Http\JsonResponse
    {
        $customers = Customer::query()
            ->select(['id', 'name', 'cpf', 'phone', 'created_at', 'updated_at'])
            // ->withCount('orders') TODO MELHORAR RELACIONAMENTO
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withPath('/delivery/customers/showAll');

        $data = CustomerResource::collection($customers);

        if ($data->isEmpty()) {
            return $this->response('Nenhum cliente encontrado', 404, $data);
        }

        $customers = [
            'customers' => $data,
            'links' => [
                'first' => $customers->url(1),
                'last' => $customers->url($customers->lastPage()),
                'prev' => $customers->previousPageUrl(),
                'next' => $customers->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $customers->currentPage(),
                'from' => $customers->firstItem(),
                'last_page' => $customers->lastPage(),
                'path' => $customers->path(),
                'per_page' => $customers->perPage(),
                'last_item' => $customers->lastItem(),
                'total' => $customers->total(),
            ]
        ];

        return $this->response('Clientes encontrados', 200, $customers);
    }

    public function store(CustomerRequest $request)
    {
        try {
            $created = Customer::query()->create([
                'name'              => $request->name,
                'cpf'             => $request->cpf,
                'phone'        => $request->phone
            ]);

            if ($created->save()) {
                return $this->response('Cliente cadastrado', 200, new CustomerResource($created));
            }

            return $this->error('Não foi possivel cadastrar o cliente', 400);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return $this->error('Cliente não encontrado', 404);
        }


        $updated = $customer->update([
            'name'              => $request->name,
            'cpf'             => $request->cpf,
            'phone'        => $request->phone
        ]);

        if ($updated) {
            return $this->response('Cliente Atualizado', 200, new CustomerResource($customer));
        } else {
            return $this->error('Cliente não atualizado', 400);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return $this->error('Cliente não encontrado', 404);
        }

        if ($customer->delete()) {
            return $this->response('Cliente deletado', 200);
        } else {
            return $this->error('Não foi possível deletar o cliente', 400);
        }
    }
}

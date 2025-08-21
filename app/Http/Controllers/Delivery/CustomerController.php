<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Resources\Delivery\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Storage;

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
        $products = Customer::paginate(20)->withPath('/delivery/customers/showAll');

        $data = CustomerResource::collection($products);

        if ($data->isEmpty()) {
            return $this->response('Nenhum cliente encontrado', 404, $data);
        }

        $products = [
            'customers' => $data,
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $products->currentPage(),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'path' => $products->path(),
                'per_page' => $products->perPage(),
                'last_item' => $products->lastItem(),
                'total' => $products->total(),
            ]
        ];

        return $this->response('Clientes encontrados', 200, $products);
    }

    public function store(Request $request)
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

    public function update(Request $request, $id)
    {
        // Logic to update an existing customer
    }

    public function destroy($id)
    {
        // Logic to delete a customer
    }
}

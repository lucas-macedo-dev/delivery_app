<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Delivery\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('delivery.products');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $extension = $request->image->extension();
                $imageName = md5($request->image->getClientOriginalName() . strtotime("now")) . "." . $extension;
                Storage::disk('delivery')->putFileAs('/', $request->image, $imageName);
            }

            $created = Product::query()->create([
                'name'              => $request->name,
                'price'             => $request->price,
                'image_name'        => $imageName ?? null,
                'available'         => $request->available ? 1 : 0,
                'unit_measure'      => $request->unit_measure,
                'stock_quantity'    => $request->stock,
                'category'          => $request->category
            ]);

            if ($created->save()) {
                return $this->response('Produto Criado', 200, new ProductResource($created));
            }

            return $this->error('Não foi possivel criar o produto', 400);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $product = Product::query()->find($id);

            if ($product) {
                return $this->response('Produto Encontrado', 200, new ProductResource($product));
            } else {
                return $this->error('Produto nãoo encontrado', 404);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function showAll(): \Illuminate\Http\JsonResponse
    {
        $products = Product::paginate(20)->withPath('/delivery/products/showAll');

        $data = ProductResource::collection($products);

        if ($data->isEmpty()) {
            return $this->error('Nenhum produto encontrado', 404);
        }

        $products = [
            'products' => $data,
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

        return $this->response('Produtos encontrados', 200, $products);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return $this->error('Produto não encontrado', 404);
        }

        $updateData = [
            'name'              => $request->name,
            'price'             => $request->price,
            'available'         => $request->available ? 1 : 0,
            'unit_measure'      => $request->unit_measure,
            'stock_quantity'    => $request->stock,
            'category'          => $request->category
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $extension = $request->image->extension();
            $imageName = md5($request->image->getClientOriginalName() . strtotime("now")) . "." . $extension;

            Storage::disk('delivery')->putFileAs('/', $request->image, $imageName);

            if ($product->image_name && Storage::disk('delivery')->exists($product->image_name)) {
                Storage::disk('delivery')->delete($product->image_name);
            }

            $updateData['image_name'] = $imageName;
        }
        $updated = $product->update($updateData);

        if ($updated) {
            $product->refresh();
            return $this->response('Produto Atualizado', 200, new ProductResource($product));
        } else {
            return $this->error('Produto não atualizado', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::query()->find($id);

            if (!$product) {
                return $this->error('Produto não encontrado', 404);
            }

            if ($product->image_name && Storage::disk('delivery')->exists($product->image_name)) {
                Storage::disk('delivery')->delete($product->image_name);
            }

            if ($product->delete()) {
                return $this->response('Produto deletado com sucesso', 200);
            }

            return $this->error('Product não deletado', 400);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}

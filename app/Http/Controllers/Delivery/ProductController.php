<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Delivery\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()->get();

        return view('delivery.products', ['products' => $products]);
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
                'stock_quantity'    => $request->stock
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
                return $this->response('Product Found', 200, new ProductResource($product));
            } else {
                return $this->error('Product Not Found', 404);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
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

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $extension = $request->image->extension();
            $imageName = md5($request->image->getClientOriginalName() . strtotime("now")) . "." . $extension;
            Storage::disk('delivery')->putFileAs('/', $request->image, $imageName);
        }

        $updated = $product->update([
            'name'              => $request->name,
            'price'             => $request->price,
            'image_name'        => $imageName ?? null,
            'available'         => $request->available ? 1 : 0,
            'unit_measure'      => $request->unit_measure,
            'stock_quantity'    => $request->stock
        ]);

        if ($updated) {
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

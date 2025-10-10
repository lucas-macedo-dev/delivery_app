<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Delivery\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
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
    public function store(ProductRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $created = Product::query()->create([
                'name'           => $request->name,
                'price'          => $request->price,
                'available'      => $request->available ? 1 : 0,
                'unit_measure'   => $request->unit_measure,
                'stock_quantity' => $request->stock,
                'category'       => $request->category
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
        $products = Product::with('categories')->paginate(20)->withPath('/delivery/products/showAll');

        $data = ProductResource::collection($products);

        if ($data->isEmpty()) {
            return $this->error('Nenhum produto encontrado', 404);
        }

        $products = [
            'products' => $data,
            'meta'     => [
                'current_page' => $products->currentPage(),
                'from'         => $products->firstItem(),
                'last_page'    => $products->lastPage(),
                'path'         => $products->path(),
                'per_page'     => $products->perPage(),
                'last_item'    => $products->lastItem(),
                'total'        => $products->total(),
            ]
        ];

        return $this->response('Produtos encontrados', 200, $products);
    }

    public function loadCategories()
    {
        return $this->response('Categorias encontradas', 200, Category::all()->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return $this->error('Produto não encontrado', 404);
        }

        $updateData = [
            'name'           => $request->name,
            'price'          => $request->price,
            'available'      => $request->available == 'true' ? 1 : 0,
            'unit_measure'   => $request->unit_measure,
            'stock_quantity' => $request->stock,
            'category'       => $request->category
        ];

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
    public function destroy(string $id): \Illuminate\Http\JsonResponse
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

    public function mostSaledProduct(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::query()
                ->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->selectRaw('products.*, SUM(order_items.quantity) as total_quantity')
                ->groupBy('products.id')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();
    }
}

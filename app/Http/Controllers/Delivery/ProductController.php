<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|max:50',
                'description' => 'required',
                'price'       => 'required|numeric',
                'image'       => 'required|image',
                'available'   => 'required|in:true,false,1,0',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors());
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $extension = $request->image->extension();
                $imageName = md5($request->image->getClientOriginalName() . strtotime("now")) . "." . $extension;
                Storage::disk('delivery')->putFileAs('/', $request->image, $imageName);
            }

            $created = Product::query()->create([
                'name'        => $request->name,
                'description' => $request->description,
                'price'       => $request->price,
                'image_name'  => $imageName ?? null,
                'available'   => $request->available ? 1 : 0,
            ]);

            if ($created->save()) {
                return $this->response('Invoice Created', 200, new ProductResource($created));
            }

            return $this->error('Invoice not Created', 400);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

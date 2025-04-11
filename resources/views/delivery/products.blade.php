@extends('delivery.home')
@vite(['resources/js/delivery/products.js'])
@section('content')
    <div class="container">
        <h1>{{__('Products')}}</h1>
        <hr class="my-4">
        <div class="text-end mb-3">
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#products_modal">
                <i class="fa-solid fa-plus"></i>&nbsp;{{__('products.add_product')}}
            </button>
        </div>
        @if ($products->isEmpty())
            <div class="alert alert-danger" role="alert">
                {{__('products.product_not_found')}}
            </div>
        @else
            @foreach ($products as $product)
                <div class="row align-items-center border mb-3 mx-1 p-2 rounded text-center text-md-start">
                    <div class="col-12 col-md-2 ">
                        <img src="{{asset('storage/delivery/'. $product->image_name)}}"
                             class="card-img-top figure-img img-fluid rounded"
                             alt="{{$product->image_name}}"
                             style="max-width: 200px;">
                    </div>
                    <div class="col col-md-8">
                        <h4><strong>{{__('products.name')}}:</strong> {{$product->name}}</h4>
                        <h5><strong>{{__('products.price')}}:</strong> R$ {{number_format($product->price , 2, ',', '.')}}</h5>
                        <p><strong>{{__('products.description')}}:</strong> {{$product->description}}</p>
                    </div>
                    <div class="col-12 col-md-2 text-center">
                        <a id="btn_edit_{{$product->id}}" class="btn btn-lg mb-1 btn-warning btn_edit_product">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-lg mb-1 btn-danger">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
        <!-- Modal -->
        <div class="modal fade" id="products_modal" tabindex="-1" aria-labelledby="productsLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog  modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="productsLabel">{{__('products.add_product')}}</h1>
                        <button type="button" class="btn-close btn_close_modal" data-bs-dismiss="modal"
                                onclick="closeProductModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('products.store')}}" method="POST">
                            @csrf
                            <div class="row justify-content-center text-center d-none" id="image_preview">
                                <div class="col-auto">
                                    <h3><i class="fa-solid fa-image"></i>&nbsp;{{__('products.preview')}}</h3>
                                    <div>
                                        <img src="#" id="category-img-tag" class="figure-img img-fluid rounded"
                                             style="max-width: 300px;"/>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="product_name" class="form-label">{{__('products.name')}}</label>
                                <input type="text" class="form-control" id="product_name" name="product_name">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="product_price" name="product_price"
                                       aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text">.00</span>
                            </div>
                            <div class="mb-3">
                                <label for="product_description"
                                       class="form-label">{{__('products.description')}}</label>
                                <textarea class="form-control" id="product_description" rows="3"
                                          name="product_description"></textarea>
                            </div>
                            <div class="mx-0 rounded mb-3 row border">
                                <div class="align-content-center col d-flex flex-wrap justify-content-center">
                                    <div class="my-3">
                                        <label for="product_image" class="form-label">
                                            <i class="fa-solid fa-image"></i>&nbsp;{{__('products.image')}}
                                        </label>
                                        <input class="bg-light-subtle form-control p-1 rounded" type="file"
                                               id="product_image" name="product_image">
                                    </div>
                                </div>
                                <div
                                    class="align-content-center col d-flex flex-wrap justify-content-center border-start">
                                    <div class="form-check form-switch my-3">
                                        <input class="form-check-input form-select-lg" type="checkbox" role="switch"
                                               id="product_available" name="product_available">
                                        <label class="form-check-label"
                                               for="product_available">{{__('products.available')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 text-center">
                                <button type="reset" class="btn btn-secondary" class="btn_close_modal" onclick="closeProductModal()"
                                        data-bs-dismiss="modal">{{__('products.close')}}
                                </button>
                                <button type="button" class="btn btn-primary"
                                        id="btn_create_prd">{{__('products.add_product')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@extends('header')
@vite(['resources/js/delivery/products.js'])
@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Gerenciar Produtos</h1>
                <p class="page-subtitle">Gerencie seu estoque</p>
            </div>
            <button class="btn btn-primary" onclick="openProductModal()">
                <i class="bi bi-folder-plus"></i>&nbsp;Adicionar Produto
            </button>
        </div>
    </div>

    <div class="row" id="productList">
        <div class="spinner-border">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>
    <div class="row">
        <nav aria-label="Page pagination" class="d-none" id="pagination" >
            <ul class="pagination" >
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">
                        Adicionar Produto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <div class="row justify-content-center text-center d-none" id="imagePreview">
                            <div class="col-auto">
                                <img src="#" id="category-img-tag" class="figure-img img-fluid rounded"
                                    style="max-width: 200px;" />
                            </div>
                        </div>
                        <input type="hidden" id="productId">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="mb-3">
                            <label for="productValue" class="form-label">Valor</label>
                            <input type="number" class="form-control" id="productValue" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="productStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="productUnit" class="form-label">Unidade de Medida</label>
                            <select class="form-select" id="productUnit" required>
                                <option value="un">UN - UNIDADE</option>
                                <option value="kg">KG - KILOGRAMA</option>
                                <option value="g">G - GRAMA</option>
                                <option value="l">L - LITRO</option>
                                <option value="ml">ML - MILILITRO</option>
                            </select>
                        </div>
                        <div class="mx-0 rounded mb-3 row border">
                            <div class="align-content-center col d-flex flex-wrap justify-content-center">
                                <div class="my-3">
                                    <label for="productImage" class="form-label">
                                        <i class="bi bi-file-earmark-image"></i>&nbsp;Imagem
                                    </label>
                                    <input class="bg-light-subtle form-control p-1 rounded" type="file"
                                        id="productImage" name="productImage">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="productAvailable" checked>
                                <label class="form-check-label" for="productAvailable">
                                    Disponível
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i>&nbsp;Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="saveProduct" onclick="saveProduct()">
                        <i class="bi bi-floppy"></i>&nbsp;Salvar
                    </button>
                    <button type="button" class="btn btn-warning d-none" id="updateProduct"
                        onclick="saveProduct('update')">
                        <i class="bi bi-floppy"></i>&nbsp;Atualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

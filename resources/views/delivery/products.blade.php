@extends('header')
@vite(['resources/js/delivery/products.js'])
@section('title', 'Produtos')
@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Gerenciar Produtos</h1>
                <p class="page-subtitle">Gerencie seu estoque</p>
            </div>
            <div>
                <button class="btn btn-secondary mb-2" disabled>
                    <i class="bi bi-diagram-2"></i>&nbsp;Adicionar Categoria
                </button>
                <button class="btn btn-primary mb-2" onclick="openProductModal()">
                    <i class="bi bi-folder-plus"></i>&nbsp;Adicionar Produto
                </button>

            </div>
        </div>
    </div>

    <div class="row" id="productList">
        <div class="col-12 mb-3">
            <div class="card h-100 p-4 shadow-sm text-center">
                <h5>Carregando produtos <i class="fa-solid fa-ellipsis fa-bounce"></i></h5>
            </div>
        </div>
    </div>
    <div class="row">
        <nav aria-label="Page pagination">
            <ul class="pagination d-none" id="pagination">
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
                        <input type="hidden" id="productId">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="productName" placeholder="Nome do Produto" required>
                        </div>
                        <div class="mb-3">
                            <label for="productValue" class="form-label">Valor</label>
                            <input type="number" class="form-control" id="productValue" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="productStock" placeholder="0" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" required>
                                <option value="">Selecione...</option>
                            </select>
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

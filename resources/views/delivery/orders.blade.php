@extends('header')
@vite(['resources/js/delivery/orders.js'])
@section('title', 'Pedidos')
@section('content')
    <div class="page-header">
        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <h1 class="page-title">Gerenciar Pedidos</h1>
                <p class="page-subtitle">Gerencie seus pedidos</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <button class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#importOrderModal">
                        <i class="bi bi-file-arrow-up-fill"></i>&nbsp;Importar Pedidos
                    </button>
                    <button class="btn btn-primary my-2" onclick="openOrderModal()">
                        <i class="bi bi-plus"></i>&nbsp;Adicionar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group search-box">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar Pedidos..." id="orderSearch">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex action-buttons justify-content-md-end">
                        <select class="form-select disabled" id="statusFilter" style="max-width: 150px;">
                            <option value="all">Todos Status</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ordersTable">
                    <thead>
                        <tr>
                            <th>ID do Pedido</th>
                            <th>N° Pedido no Ifood</th>
                            <th>R$ Total Pago</th>
                            <th>R$ Total Recebido</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="7" class="text-center">Carregando pedidos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <nav aria-label="Page pagination" class="my-5">
        <ul class="pagination d-none justify-content-center" id="pagination">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav>

    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalTitle">Adicionar Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="orderId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderIdIfood" class="form-label">Id do Pedido no Ifood</label>
                                    <input type="text" class="form-control" id="orderIdIfood"
                                           placeholder="Número do Pedido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ifoodOrderNumber" class="form-label">Número do Pedido no Ifood</label>
                                    <input type="text" class="form-control" id="ifoodOrderNumber"
                                           placeholder="Número do Pedido no Ifood">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="orderDate" class="form-label">Status do Pedido</label>
                            <select class="form-select" id="orderStatus" required>
                                <option value="completed" selected>Completo</option>
                                <option value="pending">Pendente</option>
                                <option value="processing">Em processamento</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="orderDate" class="form-label"><i class="bi bi-calendar-date"></i>&nbsp;Data do Pedido</label>
                            <input type="datetime-local" class="form-control" id="orderDate" required>
                        </div>
                        <div id="itemsSection" class=" p-3 mb-3 rounded border">
                            <h6><i class="bi bi-box-seam"></i>&nbsp;Itens do Pedido</h6>
                            <div class="row g-2 align-items-end mb-3">
                                <div class="col-md-5">
                                    <label class="form-label" for="itemId">Nome do Item</label>
                                    <select class="form-select" id="itemId">
                                        <option value="">Selecione o Item</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->id }}
                                                - {{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="itemQuantity" value="1"
                                           min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Preço Unitário</label>
                                    <input type="number" min="0" class="form-control" id="itemPrice" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-success w-100" id="addItem">
                                        <i class="bi bi-plus"></i> Adicionar Item
                                    </button>
                                </div>
                            </div>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>Total</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Linhas de itens serão inseridas aqui via JS -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label for="orderAmount" class="form-label">
                                <i class="bi bi-cash-coin"></i> Total Recebido pela Loja
                            </label>
                            <input type="number" value="0" min="0" class="form-control" id="receivedAmount" step="0.01"
                                   required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="customerAmount" class="form-label">
                                <i class="bi bi-cash"></i> Total Pago pelo Cliente
                            </label>
                            <input type="number" value="0" min="0" class="form-control" id="customerAmount" step="0.01"
                                   required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i>&nbsp;Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="saveOrder" onclick="saveOrder('create')">
                        <i class="bi bi-floppy"></i>&nbsp;Salvar Pedido
                    </button>
                    <button type="button" class="btn btn-warning d-none" id="updateOrder" onclick="saveOrder('update')">
                        <i class="bi bi-pencil"></i>&nbsp;Atualizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Import Order Modal -->
    <div class="modal fade" id="importOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importOrderModalTitle">Importar planilha de pedidos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeImportOrderModal"></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <input type="file" class="form-control" name="file" id="ordersFile" required>
                            </div>
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-success my-5" onclick="importOrders()">
                                    <i class="bi bi-file-arrow-up-fill"></i>&nbsp;Importar Pedidos
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

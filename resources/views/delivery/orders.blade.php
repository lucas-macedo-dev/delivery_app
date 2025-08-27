@extends('header')
@vite(['resources/js/delivery/orders.js'])
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

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalTitle">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="orderId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderCustomer" class="form-label">Customer</label>
                                    <select class="form-select" id="orderCustomer" required>
                                        <option value="">Select customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderStatus" class="form-label">Status</label>
                                    <select class="form-select" id="orderStatus" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Processing">Processing</option>
                                        <option value="In Transit">In Transit</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="orderAddress" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="orderAddress" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="orderDate" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="orderDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="orderItems" class="form-label">Items</label>
                            <textarea class="form-control" id="orderItems" rows="3" placeholder="Enter order items"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="orderAmount" class="form-label">Total Amount</label>
                            <input type="number" class="form-control" id="orderAmount" step="0.01" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveOrder">Save Order</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Order Modal -->
    <div class="modal fade" id="importOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importOrderModalTitle">Importar planilha de pedidos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            {{-- <a href="{{ asset('templates/orders_template.xlsx') }}" class="btn btn-info" download>
                                <i class="bi bi-download"></i>&nbsp;Baixar Modelo
                            </a> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('header')
@vite(['resources/js/delivery/customers.js'])
@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Gerenciar Clientes</h1>
                <p class="page-subtitle">Gerencie seus clientes</p>
            </div>
            <button class="btn btn-primary" onclick="openCustomerModal()">
                <i class="bi bi-plus me-2"></i>Cadastrar Cliente
            </button>
        </div>
    </div>


    <div class="row">
        <div class="col table-responsive">
            <table class="table  table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="d-none d-md-table-cell">ID</th>
                        <th class="d-block d-md-none">
                            <i class="bi bi-person-circle"></i> &nbsp;Informações do Cliente
                        </th>
                        <th class="d-none d-md-table-cell">Nome</th>
                        <th class="d-none d-md-table-cell">Telefone</th>
                        <th class="d-none d-md-table-cell">CPF</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    <!-- Customer rows will be populated here by JavaScript -->
                    <tr>
                        <td colspan="5" class="text-center">Carregando clientes... </td>
                    </tr>
                </tbody>
            </table>
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
    </div>



    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalTitle">Cadastrar Novo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <input type="hidden" id="customerId">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Nome do Cliente <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Celular</label>
                            <input type="tel" class="form-control" id="customerPhone">
                        </div>
                        <div class="mb-3">
                            <label for="customerCpf" class="form-label">CPF</label>
                            <input type="number" class="form-control" id="customerCpf">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveCustomer" onclick="saveCustomer()">
                        Salvar
                    </button>
                    <button type="button" class="btn btn-warning d-none" id="updateCustomer"
                        onclick="saveCustomer('update')">
                        <i class="bi bi-floppy"></i>&nbsp;Atualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

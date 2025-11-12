@extends('header')
@vite(['resources/js/delivery/expenses.js'])
@section('title', 'Despesas')
@section('content')
    <div class="page-header">
        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <h1 class="page-title">
                    <i class="bi bi-receipt me-2"></i>Gestão de Despesas
                </h1>
                <p class="page-subtitle">Gerencie suas despesas</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal"
                            onclick="openCreateModal()">
                        <i class="bi bi-plus me-2"></i>
                        Nova Despesa
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Summary Cards -->
                <div class="row justify-content-center mb-4" id="summaryCards">
                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-0">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-0 small">Total de Despesas</h6>
                                        <h4 class="mb-0 fw-bold" id="totalExpenses">R$ 0,00</h4>
                                    </div>
                                    <div class="ms-2">
                                        <i class="bi bi-cash-coin" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-0">
                        <div class="card bg-danger-subtle  h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-0 small">Quantidade</h6>
                                        <h4 class="mb-0 fw-bold" id="expenseCount">0</h4>
                                    </div>
                                    <div class="ms-2">
                                        <i class="bi bi-list-ul" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-6 col-xl-4">
                                <label for="searchInput" class="form-label">Pesquisar</label>
                                <input type="text" class="form-control" id="searchInput"
                                       placeholder="Buscar por descrição...">
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="startDate" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="endDate" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                                <label for="perPage" class="form-label">Por Página</label>
                                <select class="form-select" id="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 d-flex align-items-end">
                                <div class="d-grid gap-2 w-100">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="clearFilters()">
                                        <i class="bi bi-x me-1"></i>
                                        <span class="d-none d-sm-inline">Limpar</span>
                                        <span class="d-sm-none">Limpar Filtros</span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="getAllExpenses()">
                                        <i class="bi bi-search me-1"></i>
                                        <span class="d-none d-sm-inline">Buscar</span>
                                        <span class="d-sm-none">Aplicar Filtros</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Expenses Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-striped d-md-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th class="d-none d-lg-table-cell">Data da Despesa</th>
                                        <th class="d-none d-lg-table-cell">Criado por</th>
                                        <th class="d-none d-md-table-cell">Atualizado por</th>
                                        <th class="d-none text-center d-lg-table-cell">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="expensesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <nav aria-label="Expenses pagination" id="paginationContainer" class="mt-3">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseModalLabel">Nova Despesa</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" onclick="closeExpenseModal()"
                            aria-label="Close"></button>
                </div>
                <form id="expenseForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Descrição *</label>
                                <input type="text" class="form-control" id="description" name="description" required
                                       maxlength="255" placeholder="Descrição da despesa">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Categoria</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="" selected>Selecione uma categoria</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="value" class="form-label">Valor *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="value" name="value" step="0.01"
                                           min="0" required placeholder="0.00">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="expense_date" class="form-label">Data da Despesa *</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date"
                                       placeholder="Selecione uma data" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
                        <button type="button" class="btn btn-secondary btn_close_modal order-2 order-sm-1"
                                data-bs-dismiss="modal">Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary order-1 order-sm-2" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir esta despesa?</p>
                    <p class="text-muted" id="deleteExpenseInfo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/expenses.js') }}"></script>
@endsection



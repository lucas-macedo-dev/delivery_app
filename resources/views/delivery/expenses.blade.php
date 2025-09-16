@extends('header')
@vite(['resources/js/delivery/expenses.js'])
@section('content')
    div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>
                    Gestão de Despesas
                </h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal" onclick="openCreateModal()">
                    <i class="bi bi-plus me-2"></i>
                    Nova Despesa
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4" id="summaryCards">
                <div class="col-sm-6 col-lg-4 mb-3 mb-lg-0">
                    <div class="card bg-primary text-white h-100">
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
                    <div class="card bg-info text-white h-100">
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
                <div class="col-sm-12 col-lg-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-0 small">Média por Despesa</h6>
                                    <h4 class="mb-0 fw-bold" id="averageExpense">R$ 0,00</h4>
                                </div>
                                <div class="ms-2">
                                    <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por descrição...">
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
                                <div class="btn-group d-block d-sm-flex" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-2 mb-sm-0" onclick="clearFilters()">
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
            </div>

            <!-- Expenses Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Data da Despesa</th>
                                    <th>Criado por</th>
                                    <th>Atualizado por</th>
                                    <th>Ações</th>
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
                    <nav aria-label="Expenses pagination" id="paginationContainer">
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
                    <button type="button" class="btn-close btn_close_modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="expenseForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Descrição *</label>
                                <input type="text" class="form-control" id="description" name="description" required maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="value" class="form-label">Valor *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="value" name="value" step="0.01" min="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="expense_date" class="form-label">Data da Despesa *</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
                        <button type="button" class="btn btn-secondary btn_close_modal order-2 order-sm-1" data-bs-dismiss="modal">Cancelar</button>
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

@section('styles')
    <style>
        /* Base styles */
        .table th {
            border-top: none;
            font-weight: 600;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-group .btn {
            border-radius: 0.375rem !important;
            margin-right: 0.25rem;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .pagination {
            margin-bottom: 0;
        }

        .alert {
            margin-bottom: 1rem;
            border: none;
            border-radius: 0.5rem;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Mobile optimizations */
        @media (max-width: 575.98px) {
            /* Header responsive */
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch !important;
            }

            .d-flex.justify-content-between.align-items-center h2 {
                text-align: center;
                font-size: 1.5rem;
            }

            /* Table responsive */
            .table-responsive {
                font-size: 0.8rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                word-break: break-word;
            }

            /* Hide some columns on mobile */
            .table th:nth-child(1),
            .table td:nth-child(1),
            .table th:nth-child(5),
            .table td:nth-child(5),
            .table th:nth-child(6),
            .table td:nth-child(6) {
                display: none;
            }

            /* Action buttons mobile */
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .btn-group .btn {
                margin-right: 0;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            /* Modal improvements */
            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                padding: 1rem;
            }

            .modal-footer .btn {
                width: 100%;
            }

            /* Pagination mobile */
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-link {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        /* Tablet optimizations */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .table-responsive {
                font-size: 0.9rem;
            }

            .btn-group .btn {
                font-size: 0.8rem;
                padding: 0.375rem 0.5rem;
            }

            /* Hide created_by column on tablet */
            .table th:nth-child(5),
            .table td:nth-child(5) {
                display: none;
            }
        }

        /* Desktop optimizations */
        @media (min-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: row;
            }
        }

        /* Large screens */
        @media (min-width: 1200px) {
            .container-fluid {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        /* Custom scrollbar for table */
        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #6c757d;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        /* Card hover effects */
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.15s ease-in-out;
        }

        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
        }

        /* Form improvements */
        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection

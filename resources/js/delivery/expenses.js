'use strict';

let currentPage = 1;
let editingExpenseId = null;
let deleteExpenseId = null;
const baseUrl = window.location.origin;

window.onload = () => {
    getAllExpenses();
    loadSummary();
    setupEventListeners();

    // Set today's date as default for new expenses
    const expenseDateInput = document.getElementById('expense_date');
    if (expenseDateInput) {
        expenseDateInput.valueAsDate = new Date();
    }
};

// Setup event listeners
function setupEventListeners() {
    // Search input with debounce
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                getAllExpenses();
            }, 500);
        });
    }

    // Date filters
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    if (startDate) {
        startDate.addEventListener('change', function() {
            currentPage = 1;
            getAllExpenses();
            loadSummary();
        });
    }

    if (endDate) {
        endDate.addEventListener('change', function() {
            currentPage = 1;
            getAllExpenses();
            loadSummary();
        });
    }

    // Per page selector
    const perPage = document.getElementById('perPage');
    if (perPage) {
        perPage.addEventListener('change', function() {
            currentPage = 1;
            getAllExpenses();
        });
    }

    // Form submission
    const expenseForm = document.getElementById('expenseForm');
    if (expenseForm) {
        expenseForm.addEventListener('submit', handleFormSubmit);
    }

    // Delete confirmation
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteExpense);
    }
}

// Get all expenses with pagination and filters
window.getAllExpenses = async function (page = 1) {
    currentPage = page;

    try {
        showTableLoading();

        const searchInput = document.getElementById('searchInput');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const perPage = document.getElementById('perPage');

        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage ? perPage.value : 10,
            search: searchInput ? searchInput.value : '',
            start_date: startDate ? startDate.value : '',
            end_date: endDate ? endDate.value : ''
        });

        const response = await fetch(`./expenses/showAll?${params}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 200 && data.data) {
            buildExpensesTable(data.data.expenses);
            buildPagination(data.data.meta);
        } else {
            buildExpensesTable([]);
            window.modalMessage({
                title: 'Erro ao carregar despesas',
                description: data.message || 'Erro desconhecido',
                type: 'error',
            });
        }
    } catch (error) {
        console.error('Error loading expenses:', error);
        buildExpensesTable([]);
        window.modalMessage({
            title: 'Erro ao carregar despesas',
            description: 'Erro de conexão. Tente novamente.',
            type: 'error',
        });
    }
};

// Get a specific expense
window.getExpense = async function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao buscar despesa',
            description: 'ID da despesa não informado',
            type: 'error',
        });
        return false;
    }

    try {
        const response = await fetch(`./expenses/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 200 && data.data) {
            return data.data;
        } else {
            window.modalMessage({
                title: 'Erro ao buscar despesa',
                description: data.message || 'Despesa não encontrada',
                type: 'error',
            });
        }
    } catch (error) {
        console.error('Error getting expense:', error);
        window.modalMessage({
            title: 'Erro ao buscar despesa',
            description: 'Erro de conexão. Tente novamente.',
            type: 'error',
        });
    }

    return false;
};

// Load summary statistics
window.loadSummary = async function () {
    try {
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        const params = new URLSearchParams({
            start_date: startDate ? startDate.value : '',
            end_date: endDate ? endDate.value : ''
        });

        const response = await fetch(`./expenses/summary?${params}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 200 && data.data) {
            const totalExpenses = document.getElementById('totalExpenses');
            const expenseCount = document.getElementById('expenseCount');
            const averageExpense = document.getElementById('averageExpense');

            if (totalExpenses) totalExpenses.textContent = formatCurrency(data.data.total_expenses);
            if (expenseCount) expenseCount.textContent = data.data.expense_count;
            if (averageExpense) averageExpense.textContent = formatCurrency(data.data.average_expense);
        }
    } catch (error) {
        console.error('Error loading summary:', error);
    }
};

// Build expenses table
window.buildExpensesTable = function (expenses) {
    const tbody = document.getElementById('expensesTableBody');

    if (!tbody) return;

    if (!expenses || expenses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <p class="text-muted mb-0">Nenhuma despesa encontrada</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    expenses.forEach(expense => {
        html += `
            <tr id="expense_${expense.id}">
                <td class="d-none d-md-table-cell">${expense.id}</td>
                <td>
                    <div class="fw-bold">${expense.description}</div>
                    <small class="text-muted d-md-none">ID: ${expense.id}</small>
                </td>
                <td>
                    <span class="fw-bold text-danger">${formatCurrency(expense.value)}</span>
                    <div class="small text-muted d-md-none">${formatDate(expense.expense_date)}</div>
                </td>
                <td class="d-none d-sm-table-cell">${formatDate(expense.expense_date)}</td>
                <td class="d-none d-lg-table-cell">${expense.user_inserter ? expense.user_inserter.name : 'N/A'}</td>
                <td class="d-none d-md-table-cell">${expense.user_updater ? expense.user_updater.name : 'N/A'}</td>
                <td>
                    <div class="btn-group-vertical btn-group-sm d-sm-none" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-1" onclick="editExpense(${expense.id})"
                                id="editExpense_${expense.id}" title="Editar">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(${expense.id}, '${expense.description.replace(/'/g, '&#39;')}', '${formatCurrency(expense.value)}')"
                                id="deleteExpense_${expense.id}" title="Excluir">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </div>
                    <div class="btn-group d-none d-sm-flex" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editExpense(${expense.id})"
                                id="editExpense_${expense.id}_desktop" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${expense.id}, '${expense.description.replace(/'/g, '&#39;')}', '${formatCurrency(expense.value)}')"
                                id="deleteExpense_${expense.id}_desktop" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
};

// Build pagination
window.buildPagination = function (meta) {
    const pagination = document.getElementById('pagination');

    if (!pagination || meta.last_page <= 1) {
        if (pagination) pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Previous button
    if (meta.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="getAllExpenses(${meta.current_page - 1})">Anterior</a></li>`;
    }

    // Page numbers
    const startPage = Math.max(1, meta.current_page - 2);
    const endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="getAllExpenses(1)">1</a></li>`;
        if (startPage > 2) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === meta.current_page ? 'active' : '';
        paginationHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="javascript:void(0)" onclick="getAllExpenses(${i})">${i}</a></li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="getAllExpenses(${meta.last_page})">${meta.last_page}</a></li>`;
    }

    // Next button
    if (meta.current_page < meta.last_page) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="getAllExpenses(${meta.current_page + 1})">Próximo</a></li>`;
    }

    pagination.innerHTML = paginationHTML;
};

// Show table loading state
function showTableLoading() {
    const tbody = document.getElementById('expensesTableBody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Open create modal
window.openCreateModal = function () {
    editingExpenseId = null;
    const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
    const modalLabel = document.getElementById('expenseModalLabel');
    const form = document.getElementById('expenseForm');
    const expenseDate = document.getElementById('expense_date');

    if (modalLabel) modalLabel.textContent = 'Nova Despesa';
    if (form) form.reset();
    if (expenseDate) expenseDate.valueAsDate = new Date();

    clearFormErrors();
    modal.show();
};

// Edit expense
window.editExpense = async function (id) {
    const expense = await getExpense(id);

    if (!expense) {
        return;
    }

    editingExpenseId = id;
    const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
    const modalLabel = document.getElementById('expenseModalLabel');

    if (modalLabel) modalLabel.textContent = 'Editar Despesa';

    // Fill form fields
    const description = document.getElementById('description');
    const value = document.getElementById('value');
    const expenseDate = document.getElementById('expense_date');

    if (description) description.value = expense.description;
    if (value) value.value = expense.value;
    if (expenseDate) expenseDate.value = expense.expense_date;

    clearFormErrors();
    modal.show();
};

// Open delete modal
window.openDeleteModal = function (id, description, value) {
    deleteExpenseId = id;
    const deleteExpenseInfo = document.getElementById('deleteExpenseInfo');

    if (deleteExpenseInfo) {
        deleteExpenseInfo.textContent = `${description} - ${value}`;
    }

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
};

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

    try {
        // Show loading state
        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');

        clearFormErrors();

        const formData = new FormData(e.target);
        const data = {
            description: formData.get('description'),
            value: formData.get('value'),
            expense_date: formData.get('expense_date')
        };

        const url = editingExpenseId
            ? `./expenses/edit/${editingExpenseId}`
            : `./expenses/new_expense`;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.status === 200 || result.status === 201) {
            // Close modal first
            closeExpenseModal();

            // Show success message
            if (typeof window.modalMessage === 'function') {
                window.modalMessage({
                    title: 'Sucesso',
                    description: result.message,
                    type: 'success',
                });
            }

            // Reload data
            getAllExpenses(currentPage);
            loadSummary();
        } else {
            if (response.status === 422 && result.errors) {
                showFormErrors(result.errors);
            } else {
                // Don't close modal on error, show error message
                if (typeof window.modalMessage === 'function') {
                    window.modalMessage({
                        title: 'Erro ao salvar despesa',
                        description: result.message || 'Erro desconhecido',
                        type: 'error',
                    });
                }
            }
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        if (typeof window.modalMessage === 'function') {
            window.modalMessage({
                title: 'Erro ao salvar despesa',
                description: 'Erro de conexão. Tente novamente.',
                type: 'error',
            });
        }
    } finally {
        // Hide loading state
        if (submitBtn) submitBtn.disabled = false;
        if (spinner) spinner.classList.add('d-none');
    }
}

// Delete expense
async function deleteExpense() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const spinner = confirmBtn ? confirmBtn.querySelector('.spinner-border') : null;

    if (!deleteExpenseId) return;

    try {
        // Show loading state
        if (confirmBtn) confirmBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');

        const response = await fetch(`./expenses/delete/${deleteExpenseId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 200) {
            // Close delete modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            if (modal) modal.hide();

            // Show success message
            if (typeof window.modalMessage === 'function') {
                window.modalMessage({
                    title: 'Sucesso',
                    description: data.message,
                    type: 'success',
                });
            }

            // Remove row from table
            const expenseRow = document.getElementById(`expense_${deleteExpenseId}`);
            if (expenseRow) expenseRow.remove();

            getAllExpenses(currentPage);
            loadSummary();
        } else {
            if (typeof window.modalMessage === 'function') {
                window.modalMessage({
                    title: 'Erro ao excluir despesa',
                    description: data.message || 'Erro desconhecido',
                    type: 'error',
                });
            }
        }
    } catch (error) {
        console.error('Error deleting expense:', error);
        if (typeof window.modalMessage === 'function') {
            window.modalMessage({
                title: 'Erro ao excluir despesa',
                description: 'Erro de conexão. Tente novamente.',
                type: 'error',
            });
        }
    } finally {
        // Hide loading state
        if (confirmBtn) confirmBtn.disabled = false;
        if (spinner) spinner.classList.add('d-none');

        // Reset delete ID
        deleteExpenseId = null;
    }
}

// Close expense modal
window.closeExpenseModal = function () {
    try {
        const modal = bootstrap.Modal.getInstance(document.getElementById('expenseModal'));
        if (modal) {
            modal.hide();
        }

        // Force cleanup in case modal instance is stuck
        const modalElement = document.getElementById('expenseModal');
        if (modalElement) {
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
        }

        // Remove backdrop if it exists
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }

        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Reset form and variables
        editingExpenseId = null;
        clearFormErrors();

        const form = document.getElementById('expenseForm');
        if (form) {
            form.reset();
        }
    } catch (error) {
        console.error('Error closing modal:', error);
        // Force cleanup even if error occurs
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
};

// Clear filters
window.clearFilters = function () {
    const searchInput = document.getElementById('searchInput');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const perPage = document.getElementById('perPage');

    if (searchInput) searchInput.value = '';
    if (startDate) startDate.value = '';
    if (endDate) endDate.value = '';
    if (perPage) perPage.value = '10';

    currentPage = 1;
    getAllExpenses();
    loadSummary();
};

// Show form errors
function showFormErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            const feedback = input.parentElement.querySelector('.invalid-feedback');

            input.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = errors[field][0];
            }
        }
    });
}

// Clear form errors
function clearFormErrors() {
    document.querySelectorAll('.is-invalid').forEach(input => {
        input.classList.remove('is-invalid');
    });
}

// Utility functions
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';

    try {
        // Handle different date formats from API
        let date;

        if (dateString.includes('T')) {
            // ISO format: "2025-09-15T00:00:00.000000Z"
            date = new Date(dateString);
        } else {
            // Simple date format: "2025-09-15"
            date = new Date(dateString + 'T00:00:00');
        }

        // Check if date is valid
        if (isNaN(date.getTime())) {
            return 'Data inválida';
        }

        return date.toLocaleDateString('pt-BR');
    } catch (error) {
        console.error('Error formatting date:', error, dateString);
        return 'Data inválida';
    }
}

// Show loading (if you have a global loading function)
window.showLoading = function(show) {
    // Implement your loading logic here
    // This matches the pattern from your products.js
    console.log(show ? 'Loading...' : 'Loaded');
};

// Pagination helper function (matches your products.js pattern)
window.pagination = function(config) {
    buildPagination({
        current_page: config.page,
        last_page: Math.ceil(config.total / config.max),
        total: config.total,
        per_page: config.max
    });
};

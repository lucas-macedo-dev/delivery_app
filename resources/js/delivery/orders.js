'use strict';

// Global state variables
let orderItems   = [];
let currentPage  = 1;
let filterParams = {};

window.onload = () => {
    loadOrders().then(r => {
    });
    bindEventListeners();
    document.getElementById('closeImportOrderModal').addEventListener('click', () => {
        document.getElementById('ordersFile').value = '';
    })
}

function bindEventListeners() {
    const searchInput  = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(() => filterOrders(), 300));
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', () => filterOrders());
    }
}

function debounce(func, wait) {
    if (typeof func !== 'function') {
        return () => {
        };
    }

    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

window.loadOrders = async function (page = 1, filterParameters = {}) {
    console.log(page);
    try {
        currentPage  = page;
        filterParams = filterParameters || {};

        const queryParams = buildQueryParams(filterParameters);
        const response    = await fetchOrders(page, queryParams);

        if (response?.status === 200 && response?.data?.orders) {
            renderOrdersTable(response.data.orders);
            renderStatusFilter(response.data.orders);
            window.pagination({
                page    : page,
                total   : response.data.meta?.total || 0,
                max     : response.data.meta?.per_page || 10,
                qtt     : 5,
                id      : 'pagination',
                callback: 'loadOrders'
            });
        } else {
            renderOrdersTable({})
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        showErrorMessage('Erro ao carregar pedidos');
        renderEmptyState();
    }
}

function buildQueryParams(filterParameters) {
    const possibleParams = ['search', 'status'];
    const queryParams    = new URLSearchParams();

    if (filterParameters && typeof filterParameters === 'object') {
        Object.entries(filterParameters).forEach(([key, value]) => {
            if (possibleParams.includes(key) && value && value.toString().trim()) {
                queryParams.set(key, value.toString().trim());
            }
        });
    }

    return queryParams;
}

async function fetchOrders(page, queryParams) {
    const url      = `./orders/showAll?page=${page}&${queryParams.toString()}`;
    const response = await fetch(url);

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data;
}

function renderEmptyState() {
    const tbody = document.getElementById('ordersTableBody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">Nenhum pedido encontrado.</td>
            </tr>
        `;
    }

    const cardsContainer = document.getElementById('ordersCardsContainer');
    if (cardsContainer) {
        cardsContainer.innerHTML = `<div class="p-3 text-center text-muted">Nenhum pedido encontrado.</div>`;
    }
}

function renderStatusFilter(orders) {
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) return;

    if (!orders || !Array.isArray(orders)) {
        return;
    }

    const statuses     = [...new Set(orders.map(order => order.status).filter(Boolean))];
    const currentValue = statusFilter.value;

    statusFilter.innerHTML = '<option value="all">Todos Status</option>' +
        statuses.map(status =>
            `<option value="${status}" ${status === currentValue ? 'selected' : ''}>${status}</option>`
        ).join('');

    statusFilter.classList.remove('disabled');
}

function renderOrdersTable(orders) {
    const table = document.getElementById('ordersTable');
    if (!table) return;

    setupTableWrapper(table);

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const cardsContainer = setupCardsContainer(table);

    // Render table (desktop)
    tbody.innerHTML = renderTableRows(orders);

    // Render cards (mobile)
    if (cardsContainer) {
        cardsContainer.innerHTML = renderMobileCards(orders);
    }
}

function setupTableWrapper(table) {
    if (!table) return;

    table.classList.add('table', 'table-striped', 'table-hover', 'd-none', 'd-md-table');

    if (table.parentElement && !table.parentElement.classList.contains('table-responsive')) {
        const wrapper     = document.createElement('div');
        wrapper.className = 'table-responsive d-none d-md-block';
        table.parentElement.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    } else if (table.parentElement) {
        table.parentElement.classList.add('d-none', 'd-md-block');
    }
}

function setupCardsContainer(table) {
    let cardsContainer = document.getElementById('ordersCardsContainer');
    if (!cardsContainer) {
        cardsContainer           = document.createElement('div');
        cardsContainer.id        = 'ordersCardsContainer';
        cardsContainer.className = 'd-block d-md-none';

        if (table.parentElement && table.parentElement.parentElement) {
            table.parentElement.parentElement.insertBefore(cardsContainer, table.parentElement.nextSibling);
        }
    }
    return cardsContainer;
}

/**
 * Get status badge class
 */
function getStatusBadgeClass(status) {
    if (!status) return 'badge bg-secondary';

    const s = String(status).toLowerCase();
    if (s.includes('pending') || s.includes('pendente')) return 'badge bg-warning text-dark';
    if (s.includes('completed') || s.includes('concluído') || s.includes('concluido')) return 'badge bg-success';
    if (s.includes('cancel')) return 'badge bg-danger';
    if (s.includes('processing') || s.includes('processando')) return 'badge bg-info text-dark';
    return 'badge bg-secondary';
}

/**
 * Render table rows for desktop view
 */
function renderTableRows(orders) {
    if (!orders || !Array.isArray(orders) || orders.length === 0) {
        return `<tr><td colspan="7" class="text-center">Nenhum pedido encontrado.</td></tr>`;
    }

    return orders.map(order => {
        const id       = order.id || 'N/A';
        const itemsId  = `items-section-${id}`;
        const hasItems = Array.isArray(order.items) && order.items.length > 0;

        const itemsRows = hasItems
            ? order.items.map(item => `
                <tr>
                    <td>${item.product?.name || 'N/A'}</td>
                    <td>${item.quantity}</td>
                    <td>R$ ${Number(item.price || 0).toFixed(2)}</td>
                    <td>R$ ${Number(item.total || item.quantity * item.price || 0).toFixed(2)}</td>
                </tr>
            `).join('')
            : `<tr><td colspan="4">No items</td></tr>`;

        return `
            <tr>
                <td>#${id}</td>
                <td>${order.ifood_order_number || 'N/A'}</td>
                <td>R$ ${Number(order.total_amount_order || 0).toFixed(2)}</td>
                <td>R$ ${Number(order.total_amount_received || 0).toFixed(2)}</td>
                <td><span class="${getStatusBadgeClass(order.status)}">${order.status || 'N/A'}</span></td>
                <td>${order.order_date || 'N/A'}</td>
                <td>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" onclick="editOrder(${id})" title="Editar pedido">
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${hasItems ? `<button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#${itemsId}" title="Ver itens"><i class="fa-solid fa-box"></i></button>` : ''}
                        </div>
                        <button class="btn btn-outline-danger" onclick="deleteOrder(${id})" title="Excluir pedido">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            ${hasItems ? `
            <tr class="collapse" id="${itemsId}">
                <td colspan="7">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>${itemsRows}</tbody>
                    </table>
                </td>
            </tr>` : ''}
        `;
    }).join('');
}

function renderMobileCards(orders) {
    if (!orders || !Array.isArray(orders) || orders.length === 0) {
        return `<div class="p-3 text-center text-muted">Nenhum pedido encontrado.</div>`;
    }

    return orders.map(order => {
        const id       = order.id || 'N/A';
        const itemsId  = `items-mobile-${id}`;
        const hasItems = Array.isArray(order.items) && order.items.length > 0;

        const itemsHtml = hasItems
            ? order.items.map(item => `
                <div class="d-flex justify-content-between">
                    <div>${item.product?.name || 'N/A'}</div>
                    <div>${item.quantity} x R$ ${Number(item.price || 0)
                .toFixed(2)} = R$ ${Number(item.total || item.quantity * item.price || 0).toFixed(2)}</div>
                </div>
            `).join('')
            : `<div class="text-muted">No items</div>`;

        return `
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">#${id} ${order.ifood_order_number ? `- ${order.ifood_order_number}` : ''}</h6>
                            <div class="small text-muted">${order.order_date || 'N/A'}</div>
                            <div class="small my-2 rounded  text-white text-center
                                ${order.ifood_order_number ? 'bg-danger' : 'bg-primary'}">${order.ifood_order_number ? 'ifood' : 'próprio'}
                            </div>

                        </div>
                        <div class="text-end">
                            <div>Total: <strong>R$ ${Number(order.total_amount_order || 0).toFixed(2)}</strong></div>
                            <div>Recebido: <strong>R$ ${Number(order.total_amount_received || 0).toFixed(2)}</strong></div>
                            <div class="my-2">${order.status ? `<span class="${getStatusBadgeClass(order.status)}">${order.status}</span>` : ''}</div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editOrder(${id})" title="Editar pedido">
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${hasItems ? `<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#${itemsId}" title="Ver itens"><i class="fa-solid fa-box"></i></button>` : ''}
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${id})" title="Excluir pedido">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    ${hasItems ? `
                    <div class="collapse mt-3" id="${itemsId}">
                        <div class="card card-body p-2">${itemsHtml}</div>
                    </div>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function filterOrders() {
    const search = document.getElementById('orderSearch')?.value?.toLowerCase() || '';
    const status = document.getElementById('statusFilter')?.value || '';

    const filterParameters = {};
    if (search && search.trim()) filterParameters.search = search.trim();
    if (status && status !== 'all') filterParameters.status = status;

    loadOrders(1, filterParameters);
}

window.editOrder = async function (id) {
    if (!id) {
        showErrorMessage('ID do pedido não fornecido');
        return;
    }

    try {
        const response = await fetch(`./orders/show/${id}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (result?.status === 200 && result?.data) {
            openOrderModal(result.data, true);
        } else {
            showErrorMessage('Erro ao carregar dados do pedido');
        }
    } catch (error) {
        console.error('Error loading order:', error);
        showErrorMessage('Erro ao carregar pedido para edição');
    }
}

window.deleteOrder = async function (id) {
    if (!id) {
        showErrorMessage('ID do pedido não fornecido');
        return;
    }

    if (!confirm('Tem certeza que deseja excluir este pedido?')) {
        return;
    }

    try {
        const response = await fetch(`./orders/delete/${id}`, {
            method : 'DELETE',
            headers: {
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type'    : 'application/json'
            }
        });

        const result = await response.json();

        if (result?.status === 200) {
            showSuccessMessage('Pedido excluído com sucesso!');
            loadOrders(currentPage, filterParams);
        } else {
            showErrorMessage(result?.message || 'Erro ao excluir pedido');
        }
    } catch (error) {
        console.error('Error deleting order:', error);
        showErrorMessage('Erro ao excluir pedido');
    }
}

function setOrderModalTitle(isEdit) {
    const title = document.getElementById('orderModalTitle');
    if (title) {
        title.textContent = isEdit ? 'Editar Pedido' : 'Adicionar Pedido';
    }

    const saveBtn   = document.getElementById('saveOrder');
    const updateBtn = document.getElementById('updateOrder');

    if (saveBtn) saveBtn.classList.toggle('d-none', isEdit);
    if (updateBtn) updateBtn.classList.toggle('d-none', !isEdit);
}

function fillOrderForm(order) {
    console.log(order.order_date);
    const fields = {
        'orderId'         : order.id || '',
        'orderStatus'     : order.status || '',
        'orderDate'       : order.order_date,
        'ifoodOrderNumber': order.ifood_order_number || '',
        'customerAmount'  : order.total_amount_order || '',
        'receivedAmount'  : order.total_amount_received || '',
        'orderIdIfood'    : order.ifood_id || ''
    };

    Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = value;
        }
    });

    if (order.items && Array.isArray(order.items)) {
        orderItems = order.items.map(item => ({
            product_id: item.product_id || '',
            quantity  : item.quantity || 0,
            price     : item.unit_price || 0,
            total     : item.total_price || 0,
            product   : item.product || null
        }));
        renderOrderItems();
    } else {
        orderItems = [];
        renderOrderItems();
    }
}

function resetOrderForm() {
    const form = document.getElementById('orderForm');
    if (form) {
        form.reset();
    }

    const orderId   = document.getElementById('orderId');
    const orderDate = document.getElementById('orderDate');

    if (orderId) orderId.value = '';
    if (orderDate) orderDate.value = new Date().toISOString().split('T')[0];

    orderItems = [];
    renderOrderItems();
    updateOrderTotal();
}

function renderOrderItems() {
    const itemsTableBody = document.getElementById('itemsTableBody');
    if (!itemsTableBody) return;

    if (!orderItems || !Array.isArray(orderItems)) {
        itemsTableBody.innerHTML = '';
        return;
    }

    itemsTableBody.innerHTML = orderItems.map((item, index) => `
        <tr>
            <td>${item.product?.name || item.product_id || 'N/A'}</td>
            <td>${item.quantity || 0}</td>
            <td>R$ ${Number(item.price || 0).toFixed(2)}</td>
            <td>R$ ${Number(item.total || item.quantity * item.price || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item" data-index="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    itemsTableBody.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const index = parseInt(e.target.closest('.remove-item').dataset.index);
            removeOrderItem(index);
        });
    });
}

function removeOrderItem(index) {
    if (!orderItems || !Array.isArray(orderItems) || index < 0 || index >= orderItems.length) {
        return;
    }

    orderItems.splice(index, 1);
    renderOrderItems();
    updateOrderTotal();
}

function updateOrderTotal() {
    if (!orderItems || !Array.isArray(orderItems)) {
        return;
    }

    const total = orderItems.reduce((sum, item) => {
        const itemTotal = item.total || (item.quantity * item.price) || 0;
        return sum + Number(itemTotal);
    }, 0);

    const receivedAmountField = document.getElementById('receivedAmount');
    if (receivedAmountField) {
        receivedAmountField.value = total.toFixed(2);
    }
}

function setupAddItem() {
    const addItemBtn = document.getElementById('addItem');
    if (!addItemBtn) return;

    const newAddBtn = addItemBtn.cloneNode(true);
    if (addItemBtn.parentNode) {
        addItemBtn.parentNode.replaceChild(newAddBtn, addItemBtn);
    }

    newAddBtn.addEventListener('click', () => {
        addOrderItem();
    });

    return newAddBtn;
}

function addOrderItem() {
    const productId = document.getElementById('itemId')?.value?.trim();
    const quantity  = parseInt(document.getElementById('itemQuantity')?.value);
    const price     = parseFloat(document.getElementById('itemPrice')?.value);

    if (!productId || isNaN(quantity) || isNaN(price) || quantity <= 0 || price <= 0) {
        showErrorMessage('Preencha todos os campos do item corretamente');
        return;
    }

    const total   = quantity * price;
    const newItem = {
        product_id: productId,
        quantity  : quantity,
        price     : price,
        total     : total
    };

    if (!orderItems) {
        orderItems = [];
    }

    orderItems.push(newItem);
    renderOrderItems();
    updateOrderTotal();
    clearItemForm();
}

function clearItemForm() {
    const fields = ['itemId', 'itemQuantity', 'itemPrice'];
    fields.forEach(id => {
        const element = document.getElementById(id);
        if (element && element.value !== undefined) {
            if (id === 'itemQuantity') {
                element.value = '1';
            } else {
                element.value = '';
            }
        }
    });
}

window.openOrderModal = function (order = null, isEdit = false) {
    const modalElement = document.getElementById('orderModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);

    setOrderModalTitle(isEdit);

    if (isEdit && order) {
        fillOrderForm(order);
    } else {
        resetOrderForm();
    }

    setupAddItem();
    modal.show();
}

window.saveOrder = async function (action = 'create', id = null) {
    if (!validateOrderForm()) {
        return;
    }

    const orderData = getOrderFormData();

    if (action === 'update' && !id) {
        id = document.getElementById('orderId')?.value;
    }

    if (action === 'update' && !id) {
        showErrorMessage('ID do pedido não encontrado para atualização');
        return;
    }

    const route = action === 'create' ? 'orders/new_order' : `orders/edit/${id}`;

    try {
        showLoading(true);

        const response = await fetch(route, {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type'    : 'application/json'
            },
            body   : JSON.stringify(orderData)
        });

        const result = await response.json();

        if ([200, 201].includes(result?.status)) {
            showSuccessMessage(result.message || 'Pedido salvo com sucesso!');
            closeModal();
            loadOrders(currentPage, filterParams);
        } else {
            showErrorMessage(result.message || 'Erro ao salvar pedido');
        }
    } catch (error) {
        console.error('Error saving order:', error);
        showErrorMessage('Erro ao salvar pedido');
    } finally {
        showLoading(false);
    }
}

function validateOrderForm() {
    if (!orderItems || !Array.isArray(orderItems) || orderItems.length === 0) {
        showErrorMessage('Nenhum item adicionado ao pedido');
        return false;
    }

    const requiredFields = ['orderStatus', 'orderDate'];
    for (const field of requiredFields) {
        const element = document.getElementById(field);
        if (!element || !element.value || !element.value.trim()) {
            showErrorMessage(`Campo ${field} é obrigatório`);
            return false;
        }
    }

    return true;
}

function getOrderFormData() {
    return {
        ifoodId         : document.getElementById('orderIdIfood')?.value || '',
        orderStatus     : document.getElementById('orderStatus')?.value || '',
        orderDate       : document.getElementById('orderDate')?.value || '',
        orderItems      : orderItems || [],
        receivedAmount  : parseFloat(document.getElementById('receivedAmount')?.value) || 0,
        customerAmount  : parseFloat(document.getElementById('customerAmount')?.value) || 0,
        ifoodOrderNumber: document.getElementById('ifoodOrderNumber')?.value || ''
    };
}

function closeModal() {
    const modalElement = document.getElementById('orderModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
}

window.importOrders = async function () {
    const input = document.getElementById('ordersFile');
    if (!input || !input.files || input.files.length === 0) {
        showErrorMessage('Selecione um arquivo!');
        return;
    }

    const formData = new FormData();
    formData.append('file', input.files[0]);

    try {
        showLoading(true);

        const response = await fetch('orders/import', {
            method : 'POST',
            body   : formData,
            headers: {
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 200) {
            showSuccessMessage(data.message || 'Pedidos importados com sucesso!');
            loadOrders();
        } else {
            showErrorMessage(data.message || 'Erro na importação');
        }
    } catch (error) {
        console.error('Error importing orders:', error);
        showErrorMessage('Erro na importação');
    } finally {
        showLoading(false);
    }
}

function showLoading(show) {
    if (typeof window.showLoading === 'function') {
        window.showLoading(show);
    }
}

function showSuccessMessage(message) {
    if (typeof window.modalMessage === 'function') {
        window.modalMessage({
            title      : 'Sucesso',
            description: message,
            type       : 'success'
        });
    } else {
        alert(message);
    }
}

function showErrorMessage(message) {
    if (typeof window.modalMessage === 'function') {
        window.modalMessage({
            title      : 'Erro',
            description: message,
            type       : 'error'
        });
    } else {
        alert(message);
    }
}

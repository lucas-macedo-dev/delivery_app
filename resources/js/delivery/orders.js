'use strict';

window.onload = () => {
    getAllOrders().then(r => {
    });
};

window.orderItems = [];

window.getAllOrders = async function (page = 1, filterParams = {}) {

    const possibleParams = ['search', 'status'];
    const queryParams    = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (possibleParams.includes(key) && value) {
            queryParams.set(key, value);
        }
    });

    let orders   = await fetch('./orders/showAll?page=' + page + '&' + queryParams.toString());
    let response = await orders.json();
    if (response?.status === 200 && response?.data?.orders) {
        renderOrdersTable(response.data.orders);
        renderStatusFilter(response.data.orders);
    } else {
        document.getElementById('ordersTableBody').innerHTML = `
            <tr>
                <td colspan="7" class="text-center"> Nenhum pedido encontrado.
                </td>
            </tr>
        `;
    }

    window.pagination({
        page    : page,
        total   : response?.data?.meta?.total,
        max     : response?.data?.meta?.per_page,
        qtt     : 5,
        id      : `pagination`,
        callback: `getAllOrders`
    });
};

window.renderStatusFilter = function (orders) {
    const statusFilter = document.getElementById('statusFilter');
    const status       = [...new Set(orders.map(order => order.status))];

    statusFilter.innerHTML = '<option value="all">Todos Status</option>' +
        status.map(status => `<option value="${status}" ${status === statusFilter.value ? 'selected' : ''}>${status}</option>`)
            .join('');

    statusFilter.classList.remove('disabled');
};

window.oldRenderOrdersTable = function (orders) {
    let rows    = '';
    const tbody = document.querySelector('#ordersTable tbody');
    if (!tbody) return;
    rows            = orders.map(order => `
        <tr>
            <td>#${order.id}</td>
            <td>${order.ifood_order_number || 'N/A'}</td>
            <td>R$ ${order.total_amount_order || 'N/A'}</td>
            <td>R$ ${order.total_amount_received || 'N/A'}</td>
            <td><span class="status-badge status-${order.status.toLowerCase().replace(' ', '-')}">${order.status}</span></td>
            <td>${order.order_date}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editOrder(${order.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    tbody.innerHTML = rows;
};
// javascript
window.renderOrdersTable = function (orders) {
    const table = document.getElementById('ordersTable');
    if (!table) return;

    // Ensure table has bootstrap table classes and is visible only on md+ (desktop/tablet)
    table.classList.add('table', 'table-striped', 'table-hover', 'd-none', 'd-md-table');
    // Wrap table in .table-responsive if not already
    if (!table.parentElement.classList.contains('table-responsive')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive d-none d-md-block'; // only for md+
        table.parentElement.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    } else {
        // ensure responsive wrapper visible only on md+
        table.parentElement.classList.add('d-none', 'd-md-block');
    }

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    // Create or update mobile cards container (visible on small screens only)
    let cardsContainer = document.getElementById('ordersCardsContainer');
    if (!cardsContainer) {
        cardsContainer = document.createElement('div');
        cardsContainer.id = 'ordersCardsContainer';
        cardsContainer.className = 'd-block d-md-none'; // visible on small only
        table.parentElement.parentElement.insertBefore(cardsContainer, table.parentElement.nextSibling);
    }

    // Helper to map status to bootstrap badge classes
    const statusBadgeClass = (status) => {
        const s = String(status || '').toLowerCase();
        if (s.includes('pending') || s.includes('pendente')) return 'badge bg-warning text-dark';
        if (s.includes('completed') || s.includes('concluído') || s.includes('concluido')) return 'badge bg-success';
        if (s.includes('cancel') || s.includes('cancelled') || s.includes('cancelado')) return 'badge bg-danger';
        if (s.includes('processing') || s.includes('processando')) return 'badge bg-info text-dark';
        return 'badge bg-secondary';
    };

    // Build desktop table rows (md+)
    const tableRows = orders.map(order => {
        const id = order.id;
        const itemsId = `items-section-${id}`;
        const hasItems = Array.isArray(order.items) && order.items.length > 0;

        const itemsRows = hasItems ? order.items.map(item => `
            <tr>
                <td>${item.product?.name || 'N/A'}</td>
                <td>${item.quantity}</td>
                <td>R$ ${Number(item.price || 0).toFixed(2)}</td>
                <td>R$ ${Number(item.total || (item.quantity * item.price) || 0).toFixed(2)}</td>
            </tr>
        `).join('') : `<tr><td colspan="4">No items</td></tr>`;

        return `
            <tr>
                <td>#${id}</td>
                <td>${order.ifood_order_number || 'N/A'}</td>
                <td>R$ ${Number(order.total_amount_order || 0).toFixed(2)}</td>
                <td>R$ ${Number(order.total_amount_received || 0).toFixed(2)}</td>
                <td><span class="${statusBadgeClass(order.status)}">${order.status || 'N/A'}</span></td>
                <td>${order.order_date || 'N/A'}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary" onclick="editOrder(${id})"><i class="bi bi-pencil"></i></button>
                        ${hasItems ? `<button class="btn btn-outline-success" type="button" data-bs-toggle="collapse" data-bs-target="#${itemsId}" aria-expanded="false"><i class="fa-solid fa-box"></i></button>` : ''}
                        <button class="btn btn-outline-danger" onclick="deleteOrder(${id})"><i class="bi bi-trash"></i></button>
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
                        <tbody>
                            ${itemsRows}
                        </tbody>
                    </table>
                </td>
            </tr>
            ` : ''}
        `;
    }).join('');

    tbody.innerHTML = tableRows || `
        <tr>
            <td colspan="7" class="text-center">Nenhum pedido encontrado.</td>
        </tr>
    `;

    // Build mobile cards
    const mobileHtml = orders.map(order => {
        const id = order.id;
        const itemsId = `items-mobile-${id}`;
        const hasItems = Array.isArray(order.items) && order.items.length > 0;

        const itemsHtml = hasItems ? order.items.map(item => `
            <div class="d-flex justify-content-between">
                <div>${item.product?.name || 'N/A'}</div>
                <div>${item.quantity} x R$ ${Number(item.price || 0).toFixed(2)} = R$ ${Number(item.total || (item.quantity * item.price) || 0).toFixed(2)}</div>
            </div>
        `).join('') : `<div class="text-muted">No items</div>`;

        return `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-1">#${id} ${order.ifood_order_number ? `- ${order.ifood_order_number}` : ''}</h6>
                            <div class="small text-muted">${order.order_date || 'N/A'}</div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">Total: <strong>R$ ${Number(order.total_amount_order || 0).toFixed(2)}</strong></div>
                            <div class="mb-1">Recebido: <strong>R$ ${Number(order.total_amount_received || 0).toFixed(2)}</strong></div>
                            <div>${order.status ? `<span class="${statusBadgeClass(order.status)}">${order.status}</span>` : ''}</div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editOrder(${id})"><i class="bi bi-pencil"></i></button>
                        ${hasItems ? `<button class="btn btn-sm btn-outline-success" type="button" data-bs-toggle="collapse" data-bs-target="#${itemsId}" aria-expanded="false"><i class="fa-solid fa-box"></i></button>` : ''}
                        </div>
                            <button class="btn btn-sm btn-outline-danger me-1" onclick="deleteOrder(${id})"><i class="bi bi-trash"></i></button>
                    </div>

                    ${hasItems ? `
                    <div class="collapse mt-3" id="${itemsId}">
                        <div class="card card-body p-2">
                            ${itemsHtml}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');

    cardsContainer.innerHTML = mobileHtml || `<div class="p-3 text-center text-muted">Nenhum pedido encontrado.</div>`;
};

window.filterOrders = function () {
    const search = document.getElementById('orderSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    if (search) {
        getAllOrders(1, {search}).then(r => {});
    } else if (status) {
        getAllOrders(1, {status}).then(r => {});
    }
};

window.setOrderModalTitle = function (isEdit) {
    const title       = document.getElementById('orderModalTitle');
    title.textContent = isEdit ? 'Editar Pedido' : 'Adicionar Pedido';
    document.getElementById('saveOrder').classList.toggle('d-none', isEdit);
    document.getElementById('updateOrder').classList.toggle('d-none', !isEdit);
};

window.fillOrderForm = function (order) {
    document.getElementById('orderId').value     = order.id || '';
    document.getElementById('orderStatus').value = order.status || '';
    document.getElementById('orderDate').value   = order.date || new Date().toISOString().split('T')[0];
    // If order.items and order.amount exist, set them, else clear
    if (order.items !== undefined) {
        document.getElementById('orderItems').value = order.items;
    }
    if (order.amount !== undefined) {
        document.getElementById('receivedAmount').value = order.amount;
    }
};

window.resetOrderForm = function () {
    document.getElementById('orderForm').reset();
    document.getElementById('orderId').value   = '';
    document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];
};

window.setupAddItem = function (addItemBtn, itemsTableBody, receivedAmountRef) {
    // Remove previous event listeners by cloning
    const newAddBtn = addItemBtn.cloneNode(true);
    addItemBtn.parentNode.replaceChild(newAddBtn, addItemBtn);

    let orderTotal = 0;

    newAddBtn.addEventListener("click", () => {
        const product_id = document.getElementById("itemId").value;
        const quantity   = parseInt(document.getElementById("itemQuantity").value);
        const price      = parseFloat(document.getElementById("itemPrice").value);
        const total      = (quantity * price).toFixed(2);
        window.orderItems.push({product_id, quantity, price, total});

        if (!product_id || isNaN(quantity) || isNaN(price)) {
            alert("Preencha todos os campos do item");
            return;
        }
        orderTotal += +total;
        receivedAmountRef.value = orderTotal.toFixed(2);

        const row     = document.createElement("tr");
        row.innerHTML = `
              <td class="text-truncate" id="itemid_${product_id}">${product_id}</td>
              <td class="text-truncate" id="itemQuantity_${quantity}">${quantity}</td>
              <td class="text-truncate" id="itemPrice_${price}">R$ ${price.toFixed(2)}</td>
              <td class="text-truncate" id="itemTotal_${total}">R$ ${total}</td>
              <td><button type="button" class="btn btn-sm btn-danger remove-item">X</button></td>
            `;

        row.querySelector(".remove-item").addEventListener("click", () => {
            row.remove();
            orderTotal -= +total;
            receivedAmountRef.value = orderTotal.toFixed(2);
            window.orderItems       = orderItems.filter(item => item.product_id + item.quantity !== product_id + quantity);

        });
        itemsTableBody.appendChild(row);

        // limpa os campos
        document.getElementById("itemId").value       = "";
        document.getElementById("itemQuantity").value = 1;
        document.getElementById("itemPrice").value    = "";
    });
    return newAddBtn;
};

window.openOrderModal = function (order = null) {
    const modal  = new bootstrap.Modal(document.getElementById('orderModal'));
    const isEdit = !!order;

    setOrderModalTitle(isEdit);

    if (isEdit) {
        fillOrderForm(order);
    } else {
        resetOrderForm();
    }

    const toggleItems    = document.getElementById("toggleItems");
    const itemsSection   = document.getElementById("itemsSection");
    const addItemBtn     = document.getElementById("addItem");
    const itemsTableBody = document.getElementById("itemsTableBody");
    let receivedAmount   = document.getElementById("receivedAmount");

    setupAddItem(addItemBtn, itemsTableBody, receivedAmount);

    modal.show();
};

window.saveOrder = async function (action = 'create', id = null) {

    // Validate that the element with id 'orderItems' exists
    if (!window.orderItems || window.orderItems.length === 0) {
        window.modalMessage({
            title      : 'Erro',
            description: 'Nenhum item adicionado ao pedido',
            type       : 'error',
        });
        showLoading(false);
        return;
    }
    const orderItems       = window.orderItems;
    const receivedAmount   = parseFloat(document.getElementById('receivedAmount').value);
    const ifoodOrderNumber = document.getElementById('ifoodOrderNumber').value;
    const customerAmount   = parseFloat(document.getElementById('customerAmount').value) || 0;
    const ifoodId          = document.getElementById('orderIdIfood').value;
    const orderStatus      = document.getElementById('orderStatus').value;
    const orderDate        = document.getElementById('orderDate').value;

    let route = 'orders/new_order';
    if (action !== 'create') {
        route = `orders/edit/${id}`;
    }

    let options = {
        method : 'POST',
        headers: window.ajax_headers,
        body   : JSON.stringify({
            ifoodId,
            orderStatus,
            orderDate,
            orderItems,
            receivedAmount,
            customerAmount,
            ifoodOrderNumber
        }),
    };

    showLoading(true);
    let response = await fetch(`${route}`, options);
    let retorno  = await response.json();

    if (retorno) {
        if (retorno?.status === 200 && retorno?.data) {
            window.modalMessage({
                title      : retorno.message,
                description: retorno.message,
                type       : 'success',
            });
        }
    } else {
        window.modalMessage({
            title      : 'Erro ao criar pedido',
            description: 'Ocorreu um erro ao criar o pedido',
            type       : 'error',
        });
    }
    showLoading(false);
    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
};

window.editOrder = function (id) {
    openOrderModal(order);
};

window.deleteOrder = function (id) {
    if (confirm('Are you sure you want to delete this order?')) {
        orders = orders.filter(o => o.id !== id);
        loadOrdersPage();
    }
};


window.importOrders = function () {
    let input = document.getElementById('ordersFile');
    if (input.files.length === 0) {
        alert("Selecione um arquivo!");
        return;
    }

    let formData = new FormData();
    formData.append('file', input.files[0]);
    window.showLoading(true);

    fetch("orders/import", {
        method : "POST",
        body   : formData,
        headers: {
            'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(res => res.json())
        .then(data => {
            window.showLoading(false);
            window.modalMessage({
                title      : data.message,
                description: data.errors ? data.errors?.description : data.message,
                type       : data.status === 200 ? 'success' : 'error'
            });
            getAllOrders();
        }).catch(err => {
        window.showLoading(false);
        window.modalMessage({
            title      : 'Erro na importação',
            description: err.message,
            type       : 'error'
        });
        console.error(err);
    });
};

// Add event listeners
document.getElementById('orderSearch').addEventListener('input', filterOrders);
document.getElementById('statusFilter').addEventListener('change', filterOrders);


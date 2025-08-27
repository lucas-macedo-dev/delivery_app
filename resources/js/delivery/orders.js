'use strict';

window.onload = () => {
    getAllOrders();
};

window.getAllOrders = async function (page = 1, filterParams = {}) {

    const possibleParams = ['search', 'status'];
    const queryParams = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (possibleParams.includes(key) && value) {
            queryParams.set(key, value);
        }
    });
    console.log(queryParams.toString());

    let orders = await fetch('./orders/showAll?page=' + page + '&' + queryParams.toString());
    let response = await orders.json();
    if (response?.status === 200 && response?.data?.orders) {
        renderOrdersTable(response.data.orders);
        renderStatusFilter(response.data.orders);
    } else {
        document.getElementById('ordersTableBody').innerHTML = `
            <tr>
                <td colspan="7" class="text-center"> Nenhum cliente encontrado.
                </td>
            </tr>
        `;
    }

    window.pagination({
        page: page,
        total: response?.data?.meta?.total,
        max: response?.data?.meta?.per_page,
        qtt: 5,
        id: `pagination`,
        callback: `getAllOrders`
    });
};

window.renderStatusFilter = function (orders) {
    const statusFilter = document.getElementById('statusFilter');
    const status = [...new Set(orders.map(order => order.status))];

    statusFilter.innerHTML = '<option value="all">Todos Status</option>' +
        status.map(status => `<option value="${status}" ${status === statusFilter.value ? 'selected' : ''}>${status}</option>`).join('');

    statusFilter.classList.remove('disabled');
};

window.renderOrdersTable = function (orders) {
    let rows = '';
    const tbody = document.querySelector('#ordersTable tbody');
    if (!tbody) return;
    rows = orders.map(order => `
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

window.filterOrders = function () {
    const search = document.getElementById('orderSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    if (search) {
        getAllOrders(1, { search });
    } else if (status) {
        getAllOrders(1, { status});
    }
};

window.openOrderModal = function (order = null) {
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    const title = document.getElementById('orderModalTitle');
    const customerSelect = document.getElementById('orderCustomer');

    // Populate customer options
    customerSelect.innerHTML = '<option value="">Select customer</option>' +
        orders.map(c => `<option value="${c.name}">${c.name}</option>`).join('');

    if (order) {
        title.textContent = 'Edit Order';
        document.getElementById('orderId').value = order.id;
        document.getElementById('orderCustomer').value = order.customer;
        document.getElementById('orderStatus').value = order.status;
        document.getElementById('orderAddress').value = order.address;
        document.getElementById('orderDate').value = order.date;
        document.getElementById('orderItems').value = order.items;
        document.getElementById('orderAmount').value = order.amount;
    } else {
        title.textContent = 'Add New Order';
        document.getElementById('orderForm').reset();
        document.getElementById('orderId').value = '';
        document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];
    }

    modal.show();
};

window.saveOrder = function () {
    const id = document.getElementById('orderId').value;
    const customer = document.getElementById('orderCustomer').value;
    const status = document.getElementById('orderStatus').value;
    const address = document.getElementById('orderAddress').value;
    const date = document.getElementById('orderDate').value;
    const items = document.getElementById('orderItems').value;
    const amount = parseFloat(document.getElementById('orderAmount').value);

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
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(res => res.json())
        .then(data => {
            window.showLoading(false);
            window.modalMessage({
                title: data.message,
                description: data.errors ? data.errors?.description : data.message,
                type: data.status === 200 ? 'success' : 'error'
            });
            getAllOrders();
        }).catch(err => {
            window.showLoading(false);
            window.modalMessage({
                title: 'Erro na importação',
                description: err.message,
                type: 'error'
            });
            console.error(err);
        });
};

// Add event listeners
document.getElementById('orderSearch').addEventListener('input', filterOrders);
document.getElementById('statusFilter').addEventListener('change', filterOrders);


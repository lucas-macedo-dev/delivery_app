// Mock data storage
let orders = [
    { id: 1, customer: 'John Doe', status: 'Delivered', amount: 142.50, date: '2025-01-18', address: '123 Main St, City', items: 'Pizza, Drinks' },
    { id: 2, customer: 'Jane Smith', status: 'In Transit', amount: 89.20, date: '2025-01-18', address: '456 Oak Ave, Town', items: 'Burger, Fries' },
    { id: 3, customer: 'Mike Johnson', status: 'Processing', amount: 267.80, date: '2025-01-17', address: '789 Pine St, Village', items: 'Pasta, Salad' },
    { id: 4, customer: 'Sarah Wilson', status: 'Delivered', amount: 156.90, date: '2025-01-17', address: '321 Elm St, City', items: 'Sushi, Soup' },
    { id: 5, customer: 'Tom Brown', status: 'Pending', amount: 98.75, date: '2025-01-16', address: '654 Maple Ave, Town', items: 'Sandwich, Coffee' }
];

function renderOrdersTable() {
    return orders.map(order => `
        <tr>
            <td>#${order.id.toString().padStart(3, '0')}</td>
            <td>${order.customer}</td>
            <td><span class="status-badge status-${order.status.toLowerCase().replace(' ', '-')}">${order.status}</span></td>
            <td>$${order.amount.toFixed(2)}</td>
            <td>${order.date}</td>
            <td>${order.address}</td>
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
}

function filterOrders() {
    const search = document.getElementById('orderSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;

    let filtered = orders;

    if (search) {
        filtered = filtered.filter(order =>
            order.customer.toLowerCase().includes(search) ||
            order.id.toString().includes(search) ||
            order.address.toLowerCase().includes(search)
        );
    }

    if (statusFilter) {
        filtered = filtered.filter(order => order.status === statusFilter);
    }

    const tbody = document.querySelector('#ordersTable tbody');
    tbody.innerHTML = filtered.map(order => `
        <tr>
            <td>#${order.id.toString().padStart(3, '0')}</td>
            <td>${order.customer}</td>
            <td><span class="status-badge status-${order.status.toLowerCase().replace(' ', '-')}">${order.status}</span></td>
            <td>$${order.amount.toFixed(2)}</td>
            <td>${order.date}</td>
            <td>${order.address}</td>
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
}

function openOrderModal(order = null) {
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    const title = document.getElementById('orderModalTitle');
    const customerSelect = document.getElementById('orderCustomer');

    // Populate customer options
    customerSelect.innerHTML = '<option value="">Select customer</option>' +
        customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');

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
}


function saveOrder() {
    const id = document.getElementById('orderId').value;
    const customer = document.getElementById('orderCustomer').value;
    const status = document.getElementById('orderStatus').value;
    const address = document.getElementById('orderAddress').value;
    const date = document.getElementById('orderDate').value;
    const items = document.getElementById('orderItems').value;
    const amount = parseFloat(document.getElementById('orderAmount').value);

    if (id) {
        // Update existing order
        const index = orders.findIndex(o => o.id == id);
        orders[index] = { id: parseInt(id), customer, status, address, date, items, amount };
    } else {
        // Add new order
        const newId = Math.max(...orders.map(o => o.id)) + 1;
        orders.push({ id: newId, customer, status, address, date, items, amount });
    }

    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
    loadOrdersPage();
}

function editOrder(id) {
    const order = orders.find(o => o.id === id);
    openOrderModal(order);
}

function deleteOrder(id) {
    if (confirm('Are you sure you want to delete this order?')) {
        orders = orders.filter(o => o.id !== id);
        loadOrdersPage();
    }
}


const content = `
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Gerenciar Pedidos</h1>
                    <p class="page-subtitle">Gerencie seus pedidos</p>
                </div>
                <button class="btn btn-primary" onclick="openOrderModal()">
                    <i class="bi bi-plus me-2"></i>Adicionar Pedido
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group search-box">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search orders..." id="orderSearch">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex action-buttons justify-content-md-end">
                            <select class="form-select" id="statusFilter" style="max-width: 150px;">
                                <option value="">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
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
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderOrdersTable()}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

document.getElementById('page-content').innerHTML = content;

// Add event listeners
document.getElementById('orderSearch').addEventListener('input', filterOrders);
document.getElementById('statusFilter').addEventListener('change', filterOrders);


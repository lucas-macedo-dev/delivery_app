let customers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', phone: '+1 (555) 123-4567', address: '123 Main St, City, State 12345' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '+1 (555) 234-5678', address: '456 Oak Ave, Town, State 67890' },
    { id: 3, name: 'Mike Johnson', email: 'mike@example.com', phone: '+1 (555) 345-6789', address: '789 Pine St, Village, State 54321' },
    { id: 4, name: 'Sarah Wilson', email: 'sarah@example.com', phone: '+1 (555) 456-7890', address: '321 Elm St, City, State 98765' }
];

function openCustomerModal(customer = null) {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    const title = document.getElementById('customerModalTitle');
    
    if (customer) {
        title.textContent = 'Edit Customer';
        document.getElementById('customerId').value = customer.id;
        document.getElementById('customerName').value = customer.name;
        document.getElementById('customerEmail').value = customer.email;
        document.getElementById('customerPhone').value = customer.phone;
        document.getElementById('customerAddress').value = customer.address;
    } else {
        title.textContent = 'Add New Customer';
        document.getElementById('customerForm').reset();
        document.getElementById('customerId').value = '';
    }
    
    modal.show();
}

function saveCustomer() {
    const id = document.getElementById('customerId').value;
    const name = document.getElementById('customerName').value;
    const email = document.getElementById('customerEmail').value;
    const phone = document.getElementById('customerPhone').value;
    const address = document.getElementById('customerAddress').value;
    
    if (id) {
        // Update existing customer
        const index = customers.findIndex(c => c.id == id);
        customers[index] = { id: parseInt(id), name, email, phone, address };
    } else {
        // Add new customer
        const newId = Math.max(...customers.map(c => c.id)) + 1;
        customers.push({ id: newId, name, email, phone, address });
    }
    
    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
    loadCustomersPage();
}

function editCustomer(id) {
    const customer = customers.find(c => c.id === id);
    openCustomerModal(customer);
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        customers = customers.filter(c => c.id !== id);
        loadCustomersPage();
    }
}

const content = `
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Gerenciar Clientes</h1>
                    <p class="page-subtitle">Gerencie seus clientes</p>
                </div>
                <button class="btn btn-primary" onclick="openCustomerModal()">
                    <i class="bi bi-plus me-2"></i>Adicionar Cliente
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${customers.map(customer => `
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                ${customer.name.split(' ').map(n => n[0]).join('')}
                                            </div>
                                            ${customer.name}
                                        </div>
                                    </td>
                                    <td>${customer.email}</td>
                                    <td>${customer.phone}</td>
                                    <td>${customer.address}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editCustomer(${customer.id})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(${customer.id})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('page-content').innerHTML = content;
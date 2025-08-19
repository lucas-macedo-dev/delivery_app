// Mock data storage
let orders = [
    { id: 1, customer: 'John Doe', status: 'Delivered', amount: 142.50, date: '2025-01-18', address: '123 Main St, City', items: 'Pizza, Drinks' },
    { id: 2, customer: 'Jane Smith', status: 'In Transit', amount: 89.20, date: '2025-01-18', address: '456 Oak Ave, Town', items: 'Burger, Fries' },
    { id: 3, customer: 'Mike Johnson', status: 'Processing', amount: 267.80, date: '2025-01-17', address: '789 Pine St, Village', items: 'Pasta, Salad' },
    { id: 4, customer: 'Sarah Wilson', status: 'Delivered', amount: 156.90, date: '2025-01-17', address: '321 Elm St, City', items: 'Sushi, Soup' },
    { id: 5, customer: 'Tom Brown', status: 'Pending', amount: 98.75, date: '2025-01-16', address: '654 Maple Ave, Town', items: 'Sandwich, Coffee' }
];

let products = [
    { id: 1, name: 'Premium Pizza', value: 24.99, stock: 50, unit: 'pcs', image: 'https://images.pexels.com/photos/315755/pexels-photo-315755.jpeg', available: true },
    { id: 2, name: 'Gourmet Burger', value: 18.50, stock: 30, unit: 'pcs', image: 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg', available: true },
    { id: 3, name: 'Fresh Salad', value: 12.99, stock: 25, unit: 'pcs', image: 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg', available: true },
    { id: 4, name: 'Craft Beer', value: 8.99, stock: 100, unit: 'bottle', image: 'https://images.pexels.com/photos/159291/beer-machine-alcohol-brewery-159291.jpeg', available: true },
    { id: 5, name: 'Artisan Coffee', value: 4.50, stock: 80, unit: 'cup', image: 'https://images.pexels.com/photos/302899/pexels-photo-302899.jpeg', available: true }
];

let customers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', phone: '+1 (555) 123-4567', address: '123 Main St, City, State 12345' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '+1 (555) 234-5678', address: '456 Oak Ave, Town, State 67890' },
    { id: 3, name: 'Mike Johnson', email: 'mike@example.com', phone: '+1 (555) 345-6789', address: '789 Pine St, Village, State 54321' },
    { id: 4, name: 'Sarah Wilson', email: 'sarah@example.com', phone: '+1 (555) 456-7890', address: '321 Elm St, City, State 98765' }
];

let feePayments = [
    { id: 1, customer: 'John Doe', amount: 25.00, type: 'Delivery Fee', status: 'Paid', date: '2025-01-18' },
    { id: 2, customer: 'Jane Smith', amount: 15.00, type: 'Service Fee', status: 'Pending', date: '2025-01-18' },
    { id: 3, customer: 'Mike Johnson', amount: 30.00, type: 'Delivery Fee', status: 'Paid', date: '2025-01-17' },
    { id: 4, customer: 'Sarah Wilson', amount: 20.00, type: 'Processing Fee', status: 'Overdue', date: '2025-01-15' }
];


// const content = `
//         <div class="page-header">
//             <h1 class="page-title">Dashboard</h1>
//             <p class="page-subtitle">Welcome to your delivery control system</p>
//         </div>
        
//         <div class="row mb-4">
//             <div class="col-md-3 mb-3">
//                 <div class="card stat-card stat-orders">
//                     <div class="card-body">
//                         <div class="d-flex justify-content-between align-items-center">
//                             <div>
//                                 <h6 class="card-subtitle mb-2">Total Orders</h6>
//                                 <h2 class="card-title mb-0">${orders.length}</h2>
//                                 <small class="text-light">+12% vs last month</small>
//                             </div>
//                             <i class="bi bi-cart fs-1 opacity-75"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//             <div class="col-md-3 mb-3">
//                 <div class="card stat-card stat-products">
//                     <div class="card-body">
//                         <div class="d-flex justify-content-between align-items-center">
//                             <div>
//                                 <h6 class="card-subtitle mb-2">Products</h6>
//                                 <h2 class="card-title mb-0">${products.length}</h2>
//                                 <small class="text-light">+5% vs last month</small>
//                             </div>
//                             <i class="bi bi-box fs-1 opacity-75"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//             <div class="col-md-3 mb-3">
//                 <div class="card stat-card stat-customers">
//                     <div class="card-body">
//                         <div class="d-flex justify-content-between align-items-center">
//                             <div>
//                                 <h6 class="card-subtitle mb-2">Customers</h6>
//                                 <h2 class="card-title mb-0">${customers.length}</h2>
//                                 <small class="text-light">+8% vs last month</small>
//                             </div>
//                             <i class="bi bi-people fs-1 opacity-75"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//             <div class="col-md-3 mb-3">
//                 <div class="card stat-card stat-revenue">
//                     <div class="card-body">
//                         <div class="d-flex justify-content-between align-items-center">
//                             <div>
//                                 <h6 class="card-subtitle mb-2">Revenue</h6>
//                                 <h2 class="card-title mb-0">$${orders.reduce((sum, order) => sum + order.amount, 0).toFixed(2)}</h2>
//                                 <small class="text-light">+15% vs last month</small>
//                             </div>
//                             <i class="bi bi-currency-dollar fs-1 opacity-75"></i>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
        
//         <div class="row">
//             <div class="col-lg-8 mb-4">
//                 <div class="card">
//                     <div class="card-header">
//                         <h5 class="card-title mb-0">Recent Orders</h5>
//                     </div>
//                     <div class="card-body p-0">
//                         <div class="table-responsive">
//                             <table class="table table-hover mb-0">
//                                 <thead>
//                                     <tr>
//                                         <th>Order ID</th>
//                                         <th>Customer</th>
//                                         <th>Status</th>
//                                         <th>Amount</th>
//                                         <th>Date</th>
//                                     </tr>
//                                 </thead>
//                                 <tbody>
//                                     ${orders.slice(0, 5).map(order => `
//                                         <tr>
//                                             <td>#${order.id.toString().padStart(3, '0')}</td>
//                                             <td>${order.customer}</td>
//                                             <td><span class="status-badge status-${order.status.toLowerCase().replace(' ', '-')}">${order.status}</span></td>
//                                             <td>$${order.amount.toFixed(2)}</td>
//                                             <td>${order.date}</td>
//                                         </tr>
//                                     `).join('')}
//                                 </tbody>
//                             </table>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//             <div class="col-lg-4 mb-4">
//                 <div class="card">
//                     <div class="card-header">
//                         <h5 class="card-title mb-0">Quick Actions</h5>
//                     </div>
//                     <div class="card-body">
//                         <div class="d-grid gap-2">
//                             <button class="btn btn-primary" onclick="navigateToPage('orders')">
//                                 <i class="bi bi-plus-circle me-2"></i>New Order
//                             </button>
//                             <button class="btn btn-outline-primary" onclick="navigateToPage('products')">
//                                 <i class="bi bi-box me-2"></i>Manage Products
//                             </button>
//                             <button class="btn btn-outline-primary" onclick="navigateToPage('customers')">
//                                 <i class="bi bi-person-plus me-2"></i>Add Customer
//                             </button>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;

// document.getElementById('page-content').innerHTML = content;


// Modal functions
function initializeModals() {
    // Product modal save
    document.getElementById('saveProduct').addEventListener('click', saveProduct);
    
    // Order modal save
    document.getElementById('saveOrder').addEventListener('click', saveOrder);
    
    // Customer modal save
    document.getElementById('saveCustomer').addEventListener('click', saveCustomer);
}

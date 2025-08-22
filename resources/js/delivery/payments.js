let feePayments = [
    { id: 1, customer: 'John Doe', amount: 25.00, type: 'Delivery Fee', status: 'Paid', date: '2025-01-18' },
    { id: 2, customer: 'Jane Smith', amount: 15.00, type: 'Service Fee', status: 'Pending', date: '2025-01-18' },
    { id: 3, customer: 'Mike Johnson', amount: 30.00, type: 'Delivery Fee', status: 'Paid', date: '2025-01-17' },
    { id: 4, customer: 'Sarah Wilson', amount: 20.00, type: 'Processing Fee', status: 'Overdue', date: '2025-01-15' }
];

function markAsPaid(id) {
    const fee = feePayments.find(f => f.id === id);
    if (fee) {
        fee.status = 'Paid';
        loadFeePaymentsPage();
    }
}

function generateReport() {
    alert('Report generation feature would be implemented here. This would typically generate a PDF or Excel file with payment details.');
}

const content = `
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Métodos de Pagamento</h1>
                    <p class="page-subtitle">Cadastre e gerencie os métodos de pagamento</p>
                </div>
                <button class="btn btn-primary" onclick="generateReport()">
                    <i class="bi bi-file-earmark-text me-2"></i>Gerar Relatório
                </button>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success">$${feePayments.filter(f => f.status === 'Paid').reduce((sum, f) => sum + f.amount, 0).toFixed(2)}</h4>
                        <p class="mb-0 text-muted">Total Collected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning">$${feePayments.filter(f => f.status === 'Pending').reduce((sum, f) => sum + f.amount, 0).toFixed(2)}</h4>
                        <p class="mb-0 text-muted">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-danger">$${feePayments.filter(f => f.status === 'Overdue').reduce((sum, f) => sum + f.amount, 0).toFixed(2)}</h4>
                        <p class="mb-0 text-muted">Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary">$${feePayments.reduce((sum, f) => sum + f.amount, 0).toFixed(2)}</h4>
                        <p class="mb-0 text-muted">Total Fees</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Fee Payments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${feePayments.map(fee => `
                                <tr>
                                    <td>${fee.customer}</td>
                                    <td>${fee.type}</td>
                                    <td>$${fee.amount.toFixed(2)}</td>
                                    <td>
                                        <span class="badge ${fee.status === 'Paid' ? 'bg-success' : fee.status === 'Pending' ? 'bg-warning' : 'bg-danger'}">
                                            ${fee.status}
                                        </span>
                                    </td>
                                    <td>${fee.date}</td>
                                    <td>
                                        ${fee.status !== 'Paid' ? `
                                            <button class="btn btn-sm btn-success" onclick="markAsPaid(${fee.id})">
                                                <i class="bi bi-check-circle me-1"></i>Mark Paid
                                            </button>
                                        ` : `
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Paid</span>
                                        `}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    //document.getElementById('page-content').innerHTML = content;
@extends('delivery.home')
@vite(['resources/js/delivery/customers.js'])
@section('content')

<!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalTitle">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <input type="hidden" id="customerId">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="customerPhone" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="customerAddress" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCustomer">Save Customer</button>
                </div>
            </div>
        </div>
    </div>
@endsection
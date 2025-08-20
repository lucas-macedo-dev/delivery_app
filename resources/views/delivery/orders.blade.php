@extends('header')
@vite(['resources/js/delivery/orders.js'])
@section('content')
     <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalTitle">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="orderId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderCustomer" class="form-label">Customer</label>
                                    <select class="form-select" id="orderCustomer" required>
                                        <option value="">Select customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderStatus" class="form-label">Status</label>
                                    <select class="form-select" id="orderStatus" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Processing">Processing</option>
                                        <option value="In Transit">In Transit</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="orderAddress" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="orderAddress" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="orderDate" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="orderDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="orderItems" class="form-label">Items</label>
                            <textarea class="form-control" id="orderItems" rows="3" placeholder="Enter order items"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="orderAmount" class="form-label">Total Amount</label>
                            <input type="number" class="form-control" id="orderAmount" step="0.01" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveOrder">Save Order</button>
                </div>
            </div>
        </div>
    </div>
@endsection

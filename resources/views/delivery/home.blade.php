@extends('header')
@section('content')
    <header class="d-flex justify-content-between align-items-center">
        <h1> <i class="bi bi-speedometer2"></i>&nbsp;Visão Geral</h1>
    </header>

    <div class="row mt-4">
        <div class="col col-md-3 my-1">
            <div class="card text-center">
                <div class="card-body bg-info-subtle">
                    <h5 class="card-title">
                        <i class="bi bi-people"></i>&nbsp;Clientes
                    </h5>
                    <p class="card-text">1,234</p>
                </div>
            </div>
        </div>
        <div class="col col-md-3 my-1">
            <div class="card text-center">
                <div class="card-body bg-info-subtle">
                    <h5 class="card-title"><i class="bi bi-truck"></i>&nbsp;Pedidos</h5>
                    <p class="card-text">423</p>
                </div>
            </div>
        </div>
        <div class="col col-md-3 my-1">
            <div class="card text-center">
                <div class="card-body bg-success-subtle">
                    <h5 class="card-title"><i class="bi bi-cart"></i>&nbsp;Faturamento</h5>
                    <p class="card-text">R$ 12,345</p>
                </div>
            </div>
        </div>
        <div class="col col-md-3 my-1">
            <div class="card text-center">
                <div class="card-body bg-danger-subtle">
                    <h5 class="card-title"><i class="bi bi-graph-down-arrow"></i>&nbsp;Gastos</h5>
                    <p class="card-text">R$ 567,00</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">User John Doe added a new product.</li>
                        <li class="list-group-item">User Jane Smith updated their profile.</li>
                        <li class="list-group-item">User Alex Johnson made a purchase.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

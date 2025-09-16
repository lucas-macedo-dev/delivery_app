@extends('header')
@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient"
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Bem-vindo ao Dashboard</h2>
                            <p class="mb-0 opacity-75">Gerencie seu negócio de delivery com facilidade</p>
                            <small class="opacity-50">Última atualização: {{ date('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <i class="bi bi-speedometer2" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body text-center position-relative overflow-hidden">
                    <div
                        class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total de Clientes</h6>
                    <h3 class="mb-2 fw-bold text-primary">{{number_format($data['customers'] ?? 0, 0, ',', '.')}}</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +5% este mês
                    </small>
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body text-center position-relative overflow-hidden">
                    <div
                        class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-truck text-info fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Pedidos Total</h6>
                    <h3 class="mb-2 fw-bold text-info">{{number_format($data['orders'] ?? 0)}}</h3>
                    <small class="text-warning">
                        <i class="bi bi-clock"></i> {{ $data['pending_orders'] ?? 0 }} pendentes
                    </small>
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body text-center position-relative overflow-hidden">
                    <div
                        class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-currency-dollar text-success fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Faturamento</h6>
                    <h3 class="mb-2 fw-bold text-success">R$ {{number_format($data['amount'] ?? 0, 2, ',', '.')}}</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +12% este mês
                    </small>
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body text-center position-relative overflow-hidden">
                    <div
                        class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-graph-down-arrow text-danger fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Gastos</h6>
                    <h3 class="mb-2 fw-bold text-danger">R$ {{number_format($data['expenses'] ?? 0, 2, ',', '.')}}</h3>
                    <small class="text-muted">
                        <i class="bi bi-calendar3"></i> Este mês
                    </small>
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row d-none g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div
                        class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-box text-warning fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Produtos</h6>
                    <h4 class="mb-0 fw-bold">{{ $data['total_products'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div
                        class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-star text-primary fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Avaliação Média</h6>
                    <h4 class="mb-0 fw-bold">{{ $data['avg_rating'] ?? '4.5' }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div
                        class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-clock text-info fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Tempo Médio</h6>
                    <h4 class="mb-0 fw-bold">{{ $data['avg_delivery_time'] ?? '35' }}min</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div
                        class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-repeat text-success fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Taxa Retorno</h6>
                    <h4 class="mb-0 fw-bold">{{ $data['return_rate'] ?? '68' }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Vendas dos Últimos 7 Dias</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="period" id="week" checked>
                            <label class="btn btn-outline-primary" for="week">7 dias</label>
                            <input type="radio" class="btn-check" name="period" id="month">
                            <label class="btn btn-outline-primary" for="month">30 dias</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">Produtos Mais Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-success rounded" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Pizza Margherita</h6>
                            <small class="text-muted">156 vendas</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-success">1º</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary rounded" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Hambúrguer Clássico</h6>
                            <small class="text-muted">142 vendas</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">2º</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-warning rounded" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Refrigerante Coca</h6>
                            <small class="text-muted">128 vendas</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-warning">3º</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">Pedidos Recentes</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                            @foreach ($data['last_orders'] as $order)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">Pedido #{{$order['id']}}</h6>
                                    <small class="text-muted">Total recebido - R$ {{$order['total_amount_received']}}</small>
                                </div>
                                <span class="badge {{ $order['status'] == 'completed' ? 'bg-success' : 'bg-danger'}} ">{{ $order['status'] }}</span>
                            </div>
                            @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="./orders" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle mb-2 d-block fs-4"></i>
                                Novo Pedido
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="./products" class="btn btn-outline-success w-100">
                                <i class="bi bi-box mb-2 d-block fs-4"></i>
                                Produtos
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="./customers" class="btn btn-outline-info w-100">
                                <i class="bi bi-people mb-2 d-block fs-4"></i>
                                Clientes
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="./expenses" class="btn btn-outline-danger w-100">
                                <i class="bi bi-graph-down mb-2 d-block fs-4"></i>
                                Despesas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        #salesChart {
            max-height: 300px;
        }
    </style>
@endsection

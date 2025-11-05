@extends('header')
@section('title', 'Dashboard')
@vite('resources/js/delivery/home.js')
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

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel-fill"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row justify-content-center g-2 g-md-3">
                <div class="col-12 col-md-4 col-xl-4 mb-2 mb-xl-0">
                    <label for="startDate" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="startDate" value="{{\Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-12 col-md-4 col-xl-4 mb-2 mb-xl-0">
                    <label for="endDate" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="endDate" value="{{\Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
                <div class="col-12 d-flex col-md-2 col-xl-2  align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100"
                            onclick="clearFilters()">
                        <i class="bi bi-x me-1"></i>
                        <span class="d-none d-sm-inline">Limpar</span>
                        <span class="d-sm-none">Limpar Filtros</span>
                    </button>
                </div>
                <div class="col-12 d-flex col-md-2 col-xl-2 align-items-end">
                    <button type="button" class="btn btn-primary w-100" onclick="searchData()">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">Buscar</span>
                        <span class="d-sm-none">Aplicar Filtros</span>
                    </button>
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
                    <h3 class="mb-2 fw-bold text-primary" id="total_customers">{{number_format( 0, 0, ',', '.')}}</h3>
                    <small class="text-success d-none" id="total_customers_percentage">
                        <i class="bi bi-arrow-up"></i> -% este mês
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
                    <h6 class="text-muted mb-1">Total de Pedidos</h6>
                    <h3 class="mb-2 fw-bold text-info" id="total_orders">0</h3>
                    <small class="text-warning d-none">
                        <i class="bi bi-clock"></i> {{ 0 }} pendentes
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
                    <h3 class="mb-2 fw-bold text-success" id="total_amount">R$ {{number_format( 0, 2, ',', '.')}}</h3>
                    <small class="text-success d-none">
                        <i class="bi bi-arrow-up"></i> -% este mês
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
                    <h3 class="mb-2 fw-bold text-danger" id="total_expenses">
                        R$ {{number_format($data['expenses'] ?? 0, 2, ',', '.')}}</h3>
                    <small class="text-muted d-none">
                        <i class="bi bi-calendar3"></i> -% Este mês
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
                    <h4 class="mb-0 fw-bold">{{ $data['avg_rating'] ?? '-' }}</h4>
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
                    <h4 class="mb-0 fw-bold">{{ $data['avg_delivery_time'] ?? '-' }}min</h4>
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
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-center align-items-center">
                        <h5 class="mb-0">Vendas dos Últimos 7 Dias</h5>
                    </div>
                </div>
                <div class="card-body text-center">
                     <canvas id="salesChart" height="300"></canvas>
                    {{-- <img src="{{ asset('images/em_construcao.png') }}" style="max-width: 80%; height: auto;"
                         class="img-fluid" alt="Página em construção"> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0"><i class="fa-solid fa-money-bill-trend-up"></i>&nbsp;Produtos Mais Vendidos</h5>
                </div>
                <div class="card-body" id="most_saled_product">
                    <p class="text-muted mb-0">Nenhum produto vendido ainda.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0"><i class="fa-solid fa-clock"></i>&nbsp;Pedidos Recentes</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @if (!empty($data['last_orders']))
                            @foreach ($data['last_orders'] as $order)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1">Pedido #{{$order['id']}}</h6>
                                        <small class="text-muted">Total recebido -
                                            R$ {{$order['total_amount_received']}}</small>
                                    </div>
                                    <span
                                        class="badge {{ $order['status'] == 'completed' ? 'bg-success' : 'bg-danger'}} ">{{ $order['status']  == 'completed' ? 'Concluído' : 'Pendente'}}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">Nenhum pedido encontrado</h6>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 h-100 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0"><i class="fa-solid fa-bolt"></i>&nbsp;Ações Rápidas</h5>
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

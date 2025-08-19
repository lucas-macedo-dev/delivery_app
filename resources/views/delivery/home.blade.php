@extends('modal')
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Página Inicial</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css', 'resources/js/delivery/home.js'])
</head>

<body>
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
            <div class="text-center mb-4">
                <i class="bi bi-truck text-primary fs-1"></i>
                <h5 class="mt-2">Rock and Burger</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link  {{ request()->is('delivery/home') || request()->is('/') ? 'active' : '' }} "
                        href="{{ route('delivery.home') }}" data-page="home">
                        <i class="bi bi-house-door me-2 "></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('delivery/orders') ? 'active' : '' }}" href="{{ route('delivery.orders') }}"
                        data-page="orders">
                        <i class="bi bi-cart me-2"></i>
                        Pedidos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('delivery/products') ? 'active' : '' }}"
                        href="{{ route('delivery.products') }}" data-page="products">
                        <i class="bi bi-box me-2"></i>
                        Produtos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('delivery/customers') ? 'active' : '' }}" href="{{ route('delivery.customers') }}"
                        data-page="customers">
                        <i class="bi bi-people me-2"></i>
                        Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('delivery/payments') ? 'active' : '' }}" href="{{ route('delivery.payments') }}" data-page="fee-payments">
                        <i class="bi bi-credit-card me-2"></i>
                        Métodos de Pagamento
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <!-- Top navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="ms-auto">
                    @auth
                        <form id="logout_form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" id="btn_logout" class="btn bg-secondary-subtle text-dark me-2">
                                <i class="fa-solid fa-person-running"></i>&nbsp;Sair
                            </button>
                        </form>
                    @endauth

                    @guest
                        <button type="button" id="btn_login" class="btn btn-light text-dark me-2">
                            {{ __('Entrar') }}
                        </button>
                        <button type="button" id="btn_register" class="btn btn-primary">
                            {{ __('Registrar-se') }}
                        </button>
                    @endguest
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <div id="page-content" class="p-4">
            @yield('content')
        </div>
    </main>
</body>

</html>

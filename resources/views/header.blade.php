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
    <link rel="icon" href="{{ asset('images/logo.ico') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="pt-3">
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
                    <a class="nav-link {{ request()->is('delivery/orders') ? 'active' : '' }}"
                        href="{{ route('delivery.orders') }}" data-page="orders">
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
                    <a class="nav-link {{ request()->is('delivery/customers') ? 'active' : '' }}"
                        href="{{ route('delivery.customers') }}" data-page="customers">
                        <i class="bi bi-people me-2"></i>
                        Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('delivery/expenses') ? 'active' : '' }}"
                        href="{{ route('delivery.expenses') }}" data-page="fee-expenses">
                        <i class="bi bi-cash-coin"></i>
                        Despesas
                    </a>
                </li>
                @if (Auth::user() && Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}" data-page="users">
                            <i class="bi bi-person me-2"></i>
                            Usuários
                        </a>
                    </li>
                @endif
            </ul>
            <a class="nav-link d-sm-none bg-secondary-subtle text-dark" data-bs-toggle="collapse"
               data-bs-target="#sidebar">
                <i class="bi bi-list"></i>
                Fechar Menu
            </a>
        </div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10"> <!-- Top navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" id="main_navbar">
            <div class="container-fluid">
                <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="ms-auto">
                    @auth
                        <div class="dropdown">
                            <button class="btn bg-secondary-subtle text-dark me-2 dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>&nbsp;{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fa-solid fa-id-badge"></i>&nbsp;Perfil
                                    </a>
                                </li>
                                <li>
                                    <form id="logout_form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" id="btn_logout" class="dropdown-item">
                                            <i class="fa-solid fa-person-running"></i>&nbsp;Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
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

            <span class="back_to_top d-none" data-toggle="tooltip" data-bs-placement="left"
                title="Clique para voltar ao topo da p&aacute;gina">
                <i class="fas fa-arrow-alt-circle-up fa-2x"></i>
            </span>
        </div>
    </main>
</body>

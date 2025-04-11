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
        @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])
    </head>
    <body>
        <main>
            <header>
                <div class="px-3 py-2 text-white border-bottom" style="background-color: #ea525f">
                    <div class="container">
                        <div
                            class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                            <a href="/"
                               class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                                <i class="fa-solid fa-skull fa-2xl"></i>
                            </a>
                            @auth
                                <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                                    <li>
                                        <a href="{{route('delivery.home')}}"
                                           class="nav-link {{ request()->is('delivery/home') || request()->is('/') ? 'text-dark' : 'text-white' }}">
                                            <i class="fa-solid fa-house"></i>
                                            Home
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link text-white">
                                            <i class="fa-solid fa-gauge-high"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link text-white">
                                            <i class="fa-solid fa-table"></i>
                                            Orders
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('delivery.products') }}"
                                           class="nav-link {{ request()->is('delivery/products') ? 'text-dark' : 'text-white' }}">
                                            <i class="fa-solid fa-burger"></i>
                                            Products
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link text-white">
                                            <i class="fa-solid fa-users"></i>
                                            Customers
                                        </a>
                                    </li>
                                </ul>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="px-3 py-2 border-bottom mb-3">
                    <div class="container d-flex flex-wrap justify-content-center">
                        <form class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto" role="search">
                            <input type="search" class="form-control" placeholder="{{__('Search')}}..." aria-label="Search">
                        </form>
                        <div class="text-end">
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
                                    {{__('Entrar')}}
                                </button>
                                <button type="button" id="btn_register" class="btn btn-primary">
                                    {{__('Registrar-se')}}
                                </button>
                            @endguest
                        </div>
                    </div>
                </div>
            </header>
            <div class="b-example-divider"></div>
            @yield('content')
        </main>
    </body>
</html>

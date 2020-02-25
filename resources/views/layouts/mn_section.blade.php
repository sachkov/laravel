<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-159083370-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-159083370-1');
    </script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/prayers.css') }}" rel="stylesheet">
    @yield('css')
    <!-- Jquery UI CSS -->
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#ffffff">
    
</head>
<body class="h-full">
    <div class="body-page flex flex-col	h-full">
        <header class="bg-grey flex-1 header-piсture">
            <div class="container mx-auto">
                <div class="header-first-line">
                    <div class="header-logo">
                        <img class="" src="/img/cross.png" alt="logo-cross"/>
                        <div class="church-name">
                            <span class="name">пробуждение</span>
                            <span class="description">церковь евангельских христиан</span>
                        </div>
                        
                    </div>
                    <label for="menu-checkbox">
                        <img class="mobile-menu" src="/img/icons8-menu-filled.svg" alt="menu">
                    </label>
                    <input id="menu-checkbox" type="checkbox" style="display: none;"/>
                    <div class="enter_link">
                        @if (Auth::guest())
                            <a class="list" href="{{ route('login') }}">Войти</a>
                            <a class="list" href="{{ route('register') }}">Зарегистрироваться</a>
                        @else
                            <span class="list">{{ Auth::user()->name }}</span>
                            <a class="list" href="{{route('home')}}">Мои нужды</a>
                            <a class="list" href="{{route('list')}}">Молитвы</a>
                            <a class="list" href="{{route('personal')}}">Личный кабинет</a>
                            <a class="list" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                Выход
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <div class="main-section bg-yellow-lightest flex-1">
            <div class="container mx-auto">
           @yield('content')
            </div>
        </div>
        <footer class="flex-none">
            <div class="container footer mx-auto">
                <!--<div class="contacts">
                    <p>г. Тольятти, улица Липовая 16</p>
                    <p>тел. 8 (8482) 20-85-98</p>
                    <p>Email : vitalela@gmail.com</p>
                </div>-->
            </div>
        </footer>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Custom scripts -->
    @yield('script')
     <!-- JQuery UI scripts -->
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <!-- Jquery UI CSS -->
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
    <title>Admin panel</title>
    
</head>
<body class="h-full">
    <div class="body-page flex flex-col	h-full">
        <header class="bg-grey flex-1 header-piсture">
            <div class="container mx-auto">
                <div class="header-first-line">
                    <div class="header-logo">
                        <img class="" src="img/cross.png" alt="logo-cross"/>
                        <div class="church-name">
                            <span class="name">пробуждение</span>
                            <span class="description">церковь евангельских христиан</span>
                        </div>
                        
                    </div>
                    <label for="menu-checkbox">
                        <img class="mobile-menu" src="img/icons8-menu-filled.svg" alt="menu">
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
           <?/*CONTENT*/?>
           <input type="hidden" id="x_token" value="{{ csrf_token() }}">
           <pre><?//print_r($code);?></pre>
           @foreach($tables as $name=>$number)
           <div class="point" onclick="show_table('{{$name}}')">{{$name}} ({{$number}})</div>
           @endforeach
           <div id="main_table"></div>
           <?/*CONTENT*/?>
            </div>
        </div>
        <footer class="flex-none">
            <div class="container footer mx-auto">
            </div>
        </footer>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Custom scripts -->
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
     <!-- JQuery UI scripts -->
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
</body>
</html>

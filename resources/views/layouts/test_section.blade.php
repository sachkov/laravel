<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/prayers.css') }}" rel="stylesheet">
</head>
<body>
    <div id="mn-sections">
        <nav class="navbar navbar-default navbar-static-top">
                <ul role="menu">
                    <li class="d-inline px-3">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <li class="d-inline px-3">
                        <a href="{{ route('home') }}">
                            Home
                        </a>
                    </li>
                </ul>
            </li>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Custom scripts -->
    <script src="{{ asset('js/test.js') }}"></script>
</body>
</html>

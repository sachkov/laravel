@extends('layouts.mn_section')

<?/*@section('title', 'Личный кабинет')*/?>

@section('page_menu')
    <a class="list" href="{{route('prayersEnd')}}">Завершенные молитвы</a>
    @if(Route::currentRouteName() != "generateCode")
        <a class="list" href="{{route('generateCode')}}">Пригласить друга</a>
    @endif
    @if(Route::currentRouteName() != "personal")
        <a class="list" href="{{route('personal')}}">Личный кабинет</a>
    @endif
    @if(Route::currentRouteName() != "about")
        <a class="list" href="{{route('about')}}">О сайте</a>
    @endif
@endsection

@section('content')

<div id="personal">
    <div class="personal">
        <?/*
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{route('prayersEnd')}}">Завершенные молитвы</a>
            </li>
            @if(Route::currentRouteName() != "generateCode")
            <li class="list-group-item">
                <a href="{{route('generateCode')}}">Пригласить друга</a>
            </li>
            @endif
            @if(Route::currentRouteName() != "personal")
            <li class="list-group-item">
                <a href="{{route('personal')}}">Личный кабинет</a>
            </li>
            @endif
            @if(Route::currentRouteName() != "about")
            <li class="list-group-item">
                <a href="{{route('about')}}">О сайте</a>
            </li>
            @endif
        </ul>
        */?>
        <div class="p_content">
            @yield('page_content')
        </div>
    </div>
</div>

<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<script>
    let auth = true;
    <?if(Auth::guest()){?>
       auth = false; 
    <?}?>
</script>
@endsection

@section('script')
    <script src="{{ asset('js/personal.js') }}"></script>
    @yield('page_js')
@endsection

@section('css')
    <link href="{{ asset('css/personal.css') }}" rel="stylesheet">
    @yield('page_css')
@endsection

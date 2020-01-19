@extends('layouts.mn_section')

@section('title', 'Личный кабинет')

@section('content')
<div id="personal">
    <div class="personal">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{route('prayersEnd')}}">Завершенные молитвы</a>
            </li>
            <li class="list-group-item">
                <a href="{{route('generateCode')}}">Пригласить друга</a>
            </li>
        </ul>
        <div class="p_content">
            <h3 class="info">
                Личный кабинет
            </h3>
            <div class="name">{{$user->name}}</div>
            <div class="email">{{$user->email}}</div>
        </div>
    </div>
</div>
<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<pre><?//print_r($arMN);?></pre>
<script>
    let auth = true;
    <?if(Auth::guest()){?>
       auth = false; 
    <?}?>
</script>
@endsection

@section('script')
    <script src="{{ asset('js/personal.js') }}"></script>
@endsection

@section('css')
    <link href="{{ asset('css/personal.css') }}" rel="stylesheet">
@endsection

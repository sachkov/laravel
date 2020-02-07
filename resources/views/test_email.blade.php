@extends('layouts.mn_section')

@section('title', 'Проверка')

@section('content')
<div id="appV">

    <pre><?print_r($user);?></pre>
    <b>{{$user->name}}</b>
</div>

@endsection

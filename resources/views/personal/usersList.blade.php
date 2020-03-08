@extends('layouts.personal_layout')

@section('title', 'Список пользователей')

@section('page_content')
    <h3 class="info">
    Список пользователей
    </h3>
    <table>
        <tr><th>Дата регистрации</th><th>Имя</th></tr>
        @foreach($users as $user)
        <tr>
            <td>{{$user->created_at->format("d.m.Y")}}</td>
            <td>{{$user->name}}</td>
        </tr>
        @endforeach
    </table>
    @if($nextLink)
        <a href="{{$nextLink}}" class="btn btn-outline-warning">Еще</a>
    @endif

    <pre><?//print_r($count);?></pre>
@endsection


@section('page_js')

@endsection

@section('page_css')

@endsection
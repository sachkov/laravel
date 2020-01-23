@extends('layouts.personal_layout')

@section('title', 'Личный кабинет')

@section('page_content')
    <h3 class="info">
        Личный кабинет
    </h3>
    <div class="name">{{$user->name}}</div>
    <div class="email">{{$user->email}}</div>
    <div class="users-groups">
        <h4 class="my_groups">Мои группы</h4>
        @if(count($groups))
        <table class="table groups">
            <tr>
                <th>Название</th>
                <th>Покинуть группу</th>
            </tr>
            @foreach($groups as $group)
            <tr>
                <td>{{@group->name($group->number)}}</td>
                <td onclick="leave({{$group->id}})">
                    <img src="/img/cross.png" alt="покинуть группу">
                </td>
            </tr>
            @endforeach
        </table>
        @endif
        <span>Добавить группу:</span>
        <select class="select-groups"></select>
    </div>
@endsection

@extends('layouts.personal_layout')

@section('title', 'Личный кабинет')

@section('page_content')
    <h3 class="info">
        Личный кабинет
    </h3>
    <div class="name">{{$user->name}}</div>
    <div class="email">{{$user->email}}</div>
    <div class="users-groups">
        @if(count($groups))
        <h4 class="my_groups">Мои группы</h4>
        <table class="table groups">
            @foreach($groups as $group)
            <tr>
                <td>{{$group['name']}}({{$group['number']}})</td>
                <td onclick="leave({{$group['id']}})">
                    <button class="btn btn-primary" id="come-in-group">
                        <span class="d-screen">Покинуть группу</span>
                        <span class="d-mobile">-</span>
                    </button>
                </td>
            </tr>
            @endforeach
        </table>
        @endif

        <h4 class="my_groups">Войти в существующую группу</h4>
        <select class="groups-select"></select>
        <button class="btn btn-primary" id="come-in-group">
            <span class="d-screen">Войти</span>
            <span class="d-mobile">></span>
        </button>

        <h4 class="my_groups">Создать новую группу</h4>
        <div class="form-group">
            <input type="text" class="form-control" id="group-name" 
                placeholder="Название группы">
            <div class="invalid-feedback">Поле Наименование должно быть заполено!</div>
        </div>
        <button class="btn btn-primary" id="create-group">
            <span class="d-screen">Создать</span>
            <span class="d-mobile">+</span>
        </button>
    </div>
    <pre><?print_r($groups)?></pre>
@endsection

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
            <tr>
                <th>Название</th>
                <th>Покинуть группу</th>
            </tr>
            @foreach($groups as $group)
            <tr>
                <td>{{@group['name']}}({{$group['number']}})</td>
                <td onclick="leave({{$group['id']}})">
                    <img src="/img/cross.png" alt="покинуть группу">
                </td>
            </tr>
            @endforeach
        </table>
        @endif
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
@endsection

@extends('layouts.personal_layout')

@section('title', 'Личный кабинет')

@section('page_content')
    <h3 class="info">
        Личный кабинет
    </h3>
    <div class="name">{{$user->name}}</div>
    <div class="email">{{$user->email}}</div>
    <div class="users-groups">

        <div class="table_groups">
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
        </div>

        <h4 class="my_groups">Поиск существующей группы</h4>
        <div class="form-group">
            <input type="text" class="form-control" id="select-group" 
                placeholder="Начинайте вводить название группы">
        </div>
        <button class="btn btn-secondary" id="come-in-group">
            <span class="d-screen">Присоединится</span>
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
    <pre><?//print_r($groups)?></pre>
@endsection


@section('page_js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
@endsection

@section('page_css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
@endsection
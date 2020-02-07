@extends('layouts.personal_layout')

@section('title', 'Личный кабинет')

@section('page_content')
    <h3 class="info">
        Личный кабинет
    </h3>
    <div class="name">{{$user->name}}</div>
    <div class="email">{{$user->email}}</div>
    <div class="users-groups" id="v-personal-groups">

        <div class="table_groups">
            <div v-show="group_table.length">
                <h4 class="my_groups">Мои группы</h4>
                <table class="table groups">

                    <tr v-for="(group, indx) in group_table">
                        <td v-if="group.is_author" class="group_name">
                            <input class="group_edit" 
                                v-model="group.name"
                                @focus="saveName(indx)"
                                @blur="changeName(indx)">
                        </td>
                        <td v-else  class="group_name">
                            @{{group.name}}(@{{group.number}})
                        </td>
                        <td>
                            <button @click="leave(indx)"
                            class="personal-btn leave">
                                <span class="d-screen">Покинуть группу</span>&nbsp;
                            </button>
                            <button @click="del_group(indx)"
                            v-if="group.is_author"
                            class="personal-btn delete">
                            <span class="d-screen">Удалить группу</span>&nbsp;
                            </button>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <h4 class="my_groups">Поиск группы</h4>
        <div class="form-group">
            <input type="text" class="form-control" id="select-group" 
                placeholder="Начинайте вводить название группы">
        </div>
        <button class="personal-btn disable" id="come-in-group">
            <span class="d-screen">Присоединится</span>
            <span class="d-mobile">&nbsp;</span>
        </button>

        <h4 class="my_groups">Создать новую группу</h4>
        <div class="form-group">
            <input type="text" class="form-control" id="group-name" 
                placeholder="Название группы">
            <div class="invalid-feedback">Поле Наименование должно быть заполено!</div>
        </div>
        <button class="personal-btn" id="create-group">
            <span class="d-screen">Создать</span>
            <span class="d-mobile">&nbsp;</span>
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
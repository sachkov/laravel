@extends('layouts.mn_section')

@section('content')
<div id="appV">
    <form id="create-form">
        <input type="hidden" id="x_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <label for="input-name">Наименование</label>
            <input type="text" class="form-control" id="input-name" placeholder="Заголовок 'о чем' или 'о ком'">
            <div class="invalid-feedback">Поле Наименование должно быть заполено!</div>
        </div>

        <?/*<div class="form-group">
            <label for="sel-groups">Видно группам</label>
            <select multiple class="form-control" id="sel-groups">
                <option>Вся церковь</option>
                <option>Малая группа</option>
                <option>Тестовая группа</option>
                <option>Братья</option>
            </select>
        </div>*/?>
        <div class="form-group">
            <label for="input-user">Видно людям</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in table">
                        @{{item.label}}
                    <span class="x-del" @click="del(i)">X</span>
                </span>
            </div>
            <input type="text" class="form-control" id="input-user" placeholder="Начните вводить имя или фамилию">
        </div>
        <div class="form-group">
            <label for="textarea-descr">Описание</label>
            <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
        </div>
        <?/*<div class="form-group">
            <label for="input-name">Действует до</label>
            <input type="text" class="form-control" id="input-date" placeholder="Дата окончания, если исвестно">
        </div>*/?>
        <div id="btn-save-mn" class="btn btn-success">Сохранить</div>
        <div id="btn-cancel-mn" class="btn btn-light">Отмена</div>
    </form>
    <div class="d-flex justify-content-end my-3">
        <button type="button" id="btn-add-mn" class="btn btn-success">Добавить</button>
    </div>
    <table class="table table-hover prayers-main-table">
        <thead>
            <tr>
                <th scope="col">Дата</th>
                <th scope="col">Автор</th>
                <th scope="col">Название</th>
                <th scope="col">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arMN as $MN)
            <tr class="main" data-mnid="{{$MN->id}}">
                <th title="Дата" scope="row">{{ $MN->created_at->format('d.m.Y') }}</th>
                <td title="Автор">{{ $MN->author->name }}</td>
                <td title="Название" class="main-name">{{ $MN->name }}</td>
                <td title="Действия">
                    <button type="button" class="btn btn-outline-primary btn-sm mn-show">Показать описание</button>
                    <button type="button" class="btn btn-outline-info btn-sm mn-act" data-act="edit">Редактировать</button>
                    <button type="button" class="btn btn-outline-success btn-sm mn-act" data-act="done">Завершить</button>
                </td>
            </tr>
            <tr id="desc-{{ $MN->id }}" class="mn-description">
                <td colspan="4">
                    {{ $MN->description }}
                <?// В описание надо включить описание видимости данной молитвы?>
                </td>
            </tr>
            <tr id="container-{{ $MN->id }}" class="tr-container"><td colspan="4" id="container-td-{{ $MN->id }}"></td></tr>
            @endforeach
        </tbody>
        
    </table>

    <div class="edit-form">
        <div class="form-group">
            <label for="name-edit">Наименование</label>
            <input type="text" class="form-control" id="name-edit" >
            <div class="invalid-feedback">Поле Наименование должно быть заполено!</div>
        </div>
        <div class="form-group">
            <label for="descr-edit">Описание</label>
            <textarea class="form-control" id="descr-edit" rows="3" ></textarea>
        </div>
        <div class="form-group" id="result-egit-form">
            <label for="result-edit">Результат</label>
            <textarea class="form-control" id="result-edit" rows="3"></textarea>
        </div>
        <?/*<div class="form-group">
            <label for="sel-groups">Группы</label>
            <select multiple class="form-control" id="sel-groups">
                <option>Вся церковь</option>
                <option>Малая группа</option>
                <option>Тестовая группа</option>
                <option>Братья</option>
            </select>
        </div>*/?>
        <div class="form-group">
            <label for="share-edit">Видно людям</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in edit_users_table">
                        @{{item.name}}
                    <span class="x-del" @click="edit_del(i)">X</span>
                </span>
            </div>
            <input type="text" class="form-control" id="share-edit" placeholder="Начните вводить имя или фамилию">
        </div>
        <div class="btn btn-primary" id="btn-save-edit">Сохранить</div>
        <div id="btn-cancel-edit" class="btn btn-light">Отмена</div>
    </div>
    <div class="done-form">
        <div class="form-group">
            <label for="name-done">Наименование</label>
            <input type="text" class="form-control" id="name-done" placeholder="Заголовок 'о чем' или 'о ком'" readonly>
        </div>

        <div class="form-group">
            <label for="descr-done">Описание</label>
            <textarea class="form-control" rows="3" id="descr-done" readonly="false"></textarea>
        </div>
        <div class="form-group">
            <label for="result-done">Результат</label>
            <textarea class="form-control" id="result-done" rows="3"></textarea>
            <div class="invalid-feedback">Поле Результат должно быть заполено!</div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="thankfulness">
            <label class="form-check-label" for="thankfulness">Опубликовать как благодарность</label>
        </div>
        <div class="btn btn-primary" onclick="saveDoneForm()">Сохранить</div>
        <div class="btn btn-light" onclick="closeDoneForm()">Отмена</div>
    </div>

    <pre><?//print_r($arMN);

    ?></pre>
</div>
<test></test>
@endsection

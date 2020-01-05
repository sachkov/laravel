@extends('layouts.mn_section')

@section('title', 'Мои нужды')

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
            <input type="text" class="form-control" id="input-date" placeholder="Дата окончания, если известно">
        </div>*/?>
        <div id="btn-save-mn" class="btn btn-success">Сохранить</div>
        <div id="btn-cancel-mn" class="btn btn-light">Отмена</div>
    </form>
    <div class="home-btns-add">
        <a class="btn btn-info" href="{{route('prayersEnd')}}">Завершенные молитвы</a>
        <button type="button" id="btn-add-mn" class="btn btn-success">Добавить</button>
    </div>
    
    
    <div class="prayers-main-table">
        <div class="t-tr thead">
            <div class="t-td t-date">Дата</div>
            <div class="t-td t-name">Название</div>
            <div class="t-td t-action">Действия</div>
        </div>
        <div class="tbody">
            @foreach($arMN as $MN)
            <div class="mn-item">
                <div class="t-tr" data-mnid="{{$MN->id}}">
                    <div class="t-td t-date" title="Дата" >{{ $MN->created_at->format('d.m.Y') }}</div>
                    <div class="t-td t-name" title="Название">{{ $MN->name }}</div>
                    <div class="t-td t-action" title="Действия">
                        <button type="button" class="btn btn-outline-primary btn-sm mn-show">
                            <span>Показать описание</span>
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm mn-act" data-act="edit">
                            <span>Редактировать</span>
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm mn-act" data-act="done">
                            <span>Завершить</span>
                        </button>
                    </div>
                    <div id="desc-{{ $MN->id }}" class="mn-description">
                        <p>{{ $MN->description }}</p>
                        @if($MN->answer)
                            <p>{{$MN->answer}}</p>
                        @endif
                    <?// В описание можно включить описание видимости данной молитвы?>
                    </div>
                </div>
                <div id="container-{{ $MN->id }}" class="tr-container"></div>
            </div>
            @endforeach
        </div>
        
    </div>


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
        <div class="form-group" id="result-edit-form">
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
@endsection

@section('script')
    <script src="{{ asset('js/prayers.js') }}"></script>
@endsection

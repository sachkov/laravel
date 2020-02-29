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

        <div class="form-group">
            <label for="textarea-descr">Описание</label>
            <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
        </div>

        <div class="form-group" v-show="is_groups_table">
            <label for="input-name">Молитва</label>
            <div class="check_flex">
                <label for="type-left" class="check radio-left active">
                    <input type="radio" id="type-left" name="radio" 
                        v-model="mn_type" value=0 hidden>
                    <span class="title">Личная</span>
                    <span class="descr">
                        Молитва, которую Вы добавляете от своего имени, 
                        ею можно поделится с друзьями и группами.
                    </span>
                </label>
                <label for="type-right" class="check radio-right">
                    <input type="radio" id="type-right" name="radio"
                        v-model="mn_type" value=1 hidden>
                    <span class="descr">
                        Молитва, которую Вы добавляете как администратор группы, 
                        ею можно поделится только с группами.
                    </span>
                    <span class="title">Общая</span>
                </label>
            </div>
        </div>

        <div class="form-group" v-show="!mn_type">
            <label for="input-user">Видно людям</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in add_users_table">
                        @{{item.label}}
                    <span class="x-del" @click="del(i)">X</span>
                </span>
            </div>
            <input type="text" class="form-control" id="input-user" placeholder="Начните вводить имя или фамилию">
        </div>

        <div class="form-group" v-show="is_groups_table">
            <label for="input-user">Видно группам</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in add_groups_table">
                        @{{item.label}}
                    <span class="x-del" @click="del_gr(i)">X</span>
                </span>
            </div>
            <?/*<input type="text" class="form-control" id="input-group" placeholder="Начните вводить название группы">*/?>
            <select class="form-control" id="select-group" @change="add_gr($event)">
                <option default>Выберите группу</option>
                <option v-for="(x, index) in user_groups"
                    :value="index">
                    @{{x.label}}
                </option>
            </select>
        </div>

        <?/*    //l-7
        <div class="form-group" v-show="mn_type">
            <label for="input-user">Видно группам</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in add_adm_groups_table">
                        @{{item.name}}
                    <span class="x-del" @click="del_adm_gr(item)">X</span>
                </span>
            </div>
            <select class="form-control" id="input-adm-group" multiple>
                <option v-for="i in admin_groups"
                    @click="add_adm_gr(i)">
                    @{{i.name}}
                </option>
            </select>
        </div>
        */?>

        <div class="form-group" v-show="mn_type && is_groups_table">
            <label for="input-user">Показывать по графику</label>
            <div class="select_flex">
                <select class="form-control" id="week-day" v-model="week_day">
                    <option v-for="(day, index) in week"
                        :value="index">
                        @{{day}}
                    </option>
                </select>
                <select class="form-control" id="month-day" v-model="month_day">
                    <option v-for="(day, index) in month"
                        :value="index">
                        @{{day}}
                    </option>
                </select>
            </div>
        </div>
        
        <div id="btn-save-mn" class="btn btn-success">Сохранить</div>
        <div id="btn-cancel-mn" class="btn btn-light">Отмена</div>
    </form>
    <div class="home-btns-add">
        <a class="btn btn-info" href="{{route('list')}}">Молитвы</a>
        <button type="button" id="btn-add-mn" class="btn btn-success">Добавить</button>
    </div>
    
    
    <div id="main-table" class="prayers-main-table">
        <div class="tbody">
            
            <div class="list-item" v-for="(mn, indx) in mainTable" v-bind:key="indx">
                <div class="first-line" :data-mnid="mn.id">
                    <div class="mn-date" title="Дата">@{{mn.created_at}}</div>
                    <div class="mn-name" title="Название"
                    @click="toggle_description(indx)">
                        @{{mn.name}}
                    </div>
                    <div class="mn-btn" title="Меню"
                        @click="drop(indx, $event)"
                        >...
                    </div>
                </div>
                <div class="mn-description" v-show="mn.description_show">
                    <p>@{{mn.description}}</p>
                    <p>@{{mn.answer}}</p>
                    <?// В описание можно включить описание видимости данной молитвы?>
                </div>
                <div v-bind:id="'container-'+indx" class="tr-container"></div>
            </div>

        </div>
        <div class="table_empty" v-if="!mainTable.length">
            На этой странице располагается Ваш список молитвенных нужд, он виден только Вам.
        </div>
        <div>
            <div id="more_btn" class="btn btn-outline-warning" onclick="getMorePrayers()">
                Еще
            </div>
        </div>
    </div>


    <div class="edit-form">
        <h5 class="mobile-header-form">Форма редактирования</h5>
        <div class="form-group">
            <label for="name-edit">Наименование</label>
            <input type="text" class="form-control" id="name-edit" 
                v-model.trim="edit.name" :disabled="(edit.is_thanks!=0)">
            <div class="invalid-feedback">
                Поле Наименование должно быть заполено!
            </div>
        </div>
        <div class="form-group">
            <label for="descr-edit">Описание</label>
            <textarea class="form-control" id="descr-edit" 
                rows="3" v-model.trim="edit.description" 
                :disabled="(edit.is_thanks!=0)">
            </textarea>
        </div>
        <div class="form-group" id="result-edit-form" v-show="edit.is_thanks">
            <label for="result-edit">Результат</label>
            <textarea class="form-control" id="result-edit" rows="3" v-model.trim="edit.answer"></textarea>
        </div>
        <div class="form-group" v-show="!edit.is_thanks">
            <label for="share-edit">Видно людям</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in edit_users_table">
                        @{{item.name}}
                    <span class="x-del" @click="edit_del(i)">X</span>
                </span>
            </div>
            <input type="text" class="form-control" 
                id="share-edit" placeholder="Начните вводить имя или фамилию">
        </div>
        <div class="form-group" v-show="!edit.is_thanks && is_groups_table">
            <label for="share-edit">Видно группам</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in edit_groups_table">
                        @{{item.name}}
                    <span class="x-del" @click="edit_del_gr(i)">X</span>
                </span>
            </div>
            <?/*
            <input type="text" class="form-control" 
                id="groups-edit" placeholder="Начните вводить название группы">
            */?>
            <select class="form-control" id="edit-group" @change="edit_gr($event)">
                <option default>Выберите группу</option>
                <option v-for="(x, index) in user_groups"
                    :value="index">
                    @{{x.label}}
                </option>
            </select>
        </div>

        <div class="form-group" v-show="!edit.is_thanks && is_groups_table">
            <label for="input-user">Показывать по графику</label>
            <div class="select_flex">
                <select class="form-control" id="week-day" v-model="week_day">
                    <option v-for="(day, index) in week"
                        :value="index">
                        @{{day}}
                    </option>
                </select>
                <select class="form-control" id="month-day" v-model="month_day">
                    <option v-for="(day, index) in month"
                        :value="index">
                        @{{day}}
                    </option>
                </select>
            </div>
        </div>

        <div class="btn btn-primary" id="btn-save-edit">Сохранить</div>
        <div id="btn-cancel-edit" class="btn btn-light">Отмена</div>
    </div>
    
    <div class="done-form">
        <h4 class="mobile-header-form">Форма завершения</h4>
        <div class="form-group">
            <label for="name-edit">Наименование</label>
            <input type="text" class="form-control" id="name-done" 
            v-model.trim="done.name" disabled>
        </div>
        <div class="form-group">
            <label for="descr-edit">Описание</label>
            <textarea class="form-control" id="descr-done" rows="3" 
                v-model.trim="done.description" disabled>
            </textarea>
        </div>
        <div class="form-group">
            <label for="result-done">Результат</label>
            <textarea class="form-control" id="result-done" rows="3" v-model.trim="done.answer"></textarea>
            <div class="invalid-feedback">Поле Результат должно быть заполено!</div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="thankfulness" v-model="done.is_thanks">
            <label class="form-check-label" for="thankfulness">Опубликовать как благодарность</label>
        </div>
        <div class="btn btn-primary" onclick="saveDoneForm()">Сохранить</div>
        <div class="btn btn-light" onclick="closeDoneForm()">Отмена</div>
    </div>

    <nav class="drop-down-menu">
        <ul class="ddm-ul">
            <li class="ddm-li" @click="show_edit_form()">редактировать</li>
            <li class="ddm-li" @click="show_done_form()">завершить</li>
            <li class="ddm-li" @click="del_mn()">удалить</li>
        </ul>
    </nav>

    <pre><?//echo config('app.env');?></pre>

</div>
<script>
    let auth = true;
    <?if(Auth::guest()){?>
       auth = false; 
    <?}?>
</script>
@endsection

@section('page_menu')
    <span id="view_mode" class="list link" data-mode="personal">
        Показать все
    </span>
@endsection

@section('script')
    <script src="{{ asset('js/prayers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
@endsection

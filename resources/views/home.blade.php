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
            <input type="text" class="form-control" id="input-group" placeholder="Начните вводить название группы">
        </div>
        
        <div class="form-group">
            <label for="textarea-descr">Описание</label>
            <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
        </div>
        <div id="btn-save-mn" class="btn btn-success">Сохранить</div>
        <div id="btn-cancel-mn" class="btn btn-light">Отмена</div>
    </form>
    <div class="home-btns-add">
        <a class="btn btn-info" href="{{route('list')}}">Молитвы</a>
        <button type="button" id="btn-add-mn" class="btn btn-success">Добавить</button>
    </div>
    
    
    <div class="prayers-main-table">
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
    </div>


    <div class="edit-form">
        <h5 class="mobile-header-form">Форма редактирования</h5>
        <div class="form-group" v-show="!edit.is_thanks">
            <label for="name-edit">Наименование</label>
            <input type="text" class="form-control" id="name-edit" v-model.trim="edit.name">
            <div class="invalid-feedback">Поле Наименование должно быть заполено!</div>
        </div>
        <div class="form-group" v-show="!edit.is_thanks">
            <label for="descr-edit">Описание</label>
            <textarea class="form-control" id="descr-edit" rows="3" v-model.trim="edit.description"></textarea>
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
            <input type="text" class="form-control" id="share-edit" placeholder="Начните вводить имя или фамилию">
        </div>
        <div class="form-group" v-show="!edit.is_thanks && is_groups_table">
            <label for="share-edit">Видно группам</label>
            <div class="users-list">
                <span class="users-list-text" v-for="(item,i) in edit_groups_table">
                        @{{item.name}}
                    <span class="x-del" @click="edit_del_gr(i)">X</span>
                </span>
            </div>
            <input type="text" class="form-control" id="groups-edit" placeholder="Начните вводить название группы">
        </div>
        <div class="btn btn-primary" id="btn-save-edit">Сохранить</div>
        <div id="btn-cancel-edit" class="btn btn-light">Отмена</div>
    </div>
    
    <div class="done-form">
        <h4 class="mobile-header-form">Форма завершения</h4>
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
    
    <div>
        <div id="more_btn" class="btn btn-outline-warning" onclick="getMorePrayers()">Еще</div>
    </div>

    <nav class="drop-down-menu">
        <ul class="ddm-ul">
            <li class="ddm-li" @click="show_edit_form()">редактировать</li>
            <li class="ddm-li" @click="show_done_form()">завершить</li>
            <li class="ddm-li" @click="del_mn()">удалить</li>
        </ul>
    </nav>

    <pre><?//print_r($ar);?></pre>
</div>
<script>
    let auth = true;
    <?if(Auth::guest()){?>
       auth = false; 
    <?}?>
</script>
@endsection

@section('script')
    <script src="{{ asset('js/prayers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
@endsection

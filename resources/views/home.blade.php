@extends('layouts.mn_section')

@section('content')
<form>
    <div class="form-group">
        <label for="input-name">Наименование</label>
        <input type="text" class="form-control" id="input-name" placeholder="Заголовок 'о чем' или 'о ком'">
    </div>

    <div class="form-group">
        <label for="sel-groups">Группы</label>
        <select multiple class="form-control" id="sel-groups">
            <option>Вся церковь</option>
            <option>Малая группа</option>
            <option>Тестовая группа</option>
            <option>Братья</option>
        </select>
    </div>
    <div class="form-group">
        <label for="textarea-descr">Описание</label>
        <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="input-name">Действует до</label>
        <input type="text" class="form-control" id="input-name" placeholder="Дата окончания, если исвестно">
    </div>
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
        <tr class="main">
            <th title="Дата" scope="row">1</th>
            <td title="Автор">Mark</td>
            <td title="Название" class="js-click-Pname">Otto</td>
            <td title="Действия">
                <button type="button" id="btn-add-mn" class="btn btn-outline-primary btn-sm">Описание</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-info btn-sm">Редактировать</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-success btn-sm">Удалить</button>
            </td>
        </tr>
        <tr class="description">
            <td colspan="4">Описание</td>
        </tr>
        <tr>
            <th title="Дата" scope="row">2</th>
            <td title="Автор">Mark</td>
            <td title="Название" class="js-click-Pname">Otto</td>
            <td title="Действия">
                <button type="button" id="btn-add-mn" class="btn btn-outline-primary btn-sm">Описание</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-info btn-sm">Редактировать</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-success btn-sm">Удалить</button>
            </td>
        </tr>
        <tr>
            <th title="Дата" scope="row">3</th>
            <td title="Автор">Mark</td>
            <td title="Название" class="js-click-Pname">Otto</td>
            <td title="Действия">
                <button type="button" id="btn-add-mn" class="btn btn-outline-primary btn-sm">Описание</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-info btn-sm">Редактировать</button>
                <button type="button" id="btn-add-mn" class="btn btn-outline-success btn-sm">Удалить</button>
            </td>
        </tr>
    </tbody>
</table>

<form class="">
    <div class="form-group">
        <label for="input-name">Наименование</label>
        <input type="text" class="form-control" id="input-name" placeholder="Заголовок 'о чем' или 'о ком'">
    </div>

    <div class="form-group">
        <label for="sel-groups">Группы</label>
        <select multiple class="form-control" id="sel-groups">
            <option>Вся церковь</option>
            <option>Малая группа</option>
            <option>Тестовая группа</option>
            <option>Братья</option>
        </select>
    </div>
    <div class="form-group">
        <label for="textarea-descr">Описание</label>
        <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="textarea-descr">Результат</label>
        <textarea class="form-control" id="textarea-descr" rows="3"></textarea>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="thankfulness" value="1">
        <label class="form-check-label" for="thankfulness">Опубликовать как благодарность</label>
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
@endsection

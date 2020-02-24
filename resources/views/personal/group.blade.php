@extends('layouts.personal_layout')

@section('title', $group->name)

@section('page_content')
    <h3 class="info group_name">
        {{$group->name}}
    </h3>
    <div class="g_date">Создана: {{$group->created_at->format('d.m.Y')}}</div>
    <div class="g_author">Автор: {{$group->author->name}}</div>

    @if($is_admin)
    <input id="group_name" class="form-control" 
        type="text" placeholder="Новое наименование"/>
    <div class="btn btn-primary" onclick="changeName({{$group->id}})">Переименовать</div>
    @endif


    <h4 class="info">
        Участники ({{count($group->signed_users)}} чел)
    </h4>
    <table class="gadmins_tbl table">
        @foreach($group->signed_users as $user)
        <tr>
            <td>{{$user->name}}</td>
            @if($is_admin && $user->id == Auth::user()->id)
                <td>
                    <span class="btn btn-danger" onclick="leave({{$group->id}},{{$user->id}})">
                        Покинуть группу
                    </span>
                    <span class="btn btn-warning" onclick="del_admin({{$group->id}},{{$user->id}})">
                        Перестать администрировать
                    </span>
                </td>
            @elseif($is_admin)
                <td>
                    @if($user->pivot->admin)
                        <span>Администратор</span>
                    @else
                        <span class="btn btn-danger" onclick="leave({{$group->id}},{{$user->id}})">
                            Исключить
                        </span>
                        <span class="btn btn-success" 
                            onclick="admin({{$group->id}},{{$user->id}})">
                            Сделать администратором
                        </span>
                    @endif
                </td>
            @elseif($user->id == Auth::user()->id)
                <td>
                    <span class="btn btn-danger" onclick="leave({{$group->id}},{{$user->id}})">
                        Покинуть группу
                    </span>
                </td>
            @elseif($user->pivot->admin)
                <td>Администратор</td>
            @else
                <td></td>
            @endif 
        </tr>
        @endforeach
    </table>

    <pre><?//print_r($group->signed_users)?></pre>
@endsection


@section('page_js')

@endsection

@section('page_css')

@endsection
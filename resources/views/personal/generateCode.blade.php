@extends('layouts.personal_layout')

@section('title', 'Пригласить друга')

@section('page_content')
        
    <h3 class="info">
        Пригласить друга
    </h3>
    @if(!$valid_code)
        <button id="generate" class="btn btn-outline-success">
            Генерировать код приглашения
        </button>
        <p id="new_invite">
            Код Вашего приглашения 
            <span id="invite_code"></span>
            он действует в течении 5 дней. Сообщите его своему другу для указания
            в форме регистрации.
        </p>
    @endif


    @if(count($invites))
        <table class="table">
            <tr>
                <th>№</th>
                <th>Дата</th>
                <th>Статус</th>
            </tr>
            @foreach ($invites as $invite)
            <tr>
                <td>{{($loop->remaining+1)}}</td>
                <td>{{$invite->created_at}}</td>
                <td>{{$invite->status}}</td>
            </tr>
            @endforeach
        </table>
    @endif
@endsection
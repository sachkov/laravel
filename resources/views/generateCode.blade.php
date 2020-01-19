@extends('layouts.mn_section')

@section('title', 'Пригласить друга')

@section('content')
<div id="personal">
    <div class="personal">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{route('prayersEnd')}}">Завершенные молитвы</a>
            </li>
            <li class="list-group-item">
                <a href="{{route('personal')}}">Личный кабинет</a>
            </li>
        </ul>
        
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

        
    </div>
    <div class="p_content">
        @if($invites)
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
    </div>
</div>
<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<pre><?//print_r($invites);?></pre>
<script>
    let auth = true;
    <?if(Auth::guest()){?>
       auth = false; 
    <?}?>
</script>
@endsection

@section('script')
    <script src="{{ asset('js/personal.js') }}"></script>
@endsection

@section('css')
    <link href="{{ asset('css/personal.css') }}" rel="stylesheet">
@endsection

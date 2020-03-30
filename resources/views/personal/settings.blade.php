@extends('layouts.personal_layout')

@section('title', 'Настройки')

@section('page_content')
    <h3 class="info">
        Личный кабинет - Настройки
    </h3>
    <div class="name">{{Auth::user()->name}}</div>
    <div class="email">{{Auth::user()->email}}</div>
    <br>
    <div class="settings_block">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notification" disabled>
            <label class="form-check-label" for="notification">
                Включить уведомления с сайта
            </label>
        </div>
    </div>
    <pre>
        <?//print_r($groups)?>
    
    </pre>
    <?$d = str_replace("\n", "", file_get_contents("./../googlefirebase.json"));?>
    <script>
        let GFB = JSON.parse('<?=$d?>');
    </script>
@endsection


@section('page_js')
    <script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-messaging.js"></script>
    <script src="{{ asset('js/service/settings.js') }}"></script>
@endsection

@section('page_css')

@endsection
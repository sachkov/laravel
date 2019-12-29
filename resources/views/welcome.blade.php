<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Пробуждение</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <link href="{{ asset('css/prayers.css') }}" rel="stylesheet">
    </head>
    <body class="welcome-body">
        <div class="welcome-scr bg-mountains">
            
            <div class="quotes-cont">
                <p class="quote">В начале было Слово, и Слово было у Бога, и Слово было Бог.</p>
                <p class="quote">Итак во всем, как хотите, чтобы с вами поступали люди, так поступайте и вы с ними</p>
                <p class="quote">Ибо весь закон в одном слове заключается: люби ближнего твоего, как самого себя.</p>
                <p class="quote">Воззови ко Мне - и Я отвечу тебе, покажу тебе великое и недоступное, чего ты не знаешь.</p>
            </div>
            @if (Route::has('login'))
                <div class="top-right-links">
                    @if (Auth::check())
                        <a class="link" href="{{ url('/home') }}">Мои молитвы</a>
                    @else
                        <a class="link" href="{{ url('/login') }}">Войти</a>
                        <a class="link" href="{{ url('/register') }}">Зарегистрироваться</a>
                    @endif
                </div>
            @endif

        </div>
        <script>
            let quotesCont = document.querySelector(".quotes-cont");
            let interval = 1000;
            let timerId = setTimeout(function tick() {
                let quote = document.querySelector(".quote:first-child");
                quotesCont.appendChild(quote);
                interval += 1000;
                timerId = setTimeout(tick, interval);
            }, interval);
        </script>
    </body>
</html>

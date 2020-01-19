@extends('layouts.mn_section')

@section('title', 'Завершенные молитвы')

@section('content')
<h3>Завершенные молитвы</h3>
<div class="prayers-list">
    @foreach ($arMN as $MN)
    <div class="prayes">
        <div class="timeText">
            <div class="time-column">
                <p class="time from">{{ $MN->created_at->format('d.m.Y') }}</p>
                <p class="time at">{{ date('d.m.Y',strtotime($MN->end_date)) }}</p>
            </div>
            <div class="text-column">
                <p class="header"><span>{{$MN->name}}</span></p>
            </div>
        </div>
        <p class="description">{{$MN->description}}</p>
        @if ($MN->answer)
            <p class="answer">
                <span class="answer-date">{{ date('d.m.Y',strtotime($MN->answer_date)) }}</span>
                Результат: {{$MN->answer}}
            </p>
        @endif
        @if ($loop->first)
            <div class="act">
                {{--кнопка "вернуть", еще не придумал как работает--}}
            </div>
        @endif
        
    </div>
    @endforeach
    <pre>
        <?//print_r($arMN);?>
    </pre>
</div>
@endsection

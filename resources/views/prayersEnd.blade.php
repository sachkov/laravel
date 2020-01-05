@extends('layouts.mn_section')

@section('title', 'Завершенные молитвы')

@section('content')
<div class="prayers-list">
    @foreach ($arMN as $MN)
    <div class="prayes">
        <div class="time-column">
            <p class="time from">{{ $MN->created_at }}</p>
            <p class="time at">{{ $MN->end_date }}</p>
        </div>
        <div class="text-column">
            <p class="header"><span>{{$MN->name}}</span></p>
            <p class="description">{{$MN->description}}</p>
            @if ($MN->answer)
                <p class="answer">
                    <span class="answer-date">{{$MN->answer_date}}</span>
                    {{$MN->answer}}
                </p>
            @endif
        </div>
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

@extends('layouts.mn_section')

@section('title', 'Молитвы')

@section('content')
<h3>Общие молитвы</h3>
<div class="prayers-list">
    @foreach ($arMN as $MN)
    <div class="prayes">
        <div class="timeText">
            <div class="time-column">
                <p class="time">{{ $MN->updated_at->format('d.m.Y') }}</p>
            </div>
            <div class="text-column">
                <p class="header">
                    <span>{{$MN->author->name}}</span>
                    @if($MN->answer_date && $MN->answer)
                        <span style="color: green;">Благодарность</span>
                    @endif
                    <span>{{$MN->name}}</span>
                </p>
            </div>
        </div>
        <p class="description">{{$MN->description}}</p>
        @if ($MN->answer)
            <p class="answer">{{$MN->answer}}</p>
        @endif
    </div>
    @endforeach

    <pre>
        <?//print_r($arMN);?>
    </pre>
</div>
@endsection

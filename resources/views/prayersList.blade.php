@extends('layouts.mn_section')

@section('title', 'Молитвы')

@section('content')
<div class="prayers-list">
    @foreach ($arMN as $MN)
    <div class="prayes">
        <div class="time-column">
            <p class="time">{{ $MN->created_at }}</p>
        </div>
        <div class="text-column">
            <p class="header">
                <span>{{$MN->author_name}}</span>
                @if($MN->answer_date && $MN->answer)
                    <span style="color: green;">Благодарность</span>
                @endif
                <span>{{$MN->name}}</span>
            </p>
            <p class="description">{{$MN->description}}</p>
            @if ($MN->answer)
                <p class="answer">{{$MN->answer}}</p>
            @endif
        </div>
    </div>
    @endforeach

    <pre>
        <?//print_r($arMN);?>
    </pre>
</div>
@endsection

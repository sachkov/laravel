@extends('layouts.mn_section')

@section('title', 'Молитвы')

@section('content')
<h3>Общие молитвы</h3>
<div id="PE" class="prayers-list">
    
    <div class="prayes" v-for="mn in arMN">
        <div class="timeText">
            <div class="time-column">
                <p class="time">@{{ $mn["updated_at"] }}</p>
            </div>
            <div class="text-column">
                <p class="header">
                    <span>@{{$mn["author_name"]}}</span>
                    <span style="color: green;"
                        v-if="$mn['answer_date'] && $mn['answer']">
                        Благодарность
                    </span>
                    <span>@{{$mn['name']}}</span>
                </p>
            </div>
        </div>
        <p class="description">@{{$mn['description']}}</p>
        <p class="answer" v-if="$mn['answer']">
            @{{$mn['answer']}}
        </p>
    </div>

    <pre>
        <?//print_r($arMN);?>
    </pre>
</div>
@endsection

@section('script')
    <script src="{{ asset('js/prayersEnd.js') }}"></script>
@endsection
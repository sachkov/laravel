@extends('layouts.mn_section')

@section('title', 'Молитвы')

@section('content')
<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<h3>Общие молитвы</h3>
<div id="PE" class="prayers-list">
    
    <div class="prayes" v-for="mn in arMN">
        <div class="groups-line" v-if="mn_groups[mn.id].length">
            <span v-for="gr in mn_groups[mn.id]"
                class="groups-li"
                :class="['c'+gr]">
                @{{group_name[gr].substr(0,15)+'..'}}
            </span>
        </div>
        <div class="timeText">
            <div class="time-column">
                <p class="time">@{{ mn.updated_at }}</p>
            </div>
            <div class="text-column">
                <p class="header">
                    <span>@{{authors[mn.author_id].name}}</span>
                    <span style="color: green;"
                        v-if="mn.answer_date && mn.answer">
                        Благодарность
                    </span>
                    <span>@{{mn.name}}</span>
                </p>
            </div>
        </div>
        <p class="description">@{{mn.description}}</p>
        <p class="answer" v-if="mn.answer">
            @{{mn.answer}}
        </p>
    </div>

    <pre>
        <?//print_r($arMN);?>
    </pre>
</div>
@endsection

@section('script')
    <script src="{{ asset('js/prayersList.js') }}"></script>
@endsection

@section('css')
    <link href="{{ asset('css/prayersList.css') }}" rel="stylesheet">
@endsection

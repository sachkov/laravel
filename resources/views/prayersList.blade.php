@extends('layouts.mn_section')

@section('title', 'Молитвы')

@section('content')
<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<h3>Общие молитвы</h3>
<div id="PE" class="prayers-list">
    
    <div class="prayes" v-for="(mn, indx) in MNid" v-bind:key="indx">
        <div class="groups-line" :class="{shown:groups[mn].length}">
            <span v-for="gr in groups[mn]"
                class="groups-li"
                :class="['c'+gr]">
                @{{group_name[gr]}}
            </span>
        </div>
        <div class="timeText">
            <div class="time-column">
                <p class="time">@{{ arMN[mn].diff }}</p>
            </div>
            <div class="text-column">
                <p class="header">
                    <span v-show="!admin.includes(mn)">
                        @{{authors[arMN[mn].author_id].name}}
                    </span>
                    <span style="color: green;"
                        v-show="arMN[mn].answer_date && arMN[mn].answer">
                        Благодарность
                    </span>
                    <span>@{{arMN[mn].name}}</span>
                </p>
            </div>
        </div>
        <p class="description">@{{arMN[mn].description}}</p>
        <p class="answer" v-show="arMN[mn].answer">
            @{{arMN[mn].answer}}
        </p>
    </div>

    <div id="more_btn" class="btn btn-outline-warning"
        @click="getMorePrayers(arMN[MNid[MNid.length-1]].updated_at)"
        >
        Еще
    </div>
    <pre>
        <?//print_r($gr);?>
    </pre>
</div>
@endsection

@section('script')
    <script src="{{ asset('js/prayersList.js') }}"></script>
@endsection

@section('css')
    <link href="{{ asset('css/prayersList.css') }}" rel="stylesheet">
@endsection
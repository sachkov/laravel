@extends('layouts.mn_section')

@section('title', 'Импорт молитв из xlsx файла')

@section('content')
<input type="hidden" id="x_token" value="{{ csrf_token() }}">
<h3>Импорт списка молитв из xlsx файла</h3>
<div>

    <pre>
        <?print_r($info);?>
    </pre>
</div>
@endsection

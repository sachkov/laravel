@extends('layouts.app')

@section('content')

<table class="table table-hover prayers-main-table">
  <thead>
    <tr>
      <th scope="col">Дата</th>
      <th scope="col">Автор</th>
      <th scope="col">Название</th>
      <th scope="col">Действия</th>
    </tr>
  </thead>
  <tbody>
    <tr class="main">
      <th title="Дата" scope="row">1</th>
      <td title="Автор">Mark</td>
      <td title="Название" class="js-click-Pname">Otto</td>
      <td title="Действия">@mdo</td>
    </tr>
    <tr class="description">
        <td colspan="4">Описание</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td colspan="2">Larry the Bird</td>
      <td>@twitter</td>
    </tr>
  </tbody>
</table>
<!--<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>-->
@endsection

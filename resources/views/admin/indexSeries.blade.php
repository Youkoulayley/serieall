@extends('layouts.admin')
@section('breadcrumbs')
    <li>
        <a href="{{ url('/admin') }}">
            Administration
        </a>
    </li>
    <li>
        Séries
    </li>
@endsection

@section('content')
    <div>
        <h1 id="content-h1-admin" class="txtcenter">Séries</h1>
        <p class="txtcenter">Index des séries</p>
    </div>
@endsection
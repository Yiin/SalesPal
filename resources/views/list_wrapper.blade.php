@extends('header')

@section('head')
	@parent

    <script src="{{ asset('js/jquery.contextMenu.min.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery.contextMenu.min.css') }}">

    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" type="text/css"/>

@stop

@section('content')

    @include('list')

@stop

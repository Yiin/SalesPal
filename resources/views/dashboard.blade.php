@extends('header')

@section('head')
@parent

@include('money_script')

<script src="{!! asset('js/Chart.min.js') !!}" type="text/javascript"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}" type="text/javascript"></script>
<link href="{{ asset('css/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

@stop

@section('content')

    <!--
        Nav Bar?
    -->
    <ol class="breadcrumb path">
        <li>
            <a class="fa fa-home" href="/"></a>
        </li>
        <li class="active">{{ trans('texts.dashboard') }}</li>
    </ol>

    <div class="vue-app" id="vueapp_{{ str_random() }}">
        <!--
            Statistics
        -->
        <dashboard-statistics
            :currencies="{{ $currencies }}"
            :revenue="{{ json_encode($paidToDate) }}"
            :expenses="{{ json_encode($expenses) }}"
            :balances="{{ json_encode($balances) }}"
        ></dashboard-statistics>

        <!--
            Activity
        -->
        <dashboard-activities
            :activities="{{ $activitiesList->items }}"
            :state="{{ json_encode(['first' => $activitiesList->first, 'last' => $activitiesList->last ]) }}"
        ></dashboard-activities>
    </div>
@stop
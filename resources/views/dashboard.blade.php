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
    <div class="dashboard-breadcrumb">
        <ol class="breadcrumb">
            <li class="active">Dashboard</li>
        </ol>
    </div>

    <div class="vue-app" id="vueapp_{{ str_random() }}">
        <dashboard-statistics
            :currencies="{{ $currencies }}"
            :revenue="{{ json_encode($paidToDate) }}"
            :expenses="{{ json_encode($expenses) }}"
            :balances="{{ json_encode($balances) }}"
        ></dashboard-statistics>
    </div>

    <!--
        Activity
    -->
    <div class="panel panel-dashboard">
        <div class="nav dashboard-heading">
            <ul class="nav navbar-nav navbar-left dashboard-nav">
                <li class="active"><a href="#">All Activity</a></li>
                <li><a href="#">Payments</a></li>
                <li><a href="#">Expenses</a></li>
                <li><a href="#">Upcoming Invoices</a></li>
                <li><a href="#">Invoices Past Due</a></li>
                <li><a href="#">Tasks</a></li>
                <li><a href="#">Projects</a></li>
            </ul>
            <div class="new-activity">
                <span class="amount">10</span>
                <span>New Today</span>
            </div>
        </div>
        <div class="panel-body panel-days-holder">
            <div class="day">
                <div class="day-number">
                    27th
                </div>
                <div class="border"></div>
                <div class="events">
                    <div class="event">
                        <span class="time">12:50</span>
                         <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                         <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                         <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                         <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                         <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                </div>
            </div>

            <div class="day">
                <div class="day-number">
                    27th
                </div>
                <div class="border"></div>
                <div class="events">
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                </div>
            </div>

            <div class="day">
                <div class="day-number">
                    27th
                </div>
                <div class="border"></div>
                <div class="events">
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                    <div class="event">
                        <span class="time">12:50</span>
                        <a href="#">Tomas</a> deleted invoice <a href="#">NN4552159</a>
                    </div>
                </div>
            </div>

            <div class="day">
                <div class="day-number">
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="border"></div>
            </div>
        </div>
    </div>



    <script type="text/javascript">
        $(function() {
            $('.normalDropDown:not(.dropdown-toggle)').click(function() {
                window.location = '{{ URL::to('invoices/create') }}';
            });
        });
    </script>
@stop
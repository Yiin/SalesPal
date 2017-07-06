@extends('header')

@section('head')
@parent

@include('money_script')

<script src="{!! asset('js/Chart.min.js') !!}" type="text/javascript"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}" type="text/javascript"></script>
<link href="{{ asset('css/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

@stop

@section('content')
<div class="dashboardwrraper">

            <!-- 
                Nav Bar?
             -->
<div class="dashboard-breadcrumb">
<ol class="breadcrumb">
    <li class="active">Dashboard</li>
</ol>
</div>
<div class="row">
<div class="vue-app valutebutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
  <dropdown class="calculator-show" default="2" options="[
    { label: 'Eur', value: '1' }, 
    { label: 'Usd', value: '2' }, 
    { label: '3', value: '3' }]
" width="158px"></dropdown>
</div>

<div class="vue-app datebutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
  <dropdown class="calculator-show" default="2" options="[
    { label: 'Month', value: '1' },     
    { label: '2', value: '2' }, 
    { label: '3', value: '3' }]
" width="158px"></dropdown>
</div>

<div class="vue-app menubutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
  <dropdown class="calculator-show" default="2" options="[
    { label: '', value: '1' }, 
    { label: '2', value: '2' }, 
    { label: '3', value: '3' }]
" width="308px"></dropdown>
</div>
</div>

             <!-- 
                three coll
             -->
<div class="coll-row">
        <div class="panel panel-default expenses-panel">
            <div class="panel-revenue">
                <div class="texttotal">Toral Revenue</div>
                <div class="textnumber">6,666.0</div>
                <div class="textsum">0.00</div>
                <div class="textlast">Last <span>30</span> days</div>
            </div>
        </div>
=======
    <!-- 
        Nav Bar?
    -->
    <div class="dashboard-breadcrumb">
        <ol class="breadcrumb">
            <li class="active">Dashboard</li>
        </ol>
    </div>
    <div class="dashboard-dropdowns">
        <div class="vue-app valutebutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
          <dropdown class="calculator-show" default="2" options="[
          { label: 'Eur', value: '1' }, 
          { label: 'Usd', value: '2' }, 
          { label: '3', value: '3' }
        ]" width="158px"></dropdown>
      </div>

      <div class="vue-app datebutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
          <dropdown class="calculator-show" default="2" options="[
          { label: 'Month', value: '1' },     
          { label: '2', value: '2' }, 
          { label: '3', value: '3' }]
          " width="158px"></dropdown>
      </div>

      <div class="vue-app menubutton dashboardbuttons" id="vueapp_{{ str_random() }}"">
          <dropdown class="calculator-show" default="2" options="[
          { label: '', value: '1' }, 
          { label: '2', value: '2' }, 
          { label: '3', value: '3' }]
          " width="308px"></dropdown>
      </div>
    </div>
>>>>>>> 9661d75216830cb1edb25ebed8aaf6fa1c7e9a06

    <!-- 
        Totals
    -->
    <div class="totals-wrapper">
        <div class="total-panel">
            <div class="total-panel-header">
                Total Revenue
            </div>
            <div class="total-panel-body">
                <div class="total-value">
                    € 6,666.0
                </div>
                <div class="total-meta">
                    <div class="total-sum">
                        0.00
                    </div>
                    <div class="total-time-frame">
                        Last <span class="highlight">30</span> days
                    </div>
                </div>
            </div>

        </div>
        <div class="total-panel">
            <div class="total-panel-header">
                Total Expenses
            </div>
            <div class="total-panel-body">
                <div class="total-value">
                    6,666.0
                </div>
                <div class="total-meta">
                    <div class="total-sum">
                        0.00
                    </div>
                    <div class="total-time-frame">
                        Last <span class="highlight">30</span> days
                    </div>
                </div>
            </div>
        </div>
        <div class="total-panel">
            <div class="total-panel-header">
                Total Outstanding
            </div>
            <div class="total-panel-body">
                <div class="total-value">
                    6,666.0
                </div>
                <div class="total-meta">
                    <div class="total-sum">
                        0.00
                    </div>
                    <div class="total-time-frame">
                        Last <span class="highlight">30</span> days
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- 
        Graph
    -->

    <div class="panel panel-body graphpanel">
        <div class="graphpanel-colors">
            <div class="graphpanel-lightblue"></div><span>Invoices</span>
            <div class="graphpanel-lighterblue"></div><span>Expences</span>
        </div>
        <div id="morris-one-line-chart" style="position: relative; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"><svg height="349" version="1.1" width="1628" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden; position: relative; left: -0.999996px; top: -0.499996px;"><desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Created with Raphaël 2.1.0</desc><defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs><text x="36.671875" y="316.25" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">0</tspan></text><path fill="none" stroke="#aaaaaa" d="M49.171875,316.25H2055" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="36.671875" y="243.4375" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">10</tspan></text><path fill="none" stroke="#aaaaaa" d="M49.171875,243.4375H2055" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="36.671875" y="170.625" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">20</tspan></text><path fill="none" stroke="#aaaaaa" d="M49.171875,170.625H2055" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="36.671875" y="97.8125" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">30</tspan></text><path fill="none" stroke="#aaaaaa" d="M49.171875,97.8125H2055" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="36.671875" y="25" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">40</tspan></text><path fill="none" stroke="#aaaaaa" d="M49.171875,25H2055" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="2055" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2015</tspan></text><text x="1768.6772523953853" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2014</tspan></text><text x="1482.3545047907705" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2013</tspan></text><text x="1195.2473113023075" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2012</tspan></text><text x="908.9245636976926" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2011</tspan></text><text x="622.6018160930779" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2010</tspan></text><text x="336.27906848846305" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2009</tspan></text><text x="49.171875" y="328.75" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#949ba2" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);" dy="4.375">2008</tspan></text><path fill="none" stroke="#01a8fe" d="M49.171875,267.7083333333333C120.94867337211576,255.57291666666666,264.5022701163473,222.81227200182397,336.27906848846305,219.16666666666666C407.8597553896168,215.53102200182397,551.0211291919242,253.14583333333331,622.6018160930779,238.58333333333331C694.1825029942315,224.02083333333331,837.343876796539,102.66666666666666,908.9245636976926,102.66666666666666C980.5052505988464,102.66666666666666,1123.6666244011537,228.8838579683698,1195.2473113023075,238.58333333333331C1338.604796575577,258.0088579683698,1625.3197671221158,214.31028550790754,1768.6772523953853,219.16666666666666C1840.257939296539,221.59153550790754,1983.4193130988463,255.57291666666666,2055,267.7083333333333" stroke-width="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><circle cx="49.171875" cy="267.7083333333333" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="336.27906848846305" cy="219.16666666666666" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="622.6018160930779" cy="238.58333333333331" r="8" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="908.9245636976926" cy="102.66666666666666" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="1195.2473113023075" cy="238.58333333333331" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="1768.6772523953853" cy="219.16666666666666" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="2055" cy="267.7083333333333" r="5" fill="#01a8fe" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle></svg>
        </div>
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

@stop
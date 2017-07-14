@extends('header')


@section('content')

<div class="show-blade-wrapper">
    <div class="info-blade-col">
        <div class="panel-wrapper">
            <div class="show-wrapper">
                <div class="show-wrapper-header purple-header">
                    Standings  
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col text-color">
                        Paid to date
                        <span>
                            $ 154,577.99
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col text-color">
                        Outstanding
                        <span>
                            $ 541,874,99.87
                        </span>
                    </div>
                </div>
            </div>
        </div>
     <div class="panel-wrapper">   
        <div class="show-wrapper">
                <div class="show-wrapper-img-header">
                <img src="https://images.freecreatives.com/wp-content/uploads/2015/04/logo033.png" alt="Mountain View" style="width:331px;height:135px;">
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col">
                        Company
                        <span>
                            DYNAMIX - IT Paslaugos
                        </span>
                    <hr>
                    </div>
                    <div class="grid-col">
                        Vat Number
                        <span>
                            IE411755871512
                        </span>
                    <hr>
                    </div>
                    <div class="grid-col">
                        Webstie
                        <span>
                            tomass.lt
                        </span>
                    <hr>
                    </div>
                    <div class="grid-col">
                        Phone
                        <span>
                            +370 673 31871
                        </span>
                    <hr>
                    </div>
                    <div class="grid-col">
                        Industry
                        <span>
                            Professional Services & Consulting
                        </span>
                    <hr>
                    </div>
                    <div class="grid-col">
                        Payment Terms
                        <span>
                            Net 90
                        </span>
                    </div>

                </div>
            </div>
        </div>

    <div class="panel-wrapper">
        <div class="show-wrapper">
                <div class="show-wrapper-header orange-header">
                    Contact  
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col">
                        <span>
                            Tomas Skvireckas
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Job Postion
                        <span>
                            Manager
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Email
                        <span>
                            info@tomass.lt
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Phone
                        <span>
                            +370 673 31871
                        </span>
                    </div>
                </div>
            </div>
        </div>

    <div class="panel-wrapper">
        <div class="show-wrapper">
                <div class="show-wrapper-header green-color">
                    Address  
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col">
                        Country
                        <span>
                            Lithuania
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        City
                        <span>
                            Panevežys
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Street
                        <span>
                            Basanavičiaus g.
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Apt / Suite
                        <span>
                            24 - 11
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Postal Code
                        <span>
                            32108
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="show-blade-col">
        <div class="panel-wrapper table-wrapper">
            <div class="nav table-heading">
            <ul class="nav navbar-nav navbar-left show-blade-navbar">
                <li class="active"><a href="#">Invoices</a></li>
                <li><a href="#">Payment</a></li>
                <li><a href="#">Credit</a></li>
            </ul>
            <div class="new-activity show-blade-task table-invoices">
                <span><a href="#">New Invoices</a></span>
                <span><a href="#">Enter Payment</a></span>
                <span><a href="#">Enter Credit</a></span>
            </div>
            </div>
                <div class="table-body">
                <div class="vue-app btn-client" id="vueapp_{{ str_random() }}">
        <entity-table 
            entity="{{ ENTITY_CLIENT }}"
            client-id="{{ $clientId ?? $vendorId ?? '' }}"
        ></entity-table>
    </div>
                </div>
            </div>
       
        <div class="panel panel-dashboard">
        <div class="nav dashboard-heading">
            <ul class="nav navbar-nav navbar-left dashboard-nav">
                <li class="active"><a href="#">Activity</a></li>
                <li><a href="#">Task</a></li>
            </ul>
            <div class="new-activity show-blade-task">
                <span><a href="#">Add New Task</a></span>
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
        </div>
    </div>
</div>
@stop

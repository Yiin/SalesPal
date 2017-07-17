@extends('header')

@section('head')
	@parent

    @include('money_script')

    @foreach ($account->getFontFolders() as $font)
        <script src="{{ asset('js/vfs_fonts/'.$font.'.js') }}" type="text/javascript"></script>
    @endforeach
	<script src="{{ asset('pdf.built.js') }}?no_cache={{ NINJA_VERSION }}" type="text/javascript"></script>
    <script src="{{ asset('js/lightbox.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/lightbox.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet" type="text/css"/>
	<script src="{{ asset('js/quill.min.js') }}" type="text/javascript"></script>
@stop

@section('content')
<div class="invoices-page-wrapper">
    <div class="invoices-page-col">
        <div class="panel-wrapper">
            <div class="show-wrapper">
                <div class="show-wrapper-header blue-header">
                    Status  
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col">
                        Status
                        <span>
                            Viewied
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col text-color">
                        Amount
                        <span>
                            $ 100,852,941.99
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col text-color-orange">
                        Paid In
                        <span>
                            $ 0.00
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-wrapper">
            <div class="show-wrapper">
                <div class="show-wrapper-header orange-header">
                    Details  
                </div>
                <div class="show-wrapper-body">
                    <div class="grid-col">
                        Client
                        <span>
                            DYNAMIX - IT Solution
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Invoice #
                        <span>
                            NN8547267
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        PO #
                        <span>
                            87574468
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Invoice Date
                        <span>
                            17 Apr, 2017
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Invoice Due Date
                        <span>
                            24 Apr, 2017
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col text-color">
                        Discount
                        <span>
                            100 %
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="invoices-page-col-second">
        <div class="completed-invoices-form">
            <div class="invoices-page-first">
                <div class="panel-wrapper table-wrapper">
                <div class="nav table-heading">
                    <ul class="nav navbar-nav navbar-left show-blade-navbar">
                        <li class="active">
                            <a href="#">Items</a>
                        </li>
                        <li>
                            <a href="#">Documents</a>
                        </li>
                        <li>
                            <a href="#">Note</a>
                        </li>
                        <li>
                            <a href="#">Terms</a>
                        </li>
                    </ul>
                </div>
                <div class="invoices-page-wrapper-body">
                    <div class="scrollbar style-2">
                    <div class="flex-grid">
                        <div class="grid-col">
                            Item Name
                            <br>
                                <span>
                                    Adidas SPORTBOOST Running Shoes
                                </span>
                            <hr>
                        </div>
                        <div class="grid-col">
                            Unit Cost
                            <br>
                                <span>
                                    $ 79.99
                                </span>
                            <hr>
                        </div>
                    </div>
                    <div class="flex-grid">
                        <div class="grid-col">
                            Quantity
                            <br>
                                <span>
                                    1
                                </span>
                            <hr>
                        </div>
                        <div class="grid-col">
                            Tax Rate
                            <br>
                                <span>
                                    SMALL
                                </span>
                        </div>
                    </div>
                    <div class="flex-grid">
                        <div class="grid-col description-hr">
                            Description
                            <br>
                                <span>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor 
                                    incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
                                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                    dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur
                                    excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt.
                                </span>
                            <hr>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="invoices-page-scond">
            <div class="panel-wrapper">
                <div> kazkas</div>
            </div>
    </div>
 </div>
 </div>
 </div>
@stop

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
   <div class="invoice-new-wrapper">
        <div class="invoice-new-col-first">
            <div class="panel-wrapper">
                <div class="show-wrapper">
                <div class="right-side-nav">
                 <span><a href="#"> Add New Client </a></span>
                </div>
                <div class="show-wrapper-header blue-header">
                    Client  
                </div>
                <div class="show-wrapper-body selector-invoices">
                    <div class="scrollbar style-2">
                    <div class="radio">
                    <div>
                        <input id="radio-1" name="radio" type="radio">
                        <label  for="radio-1" class="radio-label">DYNAMIX - IT Solutions</label>
                    </div>
                    <hr>
                    <div class="radio">
                        <input id="radio-2" name="radio" type="radio">
                        <label  for="radio-2" class="radio-label">UAB “AKA Juventus Panevežys”</label>
                    </div>
                    <hr>
                    <div class="radio">
                        <input id="radio-3" name="radio" type="radio">
                        <label  for="radio-3" class="radio-label">UAB “NORFA XL”</label>
                    </div>
                    <hr>
                    <div class="radio">
                        <input id="radio-4" name="radio" type="radio">
                        <label  for="radio-4" class="radio-label">UAB “MAXIMA LT”</label>
                    </div>
                    <hr>
                    <div class="radio">
                        <input id="radio-5" name="radio" type="radio">
                        <label  for="radio-5" class="radio-label">AB “Swedbank”</label>
                    </div>
                    <hr>
                                        <div class="radio">
                        <input id="radio-6" name="radio" type="radio">
                        <label  for="radio-6" class="radio-label">AB “Swedbank”</label>
                    </div>
                    <hr>
                                        <div class="radio">
                        <input id="radio-7" name="radio" type="radio">
                        <label  for="radio-7" class="radio-label">AB “Swedbank”</label>
                    </div>
                    <hr>
                                        <div class="radio">
                        <input id="radio-8" name="radio" type="radio">
                        <label  for="radio-8" class="radio-label">AB “Swedbank”</label>
                    </div>
                    </div>
                </div>
            </div>
            </div>
            </div>

        <div class="double-pannel invoices-form">
            <div class="panel-wrapper invoices-form-first">
                <div class="show-wrapper ">
                    <div class="show-wrapper-header orange-header">
                        Items  
                    </div>
                    <div class="show-wrapper-body">
                    <div class="form-panel-body">

                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Item <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Unit Cost <input name="Phone" type="text" value=""><br></span>
                            </div>
                        </div>
                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Quantity <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                 <span>Tax Rate <select name="country">
                                    <option value="country">
                                        Small
                                    </option>
                                </select></span>
                            </div>
                        </div>
                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Description
                                <textarea class="textarea-height" id="input-message" name="message"></textarea></span>
                            </div>
                        </div>
                        <hr class="invoice-hr">
                        <div class="flex-grid margin-fix">
                        <div class="col">
                                <span>Item <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Unit Cost <input name="Phone" type="text" value=""><br></span>
                            </div>
                        </div>
                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Quantity <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                 <span>Tax Rate <select name="country">
                                    <option value="country">
                                        Small
                                    </option>
                                </select></span>
                            </div>
                            </div>
                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Description
                                <textarea class="textarea-height" id="input-message" name="message"></textarea></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
                            <div class="invoices-form-secound">
                          <div class="panel-wrapper second-invoice-form">
                    <div class="show-wrapper-body">
                    <div class="grid-col">
                        Subtotal
                        <span>
                            € 75.99
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Tax
                         <div>
                        <span>
                             <div class="invoices-col-decor invoices-small-color">SMALL</div> 2.78 %
                        </span>
                        <span>
                            € 999.000
                        </span>
                        </div>
                        <hr>
                        </div>
                    <div class="grid-col">
                        Other Tax
                        <div>
                        <span>
                             <div class="invoices-col-decor invoices-big-color">BIG</div> 52.78 %
                        </span>
                        <span>
                            € 999,999.000
                        </span>
                        </div>
                        <hr>
                    </div>
                    <div class="grid-col">
                        Paid to Date
                        <span>
                            € 0.00
                        </span>
                        <hr>
                    </div>
                        <div class="grid-col ">
                        Balance Due
                        <span>
                            € 1,000,085.97
                        </span>
                    </div>
            </div>
            </div>
            </div>
            </div>
     <div class="panel-wrapper table-wrapper">
            <div class="nav table-heading">
            <ul class="nav navbar-nav navbar-left show-blade-navbar">
                <li class="active"><a href="#">Documents</a></li>
                <li><a href="#">Note to Client</a></li>
                <li><a href="#">Terms</a></li>
                <li><a href="#">Footer</a></li>
            </ul>
            </div>
            <div class="show-wrapper-body">
                <div class="drag-and-drop">

                </div>
            </div>
        </div>
        </div>
        

        <div class="invoice-new-col-secound">

            <div class="panel-wrapper invoices-wrapper">
            <div class="show-wrapper">
                <div class="show-wrapper-header green-header">
                    Details  
                </div>
                <div class="show-wrapper-body">
                    <div class="flex-grid margin-fix">
                        <div class="col">
                                <span>Invoice Date <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Invoice # <input name="Phone" type="text" value=""><br></span>
                            </div>
                        </div>
                        <div class="flex-grid margin-fix">
                            <div class="col">
                                <span>Invoice Due Date <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Po # <input name="Phone" type="text" value=""><br></span>
                        </div>
                        </div>
                            <div class="flex-grid margin-fix">
                        <div class="col">
                                <span>Partial / Deposit <input name="Vat" type="text" value=""><br></span>
                            </div>                            
                            <div class="col">
                                <span>Discount <input name="Phone" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                 <span class="select-without-name"><select name="percent">
                                    <option value="percent">
                                        Percent
                                    </option>
                                </select></span>
                            </div>

                    </div>
                </div>
            </div>
            </div>

            <div class="panel-wrapper">
                                            <div class="show-wrapper">
                <div class="show-wrapper-header purple-header">
                    Invoice Preview  
                </div>

            </div>
            </div>
        </div>

   </div>

@stop

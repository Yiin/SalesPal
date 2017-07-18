@extends('header')

@section('head')
    @parent

    @include('money_script')

    <style type="text/css">
        .input-group-addon {
            min-width: 40px;
        }
    </style>
@stop

@section('content')

	<!-- {!! Former::open($url)
        ->addClass('col-md-10 col-md-offset-1 warn-on-exit main-form')
        ->onsubmit('onFormSubmit(event)')
        ->method($method)
        ->rules(array(
    		'client' => 'required',
    		'invoice' => 'required',
    		'amount' => 'required',
    	)) !!}

    @if ($payment)
        {!! Former::populate($payment) !!}
    @else
        @if ($account->payment_type_id)
            {!! Former::populateField('payment_type_id', $account->payment_type_id) !!}
        @endif
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
    </span> -->


    <!-- start of psd design-->

    <div class="product-row fixclearElem"><!-- start of .product-row -->


      <div class="product-block float-horizontal width-cl block-height-n-p"><!-- start of .product-block -->
        <div class="product-block__name">
          Client
        </div>
        <div class="scroll-content"><!-- start of .scroll-content -->

          <ul class="scroll-ul fixclearElem">
            <li>
              <input type="radio" id="f-option" name="selector">
              <label for="f-option">DYNAMIX - IT Solutions</label>

              <div class="check"></div>
            </li>
            <li>
              <input type="radio" id="s-option" name="selector">
              <label for="s-option">UAB “AKA Juventus Panevežys”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="t-option" name="selector">
              <label for="t-option">UAB “NORFA XL”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="d-option" name="selector">
              <label for="d-option">UAB “MAXIMA LT”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="e-option" name="selector">
              <label for="e-option">AB “Swedbank”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="z-option" name="selector">
              <label for="z-option">AB “Lietuvos Bankas”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="g-option" name="selector">
              <label for="g-option">DYNAMIX - IT Solutions</label>

              <div class="check"></div>
            </li>
            <li>
              <input type="radio" id="h-option" name="selector">
              <label for="h-option">UAB “AKA Juventus Panevežys”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="i-option" name="selector">
              <label for="i-option">UAB “NORFA XL”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="j-option" name="selector">
              <label for="j-option">UAB “MAXIMA LT”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="k-option" name="selector">
              <label for="k-option">AB “Swedbank”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="l-option" name="selector">
              <label for="l-option">AB “Lietuvos Bankas”</label>

              <div class="check"><div class="inside"></div></div>
            </li>
          </ul>
        </div><!-- end of .scroll-content -->
      </div><!-- end of .product-block -->

      <div class="product-block float-horizontal width-inv block-height-n-p"><!-- start of .product-block -->
        <div class="product-block__name block__name--color-green">
          Invoice
        </div>
        <div class="scroll-content"><!-- start of .scroll-content -->

          <ul class="scroll-ul fixclearElem">
            <li>
              <input type="radio" id="a1-option" name="selector">
              <label for="a1-option">0014 - Sent - doleriai 1 - $7,273,678.80 | $7,273,678.80</label>

              <div class="check"></div>
            </li>
            <li>
              <input type="radio" id="b1-option" name="selector">
              <label for="b1-option">0022 - Sent - doleriai 1 - $233.70 | $233.70</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="c1-option" name="selector">
              <label for="c1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="d1-option" name="selector">
              <label for="d1-option">0022 - Sent - doleriai 1 - $233.70 | $233.70</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="e1-option" name="selector">
              <label for="e1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="f1-option" name="selector">
              <label for="f1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="g1-option" name="selector">
              <label for="g1-option">0014 - Sent - doleriai 1 - $7,273,678.80 | $7,273,678.80</label>

              <div class="check"></div>
            </li>
            <li>
              <input type="radio" id="h1-option" name="selector">
              <label for="h1-option">0022 - Sent - doleriai 1 - $233.70 | $233.70</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="j1-option" name="selector">
              <label for="j1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="k1-option" name="selector">
              <label for="k1-option">0022 - Sent - doleriai 1 - $233.70 | $233.70</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="l1-option" name="selector">
              <label for="l1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
            <li>
              <input type="radio" id="m1-option" name="selector">
              <label for="m1-option">0018 - Partial - pounds 1 - E£26,408.10 | E£24,750.00</label>

              <div class="check"><div class="inside"></div></div>
            </li>
          </ul>
        </div><!-- end of .scroll-content -->
      </div><!-- end of .product-block -->

      <div class="product-block float-horizontal width-new-d block-height-n-p no-r-mar"><!-- start of .product-block -->
        <div class="product-block__name block__name--color-orange">
          Details
        </div>
        <div class="product-block-details details-padding">
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Amount</div>
            <div>
              <input class="p-details-input input-space" type="text" name="" value="39.99">
              <div class="p-details-input input-small">
                &euro;<i class="fa fa-caret-down" aria-hidden="true"></i>
              </div>
            </div>
          </div>
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Payment Type</div>
            <div class="p-details-input full-w">
              American Express <i class="fa fa-caret-down" aria-hidden="true"></i>
            </div>
          </div>
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Payment Date</div>
            <div class="p-details-input full-w">
              10 Apr, 2017 <i class="fa fa-calendar" aria-hidden="true"></i>
            </div>
          </div>
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Transaction Reference</div>
            <input class="p-details-input full-w" type="text" name="" value="PAYMENT NUMBER 589003885885">
          </div>
        </div>
      </div><!-- end of .product-block -->

    </div><!-- end of .product-row -->

    <div class="product-row fixclearElem"><!-- start of .product-row -->
      <div class="product-button">
        Save
      </div>
      <div class="product-button p-button-balck">
        Cancel
      </div>
    </div>

    <!-- end of psd design-->

<!--test
	<div class="row">
		<div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
            <div class="panel-body">

            @if ($payment)
             {!! Former::plaintext()->label('client')->value($payment->client->present()->link) !!}
             {!! Former::plaintext()->label('invoice')->value($payment->invoice->present()->link) !!}
             {!! Former::plaintext()->label('amount')->value($payment->present()->amount) !!}
            @else
			 {!! Former::select('client')->addOption('', '')->addGroupClass('client-select') !!}
			 {!! Former::select('invoice')->addOption('', '')->addGroupClass('invoice-select') !!}
			 {!! Former::text('amount') !!}

             @if (isset($paymentTypeId) && $paymentTypeId)
               {!! Former::populateField('payment_type_id', $paymentTypeId) !!}
             @endif
            @endif

            @if (!$payment || !$payment->account_gateway_id)
			 {!! Former::select('payment_type_id')
                    ->addOption('','')
                    ->fromQuery($paymentTypes, 'name', 'id')
                    ->addGroupClass('payment-type-select') !!}
            @endif

			{!! Former::text('payment_date')
                        ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT))
                        ->addGroupClass('payment_date')
                        ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
			{!! Former::text('transaction_reference') !!}

            @if (!$payment)
                {!! Former::checkbox('email_receipt')->label('&nbsp;')->text(trans('texts.email_receipt'))->value(1) !!}
            @endif

            </div>
            </div>

		</div>
	</div>
test2-->
<!--
	<center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->appendIcon(Icon::create('remove-circle'))->asLinkTo(URL::to('/payments'))->large() !!}
        @if (!$payment || !$payment->is_deleted)
            {!! Button::success(trans('texts.save'))->withAttributes(['id' => 'saveButton'])->appendIcon(Icon::create('floppy-disk'))->submit()->large() !!}
        @endif

        @if ($payment)
            {!! DropdownButton::normal(trans('texts.more_actions'))
                  ->withContents($actions)
                  ->large()
                  ->dropup() !!}
        @endif

	</center>
test3-->
    @include('partials/refund_payment')

	{!! Former::close() !!}

	<script type="text/javascript">

	var invoices = {!! $invoices !!};
	var clients = {!! $clients !!};

	$(function() {

        @if ($payment)
          $('#payment_date').datepicker('update', '{{ $payment->payment_date }}')
          @if ($payment->payment_type_id != PAYMENT_TYPE_CREDIT)
            $("#payment_type_id option[value='{{ PAYMENT_TYPE_CREDIT }}']").remove();
          @endif
        @else
          $('#payment_date').datepicker('update', new Date());
		  populateInvoiceComboboxes({{ $clientPublicId }}, {{ $invoicePublicId }});
        @endif

		$('#payment_type_id').combobox();

        @if (!$payment && !$clientPublicId)
            $('.client-select input.form-control').focus();
        @elseif (!$payment && !$invoicePublicId)
            $('.invoice-select input.form-control').focus();
        @elseif (!$payment)
            $('#amount').focus();
        @endif

        $('.payment_date .input-group-addon').click(function() {
            toggleDatePicker('payment_date');
        });
	});

    function onFormSubmit(event) {
        $('#saveButton').attr('disabled', true);
    }

    function submitAction(action) {
        $('#action').val(action);
        $('.main-form').submit();
    }

    function submitForm_payment(action) {
        submitAction(action);
    }

    function onDeleteClick() {
        sweetConfirm(function() {
            submitAction('delete');
        });
    }

	</script>

@stop

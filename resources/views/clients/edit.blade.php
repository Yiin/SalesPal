@extends('header')

@section('onReady')
    $('input#name').focus();
@stop

@section('content')

    @if ($errors->first('contacts'))
        <div class="alert alert-danger">{{ trans($errors->first('contacts')) }}</div>
    @endif

    <!-- ------------------------------------------------- -->

    <div class="col-md-12">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Check your vat number</h3>
							</div>
							<div class="panel-body">
								<form id="recieve-vat" method="POST" action="{{ url('vat') }}">
									{{ csrf_field() }}
                                    <div class="form-group">
										<input type="text" class="form-control" name="vat" id="input-vat">
									</div>
									<div class="form-group">
										<input type="submit" class="btn btn-primary btn-xs" value="Check VAT">
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="alert" id="progress-to-add">
					<p class="test-result">

					</p>
				</div>
			</div>
	</div>


    <script>
        $('#recieve-vat').submit(function (e) {
            e.preventDefault();
            var form = $('#recieve-vat'),
                form_data = form.serialize(),
                vat_value = form.find('input[name="vat"]').val();

            if (vat_value !== "" && vat_value.length < 20) {
                $("#progress-to-add").removeClass();
                $.ajax({
                    url: "{{ url('vat') }}",
                    type: "POST",
                    data: form_data,
                    dataType: 'json',
                    success: function (data) {
                        var d = $("#progress-to-add");

                        var html = '';

                        if (data.results.Name === "---") {
                            d.addClass("alert-danger");

                            html += "Your provided VAT number is not valid. Please Check again or try a different number.";
                        }
                        else {
                            d.addClass("alert-success");

                            html += '<ul>';
                            for (var key in data.results) {
                                html += '<li>' + key + ': ' + data.results[key] + '</li>';
                            }
                            html += '</ul>';
                        }
                        $('.test-result').html(html);
                    },
                    error: function () {
                        var d = $("#progress-to-add");
                        d.addClass("alert-error");

                        $('.test-result').text("Your provided VAT number is not valid. Please Check again or try a different number.")
                    }
                });
            }
            else if (vat_value.length > 20) {
                $('.test-result').text('Your entered value is to long. Max lenght 20 char.');
            }
            else {
                $('.test-result').text('You have to enter value.');
            }
        });
	</script>

    {!! Former::open($url)
            ->autocomplete('off')
            ->rules(
                ['email' => 'email']
            )->addClass('col-md-12 recieve-vat warn-on-exit')
            ->method($method) !!}

    @include('partials.autocomplete_fix')

    @if ($client)
        {!! Former::populate($client) !!}
        {!! Former::hidden('public_id') !!}
    @else
        {!! Former::populateField('invoice_number_counter', 1) !!}
        {!! Former::populateField('quote_number_counter', 1) !!}
        @if ($account->client_number_counter)
            {!! Former::populateField('id_number', $account->getNextNumber()) !!}
        @endif
    @endif

    <div class="row">
        <ul class="vat-checks">
            @if($client)
                @foreach($client->vatChecks as $vatCheck)
                    <li>
                        <ul>
                            <li>Status: {{ $vatCheck->is_valid ? 'VALID' : 'INVALID' }}</li>
                            <li>Address: {{ $vatCheck->address }}</li>
                            <li>Country code: {{ $vatCheck->country_code }}</li>
                            <li>Name: {{ $vatCheck->name }}</li>
                            <li>VAT Number{{ $vatCheck->vat_number }}</li>
                            <li>Checked at: {{ $vatCheck->created_at }}</li>
                        </ul>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    <input type="hidden" name="wat_checks" value="">

    <div class="row">
		<div class="col-md-6">
        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.organization') !!}</h3>
          </div>
            <div class="panel-body">

			{!! Former::text('name')->data_bind("attr { placeholder: placeholderName }") !!}
                {!! Former::text('id_number')->placeholder($account->clientNumbersEnabled() ? $account->getNextNumber() : ' ') !!}
                {!! Former::text('vat_number') !!}
                {!! Former::text('website') !!}
                {!! Former::text('work_phone') !!}

                @if (Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS))
                    @if ($customLabel1)
                        {!! Former::text('custom_value1')->label($customLabel1) !!}
                    @endif
                    @if ($customLabel2)
                        {!! Former::text('custom_value2')->label($customLabel2) !!}
                    @endif
                @endif

                @if ($account->usesClientInvoiceCounter())
                    {!! Former::text('invoice_number_counter')->label('invoice_counter') !!}

                    @if (! $account->share_counter)
                        {!! Former::text('quote_number_counter')->label('quote_counter') !!}
                    @endif
                @endif
            </div>
        </div>

        <div class="panel panel-default" style="min-height: 500px">
          <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.address') !!}</h3>
          </div>
            <div class="panel-body">

			{!! Former::text('address1') !!}
                {!! Former::text('address2') !!}
                {!! Former::text('city') !!}
                {!! Former::text('state') !!}
                {!! Former::text('postal_code') !!}
                {!! Former::select('country_id')->addOption('','')
                    ->fromQuery($countries, 'name', 'id') !!}

        </div>
        </div>
		</div>
		<div class="col-md-6">


        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.contacts') !!}</h3>
          </div>
            <div class="panel-body">

			<div data-bind='template: { foreach: contacts,
		                            beforeRemove: hideContact,
		                            afterAdd: showContact }'>
				{!! Former::hidden('public_id')->data_bind("value: public_id, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][public_id]'}") !!}
                {!! Former::text('first_name')->data_bind("value: first_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][first_name]'}") !!}
                {!! Former::text('last_name')->data_bind("value: last_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][last_name]'}") !!}
                {!! Former::text('email')->data_bind("value: email, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][email]', id:'email'+\$index()}") !!}
                {!! Former::text('phone')->data_bind("value: phone, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][phone]'}") !!}
                @if ($account->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $account->enable_portal_password)
                    {!! Former::password('password')->data_bind("value: password()?'-%unchanged%-':'', valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][password]'}")->autocomplete('new-password') !!}
                @endif
                <div class="form-group">
					<div class="col-lg-8 col-lg-offset-4 bold">
						<span class="redlink bold" data-bind="visible: $parent.contacts().length > 1">
							{!! link_to('#', trans('texts.remove_contact').' -', array('data-bind'=>'click: $parent.removeContact')) !!}
						</span>
						<span data-bind="visible: $index() === ($parent.contacts().length - 1)" class="pull-right greenlink bold">
							{!! link_to('#', trans('texts.add_contact').' +', array('onclick'=>'return addContact()')) !!}
						</span>
					</div>
				</div>
			</div>
            </div>
            </div>


        <div class="panel panel-default" style="min-height: 500px">
          <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.additional_info') !!}</h3>
          </div>
            <div class="panel-body">

            {!! Former::select('currency_id')->addOption('','')
                ->placeholder($account->currency ? $account->currency->name : '')
                ->fromQuery($currencies, 'name', 'id') !!}
                {!! Former::select('language_id')->addOption('','')
                    ->placeholder($account->language ? trans('texts.lang_'.$account->language->name) : '')
                    ->fromQuery($languages, 'name', 'id') !!}
                {!! Former::select('payment_terms')->addOption('','')
                    ->fromQuery(\App\Models\PaymentTerm::getSelectOptions(), 'name', 'num_days')
                    ->placeholder($account->present()->paymentTerms)
                    ->help(trans('texts.payment_terms_help')) !!}
                {!! Former::select('size_id')->addOption('','')
                    ->fromQuery($sizes, 'name', 'id') !!}
                {!! Former::select('industry_id')->addOption('','')
                    ->fromQuery($industries, 'name', 'id') !!}
                {!! Former::textarea('private_notes') !!}


                @if (Auth::user()->account->isNinjaAccount())
                    @if (isset($planDetails))
                        {!! Former::populateField('plan', $planDetails['plan']) !!}
                        {!! Former::populateField('plan_term', $planDetails['term']) !!}
                        @if (!empty($planDetails['paid']))
                            {!! Former::populateField('plan_paid', $planDetails['paid']->format('Y-m-d')) !!}
                        @endif
                        @if (!empty($planDetails['expires']))
                            {!! Former::populateField('plan_expires', $planDetails['expires']->format('Y-m-d')) !!}
                        @endif
                        @if (!empty($planDetails['started']))
                            {!! Former::populateField('plan_started', $planDetails['started']->format('Y-m-d')) !!}
                        @endif
                    @endif
                    {!! Former::select('plan')
                                ->addOption(trans('texts.plan_free'), PLAN_FREE)
                                ->addOption(trans('texts.plan_pro'), PLAN_PRO)
                                ->addOption(trans('texts.plan_enterprise'), PLAN_ENTERPRISE)!!}
                    {!! Former::select('plan_term')
                                ->addOption()
                                ->addOption(trans('texts.plan_term_yearly'), PLAN_TERM_YEARLY)
                                ->addOption(trans('texts.plan_term_monthly'), PLAN_TERM_MONTHLY)!!}
                    {!! Former::text('plan_started')
                                ->data_date_format('yyyy-mm-dd')
                                ->addGroupClass('plan_start_date')
                                ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
                    {!! Former::text('plan_paid')
                                ->data_date_format('yyyy-mm-dd')
                                ->addGroupClass('plan_paid_date')
                                ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
                    {!! Former::text('plan_expires')
                                ->data_date_format('yyyy-mm-dd')
                                ->addGroupClass('plan_expire_date')
                                ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
                    <script type="text/javascript">
                    $(function () {
                        $('#plan_started, #plan_paid, #plan_expires').datepicker();
                    });
                </script>
                @endif

            </div>
            </div>

		</div>
	</div>


    {!! Former::hidden('data')->data_bind("value: ko.toJSON(model)") !!}
    <script type="text/javascript">

	$(function () {
        $('#country_id').combobox();
    });

    function ContactModel(data) {
        var self = this;
        self.public_id = ko.observable('');
        self.first_name = ko.observable('');
        self.last_name = ko.observable('');
        self.email = ko.observable('');
        self.phone = ko.observable('');
        self.password = ko.observable('');

        if (data) {
            ko.mapping.fromJS(data, {}, this);
        }
    }

    function ClientModel(data) {
        var self = this;

        self.contacts = ko.observableArray();

        self.mapping = {
            'contacts': {
                create: function (options) {
                    return new ContactModel(options.data);
                }
            }
        }

        if (data) {
            ko.mapping.fromJS(data, self.mapping, this);
        } else {
            self.contacts.push(new ContactModel());
        }

        self.placeholderName = ko.computed(function () {
            if (self.contacts().length == 0) return '';
            var contact = self.contacts()[0];
            if (contact.first_name() || contact.last_name()) {
                return contact.first_name() + ' ' + contact.last_name();
            } else {
                return contact.email();
            }
        });
    }

    @if ($data)
        window.model = new ClientModel({!! $data !!});
    @else
        window.model = new ClientModel({!! $client !!});
    @endif

        model.showContact = function (elem) {
        if (elem.nodeType === 1) $(elem).hide().slideDown()
    }
    model.hideContact = function (elem) {
        if (elem.nodeType === 1) $(elem).slideUp(function () {
            $(elem).remove();
        })
    }


    ko.applyBindings(model);

    function addContact() {
        model.contacts.push(new ContactModel());
        return false;
    }

    model.removeContact = function () {
        model.contacts.remove(this);
    }


	</script>
    <center class="buttons">
    	{!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/clients/' . ($client ? $client->public_id : '')))->appendIcon(Icon::create('remove-circle')) !!}
        {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
	</center>

    {!! Former::close() !!}
	</div>
@stop

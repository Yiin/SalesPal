@extends('header')

@section('onReady')
    $('input#name').focus();
@stop

@section('content')

    <ol class="breadcrumb path">
        <li>
            <a class="fa fa-home" href="/"></a>
        </li>
        <li>Clients</li>
        <li class="active">New Client</li>
    </ol>
    {!! Former::open($url)
            ->autocomplete('off')
            ->rules(
                ['email' => 'email']
            )->addClass('recieve-vat warn-on-exit')
            ->method($method) !!}

    <div class="vue-app" id="vueapp_{{ str_random() }}">
        <div class="new-client-panels-wrapper">
            <div class="new-clients-col">
                <div class="new-clients-wrapper">
                    <div class="form-wrapper">
                        <div class="organization-panel-header new-clients-header">
                            Organization
                        </div>
                        <div class="form-panel-body">
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Company Name <input name="name" type="text" value="{{ $client ? $client->name : old('name') }}"><br></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Registration Number <input name="id_number" type="text" value="{{ $client ? $client->id_number : old('id_number') }}"><br></span>
                                </div>
                                <div class="col">
                                    <span>VAT Number <input name="vat_number" type="text" value="{{ $client ? $client->vat_number : old('vat_number') }}"><br></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Website <input name="website" type="text" value="{{ $client ? $client->website : old('website') }}"><br></span>
                                </div>
                                <div class="col">
                                    <span>Phone <input name="work_phone" type="text" value="{{ $client ? $client->work_phone : old('work_phone') }}"><br></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="new-clients-wrapper">
                    <div class="form-wrapper">
                        <div class="address-panel-header new-clients-header">
                            Address
                        </div>
                        <div class="form-panel-body">
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Street <input name="address1" type="text" value="{{ $client ? $client->address1 : old('address1') }}"><br></span>
                                </div>
                                <div class="col">
                                    <span>Apt / Suite <input name="address2" type="text" value="{{ $client ? $client->address2 : old('address2') }}"><br></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>City <input name="city" type="text" value="{{ $client ? $client->city : old('city') }}"><br></span>
                                </div>
                                <div class="col">
                                    <span>Postal Code <input name="postal_code" type="text" value="{{ $client ? $client->postal_code : old('postal_code') }}"><br></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>State / Province <input name="state" type="text" value="{{ $client ? $client->state : old('state') }}"><br></span>
                                </div>
                                <div class="col">
                                    <span>Country <select id="country_id" name="country_id">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="new-clients-wrapper">
                    <contacts-panel
                        @if(isset($client))
                            :contacts="{{ $client->contacts }}"
                        @endif
                    ></contacts-panel>
                </div>
                <div class="new-clients-wrapper">
                    <div class="form-wrapper">
                        <div class="add-panel-header new-clients-header">
                            Additional information
                        </div>
                        <div class="form-panel-body">
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Currency <select name="currency_id">
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ $client && $client->currency_id === $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name }} ({{ $currency->code }})
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Language <select name="language_id">
                                        @foreach($languages as $language)
                                            <option value="{{ $language->id }}" {{ $client && $client->language_id === $language->id ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                                <div class="col">
                                    <span>Payment-Terms <select name="payment_terms">
                                        @foreach($paymentTerms as $paymentTerm)
                                            <option value="{{ $paymentTerm->id }}" {{ $client && $client->payment_terms === $paymentTerm->id ? 'selected' : '' }}>
                                                {{ $paymentTerm->name }}
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Company Size <select name="size_id">
                                        @foreach($sizes as $companySize)
                                            <option value="{{ $companySize->id }}" {{ $client && $client->size_id == $companySize->id ? 'selected' : '' }}>
                                                {{ $companySize->name }}
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                                <div class="col">
                                    <span>Industry <select name="industry">
                                        @foreach($industries as $industry)
                                            <option value="{{ $industry->id }}" {{ $client && $client->industry_id == $industry->id ? 'selected' : '' }}>
                                                {{ $industry->name }}
                                            </option>
                                        @endforeach
                                    </select></span>
                                </div>
                            </div>
                            <div class="flex-grid">
                                <div class="col">
                                    <span>Private Notes 
                                    <textarea name="private_notes">{{ $client ? $client->private_notes : old('private_notes') }}</textarea></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vat-col">
                <client-vat-checker
                    @if(isset($client))
                        :vatChecks="{{ $client->vatChecks()->limit(12)->get() }}"
                    @endif
                ></client-vat-checker>
            </div>
        </div>
    </div>

    <div class="buton-wrapper">
        <button type="submit" class="btn btn-primary complete-buton">
            Save
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-primary complete-buton cancel-buton">
            Cancel
        </a>
    </div>
    {!! Former::close() !!}

@stop

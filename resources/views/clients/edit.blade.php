@extends('header')

@section('onReady')
    $('input#name').focus();
@stop

@section('content')

    <ol class="breadcrumb path">
        <li>
            <a class="fa fa-home" href="/"></a>
        </li>
        <li class="active">Clients / New Client</li>
    </ol>
    {!! Former::open($url)
            ->autocomplete('off')
            ->rules(
                ['email' => 'email']
            )->addClass('recieve-vat warn-on-exit')
            ->method($method) !!}

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
                                <span>State / Province <input name="state" type="text"><br></span>
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
                <div class="form-wrapper">
                    <div class="contacts-panel-header new-clients-header">
                        Contacts
                    </div>
                    <div class="form-panel-body">
                        <div class="flex-grid">
                            <div class="col">
                                <span>First Name <input name="Registration" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Last Name <input name="Website" type="text" value=""><br></span>
                            </div>
                            <div class="col item">
                                <span>Job Position <input name="Website" type="text" value=""><br></span>
                            </div>
                        </div>
                        <div class="flex-grid">
                            <div class="col">
                                <span>Email <input name="Vat" type="text" value=""><br></span>
                            </div>
                            <div class="col">
                                <span>Phone <input name="Phone" type="text" value=""><br></span>
                            </div>
                        </div>
                        <div class="new-client-link">
                            <a href="javascript:addContact()">+ Add New Contact</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="new-clients-wrapper">
                <div class="form-wrapper">
                    <div class="add-panel-header new-clients-header">
                        Additional information
                    </div>
                    <div class="form-panel-body">
                        <div class="flex-grid">
                            <div class="col">
                                <span>Currency <select name="currency">
                                    <option value="euros">
                                        Euros
                                    </option>
                                </select></span>
                            </div>
                        </div>
                        <div class="flex-grid">
                            <div class="col">
                                <span>Language <select name="language">
                                    <option value="language">
                                        Lithuanian
                                    </option>
                                </select></span>
                            </div>
                            <div class="col">
                                <span>Payment-Terms <select name="payment">
                                    <option value="payment">
                                        Net 90
                                    </option>
                                </select></span>
                            </div>
                        </div>
                        <div class="flex-grid">
                            <div class="col">
                                <span>Company Size <select name="company">
                                    <option value="company">
                                        101 - 500
                                    </option>
                                </select></span>
                            </div>
                            <div class="col">
                                <span>Industry <select name="industry">
                                    <option value="industry">
                                        Professional Services &amp; Consulting
                                    </option>
                                </select></span>
                            </div>
                        </div>
                        <div class="flex-grid">
                            <div class="col">
                                <span>Private Notes 
                                <textarea id="input-message" name="message"></textarea></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="vat-col">
            <div class="vat-clients-wrapper">
                <div class="vat-wrapper">
                    <div class="vat-panel-header">
                        Check Your Vat Number
                        <hr>
                    </div>
                    <div class="vat-panel-body">
                        <div class="vat-panel-body-header">
                            <span>Vat Number <input name="Check" type="text" value=""><br></span>
                            <div class="btn-primary btn check-button">
                                Check
                            </div>
                            <hr>
                        </div>
                        <div class="vat-result">
                            <div class="border"></div>
                            <div class="details">
                                <div class="detail">
                                    VAT: <span>IE156548987456123155</span>
                                </div>
                                <div class="detail green-text">
                                    Status: <span>Correct</span>
                                </div>
                                <div class="detail">
                                    Checkedat: <span>Now</span>
                                </div>
                            </div>
                        </div>
                        <div class="vat-result">
                            <div class="border"></div>
                            <div class="details">
                                <div class="detail">
                                    VAT: <span>IE156548987456123155</span>
                                </div>
                                <div class="detail green-text">
                                    Status: <span>Correct</span>
                                </div>
                                <div class="detail">
                                    Checkedat: <span>Now</span>
                                </div>
                            </div>
                        </div>
                        <div class="vat-result">
                            <div class="border"></div>
                            <div class="details">
                                <div class="detail">
                                    VAT: <span>IE156548987456123155</span>
                                </div>
                                <div class="detail green-text">
                                    Status: <span>Correct</span>
                                </div>
                                <div class="detail">
                                    Checkedat: <span>Now</span>
                                </div>
                            </div>
                        </div>
                        <div class="vat-result">
                            <div class="border red-border"></div>
                            <div class="details">
                                <div class="detail">
                                    VAT: <span>IE156548987456123155</span>
                                </div>
                                <div class="detail red-text">
                                    Status: <span>Incorect</span>
                                </div>
                                <div class="detail">
                                    Checkedat: <span>Now</span>
                                </div>
                            </div>
                        </div>
                        <div class="vat-result">
                            <div class="border red-border"></div>
                            <div class="details">
                                <div class="detail">
                                    VAT: <span>IE156548987456123155</span>
                                </div>
                                <div class="detail red-text">
                                    Status: <span>Incorect</span>
                                </div>
                                <div class="detail">
                                    Checkedat: <span>Now</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

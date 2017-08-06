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
                        {{ trans('texts.paid_to_date') }}
                        <span>
                            {{ Utils::formatMoney($client->paid_to_date, $client->getCurrencyId()) }}
                        </span>
                        <hr>
                    </div>
                    <div class="grid-col text-color">
                        {{ trans('texts.outstanding') }}
                        <span>
                            {{ Utils::formatMoney($client->balance, $client->getCurrencyId()) }}
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
                        {{ trans('texts.company') }}
                        <span>
                            {{ $client->getDisplayName() }}
                        </span>
                    <hr>
                    </div>

                    @if ($client->vat_number)
                        <div class="grid-col">
                            {{ trans('texts.vat_number') }}
                            <span>
                                {{ $client->vat_number }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if ($client->website)
                        <div class="grid-col">
                            {{ trans('texts.website') }}
                            <span>
                                {!! Utils::formatWebsite($client->website) !!}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if ($client->work_phone)
                        <div class="grid-col">
                            {{ trans('texts.phone') }}
                            <span>
                                {{ $client->work_phone }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if ($client->client_industry)
                        <div class="grid-col">
                            {{ trans('texts.industry') }}
                            <span>
                                {{ $client->client_industry->name }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if ($client->payment_terms)
                        <div class="grid-col">
                            {{ trans('texts.payment_terms') }}
                            <span>
                                {{ $client->present()->paymentTerms }}
                            </span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        @if ($client->contacts)
            <div class="panel-wrapper">
                <div class="show-wrapper">
                    <div class="show-wrapper-header orange-header">
                        {{ trans('texts.contact') }}  
                    </div>
                    <div class="show-wrapper-body">
                        <div class="grid-col">
                            <span>
                                {{ $client->contacts->first()->getDisplayName() }}
                            </span>
                            <hr>
                        </div>

                        @if ($client->contacts->first()->job_position)
                            <div class="grid-col">
                                Job Postion
                                <span>
                                    {{ $client->contacts->first()->job_position }}
                                </span>
                                <hr>
                            </div>
                        @endif

                        @if ($client->contacts->first()->email)
                            <div class="grid-col">
                                Email
                                <span>
                                    {{ $client->contacts->first()->email }}
                                </span>
                                <hr>
                            </div>
                        @endif

                        @if ($client->contacts->first()->phone)
                            <div class="grid-col">
                                Phone
                                <span>
                                    {{ $client->contacts->first()->phone }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="panel-wrapper">
            <div class="show-wrapper">
                <div class="show-wrapper-header green-color">
                    {{ trans('texts.address') }}  
                </div>
                <div class="show-wrapper-body">

                    @if($client->country)
                        <div class="grid-col">
                            Country
                            <span>
                                {{ $client->country->name }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if($client->state)
                        <div class="grid-col">
                            {{ trans('texts.state') }}
                            <span>
                                {{ $client->state }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if($client->city)
                        <div class="grid-col">
                            {{ trans('texts.city') }}
                            <span>
                                {{ $client->city }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if($client->address1)
                        <div class="grid-col">
                        {{ trans('texts.address1') }}
                        <span>
                            {{ $client->address1 }}
                        </span>
                        <hr>
                    </div>
                    @endif

                    @if($client->address2)
                        <div class="grid-col">
                            {{ trans('texts.address2') }}
                            <span>
                                {{ $client->address2 }}
                            </span>
                            <hr>
                        </div>
                    @endif

                    @if($client->postal_code)
                        <div class="grid-col">
                            {{ trans('texts.postal_code') }}
                            <span>
                                {{ $client->postal_code }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="show-blade-col">
        <div class="vue-app" id="vueapp_{{ str_random() }}">
            <div class="panel-wrapper table-wrapper">
                <div class="nav table-heading">
                    <ul class="nav navbar-nav navbar-left show-blade-navbar">
                        <li class="active"><a href="#invoices" role="tab" data-toggle="tab">{{ trans('texts.invoices') }}</a></li>
                        <li><a href="#payments" role="tab" data-toggle="tab">{{ trans('texts.payments') }}</a></li>
                        <li><a href="#credits" role="tab" data-toggle="tab">{{ trans('texts.credits') }}</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right show-blade-navbar">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Actions <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('clients.edit', ['id' => $client->public_id]) }}">Edit Client</a></li>
                                <li role="separator" class="divider"></li>
                                
                                @foreach ($actionLinks as $action)
                                    @if ($action === DropdownButton::DIVIDER)
                                        <li role="separator" class="divider"></li>
                                    @else
                                        <li><a href="{{ $action['url'] }}">{{ $action['label'] }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="table-body tab-pane active" id="invoices">
                        <entity-table 
                            entity="{{ ENTITY_INVOICE }}"
                            client-id="{{ $clientId ?? $vendorId ?? '' }}"
                            :disable-shadows="true"
                            :disable-state="true"
                        ></entity-table>
                    </div>
                    <div role="tabpanel" class="table-body tab-pane" id="payments">
                        <entity-table 
                            entity="{{ ENTITY_PAYMENT }}"
                            client-id="{{ $clientId ?? $vendorId ?? '' }}"
                            :disable-shadows="true"
                            :disable-state="true"
                        ></entity-table>
                    </div>
                    <div role="tabpanel" class="table-body tab-pane" id="credits">
                        <entity-table 
                            entity="{{ ENTITY_CREDIT }}"
                            client-id="{{ $clientId ?? $vendorId ?? '' }}"
                            :disable-shadows="true"
                            :disable-state="true"
                        ></entity-table>
                    </div>
                </div>
            </div>
           
            <dashboard-activities
                :activities="{{ $activities->items }}"
                :state="{{ json_encode(['first' => $activities->first, 'last' => $activities->last ]) }}"
                :client="{{ $client }}"
            ></dashboard-activities>
        </div>
    </div>
</div>
@stop

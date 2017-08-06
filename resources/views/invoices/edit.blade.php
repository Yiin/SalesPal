@extends('header')

@section('head')
    @parent
@stop

@section('content')
    <div id="invoice_form" style="display: none">
        <div class="invoice-new-wrapper">
            <div class="invoice-new-col-first">
                <div class="panel-wrapper">
                    <div class="show-wrapper">
                        <div class="right-side-nav">
                            <span><a href="#">Add New Client</a></span>
                        </div>
                        <div class="show-wrapper-header blue-header">
                            Client
                        </div>
                        <div class="show-wrapper-body --invoice-client">
                            <div class="client-search-wrapper">
                                <div
                                    ref="input"
                                    @keyup="updateClientSearchInput"
                                    data-placeholder="Type Client Name"
                                    class="clients-list-item --search"
                                ></div>
                            </div>
                            <div class="clients-list">
                                <div class="radio">
                                    <template v-for="(client, index) in list_of_clients">
                                        <hr>
                                        <input v-model="client_id" :value="client.public_id" :id.once="`client_${client.public_id}`" type="radio">
                                        <label class="radio-label" :for.once="`client_${client.public_id}`">@{{ client.name }}</label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-wrapper invoices-form">
                    <div class="invoices-form-first">
                        <div class="show-wrapper">
                            <div class="show-wrapper-header orange-header">
                                Items
                            </div>
                            <div class="show-wrapper-body">
                                <div class="form-panel-body">
            
                                    <div class="form-list form-list__invoice">
                                        <div v-for="(item, index) in items" class="form-list-row">
                                            
                                            <div class="form-list-field">
                                                <div class="remove-item" @click="removeItem(item)"></div>
                                            </div>

                                            <div class="form-list-field --name">
                                                <label>
                                                    Item
                                                </label>

                                                <autocomplete-input
                                                    v-model="item.name"
                                                    @select="setInitialItemDate(item, $event)"
                                                    :data="products"
                                                    :data-structure="{ key: 'product_key' }"
                                                ></autocomplete-input>
                                            </div>

                                            <div class="form-list-field">
                                                <label>
                                                    Description
                                                </label>
                                                <input type="text" class="form-input" v-model="item.notes">
                                            </div>

                                            <div class="form-list-field">
                                                <label>
                                                    Cost
                                                </label>
                                                <div class="form-input-group">
                                                    <div class="form-input-group-addon">@{{ currency_symbol }}</div>
                                                    <input type="text" class="form-input" v-model="item.cost">
                                                </div>
                                            </div>

                                            <div class="form-list-field">
                                                <label>
                                                    Quantity
                                                </label>
                                                <input type="number" class="form-input" v-model="item.qty">
                                            </div>

                                            <div class="form-list-field">
                                                <label>
                                                    Tax Rate
                                                </label>

                                                <autocomplete-input
                                                    v-model="item.tax_rate"
                                                    :data="taxRates"
                                                    :data-structure="{ key: 'name' }"
                                                ></autocomplete-input>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-list-footer">
                                        <div class="value-block">
                                            <label>
                                                Subtotal
                                            </label>
                                            <span class="currency --symbol">
                                                @{{ currency_symbol }}
                                            </span>
                                            <span class="currency --value">
                                                @{{ subtotal }}
                                            </span>
                                        </div>

                                        <div class="value-block">
                                            <label>
                                                Paid to Date
                                            </label>
                                            <span class="currency --symbol">
                                                @{{ currency_symbol }}
                                            </span>
                                            <span class="currency --value">
                                                @{{ paid_to_date }}
                                            </span>
                                        </div>

                                        <div class="value-block">
                                            <label>
                                                Balance Due
                                            </label>
                                            <span class="currency --symbol">
                                                @{{ currency_symbol }}
                                            </span>
                                            <span class="currency --value">
                                                @{{ balance_due }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-wrapper table-wrapper">
                    <div class="nav table-heading">
                        <ul class="nav navbar-nav navbar-left show-blade-navbar">
                            <li v-for="tab in tabs"
                                :class="{ active: tab.active }"
                                @mousedown="selectTab(tab)"
                            >
                                <a href="javascript:">@{{ tab.title }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="show-wrapper-body">
                        <div class="textarea-wrapper">
                            <textarea :placeholder="active_tab.description" class="textarea" v-model="textarea" rows="10"></textarea>
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
                                    <span>
                                        Invoice Date 
                                        <input ref="invoice_date" type="text" class="form-input" v-model="invoice_date">
                                    </span>
                                </div>
                                <div class="col">
                                    <span>
                                        Invoice #
                                        <input type="text" class="form-input" v-model="invoice_number">
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grid margin-fix">
                                <div class="col">
                                    <span>
                                        Invoice Due Date 
                                        <input ref="invoice_due_date" type="text" class="form-input" v-model="invoice_due_date">
                                    </span>
                                </div>
                                <div class="col">
                                    <span>
                                        Po # 
                                        <input type="text" class="form-input" v-model="po_number">
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grid margin-fix">
                                <div class="col invoices-col-partial">
                                    <span>
                                        Partial / Deposit 
                                        <input name="Vat" type="text" class="form-input" v-model="partial">
                                    </span>
                                </div>
                                <div class="col invoices-col-discount">
                                    <span>
                                        Discount 
                                        <input name="Phone" type="text" class="form-input" v-model="discount">
                                    </span>
                                </div>
                                <div class="col invoices-col-percent">
                                    <span><select class="form-select select-without-name" name="percent" v-model="discount_type">
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
        <div class="invoices-buton-wrapper">
            <button class="btn btn-primary complete-buton sent-buton" type="submit">Mark Sent</button>
            <button class="btn btn-primary complete-buton" type="submit">Email Invoice</button> 
            <button class="btn btn-primary complete-buton cancel-buton" type="submit">Save Draft</button>
        </div>
    </div>

<script>
$(window).on('load', initVue.bind(window));

function initVue() {
    let ItemModel = (data = {}) => {
        let { 
            name = '', 
            cost = 0, 
            qty = 1, 
            notes = ''
        } = data;

        return {
            name,
            cost,
            qty,
            notes
        };
    };

    const DiscountType = {
        Percentage: 'percentage',
        Cash: 'cash'
    };

    this.vm = new Vue({
        el: '#invoice_form',

        data: {
            invoice: {!! $invoice !!},
            clients: {!! $clients !!},
            products: {!! $products !!},
            taxRates: {!! $taxRates !!},

            tabs: [
                { title: 'Note to Client', model: 'note_to_client', description: 'You can enter your note here.', active: true },
                { title: 'Terms', model: 'terms', description: 'You can list your terms here.', active: false },
                { title: 'Footer', model: 'footer', description: 'You can enter footer text here.', active: false }
            ],

            clientSearchQuery: '',

            client_id: null,

            items: [
                @if ($invoice && $invoice->invoice_items)
                    @foreach ($invoice->invoice_items as $item)
                        ItemModel({
                            name: '{{ $item->product_key }}',
                            cost: {{ $item->cost }}, 
                            qty: {{ $item->qty }}, 
                            notes: '{{ $item->notes }}'
                        }),
                    @endforeach
                @endif
                ItemModel()
            ],
            note_to_client: null,
            terms: null,
            footer: null,
            invoice_date: null,
            invoice_due_date: null,
            partial: null,
            invoice_number: null,
            po_number: null,
            discount: null,
            discount_type: DiscountType.Percentage
        },

        watch: {
            items: {
                handler: function (after, before) {
                    let emptyInputs = after.filter(item => {
                        return !item.name.length
                            && !item.cost.length
                            && !item.notes.length;
                    });

                    if (!emptyInputs.length) {
                        this.addItem();
                    }

                    after.map(item => {
                        if (item.qty <= 0) {
                            item.qty = 1;
                        }
                        if (item.cost < 0) {
                            item.cost = 0;
                        }
                    })
                },
                deep: true,
                immediate: true
            }
        },

        computed: {
            active_tab() {
                return this.tabs.filter(t => t.active)[0];
            },

            textarea: {
                set: function (val) {
                    this[this.active_tab.model] = val;
                },
                get: function () {
                    return this[this.active_tab.model];
                }
            },

            currency_symbol() {
                const defaultSymbol = '$';

                let client = this.clients.find(client => client.public_id === this.client_id);

                if (!client) {
                    return defaultSymbol;
                }

                if (!client.currency) {
                    return defaultSymbol;
                }

                if (!client.currency.symbol) {
                    return client.currency.code;
                }

                return client.currency.symbol;
            },

            list_of_clients() {
                if (this.clientSearchQuery.length) {
                    return this.clients.filter(client => {
                        return this.clientSearchQuery.toLowerCase().split(' ').filter(term => {
                            return client.name.toLowerCase().indexOf() < 0;
                        }).length === 0;
                    });
                }
                return this.clients;
            },

            invoice_amount() {
                return this.items.filter(item => item.cost)
                    .map(item => item.cost * (item.qty || 1))
                    .map(parseFloat)
                    .reduce((a, b) => a + b, 0);
            },

            subtotal: function () {
                let value = this.invoice_amount;

                value = this.formatCash(value);
                return value;
            },

            paid_to_date: function () {
                let value = this.invoice.amount - this.invoice.balance;

                value = this.formatCash(value);
                return value;
            },

            balance_due: function () {
                let value = this.invoice_amount - (this.invoice.amount - this.invoice.balance);

                value = this.formatCash(value);
                return value;
            }
        },

        methods: {

            selectTab(tab) {                
                this.tabs.forEach(t => {
                    if (t.active) {
                        t.active = false;
                    }
                });
                tab.active = true;
            },


            formatCash(value) {
                return numeral(value).format(value.toString().length > 10 ? '0.00a' : '0,0.00');
            },


            addItem() {
                this.items.push(ItemModel());
            },


            removeItem(itemToRemove) {
                this.items = this.items.filter(item => item !== itemToRemove);

                if (this.items.length === 0) {
                    this.addItem();
                }
            },


            setInitialItemDate(item, $event) {
                item.notes = $event.notes;
                item.cost = parseFloat($event.cost);
            },


            initiateClientSearchInput() {
                this.medium = new Medium({
                    element: this.$refs.input,
                    mode: Medium.inlineMode
                });
            },

            updateClientSearchInput() {
                let value = this.medium.value().replace('&nbsp;', ' ').trim().toLowerCase();

                if (this.clientSearchQuery !== value) {
                    this.clientSearchQuery = value;

                    this.$forceUpdate();
                }
            },

            initiateInvoiceDatePickers() {
                $(this.$refs.invoice_date).datepicker();

                $(this.$refs.invoice_due_date).datepicker();
            }

        },

        mounted() {
            this.initiateClientSearchInput();
            this.initiateInvoiceDatePickers();

            document.getElementById('invoice_form').style.display = 'block';
        }
    });
}
</script>

@stop
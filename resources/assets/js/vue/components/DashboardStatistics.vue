<template>
    <div>
        <div class="dashboard-dropdowns">
            <dropdown ref="currencyDropdown" :default="currency_id" :options="currencies" @change="updateCurrency" width="128px"></dropdown>
            <dropdown ref="intervalDropdown" :default="interval" :options="intervals" @change="updateInterval" width="128px"></dropdown>

            <div ref="daterange" class="daterange">
                <i class="icon-calendar-weekly"></i>&nbsp;
                <span v-if="start_date">{{ time_frame_start }} - {{ time_frame_end }}</span> <b class="caret"></b>
            </div>
        </div>

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
                        {{ total_revenue }}
                    </div>
                    <div class="total-meta">
                        <div class="total-sum">
                            {{ total_revenue_in_time_frame}}
                        </div>
                        <div class="total-time-frame" v-html="time_frame"></div>
                    </div>
                </div>
            </div>
            <div class="total-panel">
                <div class="total-panel-header">
                    Total Expenses
                </div>
                <div class="total-panel-body">
                    <div class="total-value">
                        {{ total_expenses }}
                    </div>
                    <div class="total-meta">
                        <div class="total-sum">
                            {{ total_expenses_in_time_frame }}
                        </div>
                        <div class="total-time-frame" v-html="time_frame"></div>
                    </div>
                </div>
            </div>
            <div class="total-panel">
                <div class="total-panel-header">
                    Total Outstanding
                </div>
                <div class="total-panel-body">
                    <div class="total-value">
                        {{ total_outstanding }}
                    </div>
                    <div class="total-meta">
                        <div class="total-sum">
                            {{ total_outstanding_in_time_frame }}
                        </div>
                        <div class="total-time-frame" v-html="time_frame"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import localforage from 'localforage';

    const Interval = {
        DAY: 'day',
        WEEK: 'week',
        MONTH: 'month'
    };
    export default {
        props: [
            'account',
            'currencies',
            'revenue',
            'expenses',
            'balances'
        ],


        data() {
            return {
                currency_id: Laravel.defaultCurrency,
                currency_symbol: Laravel.defaultCurrencySymbol,
                interval: Interval.DAY,
                intervals: [
                    { value: Interval.DAY, label: 'Day' },
                    { value: Interval.WEEK, label: 'Week' },
                    { value: Interval.MONTH, label: 'Month' }
                ],
                start_date: null,
                end_date: null,

                revenue_in_time_frame: 0,
                expenses_in_time_frame: 0,
                balance_in_time_frame: 0
            };
        },



        computed: {
            time_frame() {
                return 'Last <span class="highlight">30</span> days';
            },

            time_frame_start() {
                return moment(this.start_date).format(Laravel.dateFormat);
            },

            time_frame_end() {
                return moment(this.end_date).format(Laravel.dateFormat);
            },

            total_revenue() {
                let sum = 0;
                let revenue;

                if (this.currency_id === Laravel.defaultCurrency) {
                    revenue = this.revenue.filter(payment => payment.currency_id === this.currency_id || payment.currency_id === null);
                }
                else {
                    revenue = this.revenue.filter(payment => payment.currency_id === this.currency_id);
                }

                revenue.forEach(payment => sum += parseFloat(payment.value));

                return this.currency_symbol + ' ' + sum.toFixed(2);
            },
            total_expenses() {
                let sum = 0;
                let expenses;

                if (this.currency_id === Laravel.defaultCurrency) {
                    expenses = this.expenses.filter(expense => expense.currency_id === this.currency_id || expense.currency_id === null);
                }
                else {
                    expenses = this.expenses.filter(expense => expense.currency_id === this.currency_id);
                }

                expenses.forEach(expense => sum += parseFloat(expense.value));

                return this.currency_symbol + ' ' + sum.toFixed(2);
            },
            total_outstanding() {
                let sum = 0;
                let balances;

                if (this.currency_id === Laravel.defaultCurrency) {
                    balances = this.balances.filter(balance => balance.currency_id === this.currency_id || balance.currency_id === null);
                }
                else {
                    balances = this.balances.filter(balance => balance.currency_id === this.currency_id);
                }

                balances.forEach(balance => sum += parseFloat(balance.value));

                return this.currency_symbol + ' ' + sum.toFixed(2);
            },
            total_revenue_in_time_frame() {
                return this.currency_symbol + ' ' + this.revenue_in_time_frame.toFixed(2);
            },
            total_expenses_in_time_frame() {
                return this.currency_symbol + ' ' + this.expenses_in_time_frame.toFixed(2);
            },
            total_outstanding_in_time_frame() {
                return this.currency_symbol + ' ' + this.balance_in_time_frame.toFixed(2);
            },
        },



        methods: {

            updateCurrency(value, settings = {}) {
                let { force = false } = settings;

                this.updateStatistics().then(() => {
                    this.currency_id = value.value;
                    this.currency_symbol = value.symbol;
                    localforage.setItem('last:dashboard_currency_id', value.value);

                    if (force) {
                        this.$refs.currencyDropdown.setValue(value.value);
                    }
                });
            },

            updateInterval(value, settings = {}) {
                let { force = false, request = true } = settings;

                if (request) {
                    this.updateStatistics().then(() => {
                        this.interval = value.value;
                        localforage.setItem('last:dashboard_interval', value.value);
                    });
                }
                else {
                    this.$refs.intervalDropdown.setValue(value.value);
                    this.interval = value.value;
                }
            },

            updateStatistics() {
                return this.$http.get(`/dashboard_chart_data/${this.interval}/${this.start_date}/${this.end_date}/${this.currency_id}/true`)
                    .then(response => response.data)
                    .then(this.handleStatisticsData);
            },

            handleStatisticsData(data) {
                this.balance_in_time_frame = data.totals.balance;
                this.expenses_in_time_frame = data.totals.expenses;
                this.revenue_in_time_frame = data.totals.revenue;
            },

            initDateRangePicker() {
                let cb = (start, end) => {
                    $('#reportrange span').html(start.format(Laravel.dateFormat) + ' - ' + end.format(Laravel.dateFormat));

                    this.start_date = start.format('YYYY-MM-DD');
                    this.end_date = end.format('YYYY-MM-DD');

                    this.updateStatistics().then(() => {
                        localforage.setItem('last:dashboard_range', `${this.start_date};${this.end_date}`);
                    });
                };

                let init = (start, end) => {
                    $(this.$refs.daterange).daterangepicker({
                        startDate: start,
                        endDate: end,
                        ranges: {
                           'Today': [moment(), moment()],
                           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                           'This Month': [moment().startOf('month'), moment().endOf('month')],
                           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        }
                    }, cb);

                    cb(start, end);
                };

                localforage.getItem('last:dashboard_range')
                    .then(value => {
                        let range = value.split(';');
                        let start = moment(range[0]);
                        let end = moment(range[1]);

                        init(start, end);
                    }).catch(() => {
                        let start = moment().subtract(29, 'days');
                        let end = moment();

                        init(start, end);
                    });
            },

            setLastValues() {
                localforage.getItem('last:dashboard_currency_id').then(value => {
                    if (value) {
                        let currency = this.currencies.filter(currency => currency.value === value);

                        if (currency.length) {
                            currency = currency[0];

                            localforage.getItem('last:dashboard_interval').then(value => {
                                if (value) {
                                    this.updateInterval({ value }, {
                                        force: true,
                                        request: false
                                    });
                                }
                                this.updateCurrency(currency, {
                                    force: true
                                })
                            });
                            return;
                        }
                    }
                    localforage.getItem('last:dashboard_interval').then(value => {
                        if (value) {
                            this.updateInterval({ value }, {
                                force: true
                            });
                        }
                    });
                });
            }

        },

        mounted() {
            this.initDateRangePicker();
            this.setLastValues();
        }
    }
</script>
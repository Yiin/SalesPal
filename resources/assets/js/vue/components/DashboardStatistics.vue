<template>
    <div>
        <div class="dashboard-dropdowns">
            <dropdown class="calculator-show" :default="currency" :options="currencies" @change="updateStatistics" width="158px"></dropdown>
            <dropdown class="calculator-show" :default="currency" :options="currencies" @change="updateStatistics" width="158px"></dropdown>
            <dropdown class="calculator-show" :default="currency" :options="currencies" @change="updateStatistics" width="308px"></dropdown>
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
    export default {
        data() {
            return {
                currency: '$',
                currencies: [
                    { value: '€', label: 'EUR' },
                    { value: '$', label: 'USD' },
                    { value: '£', label: 'GBP' }
                ]
            }
        },
        


        computed: {
            time_frame() {
                return 'Last <span class="highlight">30</span> days';
            },
            total_revenue() {
                return this.currency + ' 0.00';
            },
            total_revenue_in_time_frame() {
                return this.currency + ' 0.00';
            },
            total_expenses() {
                return this.currency + ' 0.00';
            },
            total_expenses_in_time_frame() {
                return this.currency + ' 0.00';
            },
            total_outstanding() {
                return this.currency + ' 0.00';
            },
            total_outstanding_in_time_frame() {
                return this.currency + ' 0.00';
            },
        },



        methods: {

            updateStatistics(value) {
                this.currency = value.value;
            }

        }
    }
</script>
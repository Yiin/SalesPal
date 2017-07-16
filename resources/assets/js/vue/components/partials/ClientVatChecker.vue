<template>
    <div class="vat-clients-wrapper">
        <div class="vat-wrapper">
            <div class="vat-panel-header">
                Check Your Vat Number
                <hr>
            </div>
            <div class="vat-panel-body">
                <div class="vat-panel-body-header">
                    <span>
                        Vat Number <span class="input-error">* Please enter VAT number.</span>
                    </span>
                    <div class="input-row">
                        <input type="text" v-model="vat_number">
                        <div @click="check" class="btn-primary btn check-button">
                            <i v-if="is_loading" class="fa fa-spinner fa-pulse fa-fw"></i>
                            <template v-else>
                                Check
                            </template>
                        </div>
                    </div>
                    <hr>
                </div>
                <div v-for="result in results" class="vat-result">
                    <input v-if="result.just_checked" type="hidden" name="vat_checks[][is_valid]" :value="result.is_valid ? 1 : 0">

                    <div :class="{ 'red-border': !result.is_valid }" class="border"></div>
                    <div class="details">
                        <div class="detail">
                            VAT: <span>{{ result.country_code + result.vat_number }}</span>
                        </div>
                        <div :class="[ result.is_valid ? 'green-text' : 'red-text' ]" class="detail">
                            Status: <span>{{ result.is_valid ? 'Correct' : 'Incorrect' }}</span>
                        </div>
                        <div class="detail">
                            Checked at: <span>{{ result.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'vatChecks'
        ],

        data() {
            return {
                // vat number we want to check
                vat_number: '',

                // are we waiting for result?
                is_loading: false,

                // list of recent recults
                results: this.vatChecks ? this.vatChecks.map(check => {
                    return {
                        is_valid: check.is_valid,
                        country_code: check.country_code,
                        vat_number: check.vat_number,
                        created_at: moment(check.created_at.date).fromNow()
                    };
                }) : []
            };
        },

        methods: {
            check() {
                this.is_loading = true;

                let data = {};

                data.vat = this.vat_number;

                if (this.client) {
                    data.client_id = this.client.id;
                }

                this.$http.post('/vat', data)
                    .then(response => response.data)
                    .then(data => {
                        this.addResult(
                            data.name !== '---', 
                            data.country_code, 
                            data.vat_number, 
                            data.search_date
                        );
                    })
                    .catch(response => {
                        console.log(response.data);
                    })
                    .then(() => this.vat_number = '')
                    .then(() => this.is_loading = false)
                ;
            },

            addResult(is_valid, country_code, vat_number, created_at) {
                this.results.unshift({
                    is_valid,
                    country_code,
                    vat_number,
                    created_at: moment(created_at).fromNow()
                });
            }
        }
    }
</script>
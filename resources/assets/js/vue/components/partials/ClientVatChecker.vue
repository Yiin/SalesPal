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
                        Country
                    </span>
                    <select v-model="country_code" class="form-select">
                        <option v-for="country in countries" :value="country.iso_3166_2">
                            {{ country.name }}
                        </option>
                    </select>

                    <span>
                        Vat Number 
                        <span v-if="error" class="input-error">
                            * Please enter valid VAT number.
                        </span>
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
                <div v-for="(result, index) in results" class="vat-result">
                    <input v-if="!client && result.just_created" type="hidden" :name="`vat_checks[${index}][is_valid]`" :value="result.is_valid ? 1 : 0">
                    <input v-if="!client && result.just_created" type="hidden" :name="`vat_checks[${index}][name]`" :value="result.name">
                    <input v-if="!client && result.just_created" type="hidden" :name="`vat_checks[${index}][address]`" :value="result.address">
                    <input v-if="!client && result.just_created" type="hidden" :name="`vat_checks[${index}][country_code]`" :value="result.country_code">
                    <input v-if="!client && result.just_created" type="hidden" :name="`vat_checks[${index}][vat_number]`" :value="result.vat_number">

                    <div :class="{ 'red-border': !result.is_valid }" class="border"></div>
                    <div class="details">
                        <div class="detail">
                            VAT: <span>{{ result.country_code + result.vat_number }}</span>
                        </div>
                        <div :class="[ result.is_valid ? 'green-text' : 'red-text' ]" class="detail">
                            Status: <span>{{ result.is_valid ? 'Correct' : 'Incorrect' }}</span>
                        </div>
                        <div class="detail">
                            Checked <span>{{ result.just_created ? 'a moment ago' : result.created_at }}</span>
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
            'vatChecks',
            'client',
            'countries'
        ],

        data() {
            return {
                // vat number we want to check
                country_code: '',
                vat_number: '',

                // are we waiting for result?
                is_loading: false,

                // is there error with input?
                error: false,

                // list of recent recults
                results: this.vatChecks ? this.vatChecks.map(check => {
                    return {
                        is_valid: check.is_valid,
                        country_code: check.country_code,
                        vat_number: check.vat_number, 
                        created_at: moment(check.created_at).fromNow()
                    };
                }) : []
            };
        },

        methods: {
            check() {
                this.is_loading = true;
                this.error = false;

                let data = {};

                data.vat = this.country_code + this.vat_number;

                if (this.client) {
                    data.client_id = this.client.public_id;
                }

                this.$http.post('/vat', data)
                    .then(response => response.data)
                    .then(data => {
                        this.addResult(
                            data.name !== '---', 
                            data.name,
                            data.address,
                            data.country_code, 
                            data.vat_number
                        );
                    })
                    .catch(() => {
                        this.error = true;
                    })
                    .then(() => this.vat_number = '')
                    .then(() => this.is_loading = false)
                ;
            },

            addResult(is_valid, name, address, country_code, vat_number, created_at) {
                this.results.unshift({
                    just_created: true,
                    is_valid,
                    name,
                    address,
                    country_code,
                    vat_number,
                    created_at: moment().toISOString()
                });
            }
        }
    }
</script>

<style scoped>
    select {
        margin-bottom: 20px;
    }

    .input-row {
        display: flex;
        justify-content: space-between;
    }

    input {
        margin-right: 20px;
    }

    .check-button {
        margin-top: 5px;
    }
</style>
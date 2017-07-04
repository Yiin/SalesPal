<template>
    <a class="status" :class="{ 
            unknown: current_state === 'unknown',
            invalid: current_state === 'invalid',
            verified: current_state === 'valid',
            loading: current_state === 'loading'
        }"
        @click.prevent="check"
        v-html="action_text"
    ></a>
</template>

<script>
    
    export default {

        props: ['vat', 'state'],

        data() {
            return {
                current_state: this.state
            }
        },

        computed: {

            action_text() {
                switch (this.current_state) {
                    case 'loading':
                        return '<i class="fa fa-spinner fa-pulse fa-fw"></i>';
                    default:
                        return '';
                }
            }

        },

        methods: {

            check() {
                this.current_state = 'loading';

                this.$http.post('/vat', { vat: this.vat, save: true })
                    .then(response => response.data)
                    .then(this.handleResponse)
                    .catch(this.handleError);
            },

            handleResponse(results) {
                if (results.name !== '---') {
                    this.current_state = 'valid';
                }
                else {
                    this.current_state = 'invalid';
                }
            },

            handleError(response) {
                this.current_state = 'invalid';
            }

        }

    }

</script>

<style>

    .status
    {
        font-size: 14px !important;
        font-weight: bold;

        position: absolute;
        top: 15px;
        right: 20px;

        width: 65px;
        height: 22px;
        padding-top: 1px;

        cursor: pointer;
        user-select: none;
        text-align: center;
        text-transform: uppercase;

        color: #fff !important;
        border-radius: 2px;
    }

    .status.loading
    {
        background-color: #000;
    }

    .status.verified
    {
        margin-top: -1px;

        height: 24px;

        background: url(/img/icons/clients/vat-verified.svg) center center no-repeat;
    }

    .status.unknown
    {
        background: url(/img/icons/clients/vat-unknown.svg);
    }

    .status.invalid
    {
        background: url(/img/icons/clients/vat-invalid.svg);
    }


</style>
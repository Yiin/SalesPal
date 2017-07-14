<template>
    <div>
        <a class="status" :class="{ 
                unknown: current_state === 'unknown',
                invalid: current_state === 'invalid',
                verified: current_state === 'valid',
                loading: current_state === 'loading'
            }"
            @click.prevent="check"
            v-html="action_text"
        ></a>
        <div class="status-popup-wrapper">
            <div v-show="status_text.length" class="status-popup">{{ status_text }}</div>
        </div>
    </div>
</template>

<script>
    
    export default {

        props: ['client_id', 'vat', 'state'],

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
            },

            status_text() {
                switch (this.current_state) {
                    case 'unknown':
                        return 'Press to verify this VAT number now';
                    case 'invalid':
                        return 'VAT number has not been verified, it\'s either inactive or invalid';
                    case 'valid':
                        return 'This VAT number has been successfully verified';
                    default:
                        return '';
                }
            }

        },

        methods: {

            check() {
                this.current_state = 'loading';

                this.$http.post('/vat', { vat: this.vat, client_id: this.client_id })
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

        background: url(/img/icons/vat-verified.svg) center center no-repeat;
    }

    .status.unknown
    {
        background: url(/img/icons/vat-unknown.svg);
    }

    .status.invalid
    {
        background: url(/img/icons/vat-invalid.svg);
    }

    .status-popup-wrapper {
        display: none;
    }

    .status:hover + .status-popup-wrapper {
        position: absolute;
        display: block;
        top: -40px;
        left: 0px;
        width: 164%;
        text-align: center;
    }

    .status-popup {
        background: white;
        box-shadow: 0 0 5px #aaaaaa;
        z-index: 1;
        padding: 14px 31px;
        white-space: nowrap;
        font-size: 18px;
        display: inline-block;
        line-height: 1;
        color: #373737;
        border-radius: 4px;
        top: -40px;
    }

    .status-popup::after {
        content: '▼';
        position: absolute;
        color: white;
        text-shadow: 0 2px 3px #aaaaaa;
        top: 39px;
        left: 50%;
        transform: scale(0.6732, 0.3332);
        line-height: 1;
    }


</style>
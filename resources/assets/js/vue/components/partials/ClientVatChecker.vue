<template>
    <div class="vat-clients-wrapper">
        <div class="vat-wrapper">
            <div class="vat-panel-header">
                Check Your Vat Number
                <hr>
            </div>
            <div class="vat-panel-body">
                <div class="vat-panel-body-header">
                    <span>Vat Number <input type="text" value=""><br></span>
                    <div class="btn-primary btn check-button">
                        Check
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

    }
</script>
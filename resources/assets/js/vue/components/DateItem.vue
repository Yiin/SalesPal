<template>
    <div :data-placeholder="option.label" :class="{ placeholder: value_empty }">
        <datepicker 
            @input="changed" 
            v-model="value" 
            :range="true" 
            format="yyyy-MM-dd" 
        ></datepicker>
    </div>
</template>

<script>
import Datepicker from './Datepicker.vue';
    
export default {

    components: {
        Datepicker
    },

    props: [
        'option'
    ],

    data() {
        return {
            value: []
        };
    },

    computed: {
        value_empty() {
            return !this.value || !this.value.length || !this.value[0];
        }
    },

    methods: {

        changed() {
            this.option.value = this.value;

            this.$emit('changed');
        }

    }
}

</script>

<style>
.placeholder:before {
    content: attr(data-placeholder);
    display: block; /* For Firefox */
    text-transform: capitalize;
    position: absolute;
}

.vue-dropdown-option input {
    border: none;
    background: none;
}

.vue-dropdown-option input::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color:    #333;
}
.vue-dropdown-option input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color:    #333;
   opacity:  1;
}
.vue-dropdown-option input::-moz-placeholder { /* Mozilla Firefox 19+ */
   color:    #333;
   opacity:  1;
}
.vue-dropdown-option input:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color:    #333;
}
.vue-dropdown-option input::-ms-input-placeholder { /* Microsoft Edge */
   color:    #333;
}
</style>
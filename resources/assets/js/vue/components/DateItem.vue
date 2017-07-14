<template>
    <div :data-placeholder="option.label" :class="{ placeholder: value_empty }">
        <datepicker 
            @changed="changed" 
            v-model="display" 
            :range="true" 
            :format="format" 
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
            display: [],
            format: Laravel.dateFormat
        };
    },

    computed: {
        value_empty() {
            return !this.value || !this.value.length || !this.value[0];
        }
    },

    methods: {

        changed(value) {
            this.option.value = value;
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
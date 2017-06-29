<template>
    <div class="text-input">
        <div v-if="option && option.value && option.value.length" 
             @click="clear"
             class="clear-input"
        ></div>
        <div ref="input" class="animation" :data-placeholder="option.label"></div>
    </div>
</template>

<script>
import { medium } from '../medium.patched.js';
    
export default {

    props: [
        'option'
    ],

    data() {
        return {
            medium: undefined
        }
    },

    methods: {
        clear() {
            if (this.medium.value().trim().length) {
                this.medium.value('');
                this.option.value = '';
                this.$forceUpdate();
                this.$emit('changed');
            }
        }
    },

    mounted() {
        this.medium = new Medium({
            element: this.$refs.input,
            mode: Medium.inlineMode
        });

        this.$el.addEventListener('keyup', () => {
            if (this.option.value !== this.medium.value().trim()) {
                this.option.value = this.medium.value().trim();
                this.$forceUpdate();
                this.$emit('changed');
            }
        });
    }
}

</script>

<style>
.text-input {
    position: relative;
    padding: 0 !important;
    margin: 0 !important;
}

.clear-input {
    position: absolute;
    background: url(/img/icons/cross.svg) no-repeat;
    background-size: contain;
    width: 12px;
    height: 12px;
    top: 11px;
    right: 15px;
    cursor: pointer;
}

.clear-input:hover {
    opacity: 0.8;
}

[contenteditable=true] {
    font-weight: 600;
    color: #01a8fe;
    padding-top: 6px;
    padding-bottom: 7px;
    padding-left: 26px;
    padding-right: 15px;
}

[contenteditable=true]:empty::before {
    content: attr(data-placeholder);
    text-transform: capitalize;
    font-weight: normal;
    color: #000000;
    background: white;
}

[contenteditable=true]:hover {
    color: black;
    background: #f5f5f5;
}

[contenteditable=true]:hover::before {
    background: none;
}

[contenteditable=true]:focus::before {
    display: none;
}

[contenteditable=true]:focus {
    background: #01a8fe !important;
    color: white !important;
}

</style>
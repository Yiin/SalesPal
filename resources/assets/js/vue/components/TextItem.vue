<template>
    <div class="text-input">
        <div ref="input" 
             @focus="focus"
             @blur="blur"
             class="animation" 
             :data-placeholder="option.label"
        ></div>

        <div v-show="option && option.value && option.value.length"
             @click="clear"
             class="clear-input"
        ></div>
    </div>
</template>

<script>
    
export default {

    props: [
        'option'
    ],

    data() {
        return {
            medium: undefined
        }
    },

    computed: {
        default() {
            if (this.option) {
                switch (this.option.type) {
                    case 'number':
                        return '=';
                }
            }
            return '';
        }
    },

    methods: {
        focus() {
            if (this.option.value.length === 0 && this.default.length) {
                this.medium.value(this.default);
                this.option.value = this.default;
            }
        },

        blur() {
            if (this.option.value === this.default) {
                this.medium.value('');
                this.option.value = '';
            }
        },

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

        this.$refs.input.addEventListener('keyup', () => {
            let value = this.medium.value().trim();

            if (this.option.value !== value) {
                this.option.value = value;

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
    cursor: text;
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

[contenteditable="true"]:focus + .clear-input {
    background-image: url(/img/icons/white-cross.svg);
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
    cursor: text;
}

[contenteditable=true]:hover {
    color: #01a8fe;
    background: #f5f5f5;
}

.text-input:hover [contenteditable=true]::before {
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
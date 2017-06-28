<template>
    <div class="animation" :data-placeholder="option.label">
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

    mounted() {
        this.medium = new Medium({
            element: this.$el,
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
[contenteditable=true]:empty::before {
    content: attr(data-placeholder);
    text-transform: capitalize;
    color: #000000;
}

[contenteditable=true] {
    font-weight: 600;
    color: #01a8fe;
}

[contenteditable=true]:empty::before {
    background: white;
    color: black;
    font-weight: normal;
}

[contenteditable=true]:hover {
    color: black;
    background: #d4d4d4;
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
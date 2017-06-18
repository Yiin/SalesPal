<template>
    <div :data-placeholder="option.label"></div>
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
[contenteditable=true]:empty:before {
    content: attr(data-placeholder);
    display: block; /* For Firefox */
    text-transform: capitalize;
}
</style>
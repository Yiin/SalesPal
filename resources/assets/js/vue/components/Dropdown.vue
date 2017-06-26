<template>
    
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button @click="toggleDropdown" :style="{ 'min-width': width }" type="button">
            {{ selected.label }}
            <span class="caret"></span>
        </button>

        <div :class="{ open: is_open }" :style="{ 'min-width': width }" class="vue-dropdown-menu">

            <template v-for="option in options">

                <div @click="select(option)" :class="{ selected: selected.value === option.value }" class="vue-dropdown-option">

                    <label>{{ option.label }}</label>

                </div>

            </template>

        </div>
    </div>

</template>

<script>

import { mixin as clickaway } from '../mixins/clickaway';

export default {
    mixins: [
        clickaway
    ],

    props: ['default', 'options', 'width'],

    data() {
        return {
            is_open: false,
            selected: {
                label: '',
                value: null
            },
        };
    },

    methods: {

        toggleDropdown() {

            this.is_open = !this.is_open;

        },


        select (option) {

            this.selected = option;
            this.$emit('change', option);
            this.clickAway();

        },


        clickAway() {
            this.is_open = false;
        },

    },

    mounted() {
        this.selected = this.options.filter(option => option.name === this.default || option.value === this.default)[0];
    }

}

</script>

<style scoped>
    .caret {
        float: right;
        margin-top: 8px;
    }

    .vue-dropdown {
        position: relative;
        display: inline-block;
        margin: 0 15px;
    }

    .vue-dropdown > button {
        background:  white;
        border: none;
        box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.08), 1px 2px 5px rgba(0, 0, 0, 0.08), 0px 3px 5px rgba(0, 0, 0, 0.08);
        height: 46px;
        text-align: left;
        padding: 0 15px;
        font-size: 16px;
        color: #373737;
        border-radius: 4px;
    }

    .vue-dropdown-menu {
        background: #FFFFFF;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        display: none;
        margin: 0;
        padding: 0;
        position: absolute;
        z-index: 999999;

    }

    .vue-dropdown-menu.open {
        display: block;
        position: absolute;
        left: 0;
        padding-bottom: 23px;
        padding-top: 18px;
    }

    .vue-dropdown-menu.open label {
        font-weight: 400;
    }

    .vue-dropdown-menu .vue-dropdown-option, .vue-dropdown-menu .vue-dropdown-option > a {
        font-size: 16px;
        cursor: default;
        user-select: none;
    }

    .vue-dropdown-menu .vue-dropdown-option {
        padding-bottom: 4px;
        padding-right: 10px;
        padding-left: 25px;
        text-align: left;
        padding-top: 4px;
    }

    .vue-dropdown-menu .vue-dropdown-option:not(.separator):hover {
        background-color: #eee;
    }
</style>
<template>
    
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button :class="{ open: is_open }" @click="toggleDropdown" :style="{ 'min-width': width }" type="button">
            {{ selected.label }}
            <span :class="{ active: is_open }" class="caret"></span>
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
            }
        };
    },

    methods: {

        toggleDropdown() {

            this.is_open = !this.is_open;

        },


        setValue(value) {

            let option = this.options.filter(option => option.value === value);

            if (option.length) {
                this.selected = option[0];
            }

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
        let defaultOption = this.options.filter(option => option.name === this.default || option.value === this.default);
        this.selected = defaultOption.length ? defaultOption[0] : this.options.length ? this.options[0] : null;
    }

}

</script>

<style scoped>

    .vue-dropdown
    {
        position: relative;

        display: inline-block;
        vertical-align: top;

        margin: 0 15px;
    }

    .vue-dropdown > button
    {
        font-size: 16px;

        height: 44px;
        margin-top: -3px;
        padding: 11px 15px;

        text-align: left;

        color: #373737;
        border: none;
        border-radius: 2px;
        background: white;
        box-shadow: -3px 2px rgba(0, 0, 0, .05), 3px 2px 5px rgba(0, 0, 0, .05), 0 5px 5px rgba(0, 0, 0, .05);
    }

    .vue-dropdown > button.open
    {
        border-radius: 2px 2px 0 0;
    }

    .vue-dropdown-menu
    {
        position: absolute;
        z-index: 999999;

        display: none;

        margin: 0;
        padding: 0;

        background: #fff;
        box-shadow: -3px 2px rgba(0, 0, 0, .05), 3px 2px 5px rgba(0, 0, 0, .05), 0 5px 5px rgba(0, 0, 0, .05);
    }

    .vue-dropdown-menu.open
    {
        position: absolute;
        left: 0;

        display: block;

        padding-top: 17px;
        padding-bottom: 17px;

        border-top: 1px solid #ebebeb;
        border-radius: 0 0 2px 2px;
    }

    .vue-dropdown-menu.open label
    {
        font-weight: 400;
    }

    .vue-dropdown-menu .vue-dropdown-option,
    .vue-dropdown-menu .vue-dropdown-option > a
    {
        font-size: 16px;

        cursor: default;
        user-select: none;
    }

    .vue-dropdown-menu .vue-dropdown-option
    {
        margin-bottom: 0;
        padding-top: 6px;
        padding-right: 10px;
        padding-bottom: 7px;
        padding-left: 26px;

        text-align: left;
    }

    .vue-dropdown-menu .vue-dropdown-option:not(.separator):hover
    {
        background-color: #f5f5f5;
    }

</style>
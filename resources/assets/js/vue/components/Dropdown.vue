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
    .vue-dropdown {
        position: relative;
        display: inline-block;
        margin: 0 15px;
    }

    .vue-dropdown > button {
        background:  white;
        border: none;
        box-shadow: -3px 2px rgba(0, 0, 0, 0.05), 3px 2px 5px rgba(0, 0, 0, 0.05), 0px 5px 5px rgba(0, 0, 0, 0.05);
        height: 44px;
        text-align: left;
        padding: 11px 15px;
        font-size: 16px;
        color: #373737;
        border-radius: 2px;
        margin-top: -3px;
    }

    .vue-dropdown > button.open {
        border-radius: 2px 2px 0 0;
    }

    .vue-dropdown-menu {
        background: #FFFFFF;
        box-shadow: -3px 2px rgba(0, 0, 0, 0.05), 3px 2px 5px rgba(0, 0, 0, 0.05), 0px 5px 5px rgba(0, 0, 0, 0.05);
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
        padding-bottom: 17px;
        padding-top: 17px;
        border-top: 1px solid #ebebeb;
        border-radius: 0 0 2px 2px;
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
        padding-bottom: 7px;
        padding-right: 10px;
        padding-left: 26px;
        text-align: left;
        padding-top: 6px;
        margin-bottom: 0px;
    }

    .vue-dropdown-menu .vue-dropdown-option:not(.separator):hover {
        background-color: #eee;
    }
</style>
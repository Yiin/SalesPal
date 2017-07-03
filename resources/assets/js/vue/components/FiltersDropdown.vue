<template>
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button :class="{ open: is_open }" @click="toggleDropdown" type="button">
            {{ title }}
            <span :class="{ active: is_open }" class="caret"></span>
        </button>

        <div :class="{ open: is_open }" class="vue-dropdown-menu">

            <div @click="toggleAll" :class="{ checked: selected_all }" class="vue-dropdown-option --checkbox">
                <div class="checkbox-holder"></div>
                <div class="option-label">Show All</div>
            </div>

            <hr class="separator"/>

            <template v-for="option in options">
                <template v-if="option.type === 'separator'">

                    <hr class="separator"></hr>

                </template>
                <template v-else>

                    <template v-if="option.type === 'dropdown'">

                        <!-- 
                            Dropdown
                         -->
                        <div @mouseover="openChildDropdown(option)" @mouseleave="closeChildDropdown" class="vue-dropdown-option --dropdown">
                            <div class="option-label">{{ option.label }}</div>
                            <child-dropdown-menu 
                                :parent="option" 
                                :class="{ open: openedDropdown === option }"
                            ></child-dropdown-menu>
                        </div>

                    </template>
                    <template v-else>
                        <div @click="toggle(option)" :class="{ checked: option.selected }" class="vue-dropdown-option --checkbox">

                            <div class="checkbox-holder"></div>
                            <div class="option-label">{{ option.label }}</div>

                        </div>
                    </template>

                </template>
            </template>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from '../mixins/clickaway';
import { medium } from '../medium.patched.js';

export default {
    mixins: [
        clickaway
    ],

    props: {
        title: {
            default: 'Select to show',
            type: String
        },
        options: {
            default: [],
            type: Array
        }
    },



    data() {
        return {
            openedDropdown: null,
            is_open: false,
            childDropdownTimeout: null,
            selected_all: true
        }
    },



    methods: {

        checkboxes() {
            return _.flatten(...this.options.filter(option => option.type === 'dropdown').map(option => option.options))
                .concat(this.options.filter(option => option.type === 'checkbox'));
        },

        toggleAll() {
            this.selected_all = !this.selected_all;

            if (this.selected_all) {
                this.checkboxes().forEach(option => {
                    option.selected = false;
                });
            }

            this.$forceUpdate();
        },



        toggle(option) {
            this.$set(option, 'selected', !option.selected);

            this.selected_all = this.checkboxes().filter(option => option.selected) === 0;

            this.$forceUpdate();
        },



        toggleDropdown() {
            this.openedDropdown = null;

            this.is_open = !this.is_open;
        },



        openChildDropdown(option) {
            if (this.childDropdownTimeout) {
                clearTimeout(this.childDropdownTimeout.timeout);
                this.childDropdownTimeout = null;
            }
            this.openedDropdown = option;
        },



        closeChildDropdown() {
            this.childDropdownTimeout = {
                label: this.openedDropdown.label,
                timeout: setTimeout(() => {
                    // this.openedDropdown = null;  
                }, 300)
            };
        },


        clickAway(ev) {
            this.openedDropdown = null;
            this.is_open = false;
        },
    }
}
</script>


<style scoped>

    .vue-dropdown
    {
        position: relative;

        display: inline-block;

        margin: 0 15px;
    }

    .vue-dropdown > button
    {
        font-size: 16px;

        width: 268px;
        height: 44px;
        margin-right: 15px;
        margin-left: -3px;
        padding: 0 15px;

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

        width: 268px;
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

        margin-left: -3px;
        padding-top: 17px;
        padding-bottom: 18px;

        border-top: 1px solid #ebebeb;
        border-radius: 0 0 2px 2px;
    }
</style>
<style>
    .vue-dropdown-menu.open .option-label
    {
        font-size: 16px;
        font-weight: 400;

        display: inline-block;
        overflow: hidden;

        max-width: 160px;
        margin-top: 6px;

        vertical-align: top;
        white-space: nowrap;
        text-overflow: ellipsis;

        border-radius: 2px;
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
        height: 34px;
        padding-right: 10px;
        padding-left: 30px;
    }

    .vue-dropdown-menu .vue-dropdown-option.--dropdown .vue-dropdown-menu
    {
        width: 268px;
        max-height: 387px;

        transform: translate(271px, -34px);

        border-radius: 0 2px 2px 2px;
    }

    .vue-dropdown-menu .vue-dropdown-option.--dropdown .vue-dropdown-option
    {
        padding-left: 26px;
    }

    .vue-dropdown-menu .vue-dropdown-option.separator
    {
        width: 85%;
        margin: 5px auto;
        padding: 0;

        border-bottom: 1px solid #ebebeb;
    }

    .vue-dropdown-option:not(.separator):hover
    {
        background-color: #f5f5f5;
    }

    .vue-dropdown-option.--checkbox
    {
        font-size: 0;

        position: relative;
    }

    .vue-dropdown-option.--checkbox > .option-label
    {
        margin-left: 18px;
    }

    .vue-dropdown-option.--checkbox > .checkbox-holder
    {
        position: relative;

        display: inline-block;

        width: 22px;
        height: 22px;
        margin-top: 6px;
    }

    .vue-dropdown-option.--checkbox > .checkbox-holder::before
    {
        position: absolute;
        top: 0;
        left: 0;

        width: 22px;
        height: 22px;

        content: '';

        border: 2px solid #373737;
        border-radius: 0;
        background: #fff;
    }

    .vue-dropdown-option.--checkbox.checked > .checkbox-holder::after
    {
        position: absolute;
        top: 6px;
        left: 6px;

        width: 10px;
        height: 10px;

        content: '';

        background: #01a8fe;
    }

</style>
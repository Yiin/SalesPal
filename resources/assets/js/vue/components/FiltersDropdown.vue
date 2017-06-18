<template>
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button @click="toggleDropdown" type="button">
            {{ title }}
        </button>

        <div :class="{ open: is_open }" class="vue-dropdown-menu">

            <div @click="toggleAll" class="vue-dropdown-option">
                <label>Toggle All</label>
            </div>

            <hr class="separator"/>

            <template v-for="option in options">
                <template v-if="option.type === 'separator'">

                    <hr class="separator"></hr>

                </template>
                <template v-else>

                    <template v-if="option.type === 'dropdown'">

                        <div @mouseover="openChildDropdown(option)" @mouseleave="closeChildDropdown" class="vue-dropdown-option --dropdown">
                            <label>{{ option.label }}</label>
                            <div :class="{ open: openedDropdown === option }" class="vue-dropdown-menu">
                                
                                <template v-for="_option in option.options">
                                    <template v-if="_option.type === 'separator'">

                                        <hr class="separator"></hr>

                                    </template>
                                    <template v-else>

                                        <div @click="toggle(_option)" :class="{ checked: _option.selected }" class="vue-dropdown-option --checkbox">
                                            <label>{{ _option.label }}</label>
                                        </div>

                                    </template>
                                </template>
                            </div>
                        </div>

                    </template>
                    <template v-else>

                        <div @click="toggle(option)" :class="{ checked: option.selected }" class="vue-dropdown-option --checkbox">

                            <label>{{ option.label }}</label>

                        </div>
                    </template>

                </template>
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
            childDropdownTimeout: null
        }
    },



    computed: {
        checkboxes() {
            return this.options.filter(option => option.type === 'checkbox');
        },


        option_all() {
            let selected = this.checkboxes.filter(option => option.selected).length;
            return selected === this.checkboxes.length;
        }
    },



    methods: {
        toggleAll() {
            let selected = !!this.checkboxes.filter(option => !option.selected).length;
            this.checkboxes.forEach(option => {
                option.selected = selected;
            });
            this.$forceUpdate();
        },



        toggle(option) {
            this.$set(option, 'selected', !option.selected);
            this.$forceUpdate();
        },



        toggleDropdown() {
            this.openedDropdown = null;

            this.is_open = !this.is_open;
        },



        openChildDropdown(option) {
            if (this.childDropdownTimeout && this.childDropdownTimeout.label === option.label) {
                clearTimeout(this.childDropdownTimeout.timeout);
                this.childDropdownTimeout = null;
            }
            this.openedDropdown = option;
        },



        closeChildDropdown() {
            this.childDropdownTimeout = {
                label: this.openedDropdown.label,
                timeout: setTimeout(() => {
                    this.openedDropdown = null;  
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
    .vue-dropdown {
        position: relative;
        display: inline-block;
        margin: 0 15px;
    }

    .vue-dropdown > button {
        width: 200px;
        background:  white;
        border: none;
        box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.08), 1px 2px 5px rgba(0, 0, 0, 0.08), 0px 3px 5px rgba(0, 0, 0, 0.08);
        height: 42px;
        text-align: left;
        padding: 0 15px;
        font-size: 16px;
        color: #373737;
    }

    .vue-dropdown-menu {
        background: #FAFAFA;
        border: 1px solid #BDBDBD;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        display: none;
        margin: 0;
        padding: 0;
        position: absolute;
        width: 250px;
        z-index: 999999;
    }

    .vue-dropdown-menu.open {
        display: block;
        position: absolute;
        left: 0;
    }

    .vue-dropdown-menu .vue-dropdown-menu.open {
        transform: translate(245px, -30px);
        width: 270px;
        
        background: #ffffff;
        border: 1px solid #eee;

        max-height: 300px;
        overflow-y: auto;
    }

    .vue-dropdown-menu .vue-dropdown-option, .vue-dropdown-menu .vue-dropdown-option > a {
        font-size: 16px;
        cursor: default;
        user-select: none;
    }

    .vue-dropdown-menu .vue-dropdown-option {
        padding: 10px 20px;
    }

    .vue-dropdown-option.--checkbox::before {
        content: " ";
        display: block;
        position: absolute;
        width: 20px;
        height: 20px;
        border: 1px solid black;
        vertical-align: middle;
        margin-left: -30px;
    }

    .vue-dropdown-option.--checkbox {
        padding-left: 45px;
    }

    .vue-dropdown-option.--checkbox.checked::before {
        background: #000000;
    }

    .vue-dropdown-menu .vue-dropdown-option.separator {
        padding: 0;
        border-bottom: 1px solid #e0e0e0;
        width: 85%;
        margin: 5px auto;
    }

    .vue-dropdown-menu .vue-dropdown-option:not(.separator):hover {
        background-color: #eee;
    }
</style>
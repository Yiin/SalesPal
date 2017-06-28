<template>
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button @click="toggleDropdown" type="button">
            {{ title }}
            <span :class="{ active: is_open }" class="caret"></span>
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

                        <!-- 
                            Dropdown
                         -->
                        <div @mouseover="openChildDropdown(option)" @mouseleave="closeChildDropdown" class="vue-dropdown-option --dropdown">
                            <label>{{ option.label }}</label>
                            <div :class="{ open: openedDropdown === option }" class="vue-dropdown-menu">

                                <!-- 
                                    Search
                                 -->
                                <input v-if="option.options.length > 6" v-model="searchQuery" placeholder="Type In or Select From List" type="text" class="vue-dropdown-option --search">

                                <!-- 
                                    Dropdown Items
                                 -->
                                <template v-for="_option in option.options">
                                    <template v-if="_option.type === 'separator'">

                                        <hr class="separator"></hr>

                                    </template>
                                    <template v-else>

                                        <div v-if="!searchFor || _option.label.toLowerCase().indexOf(searchFor) !== -1" @click="toggle(_option)" :class="{ checked: _option.selected }" class="vue-dropdown-option --checkbox">
                                            <input type="checkbox" name="asdasd[]" class="" value="1">
                                            <label class="checkinthebox"></label>
                                            <label>{{ _option.label }}</label>
                                        </div>

                                    </template>
                                </template>
                            </div>
                        </div>

                    </template>
                    <template v-else>
                        <div @click="toggle(option)" :class="{ checked: option.selected }" class="vue-dropdown-option">
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
            childDropdownTimeout: null,
            searchQuery: ''
        }
    },



    computed: {
        checkboxes() {
            return this.options.filter(option => option.type === 'checkbox');
        },


        option_all() {
            let selected = this.checkboxes.filter(option => option.selected).length;
            return selected === this.checkboxes.length;
        },


        searchFor() {
            return this.searchQuery.trim().toLowerCase();
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
    .caret.active {
        border-bottom: 4px solid;
        border-top: 4px solid transparent;
        border-left: 4px solid transparent;
        margin-top: -4px;
    }

    .caret {
        margin-left: 118px;
        margin-top: -2px;
    }

    .vue-dropdown {
        position: relative;
        display: inline-block;
        margin: 0 15px;
    }

    .vue-dropdown > button {
        background:  white;
        border: none;
        box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.05), 1px 2px 5px rgba(0, 0, 0, 0.05), 0px 3px 5px rgba(0, 0, 0, 0.05);
        width: 268px;
        height: 44px;
        text-align: left;
        padding: 0 15px;
        font-size: 16px;
        color: #373737;
        border-radius: 2px;
        margin-right: 15px;
        margin-left: -3px;
    }

    .vue-dropdown-menu {
        background: #FFFFFF;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.05),0 3px 1px -2px rgba(0,0,0,0.05),0 1px 5px 0 rgba(0,0,0,0.05);
        display: none;
        margin: 0;
        padding: 0;
        position: absolute;
        width: 268px;
        z-index: 999999;
    }

    .vue-dropdown-menu.open {
        display: block;
        position: absolute;
        left: 0;
        padding-bottom: 18px;
        padding-top: 17px;
        margin-left: -3px;

    }

    .vue-dropdown-menu.open label {
        font-weight: 400;
        border-radius: 2px;
        margin-bottom: 0px;
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
        padding-bottom: 7px;
        padding-right: 10px;
        text-align: left;
        padding-top: 6px;
        padding-left: 26px;
    }
    

    .vue-dropdown-option.--search {
        width: 90%;
        margin: 0 auto 15px auto;
        display: block;
        padding-left: 0;
        padding-right: 0;
        border-bottom: 1px solid #e2e2e2;
    }

    .vue-dropdown-option.--search:hover {
        background: white;
    }

    .vue-dropdown-option.--checkbox::before {
        content: " ";
        display: block;
        position: absolute;
        width: 20px;
        height: 20px;
        border: 2px solid black;
        vertical-align: middle;
        margin-left: -30px;
    }

    .vue-dropdown-option.--checkbox label::before {     
        content: '';
        top: 6px;
        left: 6px;
        margin: 0px;
        background: #01a8fe;
        width: 10px;
        height: 10px;
    }

    .vue-dropdown-option.--checkbox {
            padding-left: 56px;
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

    .checkinthebox > [type="checkbox"]:not(:checked) + label, .checkinthebox > [type="checkbox"]:checked + label {
    font-weight: 300;
    }

    .checkinthebox > [type="checkbox"]:not(:checked) + label::before, .checkinthebox > [type="checkbox"]:checked + label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        margin-top: 0;
        width: 22px;
        height: 22px;
    }

    .checkinthebox > [type="checkbox"]:checked + label::after {
        content: '';
        top: 6px;
        left: 6px;
        margin: 0px;
        background: #01a8fe;
        width: 10px;
        height: 10px;
    }

    .checkinthebox > [type="checkbox"]:checked + label::after {
        transform: scale(1);
    }

    .vue-dropdown-option::hover {
        background-color: #f5f5f5;
    }
</style>
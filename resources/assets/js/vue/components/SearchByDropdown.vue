<template>
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button @click="toggleDropdown" type="button">
            {{ title }}
            <span class="caret"></span>
        </button>

        <div :class="{ open: is_open }" class="vue-dropdown-menu">

            <template v-for="option in options">
                <template v-if="option.type === 'separator'">

                    <hr class="separator"></hr>

                </template>
                <template v-else-if="option.type === 'date'">

                    <date-item :option="option" @changed="changed" class="vue-dropdown-option"></date-item>

                </template>
                <template v-else>

                    <text-item :option="option" @changed="changed" class="vue-dropdown-option"></text-item>

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
            default: 'Search by',
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


    methods: {
        changed() {
            this.$emit('changed');
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
    },

    mounted() {
        this.options.forEach(option => {
            option.value = '';
        });
    }
}
</script>


<style scoped>
    .caret {
        margin-left: 155px;
    }

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
        height: 47px;
        text-align: left;
        padding: 0 15px;
        font-size: 16px;
        color: #373737;
        width: 270px;
        border-radius: 4px;
        margin-left: -5px;
    }

    .vue-dropdown-menu {
        background: #FFFFFF;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        display: none;
        margin: 0;
        padding: 0;
        position: absolute;
        width: 270px;
        z-index: 999999;
        margin-left: -5px;
        padding-bottom: 23px;
        padding-top: 18px;
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
        padding-top: 7px;
        padding-bottom: 6px;
        padding-left: 25px;
        padding-right: 15px;
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
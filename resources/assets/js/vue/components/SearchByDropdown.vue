<template>
    <div class="vue-dropdown" v-on-clickaway="clickAway">
        <button :class="{ open: is_open }" @click="toggleDropdown" type="button">
            {{ title }}
            <span :class="{ active: is_open }" class="caret"></span>
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

                    <text-item :option="option" @changed="changed" class="vue-dropdown-option --text"></text-item>

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
    .vue-dropdown {
        position: relative;
        display: inline-block;
        margin: 0 15px 0 0;
    }

    .vue-dropdown > button {
        background:  white;
        border: none;
        box-shadow: -3px 2px rgba(0, 0, 0, 0.05), 3px 2px 5px rgba(0, 0, 0, 0.05), 0px 5px 5px rgba(0, 0, 0, 0.05);
        width: 268px;
        height: 44px;
        text-align: left;
        padding: 0 15px;
        font-size: 16px;
        color: #373737;
        border-radius: 2px;
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
        width: 268px;
        z-index: 999999;
        padding-bottom: 17px;
        padding-top: 17px;
        border-radius: 2;
    }

    .vue-dropdown-menu.open {
        display: block;
        position: absolute;
        left: 0;
        border-top: 1px solid #ebebeb;
        border-radius: 0 0 2px 2px;
    }

    .vue-dropdown-menu.open label {
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
        padding-top: 6px;
        padding-bottom: 7px;
        padding-left: 26px;
        padding-right: 15px;
    }
    .vue-dropdown-menu .vue-dropdown-option.--text {
        cursor: text;
    }

    .vue-dropdown-menu .vue-dropdown-option.separator {
        padding: 0;
        border-bottom: 1px solid #e0e0e0;
        width: 85%;
        margin: 5px auto;
    }

    .vue-dropdown-option:hover {
        background-color: #f5f5f5;
    }

    .vue-dropdown-option:hover [contenteditable=true]:empty::before {
        background-color: #f5f5f5;
    }

</style>
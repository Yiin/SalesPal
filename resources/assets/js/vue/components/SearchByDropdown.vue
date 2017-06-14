<template>
    <div class="vue-dropdown">
        <button @click="toggleDropdown" class="btn btn-primary" type="button">
            {{ title }}
        </button>

        <div class="vue-dropdown-menu">

            <div class="vue-dropdown-option">
                <input :checked="option_all" type="checkbox">
                <label>All</label>
            </div>

            <hr class="separator"/>

            <template v-for="option in options">
                <template v-if="option.type === 'separator'">

                    <div class="separator"></div>

                </template>
                <template v-else>

                    <div class="vue-dropdown-option">
                        <template v-if="option.type === 'checkbox'">
                            <input :checked="option.value" type="checkbox">
                            <label>{{ option.label }}</label>
                        </template>
                        <template v-else-if="option.type === 'text'">
                            <input v-model="option.value" :placeholder="option.label" type="text">
                        </template>
                        <template v-else-if="option.type === 'number'">
                            <input v-model="option.value" :placeholder="option.label" type="number">
                        </template>
                        <template v-else>
                            <label>{{ option.label }}</label>
                        </template>
                    </div>

                </template>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
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
                option_all: false
            }
        },

        watch: {
            option_all: function (val, oldVal) {
                if (val) {
                    this.options.filter(option => option.selected).forEach(option => option.selected = false);
                }
            }
        },

        mathods: {
            toggle () {
                this.option_all = this.options.filter(option => option.selected) === 0;
            }
        }
    }
</script>


<style scoped>
    .vue-dropdown-menu {
        background: #FAFAFA;
        border: 1px solid #BDBDBD;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        /*display: none;*/
        list-style: none;
        margin: 0;
        padding: 0;
        position: absolute;
        width: 250px;
        z-index: 999999;
    }

    .vue-dropdown-menu li, .vue-dropdown-menu li > a {
        font-size: 16px;
        cursor: default;
        user-select: none;
    }

    .vue-dropdown-menu li {
        padding: 10px 20px;
    }

    .vue-dropdown-menu li.separator {
        padding: 0;
        border-bottom: 1px solid #e0e0e0;
        width: 85%;
        margin: 5px auto;
    }

    .vue-dropdown-menu li:not(.separator):hover {
        background-color: #eee;
    }
</style>
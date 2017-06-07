<template>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle devel-dropdown-button" type="button" data-toggle="dropdown">
            <p class="devel-dropdown-text">{{ title }}</p>
            <span class="caret caret-dev-top"></span>
        </button>

        <ul class="dropdown-menu devel-dropdown-menu">

            <li class="custom-checkbox custom-checkbox-dropdown-menu">
                <input :checked="option_all" type="checkbox">
                <label>All</label>
            </li>

            <hr class="separator"/>

            <template v-for="option in options">
                <template v-if="option.name === 'separator'">

                    <hr class="separator">

                </template>
                <template v-else>

                    <li class="custom-checkbox custom-checkbox-dropdown-menu">
                        <template v-if="option.type === 'checkbox'">
                            <input :checked="option.value" type="checkbox">
                            <label>{{ option.name }}</label>
                        </template>
                        <template v-else-if="option.type === 'text'">
                            <input v-model="option.value" type="text">
                            <label>{{ option.name }}</label>
                        </template>
                        <template v-else-if="option.type === 'number'">
                            <input v-model="option.value" type="number">
                            <label>{{ option.name }}</label>
                        </template>
                        <template v-else>
                            <label>{{ option.name }}</label>
                        </template>
                    </li>

                </template>
            </template>
        </ul>
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
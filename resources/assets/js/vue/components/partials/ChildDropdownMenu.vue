<template>
    <div class="vue-dropdown-menu">

        <!-- 
            Search
         -->
        <div 
            ref="input"
            data-placeholder="Type In or Select From List"
            class="vue-dropdown-option --search"
        ></div>
        <div v-show="query && query.length" 
             @click="clear"
             class="clear-input"
        ></div>

        <!-- 
            Dropdown Items
         -->
        <div :class="{ scroll: option.options >= 10 }" class="vue-dropdown-items">
            <template v-for="option in parent.options">
                <template v-if="option.type === 'separator'">

                    <hr class="separator"></hr>

                </template>
                <template v-else>

                    <div
                        v-if="shouldBeVisible(option)" 
                        
                        @click="toggle(option)"

                        :title="option.label"
                        :class="{ 
                           checked: option.selected
                        }"

                        class="vue-dropdown-option --checkbox"
                    >

                        <div class="checkbox-holder"></div>
                        <div class="option-label">
                            {{ option.label }}
                        </div>

                    </div>

                </template>
            </template>
        </div>
    </div>
</template>

<script>

    export default {

        props: [
            'parent'
        ],

        data() {
            return {
                medium: null,
                query: ''
            }
        },

        methods: {

            // option should be visible only
            shouldBeVisible(option) {
                // if we're not searching for anything
                if (! this.query) {
                    return true;
                }
                // or this option matches search criterias
                if (option.label.toLowerCase().indexOf(this.query) !== -1) {
                    return true;
                }
                // else ignore it completely
                return false;
            },



            toggle(option) {
                this.$emit('toggle', option);
            },



            clear() {
                this.medium.value('');
                this.query = '';
            }

        },

        mounted() {
            this.medium = new Medium({
                element: this.$refs.input,
                mode: Medium.inlineMode
            });

            this.$refs.input.addEventListener('keyup', () => {
                let value = this.medium.value().trim().toLowerCase();

                if (this.query !== value) {
                    this.query = value;

                    this.$forceUpdate();
                }
            });
        }
    }

</script>

<style>

    .vue-dropdown-items
    {
        overflow-y: auto;

        max-height: 300px;
    }

    .vue-dropdown-items.scroll {
        margin-right: 26px;
    }

    .vue-dropdown-items::-webkit-scrollbar-track
    {
        margin-top: 6px;

        border-radius: 5px;
        background-color: #f3f3f3;
    }

    .vue-dropdown-items::-webkit-scrollbar
    {
        width: 6px;

        background-color: transparent;
    }

    .vue-dropdown-items::-webkit-scrollbar-thumb
    {
        border-radius: 5px;
        background-color: #01a8fe;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search::before
    {
        font-weight: normal;

        text-transform: capitalize;

        color: #949494;
        background: white;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search:empty::before
    {
        content: attr(data-placeholder);
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search:not(:empty)::before
    {
        color: #333;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search
    {
        display: block;

        width: 90%;
        margin: 0 0 17px 15px;
        padding: 0;
        font-weight: 600;

        border-bottom: 1px solid #e1e1e1;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search:focus
    {
        color: #333 !important;
        outline: none;
        background: #fff !important;
        font-weight: normal;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search + .clear-input {
        top: 22px;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search:focus + .clear-input {
        background-image: url(/img/icons/cross.svg);
        top: 22px;
    }

    .vue-dropdown-option.vue-dropdown-option.--dropdown .vue-dropdown-option.--search:hover
    {
        background: white;
    }

</style>
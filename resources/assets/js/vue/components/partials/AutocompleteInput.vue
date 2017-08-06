<template>
    <div class="autocomplete-input">
        <input type="text" class="form-input" @focusin="show_suggestions" @focusout="hide_suggestions" v-model="val">
        <span :class="{ active: is_open }" class="caret"></span>
        <div v-if="visible && suggestions.length" class="suggestions">
            <div v-for="suggestion in suggestions" 
                 class="suggestion"
                 @mousedown="select(suggestion.value)" 
                 v-html="suggestion.title"
            ></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'value', 'data', 'dataStructure'
        ],

        data() {
            return {
                val: this.value,
                visible: false
            };
        },

        watch: {
            val: function (current, previous) {
                if (current !== previous) {
                    this.$emit('input', current);
                }
            },
            value: function (current) {
                this.val = current;
            }
        },

        computed: {
            suggestions() {
                let items = this.data;
                let value = this.val ? this.val.toLowerCase() : '';

                if (value) {
                    items = items.filter(item => item[this.dataStructure.key].toLowerCase().indexOf(value) > -1);
                }

                return items.map(item => {
                    let title = item[this.dataStructure.key];

                    if (value) {
                        let searchable_title = title.toLowerCase();
                        let index = searchable_title.indexOf(value);
                        let length = value.length;

                        title = title.substr(0, index) 
                            + '<strong>' 
                            + title.substr(index, length) 
                            + '</strong>' 
                            + title.substr(index + length)
                        ;
                    }

                    return {
                        title,
                        value: item
                    };
                });
            }
        },

        methods: {
            show_suggestions() {
                this.visible = true;
            },
            hide_suggestions(options = {}) {
                let { delay = 100 } = options;

                setTimeout(() => this.$set(this, 'visible', false), delay);
            },
            select(suggestion) {
                this.$set(this, 'val', suggestion[this.dataStructure.key]);
                this.$emit('select', suggestion);
                this.hide_suggestions({ delay: 0 });
            }
        }
    }
</script>

<style scoped>

    .caret {
        position: absolute;
        right: 15px;
        top: 20px;
    }

    .autocomplete-input .form-input {
        padding-right: 20px;
        cursor: default;
    }

    .autocomplete-input .form-input:focus {
        cursor: text;
    }

    .suggestions {
        background: white;
        border-radius: 0 0 2px 2px;
        padding: 5px 0;
        width: 100%;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.25);
        z-index: 1;
        max-height: 200px;
        overflow-y: auto;
        top: 45px;
    }

    .suggestion {
        position: relative;
        line-height: 1;
        padding: 10px 15px;
        cursor: default;
    }

    .suggestion:hover {
        background: #f3f3f3;
    }
</style>
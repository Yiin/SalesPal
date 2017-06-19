<template>
    <div>
            <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
            <li class="active">Clients</li>
    </ol>

        <div class="row">
            <div v-if="create" v-html="create" class="create-btn-wrapper"></div>

            <div class="col-xs-3">
                <dropdown :options="filters"></dropdown>
            </div>
        </div>



        <div ref="table_wrapper" class="dataTables_wrapper form-inline no-footer">
            <table class="table table-striped data-table dataTable no-footer">
                <thead>
                    <tr v-if="!columns_loaded">
                        <th>
                            Loading columns...
                        </th>
                    </tr>
                    <tr>
                        <th v-if="bulkEdit">
                            <div @click="toggleSelectAll()" class="custom-checkbox custom-checkbox-datatables-header">
                                <input type="checkbox" v-model="checkboxAll">
                                <label></label>
                            </div>
                        </th>
                        <th v-for="column in table_columns">
                            {{ column.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!entities_loaded">
                        <td valign="top" :colspan="table_columns.length + (bulkEdit ? 1 : 0)" class="dataTables_empty">
                            Loading data...
                        </td>
                    </tr>
                    <tr v-if="table_state.is_empty">
                        <td valign="top" :colspan="table_columns.length + (bulkEdit ? 1 : 0)" class="dataTables_empty">
                            No data available in table
                        </td>
                    </tr>
                    <tr v-for="row in table_rows" 
                    @click="(row.__checkbox.show ? toggleSelect(row.__checkbox.data.id) : null)"
                    @contextmenu.prevent="showContextMenu($event, row)"
                    :class="{ hover: row === contextMenu.row }"
                    >
                    <td v-if="bulkEdit">
                        <div v-if="row.__checkbox.show" class="custom-checkbox custom-checkbox-datatable">
                            <input type="checkbox" name="ids[]" :value="row.__checkbox.data.id" v-model="selected_entities" :class="row.__checkbox.data.class">
                            <label></label>
                        </div>
                    </td>
                    <td v-for="column in table_columns" v-html="typeof row[column.field] === 'string' ? row[column.field] : row[column.field].display"></td>
                </tr>
            </tbody>
        </table>
        <div v-if="!table_state.is_empty && entities_loaded" class="table-controls-wrapper">
            <div class="calculator">
                <span>Total</span>
                    <div class="calculator-show" >
                        <select class="calculator-buton" v-model="selected">
                            <option>Show</option>
                            <option>Balance</option>
                        </select>
                        </div>
                        <span>for selected is {{ selected }}</span>
            </div>
            <div class="table-controls">
            <span>Page</span>
                <div class="pagination">
                    <li v-if="table_state.page > 1" @click="previousPage()" :disabled="table_state.loading" class="prev disabled">
                        <a>«</a>
                    </li>
                    <li><input type="text" min="1" :max="table_state.page_count" v-model.number="table_state.page" :disabled="table_state.loading" class="page active table-state"></li>
                    <li v-if="table_state.page < table_state.page_count" @click="nextPage()" :disabled="table_state.loading" class="next">
                        <a>»</a>
                    </li>
                </div>
                <div class="elements-control">
                <span>
                    <template v-if="table_state.entities_count > 0">
                        Showing {{ showing_from }} to {{ showing_to }} out of {{ showing_out_of }} entries
                    </template>
                    <template v-else>
                        Showing 0 entries
                    </template>
                </span>
                </div>
                <div class="entities-count-control entities-control">
                    <select v-model="table_state.entities_per_page">
                        <option value="5">5</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <span>rows</span>
            </div>
        </div>

            <ul v-on-clickaway="clickAway" 
            ref="contextmenu" 
            v-if="contextMenu.visible" 
            :style="{ top: contextMenu.position.top, left: contextMenu.position.left }" 
            class="context-menu"
            >
            <li v-for="element in contextMenu.elements" 
            v-html="element.title" 
            :class="{ divider: element === '' }"
            :onclick="( typeof element.action !== 'undefined' ? element.action : '' )"
            ></li>
        </ul>
    </div>
    
    <div class="label retry ">RETRY</div>
    <div class="label verify ">VERIFY</div>

</div>
</div>
</template>

<script>

    import { mixin as clickaway } from 'vue-clickaway';

    export default {
        mixins: [
        clickaway
        ],



        props: [
        'entity', 'entities', 'create', 'clientId'
        ],



        data() {
            return {
                contextMenu: {
                    position: {
                        top: 0,
                        left: 0
                    },
                    visible: false,
                    elements: [],
                    row: null
                },
                filter: {
                    all: true
                },
                filters: [],

                bulkEdit: false,
                checkboxAll: false,
                selected_entities: [],

                orderBy: 'created_at',
                orderDirection: 'DESC',
                table_state: {
                    page: 1,
                    page_count: 1,
                    entities_per_page: 5,
                    entities_count: 2,
                    loading: false,
                    is_empty: false
                },
                columns_loaded: false,
                entities_loaded: false,
                table_filters: [],
                table_columns: [],
                table_rows: []
            }
        },



        computed: {

            all_rows_are_checked() {
                return (this.selected_entities.length === this.table_rows.filter(row => row.__checkbox.show).length);
            },

            showing_from() {
                return (this.table_state.page - 1) * this.table_state.entities_per_page + 1;
            },

            showing_to() {
                let max = this.table_state.page * this.table_state.entities_per_page;
                let count = this.table_state.entities_count;

                return max > count ? count : max;
            },

            showing_out_of() {
                return this.table_state.entities_count;
            }

        },



        watch: {
            'table_state.page': function (current, previous) {            
                if (current && current !== previous) {
                    if(current > this.table_state.page_count) {
                        this.table_state.page = this.table_state.page_count;
                    }
                    this.loadEntities();
                }
            },
            'table_state.entities_per_page': function (entities_per_page, previous) {
                if (entities_per_page * (this.table_state.page - 1) > this.table_state.entities_count) {
                    this.table_state.page = Math.ceil(this.table_state.entities_count / entities_per_page);
                }
                this.loadEntities();
            }
        },



        methods: {

            loadData() {
            // this.loadFilters();
            this.loadColumns();
            this.loadEntities();
        },


        registerListeners() {
            window.addEventListener('keydown', e => {
                switch(e.keyCode) {
                    /* esc */ case 27:
                    this.clickAway();
                    break;
                }
            });
        },


        loadFilters() {
            this.$http.get(`/api/${this.entities || this.entity + 's'}-filters`)
            .then(response => response.data)
            .then(this.handleFilters)
            .catch(this.handleError);
        },


        loadColumns() {
            this.$http.get(`/api/${this.entities || this.entity + 's'}-columns/${this.clientId}`)
            .then(response => response.data)
            .then(this.handleColumns)
            .then(() => this.columns_loaded = true)
            .catch(this.handleError);
        },


        loadEntities() {
            if(this.table_state.loading) {
                return;
            }

            this.table_state.loading = true;

            let query = [];

            for (let key in this.table_state) {
                query.push(`state[${key}]=${this.table_state[key]}`);
            }

            if (!this.filter.all) {
                for (let key in this.filters) {
                    query.push(`filter[${key}]=${this.filters[key]}`);
                }
            }

            query.push(`orderBy[0]=${this.orderBy}`);
            query.push(`orderBy[1]=${this.orderDirection}`);

            this.$http.get(`/api/${this.entities || this.entity + 's'}/${this.clientId}` + '?' + query.join('&'))
            .then(response => response.data)
            .then(this.handleEntities)
            .then(() => this.table_state.loading = false)
            .then(() => this.entities_loaded = true)
            .catch(this.handleError);
        },


        handleColumns(columns) {
            this.table_columns = columns;
        },


        handleEntities(entities) {
            this.bulkEdit = entities.bulkEdit;
            this.table_rows = entities.rows;
            this.table_state = entities.table_state;
        },


        handleError(err) {
            console.error(err);
        },



        previousPage() {
            if (this.table_state.page > 1) {
                this.table_state.page--;
            }
        },


        nextPage() {
            if (this.table_state.page < this.table_state.page_count) {
                this.table_state.page++;
            }
        },



        toggleSelect(id) {
            let index = this.selected_entities.indexOf(id);

            if (index > -1) {
                this.selected_entities.splice(index, 1);
            }
            else {
                this.selected_entities.push(id);
            }

            this.checkboxAll = this.all_rows_are_checked;
        },


        toggleSelectAll() {
            let select = !this.all_rows_are_checked;

            this.checkboxAll = select;
            this.selected_entities = [];

            if (select) {
                this.table_rows.forEach(row => this.selected_entities.push(row.__checkbox.data.id));
            }
        },


        showContextMenu(e, row) {
            this.contextMenu.elements = [];

            if (this.selected_entities.length) {
                this.contextMenu.elements.push({ title: `Selected ${this.entities || this.entity + 's'}: ${this.selected_entities.length}` });
                this.contextMenu.elements.push({ title: 'Archive', action: `javascript:submitForm_${this.entity}('archive');` });
                this.contextMenu.elements.push({ title: 'Delete', action: `javascript:submitForm_${this.entity}('delete');` });
                this.contextMenu.elements.push('');
                this.contextMenu.elements.push({ title: this.entity[0].toUpperCase() + `${this.entity}: ${row.__title}`.slice(1) });
            }

            row.__actions.forEach(action => {
                this.contextMenu.elements.push(action);
            });

            if (this.contextMenu.elements.length) {
                this.contextMenu.visible = true;
                this.contextMenu.row = row;

                Vue.nextTick(() => this.setMenuPosition(e.y, e.x));
            }
        },


        clickAway() {
            this.contextMenu.visible = false;
            this.contextMenu.row = null;
        },


        setMenuPosition(top, left) {
            let offset = this.getElementPosition(this.$refs.table_wrapper);
            let largestHeight = window.innerHeight - this.$refs.contextmenu.offsetHeight - 25;
            let largestWidth = window.innerWidth - this.$refs.contextmenu.offsetWidth - 25;
            
            if (top > largestHeight) top = largestHeight;
            if (left > largestWidth) left = largestWidth;

            top -= offset.top;
            left -= offset.left;

            this.contextMenu.position.top = top + 'px';
            this.contextMenu.position.left = left + 'px';
        },


        getElementPosition(elem) {
            let box = elem.getBoundingClientRect();

            let body = document.body;
            let docEl = document.documentElement;

            let scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
            let scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

            let clientTop = docEl.clientTop || body.clientTop || 0;
            let clientLeft = docEl.clientLeft || body.clientLeft || 0;

            let top  = box.top +  scrollTop - clientTop;
            let left = box.left + scrollLeft - clientLeft;

            return { top: Math.round(top), left: Math.round(left) };
        }

    },



    mounted() {
        this.loadData();
        this.registerListeners();
    }
}
</script>

<style scoped>
    .table-state {
        border: #ffffff;
        padding-top: 12px;
        padding-bottom: 13px;
        box-shadow: 0px 3px 5px 0px rgba(161, 161, 161, 0.2);
        font-size: 16px;
    }

    .calculator-buton {
        width: 160px !important;
        border: white;
        width: 160px;
        box-shadow: 0px 3px 5px 0px rgba(161, 161, 161, 0.2);
        font-size: 16px;
        padding-top: 11px;
        margin-top: -4px;
        text-align: center;
        padding-bottom: 10px;
        margin-left: 20px;
        margin-right: 15px;
    }
    .entities-control {
    padding-left: 20px;
    padding-top: 10px;
}
    .entities-count-control option {
    font-weight: 500;
}

    .calculator {
        display: inline-flex;
        float: left;
        margin-top: 20px;
    }

    .calculator span {
        vertical-align: top;
        font-size: 16px;
        font-weight: 600;
        color: #949494 !important;
        margin: 7px 0;
        display: inline-block;
    }

    .retry {
        background-color: #373737;
        font-size: 100%;
    }

    .verify {
        background-color: #01a8fe;
        font-size: 100%;
    }
    div.entities-count-control select {
        text-align: center;
        background: #ffffff !important;
        color: #373737 !important;
        border: none;
        box-shadow: 0px 3px 5px 0px rgba(161, 161, 161, 0.2);
        font-size: 16px;
        padding: 7px 10px 7px;
        margin-right: 10px;
        width: 80px;
    }
    .create-btn-wrapper {
        display: inline-block;
        float: left;
        margin: 0 15px;
    }

    tr:hover, tr.hover {
        background-color: #f5f5f5 !important;
    }

    .context-menu {
        background: #FAFAFA;
        border: 1px solid #BDBDBD;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        display: block;
        list-style: none;
        margin: 0;
        padding: 0;
        position: absolute;
        width: 250px;
        z-index: 999999;
    }

    .context-menu li, .context-menu li > a {
        font-size: 16px;
        cursor: default;
        user-select: none;
    }

    .context-menu li {
        padding: 10px 20px;
    }

    .context-menu li.divider {
        padding: 0;
        border-bottom: 1px solid #e0e0e0;
        width: 85%;
        margin: 5px auto;
    }

    .context-menu li:not(.divider):hover {
        background-color: #eee;
    }
</style>
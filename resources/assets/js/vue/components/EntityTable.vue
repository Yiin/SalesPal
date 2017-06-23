<template>
    <div>
        <ol class="breadcrumb"><li><a class="glyphicon glyphicon-home" href="http://185.81.165.216/index.php/index.php"></a></li><li class="active">Clients</li></ol>
        <div class="row">
            <div v-if="create" v-html="create" class="create-btn-wrapper"></div>

            <filters-dropdown v-if="filters.length" :options="filters"></filters-dropdown>
            <search-by-dropdown v-if="searchBy.length" :options="searchBy" @changed="searchByHandler"></search-by-dropdown>
        </div>



        <div ref="table_wrapper" class="dataTables_wrapper form-inline no-footer">
            <table class="table table-striped data-table dataTable no-footer">
                <!-- 
                    Table Columns
                 -->
                <thead>
                    <tr v-if="!columns_loaded">
                        <th>
                            Loading columns...
                        </th>
                    </tr>
                    <tr>
                        <th v-if="bulkEdit" style="width: 4%">
                            <div @click="toggleSelectAll()" class="custom-checkbox custom-checkbox-datatables-header">
                                <input type="checkbox" v-model="checkboxAll">
                                <label></label>
                            </div>
                        </th>
                        <th v-for="column in table_columns" 
                            @click="order(column.field)"
                            :class="{ 
                                sorting_asc: orderBy === column.field && orderDirection === 'ASC', 
                                sorting_desc: orderBy === column.field && orderDirection === 'DESC'
                            }"
                            :style="{ width: column.width }"
                        >
                            {{ column.label }}
                        </th>
                    </tr>
                </thead>

                <!-- 
                    Table Rows
                 -->
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

            <!-- 
                Table Controls
             -->
            <div v-if="!table_state.is_empty && entities_loaded" class="table-controls-wrapper">
            <div class="calculator">
                        <span>Total</span>
                            <div class="calculator-show" >
                                <select class="calculator-buton">
                                    <option value="">Show</option>
                                    <option value="balance">Balance</option>
                                </select>
                            </div>
                        <span>for selected is {{ selected }}</span>
                    </div>
                <div class="table-controls">
                    <span>Page</span>
                    <div class="pagination">
                        <li v-if="table_state.page > 1" @click="previousPage()" :disabled="table_state.loading" class="prev">
                            <a>«</a>
                        </li>
                        <li><input type="text" min="1" :max="table_state.page_count" v-model.number="table_state.page" :disabled="table_state.loading" class="page active page-count"></li>
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
                    <div class="entities-count-control">
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

            <!-- 
                Context Menu
             -->
            <ul v-on-clickaway="clickAway" 
                ref="contextmenu" 
                v-if="contextMenu.visible" 
                :style="{ top: contextMenu.position.top, left: contextMenu.position.left }" 
                class="context-menu"
            >
                <li v-for="element in contextMenu.elements" 
                    v-html="element.title" 
                    :class="{ divider: element === '' }"
                    @click="contextMenuClickHandler(element)"
                ></li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from '../mixins/clickaway';

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
            filters: [],
            searchBy: [],

            bulkEdit: false,
            checkboxAll: false,
            selected_entities: [],
            selected_entity_id: null,

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
            table_rows: [],

            promise: {
                loadEntities: null,
            },
            searchByTimeout: null
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
        filters: {
            handler: function (current, previous) {
                this.loadEntities();
            },
            deep: true
        },
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
            this.loadFilters();
            this.loadSearchBy();
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


        loadSearchBy() {
            this.$http.get(`/api/${this.entities || this.entity + 's'}-searchby`)
                .then(response => response.data)
                .then(this.handleSearchBy)
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
            this.table_state.loading = true;

            let query = [];

            for (let key in this.table_state) {
                query.push(`state[${key}]=${this.table_state[key]}`);
            }

            let filterIdx = 0;
            this.filters.filter(filter => filter.selected || filter.type === 'dropdown').forEach(filter => {
                if (filter.type === 'dropdown') {
                    filter.options.filter(_filter => _filter.selected).forEach(_filter => {
                        query.push(`filter[${filterIdx}]=${_filter.value}`);
                        filterIdx++;
                    });
                }
                else {
                    query.push(`filter[${filterIdx}]=${filter.value}`);
                    filterIdx++;
                }
            });

            this.searchBy.filter(option => {
                if (typeof option.value === 'string') {
                    return option.value.length > 0;
                }
                else if (typeof option.value === 'object') {
                    // array of dates, [start, end]
                    return option.value && option.value.length === 2 && option.value[0].length && option.value[1].length;
                }
                else {
                    return false;
                }
            }).forEach(option => {
                let value = _.unescape(option.value);
                query.push(`searchBy[${option.name}]=${value}`);
            });

            query.push(`orderBy[0]=${this.orderBy}`);
            query.push(`orderBy[1]=${this.orderDirection}`);

            let url = `/api/${this.entities || this.entity + 's'}/${this.clientId}` + '?' + query.join('&');

            this.$http.get(url, {

                before(request) {

                  // abort previous request, if exists
                  if (this.promise.loadEntities) {
                    this.promise.loadEntities.abort();
                  }

                  // set previous request on Vue instance
                  this.promise.loadEntities = request;
                }

            })
                .then(response => response.data)
                .then(this.handleEntities)
                .then(() => this.table_state.loading = false)
                .then(() => this.entities_loaded = true)
                .catch(this.handleError);
        },


        handleFilters(filters) {
            this.filters = filters;
        },


        handleSearchBy(searchBy) {
            this.searchBy = searchBy;
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
            // console.error(err);
        },



        searchByHandler() {
            this._loadEntities();
        },



        order(field) {
            if (this.table_state.loading) {
                return;
            }
            if (this.orderBy === field) {
                this.orderDirection = this.orderDirection === 'ASC' ? 'DESC' : 'ASC';
            }
            else {
                this.orderBy = field;
                this.orderDirection = 'ASC';
            }
            this.loadEntities();
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



        toggleSelect(id, toggleOff = null) {
            let index = this.selected_entities.indexOf(id);

            if (index > -1) {
                this.toggleSelectOff(index, true);
            }
            else {
                this.toggleSelectOn(id, false);
            }

            this.checkboxAll = this.all_rows_are_checked;
        },


        toggleSelectOff(id, is_index = false) {
            let index = is_index ? id : this.selected_entities.indexOf(id);
            this.selected_entities.splice(index, 1);
        },


        toggleSelectOn(id, check_if_exists = true) {
            if (check_if_exists && this.selected_entities.indexOf(id) !== -1) {
                return;
            }
            this.selected_entities.push(id);
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
            if (row.__checkbox) {
                let id = row.__checkbox.data.id;

                if (this.selected_entity_id && this.selected_entity_id !== id) {
                    this.toggleSelectOff(this.selected_entity_id);
                }
                this.selected_entity_id = id;
                this.toggleSelectOn(id);
            }

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


        contextMenuClickHandler(element) {
            if (typeof element.action !== 'undefined') {
                this.clickAway(false);

                eval(element.action);
            }
        },


        clickAway(cancel = true) {
            if (cancel && this.selected_entity_id) {
                this.toggleSelectOff(this.selected_entity_id);
                this.selected_entity_id = null;
            }
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

        this._loadEntities = _.debounce(this.loadEntities, 500);
    }
}
</script>

<style scoped>

    .calculator-buton option {
        padding: 15px;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .breadcrumb a {
        margin-right: 10px;
        text-decoration: none;
        color: #01a8fe;
    }

    .breadcrumb {
        font-size: 18px;
        margin-bottom: 7px;
    }
    .create-btn-wrapper > .btn {
    width: 190px;
    display: inline-flex;
    font-size: 18px;
    }

    .devel-dropdown-toggle {
    display: none;
    }
    .page-count {
        border: none;
        box-shadow: 0px 3px 5px 0px rgba(161, 161, 161, 0.2);
        font-family: 'Open Sans', sans-serif;
        font-size: 16px;
            box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.08), 1px 2px 5px rgba(0, 0, 0, 0.08), 0px 3px 5px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
    }

    li.active input {
        padding-bottom: 13px;
        padding-top: 13px;
    }

    .calculator {
        display: inline-flex;
        float: left;
        margin-top: 34px;
    }

    .calculator span {
        vertical-align: top;
        font-size: 16px;
        font-weight: 600;
        color: #949494 !important;
        margin: 7px 0;
        display: inline-block;
    }

     .calculator-buton {
        width: 160px !important;
        border: white;
        width: 160px;
        box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.08), 1px 2px 5px rgba(0, 0, 0, 0.08), 0px 3px 5px rgba(0, 0, 0, 0.08);
        font-size: 16px;
        padding-top: 11px;
        margin-top: -4px;
        text-align: center;
        padding-bottom: 10px;
        margin-left: 20px;
        margin-right: 15px;
        padding-left: 15px;
        border-radius: 4px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .active {
        padding-bottom: 12px;
        padding-top: 12px;
    }
    .create-btn-wrapper {
        display: inline-block;
        float: left;
        margin: 0 15px;
    }

    th {
        cursor: pointer;
        user-select: none;
    }

    tr:hover, tr.hover {
        background-color: #f5f5f5 !important;
    }

    .context-menu {
        background: #FFFFFF;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
        display: block;
        list-style: none;
        margin: 0;
        padding: 0px;
        position: absolute;
        width: 320px;
        z-index: 999999;
        padding-top: 18px;
        padding-bottom: 23px;
        border: 4px;
    }

    .context-menu li, .context-menu li > a {
        font-size: 16px;
        cursor: default;
        user-select: none;
    }

    .context-menu li {
        padding-bottom: 6px;
        padding-top: 7px;
        padding-left: 30px;
        padding-right: 30px;
    }

    .context-menu li.divider {
        padding: 0;
        border-bottom: 1px solid #e0e0e0;
        width: 100%;
        margin: 5px auto;
    }

    .context-menu li:not(.divider):hover {
        background-color: #eee;
    }

    /* DataTables styles */
    td, td > a {
        color: #373737;
        font-family: "Open Sans", sans-serif;
        font-size: 16px;
    }

    td > a:hover {
        color: #ed492f;
        text-decoration: none;
    }

    /* Context menu should be above everything */
    .context-menu-list {
        z-index: 100 !important;
    }

    /* Datatable rows */
    .currency_value {
        color: #01a8fe;
        padding-left: 4px;
    }

    .currency_symbol, .currency_value {
        font-weight: 600;
    }

    /* Datatable controls */
    div.entities-count-control select {
        text-align: center;
        background: #ffffff !important;
        color: #373737 !important;
        border: none;
        box-shadow: -1px 2px 5px rgba(0, 0, 0, 0.08), 1px 2px 5px rgba(0, 0, 0, 0.08), 0px 3px 5px rgba(0, 0, 0, 0.08);
        font-size: 16px;
        padding: 7px 15px 7px;
        width: 80px;
        padding-bottom: 11px;
        padding-top: 11px;
        font-family: 'Open Sans', sans-serif;
        border-radius: 4px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .table-controls {
        float: right;
        margin-top: 30px;
    }

    .table-controls > div {
        float: none !important;
        display: inline-block;
        vertical-align: middle;
        margin: 0 !important;
        padding: 0 !important;
        margin-right: -15px;
    }

    .pagination > span, template, .table-controls > span, .elements-control > span, .dataTables_info {
        vertical-align: top;
        font-size: 16px;
        font-weight: 600;
        color: #949494 !important;
        margin: 12px 0;
        display: inline-block;
        margin-right: 14px;
        margin-left: 14px;
    }

    .pagination > ul {
        margin: 0 20px;
    }

    .pagination > li {
        display: inline-block !important;
        vertical-align: top;
    }

    .pagination .page {
        width: 70px;
        text-align: center;
    }

    .pagination > .prev a, .pagination > .next a {
        border: none;
        background: none;
        font-weight: 600;
        color: #949494;
        padding: 0;
        cursor: pointer;
        font-size: 30px;
        line-height: 30px;
    }

    .pagination > .prev a {
        padding-right: 6px;
        padding-bottom: 10px; 
        border: none;
        background: none;
        padding-top: 5px;  
    }

    .pagination > .next a {
        padding-left: 6px;
        padding-bottom: 10px; 
        border: none;
        background: none;
        padding-top: 5px;
    }

    .pagination > .prev a:hover {
    color: #333;
    }

    .pagination > .next a:hover {
    color: #333;
    }

    .pagination > .disabled > span, .pagination > .disabled > span:hover, .pagination > .disabled > span:focus, .pagination > .disabled > a, .pagination > .disabled > a:hover, .pagination > .disabled > a:focus {
        display: none;
    }

    .pagination > .active > input, .pagination > .active > span, .pagination > .active > input:hover, .pagination > .active > span:hover, .pagination > .active > input:focus, .pagination > .active > span:focus {
        background: #ffffff !important;
        color: #373737 !important;
        border: none;
        box-shadow: 0px 3px 5px 0px rgba(161, 161, 161, 0.2);
        font-size: 16px;
        padding: 7px 0;
    }

    .pagination > li > a:hover, .pagination > li > span:hover, .pagination > li > a:focus, .pagination > li > span:focus {
        color: #373737 !important;
        background: none;
        border: none;
    }

    /*
        Status Labels
    */
   
   .label {
        padding-top: 6px;
        padding-bottom: 5px;
        font-size: 100%;
        width: 102px;
        display: inline-block;
   }
   
   .label-viewed {
        background-color: #01A7FD;
    }

    .label-draft {
        background-color: #383838;
    }

    .label-sent {
        background-color: #5BC0DE;
    }

    .label-paid {
        background-color: #4ECD09;
    }

    .label-overdue {
        background-color: #E2492F;
    }

    .label-partial {
        background-color: #FF6C11;
    }
</style>
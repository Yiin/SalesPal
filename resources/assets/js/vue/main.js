require('./bootstrap');

// Vue.component('', require('./components/.vue'));
Vue.component('text-item', require('./components/TextItem.vue'));
Vue.component('date-item', require('./components/DateItem.vue'));
Vue.component('dropdown', require('./components/Dropdown.vue'));
Vue.component('filters-dropdown', require('./components/FiltersDropdown.vue'));
Vue.component('search-by-dropdown', require('./components/SearchByDropdown.vue'));
Vue.component('entity-table', require('./components/EntityTable.vue'));

Array.prototype.filter.call(document.getElementsByClassName('vue-app'), el => el)
    .forEach(el => {
        new Vue({ el: '#' + el.id })
    });
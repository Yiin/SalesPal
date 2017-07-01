require('./bootstrap');

// partials
Vue.component('child-dropdown-menu', require('./components/partials/ChildDropdownMenu.vue'));

// features
Vue.component('feature-check-vat', require('./components/features/CheckVat.vue'));

// dropdown items
Vue.component('text-item', require('./components/TextItem.vue'));
Vue.component('date-item', require('./components/DateItem.vue'));

// child components
Vue.component('dropdown', require('./components/Dropdown.vue'));
Vue.component('filters-dropdown', require('./components/FiltersDropdown.vue'));
Vue.component('search-by-dropdown', require('./components/SearchByDropdown.vue'));

// main components
Vue.component('entity-table', require('./components/EntityTable.vue'));

Array.prototype.filter.call(document.getElementsByClassName('vue-app'), el => el)
    .forEach(el => {
        new Vue({ el: '#' + el.id })
    });
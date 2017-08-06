require('./bootstrap');

// partials
Vue.component('child-dropdown-menu', require('./components/partials/ChildDropdownMenu.vue'));
Vue.component('contacts-panel', require('./components/partials/ContactsPanel.vue'));
Vue.component('client-vat-checker', require('./components/partials/ClientVatChecker.vue'));
Vue.component('autocomplete-input', require('./components/partials/AutocompleteInput.vue'));

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
Vue.component('dashboard-statistics', require('./components/DashboardStatistics.vue'));
Vue.component('dashboard-activities', require('./components/DashboardActivities.vue'));
// Vue.component('invoice-form', require('./components/InvoiceForm.vue'));
Vue.component('entity-table', require('./components/EntityTable.vue'));

Array.prototype.filter.call(document.getElementsByClassName('vue-app'), el => el)
    .forEach(el => {
        new Vue({ el: '#' + el.id })
    });
require('./bootstrap');

// Vue.component('', require('./components/.vue'));
Vue.component('dropdown', require('./components/Dropdown.vue'));
Vue.component('entity-table', require('./components/EntityTable.vue'));

Array.prototype.filter.call(document.getElementsByClassName('vue-app'), el => el)
    .forEach(el => {
        new Vue({ el: '#' + el.id })
    });
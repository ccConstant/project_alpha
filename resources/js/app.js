

require('./bootstrap');

window.Vue = require('vue').default;
import Vue from 'vue';
import VueRouter from 'vue-router';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'





Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);


const router = new VueRouter({
    mode: 'history',
    routes: [{
        path:'/',
        component:require('./components/HomePage.vue').default
    }, {
        path:'/equipment/add',
        component:require('./components/equipment/referencing/ReferenceAnEquipment.vue').default
    },{
        path:'/equipment/list/consult/:id(\\d+)',
        name: 'url_eq_consult',
        component:require('./components/equipment/consultation/EquipmentConsult.vue').default
    },{
        path:'/equipment/list/update/:id(\\d+)',
        name: 'url_eq_update',
        component:require('./components/equipment/consultation/EquipmentUpdate.vue').default
    },{
        path:'/equipment/list',
        name: 'url_eq_list',
        component:require('./components/equipment/consultation/ListOfEquipment.vue').default
    },{
        path:'/enum',
        name: 'url_enum',
        component:require('./components/enum/EnumManagment.vue').default
    },{
        path:'/equipment/life_event',
        name: 'url_life_event',
        component:require('./components/equipment/lifeEvent/ListOfEquipmentLifeEvent.vue').default
    },{
        path:'/equipment/life_event/state/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_update_state',
        component:require('./components/equipment/lifeEvent/UpdateState.vue').default
    },{
        path:'/equipment/life_event/state/:id(\\d+)',
        name: 'url_life_event_change_state',
        component:require('./components/equipment/lifeEvent/UpdateState.vue').default
    },{
        path:'/equipment/life_event/all/:id(\\d+)',
        name: 'url_life_event_all',
        component:require('./components/equipment/lifeEvent/EquipmentAllEventList.vue').default
    },{
        path:'/equipment/life_event/all/consult/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_all_consult',
        component:require('./components/equipment/lifeEvent/EquipmentEventConsult.vue').default
    },{
        path:'/equipment/life_event/reference/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_reference',
        component:require('./components/equipment/lifeEvent/ReferenceALifeEvent.vue').default
    },{
        path:'/equipment/life_event/update/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_update',
        component:require('./components/equipment/lifeEvent/UpdateALifeEvent.vue').default

    },{
        path:'/equipment/maintenance/calendar',
        name: 'url_maintenance_calendar',
        component:require('./components/equipment/calendar/EquipmentMaintenanceCalendar.vue').default

    },{
        path:'/equipment/lifesheet_pdf/:id(\\d+)',
        name: 'url_lifesheet_pdf',
        component:require('./components/equipment/consultation/LifeSheetPDF.vue').default

    },{
        path:'*',
    }]

});



Vue.component('navbar_alpha', require('./components/layout/Navbar.vue').default);
Vue.component('equipmentid', require('./components/equipment/referencing/EquipmentIDForm.vue').default);


const app = new Vue({
    router
}).$mount('#app');

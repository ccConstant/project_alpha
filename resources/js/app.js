

require('./bootstrap');

window.Vue = require('vue').default;
import Vue from 'vue';
import VueRouter from 'vue-router';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import Snotify,{ SnotifyPosition} from 'vue-snotify';
import JwPagination from 'jw-vue-pagination';
const Snotifyoptions={
    toast :{
        position :SnotifyPosition.centerTop,
        timeout: 5000
    }
}

Vue.component('jw-pagination', JwPagination);
Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(Snotify , Snotifyoptions)


var user_info=document.querySelector("meta[name='user-id']").getAttribute('content');

if(user_info!=""){
    Vue.prototype.$userId = JSON.parse(user_info);
}else{
    Vue.prototype.$userId=(user_info)
}


const router = new VueRouter({
    mode: 'history',
    routes: [{
        path:'/',
        name: 'home',
        component:require('./components/HomePage.vue').default
    }, {
        path:'/equipment/add',
        name: 'url_eq_add',
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
        path:'/equipment/reform/:id(\\d+)',
        name: 'url_eq_reform',
        component:require('./components/equipment/consultation/EquipmentReform.vue').default

    },{
        path:'/infos',
        name: 'url_infos',
        component:require('./components/infos/InfosManagment.vue').default

    },{
        path:'/accounts',
        name: 'url_accounts',
        component:require('./components/account/AccountsManagment.vue').default

    },{
        path:'/sign_up',
        name: 'url_sign_up',
        component:require('./components/account/SignUp.vue').default

    },{
        path:'/sign_in',
        name: 'url_sign_in',
        component:require('./components/account/SignIn.vue').default

    },{
        path:'/my_account',
        name: 'url_my_account',
        component:require('./components/account/MyAccount.vue').default

    },{
        //MME path
        path:'/mme/add',
        name: 'url_mme_add',
        component:require('./components/mme/referencing/ReferenceAnMME.vue').default
    },{
        path:'/mme/list',
        name: 'url_mme_list',
        component:require('./components/mme/consultation/ListOfMME.vue').default
    },{
        path:'/mme/list/consult/:id(\\d+)',
        name: 'url_mme_consult',
        component:require('./components/mme/consultation/MMEConsult.vue').default
    },{
        path:'/mme/list/update/:id(\\d+)',
        name: 'url_mme_update',
        component:require('./components/mme/consultation/MMEUpdate.vue').default
    },{
        path:'/mme/reform/:id(\\d+)',
        name: 'url_mme_reform',
        component:require('./components/mme/consultation/MMEReform.vue').default

    },{
        path:'/mme/life_event',
        name: 'url_mme_life_event',
        component:require('./components/mme/lifeEvent/ListOfMMELifeEvent.vue').default
    },{
        path:'*',
    }]

});



Vue.component('navbar_alpha', require('./components/layout/Navbar.vue').default);
Vue.component('equipmentid', require('./components/equipment/referencing/EquipmentIDForm.vue').default);


const app = new Vue({
    router
}).$mount('#app');

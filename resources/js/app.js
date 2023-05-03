require('./bootstrap');

window.Vue = require('vue').default;
import Vue from 'vue';
import VueRouter from 'vue-router';
import {BootstrapVue, BootstrapVueIcons} from 'bootstrap-vue'
import Snotify, {SnotifyPosition} from 'vue-snotify';
import JwPagination from 'jw-vue-pagination';

const Snotifyoptions = {
    toast: {
        position: SnotifyPosition.centerTop,
        timeout: 5000
    }
}

Vue.component('jw-pagination', JwPagination);
Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(Snotify, Snotifyoptions)


var user_info = document.querySelector("meta[name='user-id']").getAttribute('content');

if (user_info != "") {
    Vue.prototype.$userId = JSON.parse(user_info);
} else {
    Vue.prototype.$userId = (user_info)
}


const router = new VueRouter({
    mode: 'history',
    routes: [{
        path: '/',
        name: 'home',
        component: require('./components/HomePage.vue').default
    }, {
        path: '/SW01',
        name: 'SW01',
        component: require('./components/SW01/HomePageSW01.vue').default
    }, {
        path: '/SW03',
        name: 'SW03',
        component: require('./components/SW03/HomePageSW03.vue').default
    }, {
        path: '/equipment/add',
        name: 'url_eq_add',
        component: require('./components/SW01/equipment/referencing/ReferenceAnEquipment.vue').default
    }, {
        path: '/equipment/list/consult/:id(\\d+)',
        name: 'url_eq_consult',
        component: require('./components/SW01/equipment/consultation/EquipmentConsult.vue').default
    }, {
        path: '/equipment/list/update/:id(\\d+)',
        name: 'url_eq_update',
        component: require('./components/SW01/equipment/consultation/EquipmentUpdate.vue').default
    }, {
        path: '/equipment/list',
        name: 'url_eq_list',
        component: require('./components/SW01/equipment/consultation/ListOfEquipment.vue').default
    }, {
        path: '/equipment/list/PDF',
        name: 'url_eq_list_pdf',
        component: require('./components/SW01/equipment/consultation/ListOfEquipmentPDF.vue').default
    }, {
        path: '/enum',
        name: 'url_enum',
        component: require('./components/SW01/enum/EnumManagment.vue').default
    }, {
        path: '/equipment/life_event',
        name: 'url_life_event',
        component: require('./components/SW01/equipment/lifeEvent/ListOfEquipmentLifeEvent.vue').default
    }, {
        path: '/equipment/life_event/state/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_update_state',
        component: require('./components/SW01/equipment/lifeEvent/UpdateState.vue').default
    }, {
        path: '/equipment/life_event/state/:id(\\d+)',
        name: 'url_life_event_change_state',
        component: require('./components/SW01/equipment/lifeEvent/UpdateState.vue').default
    }, {
        path: '/equipment/life_event/all/:id(\\d+)',
        name: 'url_life_event_all',
        component: require('./components/SW01/equipment/lifeEvent/EquipmentAllEventList.vue').default
    }, {
        path: '/equipment/life_event/reference/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_reference',
        component: require('./components/SW01/equipment/lifeEvent/ReferenceALifeEvent.vue').default
    }, {
        path: '/equipment/life_event/update/:id(\\d+)/:state_id(\\d+)',
        name: 'url_life_event_update',
        component: require('./components/SW01/equipment/lifeEvent/UpdateALifeEvent.vue').default
    }, {
        path: '/equipment/annual/planning',
        name: 'url_annual_planning',
        component: require('./components/SW01/equipment/calendar/AnnualEquipmentPlanning.vue').default
    }, {
        path: '/equipment/monthly/planning',
        name: 'url_monthly_planning',
        component: require('./components/SW01/equipment/calendar/MonthlyEquipmentPlanning.vue').default
    }, {
        path: '/equipment/lifesheet_pdf/:id(\\d+)',
        name: 'url_lifesheet_pdf',
        component: require('./components/SW01/equipment/consultation/LifeSheetPDF.vue').default
    }, {
        path: '/equipment/reform/:id(\\d+)',
        name: 'url_eq_reform',
        component: require('./components/SW01/equipment/consultation/EquipmentReform.vue').default
    }, {
        path: '/infos',
        name: 'url_infos',
        component: require('./components/SW01/infos/InfosManagment.vue').default
    }, {
        path: '/accounts',
        name: 'url_accounts',
        component: require('./components/account/AccountsManagment.vue').default
    }, {
        path: '/sign_up',
        name: 'url_sign_up',
        component: require('./components/account/SignUp.vue').default
    }, {
        path: '/sign_in',
        name: 'url_sign_in',
        component: require('./components/account/SignIn.vue').default
    }, {
        path: '/my_account',
        name: 'url_my_account',
        component: require('./components/account/MyAccount.vue').default
    }, {
        //MME path
        path: '/mme/add',
        name: 'url_mme_add',
        component: require('./components/SW01/mme/referencing/ReferenceAnMME.vue').default
    }, {
        path: '/mme/list',
        name: 'url_mme_list',
        component: require('./components/SW01/mme/consultation/ListOfMME.vue').default
    }, {
        path: '/mme/list/consult/:id(\\d+)',
        name: 'url_mme_consult',
        component: require('./components/SW01/mme/consultation/MMEConsult.vue').default
    }, {
        path: '/mme/list/update/:id(\\d+)',
        name: 'url_mme_update',
        component: require('./components/SW01/mme/consultation/MMEUpdate.vue').default
    }, {
        path: '/mme/reform/:id(\\d+)',
        name: 'url_mme_reform',
        component: require('./components/SW01/mme/consultation/MMEReform.vue').default
    }, {
        path: '/mme/life_event',
        name: 'url_mme_life_event',
        component: require('./components/SW01/mme/lifeEvent/ListOfMMELifeEvent.vue').default
    }, {
        path: '/mme/life_event/state/:id(\\d+)/:state_id(\\d+)',
        name: 'url_mme_life_event_update_state',
        component: require('./components/SW01/mme/lifeEvent/UpdateMMEState.vue').default
    }, {
        path: '/mme/life_event/reference/:id(\\d+)/:state_id(\\d+)',
        name: 'url_mme_life_event_reference',
        component: require('./components/SW01/mme/lifeEvent/ReferenceAMMELifeEvent.vue').default
    }, {
        path: '/mme/life_event/state/:id(\\d+)',
        name: 'url_mme_life_event_change_state',
        component: require('./components/SW01/mme/lifeEvent/UpdateMMEState.vue').default
    }, {
        path: '/mme/life_event/all/:id(\\d+)',
        name: 'url_mme_life_event_all',
        component: require('./components/SW01/mme/lifeEvent/MMEAllEventList.vue').default
    }, {
        path: '/mme/life_event/update/:id(\\d+)/:state_id(\\d+)',
        name: 'url_mme_life_event_update',
        component: require('./components/SW01/mme/lifeEvent/UpdateAMMELifeEvent.vue').default
    }, {
        path: '/mme/annual/planning',
        name: 'url_mme_annual_planning',
        component: require('./components/SW01/mme/calendar/AnnualMMEPlanning.vue').default
    }, {
        path: '/mme/monthly/planning',
        name: 'url_mme_monthly_planning',
        component: require('./components/SW01/mme/calendar/MonthlyMMEPlanning.vue').default
    }, {
        path: '/mme/lifesheet_pdf/:id(\\d+)',
        name: 'mme_url_lifesheet_pdf',
        component: require('./components/SW01/mme/consultation/LifeSheetPDFMME.vue').default
    }, {
        path: '/supplier/add',
        name: 'supplier_url_add',
        component: require('./components/SW03/supplier/referencing/ReferenceASupplier.vue').default
    }, {
        path: '/supplier/list',
        name: 'supplier_url_list',
        component: require('./components/SW03/supplier/consultation/ListOfSupplier.vue').default
    }, {
        path: '/supplier/list/consult/:id(\\d+)',
        name: 'supplier_url_consult',
        component: require('./components/SW03/supplier/consultation/SupplierConsult.vue').default
    }, {
        path: '/supplier/list/update/:id(\\d+)',
        name: 'supplier_url_update',
        component: require('./components/SW03/supplier/consultation/SupplierUpdate.vue').default
    },{
        path: '/article/add',
        name: 'article_url_add',
        component: require('./components/SW03/article/referencing/ReferenceAnArticleFamily.vue').default
    },{
        path: '/article/list',
        name: 'article_url_list',
        component: require('./components/SW03/article/consultation/ListOfArticle.vue').default
    },{
        path: '/SW03/enum',
        name: 'url_enum_SW03',
        component: require('./components/SW03/enum/EnumManagment.vue').default
    },{
        path: '*',
    }]
})



Vue.component('navbar', require('./components/layout/Navbar.vue').default);
Vue.component('navbar_sw01', require('./components/layout/NavbarSW01.vue').default);
Vue.component('navbar_sw03', require('./components/layout/NavbarSW03.vue').default);
Vue.component('equipmentid', require('./components/SW01/equipment/referencing/EquipmentIDForm.vue').default);


const app = new Vue({router}).$mount('#app');



<template>
    <div>
        <div v-if="loaded !== true">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <ErrorAlert ref="errorAlert"/>
            <h1>Supplier's List</h1>
            <input placeholder="Search a supplier by his name" v-model="searchTerm" class="form-control w-50 search_bar align-self-center" type="text">
            <ul>
                <div class="one_element_list" v-for="(list,index) in pageOfItems " :key="index">
                    <li class="list-group-item" :class="'element'+index%2">
                        <div class="supplier_name">
                            <b>{{list.supplr_name}}</b>
                        </div>
                        <div class="supplier_list_options">
                            <router-link :to="{name:'url_eq_consult',params:{id: list.id} }">Consult</router-link>
                            <a href="#" @click="warningUpdate(list.id,list.supplr_technicalReviewerId !== null)">Update</a>
                            <a v-if="!(list.supplr_technicalReviewerId !== null)" href="#" @click="technicalValidation(list.id)">Technical validation</a>
                            <a v-if="list.supplr_technicalReviewerId !== null">Statut : Approved</a>
                            <router-link  :to="{name:'url_lifesheet_pdf',params:{id: list.id} }">Generate PDF</router-link>
                        </div>
                    </li>
                </div>
            </ul>
            <jw-pagination class="eq_list_pagination" :pageSize=10 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>
        </div>
        <b-modal :id="`modal-updateWarning-${_uid}`"  @ok="warningUpdate(eq_id,technical,quality,true)">
            <p class="my-4">Your equipment has a validated life sheet, If you update your equipment you will have to
                revalidate </p>
        </b-modal>
        <div class="router-link"><router-link :to="{name:'url_eq_list_pdf',params:{} }"> <button class="btn btn-primary">GO TO PDF</button> </router-link></div>
    </div>
</template>

<script>
import ErrorAlert from "../../../alert/ErrorAlert.vue";

export default {
    components : {
        ErrorAlert
    },
    data(){
        return{
            suppliers: [],
            searchTerm: "",
            loaded: false,
            pageOfItems: [],
            suppliers_id: null,
            technicalReviewer: null
        }
    },
    computed: {
        filterByTerm() {
            return this.suppliers.filter(option => {
                return option.supplr_name.toLowerCase().includes(this.searchTerm);
            });
        }
    },
    created() {
        axios.get('/supplier/send')
            .then(response => {
                console.log(response.data)
                this.suppliers = response.data;
                this.loaded = true;
            })
            .catch(error => console.log(error));
    },
    methods: {
        technicalValidation(id) {
            if (this.$userId.user_makeTechnicalValidationRight !== true) {
                ErrorAlert.showAlert("You don't have the right");
            } else {
                this.$router.replace({ name: "url_eq_consult", params: { id: id }, query: { method: "technical" } })
            }
        },
        warningUpdate(id, technicalReviewer, redirect) {
            this.suppliers_id = id;
            this.technicalReviewer = technicalReviewer;
            if (technicalReviewer === true) {
                // TODO : faut faire quoi ?
            }
        },
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
    }
}
</script>

<style scoped>

</style>

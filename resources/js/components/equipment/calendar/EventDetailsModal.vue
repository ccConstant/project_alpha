<template>
    <div>
        <b-modal id="modal-event_details" @hidden="resetModal" title="Details" hide-footer>
             <ErrorAlert ref="errorAlert"/>
            <div>
                <div v-for="option in prvMtnOps" :key="option.id">
                    <div>
                        <b-card>
                            <h3>{{option.eq_internalReference}}</h3>
                            <p class="card-text">
                                Operation number: {{option.prvMtnOp_number}}<br>
                                Description : {{option.prvMtnOp_description}}<br>
                                Protocol : {{option.prvMtnOp_protocol}}<br>
                                Operation date : {{option.prvMtnOp_nextDate}}
                            </p>
                            <div v-if="makeEqOpValidationRight==true">
                                <b-button variant="primary" @click="redirect_to_preventive(option.eq_id,option.state_id)">Record it</b-button>
                            </div>
                            <div v-else>
                                <b-button variant="primary" disabled >Record it</b-button>
                            </div>
                            
                        </b-card>
                    </div>
                </div>
            </div>
            <b-button class="mt-3" block @click="closeAndClear()">Close</b-button>
        </b-modal>     
    </div>
</template>

<script>
import ErrorAlert from '../../alert/ErrorAlert.vue'


export default {
    components:{
        ErrorAlert,
    },
    props:{
        prvMtnOps:{
            type:Array
        }
        
    },
    data(){
        return{
            makeEqOpValidationRight:this.$userId.user_makeEqOpValidationRight,
        }
    },
    methods:{
        closeAndClear(){
            this.$bvModal.hide('modal-event_details')
        },
        resetModal() {
            this.$emit('modalClosed','')
        },
        redirect_to_preventive(eq_id,state_id){
            if(this.$userId.user_makeEqOpValidationRight!=true){
            this.$refs.errorAlert.showAlert("You don't have the right");
            
            }
            var consultUrl = (state_id) => `/state/verif/beforeReferencePrvOp/${state_id}`;
            axios.post(consultUrl(state_id),{
                eq_id:eq_id
            })
            .then(response =>{
                this.$router.replace({ name: "url_life_event_reference", params: {id:eq_id,state_id:state_id }, query: {type:"preventive"}})
            ;})
            //If the controller sends errors we put it in the errors object 
            .catch(error => {
            this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
            });




            
        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8; 
    }

    

</style>
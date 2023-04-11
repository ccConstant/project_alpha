<template>
    <div>
        <ErrorAlert ref="errorAlert"/>
        <b-modal id="modal-event_details" @hidden="resetModal" title="Details" hide-footer>
            <div>
                <div v-for="option in verif" :key="option.id">
                    <div>
                        <b-card>
                            <h3>{{option.mme_internalReference}}</h3>
                            <p class="card-text">
                                Operation number : {{option.verif_number}}<br>
                                Expected result : {{option.verif_expectedResult}}<br>
                                Non compliance limit : {{option.verif_nonComplianceLimit}}<br>
                                Description : {{option.verif_description}}<br>
                                Protocol : {{option.verif_protocol}}<br>
                                Operation date : {{option.verif_nextDate}}
                            </p>
                            <div v-if="makeMmeOpValidationRight==true">
                                <b-button variant="primary" @click="redirect_to_preventive(option.mme_id,option.state_id)">Record it</b-button>
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
        ErrorAlert

    },
    props:{
        verif:{
            type:Array
        }
    },
    data(){
        return{
            makeMmeOpValidationRight:this.$userId.user_makeMmeOpValidationRight,
        }
    },
    methods:{
        closeAndClear(){
            this.$bvModal.hide('modal-event_details')
        },
        resetModal() {
            this.$emit('modalClosed','')
        },
        redirect_to_preventive(mme_id,state_id){
            if(this.$userId.user_makeMmeOpValidationRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            }
            console.log("mme_id : "+mme_id)
            var consultUrl = (state_id) => `/mme_state/verif/beforeReferenceVerif/${state_id}`;
            axios.post(consultUrl(state_id),{
                mme_id:mme_id
            })
            .then(response =>{
                this.$router.push({ name: "url_mme_life_event_reference", params: {id:mme_id,state_id:state_id }, query: {type:"verif"}})
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
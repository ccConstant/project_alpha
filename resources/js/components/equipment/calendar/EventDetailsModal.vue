<template>
    <div>
        <b-modal id="modal-event_details" @hidden="resetModal" title="Details" hide-footer>
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
                            <b-button variant="primary" @click="redirect_to_preventive(option.eq_id,option.state_id)">Record it</b-button>
                        </b-card>
                    </div>
                </div>
            </div>
            <b-button class="mt-3" block @click="closeAndClear()">Close</b-button>
        </b-modal>     
    </div>
</template>

<script>

export default {
    props:{
        prvMtnOps:{
            type:Array
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
            this.$router.replace({ name: "url_life_event_reference", params: {id:eq_id,state_id:state_id }, query: {type:"preventive"}})
        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8; 
    }

    

</style>
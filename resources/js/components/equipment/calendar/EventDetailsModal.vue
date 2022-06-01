<template>
    <div>
        <b-modal id="modal-event_details" title="Details" hide-footer>
            <div>
                <div v-for="option in prvMtnOps" :key="option.id">
                    <div>
                        <b-card>
                            <h3>{{option.eq_internalReference}}</h3>
                            <p class="card-text">
                                Number: {{option.prvMtnOp_description}}<br>
                                Description : {{option.prvMtnOp_description}}<br>
                                Protocol : {{option.prvMtnOp_protocol}}
                            </p>
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
            this.$bvModal.hide('modal-event_details'),
            this.$emit('modalClosed','')
        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8; 
    }

    

</style>
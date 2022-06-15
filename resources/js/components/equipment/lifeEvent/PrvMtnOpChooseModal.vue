<template>
    <div>
        <div v-if="prvMtnOps.length>0">
            <b-button v-b-modal.modal-1>Choose a preventive maintenance operation to realize</b-button>
        </div>
        <div v-else>
            <b-button v-b-modal.modal-1 disabled>Please inform or validate preventive maintenance operation before realizing one </b-button>
        </div>
        <b-modal id="modal-1" title="Choose a preventive maintenance operation to realize" hide-footer>
            <div>
                <b-button v-b-toggle.collapse-chooseOpe variant="primary" icon="chevron-bar-up" >Show details</b-button>
                <div v-for="option in prvMtnOps" :key="option.id">
                    <input type="radio" name="radio-input" :value="option" :id="option.id" v-model="radio_value"/>
                    {{ option.prvMtnOp_number }}
                    <div>
                        <b-collapse id="collapse-chooseOpe" class="mt-2">
                            <b-card>
                            <p class="card-text">
                                Description : {{option.prvMtnOp_description}}<br>
                                Protocol : {{option.prvMtnOp_protocol}}
                            </p>
                            </b-card>
                        </b-collapse>
                        </div>
                    </div>
            </div>
            <b-button class="mt-3" block @click="$bvModal.hide('modal-1')">Close</b-button>
            <b-button class="mt-3" block @click="chooseEquipment">Choose</b-button>
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
    data(){
        return{
            radio_value:''
        }
    },
    methods: {
        chooseEquipment(){
            if(this.radio_value!=''){
                this.$emit('choosedOpe',this.radio_value)

            }
            this.$bvModal.hide('modal-1')
        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8; 
    }

    

</style>
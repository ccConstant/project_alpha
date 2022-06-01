<template>
    <div>
        <b-button v-b-modal.modal-1 @click="importFromDB">Import From</b-button>
        <b-modal id="modal-1" title="Importation" hide-footer>
            <p class="my-4">Import from set : {{set}}</p>
            <div>
                <div v-if="imported_eq_ref.length>0">
                    <input v-model="searchTerm" type="text">
                    <div v-for="option in filterByTerm" :key="option.id">
                        <input type="radio" name="radio-input" :value="option" :id="option.eq_internalReference" v-model="radio_value"/>
                        {{ option.eq_internalReference }}
                    </div>
                </div>
                <div v-else>
                    <p>Nothing to import From</p>
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
        set:{
            type:String
        }
    },
    data(){
        return{
            imported_eq_ref: [],
            searchTerm: "",
            radio_value:''
        }
    },
    computed: {
        filterByTerm() {
            return this.imported_eq_ref.filter(option => {
                return option.eq_internalReference.toLowerCase().startsWith(this.searchTerm);
            });
        }
    },
    methods: {
        chooseEquipment(){
            if(this.radio_value!=''){
                this.$emit('choosedEq',this.radio_value)
            }
            this.$bvModal.hide('modal-1')
            
        },
        importFromDB(){
            var importUrl = (set) => `/equipments/same_set/${set}`;
            axios.get(importUrl(this.set))
            .then (response=> this.imported_eq_ref=response.data) 
            .catch(error => console.log(error)) ;
        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8; 
    }

    

</style>
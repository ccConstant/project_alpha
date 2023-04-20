<!--File name : MMEImportationModal.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to make an importation from another MME IDCard-->

<template>
    <div>
        <b-button v-b-modal.modal-mme_add_import @click="importFromDB">Import From</b-button>
        <b-modal id="modal-mme_add_import" title="Importation" hide-footer>
            <p class="my-4">Import from set : {{set}}</p>
            <div>
                <div v-if="imported_mme_ref.length>0">
                    <input v-model="searchTerm" type="text">
                    <div v-for="option in filterByTerm" :key="option.id">
                        <input type="radio" name="radio-input" :value="option" :id="option.mme_internalReference" v-model="radio_value"/>
                        {{ option.mme_internalReference }}
                    </div>
                </div>
                <div v-else>
                    <p>Nothing to import From</p>
                </div>
            </div>
            <b-button class="mt-3" block @click="$bvModal.hide('modal-mme_add_import')">Close</b-button>
            <b-button class="mt-3" block @click="chooseMME">Choose</b-button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props:{
        set:{
            type:String,
            default:null
        }
    },
    data(){
        return{
            imported_mme_ref: [],
            searchTerm: "",
            radio_value:''
        }
    },
    computed: {
        filterByTerm() {
            return this.imported_mme_ref.filter(option => {
                return option.mme_internalReference.toLowerCase().startsWith(this.searchTerm);
            });
        }
    },
    methods: {
        chooseMME(){
            if(this.radio_value!=''){
                this.$emit('choosedMME',this.radio_value)
            }
            this.$bvModal.hide('modal-mme_add_import')

        },
        importFromDB(){
            if(this.set!=null){
                const importUrl = (set) => `/mmes/same_set/${set}`;
                axios.get(importUrl(this.set))
                .then (response=> this.imported_mme_ref=response.data)
                .catch(error => console.log(error)) ;
            }

        }
    }

}
</script>

<style lang="scss">
    .modal-backdrop {
        opacity:0.8;
    }



</style>

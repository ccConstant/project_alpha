<template>
  <div>
        <MmeIdForm @MMEID="put_mme_id" @importFromMMEID="put_import_id"/>
        <div v-if="this.mme_id!=null">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                           MME Dimension
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAMMEFile :mme_id="this.mme_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            MME Verification
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceAMMEVerif :mme_id="this.mme_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            MME Usage
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <ReferenceAMMEUsage :mme_id="this.mme_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
</template>

<script>
import MmeIdForm from './MmeIdForm.vue'
import ReferenceAMMEFile from './ReferenceAMMEFile.vue'
import ReferenceAMMEVerif from './ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from './ReferenceAMMEUsage.vue'


export default {
    components: {
        MmeIdForm,
        ReferenceAMMEFile,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage

    },
    data(){
        return{
            //Id de l'mme qui vien detre crée
            mme_id:null,
            //Id de l'mme depuis lequel importer
            importation_id:null
        }
    },
    methods:{
        put_mme_id(value){
            this.mme_id=value;
        },
        put_import_id(value){
            this.importation_id=value;
        }
    }

}
</script>

<style>

</style>
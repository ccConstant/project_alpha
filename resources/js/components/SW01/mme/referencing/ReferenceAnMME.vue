<!--File name : ReferenceAnMME.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference an imported MME in another MME-->

<template>
    <div>
        <MmeIdForm @MMEID="put_mme_id" @importFromMMEID="put_import_id"/>
        <div v-if="this.mme_id!=null">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            MME File
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAMMEFile :import_id="this.importation_id" :mme_id="this.mme_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            MME Verification
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMMEVerif :import_id="this.importation_id" :mme_id="this.mme_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            MME Usage
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMMEUsage :import_id="this.importation_id" :mme_id="this.mme_id"/>
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
    data() {
        return {
            //ID of the MME to reference
            mme_id: null,
            //ID of the imported MME to reference
            importation_id: null
        }
    },
    methods: {
        put_mme_id(value) {
            this.mme_id = value;
        },
        put_import_id(value) {
            this.importation_id = value;
        }
    }
}
</script>

<style>

</style>

<!--File name : ReferenceAnArticleFamily.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div>
        <ArticleFamilyForm @ArtFamID="put_artFamily_id" @ArtFamType="put_artFamily_type" @ArtFamRef="put_artFamily_ref" @ArtFamSubFam="put_artFamily_subFam"/>

        <div v-if="this.artFam_id!=null">
            <div class="accordion">
                <div class="accordion-item" v-if="artFam_subFam">
                    <h2 class="accordion-header" id="headingZero">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseZero" aria-expanded="true" aria-controls="collapseZero">
                            Article Sub Family
                        </button>
                    </h2>
                    <div id="collapseZero" class="accordion-collapse collapse show" aria-labelledby="headingZero">
                        <div class="accordion-body">
                            <ReferenceASubFamilyForm
                                modifMod
                                :artFam_type="this.artFam_type"
                                :artFam_id="this.artFam_id"
                                :artFam_ref="this.artFam_ref"
                            />
                        </div>
                    </div>
                </div>
                <div class="accordion-item" v-if="!artFam_subFam">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            Criticality
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceACrit
                                @checkedTests="createTest"
                                modifMod
                                :articleType="this.artFam_type"
                                :article_id="this.artFam_id"
                                :import_id="this.artFam_id"
                            />
                        </div>
                    </div>
                </div>
                <div v-if="this.checkedTest!=null && this.checkedTest.length!=0 && !artFam_subFam" class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                Article Purchase Specification
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree">
                            <div class="accordion-body">
                                <ReferenceAnArticlePurchaseSpecification
                                    modifMod
                                    :artType="this.artFam_type"
                                    :artFam_id="this.artFam_id"
                                    :import_id="this.artFam_id"
                                    :checkedTest="this.checkedTest"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion" v-if="this.checkedTest!=null && this.checkedTest.length!=0 && !artFam_subFam">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                Incoming Inspection
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour">
                            <div class="accordion-body">
                                <ReferenceAnIncmgInsp
                                    modifMod
                                    :articleType="this.artFam_type"
                                    :article_id="this.artFam_id"
                                    :import_id="this.artFam_id"
                                    :checkedTest="this.checkedTest"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion" v-if="!artFam_subFam">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                Article Storage Condition
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive">
                            <div class="accordion-body">
                                <ReferenceAStorageCondition
                                    modifMod
                                    :artType="this.artFam_type"
                                    :artFam_id="this.artFam_id"
                                    :import_id="this.artFam_id"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ArticleFamilyForm from "./ArticleFamilyForm";
import ReferenceAnArticleFamilyMember from "./ReferenceAnArticleFamilyMember.vue";
import ReferenceAStorageCondition from "./ReferenceAStorageCondition";
import ReferenceAnIncmgInsp from "../../incInsp/referencing/ReferenceAnIncmgInsp.vue";
import ReferenceACrit from "../../criticality/referencing/ReferenceACrit.vue";
import ReferenceAnArticlePurchaseSpecification from "./ReferenceAnArticlePurchaseSpecification";
import ReferenceASubFamilyForm from "./ReferenceASubFamilyForm";

export default {
    components: {
        ReferenceACrit,
        ReferenceAnIncmgInsp,
        ArticleFamilyForm,
        ReferenceAStorageCondition,
        ReferenceAnArticleFamilyMember,
        ReferenceAnArticlePurchaseSpecification,
        ReferenceASubFamilyForm,
    },
    data() {
        return {
            /*ID of the equipment created*/
            artFam_id: null,
            artFam_type: null,
            artFam_ref: null,
            artFam_subFam: null,
            checkedTest: [],
        }
    },
    methods: {
        put_artFamily_id(value) {
            this.artFam_id = value;
        },
        put_artFamily_type(value) {
            this.artFam_type = value;
        },
        put_artFamily_ref(value) {
            this.artFam_ref = value;
        },
        put_artFamily_subFam(value) {
            this.artFam_subFam = value;
        },
        createTest(value) {
            this.checkedTest = value;
        },
    },
    created(){
    }
}
</script>

<style>
</style>


<!--File name : ReferenceAnArticleFamily.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div>
        <ArticleFamilyForm @ArtFamID="put_artFamily_id" @ArtFamType="put_artFamily_type"   @ArtFamRef="put_artFamily_ref" @generic="genericSetter"/>
        
        <div v-if="this.artFam_id!=null">
            <div class="accordion">
                <div class="accordion-item">
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
                                :import_id="this.artFam_id"
                                :artFam_ref="this.artFam_ref"
                            />
                        </div>
                    </div>
                </div>
                <div class="accordion-item" v-if="generic !== null">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Article Family Member
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAnArticleFamilyMember
                                modifMod
                                :artType="this.artFam_type"
                                :artFam_id="this.artFam_id"
                                :import_id="this.artFam_id"
                            />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            Criticality
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceACrit
                                modifMod
                                :articleType="this.artFam_type"
                                :article_id="this.artFam_id"
                                :import_id="this.artFam_id"
                            />
                        </div>
                    </div>
                </div>
                <div class="accordion">
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
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion">
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
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion">
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
            generic: null,
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
        genericSetter(ref, design, variableCharac, variableCharacDesign) {
            this.generic = {
                variablesCharac: variableCharac,
                variablesCharacDesign: variableCharacDesign,
                genRef: ref,
                genDesign: design,
            };
        }
    }
}
</script>

<style>
</style>


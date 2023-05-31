<!--File name : ArticleConsultV1.vue-->
<!--Creation date : 25 May 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the consultation sheet of an article-->

<template>
    <div v-if="loaded===false" >
        <b-spinner variant="primary"></b-spinner>
    </div>
    <div v-else class="supplier_consultation">
        <ErrorAlert ref="errorAlert"/>
        <SuccessAlert ref="successAlert"/>
        <h1>Article Consultation</h1>
        <!--        <ValidationButton @ValidatePressed="Validate" :eq_id="eq_id" :validationMethod="validationMethod" :Errors="errors"/>-->
        <ArticleFamilyForm
            :reference="article.ref"
            :designation="article.design"
            :drawingPath="article.drawingPath"
            :purchasedBy="article.purchasedBy"
            :variablesCharac="article.variablesCharac"
            :version="article.version"
            :type="articleType.toUpperCase()"
            :active="article.active === 1"
            :mainRef="article.mainRef"
            consultMod
        />
        <div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Article Storage Condition
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAStorageCondition
                                consultMod
                                :artType="this.articleType"
                                :artFam_id="this.articleID"
                                :import_id="this.articleID"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            Article Purchase Specification
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceAnArticlePurchaseSpecification
                                consultMod
                                :artType="this.articleType"
                                :artFam_id="this.articleID"
                                :import_id="this.articleID"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                            Incoming Inspection
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <ReferenceAnIncmgInsp
                                consultMod
                                :articleType="this.articleType"
                                :article_id="this.articleID"
                                :import_id="this.articleID"
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
                            Criticality
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <ReferenceACrit
                                consultMod
                                :articleType="this.articleType"
                                :article_id="this.articleID"
                                :import_id="this.articleID"
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
                            Article Family Member
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <ReferenceAnArticleFamilyMember
                                consultMod
                                :artType="this.articleType"
                                :artFam_id="this.articleID"
                                :import_id="this.articleID"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import ErrorAlert from "../../../alert/ErrorAlert.vue";
import SuccessAlert from "../../../alert/SuccesAlert.vue";
import ValidationButton from "../../../button/ValidationButton.vue";
import ArticleFamilyForm from "../referencing/ArticleFamilyForm.vue";
import ReferenceAnIncmgInsp from "../../incInsp/referencing/ReferenceAnIncmgInsp.vue";
import ReferenceACrit from "../../criticality/referencing/ReferenceACrit.vue";
import ReferenceAnArticleFamilyMember from "../referencing/ReferenceAnArticleFamilyMember.vue";
import ReferenceAnArticlePurchaseSpecification from "../referencing/ReferenceAnArticlePurchaseSpecification.vue";
import ReferenceAStorageCondition from "../referencing/ReferenceAStorageCondition.vue";
export default {
    components: {
        ReferenceAStorageCondition,
        ReferenceAnArticlePurchaseSpecification,
        ReferenceAnArticleFamilyMember,
        ReferenceACrit,
        ReferenceAnIncmgInsp,
        ArticleFamilyForm,
        ValidationButton,
        SuccessAlert,
        ErrorAlert
    },
    data() {
        return {
            articleID: Number(this.$route.params.id),
            articleType: this.$route.params.type,
            article: null,
            validationMethod: this.$route.query.method,
            errors: [],
            loaded: false
        }
    },
    created() {
        if(this.validationMethod === 'technical' && this.$userId.user_makeTechnicalValidationRight !== true){
            this.$router.replace({ name: "url_eq_list"})
        }
        if (this.validationMethod === 'quality' && this.$userID.user_makeQualityValidationRight !== true){
            this.$router.replace({ name: "url_eq_list"})
        }
        if (this.articleType === 'raw') {
            axios.get('/raw/family/send/' + this.articleID)
                .then(response => {
                    this.article = {
                        ref: response.data.rawFam_ref,
                        design: response.data.rawFam_design,
                        drawingPath: response.data.rawFam_drawingPath,
                        nbrVersion: response.data.rawFam_nbrVersion,
                        variablesCharac: response.data.rawFam_variablesCharac,
                        active: response.data.rawFam_active,
                        purchasedBy: response.data.rawFam_purchasedBy,
                        mainRef: response.data.rawFam_mainRef,
                        version: null
                    };
                    this.loaded = true;
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.message);
                });
        } else if (this.articleType === 'comp') {
            axios.get('/comp/family/send/' + this.articleID)
                .then(response => {
                    this.article = {
                        ref: response.data.compFam_ref,
                        design: response.data.compFam_design,
                        drawingPath: response.data.compFam_drawingPath,
                        nbrVersion: response.data.compFam_nbrVersion,
                        variablesCharac: response.data.compFam_variablesCharac,
                        active: response.data.compFam_active,
                        purchasedBy: response.data.compFam_purchasedBy,
                        mainRef: response.data.compFam_mainRef,
                        version: response.data.compFam_version
                    };
                    this.loaded = true;
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.message);
                });
        } else if (this.articleType === 'cons') {
            axios.get('/cons/family/send/' + this.articleID)
                .then(response => {
                    this.article = {
                        ref: response.data.consFam_ref,
                        design: response.data.consFam_design,
                        drawingPath: response.data.consFam_drawingPath,
                        nbrVersion: response.data.consFam_nbrVersion,
                        variablesCharac: response.data.consFam_variablesCharac,
                        active: response.data.consFam_active,
                        purchasedBy: response.data.consFam_purchasedBy,
                        version: response.data.consFam_version,
                        mainRef: response.data.consFam_mainRef
                    };
                    this.loaded = true;
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.message);
                });
        }
    },
    methods: {
        validate() {
            if(this.validationMethod === 'technical' && this.$userId.user_makeTechnicalValidationRight !== true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else if(this.validationMethod === 'quality' && this.$userId.user_makeQualityValidationRight !== true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                // TODO: Validation
            }
        }
    }
}
</script>

<style>

</style>

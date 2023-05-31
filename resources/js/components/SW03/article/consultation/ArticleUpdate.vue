<template>
    <div v-if="loaded===false">
        <b-spinner variant="primary"></b-spinner>
    </div>
    <div v-else class="supplier_consultation">
        <ErrorAlert ref="errorAlert"/>
        <SuccessAlert ref="successAlert"/>
        <h1>Article Update</h1>
        <!--        <ValidationButton @ValidatePressed="Validate" :eq_id="eq_id" :validationMethod="validationMethod" :Errors="errors"/>-->
        <ArticleFamilyForm
            :active="article.active === 1"
            :designation="article.design"
            :drawingPath="article.drawingPath"
            :genDesign="article.genDesign"
            :genRef="article.genRef"
            :mainRef="article.mainRef"
            :purchasedBy="article.purchasedBy"
            :reference="article.ref"
            :type="articleType.toUpperCase()"
            :variablesCharac="article.variablesCharac"
            :variablesCharacDesign="article.variablesCharacDesign"
            :version="article.version"
            modifMod
            @generic="genericSetter"
        />
        <div class="accordion">
            <div class="accordion-item">
                <h2 id="headingOne" class="accordion-header">
                    <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                            data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                        Article Sub Family
                    </button>
                </h2>
                <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <ReferenceAnArticleSubFamily
                            :artFam_id="this.articleID"
                            :artType="this.articleType"
                            :genDesign="this.generic.genDesign"
                            :genRef="this.generic.genRef"
                            :import_id="this.articleID"
                            :varCharac="this.generic.variablesCharac"
                            :varCharacDesign="this.generic.variablesCharacDesign"
                            modifMod
                        />
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 id="headingTwo" class="accordion-header">
                    <button aria-controls="collapseTwo" aria-expanded="true" class="accordion-button"
                            data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                        Criticality
                    </button>
                </h2>
                <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <ReferenceACrit
                            :articleType="this.articleType"
                            :article_id="this.articleID"
                            :import_id="this.articleID"
                            modifMod
                        />
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            Article Purchase Specification
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAnArticlePurchaseSpecification
                                :artFam_id="this.articleID"
                                :artType="this.articleType"
                                :import_id="this.articleID"
                                modifMod
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingFour" class="accordion-header">
                        <button aria-controls="collapseFour" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                            Incoming Inspection
                        </button>
                    </h2>
                    <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAnIncmgInsp
                                :articleType="this.articleType"
                                :article_id="this.articleID"
                                :import_id="this.articleID"
                                modifMod
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingFive" class="accordion-header">
                        <button aria-controls="collapseFive" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseFive" data-bs-toggle="collapse" type="button">
                            Article Storage Condition
                        </button>
                    </h2>
                    <div id="collapseFive" aria-labelledby="headingFive" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAStorageCondition
                                :artFam_id="this.articleID"
                                :artType="this.articleType"
                                :import_id="this.articleID"
                                modifMod
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
import ReferenceAnArticleSubFamily from "../referencing/ReferenceAnArticleSubFamily.vue";
import ReferenceAnArticlePurchaseSpecification from "../referencing/ReferenceAnArticlePurchaseSpecification.vue";
import ReferenceAStorageCondition from "../referencing/ReferenceAStorageCondition.vue";

export default {
    components: {
        ReferenceAStorageCondition,
        ReferenceAnArticlePurchaseSpecification,
        ReferenceAnArticleSubFamily,
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
            article: [],
            validationMethod: this.$route.query.method,
            errors: [],
            loaded: false,
            generic: null,
        }
    },
    created() {
        if (this.validationMethod === 'technical' && this.$userId.user_makeTechnicalValidationRight !== true) {
            this.$router.replace({name: "article_url_list"})
        }
        if (this.validationMethod === 'quality' && this.$userID.user_makeQualityValidationRight !== true) {
            this.$router.replace({name: "article_url_list"})
        }
        axios.get('/' + this.articleType + '/family/send/' + this.articleID)
            .then(response => {
                this.article = {
                    ref: response.data.rawFam_ref,
                    design: response.data.rawFam_design,
                    drawingPath: response.data.rawFam_drawingPath,
                    nbrVersion: response.data.rawFam_nbrVersion,
                    variablesCharac: response.data.rawFam_variablesCharac,
                    variablesCharacDesign: response.data.rawFam_variablesCharacDesign,
                    active: response.data.rawFam_active,
                    purchasedBy: response.data.rawFam_purchasedBy,
                    version: null,
                    genRef: response.data.rawFam_genRef,
                    genDesign: response.data.rawFam_genDesign,
                    supplier: response.data.supplr_id,
                    mainRef: response.data.rawFam_mainRef,
                };
                this.generic = {
                    variablesCharac: this.article.variablesCharac,
                    variablesCharacDesign: this.article.variablesCharacDesign,
                    genRef: this.article.genRef,
                    genDesign: this.article.genDesign,
                };
                this.$router.push({
                    name: 'article_url_update',
                    params: {id: this.articleID, type: this.articleType, generic: this.generic},
                    query: {signed: response.data.rawFam_signatureDate != null}
                });
                this.loaded = true;
            })
            .catch(error => {
                console.log(error.response.data);
                this.$refs.errorAlert.showAlert(error.response.data.message);
            });
    },
    methods: {
        genericSetter(ref, design, variableCharac, variableCharacDesign) {
            this.generic = {
                variablesCharac: variableCharac,
                variablesCharacDesign: variableCharacDesign,
                genRef: ref,
                genDesign: design,
            };
        },
    }
}
</script>

<style>

</style>

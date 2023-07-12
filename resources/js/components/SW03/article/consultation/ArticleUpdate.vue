<!--File name : ArticleUpdate.vue -->
<!--Creation date : 25 May 2023-->
<!--Update date : 5 Jul 2023-->
<!--Vue Component of the update sheet of an article-->


<template>
    <div v-if="loaded===false" >
        <b-spinner variant="primary"></b-spinner>
    </div>
    <div v-else class="supplier_consultation">
        <ErrorAlert ref="errorAlert"/>
        <SuccessAlert ref="successAlert"/>
        <h1>Article Update</h1>
        <!--        <ValidationButton @ValidatePressed="Validate" :eq_id="eq_id" :validationMethod="validationMethod" :Errors="errors"/>-->
        <ArticleFamilyForm
            :reference="article.ref"
            :designation="article.design"
            :drawingPath="article.drawingPath"
            :purchasedBy="article.purchasedBy"
            :version="article.version"
            :type="articleType.toUpperCase()"
            :ref="articleRef"
            :active="article.active === 1"
            :validate="article.validate"
            :subFam="article.subFam === 1"
            modifMod
            @ArtFamSubFam="put_artFamily_subFam"
        />
        <div class="accordion">
            <div class="accordion-item" v-if="article.subFam">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Article Sub Family
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <ReferenceASubFamily
                            modifMod
                            :artFam_type="this.articleType"
                            :artFam_id="this.articleID"
                            :import_id="this.articleID"
                            :artFam_ref="this.article.ref"
                        />
                    </div>
                </div>
            </div>
            <div class="accordion-item" v-if="!article.subFam">
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
                            :articleType="this.articleType"
                            :article_id="this.articleID"
                            :import_id="this.articleID"
                        />
                    </div>
                </div>
            </div>
            <div class="accordion" v-if="this.checkedTest!=null && this.checkedTest.length!=0 && !article.subFam">
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
                                :artType="this.articleType"
                                :artFam_id="this.articleID"
                                :import_id="this.articleID"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" v-if="this.checkedTest!=null && this.checkedTest.length!=0 && !article.subFam">
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
                                :articleType="this.articleType"
                                :article_id="this.articleID"
                                :import_id="this.articleID"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" v-if="!article.subFam">
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
import ReferenceASubFamily from "../referencing/ReferenceASubFamilyForm.vue";
import ReferenceAnArticlePurchaseSpecification from "../referencing/ReferenceAnArticlePurchaseSpecification.vue";
import ReferenceAStorageCondition from "../referencing/ReferenceAStorageCondition.vue";
export default {
    components: {
        ReferenceAStorageCondition,
        ReferenceAnArticlePurchaseSpecification,
        ReferenceASubFamily,
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
            articleType: this.$route.params.type.toLowerCase(),
            articleRef: this.$route.params.ref,
            article: [],
            validationMethod: this.$route.query.method,
            errors: [],
            loaded: false,
            generic: null,
            checkedTest: [],
        }
    },
    created() {
        if(this.validationMethod === 'technical' && this.$userId.user_makeTechnicalValidationRight !== true){
            this.$router.replace({ name: "article_url_list"})
        }
        if (this.validationMethod === 'quality' && this.$userID.user_makeQualityValidationRight !== true){
            this.$router.replace({ name: "article_url_list"})
        }
        if (this.articleType === 'raw') {
            axios.get('/raw/family/send/' + this.articleID)
                .then(response => {
                    this.article = {
                        ref: response.data.rawFam_ref,
                        design: response.data.rawFam_design,
                        drawingPath: response.data.rawFam_drawingPath,
                        nbrVersion: response.data.rawFam_nbrVersion,
                        active: response.data.rawFam_active,
                        purchasedBy: response.data.rawFam_purchasedBy,
                        version: null,
                        supplier: response.data.supplr_id,
                        validate: response.data.rawFam_validate,
                        subFam: response.data.rawFam_subFam,
                    };
                    this.$router.replace({name: 'article_url_update', params: {id: this.articleID, type: this.articleType.toLowerCase(), ref:this.article.ref}, query: {signed : response.data.rawFam_signatureDate != null}});
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
                        active: response.data.compFam_active,
                        purchasedBy: response.data.compFam_purchasedBy,
                        version: response.data.compFam_version,
                        supplier: response.data.supplr_id,
                        validate: response.data.compFam_validate,
                        subFam: response.data.compFam_subFam,
                    };
                    this.$router.replace({name: 'article_url_update', params: {id: this.articleID, type: this.articleType.toLowerCase(),ref:this.article.ref}, query: {signed : response.data.compFam_signatureDate != null}});
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
                        active: response.data.consFam_active,
                        purchasedBy: response.data.consFam_purchasedBy,
                        version: response.data.consFam_version,
                        supplier: response.data.supplr_id,
                        validate: response.data.consFam_validate,
                        subFam: response.data.consFam_subFam,
                    };
                    this.$router.replace({name: 'article_url_update', params: {id: this.articleID, type: this.articleType.toLowerCase(), ref:this.articleRef}, query: {signed : response.data.consFam_signatureDate != null}});
                    this.loaded = true;
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.message);
                });
        }
    },
    methods: {
        put_artFamily_subFam(value) {
            this.article.subFam = value;
        },
        createTest(value) {
            this.checkedTest = value;
        },
    }
}
</script>

<style>

</style>

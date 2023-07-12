<!--File name :ReferenceAnArticlePurchaseSpecification-->
<!--Creation date : 2 May 2023 -->
<!--Update date : 2 May 2023-->
<!--Vue Component used to reference a storage condition in the article-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="articlePurchaseSpecification">
            <h2 v-if="components.length>0" class="titleForm">Article Purchase Specification(s) </h2>
            <InputInfo v-if="title_info != null" :info="title_info.info_value" class="info_title"/>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingDocControl" class="accordion-header">
                        <button aria-controls="collapseDocControl" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseDocControl" data-bs-toggle="collapse"
                                type="button">
                            Documentary Control
                        </button>
                    </h2>
                    <div id="collapseDocControl" aria-labelledby="headingDocControl"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceADocControl
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :articleSubFam_id="data_artSubFam_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :docControl_name="data_docControlName"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingAspTest" class="accordion-header">
                        <button aria-controls="collapseAspTest" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseAspTest" data-bs-toggle="collapse"
                                type="button">
                            Aspect Test
                        </button>
                    </h2>
                    <div id="collapseAspTest" aria-labelledby="headingAspTest"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAnAspTest
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :articleSubFam_id="data_artSubFam_id"
                            />
                        </div>
                    </div>
                </div>
                <!-- FuncTest -->
                <div v-if="data_art_type === 'comp'" class="accordion-item">
                    <h2 id="headingFuncTest" class="accordion-header">
                        <button aria-controls="collapseFuncTest" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseFuncTest" data-bs-toggle="collapse"
                                type="button">
                            Functional Test
                        </button>
                    </h2>
                    <div id="collapseFuncTest" aria-labelledby="headingFuncTest"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAFuncTest
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :articleSubFam_id="data_artSubFam_id"
                            />
                        </div>
                    </div>
                </div>

                <!-- DimTest -->
                <div v-if="data_art_type === 'comp' || data_art_type === 'raw'" class="accordion-item">
                    <h2 id="headingDimTest" class="accordion-header">
                        <button aria-controls="collapseDimTest" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseDimTest" data-bs-toggle="collapse"
                                type="button">
                            Dimensional Test
                        </button>
                    </h2>
                    <div id="collapseDimTest" aria-labelledby="headingDimTest"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceADimTest
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :articleSubFam_id="data_artSubFam_id"
                            />
                        </div>
                    </div>

                </div>
                <div class="accordion-item">
                    <h2 id="headingAdmTest" class="accordion-header">
                        <button aria-controls="collapseAdmTest" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseAdmTest" data-bs-toggle="collapse"
                                type="button">
                            Administrative Control
                        </button>
                    </h2>
                    <div id="collapseAdmTest" aria-labelledby="headingAdmTest"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceAnAdminControl
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :articleSubFam_id="data_artSubFam_id"
                            />
                        </div>
                    </div>
                </div>
                <!-- CompTest -->
                <div v-if="data_art_type === 'cons'" class="accordion-item">
                    <h2 id="headingCompTest" class="accordion-header">
                        <button aria-controls="collapseCompTest" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseCompTest" data-bs-toggle="collapse"
                                type="button">
                            Complementary Test
                        </button>
                    </h2>
                    <div id="collapseCompTest" aria-labelledby="headingCompTest"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ReferenceACompTest
                                :articleType="data_art_type"
                                :article_id="data_art_id"
                                :checkedTest="this.data_checkedTest"
                                :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                :articleSubFam_id="data_artSubFam_id"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
            <!--ref="ask_dim_data" is used to call the child elements in this component-->
            <!--The emitted deleteDim is caught here and call the function getContent -->
            <ArticlePurchaseSpecificationForm
                :is="component.comp"
                v-for="(component, key) in components"
                :id="component.id"
                :key="component.key"
                ref="ask_purchaseSpecification_data"
                :art_id="data_art_id"
                :art_type="data_art_type"
                :checkedTest="data_checkedTest"
                :consultMod="isInConsultMod"
                :divClass="component.className"
                :modifMod="component.id !== null"
                :supplier_id="component.supplier"
                :supplier_ref="component.supplierRef"
                :remarks="component.remarks"
                @deleteStorageCondition="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another dimension button appear -->
                <button v-on:click="addComponent">Add Supplier Specification</button>
            </div>
<!--            <SaveButtonForm
                v-if="components.length>1"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
                saveAll
            />-->
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticlePurchaseSpecificationForm from './ArticlePurchaseSpecificationForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'
import ReferenceACompTest from "../../incInsp/referencing/ReferenceACompTest.vue";
import ReferenceAFuncTest from "../../incInsp/referencing/ReferenceAFuncTest.vue";
import ReferenceADocControl from "../../incInsp/referencing/ReferenceADocControl.vue";
import ReferenceAnAspTest from "../../incInsp/referencing/ReferenceAnAspTest.vue";
import ReferenceADimTest from "../../incInsp/referencing/ReferenceADimTest.vue";
import ReferenceAnAdminControl from "../../incInsp/referencing/ReferenceAnAdminControl.vue";


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ReferenceAnAdminControl,
        ReferenceADimTest,
        ReferenceAnAspTest,
        ReferenceADocControl,
        ReferenceAFuncTest,
        ReferenceACompTest,
        ArticlePurchaseSpecificationForm,
        SaveButtonForm,
        InputInfo


    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedDim: All dimensions imported from the database
        eq_id: ID of the equipment in which the dimension will be added
    ---------------------------------------------------*/
    props: {
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        artFam_id: {
            type: Number,
            default: null
        },
        artType: {
            type: String
        },
        import_id: {
            type: Number,
            default: null
        },
        checkedTest: {
            type: Array,
            default: null
        },
        articleSubFam_id: {
            type: Number
        },
        docControl_name: {
            type: String
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_art_id: data of the art id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_art_id: this.artFam_id,
            all_storageCondition_validate: [],
            title_info: null,
            data_art_type: this.artType.toLowerCase(),
            loaded: false,
            purchaseSpec: null,
            data_checkedTest: this.checkedTest,
            data_docControlName: this.docControl_name,
            data_artSubFam_id: this.articleSubFam_id
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'ArticlePurchaseSpecificationForm',
                key: this.uniqueKey++,
                id: null
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(
            purchaseSpecification_supplier,
            purchaseSpecification_supplierRef,
            purchaseSpecification_remark, purchaseSpecification_className, id) {
            this.components.push({
                comp: 'ArticlePurchaseSpecificationForm',
                key: this.uniqueKey++,
                supplier: purchaseSpecification_supplier,
                supplierRef: purchaseSpecification_supplierRef,
                className: purchaseSpecification_className,
                remark: purchaseSpecification_remark,
                id: id
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        importPurSpe() {
            if (this.purchaseSpec.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.purchaseSpec) {
                    const className = "importedPurSpe" + dt.id;
                    this.addImportedComponent(
                        dt.purSpe_supplier_id,
                        dt.purSpe_supplier_ref,
                        dt.purSpe_remarks,
                        className,
                        dt.id
                    );
                }
                this.criticality = null
            }
        },

        /*Function for saving all the data in one time*/
        /*saveAll(savedAs) {
            for (const component of this.$refs.ask_dim_data) {
                /*If the user is in modification mode
                if (this.modifMod == true) {
                    /*If the dimension doesn't have, an id
                    if (component.dim_id == null) {
                        /*AddequipmentDim is used
                        component.addEquipmentDim(savedAs);
                    } else
                        /*Else if the dimension has an id and addSucces is equal to true
                    if (component.dim_id != null || component.addSucces == true) {
                        /*updateEquipmentDim is used
                        if (component.dim_validate !== "validated") {
                            component.updateEquipmentDim(savedAs);
                        }

                    }
                } else {
                    /*Else If the user is not in modification mode
                    component.addEquipmentDim(savedAs);
                }
            }
        }*/
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        console.log("ref an article purchase spec")
        console.log(this.data_docControlName)
        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            if (this.data_article_id !== null) {
                axios.get('/artFam/purSpe/send/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        this.purchaseSpec = response.data;
                        this.importPurSpe();
                        this.loaded = true;
                    }).catch(error => {
                });
            } else {
                axios.get('/artSubFam/purSpe/send/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        this.purchaseSpec = response.data;
                        this.importPurSpe();
                        this.loaded = true;
                    }).catch(error => {
                });
            }
            /*axios.get('/purSpe/send/' + this.data_art_type + '/' + this.import_id)
                .then(response => {
                    this.purchaseSpec = response.data;
                    this.importPurSpe();
                    this.loaded = true;
                })
                .catch(error => {
                });*/
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importDim();
        }*/
    }
}
</script>

<style>

.info_title {
    position: relative;
}

.title {
    display: inline-block;
}

</style>

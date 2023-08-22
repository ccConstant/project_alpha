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
            <h2 v-if="components.length>0 || componentsCommon.length>0" class="titleForm">Article Purchase Specification(s) </h2>
            <InputInfo v-if="title_info != null" :info="title_info.info_value" class="info_title"/>
            
            <div v-if="!first || first" >
            <ArticlePurchaseSpecificationCommonForm
                :is="component.comp"
                v-for="(component, key) in componentsCommon"
                :id="component.id"
                :key="component.key"
                ref="ask_purchaseSpecification_data"
                :art_id="data_art_id"
                :art_type="data_art_type"
                :consultMod="isInConsultMod"
                :divClass="component.className"
                :modifMod="component.id !== null"
                :specification="component.specification"
                :documentsRequest="component.documentsRequest"
                :articleSubFam_id="data_artSubFam_id"
                @deletePurSpeCommon="getContentCommon(key)"
                @first="firstData()"
            />
            </div>
            <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
            <!--ref="ask_dim_data" is used to call the child elements in this component-->
            <!--The emitted deleteDim is caught here and call the function getContent -->
            <div v-if="!first || this.modifMod || this.isInConsultMod" >
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
                :supplier_ref="component.supplier_ref"
                :remark="component.remark"
                :specification="component.specification"
                :documentsRequest="component.documentsRequest"
                :articleSubFam_id="data_artSubFam_id"
                @deletePurSpe="getContent(key)"
            />
            </div>
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
import ArticlePurchaseSpecificationCommonForm from "./ArticlePurchaseSpecificationCommonForm.vue";


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
        ArticlePurchaseSpecificationCommonForm,
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
        },
        addAuto:{
            type: Boolean,
            default: true
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
            componentsCommon:[],
            uniqueKey: 0,
            count: 0,
            first: true,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_art_id: this.artFam_id,
            all_storageCondition_validate: [],
            title_info: null,
            data_art_type: this.artType.toLowerCase(),
            loaded: false,
            purchaseSpec: null,
            purchaseSpecCommon: null,
            data_checkedTest: this.checkedTest,
            data_docControlName: this.docControl_name,
            data_artSubFam_id: this.articleSubFam_id
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            if (this.first){
                this.componentsCommon.push({
                    comp: 'ArticlePurchaseSpecificationCommonForm',
                    key: this.uniqueKey++,
                    id: null
                });
            }else{
                this.components.push({
                    comp: 'ArticlePurchaseSpecificationForm',
                    key: this.uniqueKey++,
                    id: null
                });
            }
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(purchaseSpecification_supplier,
            purchaseSpecification_supplierRef,
            purchaseSpecification_remark,purchaseSpecification_className, id) {
            this.components.push({
                comp: 'ArticlePurchaseSpecificationForm',
                key: this.uniqueKey++,
                supplier: purchaseSpecification_supplier,
                supplier_ref: purchaseSpecification_supplierRef,
                className: purchaseSpecification_className,
                remark: purchaseSpecification_remark,
                id: id
            });
        },
        addImportedComponentCommon(
            purchaseSpecifications_doc, purchaseSpecifications_spec,purchaseSpecification_className, id) {
            this.componentsCommon.push({
                comp: 'ArticlePurchaseSpecificationCommonForm',
                key: this.uniqueKey++,
                specification: purchaseSpecifications_spec,
                documentsRequest: purchaseSpecifications_doc,
                className: purchaseSpecification_className,
                id: id
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        getContentCommon(key) {
            this.componentsCommon.splice(key, 1);
        },
        firstData(){
            this.first=false;
            if (!this.modifMod){
                    this.components.push({
                    comp: 'ArticlePurchaseSpecificationForm',
                    key: this.uniqueKey++,
                    id: null
                });
            }

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
                        dt.purSpe_remark,
                        className,
                        dt.id
                    );
                }
                this.criticality = null
            }
        },

        importPurSpeCommon() {
            for (const dt of this.purchaseSpecCommon) {
                if (dt.purSpe_documentsRequested!=null || dt.purSpe_specifications!=null) {
                    this.first=false;
                    const className = "importedPurSpeCommon" + dt.id;
                    this.addImportedComponentCommon(
                        dt.purSpe_documentsRequested,
                        dt.purSpe_specifications,
                        className,
                        dt.id
                    );
                }
            }
            this.criticality = null
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

        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            
            if (this.data_art_id !== null) {
                if (this.modifMod){
                    axios.get('/artFam/purSpeCommon/send/' + this.data_art_type + '/' + this.import_id)
                        .then(response => {
                            this.purchaseSpecCommon = response.data;
                            this.importPurSpeCommon();
                        }).catch(error => {
                    });
                }
                axios.get('/artFam/purSpe/send/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        this.purchaseSpec = response.data;
                        this.importPurSpe();
                        this.loaded = true;
                    }).catch(error => {
                });
            } else {
                console.log("else")
                console.log(this.modifMod)
                if (this.modifMod){
                    axios.get('/artSubFam/purSpeCommon/send/' + this.data_art_type + '/' + this.data_artSubFam_id)
                        .then(response => {
                            this.purchaseSpecCommon = response.data;
                            console.log(this.purchaseSpecCommon)
                            this.importPurSpeCommon();
                        }).catch(error => {
                    });
                }
                console.log("other")
                console.log(this.import_id)
                axios.get('/artSubFam/purSpe/send/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        console.log(response.data)
                        this.purchaseSpec = response.data;
                        console.log(this.purchaseSpec)
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

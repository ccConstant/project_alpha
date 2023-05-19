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
            <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info != null"/>
            <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
            <!--ref="ask_dim_data" is used to call the child elements in this component-->
            <!--The emitted deleteDim is caught here and call the function getContent -->
            <ArticlePurchaseSpecificationForm
                ref="ask_purchaseSpecification_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :requiredDoc="component.requiredDoc"
                :supplier_id="component.supplier"
                :supplier_ref="component.supplierRef"
                :divClass="component.className"
                :id="component.id"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :art_type="data_art_type"
                :art_id="data_art_id"
                @deleteStorageCondition="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another dimension button appear -->
                <button v-on:click="addComponent">Add</button>
            </div>
            <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                            :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticlePurchaseSpecificationForm from './ArticlePurchaseSpecificationForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
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
            type: Number
        },
        artType: {
            type: String
        },
        import_id: {
            type: Number,
            default: null
        },
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
            purchaseSpec: null
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
            purchaseSpecification_requiredDoc,
            purchaseSpecification_supplier,
            purchaseSpecification_supplierRef,
            purchaseSpecification_className, id) {
            this.components.push({
                comp: 'ArticlePurchaseSpecificationForm',
                key: this.uniqueKey++,
                requiredDoc: purchaseSpecification_requiredDoc,
                supplier: purchaseSpecification_supplier,
                supplierRef: purchaseSpecification_supplierRef,
                className: purchaseSpecification_className,
                id: id
            });},
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
                        dt.purSpe_requiredDoc,
                        dt.purSpe_supplier_id,
                        dt.purSpe_supplier_ref,
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
        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            axios.get('/purSpe/send/'+this.data_art_type+'/'+this.import_id)
                .then(response => {
                    this.purchaseSpec = response.data;
                    this.importPurSpe();
                    this.loaded = true;
                })
                .catch(error => {
                });
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

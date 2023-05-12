<!--File name :ReferenceADocControl.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a documentary control in as incoming inspection-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="dimTest">
            <h2 class="titleForm" v-if="components.length > 0">Dimensional Test</h2>
            <!--Adding to the vue dimTestIDForm by going through the components array with the v-for-->
            <!--ref="ask_dimTest_data" is used to call the child elements in this component-->
            <!--The emitted deleteFile is caught here and call the function getContent -->
            <DimTestIDForm
                ref="ask_dimTest_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :id="component.id"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :severityLevel="component.dimTest_severityLevel"
                :controlLevel="component.dimTest_controlLevel"
                :expectedMethod="component.dimTest_expectedMethod"
                :expectedValue="component.dimTest_expectedValue"
                :name="component.dimTest_name"
                :unitValue="component.dimTest_unitValue"
                :sampling="component.dimTest_sampling"
                :incmgInsp_id="incmgInsp_id"
                :articleID="data_article_id"
                :articleType="data_article_type"
                :desc="component.dimTest_desc"
                :specDoc="component.dimTest_specDoc"
                @deletedimTest="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another file button appear -->
                <button v-on:click="addComponent">Add</button>
                <!--If file array is not empty and if the user is not in modification mode -->
            </div>
            <SaveButtonForm
                saveAll
                v-if="components.length>1"
                @add="saveAll"
                @update="saveAll"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
            />
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import DimTestIDForm from "./DimTestIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        DimTestIDForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importeddimTest: All article imported from the database
        article_id: ID of the equipment in which the file will be added
        import_id: ID of the equipment with which article will be imported
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
        importeddimTest: {
            type: Array,
            default: null
        },
        article_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        articleType: {
            type: String
        },
        incmgInsp_id: {
            type: Number,
            default: null
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        article: Array of all imported article
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_article_id: data of the article_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            dimTest: this.importeddimTest,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_article_id: this.article_id,
            data_article_type: this.articleType,
            loaded: false
        };
    },
    methods: {
        /*Function for adding a new empty file form*/
        addComponent() {
            this.components.push({
                comp: 'DimTestIDForm',
                key: this.uniqueKey++,
                id: null,
                dimTest_sampling: "100%",
            });
        },
        /*Function for adding an imported file form with his data*/
        addImportedComponent(
            dimTest_severityLevel,
            dimTest_controlLevel,
            dimTest_expectedMethod,
            dimTest_expectedValue,
            dimTest_name,
            dimTest_sampling,
            dimTest_unitValue,
            dimTest_desc,
            dimTest_specDoc,
            incmgInsp_id, id, className) {
            this.components.push({
                comp: 'DimTestIDForm',
                key: this.uniqueKey++,
                dimTest_severityLevel: dimTest_severityLevel,
                dimTest_controlLevel: dimTest_controlLevel,
                dimTest_expectedMethod: dimTest_expectedMethod,
                dimTest_expectedValue: dimTest_expectedValue,
                dimTest_name: dimTest_name,
                dimTest_sampling: dimTest_sampling,
                dimTest_unitValue: dimTest_unitValue,
                dimTest_desc: dimTest_desc,
                dimTest_specDoc: dimTest_specDoc,
                incmgInsp_id: incmgInsp_id,
                id: id,
                className: className
            });
        },
        /*Suppression of a file component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported article*/
        importdimTest() {
            if (this.dimTest.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.dimTest) {
                    const className = "importedDimTest" + dt.id;
                    this.addImportedComponent(
                        dt.dimTest_severityLevel,
                        dt.dimTest_levelOfControl,
                        dt.dimTest_expectedMethod,
                        dt.dimTest_expectedValue,
                        dt.dimTest_name,
                        dt.dimTest_sampling,
                        dt.dimTest_unitValue,
                        dt.dimTest_desc,
                        dt.dimTest_specDoc,
                        dt.incmgInsp_id,
                        dt.id,
                        className
                    );
                }
                this.dimTest = null
            }
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_dimTest_data) {
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    /*If the file doesn't have, an id*/
                    if (component.id == null) {
                        /*AddequipmentFile is used*/
                        component.addEquipmentFile(savedAs);
                    } else
                        /*Else if the file has an id and addSucces is equal to true*/
                    if (component.id != null || component.addSucces == true) {
                        /*updateEquipmentFile is used*/
                        /*if (component !== "validated") {
                            component.updateEquipmentFile(savedAs);
                        }*/ // FIXME ?
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentFile(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            const consultUrl = (id) => `/incmgInsp/dimTest/sendFromIncmgInsp/${id}`; // FIXME
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.dimTest = response.data;
                    this.importdimTest();
                    this.loaded = true;
                })
                .catch(error => {
                });
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the mounted option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importdimTest();
        }*/
    }
}
</script>

<style>

</style>

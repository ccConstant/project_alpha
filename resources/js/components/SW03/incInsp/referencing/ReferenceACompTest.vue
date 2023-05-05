<!--File name :ReferenceADocControl.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a documentary control in as incoming inspection-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="compTest">
            <h2 class="titleForm" v-if="components.length > 0">Complementary Test</h2>
            <!--Adding to the vue compTestIDForm by going through the components array with the v-for-->
            <!--ref="ask_compTest_data" is used to call the child elements in this component-->
            <!--The emitted deleteFile is caught here and call the function getContent -->
            <CompTestIDForm
                ref="ask_compTest_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :id="component.id"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :severityLevel="component.compTest_severityLevel"
                :controlLevel="component.compTest_controlLevel"
                :expectedMethod="component.compTest_expectedMethod"
                :expectedValue="component.compTest_expectedValue"
                :name="component.compTest_name"
                :unitValue="component.compTest_unitValue"
                :sampling="component.compTest_sampling"
                :incmgInsp_id="incmgInsp_id"
                :articleID="data_article_id"
                :articleType="data_article_type"
                :desc="component.compTest_desc"
                @deletecompTest="getContent(key)"
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
import CompTestIDForm from "./CompTestIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        CompTestIDForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedCompTest: All article imported from the database
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
        importedCompTest: {
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
            compTest: this.importedCompTest,
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
                comp: 'CompTestIDForm',
                key: this.uniqueKey++,
                id: null,
                compTest_sampling: "100%",
            });
        },
        /*Function for adding an imported file form with his data*/
        addImportedComponent(
            compTest_severityLevel,
            compTest_controlLevel,
            compTest_expectedMethod,
            compTest_expectedValue,
            compTest_name,
            compTest_sampling,
            compTest_unitValue,
            compTest_desc,
            incmgInsp_id, id, className) {
            this.components.push({
                comp: 'CompTestIDForm',
                key: this.uniqueKey++,
                compTest_severityLevel: compTest_severityLevel,
                compTest_controlLevel: compTest_controlLevel,
                compTest_expectedMethod: compTest_expectedMethod,
                compTest_expectedValue: compTest_expectedValue,
                compTest_name: compTest_name,
                compTest_sampling: compTest_sampling,
                compTest_unitValue: compTest_unitValue,
                compTest_desc: compTest_desc,
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
        importCompTest() {
            if (this.compTest.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.compTest) {
                    const className = "importedCompTest" + dt.id;
                    this.addImportedComponent(
                        dt.compTest_severityLevel,
                        dt.compTest_levelOfControl,
                        dt.compTest_expectedMethod,
                        dt.compTest_expectedValue,
                        dt.compTest_name,
                        dt.compTest_sampling,
                        dt.compTest_unitValue,
                        dt.compTest_desc,
                        dt.incmgInsp_id,
                        dt.id,
                        className
                    );
                }
                this.compTest = null
            }
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_compTest_data) {
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
            const consultUrl = (id) => `/incmgInsp/compTest/sendFromIncmgInsp/${id}`; // FIXME
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.compTest = response.data;
                    this.importCompTest();
                    this.loaded = true;
                })
                .catch(error => {
                    console.log(error);
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

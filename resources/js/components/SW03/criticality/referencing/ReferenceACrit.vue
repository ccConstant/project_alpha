<!--File name :ReferenceADocControl.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a documentary control in as incoming inspection-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="crit">
            <h2 v-if="components.length>0" class="titleForm">Criticality</h2>
            <!--Adding to the vue dimTestIDForm by going through the components array with the v-for-->
            <!--ref="ask_dimTest_data" is used to call the child elements in this component-->
            <!--The emitted deleteFile is caught here and call the function getContent -->
            <CritIDForm
                :is="component.comp"
                v-for="(component, key) in components"
                :id="component.id"
                :key="component.key"
                ref="ask_crit_data"
                :artCriticality="component.crit_artCriticality"
                :articleID="data_article_id"
                :articleType="data_article_type"
                :consultMod="isInConsultMod"
                :data_checkedTestRadioAdm="component.crit_checkedTestRadioAdm"
                :data_checkedTestRadioAsp="component.crit_checkedTestRadioAsp"
                :data_checkedTestRadioDim="component.crit_checkedTestRadioDim"
                :data_checkedTestRadioDoc="component.crit_checkedTestRadioDoc"
                :data_checkedTestRadioFunc="component.crit_checkedTestRadioFunc"
                :data_checkedTests="component.crit_checkedTests"
                :modifMod="isInModifMod"
                :performanceMedicalDevice="component.crit_performanceMedicalDevice"
                :remarks="component.crit_remarks"
                :validate="component.validate"
                @checkedTests="createTest"
                @deleteCrit="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another file button appear -->
                <button v-on:click="addComponent">Add Criticality</button>
                <!--If file array is not empty and if the user is not in modification mode -->
            </div>
            <!--            <SaveButtonForm
                            saveAll
                            v-if="components.length>1"
                            @add="saveAll"
                            @update="saveAll"
                            :consultMod="this.isInConsultMod"
                            :modifMod="this.isInModifMod"
                        />-->
        </div>
    </div>

</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import CritIDForm from "./CritIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        CritIDForm,
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
        importedCrit: {
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
        articleSubFam_id: {
            type: Number
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
            criticality: this.importedCrit,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_article_id: this.article_id,
            data_article_type: this.articleType.toLowerCase(),
            loaded: false,
            data_artSubFam_id: this.articleSubFam_id
        };
    },
    methods: {
        /*Function for adding a new empty file form*/
        addComponent() {
            this.components.push({
                comp: 'CritIDForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported file form with his data*/
        addImportedComponent(
            crit_artCriticality,
            crit_performanceMedicalDevice,
            crit_checkedTests,
            crit_checkedTestRadioFunc,
            crit_checkedTestRadioAsp,
            crit_checkedTestRadioDoc,
            crit_checkedTestRadioAdm,
            crit_checkedTestRadioDim,
            crit_remarks,
            crit_validate,
            incmgInsp_id, id, className) {
            console.log("crit_performanceMedicalDevice :" + crit_performanceMedicalDevice)
            console.log("crit_checkedTests :" + crit_checkedTests)
            this.components.push({
                comp: 'CritIDForm',
                key: this.uniqueKey++,
                crit_artCriticality: crit_artCriticality,
                crit_performanceMedicalDevice: crit_performanceMedicalDevice,
                crit_checkedTests: crit_checkedTests,
                crit_checkedTestRadioFunc: crit_checkedTestRadioFunc,
                crit_checkedTestRadioAsp: crit_checkedTestRadioAsp,
                crit_checkedTestRadioDoc: crit_checkedTestRadioDoc,
                crit_checkedTestRadioAdm: crit_checkedTestRadioAdm,
                crit_checkedTestRadioDim: crit_checkedTestRadioDim,
                crit_remarks: crit_remarks,
                validate: crit_validate,
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
        importCrit() {
            if (this.criticality.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.criticality) {
                    const className = "importedCrit" + dt.id;
                    this.addImportedComponent(
                        dt.crit_artCriticality,
                        dt.crit_performanceMedicalDevice,
                        dt.crit_checkedTests,
                        dt.crit_checkedTestRadioFunc,
                        dt.crit_checkedTestRadioAsp,
                        dt.crit_checkedTestRadioDoc,
                        dt.crit_checkedTestRadioAdm,
                        dt.crit_checkedTestRadioDim,
                        dt.crit_remarks,
                        dt.crit_validate,
                        dt.incmgInsp_id,
                        dt.id,
                        className
                    );
                    this.createTest(dt.crit_checkedTests);
                }
                this.criticality = null
            }
        },
        createTest(value) {
            console.log("Create Test in reference a crit")
            console.log("checked test :" + value)
            this.$emit('checkedTests', value);
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            if (this.data_article_id !== null) {
                axios.get('/artFam/criticality/send/' + this.data_article_type + '/' + this.import_id)
                    .then(response => {
                        this.criticality = response.data;
                        this.importCrit();
                        this.loaded = true;
                    }).catch(error => {
                });
            } else {
                axios.get('/artSubFam/criticality/send/' + this.data_article_type + '/' + this.import_id)
                    .then(response => {
                        this.criticality = response.data;
                        this.importCrit();
                        this.loaded = true;
                    }).catch(error => {
                });
            }
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the mounted option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importCrit();
        }*/
    }
}
</script>

<style>

</style>

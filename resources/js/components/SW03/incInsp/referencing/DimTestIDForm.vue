<!--File name : EquipmentFileForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 4 Apr 2023-->
<!--Vue Component of the Form of the equipment file who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="name"
                    label="Dimensional Test Name :"
                    v-model="dimTest_name"
                    :isDisabled="isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_name"
                />
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="expectedMethod"
                    label="Expected Method :"
                    v-model="dimTest_expectedMethod"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_expectedMethod"
                />
                <InputNumberForm
                    v-if="data_article_type === 'comp'"
                    name="expectedValue"
                    label="Expected Value :"
                    v-model="dimTest_expectedValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_expectedValue"
                />
                <InputSelectForm
                    :name="'Sampling'"
                    :label="'Sampling :'"
                    isRequired
                    :options="[
                        {id_enum: 'Sampling', value: 'statistics', text: 'statistics'},
                        {id_enum: 'Sampling', value: '100%', text: '100%'},
                        {id_enum: 'Sampling', value: 'other', text: 'other'}
                    ]"
                    :isDisabled="this.isInConsultedMod"
                    v-model="dimTest_sampling"
                    :info_text="null"
                    :Errors="errors.dimTest_sampling"
                    :selctedOption="dimTest_sampling"
                    :id_actual="'DimSampling'"
                />
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="unitValue"
                    label="Unit Value :"
                    v-model="dimTest_unitValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="1"
                    :max="10"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_unitValue"
                />
                <InputSelectForm
                    v-if="this.dimTest_sampling === 'statistics'"
                    name="SeverityLevel"
                    :Errors="errors.dimTest_severityLevel"
                    label="Severity Level :"
                    :options="[
                        {id_enum: 'DimSeverityLevel', value: 'I', text: 'I'},
                        {id_enum: 'DimSeverityLevel', value: 'II', text: 'II'},
                        {id_enum: 'DimSeverityLevel', value: 'III', text: 'III'},
                        {id_enum: 'DimSeverityLevel', value: 'IV', text: 'IV'}
                    ]"
                    :selctedOption="dimTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || dimTest_sampling !== 'statistics'"
                    v-model="dimTest_severityLevel"
                    :info_text="'DimSeverityLevel'"
                    :id_actual="'DimSeverityLevel'"
                />
                <InputSelectForm
                    v-if="this.dimTest_sampling === 'statistics'"
                    :name="'ControlLevel'"
                    :label="'Control Level :'"
                    isRequired
                    :options="[
                        {id_enum: 'DimControlLevel', value: 'Reduced', text: 'Reduced'},
                        {id_enum: 'DimControlLevel', value: 'Normal', text: 'Normal'},
                        {id_enum: 'DimControlLevel', value: 'Reinforced', text: 'Reinforced'}
                    ]"
                    :isDisabled="this.isInConsultedMod || dimTest_sampling !== 'statistics'"
                    v-model="dimTest_controlLevel"
                    :info_text="null"
                    :Errors="errors.dimTest_levelOfControl"
                    :selctedOption="dimTest_controlLevel"
                    :id_actual="'DimControlLevel'"
                />
                <InputTextForm
                    v-if="dimTest_sampling === 'other'"
                    name="desc"
                    label="Description :"
                    v-model="dimTest_desc"
                    :isDisabled="!!isInConsultedMod || dimTest_sampling !== 'other'"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_desc"
                />
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces===false ">
                    <!--If this file doesn't have a id the addDocControl is called function else the updateDocControl function is called -->
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addDocControl" @update="updateDocControl"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addDocControl" @update="updateDocControl"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addDocControl" @update="updateDocControl"
                                        :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                        :savedAs="'validated'"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="'validated'" :consultMod="this.isInConsultedMod"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'
import InputSelectForm from "../../../input/InputSelectForm.vue";
import InputNumberForm from "../../../input/SW03/InputNumberForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputNumberForm,
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert
    },
    /*--------Declaration of the different props:--------
        name : File name given by the database we will put this data in the corresponding field as default value
        location : File location given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the file
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this file form
        id: ID of an already created file
        article_id: ID of the equipment in which the file will be added
    ---------------------------------------------------*/
    props: {
        severityLevel: {
            type: String
        },
        controlLevel: {
            type: String
        },
        expectedMethod: {
            type: String
        },
        expectedValue: {
            type: Number
        },
        name: {
            type: String
        },
        unitValue: {
            type: String
        },
        sampling: {
            type: String
        },
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        divClass: {
            type: String
        },
        id: {
            type: Number,
            default: null
        },
        incmgInsp_id: {
            type: Number,
            default: null
        },
        articleID: {
            type: Number,
            default: null
        },
        articleType: {
            type: String,
            default: null
        },
        desc: {
            type: String,
            default: null
        }
    },
    /*--------Declaration of the different returned data:--------
    file_name: Name of the file who will be appeared in the field and updated dynamically
    file_location: Location of the file who will be appeared in the field and updated dynamically
    file_validate: Validation option of the file
    file_id: ID oh this file
    equipment_id_add: ID of the equipment in which the file will be added
    equipment_id_update: ID of the equipment in which the file will be updated
    errors: Object of errors in which will be stores the different error occurred when adding in database
    addSucces: Boolean who tell if this file has been added successfully
    isInConsultedMod: data of the consultMod prop
-----------------------------------------------------------*/
    data() {
        return {
            dimTest_id: this.id,
            dimTest_severityLevel: this.severityLevel,
            dimTest_controlLevel: this.controlLevel,
            dimTest_expectedMethod: this.expectedMethod,
            dimTest_expectedValue: this.expectedValue,
            dimTest_name: this.name,
            dimTest_unitValue: this.unitValue,
            dimTest_sampling: this.sampling,
            dimTest_desc: this.desc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
        }
    },
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addDocControl(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                /*The First post to verify if all the fields are filled correctly
                Name, location and validate option is sent to the controller*/
                axios.post('/incmgInsp/dimTest/verif', {
                    dimTest_name: this.dimTest_name,
                    dimTest_severityLevel: this.dimTest_severityLevel,
                    dimTest_levelOfControl: this.dimTest_controlLevel,
                    dimTest_expectedMethod: this.dimTest_expectedMethod,
                    dimTest_expectedValue: this.dimTest_expectedValue,
                    incmgInsp_id: this.data_incmgInsp_id,
                    dimTest_articleType: this.data_article_type,
                    dimTest_sampling: this.dimTest_sampling,
                    dimTest_unitValue: this.dimTest_unitValue,
                    dimTest_desc: this.dimTest_desc,
                })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/incmgInsp/dimTest/add', {
                        dimTest_name: this.dimTest_name,
                        dimTest_severityLevel: this.dimTest_severityLevel,
                        dimTest_levelOfControl: this.dimTest_controlLevel,
                        dimTest_expectedMethod: this.dimTest_expectedMethod,
                        dimTest_expectedValue: this.dimTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        dimTest_articleType: this.data_article_type,
                        dimTest_sampling: this.dimTest_sampling,
                        dimTest_unitValue: this.dimTest_unitValue,
                        dimTest_desc: this.dimTest_desc,
                    })
                    /*If the file is added successfully*/
                    .then(response => {
                        this.$snotify.success(`Functional Test added successfully and saved as ${savedAs}`);
                        if (!this.modifMod) {
                            /*The form pass in consulting mode and addSucces pass to True*/
                            this.isInConsultedMod = true;
                            this.addSucces = true
                        }
                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    });
                })
                //If the controller sends errors, we put it in the error object
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateDocControl(savedAs, reason, lifesheet_created) {
            axios.post('/incmgInsp/dimTest/verif', {
                dimTest_name: this.dimTest_name,
                dimTest_severityLevel: this.dimTest_severityLevel,
                dimTest_levelOfControl: this.dimTest_controlLevel,
                dimTest_expectedMethod: this.dimTest_expectedMethod,
                dimTest_expectedValue: this.dimTest_expectedValue,
                incmgInsp_id: this.data_incmgInsp_id,
                dimTest_articleType: this.data_article_type,
                dimTest_sampling: this.dimTest_sampling,
                dimTest_unitValue: this.dimTest_unitValue,
                dimTest_desc: this.dimTest_desc,
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/incmgInsp/dimTest/update/' + this.dimTest_id, {
                        dimTest_name: this.dimTest_name,
                        dimTest_severityLevel: this.dimTest_severityLevel,
                        dimTest_levelOfControl: this.dimTest_controlLevel,
                        dimTest_expectedMethod: this.dimTest_expectedMethod,
                        dimTest_expectedValue: this.dimTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        dimTest_articleType: this.data_article_type,
                        dimTest_sampling: this.dimTest_sampling,
                        dimTest_unitValue: this.dimTest_unitValue,
                        dimTest_desc: this.dimTest_desc,
                    })
                        /*If the file is added successfully*/
                        .then(response => {
                            this.$snotify.success(`Dimensional Test successfully updated`);
                            this.isInConsultedMod = true;
                            this.addSucces = true
                            // TODO: faire l'historique
                        })
                        /*If the controller sends errors, we put it in the error object*/
                        .catch(error => {
                            this.errors = error.response.data.errors;
                            console.log(error.response.data);
                        });
                })
                //If the controller sends errors, we put it in the error object
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            this.$emit('deleteDimTest', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        this.loaded = true;
    }
}
</script>

<style>
</style>

<!--File name : EquipmentFileForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 4 Apr 2023-->
<!--Vue Component of the Form of the equipment file who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <InputTextForm
                    :Errors="errors.docControl_name"
                    name="name"
                    label="Doc Control Name :"
                    v-model="docControl_name"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    :Errors="errors.docControl_reference"
                    name="reference"
                    label="Doc Control Reference :"
                    v-model="docControl_reference"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'raw' || this.data_article_type === 'comp'"
                    :Errors="errors.docControl_materialCertiSpec"
                    name="matCertifSpec"
                    label="Doc Control Material Certificate Specifications :"
                    v-model="docControl_materialCertiSpec"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'cons'"
                    :Errors="errors.docControl_fds"
                    name="fds"
                    label="Doc Control FDS :"
                    v-model="docControl_fds"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
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

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
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
        reference: {
            type: String
        },
        docControlName: {
            type: String
        },
        materialCertiSpec: {
            type: String
        },
        fds: {
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
            docControl_id: this.id,
            docControl_reference: this.reference,
            docControl_name: this.docControlName,
            docControl_materialCertiSpec: this.materialCertiSpec,
            docControl_fds: this.fds,
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
                axios.post('/incmgInsp/docControl/verif', {
                    docControl_articleType: this.data_article_type,
                    docControl_reference: this.docControl_reference,
                    docControl_name: this.docControl_name,
                    docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                    docControl_FDS: this.docControl_fds,
                    incmgInsp_id: this.data_incmgInsp_id,
                    reason: 'add',
                    id: this.docControl_id,
                    article_id: this.data_article_id,
                })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/incmgInsp/docControl/add', {
                        docControl_articleType: this.data_article_type,
                        docControl_reference: this.docControl_reference,
                        docControl_name: this.docControl_name,
                        docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                        docControl_FDS: this.docControl_fds,
                        incmgInsp_id: this.data_incmgInsp_id,
                        reason: 'add',
                        id: this.docControl_id,
                        article_id: this.data_article_id,
                    })
                    /*If the file is added successfully*/
                    .then(response => {
                        this.$refs.sucessAlert.showAlert(`Documentary control added successfully`);
                        this.isInConsultedMod = true;
                        this.addSucces = true

                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    });
                })
                //If the controller sends errors, we put it in the error object
                .catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateDocControl(savedAs, reason, lifesheet_created) {
            axios.post('/incmgInsp/docControl/verif', {
                docControl_articleType: this.data_article_type,
                docControl_reference: this.docControl_reference,
                docControl_name: this.docControl_name,
                docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                docControl_FDS: this.docControl_fds,
                incmgInsp_id: this.data_incmgInsp_id,
                reason: 'update',
                id: this.docControl_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/incmgInsp/docControl/update/' + this.docControl_id, {
                        docControl_articleType: this.data_article_type,
                        docControl_reference: this.docControl_reference,
                        docControl_name: this.docControl_name,
                        docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                        docControl_FDS: this.docControl_fds,
                        incmgInsp_id: this.data_incmgInsp_id,
                        reason: 'update',
                        id: this.docControl_id,
                        article_id: this.data_article_id,
                    })
                        /*If the file is added successfully*/
                        .then(response => {
                            this.$snotify.success(`Documentary Control successfully updated`);
                            this.isInConsultedMod = true;
                            this.addSucces = true
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
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            this.$emit('deleteDocControl', '')
            this.$refs.sucessAlert.showAlert(`Documentary Control Form deleted successfully`);
        }
    },
    created() {
        this.loaded = true;
    }
}
</script>

<style>
</style>

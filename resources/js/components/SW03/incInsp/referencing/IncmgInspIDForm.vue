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
                <!--Call of the different component with their props-->
                <InputTextForm
                    :Errors="errors.incmgInsp_remarks"
                    name="remarks"
                    label="Incoming Inspection Remarks :"
                    v-model="incmgInsp_remarks"
                    :isDisabled="!!isInConsultMod || addSucces"
                    :info_text="null"
                    :max="255"
                />
                <InputTextForm
                    :Errors="errors.incmgInsp_partMaterialComplianceCertificate"
                    name="partMatCertif"
                    label="Incoming Inspection Part Material Compliance Certificate :"
                    v-model="incmgInsp_partMaterialCertif"
                    :isDisabled="!!isInConsultMod || addSucces"
                    :info_text="null"
                    :max="255"
                />
                <InputTextForm
                    :Errors="errors.incmgInsp_rawMaterialCertificate"
                    name="rawMatCertif"
                    label="Incoming Inspection Raw Material Certificate :"
                    v-model="incmgInsp_rawMaterialCertif"
                    :isDisabled="!!isInConsultMod || addSucces"
                    :info_text="null"
                    :max="255"
                />
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this file doesn't have a id the addIncmgInsp is called function else the updateIncmgInsp function is called -->
                    <div v-if="this.incmgInsp_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addIncmgInsp" @update="updateIncmgInsp"
                                            :consultMod="this.isInConsultMod" :savedAs="incmgInsp_validate"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addIncmgInsp" @update="updateIncmgInsp"
                                            :consultMod="this.isInConsultMod" :savedAs="incmgInsp_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addIncmgInsp" @update="updateIncmgInsp"
                                        :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                                        :savedAs="incmgInsp_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="incmgInsp_validate" :consultMod="this.isInConsultMod"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <div v-if="incmgInsp_id !== null">
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingDocControl">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDocControl" aria-expanded="true" aria-controls="collapseDocControl">
                                Documentary Control
                            </button>
                        </h2>
                        <div id="collapseDocControl" class="accordion-collapse collapse show" aria-labelledby="headingDocControl">
                            <div class="accordion-body">
                                <ReferenceADocControl
                                    :articleType="article_type"
                                    :article_id="article_id"
                                    :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                    :incmgInsp_id="incmgInsp_id"
                                    :consultMod="this.isInConsultMod"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAspTest">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseAspTest" aria-expanded="true" aria-controls="collapseAspTest">
                                Aspect Test
                            </button>
                        </h2>
                        <div id="collapseAspTest" class="accordion-collapse collapse show" aria-labelledby="headingAspTest">
                            <div class="accordion-body">
                                <ReferenceAnAspTest
                                    :articleType="article_type"
                                    :article_id="article_id"
                                    :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                    :incmgInsp_id="incmgInsp_id"
                                    :consultMod="this.isInConsultMod"
                                />
                            </div>
                        </div>
                    </div>
                    <!-- FuncTest -->
                    <div class="accordion-item" v-if="article_type === 'comp'">
                        <h2 class="accordion-header" id="headingFuncTest">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFuncTest" aria-expanded="true" aria-controls="collapseFuncTest">
                                Functional Test
                            </button>
                        </h2>
                        <div id="collapseFuncTest" class="accordion-collapse collapse show" aria-labelledby="headingFuncTest">
                            <div class="accordion-body">
                                <ReferenceAFuncTest
                                    :articleType="article_type"
                                    :article_id="article_id"
                                    :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                    :incmgInsp_id="incmgInsp_id"
                                    :consultMod="this.isInConsultMod"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- DimTest -->
                    <div class="accordion-item" v-if="article_type === 'comp' || article_type === 'raw'">
                        <h2 class="accordion-header" id="headingDimTest">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDimTest" aria-expanded="true" aria-controls="collapseDimTest">
                                Dimensional Test
                            </button>
                        </h2>
                        <div id="collapseDimTest" class="accordion-collapse collapse show" aria-labelledby="headingDimTest">
                            <div class="accordion-body">
                                <ReferenceADimTest
                                    :articleType="article_type"
                                    :article_id="article_id"
                                    :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                    :incmgInsp_id="incmgInsp_id"
                                    :consultMod="this.isInConsultMod"
                                />
                            </div>
                        </div>

                    </div>
                    <!-- CompTest -->
                    <div class="accordion-item" v-if="article_type === 'cons'">
                        <h2 class="accordion-header" id="headingCompTest">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseCompTest" aria-expanded="true" aria-controls="collapseCompTest">
                                Complementary Test
                            </button>
                        </h2>
                        <div id="collapseCompTest" class="accordion-collapse collapse show" aria-labelledby="headingCompTest">
                            <div class="accordion-body">
                                <ReferenceACompTest
                                    :articleType="article_type"
                                    :article_id="article_id"
                                    :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                    :incmgInsp_id="incmgInsp_id"
                                    :consultMod="this.isInConsultMod"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            <SucessAlert ref="sucessAlert"/>
            </div>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'
import ReferenceADocControl from "./ReferenceADocControl.vue";
import ReferenceAnAspTest from "./ReferenceAnAspTest.vue";
import ReferenceAFuncTest from "./ReferenceAFuncTest.vue";
import ReferenceADimTest from "./ReferenceADimTest.vue";
import ReferenceACompTest from "./ReferenceACompTest.vue";
export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ReferenceACompTest,
        ReferenceADimTest,
        ReferenceAFuncTest,
        ReferenceAnAspTest,
        ReferenceADocControl,
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
        remarks: {
            type: String
        },
        partMaterialCertif: {
            type: String
        },
        rawMaterialCertif: {
            type: String
        },
        validate: {
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
        article_id: {
            type: Number
        },
        article_type: {
            type: String
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
    isInConsultMod: data of the consultMod prop
-----------------------------------------------------------*/
    data() {
        return {
            incmgInsp_id: this.id,
            incmgInsp_remarks: this.remarks,
            incmgInsp_partMaterialCertif: this.partMaterialCertif,
            incmgInsp_rawMaterialCertif: this.rawMaterialCertif,
            incmgInsp_validate: this.validate,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_file: [],
            rawFam_id: null,
            consFam_id: null,
            compFam_id: null
        }
    },
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addIncmgInsp(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                if (this.article_type === 'cons') {
                    this.consFam_id = this.article_id;
                } else if (this.article_type === 'raw') {
                    this.rawFam_id = this.article_id;
                } else if (this.article_type === 'comp') {
                    this.compFam_id = this.article_id;
                }
                /*The First post to verify if all the fields are filled correctly
                Name, location and validate option is sent to the controller*/
                axios.post('/incmgInsp/verif', {
                    incmgInsp_remarks: this.incmgInsp_remarks,
                    incmgInsp_partMaterialComplianceCertificate: this.incmgInsp_partMaterialCertif,
                    incmgInsp_rawMaterialCertificate: this.incmgInsp_rawMaterialCertif,
                    incmgInsp_validate: savedAs,
                    incmgInsp_consFam_id: this.consFam_id,
                    incmgInsp_rawFam_id: this.rawFam_id,
                    incmgInsp_compFam_id: this.compFam_id,
                    incmpInsp_articleType: this.article_type
                })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/incmgInsp/add', {
                        incmgInsp_remarks: this.incmgInsp_remarks,
                        incmgInsp_partMaterialComplianceCertificate: this.incmgInsp_partMaterialCertif,
                        incmgInsp_rawMaterialCertificate: this.incmgInsp_rawMaterialCertif,
                        incmgInsp_validate: savedAs,
                        incmgInsp_consFam_id: this.consFam_id,
                        incmgInsp_rawFam_id: this.rawFam_id,
                        incmgInsp_compFam_id: this.compFam_id,
                        incmpInsp_articleType: this.article_type
                    })
                    /*If the file is added successfully*/
                    .then(response => {
                        this.$snotify.success(`Incoming Inspection added successfully`);
                        this.addSucces = true
                        /*the id of the file take the value of the newly created id*/
                        this.incmgInsp_id = response.data.id;
                        /*The validate option of this file takes the value of savedAs(Params of the function)*/
                        this.incmgInsp_validate = savedAs;
                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => this.errors = error.response.data.errors);
                })
                //If the controller sends errors, we put it in the error object
                .catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateIncmgInsp(savedAs, reason, artSheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            axios.post('/incmgInsp/verif', {
                incmgInsp_remarks: this.incmgInsp_remarks,
                incmgInsp_partMaterialComplianceCertificate: this.incmgInsp_partMaterialCertif,
                incmgInsp_rawMaterialCertificate: this.incmgInsp_rawMaterialCertif,
                incmgInsp_validate: savedAs,
                incmgInsp_consFam_id: this.consFam_id,
                incmgInsp_rawFam_id: this.rawFam_id,
                incmgInsp_compFam_id: this.compFam_id,
                incmpInsp_articleType: this.article_type
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    Type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                    const consultUrl = (id) => `/incmgInsp/update/${id}`;
                    axios.post(consultUrl(this.incmgInsp_id), {
                        incmgInsp_remarks: this.incmgInsp_remarks,
                        incmgInsp_partMaterialComplianceCertificate: this.incmgInsp_partMaterialCertif,
                        incmgInsp_rawMaterialCertificate: this.incmgInsp_rawMaterialCertif,
                        incmgInsp_validate: savedAs,
                        incmgInsp_consFam_id: this.consFam_id,
                        incmgInsp_rawFam_id: this.rawFam_id,
                        incmgInsp_compFam_id: this.compFam_id,
                        incmpInsp_articleType: this.article_type
                    })
                        .then(response => {
                            this.incmgInsp_validate = savedAs;
                            /*We test if a life sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.article_type.toLowerCase() + '/' + this.article_id, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Incoming Inspection updated successfully and saved as ${savedAs}`);
                            this.addSucces = true;
                        })
                        /*If the controller sends errors, we put it in the error object*/
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        });
                })
                /*If the controller sends errors, we put it in the error object*/
                .catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, artSheet_created) {
            /*If the user is in update mode and the file exist in the database*/
            if (this.modifMod == true && this.file_id !== null) {
                /*Send a post-request with the id of the file who will be deleted in the url*/
                const consultUrl = (id) => `/equipment/delete/file/${id}`;
                axios.post(consultUrl(this.file_id), {
                    article_id: this.equipment_id_update
                })
                    .then(response => {
                        const id = this.equipment_id_update;
                        /*We test if a life sheet has been already created*/
                        /*If it's the case we create a new enregistrement of history for saved the reason of the deleting*/
                        /*Emit to the parent component that we want to delete this component*/
                        if (artSheet_created == true) {
                            axios.post('/history/add/' + this.artFam_type.toLowerCase() + '/' + this.artFam_id, {
                                history_reasonUpdate: reason,
                            });
                            window.location.reload();
                        }
                        this.$emit('deleteFile', '')
                        this.$refs.sucessAlert.showAlert(`Incoming Inspection deleted successfully`);
                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteFile', '')
                this.$refs.sucessAlert.showAlert(`Empty Incoming Inspection Form deleted successfully`);
            }
        }
    },
    created() {
        this.loaded = true;
    }
}
</script>

<style>
</style>

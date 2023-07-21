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
            <button v-if="!this.alreadyCreated" @click="addIncmgInsp">Create a new Incoming Inspection</button>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
                <div v-if="this.incmgInsp_id!=null">
                    <div class="accordion">
                        <div class="accordion-item">
                            <h2 id="headingAdmTestIncmg" class="accordion-header">
                                <button aria-controls="collapseAdmTestIncmg" aria-expanded="true" class="accordion-button"
                                        data-bs-target="#collapseAdmTestIncmg" data-bs-toggle="collapse"
                                        type="button">
                                    Additional Administrative Control
                                </button>
                            </h2>
                            <div id="collapseAdmTestIncmg" aria-labelledby="headingAdmTestIncmg"
                                 class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <ReferenceAnAdminControl
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                        :incmgInsp_id="incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDocControlIncmg">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDocControlIncmg" aria-expanded="true" aria-controls="collapseDocControlIncmg">
                                    Documentary Control
                                </button>
                            </h2>
                            <div id="collapseDocControlIncmg" class="accordion-collapse collapse show" aria-labelledby="headingDocControlIncmg">
                                <div class="accordion-body">
                                    <ReferenceADocControl
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? this.incmgInsp_id : null"
                                        :incmgInsp_id="this.incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :docControl_name="this.data_docControl_name"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAspTestIncmg">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseAspTestIncmg" aria-expanded="true" aria-controls="collapseAspTestIncmg">
                                    Aspect Test
                                </button>
                            </h2>
                            <div id="collapseAspTestIncmg" class="accordion-collapse collapse show" aria-labelledby="headingAspTestIncmg">
                                <div class="accordion-body">
                                    <ReferenceAnAspTest
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                        :incmgInsp_id="this.incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- FuncTest -->
                        <div class="accordion-item" v-if="article_type === 'comp'">
                            <h2 class="accordion-header" id="headingFuncTestIncmg">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFuncTestIncmg" aria-expanded="true" aria-controls="collapseFuncTestIncmg">
                                    Functional Test
                                </button>
                            </h2>
                            <div id="collapseFuncTestIncmg" class="accordion-collapse collapse show" aria-labelledby="headingFuncTestIncmg">
                                <div class="accordion-body">
                                    <ReferenceAFuncTest
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                        :incmgInsp_id="incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- DimTest -->
                        <div class="accordion-item" v-if="article_type === 'comp' || article_type === 'raw'">
                            <h2 class="accordion-header" id="headingDimTestIncmg">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDimTestIncmg" aria-expanded="true" aria-controls="collapseDimTestIncmg">
                                    Dimensional Test
                                </button>
                            </h2>
                            <div id="collapseDimTestIncmg" class="accordion-collapse collapse show" aria-labelledby="headingDimTestIncmg">
                                <div class="accordion-body">
                                    <ReferenceADimTest
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                        :incmgInsp_id="incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>

                        </div>
                        <!-- CompTest -->
                        <div class="accordion-item" v-if="article_type === 'other'">
                            <h2 class="accordion-header" id="headingCompTestIncmg">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseCompTestIncmg" aria-expanded="true" aria-controls="collapseCompTestIncmg">
                                    Complementary Test
                                </button>
                            </h2>
                            <div id="collapseCompTestIncmg" class="accordion-collapse collapse show" aria-labelledby="headingCompTestIncmg">
                                <div class="accordion-body">
                                    <ReferenceACompTest
                                        :articleType="article_type"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? incmgInsp_id : null"
                                        :incmgInsp_id="incmgInsp_id"
                                        :consultMod="this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                        :modifMod="this.isInModifMod"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
import ReferenceADocControl from "./ReferenceADocControl.vue";
import ReferenceAnAspTest from "./ReferenceAnAspTest.vue";
import ReferenceAFuncTest from "./ReferenceAFuncTest.vue";
import ReferenceADimTest from "./ReferenceADimTest.vue";
import ReferenceACompTest from "./ReferenceACompTest.vue";
import ReferenceAnAdminControl from "./ReferenceAnAdminControl.vue";
export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ReferenceACompTest,
        ReferenceADimTest,
        ReferenceAFuncTest,
        ReferenceAnAspTest,
        ReferenceADocControl,
        ReferenceAnAdminControl,
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
        },
        checkedTest: {
            type: Array,
            default: null
        },
        artSubFam_id: {
            type: Number
        },
        docControl_name: {
            type: String,
            default: null
        },
        alreadyCreated: {
            type: Boolean,
            default: false
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
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_checkedTest: this.checkedTest,
            data_docControl_name: this.docControl_name,
            loaded: false,
            infos_incmgInsp: [],
            rawFam_id: null,
            consFam_id: null,
            compFam_id: null,
            rawSubFam_id: null,
            consSubFam_id: null,
            compSubFam_id: null,
            data_alreadyCreated: this.alreadyCreated    
        }
    },
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addIncmgInsp() {
            if (this.artSubFam_id!=null){
                if (this.article_type === 'cons') {
                    this.consSubFam_id = this.artSubFam_id;
                } else if (this.article_type === 'raw') {
                    this.rawSubFam_id = this.artSubFam_id;
                } else if (this.article_type === 'comp') {
                    this.compSubFam_id = this.artSubFam_id;
                }
            }
            if (this.article_type === 'cons') {
                this.consFam_id = this.article_id;
            } else if (this.article_type === 'raw') {
                this.rawFam_id = this.article_id;
            } else if (this.article_type === 'comp') {
                this.compFam_id = this.article_id;
            }
                axios.post('/artFam/incmgInsp/add', {
                    incmgInsp_consFam_id: this.consFam_id,
                    incmgInsp_rawFam_id: this.rawFam_id,
                    incmgInsp_compFam_id: this.compFam_id,
                    consSubFam_id: this.consSubFam_id,
                    rawSubFam_id: this.rawSubFam_id,
                    compSubFam_id: this.compSubFam_id,
                    incmpInsp_articleType: this.article_type
                })
                /*If the file is added successfully*/
                .then(response => {
                    this.$snotify.success(`Incoming Inspection added successfully`);
                    this.addSucces = true
                    /*the id of the file take the value of the newly created id*/
                    this.incmgInsp_id = response.data.id;

                })
                /*If the controller sends errors, we put it in the error object*/
                .catch(error => this.errors = error.response.data.errors);
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateIncmgInsp(savedAs, reason, artSheet_created) {
            /*If all the verifications passed, a new post this time to add the file in the database
            Type, name, value, unit, validate option and id of the equipment is sent to the controller
            In the post url the id correspond to the id of the file who will be updated*/
            const consultUrl = (id) => `/artFam/incmgInsp/update/${id}`;
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
        console.log(this.alreadyCreated)
        if (this.data_checkedTest!=null && (this.data_checkedTest.includes('docControl') || this.data_checkedTest.includes('dimTest') || this.data_checkedTest.includes('funcTest') || this.data_checkedTest.includes('aspTest') || this.data_checkedTest.includes('compTest') || this.data_checkedTest.includes('adminControl')|| this.data_docControl_name!='Test required to ensure performance of the medical device' && this.data_docControl_name!=null && this.data_docControl_name!='')) {
            this.alreadyCreated=true;
            this.addIncmgInsp() ;
        }
        axios.get('/info/send/IncmgInsp')
            .then(response => {
                this.infos_incmgInsp = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
            this.loaded = true;
    }
}
</script>

<style>
</style>

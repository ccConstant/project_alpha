<!--File name : ArticlePurchaseSpecificationForm.vue -->
<!--Creation date : 2 May 2023-->
<!--Update date : 11 Jul 2023 -->
<!--Vue Component of the Form of the article purchase specification who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" >
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    name="supplier"
                    :Errors="errors.purSpe_supplier_id"
                    label="Supplier :"
                    :options="suppliers"
                    :selctedOption="purSpe_supplier_id"
                    :isDisabled="!!isInConsultMod"
                    v-model="purSpe_supplier_id"
                    :info_text="null"
                    :id_actual="supplier_id"
                />
                <InputTextForm
                    v-if="this.purSpe_supplier_id !== 'Alpha'"
                    :inputClassName="null"
                    :Errors="errors.purSpe_supplier_ref"
                    name="purSpe_supplrRef"
                    label="Supplier's Reference"
                    :isDisabled="this.isInConsultMod"
                    isRequired
                    v-model="purSpe_supplier_ref"
                    :info_text="null"
                    :min="2"
                    :max="255"
                />
                <SaveButtonForm v-if="this.addSucces===false"
                    ref="saveButton"
                    @add="addPurchaseSpecification"
                    @update="updatePurchaseSpecification"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"/>
                <DeleteComponentButton :validationMode="purSpe_validate" :consultMod="this.isInConsultMod"
                                       @deleteOk="deleteComponent"/>
            </form>
            <div v-if="this.purSpe_id!=null">
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
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe_id="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
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
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe_id="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- FuncTest -->
                        <div class="accordion-item" v-if="artFam_type === 'comp'">
                            <h2 class="accordion-header" id="headingFuncTest">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFuncTest" aria-expanded="true" aria-controls="collapseFuncTest">
                                    Functional Test
                                </button>
                            </h2>
                            <div id="collapseFuncTest" class="accordion-collapse collapse show" aria-labelledby="headingFuncTest">
                                <div class="accordion-body">
                                    <ReferenceAFuncTest
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- DimTest -->
                        <div class="accordion-item" v-if="artFam_type === 'comp' || artFam_type === 'raw'">
                            <h2 class="accordion-header" id="headingDimTest">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDimTest" aria-expanded="true" aria-controls="collapseDimTest">
                                    Dimensional Test
                                </button>
                            </h2>
                            <div id="collapseDimTest" class="accordion-collapse collapse show" aria-labelledby="headingDimTest">
                                <div class="accordion-body">
                                    <ReferenceADimTest
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe_id="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                    />
                                </div>
                            </div>

                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAspTest">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseAspTest" aria-expanded="true" aria-controls="collapseAspTest">
                                    Administrative Test
                                </button>
                            </h2>
                            <div id="collapseAspTest" class="accordion-collapse collapse show" aria-labelledby="headingAspTest">
                                <div class="accordion-body">
                                    <ReferenceAnAdminControl
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe_id="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- CompTest -->
                        <div class="accordion-item" v-if="artFam_type === 'cons'">
                            <h2 class="accordion-header" id="headingCompTest">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseCompTest" aria-expanded="true" aria-controls="collapseCompTest">
                                    Complementary Test
                                </button>
                            </h2>
                            <div id="collapseCompTest" class="accordion-collapse collapse show" aria-labelledby="headingCompTest">
                                <div class="accordion-body">
                                    <ReferenceACompTest
                                        :articleType="artFam_type"
                                        :article_id="art_id"
                                        :import_id="this.isInConsultMod || this.isInModifMod ? purSpe_id : null"
                                        :purSpe_id="purSpe_id"
                                        :consultMod="!this.isInConsultMod"
                                        :checkedTest="this.data_checkedTest"
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
import InputSelectForm from "../../../input/InputSelectForm.vue";
import ReferenceADocControl from "../../incInsp/referencing/ReferenceADocControl.vue";
import ReferenceAnAspTest from "../../incInsp/referencing/ReferenceAnAspTest.vue";
import ReferenceAFuncTest from "../../incInsp/referencing/ReferenceAFuncTest.vue";
import ReferenceADimTest from "../../incInsp/referencing/ReferenceADimTest.vue";
import ReferenceACompTest from "../../incInsp/referencing/ReferenceACompTest.vue";
import ReferenceAnAdminControl from "../../incInsp/referencing/ReferenceAnAdminControl.vue";


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        ReferenceADocControl,
        ReferenceAnAspTest,
        ReferenceAFuncTest,
        ReferenceADimTest,
        ReferenceACompTest,
        ReferenceAnAdminControl,

    },

    /*--------Declaration of the different props:--------
        name : File name given by the database we will put this data in the corresponding field as default value
        location : File location given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the file
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this file form
        id: ID of an already created file
        eq_id: ID of the equipment in which the file will be added
    ---------------------------------------------------*/
    props: {
        requiredDoc: {
            type: String
        },
        supplier_id: {
            type: String
        },
        supplier_ref: {
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
        art_id: {
            type: Number
        },
        art_type:{
            type: String
        },
        checkedTest: {
            type: Array
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
            purSpe_requiredDoc: this.requiredDoc,
            purSpe_supplier_id: this.supplier_id,
            purSpe_supplier_ref: this.supplier_ref,
            purSpe_validate: this.validate,
            purSpe_id: this.id,
            art_id_add: this.art_id,
            artFam_type:this.art_type,
            art_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_purSpe: [],
            suppliers: [],
            data_checkedTest: this.checkedTest,
        }
    },
    methods: {
        /*Sending to the controller all the information about the purchase spe so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        addPurchaseSpecification(savedAs, reason, artSheet_created) {
            if (!this.addSucces) {
                /*ID of the equipment in which the file will be added*/
                let id;
                /*If the user is not in modification mode, we set the id with the value of data art_id_add*/
                if (!this.modifMod) {
                    id = this.art_id_add;
                    /*Else the user is in the update menu, we allocate to the id the value of the id get in the url*/
                } else {
                    id = this.art_id_update;
                }
                axios.post('/purSpe/verif', {
                    purSpe_requiredDoc: this.purSpe_requiredDoc,
                    purSpe_validate: savedAs,
                    purSpe_supplier_id: this.purSpe_supplier_id,
                    purSpe_supplier_ref: this.purSpe_supplier_ref,
                })
                /*If the data are correct, we send them to the controller for add them in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => `/purSpe/add/${id}`;
                    axios.post(consultUrl(id), {
                        purSpe_requiredDoc: this.purSpe_requiredDoc,
                        purSpe_validate: savedAs,
                        artFam_type : this.artFam_type.toUpperCase(),
                        purSpe_supplier_id: this.purSpe_supplier_id,
                        purSpe_supplier_ref: this.purSpe_supplier_ref,
                    })
                    /*If the data have been added in the database, we show a success message*/
                    .then(response => {
                        this.addSuccess = true;
                        this.isInConsultMod = true;
                        this.$snotify.success(`purchase specification added successfully and saved as ${savedAs}`);
                        this.purSpe_id = response.data;
                        this.purSpe_validate=savedAs;
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    });
                })
                .catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updatePurchaseSpecification(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            axios.post('/purSpe/verif', {
                purSpe_requiredDoc: this.purSpe_requiredDoc,
                purSpe_validate: savedAs,
                purSpe_supplier_id: this.purSpe_supplier_id,
                purSpe_supplier_ref: this.purSpe_supplier_ref,
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    Type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                    axios.post('/purSpe/update/' + this.artFam_type + '/' + this.purSpe_id, {
                        purSpe_requiredDoc: this.purSpe_requiredDoc,
                        purSpe_validate: savedAs,
                        purSpe_supplier_id: this.purSpe_supplier_id,
                        purSpe_supplier_ref: this.purSpe_supplier_ref,
                    })
                        .then(response => {
                            this.file_validate = savedAs;
                            /*We test if a life sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifesheet_created == true) {
                                axios.post('/artFam/history/add/' + this.artFam_type.toLowerCase() + '/' + this.art_id_update, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Article Purchase Specification updated successfully and saved as ${savedAs}`);
                        })
                        /*If the controller sends errors, we put it in the error object*/
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        });
                })
                /*If the controller sends errors, we put it in the error object*/
                .catch(error => this.errors = error.response.data.errors);
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            /*If the user is in update mode and the file exist in the database*/
            if (this.modifMod == true && this.file_id !== null) {
                /*Send a post-request with the id of the file who will be deleted in the url*/
                const consultUrl = (id) => `/equipment/delete/file/${id}`;
                axios.post(consultUrl(this.file_id), {
                    eq_id: this.equipment_id_update
                })
                    .then(response => {
                        const id = this.equipment_id_update;
                        /*We test if a life sheet has been already created*/
                        /*If it's the case we create a new enregistrement of history for saved the reason of the deleting*/
                        if (artSheet_created == true) {
                            axios.post('/history/add/' + this.artFam_type.toLowerCase() + '/' + this.artFam_id, {
                                history_reasonUpdate: reason,
                            });
                            window.location.reload();
                        }
                        /*Emit to the parent component that we want to delete this component*/
                        this.$emit('deleteFile', '')
                        this.$refs.sucessAlert.showAlert(`Equipment file deleted successfully`);
                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteFile', '')
                this.$refs.sucessAlert.showAlert(`Empty Equipment file deleted successfully`);
            }
        }
    },
    created() {
        axios.get('/info/send/purSpe')
            .then(response => {
                this.infos_purSpe = response.data;
            })
            .catch(error => {
            });
        axios.get('/supplier/active/send')
            .then(response => {
                /*this.suppliers.push({
                    id_enum: 'suppliers',
                    value: 'Alpha',
                    text: -1,
                })*/
                for (let i = 0; i < response.data.length; i++) {
                    this.suppliers.push({
                        id_enum: 'suppliers',
                        value: response.data[i].supplr_name,
                        text: response.data[i].id,
                    });
                }
                this.loaded=true;
            })
            .catch(error => {
            });

    }
}
</script>

<style>
</style>

<!--File name : ArticlePurchaseSpecificationForm.vue -->
<!--Creation date : 2 May 2023-->
<!--Update date : 2 May 2023 -->
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


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,

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

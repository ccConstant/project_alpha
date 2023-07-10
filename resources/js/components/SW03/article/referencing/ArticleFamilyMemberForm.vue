<!--File name : ArticleFamilyMemberForm.vue -->
<!--Creation date : 2 May 2023-->
<!--Update date : 2 May 2023 -->
<!--Vue Component of the Form of the article storage condition who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" >
                <InputTextDoubleForm
                    :inputClassName="null"
                    :Errors="errors.artFamMb_ref"
                    name="artFamMb_ref"
                    label="Article Family Member Reference"
                    :isDisabled="this.isInConsultMod && !this.isInModifMod"
                    isRequired
                    v-model="artFamMb_ref"
                    :famRef="this.data_artFam_ref"
                    :info_text="null"
                    :min="3"
                    :max="255"
                    :placeholer="this.data_artSubFam_ref"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFamMb_design"
                    name="artSubFam_design"
                    label="Article Family Member Designation"
                    :isDisabled="this.isInConsultMod && !this.isInModifMod"
                    isRequired
                    v-model="artFamMb_design"
                    :info_text="null"
                    :min="3"
                    :max="255"
                />
            
                <SaveButtonForm v-if="this.addSucces==false"
                    ref="saveButton"
                    @add="addArticleMember"
                    @update="updateArticleMember"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="this.artFamMb_validate"/>
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
import InputSelectForm from '../../../input/InputSelectForm.vue'
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue"
import InputTextDoubleForm from '../../../input/SW03/InputTextDoubleForm.vue';


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        InputSelectForm,
        InputTextDoubleForm

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
        reference: {
            type: String
        },
        designation: {
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
        artSubFam_id:{
            type: Number
        },
        famMb_ref:{
            type: String
        },
        famMb_design:{
            type: String
        },
        artFam_ref:{
            type: String
        },
        artSubFam_ref:{
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
            artFamMb_ref: this.reference,
            artFamMb_design: this.designation,
            artFamMb_validate: this.validate,
            artFamMember_id: this.id,
            art_id_add: this.art_id,
            artFam_type:this.art_type,
            data_artSubFam_id: this.artSubFam_id,
            art_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            data_artSubFam_ref: this.artSubFam_ref,
            data_artFam_ref: this.artFam_ref
        }
    },
    created(){
        console.log(this.artFamMb_ref)
         this.loaded=true;
    },
    methods: {
        /*Sending to the controller all the information about the article member so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        addArticleMember(savedAs, reason, artSheet_created) {
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
                /*We begin by checking if the data entered by the user are correct*/
                console.log("add method")
                console.log(savedAs)
                if (this.artFam_type=="COMP"){
                    axios.post('/comp/mb/verif', {
                        artMb_ref: this.artFamMb_ref,
                        artMb_designation: this.artFamMb_design,
                        artMb_validate : savedAs
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        console.log("verif passed")
                        this.errors = {};
                        const consultUrl = (id) => `/comp/mb/add/${id}`;
                        axios.post(consultUrl(this.data_artSubFam_id), {
                            artMb_ref: this.artFamMb_ref,
                            artMb_validate: savedAs,
                            artMb_designation: this.artFamMb_design,
                        })
                        /*If the data have been added in the database, we show a success message*/
                        .then(response => {
                            this.addSuccess = true;
                            this.isInConsultMod = true;
                            this.$snotify.success(`CompFamMember added successfully and saved as ${savedAs}`);
                            this.artFamMember_id = response.data;
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.artFam_type=="RAW"){
                        axios.post('/raw/mb/verif', {
                            artMb_ref: this.artFamMb_ref,
                            artMb_validate: savedAs,
                            artMb_designation: this.artFamMb_design,
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        console.log("verif passed")
                        console.log(savedAs)
                        console.log()
                        const consultUrl = (id) => `/raw/mb/add/${id}`;
                        axios.post(consultUrl(this.data_artSubFam_id), {
                            artMb_ref: this.artFamMb_ref,
                            artMb_validate: savedAs,
                            artMb_designation: this.artFamMb_design,
                        })
                        /*If the data have been added in the database, we show a success message*/
                        .then(response => {
                            this.addSuccess = true;
                            this.isInConsultMod = true;
                             console.log(this.isInConsultMod)
                            console.log(this.isInModifMod)
                            this.$snotify.success(`CompFamMember added successfully and saved as ${savedAs}`);
                            this.artFamMember_id = response.data;
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
                    }else{
                        if (this.artFam_type=="CONS"){
                            axios.post('/cons/mb/verif', {
                                artMb_ref: this.artFamMb_ref,
                                artMb_validate: savedAs,
                                artMb_designation: this.artFamMb_design,
                            })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                console.log("verif passed")
                                this.errors = {};
                                const consultUrl = (id) => `/cons/mb/add/${id}`;
                                axios.post(consultUrl(this.data_artSubFam_id), {
                                    artMb_ref: this.artFamMb_ref,
                                    artMb_validate: savedAs,
                                    artMb_designation: this.artFamMb_design,
                                })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.$snotify.success(`ConsFamMember added successfully and saved as ${savedAs}`);
                                    this.artFamMember_id = response.data;
                                })
                                .catch(error => this.errors = error.response.data.errors);
                            })
                            .catch(error => this.errors = error.response.data.errors);
                        }
                    }
                }
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateArticleMember(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            console.log("update method")
            let id = this.artFamMember_id;
            /*We begin by checking if the data entered by the user are correct*/
            console.log(this.artFam_type)
            if (this.artFam_type=="COMP"){
                axios.post('/comp/mb/verif', {
                    artMb_ref: this.artFamMb_ref,
                    artMb_designation: this.artFamMb_design,
                    artMb_validate : savedAs
                })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        console.log("verif passed")
                        this.errors = {};
                        const consultUrl = (id) => `/comp/mb/update/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_ref: this.artFamMb_ref,
                            artMb_designation: this.artFamMb_design,
                            artMb_validate : savedAs
                        })
                            /*If the data have been added in the database, we show a success message*/
                            .then(response => {
                                console.log("update passed")
                                this.addSuccess = true;
                                this.isInConsultMod = true;
                                if (lifesheet_created == true) {
                                    axios.post('/artFam/history/add/' + this.artFam_type.toLowerCase() + '/' + id, {
                                        history_reasonUpdate: reason,
                                    });
                                    window.location.reload();
                                }
                                this.$snotify.success(`CompFamMember updated successfully and saved as ${savedAs}`);
                                this.artFamMember_id = response.data;
                                this.artFamMb_validate = savedAs;
                            })
                            .catch(error => {
                                this.errors = error.response.data.errors;
                            });
                    })
                    .catch(error => this.errors = error.response.data.errors);
            }else{
                if (this.artFam_type=="RAW"){
                    console.log("le cas du RAW")
                    axios.post('/raw/mb/verif', {
                        artMb_ref: this.artFamMb_ref,
                        artMb_designation: this.artFamMb_design,
                        artMb_validate : savedAs
                    })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            console.log("verif passed")
                            this.errors = {};
                            const consultUrl = (id) => `/raw/mb/update/${id}`;
                            axios.post(consultUrl(id), {
                                artMb_ref: this.artFamMb_ref,
                                artMb_designation: this.artFamMb_design,
                                artMb_validate : savedAs
                            })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    if (lifesheet_created == true) {
                                        axios.post('/artFam/history/add/' + this.artFam_type.toLowerCase() + '/' + id, {
                                            history_reasonUpdate: reason,
                                        });
                                        window.location.reload();
                                    }
                                    this.$snotify.success(`CompFamMember updated successfully and saved as ${savedAs}`);
                                    this.artFamMember_id = response.data;
                                    this.artFamMb_validate = savedAs;
                                })
                                .catch(error => {
                                    this.errors = error.response.data.errors;
                                });
                        })
                        .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.artFam_type=="CONS"){
                        axios.post('/cons/mb/verif', {
                            artMb_ref: this.artFamMb_ref,
                            artMb_designation: this.artFamMb_design,
                            artMb_validate : savedAs
                        })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                this.errors = {};
                                const consultUrl = (id) => `/cons/mb/update/${id}`;
                                axios.post(consultUrl(id), {
                                    artMb_ref: this.artFamMb_ref,
                                    artMb_designation: this.artFamMb_design,
                                    artMb_validate : savedAs
                                })
                                    /*If the data have been added in the database, we show a success message*/
                                    .then(response => {
                                        this.addSuccess = true;
                                        this.isInConsultMod = true;
                                        if (artSheet_created == true) {
                                            axios.post('/artFam/history/add/' + this.artFam_type.toLowerCase() + '/' + id, {
                                                history_reasonUpdate: reason,
                                            });
                                            window.location.reload();
                                        }
                                        this.$snotify.success(`ConsFamMember updated successfully and saved as ${savedAs}`);
                                        this.artFamMember_id = response.data;
                                        this.artFamMb_validate = savedAs;
                                    })
                                    .catch(error => {
                                        this.errors = error.response.data.errors;
                                    });
                            })
                            .catch(error => this.errors = error.response.data.errors);
                    }
                }
            }
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
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
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
            }else {
                this.$emit('deleteFile', '')
                this.$refs.sucessAlert.showAlert(`Empty Equipment file deleted successfully`);
            }
        },
    },
}
</script>

<style>
</style>


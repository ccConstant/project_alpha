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
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artMb_dimension"
                    name="artMb_dimension" label="Article Member Dimension"
                    :isDisabled="this.isInConsultMod"
                    isRequired
                    v-model="artMb_dimension"
                    :info_text="'Article Member Dimension'"
                    :min="0"
                    :max="50"
                />
            
            
                <SaveButtonForm v-if="this.addSucces==false"
                    ref="saveButton"
                    @add="addArticleMember"
                    @update="updateArticleMember"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"/>
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



export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        InputSelectForm

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
        dimension: {
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
            artMb_dimension: this.dimension,
            artMb_validate: this.validate,
            artFamMember_id: this.id,
            art_id_add: this.art_id,
            artFam_type:this.art_type,
            art_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_artFamMember: [],
        }
    },
    created(){
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
                 console.log("type")
                 console.log(this.artFam_type)
                /*We begin by checking if the data entered by the user are correct*/
                if (this.artFam_type=="COMP"){
                    console.log("type=COMP")
                    axios.post('/comp/mb/verif', {
                        artMb_dimension: this.artMb_dimension,
                        artFam_validate: savedAs,
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        console.log("after verif")
                        console.log(id)
                        this.errors = {};
                        const consultUrl = (id) => `/comp/mb/add/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_dimension: this.artMb_dimension,
                              artFam_validate: savedAs,
                        })
                        /*If the data have been added in the database, we show a success message*/
                        .then(response => {
                            console.log("after add")
                            console.log(response.data)
                            this.addSuccess = true;
                            this.isInConsultMod = true;
                            console.log(this.addSuccess)
                            console.log(this.isInConsultMod)
                            this.$snotify.success(`CompFamMember added successfully and saved as ${savedAs}`);
                            this.artFamMember_id = response.data;
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.artFam_type=="RAW"){
                        axios.post('/raw/mb/verif', {
                        artMb_dimension: this.artMb_dimension,
                        artFam_validate: savedAs,
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        console.log("after verif")
                        this.errors = {};
                        const consultUrl = (id) => `/raw/mb/add/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_dimension: this.artMb_dimension,
                            artFam_validate: savedAs,
                        })
                        /*If the data have been added in the database, we show a success message*/
                        .then(response => {
                            console.log("after add")
                            console.log(response.data)
                            this.addSuccess = true;
                            this.isInConsultMod = true;
                            console.log(this.addSuccess)
                            console.log(this.isInConsultMod)
                            this.$snotify.success(`CompFamMember added successfully and saved as ${savedAs}`);
                            this.artFamMember_id = response.data;
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
                    }else{
                        if (this.artFam_type=="CONS"){
                            axios.post('/cons/mb/verif', {
                                artMb_dimension: this.artMb_dimension,
                                artFam_validate: savedAs,
                            })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                console.log("after verif")
                                this.errors = {};
                                const consultUrl = (id) => `/cons/mb/add/${id}`;
                                axios.post(consultUrl(id), {
                                    artMb_dimension: this.artMb_dimension,
                                    artFam_validate: savedAs,
                                })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    console.log("after add")
                                    console.log(response.data)
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    console.log(this.addSuccess)
                                    console.log(this.isInConsultMod)
                                    this.$snotify.success(`ConsFamMember added successfully and saved as ${savedAs}`);
                                    this.artFamMember_id = response.data;
                                })
                                .catch(error => this.errors = error.response.data.errors);
                            })
                            .catch(error => this.errors = error.response.data.errors);
                        }else{
                            if (this.artFam_type=="RAW"){
                                axios.post('/raw/mb/verif', {
                                    artMb_dimension: this.artMb_dimension,
                                    artFam_validate: savedAs,
                                })
                                /*If the data are correct, we send them to the controller for add them in the database*/
                                .then(response => {
                                    console.log("after verif")
                                    this.errors = {};
                                    axios.post('/raw/mb/add', {
                                        artMb_dimension: this.artMb_dimension,
                                        artFam_validate: savedAs,
                                    })
                                    /*If the data have been added in the database, we show a success message*/
                                    .then(response => {
                                        console.log("after add")
                                        console.log(response.data)
                                        this.addSuccess = true;
                                        this.isInConsultMod = true;
                                        console.log(this.addSuccess)
                                        console.log(this.isInConsultMod)
                                        this.$snotify.success(`RawFamMember added successfully and saved as ${savedAs}`);
                                        this.artFamMember_id = response.data;
                                    })
                                    .catch(error => this.errors = error.response.data.errors);
                                })
                                .catch(error => this.errors = error.response.data.errors);
                            }
                        }
                    }
                }
            }
        },     
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateArticleMember(savedAs, reason, artSheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            axios.post('/file/verif', {
                file_name: this.file_name,
                file_location: this.file_location,
                file_validate: savedAs,
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    Type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                    const consultUrl = (id) => `/equipment/update/file/${id}`;
                    axios.post(consultUrl(this.file_id), {
                        file_name: this.file_name,
                        file_location: this.file_location,
                        eq_id: this.equipment_id_update,
                        file_validate: savedAs
                    })
                        .then(response => {
                            this.file_validate = savedAs;
                            /*We test if a life sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            const id = this.equipment_id_update;
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/equipment/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Equipment file updated successfully and saved as ${savedAs}`);
                        })
                        /*If the controller sends errors, we put it in the error object*/
                        .catch(error => this.errors = error.response.data.errors);
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
                console.log("suppression");
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


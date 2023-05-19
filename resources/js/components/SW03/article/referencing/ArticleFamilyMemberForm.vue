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
                <p v-if="artMb_dimension !== undefined">
                    Member reference : {{ data_genRef.replace('(' + data_varCharac + ')', artMb_dimension) }}
                </p>
                <div v-if="artMb_sameValues">
                    <p v-if="artMb_dimension !== undefined">
                        Member Designation : {{ data_genDesign.replace('(' + data_varCharac + ')', artMb_dimension) }}
                    </p>
                </div>
                <div v-else>
                    <p v-if="artMb_designation !== undefined">
                        Member Designation : {{ data_genDesign.replace('(' + data_varCharac + ')', artMb_designation) }}
                    </p>
                </div>
                <div>
                    <p>
                        Variable Characteristic : {{ data_varCharacDesign }}
                    </p>
                    <InputTextForm
                        :inputClassName="null"
                        :Errors="errors.artMb_dimension"
                        name="artMb_dimension"
                        label="Article Member Dimension"
                        :isDisabled="this.isInConsultMod"
                        isRequired
                        v-model="artMb_dimension"
                        :info_text="this.infos_artFamMember[0].info_value"
                        :min="0"
                        :max="50"
                    />
                    <RadioGroupForm
                        :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                        :name="'Same value ?'"
                        :label="'sameValues'"
                        v-model="artMb_sameValues"
                        :info_text="this.infos_artFamMember[1].info_value"
                        :inputClassName="null"
                        :Errors="errors['Active']"
                        :checkedOption="isInModifMod ? artMb_sameValues : true"
                    />
                    <InputTextForm
                        v-if="!artMb_sameValues"
                        :inputClassName="null"
                        :Errors="errors.artMb_designation"
                        name="artMb_designation"
                        label="Article Member Designation"
                        :isDisabled="this.isInConsultMod"
                        isRequired
                        v-model="artMb_designation"
                        :info_text="null"
                        :min="0"
                        :max="50"
                    />
                </div>
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
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";



export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        RadioGroupForm,
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
        designation: {
            type: String
        },
        sameValues: {
            type: Boolean,
            default: true
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
        genRef: {
            type: String
        },
        genDesign: {
            type: String
        },
        varCharac: {
            type: String
        },
        varCharacDesign: {
            type: String
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
            artMb_dimension: this.dimension,
            artMb_designation: this.designation,
            artMb_sameValues: this.sameValues,
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
            data_genRef: this.genRef,
            data_genDesign: this.genDesign,
            data_varCharac: this.varCharac,
            data_varCharacDesign: this.varCharacDesign,
        }
    },
    created(){
        axios.get('/info/send/article_member')
            .then(response => {
                this.infos_artFamMember = response.data;
                this.loaded=true;
            })
            .catch(error => {
            });
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
                if (this.artFam_type=="COMP"){
                    axios.post('/comp/mb/verif', {
                        artMb_dimension: this.artMb_dimension,
                        artFam_validate: savedAs,
                        artMb_sameValues: this.artMb_sameValues,
                        artMb_designation: this.artMb_designation,
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        const consultUrl = (id) => `/comp/mb/add/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_dimension: this.artMb_dimension,
                            artFam_validate: savedAs,
                            artMb_sameValues: this.artMb_sameValues,
                            artMb_designation: this.artMb_designation,
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
                        artMb_dimension: this.artMb_dimension,
                        artFam_validate: savedAs,
                        artMb_sameValues: this.artMb_sameValues,
                        artMb_designation: this.artMb_designation,
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        const consultUrl = (id) => `/raw/mb/add/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_dimension: this.artMb_dimension,
                            artFam_validate: savedAs,
                            artMb_sameValues: this.artMb_sameValues,
                            artMb_designation: this.artMb_designation,
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
                        if (this.artFam_type=="CONS"){
                            axios.post('/cons/mb/verif', {
                                artMb_dimension: this.artMb_dimension,
                                artFam_validate: savedAs,
                                artMb_sameValues: this.artMb_sameValues,
                                artMb_designation: this.artMb_designation,
                            })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                this.errors = {};
                                const consultUrl = (id) => `/cons/mb/add/${id}`;
                                axios.post(consultUrl(id), {
                                    artMb_dimension: this.artMb_dimension,
                                    artFam_validate: savedAs,
                                    artMb_sameValues: this.artMb_sameValues,
                                    artMb_designation: this.artMb_designation,
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
        updateArticleMember(savedAs, reason, artSheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            let id = this.artFamMember_id;
            /*We begin by checking if the data entered by the user are correct*/
            if (this.artFam_type=="COMP"){
                axios.post('/comp/mb/verif', {
                    artMb_dimension: this.artMb_dimension,
                    artFam_validate: savedAs,
                    artMb_sameValues: this.artMb_sameValues,
                    artMb_designation: this.artMb_designation,
                })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        const consultUrl = (id) => `/comp/mb/update/${id}`;
                        axios.post(consultUrl(id), {
                            artMb_dimension: this.artMb_dimension,
                            artFam_validate: savedAs,
                            artMb_sameValues: this.artMb_sameValues,
                            artMb_designation: this.artMb_designation,
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
                                this.$snotify.success(`CompFamMember updated successfully and saved as ${savedAs}`);
                                this.artFamMember_id = response.data;
                            })
                            .catch(error => {
                                this.errors = error.response.data.errors;
                            });
                    })
                    .catch(error => this.errors = error.response.data.errors);
            }else{
                if (this.artFam_type=="RAW"){
                    axios.post('/raw/mb/verif', {
                        artMb_dimension: this.artMb_dimension,
                        artFam_validate: savedAs,
                        artMb_sameValues: this.artMb_sameValues,
                        artMb_designation: this.artMb_designation,
                    })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            this.errors = {};
                            const consultUrl = (id) => `/raw/mb/update/${id}`;
                            axios.post(consultUrl(id), {
                                artMb_dimension: this.artMb_dimension,
                                artFam_validate: savedAs,
                                artMb_sameValues: this.artMb_sameValues,
                                artMb_designation: this.artMb_designation,
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
                                    this.$snotify.success(`CompFamMember updated successfully and saved as ${savedAs}`);
                                    this.artFamMember_id = response.data;
                                })
                                .catch(error => {
                                    this.errors = error.response.data.errors;
                                });
                        })
                        .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.artFam_type=="CONS"){
                        axios.post('/cons/mb/verif', {
                            artMb_dimension: this.artMb_dimension,
                            artFam_validate: savedAs,
                            artMb_sameValues: this.artMb_sameValues,
                            artMb_designation: this.artMb_designation,
                        })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                this.errors = {};
                                const consultUrl = (id) => `/cons/mb/update/${id}`;
                                axios.post(consultUrl(id), {
                                    artMb_dimension: this.artMb_dimension,
                                    artFam_validate: savedAs,
                                    artMb_sameValues: this.artMb_sameValues,
                                    artMb_designation: this.artMb_designation,
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


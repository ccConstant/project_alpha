<!--File name : MMEFileForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to add a file to the MME documentation-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputTextForm v-model="file_name" :Errors="errors.file_name" :info_text="infos_file[0].info_value"
                               :isDisabled="!!isInConsultedMod" inputClassName="form-control" label="File name :"
                               name="file_name"/>
                <InputTextForm v-model="file_location" :Errors="errors.file_location" :info_text="infos_file[1].info_value"
                               :isDisabled="!!isInConsultedMod" inputClassName="form-control" label="File location :"
                               name="file_location"/>
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this file doesn't have a id the addMmeFile is called function else the updateMmeFile function is called -->
                    <div v-if="this.file_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="file_validate" @add="addMmeFile"
                                            @update="updateMmeFile"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="file_validate"
                                            @add="addMmeFile" @update="updateMmeFile"/>
                        </div>
                    </div>
                    <div v-else-if="this.file_id!==null">
                        <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="file_validate"
                                        @add="addMmeFile" @update="updateMmeFile"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :consultMod="this.isInConsultedMod" :validationMode="file_validate"
                                           @deleteOk="deleteComponent"/>

                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'

export default {
    components: {
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert
    },
    props: {
        name: {
            type: String
        },
        location: {
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
        mme_id: {
            type: Number
        }
    },
    data() {
        return {
            file_name: this.name,
            file_location: this.location,
            file_validate: this.validate,
            file_id: this.id,
            mme_id_add: this.mme_id,
            mme_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            infos_file: []
        }
    },
    methods: {
        /*Sending to the controller all the information about the mme so that it can be added to the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the update
            lifesheet_created: If we have already a lifesheet created for this file */
        addMmeFile(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                //ID of the mme in which the file will be added
                let id;
                //If the user is not in the modification mode, we set the id with the value of mme_id_add
                if (!this.modifMod) {
                    id = this.mme_id_add
                    //else the user is in the update menu, we allocate to the id the value of the id get in the url
                } else {
                    id = this.mme_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                Name, location and validate option are sent to the controller*/
                axios.post('/file/verif', {
                    file_name: this.file_name,
                    file_location: this.file_location,
                    file_validate: savedAs,
                    user_id: this.$userId.id,
                    reason: 'add'
                }).then(response => {
                    this.errors = {};
                    /*If all the verification passed, a new post this time to add the file in the database
                    The name, location of the file, validate options and id of the MME linked are sent to the controller*/
                    axios.post('/mme/add/file', {
                        file_name: this.file_name,
                        file_location: this.file_location,
                        file_validate: savedAs,
                        mme_id: id

                    })
                        //If the file is added successfully
                        .then(response => {
                            //We test if a life sheet has been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/mme/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            //If the user is not in modification mode
                            if (!this.modifMod) {
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod = true;
                                this.addSucces = true
                            }
                            //the id of the file take the value of the newly created id
                            this.file_id = response.data;
                            //The validate option of this file takes the value of savedAs
                            this.file_validate = savedAs;
                            this.$refs.sucessAlert.showAlert(`MME file added successfully and saved as ${savedAs}`);
                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }

        },
        /*Sending to the controller all the information about the mme so that it can be updated in the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validateD or validated
            reason: Reason of this update
            lifesheet_created: If a lifesheet already exist for this file*/
        updateMmeFile(savedAs, reason, lifesheet_created) {
            axios.post('/file/verif', {
                file_name: this.file_name,
                file_location: this.file_location,
                file_validate: savedAs,
                user_id: this.$userId.id,
                reason:'update',
                file_id: this.file_id,
                lifesheet_created: lifesheet_created
            }).then(response => {
                this.errors = {};
                /*If all the check passed, a new post this time to add the file in the database
                    The name, location, validate option and id of the MME affected are sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                const consultUrl = (id) => `/mme/update/file/${id}`;
                axios.post(consultUrl(this.file_id), {
                    file_name: this.file_name,
                    file_location: this.file_location,
                    mme_id: this.mme_id_update,
                    file_validate: savedAs
                }).then(response => {
                    const id = this.mme_id_update;
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/mme/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.file_validate = savedAs;
                    this.$refs.sucessAlert.showAlert(`MME file updated successfully and saved as ${savedAs}`);
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        //Function for deleting a file from the view and the database
        deleteComponent(reason, lifesheet_created) {
            //If the user is in update mode and the file exist in the database
            if (this.modifMod == true && this.file_id !== null) {
                //Send a post-request with the id of the file who will be deleted in the url
                const consultUrl = (id) => `/mme/delete/file/${id}`;
                axios.post(consultUrl(this.file_id), {
                    mme_id: this.mme_id_update,
                    user_id: this.$userId.id,
                    lifesheet_created: lifesheet_created
                }).then(response => {
                    const id = this.mme_id_update;
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/mme/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteFile', '')
                    this.$refs.sucessAlert.showAlert(`MME file deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);

            } else {
                this.$emit('deleteFile', '')
                this.$refs.sucessAlert.showAlert(`Empty MME file deleted successfully`);
            }
        }
    },
    created() {
        axios.get('/info/send/file')
            .then(response => {
                this.infos_file = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    }
}
</script>

<style>

</style>

<!--File name : ArticleStorageConditionForm.vue -->
<!--Creation date : 27 Apr 2023-->
<!--Update date : 27 Apr 2023 -->
<!--Vue Component of the Form of the article storage condition who call all the input component-->

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
                    name="artFam_storageCondition"
                    :Errors="errors.artFam_storageCondition"
                    label="Article Family Storage Condition :"
                    :options="enumArticle_storageCondition"
                    :selctedOption="artFam_storageCondition"
                    :isDisabled="this.isInConsultMod"
                    v-model="artFam_storageCondition"
                    :info_text="'Article Family Storage Condition value'"
                    :id_actual="'ArticleFamilyStorageCondition'"
                />
                <SaveButtonForm v-if="this.addSucces==false"
                    ref="saveButton"
                    @add="addStorageConditions"
                    @update="updateStorageConditions"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"
                />
                <DeleteComponentButton v-if="this.isInModifMod"
                    @delete="deleteComponent"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                />
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
        value: {
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
            artFam_storageCondition: this.value,
            storageCondition_validate: this.validate,
            storageCondition_id: this.id,
            art_id_add: this.art_id,
            artFam_type:this.art_type,
            art_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_storageCondition: [],
            enumArticle_storageCondition: []
        }
    },
    methods: {
        /*Sending to the controller all the information about the article so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        addStorageConditions(savedAs, reason, artSheet_created) {
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
                const consultUrl = (id) => `/artFam/enum/storageCondition/link/${id}`;
                axios.post(consultUrl(id), {
                    artFam_type: this.artFam_type.toUpperCase(),
                    artFam_storageCondition: this.artFam_storageCondition,
                })

                /*If the storage condition have been linked successfully*/
                .then(response => {
                    /*We test if a article sheet has been already created*/
                    /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                    /* TODO
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }*/
                    this.$refs.sucessAlert.showAlert(`Storage conditions have been successfully linked to the article`);
                    /*If the user is not in modification mode*/
                    this.isInConsultedMod = true;
                    this.addSucces = true
                    /*the id of the file take the value of the newly created id*/
                    this.storageCondition_id = response.data;
                    /*The validate option of this file takes the value of savedAs(Params of the function)*/
                    this.storageCondition_validate = savedAs;
                })
                /*If the controller sends errors, we put it in the error object*/
                .catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateStorageConditions(savedAs, reason, lifesheet_created) {
            const consultUrl = (type, id) => `/artFam/enum/storageCondition/update/${type}/${id}`;
            axios.post(consultUrl(this.artFam_type, this.art_id_add), {
                value: this.artFam_storageCondition,
                id: this.storageCondition_id
            }).then(response =>{
                if (artSheet_created == true) {
                    axios.post('/history/add/' + this.artFam_type.toLowerCase() + '/' + this.artFam_id, {
                        history_reasonUpdate: reason,
                    });
                    window.location.reload();
                }
                this.$refs.sucessAlert.showAlert(`Storage conditions have been successfully linked to the article`);
                /*If the user is not in modification mode*/
                this.isInConsultedMod = true;
                this.addSucces = true
            }).catch(error =>{
                this.errors = error.response.data.errors;
            })
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            /*If the user is in update mode and the file exist in the database*/
            if (this.modifMod == true && this.storageCondition_id !== null) {
                /*Send a post-request with the id of the file who will be deleted in the url*/
                const consultUrl = (type, id) => `/artFam/enum/storageCondition/unlink/${type}/${id}`;
                axios.post(consultUrl(this.artFam_type, this.storageCondition_id), {
                    artFam_id: this.art_id
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
                        this.$refs.sucessAlert.showAlert(`Storage condition deleted successfully`);
                    })
                    /*If the controller sends errors, we put it in the error object*/
                    .catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteFile', '')
                this.$refs.sucessAlert.showAlert(`Empty storage condition deleted successfully`);
            }
        }
    },
    created() {
        axios.get('/artFam/enum/storageCondition')
            .then(response => {
                this.enumArticle_storageCondition = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    },
}
</script>

<style>
</style>

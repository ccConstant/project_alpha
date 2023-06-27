<!--File name : EquipmentUsgForm.vue-->
<!--Creation date : 18 May 2022-->
<!--Update date : 5 Apr 2023-->
<!--Vue Component of the Form of the usage  who call all the input component-->

<template>
    <div :class="divClass" @keydown="clearError">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container">
                <!--Call of the different component with their props-->
                <InputTextAreaForm v-model="usg_type" :Errors="errors.usg_type" :info_text="infos_usage[0].info_value"
                                   :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50" label="Type :"
                                   name="usg_type"/>
                <InputTextAreaForm v-model="usg_precaution" :Errors="errors.usg_precaution"
                                   :info_text="infos_usage[1].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Precaution :" name="usg_precaution"/>
                <!--If addSuccess is equal to false, the buttons appear -->
                <div v-if="this.addSuccess==false ">
                    <!--If this usage doesn't have a id the addEquipmentUsg is called function else the updateEquipmentUsg function is called -->
                    <div v-if="this.usg_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="usg_validate" @add="addEquipmentUsg"
                                            @update="updateEquipmentUsg"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="usg_validate"
                                            @add="addEquipmentUsg" @update="updateEquipmentUsg"/>
                        </div>
                    </div>
                    <div v-else-if="this.usg_id!==null && reformMod==false">
                        <div v-if="usg_reformDate!=null">
                            <p>Reformed at {{ usg_reformDate }}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                            :reformMod="this.isInReformMod" :savedAs="usg_validate"
                                            @add="addEquipmentUsg" @update="updateEquipmentUsg"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :consultMod="this.isInConsultedMod" :validationMode="usg_validate"
                                           @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && usg_reformDate===null">
                        <ReformComponentButton :info="infos_usage[2].info_value" :reformDate="usg_reformDate"
                                               :reformMod="this.isInReformMod" :reformedBy="usg_reformedBy"
                                               @reformOk="reformComponent"/>
                    </div>
                </div>
            </form>
            <SuccessAlert ref="SuccessAlert"/>
            <ErrorAlert ref="errorAlert"/>
        </div>
    </div>
</template>

<script>
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../../button/ReformComponentButton.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'

export default {
    components: {
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert,
        SuccessAlert
    },
    /*--------Declaration of the different props:--------
        type :
        precaution :
        validate: Validation option of the Usage
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this Usage form
        id: ID of an already created Usage
        eq_id: ID of the equipment in which the Usage will be added
    ---------------------------------------------------*/
    props: {
        type: {
            type: String
        },
        precaution: {
            type: String
        },
        value: {
            type: String
        },
        unit: {
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
        reformDate: {
            type: String,
            default: null
        },
        eq_id: {
            type: Number
        },
        reformMod: {
            type: Boolean,
            default: false
        }

    },
    /*--------Declaration of the different returned data:--------
        usg_type:
        usg_precaution:
        usg_validate: Validation option of the usage
        usg_id: ID oh this usage
        equipment_id_add: ID of the equipment in which the usage will be added
        equipment_id_update: ID of the equipment in which the usage will be updated
        errors: Object of errors in which will be stores the different error occurred when adding in database
        addSuccess: Boolean who tell if this usage has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data() {
        return {
            usg_type: this.type,
            usg_precaution: this.precaution,
            usg_validate: this.validate,
            usg_reformDate: this.reformDate,
            usg_id: this.id,
            equipment_id_add: this.eq_id,
            equipment_id_update: this.$route.params.id,
            errors: {},
            addSuccess: false,
            isInConsultedMod: this.consultMod,
            isInReformMod: this.reformMod,
            infos_usage: [],
            loaded: false

        }
    },
    methods: {
        /*Sending to the controller all the information about the mme so that it can be added in the database
          @param savedAs Value of the validation option: drafted, to_be_validated or validated
          @param reason The reason of the modification
          @param lifesheet_created */
        addEquipmentUsg(savedAs, reason, lifesheet_created) {
            if (!this.addSuccess) {
                /*ID of the equipment in which the usage will be added*/
                let id;
                /*If the user is not in the modification mode, we set the id with the value of equipment_id_add*/
                if (!this.modifMod) {
                    id = this.equipment_id_add
                    /*else if the user is in the modification mode, we set the id with the value of equipment_id_update*/
                } else {
                    id = this.equipment_id_update;
                }
                /*The First post to verify if all the fields are filled correctly,
                The type, name, value, unit and validate option are sent to the controller*/
                axios.post('/usage/verif', {
                    usg_type: this.usg_type,
                    usg_precaution: this.usg_precaution,
                    usg_validate: savedAs,
                    reason: 'add',
                    user_id: this.$userId.id
                }).then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the usage in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/equipment/add/usg', {
                        usg_type: this.usg_type,
                        usg_precaution: this.usg_precaution,
                        usg_validate: savedAs,
                        eq_id: id
                    })
                        /*If the usage is added successfully*/
                        .then(response => {
                            /*We test if a life sheet has been already created
                            If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/equipment/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.SuccessAlert.showAlert(`Equipment usage added successfully and saved as ${savedAs}`);
                            /*If the user is not in modification mode*/
                            if (!this.modifMod) {
                                /*The form pass in consulting mode and addSuccess pass to True*/
                                this.isInConsultedMod = true;
                                this.addSuccess = true
                            }
                            /*the id of the usage take the value of the newly created id*/
                            this.usg_id = response.data;
                            /*The validate option of this usage takes the value of savedAs(Params of the function)*/
                            this.usg_validate = savedAs;

                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be added in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateEquipmentUsg(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
                The type, name, value, unit and validate option are sent to the controller*/
            axios.post('/usage/verif', {
                usg_type: this.usg_type,
                usg_precaution: this.usg_precaution,
                usg_validate: savedAs,
                reason: 'update',
                user_id: this.$userId.id,
                lifesheet_created: lifesheet_created,
                usg_id:this.usg_id
            }).then(response => {
                this.errors = {};
                /*If all the verifications passed, a new post this time to add the usage in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller
                    In the post url the id correspond to the id of the usage who will be updated*/
                const consultUrl = (id) => `/equipment/update/usg/${id}`;
                axios.post(consultUrl(this.usg_id), {
                    usg_type: this.usg_type,
                    usg_precaution: this.usg_precaution,
                    usg_validate: savedAs,
                    eq_id: this.equipment_id_update,

                }).then(response => {
                    this.usg_validate = savedAs;
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created
                    If it's the case we create a new enregistrement of history for saved the reason of the update*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.SuccessAlert.showAlert(`Equipment usage updated successfully and saved as ${savedAs}`);
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a usage from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            /*If the user is in update mode and the usage exist in the database*/
            if (this.modifMod == true && this.usg_id !== null) {
                /*Send a post-request with the id of the usage who will be deleted in the url*/
                const consultUrl = (id) => `/equipment/delete/usg/${id}`;
                axios.post(consultUrl(this.usg_id), {
                    eq_id: this.equipment_id_update,
                    user_id: this.$userId.id,
                    lifesheet_created: lifesheet_created
                }).then(response => {
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created
                    If it's the case we create a new enregistrement of history for saved the reason of the deleting*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    /*Emit to the parent component that we want to delete this component*/
                    this.$emit('deleteUsg', '')
                    this.$refs.SuccessAlert.showAlert(`Equipment usage deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteUsg', '')
                this.$refs.SuccessAlert.showAlert(`Empty Equipment usage deleted successfully`);
            }
        },
        reformComponent(endDate) {
            /*If the user is in update mode and the usage exists in the database,
            we send a post-request with the id of the usage who will be deleted in the url*/
            if (this.$userId.user_makeReformRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            const consultUrl = (id) => `/equipment/reform/usg/${id}`;
            axios.post(consultUrl(this.usg_id), {
                eq_id: this.equipment_id_update,
                usg_reformDate: endDate,
                user_id: this.$userId.id

            }).then(response => {
                /*Emit to the parent component that we want to delete this component*/
                this.$emit('deleteUsg', '')
            }).catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.errors['usg_reformDate'])
            });
        }
    },
    created() {
        axios.get('/info/send/usage')
            .then(response => {
                this.infos_usage = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    }
}
</script>

<style lang="scss">
.titleForm {
    padding-left: 10px;
    display: inline-block;
}

form {
    margin: 20px;
    margin-bottom: 100px;
}
</style>

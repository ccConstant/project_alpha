<!--File name : EquipmentRiskForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 4 Apr 2023-->
<!--Vue Component of the Form of the equipment risk who call all the input component-->

<template>
    <div :class="divClass">
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputSelectForm v-model="risk_for" :Errors="errors.risk_for"
                                 :id_actual="riskFor" :info_text="infos_risk[0].info_value" :isDisabled="!!isInConsultedMod" :options="enum_risk_for"
                                 :selctedOption="this.risk_for" :selectedDivName="this.divClass"
                                 label="Risk for :" name="risk_for" selectClassName="form-select w-50"
                                 @clearSelectError='clearSelectError'/>
                <InputTextAreaForm v-model="risk_remarks" :Errors="errors.risk_remarks" :info_text="infos_risk[1].info_value"
                                   :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50" label="Remarks :"
                                   name="risk_remarks"/>
                <InputTextAreaForm v-model="risk_wayOfControl" :Errors="errors.risk_wayOfControl"
                                   :info_text="infos_risk[2].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Way of Control :" name="risk_wayOfControl"/>
                <!--If addSuccess is equal to false, the buttons appear -->
                <div v-if="this.addSuccess==false ">
                    <!--If this risk doesn't have a id the addEquipmentRisk is called function else the updateEquipmentRisk function is called -->
                    <div v-if="this.risk_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="risk_validate" @add="addEquipmentRisk"
                                            @update="updateEquipmentRisk"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="risk_validate"
                                            @add="addEquipmentRisk" @update="updateEquipmentRisk"/>
                        </div>
                    </div>
                    <div v-else-if="this.risk_id!==null">
                        <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                        :savedAs="risk_validate" @add="addEquipmentRisk"
                                        @update="updateEquipmentRisk"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :consultMod="this.isInConsultedMod" :validationMode="risk_validate"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <successAlert ref="successAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import successAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputSelectForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        successAlert
    },
    /*--------Declaration of the different props:--------
        for : Type of risk
        remarks : Remarks of the risk
        wayOfControl : Way of control of the risk
        validate: Validation option of the risk
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable the save button and show update button
        divClass: Class name of this risk form
        id: ID of an already created risk
        eq_id: ID of the equipment in which the risk will be added
        RiskForEq: If this props is present the risk is for the equipment
    ---------------------------------------------------*/
    props: {
        for: {
            type: String
        },
        remarks: {
            type: String
        },
        wayOfControl: {
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
        eq_id: {
            type: Number
        },
        prvMtnOp_id: {
            type: Number
        },
        riskForEq: {
            type: Boolean
        }
    },
    /*--------Declaration of the different returned data:--------
        risk_for: Type of risk
        risk_remarks: Remarks of the risk
        risk_wayOfControl: Way of control of the risk
        risk_validate: Validation option of the risk
        risk_id: ID oh this risk
        equipment_id_add: ID of the equipment in which the risk will be added
        equipment_id_update: ID of the equipment in which the risk will be updated
        enum_risk_for : Different types of risk gets from the database
        errors: Object of errors in which will be stores the different error occurred when adding in database
        addSuccess: Boolean who tell if this risk has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data() {
        return {
            risk_for: this.for,
            risk_remarks: this.remarks,
            risk_wayOfControl: this.wayOfControl,
            risk_validate: this.validate,
            risk_id: this.id,
            equipment_id_add: this.eq_id,
            equipment_id_update: this.$route.params.id,
            enum_risk_for: [],
            errors: {},
            addSuccess: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            infos_risk: [],
            riskFor: "RiskFor"
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    created() {
        /*Ask for the controller different types of the risk  */
        axios.get('/risk/enum/riskfor')
            .then(response => {
                this.enum_risk_for = response.data;
                axios.get('/info/send/risk')
                    .then(response => {
                        this.infos_risk = response.data;
                        this.loaded = true;
                    }).catch(error => {
                });
            }).catch(error => {
        });
    },
    methods: {
        /*Sending to the controller all the information about the mme so that it can be added in the database
         @param savedAs Value of the validation option: drafted, to_be_validated or validated
         @param reason The reason of the modification
         @param lifesheet_created */
        addEquipmentRisk(savedAs, reason, lifesheet_created) {
            if (!this.addSuccess) {
                /*ID of the equipment in which the risk will be added*/
                let id;
                /*If the user is not in the modification mode, we set the id with the value of equipment_id_add*/
                if (!this.modifMod) {
                    id = this.equipment_id_add
                    /*else the user is in the modification mode, we set the id with the value of equipment_id_update*/
                } else {
                    id = this.equipment_id_update;
                }
                /*The First post to verify if all the fields are filled correctly,
                The type, name, value, unit and validate option are sent to the controller*/
                axios.post('/risk/verif', {
                    risk_for: this.risk_for,
                    risk_remarks: this.risk_remarks,
                    risk_wayOfControl: this.risk_wayOfControl,
                    risk_validate: savedAs,
                }).then(response => {
                    this.errors = {};
                    /*If the user want to add equipment*/
                    if (this.riskForEq == true) {
                        /*If all the verifications passed, a new post this time to add the risk in the database
                        The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                        axios.post('/equipment/add/risk', {
                            risk_for: this.risk_for,
                            risk_remarks: this.risk_remarks,
                            risk_wayOfControl: this.risk_wayOfControl,
                            risk_validate: savedAs,
                            eq_id: id
                        })
                            /*If the risk is added successfully*/
                            .then(response => {
                                /*We test if a life sheet has been already created
                                If it's the case we create a new enregistrement of history for saved the reason of the update*/
                                if (lifesheet_created == true) {
                                    axios.post(`/history/add/equipment/${id}`, {
                                        history_reasonUpdate: reason,
                                    });
                                    window.location.reload();
                                }
                                this.$refs.successAlert.showAlert(`Equipment risk added successfully and saved as ${savedAs}`);
                                /*If the user is not in modification mode*/
                                if (!this.modifMod) {
                                    /*The form pass in consulting mode and addSuccess pass to True*/
                                    this.isInConsultedMod = true;
                                    this.addSuccess = true
                                }
                                /*the id of the risk take the value of the newly created id*/
                                this.risk_id = response.data;
                                /*The validate option of this risk takes the value of savedAs(Params of the function)*/
                                this.risk_validate = savedAs;
                            }).catch(error => this.errors = error.response.data.errors);
                    } else {
                        /*If all the verifications passed, a new post this time to add the risk in the database
                        The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                        axios.post("/equipment/add/prvMtnOp/risk", {
                            risk_for: this.risk_for,
                            risk_remarks: this.risk_remarks,
                            risk_wayOfControl: this.risk_wayOfControl,
                            risk_validate: savedAs,
                            eq_id: id,
                            prvMtnOp_id: this.prvMtnOp_id

                        })
                            /*If the risk is added successfully*/
                            .then(response => {
                                const id = this.equipment_id_update;
                                /*We test if a life sheet has been already created
                                If it's the case we create a new enregistrement of history for saved the reason of the update*/
                                if (lifesheet_created == true) {
                                    axios.post(`/history/add/equipment/${id}`, {
                                        history_reasonUpdate: reason,
                                    });
                                    window.location.reload();
                                }
                                this.$refs.successAlert.showAlert(`Preventive maintenance operation risk added successfully and saved as ${savedAs}`);
                                /*If the user is not in modification mode*/
                                if (!this.modifMod) {
                                    /*The form pass in consulting mode and addSuccess pass to True*/
                                    this.isInConsultedMod = true;
                                    this.addSuccess = true
                                }
                                /*the id of the risk take the value of the newly created id*/
                                this.risk_id = response.data;
                                /*The validate option of this risk takes the value of savedAs(Params of the function)*/
                                this.risk_validate = savedAs;
                            }).catch(error => this.errors = error.response.data.errors);
                    }
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be added in the database
         @param savedAs Value of the validation option: drafted, to_be_validated or validated
         @param reason The reason of the modification
         @param lifesheet_created */
        updateEquipmentRisk(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The type, name, value, unit and validate option are sent to the controller*/
            axios.post('/risk/verif', {
                risk_for: this.risk_for,
                risk_remarks: this.risk_remarks,
                risk_wayOfControl: this.risk_wayOfControl,
                risk_validate: savedAs,
            }).then(response => {
                this.errors = {};
                if (this.riskForEq == true) {

                    /*If all the verifications passed, a new post this time to add the risk in the database
                        The type, name, value, unit, validate option and id of the equipment are sent to the controller
                        In the post url the id correspond to the id of the risk who will be updated*/
                    let consultUrl = (id) => `/equipment/update/risk/${id}`;
                    axios.post(consultUrl(this.risk_id), {
                        risk_for: this.risk_for,
                        risk_remarks: this.risk_remarks,
                        risk_wayOfControl: this.risk_wayOfControl,
                        risk_validate: savedAs,
                        eq_id: this.equipment_id_update,
                    }).then(response => {
                        this.risk_validate = savedAs;
                        const id = this.equipment_id_update;
                        /*We test if a life sheet has been already created
                        If it's the case we create a new enregistrement of history for saved the reason of the update*/
                        if (lifesheet_created == true) {
                            axios.post(`/history/add/equipment/${id}`, {
                                history_reasonUpdate: reason,
                            });
                            window.location.reload();
                        }
                        this.$refs.successAlert.showAlert(`Equipment risk updated successfully and saved as ${savedAs}`);
                    }).catch(error => this.errors = error.response.data.errors);
                } else {
                    /*If all the verifications passed, a new post this time to add the risk in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller
                    In the post url the id correspond to the id of the risk who will be updated*/
                    const consultUrl = (id) => `/equipment/update/prvMtnOp/risk/${id}`;
                    axios.post(consultUrl(this.risk_id), {
                        risk_for: this.risk_for,
                        risk_remarks: this.risk_remarks,
                        risk_wayOfControl: this.risk_wayOfControl,
                        risk_validate: savedAs,
                        prvMtnOp_id: this.prvMtnOp_id,
                        eq_id: this.equipment_id_update,
                    })
                        .then(response => {
                            this.risk_validate = savedAs;
                            const id = this.equipment_id_update;
                            /* We test if a life sheet has been already created
                             If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/equipment/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.successAlert.showAlert(`Preventive maintenance operation risk updated successfully and saved as ${savedAs}`);
                        }).catch(error => this.errors = error.response.data.errors);
                }
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a risk from the view and the database
        @param reason The reason of the modification
        @param lifesheet_created
        */
        deleteComponent(reason, lifesheet_created) {
            /*If the user is in update mode and the risk exist in the database*/
            if (this.modifMod == true && this.risk_id !== null) {
                /*Send a post-request with the id of the risk who will be deleted in the url*/
                const consultUrl = (id) => `/equipment/delete/risk/${id}`;
                axios.post(consultUrl(this.risk_id), {
                    eq_id: this.equipment_id_update
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
                    this.$emit('deleteRisk', '')
                    this.$refs.successAlert.showAlert(`Risk deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteRisk', '')
                this.$refs.successAlert.showAlert(`Empty risk deleted successfully`);
            }
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
    }
}
</script>

<style lang="scss">
.hr {
    display: block;
    flex: 1;
    height: 3px;
    background: #D4D4D4;
}

.titleForm {
    padding-left: 10px;
}

form {
    margin: 20px;
    margin-bottom: 100px;
}
</style>

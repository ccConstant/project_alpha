<!--File name : MMEUsageForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to add or edit a precaution for a MME-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputTextAreaForm v-model="usg_measurementType" :Errors="errors.usg_measurementType"
                                   :info_text="infos_usage[0].info_value" :isDisabled="!!isInConsultedMod"
                                   inputClassName="form-control w-50" label="Measurement type :"
                                   name="usg_measurementType"/>
                <InputTextAreaForm v-model="usg_precision" :Errors="errors.usg_precision"
                                   :info_text="infos_usage[1].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Precision :" name="usg_precision"/>
                <InputTextAreaForm v-model="usg_application" :Errors="errors.usg_application"
                                   :info_text="infos_usage[2].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Application :" name="usg_application"/>
                <InputSelectForm v-model="usg_metrologicalLevel" :Errors="errors.usg_metrologicalLevel"
                                 :id_actual="usageMetrologicalLevel" :info_text="infos_usage[3].info_value"
                                 :isDisabled="!!isInConsultedMod" :options="enum_metrologicalLevel"
                                 :selctedOption="this.usg_metrologicalLevel" :selectedDivName="this.divClass"
                                 label="Metrological level :" name="usg_metrologicalLevel"
                                 selectClassName="form-select w-50" @clearSelectError='clearSelectError'/>

                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <div v-if="this.usg_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="usg_validate" @add="addMmeUsage"
                                            @update="updateMmeUsage"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="usg_validate"
                                            @add="addMmeUsage" @update="updateMmeUsage"/>
                        </div>
                    </div>
                    <div v-else-if="this.usg_id!==null && reformMod==false ">
                        <div v-if="usg_reformDate!=null">
                            <p>Reform at {{ usg_reformDate }}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                            :savedAs="usg_validate" @add="addMmeUsage"
                                            @update="updateMmeUsage"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :Errors="errors.usg_delete" :consultMod="this.isInConsultedMod"
                                           :validationMode="usg_validate" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && usg_reformDate===null">
                        <ReformComponentButton :reformBy="usg_reformBy" :reformDate="usg_reformDate"
                                               :reformMod="this.isInReformMod" @reformOk="reformComponent"/>
                    </div>


                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
            <div v-if="this.usg_id!==null && modifMod==false & consultMod==false && import_id==null ">
                <ReferenceAMMEPrecaution :mme_id="this.mme_id" :usg_id="this.usg_id"/>
            </div>
            <div v-else-if="this.usg_id!==null && modifMod==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :consultMod="!!isInConsultedMod"
                                         :importedPrctn="importedUsgPrecaution" :mme_id="this.mme_id" :modifMod="!!this.modifMod"
                                         :usg_id="this.usg_id"/>
            </div>
            <div v-else-if="loaded==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :consultMod="!!isInConsultedMod"
                                         :importedPrctn="importedUsgPrecaution" :mme_id="this.mme_id" :modifMod="!!this.modifMod"
                                         :usg_id="this.usg_id"/>
            </div>
            <ErrorAlert ref="errorAlert"/>
        </div>

    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'

import ReferenceAMMEPrecaution from './ReferenceAMMEPrecaution.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../../button/ReformComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        SaveButtonForm,
        InputTextAreaForm,
        InputSelectForm,
        ReferenceAMMEPrecaution,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert,
        SucessAlert
    },
    props: {
        measurementType: {
            type: String,
            default: null
        },
        precision: {
            type: String
        },
        metrologicalLevel: {
            type: String
        },
        application: {
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
        },
        reformDate: {
            type: String,
            default: null
        },
        reformBy: {
            type: String,
            dfault: null
        },
        import_id: {
            type: Number,
            default: null
        },
        reformMod: {
            type: Boolean,
            default: false
        }

    },
    data() {
        return {
            usg_measurementType: this.measurementType,
            usg_precision: this.precision,
            usg_metrologicalLevel: this.metrologicalLevel,
            usg_application: this.application,
            usg_validate: this.validate,
            usg_reformDate: this.reformDate,
            usg_reformBy: this.reformBy,
            usg_id: this.id,
            mme_id_add: this.mme_id,
            mme_id_update: this.$route.params.id,
            enum_metrologicalLevel: [],
            importedUsgPrecaution: [],
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInReformMod: this.reformMod,
            infos_usage: [],
            usageMetrologicalLevel: "MetrologicalLevel",
        }
    },
    created() {
        axios.get('/usage/enum/metrologicalLevel')
            .then(response => {
                this.enum_metrologicalLevel = response.data;
                if (this.usg_id !== null && this.addSucces == false) {
                    //Make a get request to ask the controller the precaution corresponding to the id
                    const consultUrl = (id) => `/precaution/send/${id}`;
                    axios.get(consultUrl(this.usg_id))
                        .then(response => {
                            this.importedUsgPrecaution = response.data;
                            /*
        Ask all the information present in the dictionary, relating to the mme_usage
         */
                            axios.get('/info/send/mme_usage')
                                .then(response => {
                                    this.infos_usage = response.data;
                                    this.loaded = true;
                                }).catch(error => {
                            });
                        }).catch(error => {
                    });
                } else {
                    /*
        Ask all the information present in the dictionary, relating to the mme_usage
         */
                    axios.get('/info/send/mme_usage')
                        .then(response => {
                            this.infos_usage = response.data;
                            this.loaded = true;
                        }).catch(error => {
                    });
                }
            }).catch(error => {
        });
    },
    methods: {
        /*Sending to the controller all the information about the mme usage so that it can be added to the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the update
            lifesheet_created: If a lifesheet already exist for the MME*/
        addMmeUsage(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                //ID of the Mme in which the MME usage will be added
                let id;
                //If the user is not in the modification mode, we set the id with the value of mme_id_add
                if (!this.modifMod) {
                    id = this.mme_id_add
                    //else the user is in the update menu, we allocate to the id the value of the id get in the url
                } else {
                    id = this.mme_id_update;
                }
                /*The first post to check if all the fields are filled correctly
                The measurement type, precision, metrological level, application and validate options are sent to the controller*/
                axios.post('/mme_usage/verif', {
                    usg_measurementType: this.usg_measurementType,
                    usg_precision: this.usg_precision,
                    usg_metrologicalLevel: this.usg_metrologicalLevel,
                    usg_application: this.usg_application,
                    usg_validate: savedAs,
                    user_id: this.$userId.id,
                    reason:'add',
                }).then(response => {
                    this.errors = {};
                    /*If all the check passed, a new post this time to add the MME usage in the database
                    The measurement type, precision, metrological level, application and validate options are sent to the controller*/
                    axios.post('/mme/add/usg', {
                        usg_measurementType: this.usg_measurementType,
                        usg_precision: this.usg_precision,
                        usg_metrologicalLevel: this.usg_metrologicalLevel,
                        usg_application: this.usg_application,
                        usg_validate: savedAs,
                        mme_id: id

                    })
                        //If the MME usage is added successfully
                        .then(response => {
                            //We test if a life sheet has been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/mme/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`MME Usage added successfully and saved as ${savedAs}`);
                            //If the user is not in the modification mode
                            if (!this.modifMod) {
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod = true;
                                this.addSucces = true
                            }
                            //the id of the MME usage take the value of the newly created id
                            this.usg_id = response.data;
                            //The validate option of this MME usage takes the value of savedAs
                            this.usg_validate = savedAs;

                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be updated in the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the update
            lifesheet_created: If a lifesheet already exist for the MME*/
        updateMmeUsage(savedAs, reason, lifesheet_created) {
            /*The first post to verify if all the fields are filled correctly
                The measurement type, precision, metrological level, application and validate options are sent to the controller*/
            axios.post('/mme_usage/verif', {
                usg_measurementType: this.usg_measurementType,
                usg_precision: this.usg_precision,
                usg_metrologicalLevel: this.usg_metrologicalLevel,
                usg_application: this.usg_application,
                usg_validate: savedAs,
                user_id:this.$userId.id,
                reason:'update',
                usg_id:this.usg_id,
                lifesheet_created:lifesheet_created,
            }).then(response => {
                this.errors = {};
                /*If all the check has passed, a new post this time to add the MME usage in the database
                    The measurement type, precision, metrological level, application and validate options are sent to the controller
                    In the post url the id correspond to id of the MME usage who will be updated*/
                const consultUrl = (id) => `/mme/update/usg/${id}`;
                axios.post(consultUrl(this.usg_id), {
                    usg_measurementType: this.usg_measurementType,
                    usg_precision: this.usg_precision,
                    usg_metrologicalLevel: this.usg_metrologicalLevel,
                    usg_application: this.usg_application,
                    usg_validate: savedAs,
                    mme_id: this.mme_id_update,
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
                    this.usg_validate = savedAs;
                    this.$refs.sucessAlert.showAlert(`MME Usage updated successfully and saved as ${savedAs}`);
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        //Function for deleting a MME usage from the view and the database
        deleteComponent(reason, lifesheet_created) {
            //Emit to the parent component that we want to delete this component
            //If the user is in update mode and the MME usage exists in the database
            if (this.modifMod == true && this.usg_id !== null) {
                const consultUrl = (id) => `/mme/delete/usg/${id}`;
                axios.post(consultUrl(this.usg_id), {
                    mme_id: this.mme_id_update,
                }).then(response => {
                    //Send a post-request with id of the MME usage who will be deleted in the url
                    this.$emit('deleteUsage', '')
                    const id = this.mme_id_update;
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/mme/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.sucessAlert.showAlert(`MME Usage deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteUsage', '')
                this.$refs.sucessAlert.showAlert(`Empty MME Usage deleted successfully`);
            }
        },
        reformComponent(endDate) {
            if (this.$userId.user_makeReformRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            //If the user is in update mode and the MME usage exists in the database,
            //Send a post-request with the id of the usage who will be deleted in the url
            const consultUrl = (id) => `/mme/reform/usg/${id}`;
            axios.post(consultUrl(this.usg_id), {
                mme_id: this.mme_id_update,
                usg_reformDate: endDate,
                user_id:this.$userId.id,
            }).then(response => {
                //Emit to the parent component that we want to delete this component
                this.$emit('deleteUsage', '')
            }).catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.errors['usg_reformDate'])
            });
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

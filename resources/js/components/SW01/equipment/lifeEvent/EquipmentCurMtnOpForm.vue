<!--File name : EquipmentCurMtnOpForm.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component to show the form of a curative maintenance operation-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container curMtnOp-form" @keydown="clearError">
                <!--Call of the different component with their props-->
                <div
                    v-if="isInConsultMod==true && this.curMtnOp_number!==null || this.modifMod==true && this.curMtnOp_number!==null">
                    <InputNumberForm v-model="curMtnOp_number" :Errors="errors.curMtnOp_number"
                                     :info_text="infos_curMtnOp[0].info_value" :stepOfInput="1"
                                     inputClassName="form-control w-25" isDisabled
                                     label="Number :" name="curMtnOp_number"/>
                </div>
                <InputTextForm v-model="curMtnOp_reportNumber" :Errors="errors.curMtnOp_reportNumber"
                               :info_text="infos_curMtnOp[1].info_value" :isDisabled="!!isInConsultMod"
                               inputClassName="form-control w-50"
                               isRequired label="Report number :" name="curMtnOp_reportNumber"/>
                <InputTextAreaForm v-model="curMtnOp_description" :Errors="errors.curMtnOp_description"
                                   :info_text="infos_curMtnOp[2].info_value" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control w-50"
                                   label="Description :" name="curMtnOp_description"/>
                <div class="input-group">
                    <InputTextForm v-model="curMtnOp_startDate" :Errors="errors.curMtnOp_startDate"
                                   :info_text="infos_curMtnOp[3].info_value" :isDisabled="true"
                                   inputClassName="form-control" isRequired
                                   label="Start date :" name="curMtnOp_startDate"/>
                    <InputDateForm v-model="selected_startDate" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control  date-selector" isRequired name="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm v-model="curMtnOp_endDate" :Errors="errors.curMtnOp_endDate"
                                   :info_text="infos_curMtnOp[4].info_value" :isDisabled="true"
                                   inputClassName="form-control" isRequired
                                   label="End date :" name="curMtnOp_endDate"/>
                    <InputDateForm v-model="selected_endDate" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control date-selector" isRequired name="selected_endDate"/>
                </div>
                <RadioGroupForm
                    v-model="eq_realize"
                    :checkedOption="eq_realize"
                    :info_text="null"
                    :isDisabled="!!isInConsultMod"
                    :options="eq_realizeOption"
                    label="I realize ?:"
                />
                <InputPasswordForm
                    v-if="eq_realize == true"
                    v-model="user_password"
                    :Errors="errors.connexion"
                    :info_text="null"
                    divClassName="password"
                    inputClassName="form-control w-50"
                    label="Password :"
                    name="user_password"
                />
                <div v-if="this.addSucces==false ">
                    <!--If this curative maintenance operation doesn't have a id the addEquipmentCurMtnOp is called function else the updateEquipmentCurMtnOp function is called -->
                    <div v-if="this.curMtnOp_number==null ">
                        <SaveButtonForm :Errors="errors.curMtnOp_validate" :consultMod="this.isInConsultMod"
                                        :desactiveValidated="true"
                                        :is_op="true" :savedAs="curMtnOp_validate"
                                        @add="addEquipmentCurMtnOp" @update="updateEquipmentCurMtnOp"/>
                    </div>
                    <div v-else-if="this.curMtnOp_number!==null">
                        <SaveButtonForm :Errors="errors.curMtnOp_validate" :consultMod="this.isInConsultMod"
                                        :desactiveValidated="true"
                                        :is_op="true" :modifMod="this.modifMod"
                                        :savedAs="curMtnOp_validate" @add="addEquipmentCurMtnOp"
                                        @update="updateEquipmentCurMtnOp"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <div v-if="isInModifMod==true">
                        <DeleteComponentButton :Errors="errors.curMtnOp_delete" :consultMod="this.isInConsultMod"
                                               @deleteOk="deleteComponent"/>
                    </div>
                </div>
            </form>
            <SuccesAlert ref="succesAlert"/>
        </div>
    </div>
</template>

<script>
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputNumberForm from '../../../input/InputNumberForm.vue'
import InputDateForm from '../../../input/InputDateForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'
import moment from 'moment'
import InputPasswordForm from "../../../input/InputPasswordForm.vue";

export default {
    components: {
        InputPasswordForm,
        InputTextAreaForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputNumberForm,
        SaveButtonForm,
        DeleteComponentButton,
        SuccesAlert
    },
    /*--------Declaration of the differents props:--------
       number :
       description : the description of the curative maintenance operation
       validate: Validation option of the curative maintenance operation
       consultMod: If this props is set on true the form is in consult mode, and we disable all the field
       modifMod: If this props is present the form is in modification mode we replace the add buttons with the similar update button
       divClass: Class name of this curative maintenance operation form
       id: ID of an already created curative maintenance operation
       eq_id: ID of the equipment in which the curative maintenance operation will be added
   ---------------------------------------------------*/
    props: {
        number: {
            type: String,
            default: null
        },
        reportNumber: {
            type: String
        },
        description: {
            type: String
        },
        startDate: {
            type: String,
            default: null
        },
        endDate: {
            type: String,
            default: null
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
        state_id: {
            type: Number
        }

    },
    data() {
        return {
            curMtnOp_number: this.number,
            curMtnOp_reportNumber: this.reportNumber,
            curMtnOp_description: this.description,
            selected_startDate: this.startDate,
            selected_endDate: this.endDate,
            curMtnOp_startDate: '',
            curMtnOp_endDate: '',
            curMtnOp_validate: this.validate,
            curMtnOp_id: this.id,
            equipment_id_add: this.eq_id,
            equipment_state_id: this.state_id,
            equipment_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_curMtnOp: [],
            eq_realize: false,
            eq_realizeOption: [
                {id: 'eq_realize', value: true, text: 'Yes'},
                {id: 'eq_realize', value: false, text: 'No'}
            ],
            user_password: '',
        }
    },
    mounted() {
        if (this.selected_startDate !== null) {
            this.curMtnOp_startDate = moment(this.selected_startDate).format('D MMM YYYY');
        }
        if (this.selected_endDate !== null) {
            this.curMtnOp_endDate = moment(this.selected_endDate).format('D MMM YYYY');
        }
    },
    updated() {
        if (this.selected_startDate !== null) {
            this.curMtnOp_startDate = moment(this.selected_startDate).format('D MMM YYYY');
        }
        if (this.selected_endDate !== null) {
            this.curMtnOp_endDate = moment(this.selected_endDate).format('D MMM YYYY');
        }
    },
    created() {
        /* We ask the controller for the curative maintenance operation list */
        axios.get('/info/send/curativeMaintenanceOperation')
            .then(response => {
                this.infos_curMtnOp = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    },
    methods: {
        /*Sending to the controller all the information about the curative maintenance operation,
            so that it can be added to the database
        Params :
            savedAs : Value of the validation option : drafted, to_be_validated or validated  */
        addEquipmentCurMtnOp(savedAs) {
            if (!this.addSucces) {
                //ID of the equipment where the curative maintenance operation will be linked
                let id;
                //If the user is not in the modification mode, we set the id with the value of the data equipment_id_add
                if (!this.modifMod) {
                    id = this.equipment_id_add
                    //else if the user is in modification mode, we set the id with the value of equipment_id_update
                } else {
                    id = this.equipment_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                The report number, description, start and end dates, validate options of the curative maintenance operation, the id of the linked equipment and the id of his state
                are sent to the controller */
                axios.post('/curMtnOp/verif', {
                    curMtnOp_reportNumber: this.curMtnOp_reportNumber,
                    curMtnOp_description: this.curMtnOp_description,
                    curMtnOp_startDate: this.selected_startDate,
                    curMtnOp_endDate: this.selected_endDate,
                    curMtnOp_validate: savedAs,
                    curMtnOp_id: this.curMtnOp_id,
                    eq_id: id,
                    state_id: this.equipment_state_id,
                    reason: 'add'
                }).then(response => {
                    this.errors = {};
                    /*In case of the whole fields are correctly filled we make a new post this time to add the curative maintenance operation in the data base
                    The report number, description, start and end dates, validate options of the curative maintenance operation, the id of the linked equipment and the id of his state
                    are sent to the controller */
                    axios.post('/equipment/add/state/curMtnOp', {
                        curMtnOp_reportNumber: this.curMtnOp_reportNumber,
                        curMtnOp_description: this.curMtnOp_description,
                        curMtnOp_startDate: this.selected_startDate,
                        curMtnOp_endDate: this.selected_endDate,
                        curMtnOp_validate: savedAs,
                        eq_id: id,
                        state_id: this.equipment_state_id,
                        enteredBy_id: this.$userId.id
                    })
                        //If the curative maintenance operation is added successfully
                        .then(response => {
                            this.$refs.succesAlert.showAlert(`Equipment curative maintenance operation added successfully and saved as ${savedAs}`);
                            //If the user is not in modification mode, we add a new curative maintenance operation
                            if (!this.modifMod) {
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultMod = true;
                                this.addSucces = true
                                this.$emit('addSucces', '');

                            }
                            //the id of the curative maintenance operation take the value of the newlly created id
                            this.curMtnOp_id = response.data;
                            //The validate option of this curative maintenance operation take the value of savedAs
                            this.curMtnOp_validate = savedAs;
                            if (this.eq_realize) {
                                axios.post('/curMtnOp/realize/' + this.curMtnOp_id, {
                                    user_id: this.$userId.id,
                                    user_pseudo: this.$userId.user_pseudo,
                                    user_password: this.user_password,
                                }).catch(error => this.errors = error.response.data.errors);
                            }
                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the curative maintenance operation,
            so that it can be updated in the database
        Params :
            savedAs : Value of the validation option : drafted, to_be_validated or validated  */
        updateEquipmentCurMtnOp(savedAs) {
            /*First post to verify if all the fields are filled correctly
                The report number, description, start and end dates, validate options of the curative maintenance operation, the id of the linked equipment and the id of his state
                are sent to the controller */
            axios.post('/curMtnOp/verif', {
                curMtnOp_reportNumber: this.curMtnOp_reportNumber,
                curMtnOp_description: this.curMtnOp_description,
                curMtnOp_startDate: this.selected_startDate,
                curMtnOp_endDate: this.selected_endDate,
                curMtnOp_validate: savedAs,
                curMtnOp_id: this.curMtnOp_id,
                eq_id: this.equipment_id_update,
                state_id: this.equipment_state_id,
                reason: 'update'
            }).then(response => {
                /*
                                    console.log("update dans la base");
                */
                /*in case of the whole fields are correctly filled, we make a new post this time to add the curative maintenance operation in the data base
                    The report number, description, start and end dates, validate options of the curative maintenance operation, the id of the linked equipment and the id of his state
                    are sent to the controller
                    In the post url the id correspond to the id of the curative maintenance operation who will be update*/
                const consultUrl = (id) => `/equipment/update/state/curMtnOp/${id}`;
                axios.post(consultUrl(this.curMtnOp_id), {
                    curMtnOp_reportNumber: this.curMtnOp_reportNumber,
                    curMtnOp_description: this.curMtnOp_description,
                    curMtnOp_startDate: this.selected_startDate,
                    curMtnOp_endDate: this.selected_endDate,
                    curMtnOp_validate: savedAs,
                    eq_id: this.equipment_id_update,
                    state_id: this.equipment_state_id
                }).then(response => {
                    this.curMtnOp_validate = savedAs;
                    this.$refs.succesAlert.showAlert(`Equipment curative maintenance operation updated successfully and saved as ${savedAs}`);
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clear all the errors of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        //Function for deleting a curative maintenance operation from the view and the database
        deleteComponent() {
            //If the user is in update mode and the curative maintenance operation exist in the database
            if (this.modifMod == true && this.curMtnOp_id !== null) {
                //Send a post-request with the id of the curative maintenance operation who will be deleted in the url
                const consultUrl = (id) => `/state/delete/curMtnOp/${id}`;
                axios.post(consultUrl(this.curMtnOp_id), {
                    eq_id: this.equipment_id_update,
                }).then(response => {
                    //Emit to the parent component that we want to delete this curative maintenance operation
                    this.$emit('deleteCurMtnOp', '')
                    this.$refs.succesAlert.showAlert(`Equipment curative maintenance operation deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);
            }
        }
    }
}
</script>

<style>

</style>

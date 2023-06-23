<!--File name : MMEVerifRlzForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to generate a form, this form is used to add a verification realized-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <form class="container verifRlz-form" @keydown="clearError">
                <!--Call of the different component with their props-->
                <VerifChooseModal v-if="isInModifMod==false && isInConsultMod==false" :number="number" :verifs="verifs"
                                  @choosedOpe="choosedOpe"/>
                <div v-if="verif_number!==null  ">
                    <InputTextForm v-model="verif_number" :info_text="infos_verif[0].info_value"
                                   inputClassName="form-control w-50" isDisabled label="Number :" name="verif_number"/>
                    <InputTextAreaForm v-model="verif_expectedResult" :info_text="infos_verif[2].info_value"
                                       inputClassName="form-control w-50" isDisabled label="Expected Result :"
                                       name="verif_expectedResult"/>
                    <InputTextAreaForm v-model="verif_nonComplianceLimit" :info_text="infos_verif[3].info_value"
                                       inputClassName="form-control w-50" isDisabled label="Non Compliance Limit :"
                                       name="verif_nonComplianceLimit"/>
                    <InputTextAreaForm v-model="verif_description" :info_text="infos_verif[7].info_value"
                                       inputClassName="form-control w-50" isDisabled label="Description :"
                                       name="verif_description"/>
                    <InputTextAreaForm v-model="verif_protocol" :info_text="infos_verif[8].info_value"
                                       inputClassName="form-control w-50" isDisabled label="Protocol :" name="verif_protocol"/>
                </div>
                <InputTextAreaForm v-model="verif_comment" :info_text="null" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control w-50" label="Comment :" name="verif_comment"/>
                <InputTextForm v-model="verifRlz_reportNumber" :Errors="errors.verifRlz_reportNumber"
                               :info_text="infos_verifRlz[0].info_value" :isDisabled="!!isInConsultMod"
                               inputClassName="form-control w-50" label="Report number :" name="verifRlz_reportNumber"/>
                <div class="input-group">
                    <InputTextForm v-model="verifRlz_startDate" :Errors="errors.verifRlz_startDate"
                                   :info_text="infos_verifRlz[1].info_value"
                                   :isDisabled="true" :placeholer="'Operation date :'+verif_startDate_placeholer" inputClassName="form-control"
                                   label="Start date :" name="verifRlz_startDate"/>
                    <InputDateForm v-model="selected_startDate" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control  date-selector" name="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm v-model="verifRlz_endDate" :Errors="errors.verifRlz_endDate"
                                   :info_text="infos_verifRlz[2].info_value" :isDisabled="true" inputClassName="form-control"
                                   label="End date :" name="verifRlz_endDate"/>
                    <InputDateForm v-model="selected_endDate" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control date-selector" name="selected_endDate"/>
                </div>
                <RadioGroupForm v-model="verifRlz_isPassed" :Errors="errors.verifRlz_isPassed" :checkedOption="verifRlz_isPassed"
                                :info_text="infos_verifRlz[3].info_value" :isDisabled="!!isInConsultMod"
                                :options="isPassedOption" label="is Passed?:"/>
                <div v-if="this.verif_id!==null">
                    <div v-if="this.addSucces==false">
                        <!--If this verification doesn't have a id the addMmeVerifRlz is called function else the updateMmeVerifRlz function is called -->
                        <div v-if="this.verifRlz_id==null ">
                            <SaveButtonForm :Errors="errors.verifRlz_validate" :consultMod="this.isInConsultMod" :desactiveValidated="true"
                                            :is_op="true" :savedAs="verifRlz_validate"
                                            @add="addMmeVerifRlz" @update="updateMmeVerifRlz"/>
                        </div>
                        <div v-else-if="this.verifRlz_id!==null">
                            <SaveButtonForm :Errors="errors.verifRlz_validate" :consultMod="this.isInConsultMod" :desactiveValidated="true"
                                            :is_op="true" :modifMod="this.modifMod"
                                            :savedAs="verifRlz_validate" @add="addMmeVerifRlz"
                                            @update="updateMmeVerifRlz"/>
                        </div>
                        <!-- If the user is not in the consultation mode, the delete button appear -->
                        <div v-if="isInModifMod==true">
                            <DeleteComponentButton :Errors="errors.verifRlz_delete" :consultMod="this.isInConsultMod"
                                                   @deleteOk="deleteComponent"/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <SuccesAlert ref="succesAlert"/>
    </div>
</template>

<script>
import InputTextForm from '../../../input/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputDateForm from '../../../input/InputDateForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import VerifChooseModal from './VerifChooseModal.vue'
import moment from 'moment'
import SuccesAlert from '../../../alert/SuccesAlert.vue'

export default {
    components: {
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        VerifChooseModal,
        SuccesAlert
    },
    props: {
        number: {
            type: String,
            default: null
        },
        reportNumber: {
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
        isPassed: {
            type: Boolean,
            default: false
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
        state_id: {
            type: Number
        },
        verif_expectedResult_prop: {
            type: String,
            default: null
        },
        verif_nonComplianceLimit_prop: {
            type: String,
            default: null
        },
        verif_number_prop: {
            type: Number,
            default: null
        },
        verif_description_prop: {
            type: String,
            default: null
        },
        verif_protocol_prop: {
            type: String,
            default: null
        },
        verif_id_prop: {
            type: Number,
            default: null
        },
        comment: {
            type: String,
            default: null
        },
    },
    data() {
        return {
            verifRlz_reportNumber: this.reportNumber,
            selected_startDate: this.startDate,
            selected_endDate: this.endDate,
            verifRlz_startDate: '',
            verifRlz_endDate: '',
            verifRlz_isPassed: this.isPassed,
            verifRlz_validate: this.validate,
            verifRlz_id: this.id,
            verifs: [],
            isPassedOption: [
                {id: 'Yes', value: true, text: 'Yes'},
                {id: 'No', value: false, text: 'No'}
            ],
            mme_id_add: this.mme_id,
            mme_state_id: this.state_id,
            mme_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            verif_number: this.verif_number_prop,
            verif_expectedResult: this.verif_expectedResult_prop,
            verif_nonComplianceLimit: this.verif_nonComplianceLimit_prop,
            verif_description: this.verif_description_prop,
            verif_protocol: this.verif_protocol_prop,
            verif_id: this.verif_id_prop,
            infos_verif: [],
            infos_verifRlz: [],
            verif_startDate_placeholer: '',
            verif_comment: this.comment,
        }
    },
    mounted() {
        if (this.selected_startDate !== null) {
            this.verifRlz_startDate = moment(this.selected_startDate).format('D MMM YYYY');
        }

        if (this.selected_endDate !== null) {
            this.verifRlz_endDate = moment(this.selected_endDate).format('D MMM YYYY');
        }
    },
    updated() {
        if (this.selected_startDate !== null) {
            this.verifRlz_startDate = moment(this.selected_startDate).format('D MMM YYYY');
        }

        if (this.selected_endDate !== null) {
            this.verifRlz_endDate = moment(this.selected_endDate).format('D MMM YYYY');
        }
    },
    methods: {
        /*Sending to the controller all the information about the verification so that it can be added to the database
        Params :
            savedAs : Value of the validation option : drafted, to_be_validated or validated  */
        addMmeVerifRlz(savedAs) {
            if (!this.addSucces) {
                //Id of the mme in which the verification will be added
                let id;
                //If the user is not in the modification mode, we set the id with the value of mme_id_add
                if (!this.modifMod) {
                    id = this.mme_id_add
                    //else the user is in the update menu, we set the id with the value of mme_id_update
                } else {
                    id = this.mme_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                The reportNumber, startDate, endDate, isPassed, validate options, the id of the mme affected, the id of his state and the id of the verification are sent to the controller*/
                axios.post('/verifRlz/verif', {
                    verifRlz_reportNumber: this.verifRlz_reportNumber,
                    verifRlz_startDate: this.selected_startDate,
                    verifRlz_endDate: this.selected_endDate,
                    verifRlz_isPassed: this.verifRlz_isPassed,
                    verifRlz_validate: savedAs,
                    state_id: this.mme_state_id,
                    verif_id: this.verif_id,
                    mme_id: id,
                    reason: 'add'
                }).then(response => {
                    this.errors = {};
                    /*If all of the fields are correctly filled, a new post this time to add the verification in the data base
                    The reportNumber, the startDate, endDate, isPassed value, validate options, the id of the mme affected, the id of his current state, the id of verification and the user who entered it
					All of this information are sent to the controller*/
                    axios.post('/mme/add/mme_state/verifRlz', {
                        verifRlz_reportNumber: this.verifRlz_reportNumber,
                        verifRlz_startDate: this.selected_startDate,
                        verifRlz_endDate: this.selected_endDate,
                        verifRlz_isPassed: this.verifRlz_isPassed,
                        verifRlz_validate: savedAs,
                        mme_id: id,
                        state_id: this.mme_state_id,
                        verif_id: this.verif_id,
                        enteredBy_id: this.$userId.id
                    })
                        //If the verification is added successfully
                        .then(response => {
                            this.$refs.succesAlert.showAlert(`MME verification realized added successfully and saved as ${savedAs}`);
                            //If we the user is not in modifMod
                            if (!this.modifMod) {
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultMod = true;
                                this.addSucces = true
                                this.$emit('addSucces', '');
                            }
                            //the id of the verification take the value of the newly created id
                            this.verifRlz_id = response.data;
                            //The validate option of this verification takes the value of savedAs
                            this.verifRlz_validate = savedAs;
                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be updated in the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated  */
        updateMmeVerifRlz(savedAs) {
            /*First post to verify if all the fields are filled correctly
                The reportNumber, startDate, endDate, isPassed, validate options, the id of the affected mme, the id of his current state, and the id of the verification are sent to the controller*/
            axios.post('/verifRlz/verif', {
                verifRlz_reportNumber: this.verifRlz_reportNumber,
                verifRlz_startDate: this.selected_startDate,
                verifRlz_endDate: this.selected_endDate,
                verifRlz_isPassed: this.verifRlz_isPassed,
                verifRlz_validate: savedAs,
                verifRlz_id: this.verifRlz_id,
                state_id: this.mme_state_id,
                verif_id: this.verif_id,
                mme_id: this.mme_id_update,
                reason: 'update'
            }).then(response => {
                /*If all of the fields are correctly filled, a new post this time to add the verification in the data base
                The reportNumber, the startDate, endDate, isPassed value, validate options, the id of the mme affected, the id of his current state, the id of verification and the user who entered it
                All of this information are sent to the controller
                In the post url the id correspond to the id of the verification who will be updated*/
                const consultUrl = (id) => `/mme/update/mme_state/verifRlz/${id}`;
                axios.post(consultUrl(this.verifRlz_id), {
                    verifRlz_reportNumber: this.verifRlz_reportNumber,
                    verifRlz_startDate: this.selected_startDate,
                    verifRlz_endDate: this.selected_endDate,
                    verifRlz_isPassed: this.verifRlz_isPassed,
                    verifRlz_validate: savedAs,
                    mme_id: this.mme_id_update,
                    state_id: this.mme_state_id,
                    verif_id: this.verif_id
                }).then(response => {
                    this.$refs.succesAlert.showAlert(`MME verification realized updated successfully and saved as ${savedAs}`);
                    this.verifRlz_validate = savedAs;
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clear all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        //Function for deleting a verification from the view and the database
        deleteComponent() {
            //If the user is in update mode and the verification exist in the database
            if (this.modifMod == true && this.verifRlz_id !== null) {
                //Send a post-request with the id of the verification who will be deleted in the url
                const consultUrl = (id) => `/mme_state/delete/verifRlz/${id}`;
                axios.post(consultUrl(this.verifRlz_id), {
                    mme_id: this.mme_id_update,
                }).then(response => {
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteVerifRlz', '')
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        choosedOpe(value) {
            this.verif_number = value.verif_number;
            this.verif_expectedResult = value.verif_expectedResult,
                this.verif_nonComplianceLimit = value.verif_nonComplianceLimit,
                this.verif_description = value.verif_description;
            this.verif_protocol = value.verif_protocol;
            this.verif_startDate_placeholer = value.verif_nextDate;
            this.verif_id = value.id;
        }
    },
    created() {
        if (this.isInConsultMod == false && this.isInModifMod == false) {
            const consultUrl = (id) => `/verif/send/validated/${id}`;
            axios.get(consultUrl(this.mme_id))
                .then(response => {
                    this.verifs = response.data;
                    axios.get('/info/send/verif')
                        .then(response => {
                            this.infos_verif = response.data;
                            axios.get('/info/send/verifRlz')
                                .then(response => {
                                    this.loaded = true;
                                    this.infos_verifRlz = response.data;
                                }).catch(error => {
                            });
                        }).catch(error => {
                    });
                }).catch(error => {
            });
        } else {
            axios.get('/info/send/verif')
                .then(response => {
                    this.infos_verif = response.data;
                    axios.get('/info/send/verifRlz')
                        .then(response => {
                            this.loaded = true;
                            this.infos_verifRlz = response.data;
                        }).catch(error => {
                    });
                }).catch(error => {
            });
        }
    }
}
</script>

<style>

</style>

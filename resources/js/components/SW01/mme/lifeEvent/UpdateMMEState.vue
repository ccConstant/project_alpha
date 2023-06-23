<!--File name : UpdateMMEState.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to update the state of a specific MME-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <ErrorAlert ref="errorAlert"/>
            <SuccesAlert ref="succesAlert"/>
            <form class="container state-form" @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInModifMod">
                    <h2>Update the state</h2>
                </div>
                <div v-else>
                    <h2>Change the State</h2>
                    <div v-if="isInConsultMod==false">
                        <InputTextForm v-model="current_state" :isDisabled="true" inputClassName="form-control w-50"
                                       label="Current state :" name="current_state"/>
                        <InputTextForm v-model="current_startDate" :isDisabled="true"
                                       inputClassName="form-control w-50" label="Current state start Date :"
                                       name="current_startDate"/>

                    </div>
                </div>
                <div v-if="state_id!==undefined || isInConsultMod==true">
                    <InputSelectForm v-model="state_name" :Errors="errors.state_name"
                                     :id_actual="StateName" :info_text="infos_state[0].info_value" :options="enum_state_name"
                                     :selctedOption="state_name" isDisabled label="State name :"
                                     name="state_name" selectClassName="form-select w-50"/>
                </div>
                <div v-else>
                    <InputSelectForm v-model="state_name" :Errors="errors.state_name"
                                     :id_actual="StateName" :info_text="infos_state[0].info_value" :options="enum_state_name"
                                     :selctedOption="state_name" label="State name :" name="state_name"
                                     selectClassName="form-select w-50"/>
                </div>
                <InputTextAreaForm v-model="state_remarks" :Errors="errors.state_remarks"
                                   :info_text="infos_state[1].info_value" :isDisabled="!!isInConsultMod" inputClassName="form-control w-50"
                                   label="Remarks :" name="state_remarks"/>
                <div class="input-group">
                    <InputTextForm v-model="state_startDate" :Errors="errors.state_startDate"
                                   :info_text="infos_state[2].info_value" :isDisabled="true" inputClassName="form-control"
                                   label="Start date :" name="state_startDate"/>
                    <InputDateForm v-model="selected_startDate" :isDisabled="!!isInConsultMod"
                                   inputClassName="form-control  date-selector" name="selected_startDate"
                                   @clearDateError="clearDateError"/>
                </div>
                <SaveButtonForm v-if="this.addSucces==false" :Errors="errors.mme_delete" :consultMod="this.isInConsultMod"
                                :is_state="true" :modifMod="this.isInModifMod" :savedAs="state_validate"
                                @add="addMmeState" @update="updateMmeState"/>
            </form>


            <div v-if="state_name=='Downgraded'">
                <div v-if="state_validate=='validated'">
                    <div v-if="!isEmpty(mme_idCard)">
                        <MmeIdForm :construct="mme_idCard.mme_constructor"
                                   :externalReference="mme_idCard.mme_externalReference"
                                   :internalReference="mme_idCard.mme_internalReference" :name="mme_idCard.mme_name"
                                   :remarks="mme_idCard.mme_remarks"
                                   :serialNumber="mme_idCard.mme_serialNumber" :set="mme_idCard.mme_set"
                                   :validate="mme_idCard.mme_validate"
                                   consultMod/>
                    </div>
                    <div v-else>
                        <RadioGroupForm v-model="new_mme" :options="radioOption"
                                        label="Do you want to reference a new mme ?:"/>
                        <MmeIdForm v-if="new_mme==true" :construct="mme_idCard.mme_constructor" :disableImport="true"
                                   :externalReference="mme_idCard.mme_externalReference"
                                   :internalReference="mme_idCard.mme_internalReference"
                                   :name="mme_idCard.mme_name" :remarks="mme_idCard.mme_remarks"
                                   :serialNumber="mme_idCard.mme_serialNumber"
                                   :set="mme_idCard.mme_set" :state_id="state_id"
                                   :validate="mme_idCard.mme_validate"/>
                    </div>
                </div>
            </div>

        </div>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->

    </div>
</template>

<script>
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputDateForm from '../../../input/InputDateForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import moment from 'moment'
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'

export default {
    components: {
        InputTextAreaForm,
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        MmeIdForm,
        ErrorAlert,
        SuccesAlert
    },
    props: {
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
    },
    data() {
        return {
            state_name: '',
            state_remarks: '',
            selected_startDate: null,
            state_startDate: '',
            state_validate: '',
            enum_state_name: [
                {id_enum: "StateName", value: "Waiting_for_referencing"},
                {id_enum: "StateName", value: "Waiting_to_be_in_use"},
                {id_enum: "StateName", value: "In_use"},
                {id_enum: "StateName", value: "Under_verification"},
                {id_enum: "StateName", value: "In_quarantine"},
                {id_enum: "StateName", value: "Under_repair"},
                {id_enum: "StateName", value: "Broken"},
                {id_enum: "StateName", value: "Downgraded"},
                {id_enum: "StateName", value: "Reformed"},
                {id_enum: "StateName", value: "Lost"},
            ],
            isInConsultMod: this.consultMod,
            mme_id: this.$route.params.id,
            state_id: this.$route.params.state_id,
            isInModifMod: this.modifMod,
            errors: {},
            addSucces: false,
            loaded: false,
            current_state: '',
            current_startDate: '',
            radioOption: [
                {id: 'Yes', value: true},
                {id: 'No', value: false}
            ],
            new_mme: null,
            mme_idCard: [],
            infos_state: [],
            StateName: "StateName"
        }
    },
    updated() {
        if (this.selected_startDate !== null) {
            this.state_startDate = moment(this.selected_startDate).format('D MMM YYYY');
        }
    },
    methods: {
        addMmeState(savedAs) {
            if (!this.addSucces) {
                axios.post('/mme_state/verif', {
                    state_name: this.state_name,
                    state_remarks: this.state_remarks,
                    state_startDate: this.selected_startDate,
                    state_validate: savedAs,
                    mme_id: this.mme_id,
                    reason: 'add',
                    user_id: this.$userId.id
                }).then(response => {
                    this.errors = {}
                    axios.post('/mme/add/state', {
                        state_name: this.state_name,
                        state_remarks: this.state_remarks,
                        state_startDate: this.selected_startDate,
                        state_validate: savedAs,
                        mme_id: this.mme_id,
                        enteredBy_id: this.$userId.id

                    }).then(response => {
                        this.$refs.succesAlert.showAlert(`MME state added successfully and saved as ${savedAs}`);
                        this.$router.replace({name: "url_mme_list"})
                        this.addSucces = true;
                        this.isInConsultMod = true;
                        this.state_validate = savedAs

                    }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme state to check if all the fields are correctly filled, if it's okay, we can update the database */
        updateMmeState(savedAs) {
            axios.post('/mme_state/verif', {
                state_name: this.state_name,
                state_remarks: this.state_remarks,
                state_startDate: this.selected_startDate,
                state_validate: savedAs,
                state_id: this.state_id,
                mme_id: this.mme_id,
                reason: 'update'
            }).then(response => {
                this.errors = {}
                const consultUrl = (id) => `/mme/update/state/${id}`;
                axios.post(consultUrl(this.state_id), {
                    state_name: this.state_name,
                    state_remarks: this.state_remarks,
                    state_startDate: this.selected_startDate,
                    state_validate: savedAs,
                    mme_id: this.mme_id

                }).then(response => {
                    this.state_validate = savedAs
                    this.$refs.succesAlert.showAlert(`MME state updated successfully and saved as ${savedAs}`);

                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        clearDateError() {
            delete this.errors['state_startDate'];
        },
        isEmpty(object) {
            for (const property in object) {
                return false;
            }
            return true;
        },
        warningDelete(id, refernece) {
            this.$bvModal.show(`modal-deleteWarning-${this._uid}`);
        },
    },
    created() {
        if (this.$userId.user_declareNewStateRight != true) {
            this.$router.push({name: "home"})
            return;
        }
        if (this.state_id != undefined) {
            if (this.isInConsultMod == false) {
                this.isInModifMod = true;
            }
            const UrlState = (id) => `/mme_state/send/${id}`;
            axios.get(UrlState(this.state_id))
                .then(response => {
                    this.state_name = response.data[0].state_name;
                    this.state_remarks = response.data[0].state_remarks;
                    this.selected_startDate = response.data[0].state_startDate;
                    this.state_validate = response.data[0].state_validate;
                    if (this.state_name == "Downgraded") {
                        const consultUrl = (state_id) => `/send/mme_state/mme/${state_id}`;
                        axios.get(consultUrl(this.state_id))
                            .then(response => {
                                this.mme_idCard = response.data;
                                this.loaded = true
                            }).catch(error => {
                        });
                    } else {
                        this.loaded = true
                    }
                }).catch(error => this.errors = error.response.data.errors);
        } else {
            const UrlState = (id) => `/mme_state/send/${id}`;
            axios.get(UrlState(this.$route.query.currentState))
                .then(response => {
                    this.current_state = response.data[0].state_name;
                    this.current_startDate = moment(response.data[0].state_startDate).format('D MMM YYYY');

                }).catch(error => {
            });

        }
        axios.get('/info/send/mme_state')
            .then(response => {
                this.infos_state = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    }
}
</script>

<style lang="scss">
.state-form {
    .date-selector {
        width: 44px;
        margin-top: 8px
    }
}


</style>

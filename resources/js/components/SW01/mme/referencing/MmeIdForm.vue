<!--File name : MMEIdForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to edit or add an IDCard for a MME-->

<template>
    <div v-if="loaded==true" class="mmeID">
        <h2 class="titleForm1">MME ID : {{ mme_internalReference }}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <br><br><!--Call of the different component with their props-->
            <InputTextForm
                v-model="mme_internalReference"
                :Errors="errors.mme_internalReference"
                :info_text="infos_idCard[0].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="Unique ID :"
                name="mme_internalReference"
            />
            <InputTextWithOptionForm
                v-model="mme_set"
                :Errors="errors.mme_set"
                :info_text="infos_idCard[6].info_value"
                :isDisabled="!!isInConsultMod"
                :options="enum_sets"
                inputClassName="form-control w-50"
                label="MME Family"
                name="mme_set"
            />
            <InputTextForm
                v-model="mme_name"
                :Errors="errors.mme_name"
                :info_text="infos_idCard[2].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="MME name :"
                name="mme_name"
            />
            <InputTextForm
                v-model="mme_externalReference"
                :Errors="errors.mme_externalReference"
                :info_text="infos_idCard[1].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="MME ref in Alpha Database  :"
                name="mme_externalReference"
            />
            <InputTextForm
                v-model="mme_serialNumber"
                :Errors="errors.mme_serialNumber"
                :info_text="infos_idCard[3].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="MME serial Number :"
                name="mme_serialNumber"
            />
            <InputTextForm
                v-model="mme_constructor"
                :Errors="errors.mme_constructor"
                :info_text="infos_idCard[4].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="MME constructor :"
                name="mme_constructor"
            />
            <InputTextAreaForm
                v-model="mme_remarks"
                :Errors="errors.mme_remarks"
                :info_text="infos_idCard[5].info_value"
                :isDisabled="!!isInConsultMod"
                inputClassName="form-control w-50"
                label="Remarks :"
                name="mme_remarks"
            />
            <InputTextForm
                v-if="this.mme_importFrom!== undefined "
                v-model="mme_importFrom"
                inputClassName="form-control w-50"
                isDisabled
                label="Import From :" name="mme_importFrom"
            />

            <InputTextForm
                v-model="mme_location"
                :Errors="errors.mme_location"
                :isDisabled="!!isInConsultMod"
                :info_text="infos_idCard[7].info_value"
                inputClassName="form-control w-50"
                label="MME location :"
                location="mme_location"
            />
            <SaveButtonForm v-if="this.addSucces==false" ref="saveButton" :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                            :savedAs="mme_validate" @add="addMME" @update="updateMME"/>
            <div v-if="this.modifMod!=true">
                <div v-if="this.isInConsultMod!=true">
                    <MMEImportationModal v-if="disableImport==false" :set="this.mme_set" @choosedMME="importFrom"/>
                </div>
            </div>
            <SucessAlert ref="sucessAlert"/>
        </form>
    </div>
</template>

<script>

import InputTextForm from '../../../input/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputNumberForm from '../../../input/InputNumberForm.vue'
import InputTextWithOptionForm from '../../../input/InputTextWithOptionForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import MMEImportationModal from './MMEImportationModal.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'

export default {
    components: {
        InputTextForm,
        InputNumberForm,
        InputTextWithOptionForm,
        InputTextAreaForm,
        SaveButtonForm,
        MMEImportationModal,
        SucessAlert
    },
    props: {
        internalReference: {
            type: String
        },
        externalReference: {
            type: String
        },
        name: {
            type: String
        },
        serialNumber: {
            type: String
        },
        construct: {
            type: String
        },
        remarks: {
            type: String
        },
        set: {
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
        disableImport: {
            type: Boolean,
            default: false
        },
        state_id: {
            type: String,
            default: null
        },
        location: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            mme_internalReference: this.internalReference,
            mme_externalReference: this.externalReference,
            mme_name: this.name,
            mme_serialNumber: this.serialNumber,
            mme_constructor: this.construct,
            mme_remarks: this.remarks,
            mme_set: this.set,
            mme_validate: this.validate,
            mme_importFrom: undefined,
            isInConsultMod: this.consultMod,
            enum_sets: [],
            mme_id: this.$route.params.id,
            errors: {},
            addSucces: false,
            infos_idCard: [],
            info_mme_internalReference: '',
            loaded: false,
            mme_location: this.location,
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        axios.get('/mme/sets')
            .then(response => {
                this.enum_sets = response.data;
                axios.get('/info/send/mme')
                    .then(response => {
                        this.infos_idCard = response.data;
                        this.loaded = true;
                    }).catch(error => {
                });
            }).catch(error => {
        });
    },
    methods: {
        addMME(savedAs) {
            if (!this.addSucces) {
                axios.post('/mme/verif', {
                    mme_internalReference: this.mme_internalReference,
                    mme_externalReference: this.mme_externalReference,
                    mme_name: this.mme_name,
                    mme_serialNumber: this.mme_serialNumber,
                    mme_constructor: this.mme_constructor,
                    mme_remarks: this.mme_remarks,
                    mme_set: this.mme_set,
                    mme_validate: savedAs,
                    reason: 'add',
                    mme_location: this.mme_location,
                    createdBy_id: this.$userId.id
                }).then(response => {
                    this.errors = {};
                    if (this.state_id !== null) {
                        const consultUrl = (state_id) => `/state/mme/${state_id}`;
                        axios.post(consultUrl(this.state_id), {
                            mme_internalReference: this.mme_internalReference,
                            mme_externalReference: this.mme_externalReference,
                            mme_name: this.mme_name,
                            mme_serialNumber: this.mme_serialNumber,
                            mme_constructor: this.mme_constructor,
                            mme_remarks: this.mme_remarks,
                            mme_set: this.mme_set,
                            mme_validate: savedAs,
                            mme_location: this.mme_location,
                            createdBy_id: this.$userId.id
                        }).then(response => {
                            this.$refs.sucessAlert.showAlert(`ID card added successfully and saved as ${savedAs}`);
                            this.addSucces = true;
                            this.isInConsultMod = true;
                            this.mme_id = response.data;
                            this.$emit('MMEID', this.mme_id);
                        }).catch(error => this.errors = error.response.data.errors);

                    } else {
                        axios.post('/mme/add', {
                            mme_internalReference: this.mme_internalReference,
                            mme_externalReference: this.mme_externalReference,
                            mme_name: this.mme_name,
                            mme_serialNumber: this.mme_serialNumber,
                            mme_constructor: this.mme_constructor,
                            mme_remarks: this.mme_remarks,
                            mme_set: this.mme_set,
                            mme_validate: savedAs,
                            createdBy_id: this.$userId.id,
                            mme_location: this.mme_location
                        }).then(response => {
                            this.$refs.sucessAlert.showAlert(`ID card added successfully and saved as ${savedAs}`);
                            this.addSucces = true;
                            this.isInConsultMod = true;
                            this.mme_id = response.data;
                            this.$emit('MMEID', this.mme_id);
                        }).catch(error => this.errors = error.response.data.errors);
                    }
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be updated to the database */
        updateMME(savedAs, reason, lifesheet_created) {
            axios.post('/mme/verif', {
                mme_internalReference: this.mme_internalReference,
                mme_externalReference: this.mme_externalReference,
                mme_name: this.mme_name,
                mme_serialNumber: this.mme_serialNumber,
                mme_constructor: this.mme_constructor,
                mme_remarks: this.mme_remarks,
                mme_set: this.mme_set,
                mme_validate: savedAs,
                mme_id: this.mme_id,
                reason: 'update',
                mme_location: this.mme_location
            }).then(response => {
                this.errors = {};
                const consultUrl = (id) => `/mme/update/${id}`;
                axios.post(consultUrl(this.mme_id), {
                    mme_internalReference: this.mme_internalReference,
                    mme_externalReference: this.mme_externalReference,
                    mme_name: this.mme_name,
                    mme_serialNumber: this.mme_serialNumber,
                    mme_constructor: this.mme_constructor,
                    mme_remarks: this.mme_remarks,
                    mme_set: this.mme_set,
                    mme_validate: savedAs,
                    mme_location: this.mme_location
                }).then(response => {
                    const id = this.mme_id;
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/mme/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.sucessAlert.showAlert(`ID card updated successfully and saved as ${savedAs}`);
                    this.mme_validate = savedAs;

                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        importFrom(value) {
            this.mme_importFrom = value.mme_internalReference;
            this.$emit('importFromMMEID', value.id);
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
        clearAllError() {
        }
    }
}
</script>

<style lang="scss">
.titleForm {
    padding-left: 80px;
}

form {
    margin: 20px;
    margin-bottom: 100px;
}

</style>

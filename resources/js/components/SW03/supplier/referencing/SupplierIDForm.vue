<!--File name : SupplierIDForm.vue-->
<!--Creation date : 21 Apr 2023-->
<!--Update date : 21 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div class="supplierID" v-if="loaded===true">
        <vue-snotify></vue-snotify>
        <h2>Supplier's Form</h2>
        <h2 class="titleForm1">Supplier ID {{this.supplr_id}}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <b-form class="container">
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.supplr_name"
                name="supplr_name"
                label="Supplier Name"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="supplr_name"
                :info_text="infos_idCard[0].info_value"
                :min="2"
                :max="255"
            />
            <InputTextForm
                :name="'SupplierReceptionPhoneNumber'"
                :label="'Reception Phone Number'"
                v-model="supplr_receptionNumber"
                :info_text="infos_idCard[1].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_receptionNumber"
                :min="10"
                :max="30"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'Form ID'"
                :label="'Form ID'"
                v-model="supplr_formId"
                :info_text="infos_idCard[2].info_value"
                :input-class-name="null"
                :Errors="errors.supplr_formId"
                :min="2"
                :max="50"
                isRequired
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'AgreementNumber'"
                :label="'Agreement Number'"
                v-model="supplr_agreementNumber"
                :info_text="infos_idCard[3].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_agreementNumber"
                :min="2"
                :max="50"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'QualityCertificateNumber'"
                :label="'Quality Certificate Number'"
                v-model="supplr_qualityCertificateNumber"
                :info_text="infos_idCard[4].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_qualityCertificateNumber"
                :min="2"
                :max="50"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'Specific Instructions'"
                :label="'Specific Instruction'"
                v-model="supplr_specificsInstructions"
                :info_text="infos_idCard[5].info_value"
                :input-class-name="null"
                :Errors="errors.supplr_specificsInstructions"
                :min="0"
                :max="255"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'SIRET/DUNS'"
                :label="'SIRET/DUNS'"
                v-model="supplr_siret"
                :info_text="infos_idCard[7].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_siret"
                :min="2"
                :max="255"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'WebSite'"
                :label="'Web Site'"
                v-model="supplr_webSite"
                :info_text="infos_idCard[8].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_webSite"
                :min="0"
                :max="255"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'Activity(ies)'"
                :label="'Activity(ies)'"
                v-model="supplr_activity"
                :info_text="infos_idCard[9].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_activity"
                :min="2"
                :max="50"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'VATNumber'"
                :label="'VAT Number'"
                v-model="supplr_VatNumber"
                :info_text="infos_idCard[11].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_VatNumber"
                :min="2"
                :max="255"
                :isDisabled="isInConsultMod"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Real ?'"
                :label="'Real'"
                v-model="supplr_real"
                :info_text="infos_idCard[10].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_real"
                :checked-option="isInModifMod ? supplr_real : true"
                :isDisabled="isInConsultMod"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Active ?'"
                :label="'Active'"
                v-model="supplr_active"
                :info_text="infos_idCard[14].info_value"
                :inputClassName="null"
                :Errors="errors['Active']"
                :checkedOption="isInModifMod ? supplr_active : true"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Critical ?'"
                :label="'Critical'"
                v-model="supplr_critical"
                :info_text="infos_idCard[12].info_value"
                :inputClassName="null"
                :Errors="errors.supplr_critical"
                :checked-option="isInModifMod ? supplr_critical : false"
                :isDisabled="isInConsultMod"
            />
            <InputTextForm
                :name="'End Link to Folder'"
                :label="'End Link to Folder'"
                v-model="supplr_endLinkToFolder"
                :info_text="infos_idCard[13].info_value"
                :input-class-name="null"
                :Errors="errors.supplr_endLinkToFolder"
                :min="2"
                :max="55"
                :isDisabled="isInConsultMod"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Concern QM Or Product ?'"
                :label="'Concern QM Or Product ?'"
                v-model="supplr_concern"
                :info_text="null"
                :inputClassName="null"
                :Errors="errors.supplr_concern"
                :checked-option="isInModifMod ? supplr_concern : false"
            />
            <SaveButtonForm
                ref="saveButton"
                v-if="this.addSuccess === false"
                @add="addSupplier"
                @update="updateSupplier"
                :consultMod="this.isInConsultMod"
                :modifMod="this.modifMod"
                :savedAs="validate"/>
        </b-form>
    </div>
</template>

<script>
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputNumberForm from '../../../input/SW03/InputNumberForm.vue'
import InputTextWithOptionForm from '../../../input/InputTextWithOptionForm.vue'
import RadioGroupForm from '../../../input/SW03/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputTextForm,
        InputSelectForm,
        InputNumberForm,
        InputTextWithOptionForm,
        InputTextAreaForm,
        RadioGroupForm,
        SaveButtonForm,
        SuccessAlert
    },
    /*--------Declaration of the different props:--------
        ID: ID of the supplier
        Name: Name of the supplier
        ReceptionNumber: Reception number of the supplier
        FormId: ID of the formation of the supplier
        consFam_id: ID of the consumable family delivered by the supplier
        compFam_id: ID of the component family delivered by the supplier
        rawFam_id: ID of the raw material family delivered by the supplier
        agreementNumber: Agreement number of the supplier
        qualityCertificateNumber: Quality certification number of the supplier
        specificsInstructions: Specifics instructions for the supplier
        technicalReviewer: Technical reviewer of the supplier's sheet
        validate: Validation of the supplier's sheet
        signatureDate: Signature date of the supplier's sheet
        siret: Siret number of the supplier
        webSite: Website of the supplier
        activity: Activity of the supplier
        real: Real supplier or not
        VatNumber: VAT number of the supplier
        critical: Critical supplier or not
        endLinkToFolder: TODO expliquer ce que c'est
        active: Active supplier or not
    ---------------------------------------------------*/
    props: {
        ID: {
            type: Number
        },
        Name : {
            type: String
        },
        ReceptionNumber : {
            type: String
        },
        FormId : {
            type: String
        },
        consFam_id : {
            type: String
        },
        compFam_id : {
            type: String
        },
        rawFam_id : {
            type: String
        },
        agreementNumber : {
            type: String
        },
        qualityCertificateNumber : {
            type: String
        },
        specificsInstructions : {
            type: String
        },
        technicalReviewer : {
            type: String
        },
        validate : {
            type: String
        },
        signatureDate : {
            type: String
        },
        siret : {
            type: String
        },
        webSite : {
            type: String
        },
        activity : {
            type: String
        },
        real : {
            type: Boolean
        },
        VatNumber : {
            type: String
        },
        critical : {
            type: Boolean
        },
        endLinkToFolder : {
            type: String
        },
        active : {
            type: Boolean
        },
        concern : {
            type: Boolean,
            default: true
        },
        consultMod : {
            type: Boolean,
            default: false
        },
        modifMod : {
            type: Boolean,
            default: false
        },
        disableImport : {
            type: Boolean,
            default: false
        }/*,
        min : {
            type: Number,
            default: 3
        },
        max : {
            type: Number,
            default: 255
        }*/
    },
    data() {
        return {
            /*--------Declaration of the different returned data:--------
                ID: ID of the supplier
                Name: Name of the supplier
                ReceptionNumber: Reception number of the supplier
                FormId: ID of the formation of the supplier
                consFam_id: ID of the consumable family delivered by the supplier
                compFam_id: ID of the component family delivered by the supplier
                rawFam_id: ID of the raw material family delivered by the supplier
                agreementNumber: Agreement number of the supplier
                qualityCertificateNumber: Quality certification number of the supplier
                specificsInstructions: Specifics instructions for the supplier
                technicalReviewer: Technical reviewer of the supplier's sheet
                validate: Validation of the supplier's sheet
                signatureDate: Signature date of the supplier's sheet
                siret: Siret number of the supplier
                webSite: Website of the supplier
                activity: Activity of the supplier
                real: Real supplier or not
                VatNumber: VAT number of the supplier
                critical: Critical supplier or not
                endLinkToFolder: TODO expliquer ce que c'est
                active: Active supplier or not
            -----------------------------------------------------------*/
            supplr_id: this.ID,
            supplr_name: this.Name,
            supplr_receptionNumber: this.ReceptionNumber,
            supplr_formId: this.FormId,
            supplr_consFam_id: this.consFam_id,
            supplr_compFam_id: this.compFam_id,
            supplr_rawFam_id: this.rawFam_id,
            supplr_agreementNumber: this.agreementNumber,
            supplr_qualityCertificateNumber: this.qualityCertificateNumber,
            supplr_specificsInstructions: this.specificsInstructions,
            supplr_technicalReviewer: this.technicalReviewer,
            supplr_validate: this.validate,
            supplr_signatureDate: this.signatureDate,
            supplr_siret: this.siret,
            supplr_webSite: this.webSite,
            supplr_activity: this.activity,
            supplr_real: this.real,
            supplr_VatNumber: this.VatNumber,
            supplr_critical: this.critical,
            supplr_endLinkToFolder: this.endLinkToFolder,
            supplr_active: this.active,
            supplr_concern: this.concern,
            errors: [],
            infos_idCard: [],
            addSuccess: false,
            loaded: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
        }
    },
    created() {
        axios.get('/info/send/supplier')
            .then(response => {
                this.infos_idCard = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        addSupplier(savedAs) {
            axios.post('/supplier/verif', {
                supplr_name: this.supplr_name,
                supplr_receptionNumber: this.supplr_receptionNumber,
                supplr_formId: this.supplr_formId,
                supplr_consFam_id: this.supplr_consFam_id,
                supplr_compFam_id: this.supplr_compFam_id,
                supplr_rawFam_id: this.supplr_rawFam_id,
                supplr_agreementNumber: this.supplr_agreementNumber,
                supplr_qualityCertificateNumber: this.supplr_qualityCertificateNumber,
                supplr_specificsInstructions: this.supplr_specificsInstructions,
                supplr_validate: savedAs,
                supplr_siret: this.supplr_siret,
                supplr_webSite: this.supplr_webSite,
                supplr_activity: this.supplr_activity,
                supplr_real: this.supplr_real,
                supplr_VATnumber: this.supplr_VatNumber,
                supplr_critical: this.supplr_critical,
                supplr_endLinkToFolder: this.supplr_endLinkToFolder,
                supplr_active: this.supplr_active,
                supplr_concern: this.supplr_concern,
            }).then(response => {
                this.errors = [];
                axios.post('/supplier/add', {
                    supplr_name: this.supplr_name,
                    supplr_receptionNumber: this.supplr_receptionNumber,
                    supplr_formId: this.supplr_formId,
                    supplr_consFam_id: this.supplr_consFam_id,
                    supplr_compFam_id: this.supplr_compFam_id,
                    supplr_rawFam_id: this.supplr_rawFam_id,
                    supplr_agreementNumber: this.supplr_agreementNumber,
                    supplr_qualityCertificateNumber: this.supplr_qualityCertificateNumber,
                    supplr_specificsInstructions: this.supplr_specificsInstructions,
                    supplr_validate: savedAs,
                    supplr_siret: this.supplr_siret,
                    supplr_webSite: this.supplr_webSite,
                    supplr_activity: this.supplr_activity,
                    supplr_real: this.supplr_real,
                    supplr_VATnumber: this.supplr_VatNumber,
                    supplr_critical: this.supplr_critical,
                    supplr_endLinkToFolder: this.supplr_endLinkToFolder,
                    supplr_active: this.supplr_active,
                    supplr_concern: this.supplr_concern,
                }).then(response => {
                    this.$snotify.success('Supplier\'s ID card is correctly added in the database as ' + savedAs);
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                    this.supplr_id = response.data.id;
                    this.$emit('SupplierID', this.supplr_id);
                }).catch(error => {
                    this.errors = error.response.data.errors;
                });
            }).catch(error => {
                this.errors = error.response.data.errors
            });
        },
        updateSupplier(savedAs, reason, lifeSheetExist) {
            axios.post('/supplier/verif', {
                supplr_name: this.supplr_name,
                supplr_receptionNumber: this.supplr_receptionNumber,
                supplr_formId: this.supplr_formId,
                supplr_consFam_id: this.supplr_consFam_id,
                supplr_compFam_id: this.supplr_compFam_id,
                supplr_rawFam_id: this.supplr_rawFam_id,
                supplr_agreementNumber: this.supplr_agreementNumber,
                supplr_qualityCertificateNumber: this.supplr_qualityCertificateNumber,
                supplr_specificsInstructions: this.supplr_specificsInstructions,
                supplr_validate: savedAs,
                supplr_siret: this.supplr_siret,
                supplr_webSite: this.supplr_webSite,
                supplr_activity: this.supplr_activity,
                supplr_real: this.supplr_real,
                supplr_VATnumber: this.supplr_VatNumber,
                supplr_critical: this.supplr_critical,
                supplr_endLinkToFolder: this.supplr_endLinkToFolder,
                supplr_active: this.supplr_active,
                supplr_concern: this.supplr_concern,
            })
                /*If the data are correct, we send them to the controller for update data in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => '/supplier/update/' + id;
                    axios.post(consultUrl(this.supplr_id), {
                        supplr_name: this.supplr_name,
                        supplr_receptionNumber: this.supplr_receptionNumber,
                        supplr_formId: this.supplr_formId,
                        supplr_consFam_id: this.supplr_consFam_id,
                        supplr_compFam_id: this.supplr_compFam_id,
                        supplr_rawFam_id: this.supplr_rawFam_id,
                        supplr_agreementNumber: this.supplr_agreementNumber,
                        supplr_qualityCertificateNumber: this.supplr_qualityCertificateNumber,
                        supplr_specificsInstructions: this.supplr_specificsInstructions,
                        supplr_validate: savedAs,
                        supplr_siret: this.supplr_siret,
                        supplr_webSite: this.supplr_webSite,
                        supplr_activity: this.supplr_activity,
                        supplr_real: this.supplr_real,
                        supplr_VATnumber: this.supplr_VatNumber,
                        supplr_critical: this.supplr_critical,
                        supplr_endLinkToFolder: this.supplr_endLinkToFolder,
                        supplr_active: this.supplr_active,
                        supplr_concern: this.supplr_concern,
                    })
                        .then(response => {
                            /*We test if an article sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifeSheetExist == true) {
                                axios.post('/supplier/history/add/' + this.supplr_id, {
                                    history_reasonUpdate: reason,
                                }).catch(error => {
                                    this.errors = error.response.data.errors;
                                });
                                window.location.reload();
                            }
                            this.isInConsultMod = true;
                            /*If the data have been updated in the database, we show a success message*/
                            this.$snotify.success(`CompFam ID successfully updated and saved as ${savedAs}`);
                            this.supplr_validate = savedAs;
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        });
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
        },

    }
}
</script>

<style lang="scss">
.titleForm1 {
    padding-left: 10px;
    right: 100px;
    position: fixed;
    background-color: aqua;
    top: 90px;
    z-index: 5;
}

form {
    margin: 20px 20px 50px;
}

.container {
    margin-top: 50px;
}
</style>

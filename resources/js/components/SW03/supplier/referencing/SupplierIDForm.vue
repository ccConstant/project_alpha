<!--File name : SupplierIDForm.vue-->
<!--Creation date : 21 Apr 2021-->
<!--Update date : 21 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div class="supplierID">
        <h2>Supplier's Form</h2>
        <h2 class="titleForm1">Supplier ID</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <b-form class="container" @keydown="">
            <InputTextForm
                :name="'SupplierName'"
                :label="'Name'"
                :value="supplr_name"
                :isRequired="true"
                :info_text="'SupplierController Name'"
                :inputClassName="null"
                :Errors="errors['SupplierName']"
                :min="min"
                :max="max"
            />
            <InputNumberForm
                :name="'SupplierReceptionNumber'"
                :label="'Reception Number'"
                :value="supplr_receptionNumber"
                :info_text="'SupplierController Reception Number'"
                :inputClassName="null"
                :Errors="errors['SupplierReceptionNumber']"
            />
            <InputTextForm
                :name="'AgreementNumber'"
                :label="'Agreement Number'"
                :value="supplr_agreementNumber"
                :info_text="'SupplierController Agreement Number'"
                :inputClassName="null"
                :Errors="errors['AgreementNumber']"
            />
            <InputTextForm
                :name="'QualityCertificateNumber'"
                :label="'Quality Certificate Number'"
                :value="supplr_qualityCertificationNumber"
                :info_text="'SupplierController Quality Certificate Number'"
                :inputClassName="null"
                :Errors="errors['QualityCertificateNumber']"
            />
            <InputTextForm
                :name="'SIRET'"
                :label="'SIRET'"
                :value="supplr_siret"
                :info_text="'SupplierController SIRET'"
                :inputClassName="null"
                :Errors="errors['SIRET']"
            />
            <InputTextForm
                :name="'VATNumber'"
                :label="'VAT Number'"
                :value="supplr_VatNumber"
                :info_text="'SupplierController VAT Number'"
                :inputClassName="null"
                :Errors="errors['VATNumber']"
            />
            <InputTextForm
                :name="'WebSite'"
                :label="'Web Site'"
                :value="supplr_webSite"
                :info_text="'SupplierController Web Site'"
                :inputClassName="null"
                :Errors="errors['WebSite']"
            />
            <InputTextForm
                :name="'Activity(ies)'"
                :label="'Activity(ies)'"
                :value="supplr_activity"
                :info_text="'SupplierController Activity(ies)'"
                :inputClassName="null"
                :Errors="errors['Activity(ies)']"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Real ?'"
                :label="'Real'"
                :value="supplr_real"
                :info_text="'SupplierController is real or not ?'"
                :inputClassName="null"
                :Errors="errors['Real']"
                :checked-option="true"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Critical ?'"
                :label="'Critical'"
                :value="supplr_critical"
                :info_text="'SupplierController is critical or not ?'"
                :inputClassName="null"
                :Errors="errors['Critical']"
                :checked-option="false"
            />
            <InputTextForm
                :name="'Form ID'"
                :label="'Form ID'"
                :value="supplr_formId"
                :info_text="'SupplierController\'s Form ID'"
                :input-class-name="null"
                :Errors="errors['FormID']"
            />
            <InputTextForm
                :name="'Specific Instructions'"
                :label="'Specific Instruction'"
                :value="supplr_specificsInstructions"
                :info_text="'Specific Instructions'"
                :input-class-name="null"
                :Errors="errors['SpecInstr']"
            />
            <InputTextForm
                :name="'End Link to Folder'"
                :label="'End Link to Folder'"
                :value="supplr_endLinkToFolder"
                :info_text="'End Link to Folder'"
                :input-class-name="null"
                :Errors="errors['endLink']"
            />
<!--            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Active ?'"
                :label="'Active'"
                :value="Active"
                :info_text="'SupplierController is active or not ?'"
                :inputClassName="null"
                :Errors="errors['Active']"
                :checked-option="false"
            />-->

            <SaveButtonForm
                ref="saveButton"
                v-if="this.addSuccess==false"
                @add="addSupplier"
                @update="updateSupplier"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInEditMod"
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
        qualityCertificationNumber: Quality certification number of the supplier
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
            type: String
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
        qualityCertificationNumber : {
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
            type: Boolean,
            default: false
        },
        VatNumber : {
            type: String
        },
        critical : {
            type: Boolean,
            default: false
        },
        endLinkToFolder : {
            type: String
        },
        active : {
            type: Boolean,
            default: true
        },
        isInConsultMod : {
            type: Boolean,
            default: false
        },
        isInEditMod : {
            type: Boolean,
            default: false
        },
        disableImport : {
            type: Boolean,
            default: false
        },
        min : {
            type: Number,
            default: 3
        },
        max : {
            type: Number,
            default: 255
        }
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
                qualityCertificationNumber: Quality certification number of the supplier
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
            supplr_SupplierID: this.ID,
            supplr_name: this.Name,
            supplr_receptionNumber: this.ReceptionNumber,
            supplr_formId: this.FormId,
            supplr_consFam_id: this.consFam_id,
            supplr_compFam_id: this.compFam_id,
            supplr_rawFam_id: this.rawFam_id,
            supplr_agreementNumber: this.agreementNumber,
            supplr_qualityCertificationNumber: this.qualityCertificationNumber,
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
            errors: {},
            infos_idCard: [],
            addSuccess: false,
            loaded: false,
        }
    },
    created() {
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        addSupplier(savedAs) {

        },
        updateSupplier(savedAs, reason, lifeSheetExist) {

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

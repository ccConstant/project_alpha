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
                <InputTextForm
                    name="street"
                    label="Street"
                    v-model="supplrAdr_street"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_street"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="town"
                    label="Town"
                    v-model="supplrAdr_town"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_town"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="country"
                    label="Country"
                    v-model="supplrAdr_country"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_country"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="Name"
                    label="Name"
                    isRequired
                    v-model="supplrAdr_name"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_name"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    isRequired
                    v-model="supplrAdr_principal"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_principal"
                    name="principal"
                    label="Principal"
                    :checked-option="true"
                    :isDisabled="isInConsultMod"
                />
                <SaveButtonForm
                    ref="saveButton"
                    v-if="this.addSuccess===false"
                    @add="addSupplierAdr"
                    @update="updateSupplierAdr"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.modifMod"
                    :savedAs="validate"
                />
                <div v-if="this.addSuccess==false ">
                    <!--If this file doesn't have a id the addEquipmentFile is called function else the updateEquipmentFile function is called -->
                    <div v-if="this.adr_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.adr_id!==null">
                        <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                        :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                                        :savedAs="validate"/>
                    </div>
                </div>
            </form>
            <SuccessAlert ref="successAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";



export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        SuccessAlert
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
        street: {
            type: String,
            default: null
        },
        town: {
            type: String,
            default: null
        },
        country: {
            type: String,
            default: null
        },
        name: {
            type: String,
            default: null
        },
        principal: {
            type: Boolean,
            default: false
        },
        isDisabled: {
            type: Boolean,
            default: false
        },
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        supplier_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        id: {
            type: Number,
            default: null
        },
        validate: {
            type: String
        },
        divClass: {
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
            addSuccess: false,
            updateSuccess: false,
            Errors: [],
            supplrAdr_name: this.name,
            supplrAdr_street: this.street,
            supplrAdr_town: this.town,
            supplrAdr_country: this.country,
            supplrAdr_principal: this.principal,
            adr_id: this.id,
            loaded: false,
            isInConsultMod: this.consultMod,
        }
    },
    methods: {
        /*Sending to the controller all the information about the article so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        addSupplierAdr(savedAs) {
            axios.post('/supplier/adr/verif', {
                supplrAdr_validate: savedAs,
                supplrAdr_street: this.supplrAdr_street,
                supplrAdr_town: this.supplrAdr_town,
                supplrAdr_country: this.supplrAdr_country,
                supplrAdr_name: this.supplrAdr_name,
                supplrAdr_principal: this.supplrAdr_principal,
                supplr_id: this.supplier_id,
            }).then(response => {
                this.Errors = [];
                axios.post('/supplier/adr/add', {
                    supplrAdr_validate: savedAs,
                    supplrAdr_street: this.supplrAdr_street,
                    supplrAdr_town: this.supplrAdr_town,
                    supplrAdr_country: this.supplrAdr_country,
                    supplrAdr_name: this.supplrAdr_name,
                    supplrAdr_principal: this.supplrAdr_principal,
                    supplr_id: this.supplier_id,
                }).catch(error => {
                    this.Errors = error.response.data.errors;
                }).then(response => {
                    this.$snotify.success('Supplier\'s address is correctly added in the database as ' + savedAs);
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
                this.Errors = error.response.data.errors;
            });
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateSupplierAdr(savedAs, reason, lifeSheetExist) {
            axios.post('/supplier/adr/verif', {
                supplrAdr_validate: savedAs,
                supplrAdr_street: this.supplrAdr_street,
                supplrAdr_town: this.supplrAdr_town,
                supplrAdr_country: this.supplrAdr_country,
                supplrAdr_name: this.supplrAdr_name,
                supplrAdr_principal: this.supplrAdr_principal,
                supplr_id: this.supplier_id,
            }).then(response => {
                this.Errors = [];
                axios.post('/supplier/adr/update/' + this.adr_id, {
                    supplrAdr_validate: savedAs,
                    supplrAdr_street: this.supplrAdr_street,
                    supplrAdr_town: this.supplrAdr_town,
                    supplrAdr_country: this.supplrAdr_country,
                    supplrAdr_name: this.supplrAdr_name,
                    supplrAdr_principal: this.supplrAdr_principal,
                    supplr_id: this.supplier_id
                }).catch(error => {
                    this.Errors = error.response.data.errors;
                }).then(response => {
                    this.$snotify.success('Supplier\'s address is correctly updated in the database as ' + savedAs);
                    this.updateSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
                this.Errors = error.response.data.errors;
            });
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
    },
    created() {
        this.loaded = true;
    },
}
</script>

<style>
</style>

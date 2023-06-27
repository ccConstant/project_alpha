<!--File name : EquipmentIDForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the Id card of the equipment who call all the input component and send the data to the controllers-->

<template>
    <div v-if="loaded==true" class="equipmentID">
        <h2 class="titleForm1">EQ ID : {{ eq_internalReference }}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
            <InputTextForm v-model="eq_internalReference" :Errors="errors.eq_internalReference"
                           :info_text="infos_idCard[0].info_value" :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" isRequired
                           label="Unique ID" name="eq_internalReference"/>
            <InputTextWithOptionForm v-model="eq_set" :Errors="errors.eq_set" :info_text="infos_idCard[10].info_value"
                                     :isDisabled="!!isInConsultMod" :options="enum_sets" inputClassName="form-control w-50"
                                     label="Equipment Family" name="eq_set"/>
            <InputTextForm v-model="eq_name" :Errors="errors.eq_name" :info_text="infos_idCard[2].info_value"
                           :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" label="Equipment name :"
                           name="eq_name"/>
            <InputTextForm v-model="eq_externalReference" :Errors="errors.eq_externalReference"
                           :info_text="infos_idCard[1].info_value" :isDisabled="!!isInConsultMod" inputClassName="form-control w-50"
                           isRequired label="Eq ref in Alpha Database :" name="eq_externalReference"/>
            <InputSelectForm v-model="eq_type" :Errors="errors.eq_type"
                             :id_actual="equipmentType" :info_text="infos_idCard[3].info_value" :isDisabled="!!isInConsultMod" :options="enum_type"
                             :selctedOption="eq_type" label="Type :" name="eq_type"
                             selectClassName="form-select w-50" @clearSelectError='clearSelectError'/>
            <InputTextForm v-model="eq_serialNumber" :Errors="errors.eq_serialNumber" :info_text="infos_idCard[4].info_value"
                           :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" label="Equipment serial Number :"
                           name="eq_serialNumber"/>
            <InputTextForm v-model="eq_constructor" :Errors="errors.eq_constructor" :info_text="infos_idCard[5].info_value"
                           :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" label="Equipment constructor :"
                           name="eq_constructor"/>
            <InputNumberForm v-model="eq_mass" :Errors="errors.eq_mass" :info_text="infos_idCard[6].info_value"
                             :isDisabled="!!isInConsultMod" :stepOfInput="0.01" inputClassName="form-control w-50"
                             label="Equipment mass :" name="eq_mass"/>
            <InputSelectForm v-model="eq_massUnit" :Errors="errors.eq_massUnit" :id_actual="equipmentMassUnit"
                             :info_text="infos_idCard[7].info_value"
                             :isDisabled="!!isInConsultMod" :options="enum_massUnit" :selctedOption="eq_massUnit"
                             label="Unit :" name="eq_massUnit"
                             selectClassName="form-control w-50" @clearSelectError='clearSelectError'/>
            <RadioGroupForm v-model="eq_mobility" :Errors="errors.eq_mobilityOption" :checkedOption="eq_mobility"
                            :info_text="infos_idCard[8].info_value" :isDisabled="!!isInConsultMod" :options="eq_mobilityOption"
                            label="Mobil?:"/>
            <InputTextAreaForm v-model="eq_remarks" :Errors="errors.eq_remarks" :info_text="infos_idCard[9].info_value"
                               :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" label="Remarks :"
                               name="eq_remarks"/>
            <InputTextForm v-if="this.eq_importFrom!== undefined " v-model="eq_importFrom"
                           inputClassName="form-control w-50" isDisabled label="Import From :" name="eq_importFrom"/>
            <InputTextForm v-model="eq_location" :Errors="errors.eq_location" :info_text="infos_idCard[11].info_value"
                           :isDisabled="!!isInConsultMod" inputClassName="form-control w-50" isRequired label="Location"
                           name="eq_location"/>
            <SaveButtonForm v-if="this.addSuccess==false" ref="saveButton" :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                            :savedAs="eq_validate" @add="addEquipment" @update="updateEquipment"/>
            <div v-if="this.modifMod!=true">
                <div v-if="this.isInConsultMod!=true">
                    <ImportationModal v-if="disableImport==false" :set="this.eq_set" @choosedEq="importFrom"/>
                </div>
            </div>
            <SuccessAlert ref="SuccessAlert"/>
        </form>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputNumberForm from '../../../input/InputNumberForm.vue'
import InputTextWithOptionForm from '../../../input/InputTextWithOptionForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationModal from './ImportationModal.vue'
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
        ImportationModal,
        SuccessAlert
    },
    /*--------Declaration of the different props:--------
        internalReference : Internal reference given by the data base we will put this data in the corresponding field as default value
        externalReference : External reference given by the data base we will put this data in the corresponding field as default value
        name : Name given by the data base we will put this data in the corresponding field as default value
        type : Type given by the data base we will put this data in the corresponding field as default value
        serialNumber : SerialNumber given by the data base we will put this data in the corresponding field as default value
        construct : Constructor given by the data base we will put this data in the corresponding field as default value
        mass : Mass given by the data base we will put this data in the corresponding field as default value
        massUnit : Unit of the mass given by the data base we will put this data in the corresponding field as default value
        mobility : Mobility given by the data base we will put this data in the corresponding field as default value
        Remarks : Remarks given by the data base we will put this data in the corresponding field as default value
        Set: Set given by the data base we will put this data in the corresponding field as default value
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button

    ---------------------------------------------------*/
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
        type: {
            type: String
        },
        serialNumber: {
            type: String
        },
        construct: {
            type: String
        },
        mass: {
            type: String
        },
        massUnit: {
            type: String
        },
        mobility: {
            type: Boolean
        },
        remarks: {
            type: String
        },
        set: {
            type: String
        },
        location: {
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
        old_eq: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            /*--------Declaration of the different returned data:--------
                eq_internalReference: Internal reference of the equipment who  will be appear in the field and updated dynamically
                eq_externalReference: External reference of the equipment who  will be appear in the field and updated dynamically
                eq_name: Name of the equipment who  will be appear in the field and updated dynamically
                eq_type: Type of the equipment who  will be appear in the field and updated dynamically
                eq_serialNumber: Serial Number of the equipment who  will be appear in the field and updated dynamically
                eq_constructor: Constructor of the equipment who  will be appear in the field and updated dynamically
                eq_remarks : Remarks about the equipment who  will be appear in the field and updated dynamically
                eq_validate: Validation option selected by the user
                eq_validates : Different validation option with values : drafted , to_be_validated  and validated
                eq_mobilityOption : Different mobility option with their names and values
                enum_type: Different type option gets from the database
                enum_massUnit : Different unit of mass option gets from the database
                enum_sets : Other equipments sets gets from the database
                eq_id : ID of the current equipment get from the url
                errors : Errors due to a wrong input in the field, given by the controller
            -----------------------------------------------------------*/
            eq_internalReference: this.internalReference,
            eq_externalReference: this.externalReference,
            eq_name: this.name,
            eq_type: this.type,
            eq_serialNumber: this.serialNumber,
            eq_constructor: this.construct,
            eq_mass: this.mass,
            eq_massUnit: this.massUnit,
            eq_mobility: this.mobility,
            eq_remarks: this.remarks,
            eq_set: this.set,
            eq_validate: this.validate,
            eq_importFrom: undefined,
            eq_location: this.location,
            eq_mobilityOption: [
                {id: 'eq_mobil', value: true, text: 'Yes'},
                {id: 'eq_mobil', value: false, text: 'No'}
            ],
            isInConsultMod: this.consultMod,
            enum_type: [],
            enum_massUnit: [],
            enum_sets: [],
            eq_id: this.$route.params.id,
            errors: {},
            addSuccess: false,
            infos_idCard: [],
            info_eq_internalReference: '',
            loaded: false,
            equipmentMassUnit: "EquipmentMassUnit",
            equipmentType: "EquipmentType",

        }
    },
    created() {
        /*Ask for the controller different type option */
        axios.get('/equipment/enum/type')
            .then(response => {
                this.enum_type = response.data;
                /*Ask for the controller different mass unit option */
                axios.get('/equipment/enum/massUnit')
                    .then(response => {
                        this.enum_massUnit = response.data;
                        /*Ask for the controller other equipments sets */
                        axios.get('/equipment/sets')
                            .then(response => {
                                this.enum_sets = response.data;
                                /*Ask for the controller the information about equipment */
                                axios.get('/info/send/eqIdCard')
                                    .then(response => {
                                        this.infos_idCard = response.data;
                                        this.loaded = true;
                                    }).catch(error => {
                                });
                            }).catch(error => {
                        });
                    }).catch(error => {
                });
            }).catch(error => {
        });
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be added to the database */
        addEquipment(savedAs) {
            if (!this.addSuccess) {
                /*We begin by checking if the data entered by the user are correct*/
                axios.post('/equipment/verif', {
                    eq_internalReference: this.eq_internalReference,
                    eq_externalReference: this.eq_externalReference,
                    eq_name: this.eq_name,
                    eq_type: this.eq_type,
                    eq_serialNumber: this.eq_serialNumber,
                    eq_constructor: this.eq_constructor,
                    eq_mass: this.eq_mass,
                    eq_massUnit: this.eq_massUnit,
                    eq_mobility: this.eq_mobility,
                    eq_remarks: this.eq_remarks,
                    eq_set: this.eq_set,
                    eq_validate: savedAs,
                    eq_location: this.eq_location,
                    reason: 'add',
                    enteredBy_id: this.$userId.id

                })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        if (this.state_id !== null) {
                            /*case where the equipment is added from the state page*/
                            const consultUrl = (state_id) => `/state/equipment/${state_id}`;
                            axios.post(consultUrl(this.state_id), {
                                eq_internalReference: this.eq_internalReference,
                                eq_externalReference: this.eq_externalReference,
                                eq_name: this.eq_name,
                                eq_type: this.eq_type,
                                eq_serialNumber: this.eq_serialNumber,
                                eq_constructor: this.eq_constructor,
                                eq_mass: this.eq_mass,
                                eq_massUnit: this.eq_massUnit,
                                eq_mobility: this.eq_mobility,
                                eq_remarks: this.eq_remarks,
                                eq_set: this.eq_set,
                                eq_validate: savedAs,
                                eq_location: this.eq_location,
                                oldEq_id: this.old_eq,
                                enteredBy_id: this.$userId.id

                            })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.$refs.SuccessAlert.showAlert(`Equipment ID card added successfully and saved as ${savedAs}`);
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.eq_id = response.data;
                                    this.$emit('EqID', this.eq_id);
                                }).catch(error => this.errors = error.response.data.errors);

                        } else {
                            /*case the equipment is added from the equipment page*/
                            axios.post('/equipment/add', {
                                eq_internalReference: this.eq_internalReference,
                                eq_externalReference: this.eq_externalReference,
                                eq_name: this.eq_name,
                                eq_type: this.eq_type,
                                eq_serialNumber: this.eq_serialNumber,
                                eq_constructor: this.eq_constructor,
                                eq_mass: this.eq_mass,
                                eq_massUnit: this.eq_massUnit,
                                eq_mobility: this.eq_mobility,
                                eq_remarks: this.eq_remarks,
                                eq_set: this.eq_set,
                                eq_validate: savedAs,
                                createdBy_id: this.$userId.id,
                                eq_location: this.eq_location,
                            })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.$refs.SuccessAlert.showAlert(`Equipment ID card added successfully and saved as ${savedAs}`);
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.eq_id = response.data;
                                    this.$emit('EqID', this.eq_id);

                                }).catch(error => this.errors = error.response.data.errors);
                        }
                    }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateEquipment(savedAs, reason, lifesheet_created) {
            /*We begin by checking if the data entered by the user are correct*/
            axios.post('/equipment/verif', {
                eq_internalReference: this.eq_internalReference,
                eq_externalReference: this.eq_externalReference,
                eq_name: this.eq_name,
                eq_type: this.eq_type,
                eq_serialNumber: this.eq_serialNumber,
                eq_constructor: this.eq_constructor,
                eq_mass: this.eq_mass,
                eq_massUnit: this.eq_massUnit,
                eq_mobility: this.eq_mobility,
                eq_remarks: this.eq_remarks,
                eq_set: this.eq_set,
                eq_validate: savedAs,
                eq_id: this.eq_id,
                eq_location: this.eq_location,
                reason: 'update'
            })
                /*If the data are correct, we send them to the controller for update data in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => `/equipment/update/${id}`;
                    axios.post(consultUrl(this.eq_id), {
                        eq_internalReference: this.eq_internalReference,
                        eq_externalReference: this.eq_externalReference,
                        eq_name: this.eq_name,
                        eq_type: this.eq_type,
                        eq_serialNumber: this.eq_serialNumber,
                        eq_constructor: this.eq_constructor,
                        eq_mass: this.eq_mass,
                        eq_massUnit: this.eq_massUnit,
                        eq_mobility: this.eq_mobility,
                        eq_remarks: this.eq_remarks,
                        eq_set: this.eq_set,
                        eq_location: this.eq_location,
                        eq_validate: savedAs,
                    }).then(response => {
                        const id = this.eq_id;
                        /*We test if a life sheet has been already created*/
                        /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                        if (lifesheet_created == true) {
                            axios.post(`/history/add/equipment/${id}`, {
                                history_reasonUpdate: reason,
                            });
                            window.location.reload();
                        }
                        /*If the data have been updated in the database, we show a success message*/
                        this.$refs.SuccessAlert.showAlert(`Equipment ID card updated successfully and saved as ${savedAs}`);
                        this.eq_validate = savedAs;
                    }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        importFrom(value) {
            this.eq_importFrom = value.eq_internalReference;
            this.$emit('importFromEqID', value.id);
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
.titleForm1 {
    padding-left: 10px;
    right: 100px;
    position: fixed;
    background-color: aqua;
    top: 90px;
    z-index: 5;
}

form {
    margin: 20px;
    margin-bottom: 50px;
}

.container {
    margin-top: 50px;
}
</style>

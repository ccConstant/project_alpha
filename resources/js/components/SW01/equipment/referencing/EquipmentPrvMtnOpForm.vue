<!--File name : EquipmentPrvMtnOpForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the Form of the equipment preventive maintenance operation who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <div
                    v-if="isInConsultedMod==true && this.prvMtnOp_number!==null || this.modifMod==true && this.prvMtnOp_number!==null">
                    <InputNumberForm v-model="prvMtnOp_number" :Errors="errors.prvMtnOp_number"
                                     :info_text="infos_prvMtnOp[4].info_value" :stepOfInput="1" inputClassName="form-control w-50" isDisabled
                                     label="Number :" name="prvMtnOp_number"/>
                </div>
                <RadioGroupForm v-model="prvMtnOp_puttingIntoService" :Errors="errors.prvMtnOp_puttingIntoService"
                                :checkedOption="prvMtnOp_puttingIntoService"
                                :info_text="infos_prvMtnOp[6].info_value" :isDisabled="!!isInConsultedMod"
                                :options="existOptionPIS"
                                label="Putting Into Service ?:"
                                name="prvMtnOp_puttingIntoService"
                />
                <RadioGroupForm v-model="prvMtnOp_preventiveOperation" :Errors="errors.prvMtnOp_preventiveOperation"
                                :checkedOption="prvMtnOp_preventiveOperation"
                                :info_text="infos_prvMtnOp[7].info_value" :isDisabled="!!isInConsultedMod"
                                :options="existOptionPO"
                                label="Preventive Operation ?:"
                                name="prvMtnOp_preventiveOperation"
                />
                <InputTextAreaForm v-model="prvMtnOp_description" :Errors="errors.prvMtnOp_description"
                                   :info_text="infos_prvMtnOp[0].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Description :" name="prvMtnOp_description"/>
                <InputNumberForm v-model="prvMtnOp_periodicity" :Errors="errors.prvMtnOp_periodicity"
                                 :info_text="infos_prvMtnOp[1].info_value" :isDisabled="!!isInConsultedMod" :stepOfInput="1"
                                 inputClassName="form-control w-50" label="Periodicity :"
                                 name="prvMtnOp_periodicity"/>
                <InputSelectForm v-model="prvMtnOp_symbolPeriodicity" :Errors="errors.prvMtnOp_symbolPeriodicity"
                                 :id_actual="SymbolPeriodicity"
                                 :info_text="infos_prvMtnOp[2].info_value" :isDisabled="!!isInConsultedMod"
                                 :number="this.prvMtnOp_id" :options="enum_periodicity_symbol"
                                 :selctedOption="prvMtnOp_symbolPeriodicity" :selectedDivName="this.divClass"
                                 label="Symbol :" name="prvMtnOp_symbolPeriodicity"
                                 selectClassName="form-control w-50" @clearSelectError='clearSelectError'/>
                <InputTextAreaForm v-model="prvMtnOp_protocol" :Errors="errors.prvMtnOp_protocol"
                                   :info_text="infos_prvMtnOp[3].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Protocol :" name="prvMtnOp_protocol"/>
                <RadioGroupForm v-model="typeValidation" :Errors="errors.typeValidation"
                                :checkedOption="typeValidation"
                                :info_text="null" :isDisabled="!!isInConsultedMod"
                                :options="typeValidationOption"
                                label="Type of Validation"
                                name="typeValidation"
                />
                <!--If addSuccess is equal to false, the buttons appear -->
                <div v-if="this.addSuccess==false ">
                    <!--If this preventive maintenance operation doesn't have a id the addEquipmentPrvMtnOp is called function else the updateEquipmentPrvMtnOp function is called -->
                    <div v-if="this.prvMtnOp_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="prvMtnOp_validate" @add="addEquipmentPrvMtnOp"
                                            @update="updateEquipmentPrvMtnOp"/>
                        </div>
                        <div v-else> <!-- Show in gray the input if we are in consultation mode-->
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="prvMtnOp_validate"
                                            @add="addEquipmentPrvMtnOp" @update="updateEquipmentPrvMtnOp"/>
                        </div>
                    </div>
                    <div v-else-if="this.prvMtnOp_id!==null && reformMod==false ">
                        <div v-if="prvMtnOp_reformDate!=null">
                            <p>Reformed at {{ prvMtnOp_reformDate }}</p>
                        </div>
                        <div v-else>
                            <!-- The button under all of the forms -->
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                            :savedAs="prvMtnOp_validate" @add="addEquipmentPrvMtnOp"
                                            @update="updateEquipmentPrvMtnOp"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :Errors="errors.prvMtnOp_delete" :consultMod="this.isInConsultedMod"
                                           :validationMode="prvMtnOp_validate" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && prvMtnOp_reformDate===null">
                        <ReformComponentButton :info="infos_prvMtnOp[5].info_value" :reformBy="prvMtnOp_reformBy"
                                               :reformDate="prvMtnOp_reformDate" :reformMod="this.isInReformMod"
                                               @reformOk="reformComponent"/>
                    </div>
                </div>
            </form>
            <successAlert ref="successAlert"/>
            <div v-if="this.prvMtnOp_id!==null && modifMod==false && consultMod==false && import_id==null ">
                <ReferenceARisk :eq_id="this.eq_id" :prvMtnOp_id="this.prvMtnOp_id" :riskForEq="false"/>
            </div>
            <div v-else-if="this.prvMtnOp_id!==null && modifMod==true">
                <ReferenceARisk v-if="this.prvMtnOp_id!=null" :consultMod="!!isInConsultedMod" :eq_id="this.eq_id"
                                :importedRisk="importedOpRisk" :modifMod="!!this.modifMod" :prvMtnOp_id="this.prvMtnOp_id"
                                :riskForEq="false"/>
            </div>
            <div v-else-if="loaded==true">
                <ReferenceARisk v-if="this.prvMtnOp_id!=null" :consultMod="!!isInConsultedMod" :eq_id="this.eq_id"
                                :importedRisk="importedOpRisk" :modifMod="!!this.modifMod" :prvMtnOp_id="this.prvMtnOp_id"
                                :riskForEq="false"/>
            </div>
            <ErrorAlert ref="errorAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputNumberForm from '../../../input/InputNumberForm.vue'
import ReferenceARisk from './ReferenceARisk.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../../button/ReformComponentButton.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import successAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        InputTextAreaForm,
        InputNumberForm,
        ReferenceARisk,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert,
        RadioGroupForm,
        successAlert
    },
    /*--------Declaration of the different props:--------
        number :
        description :
        periodicity :
        symbolPeriodicity :
        protocol :
        validate: Validation option of the preventive maintenance operation
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable the save button and show update button
        divClass: Class name of this preventive maintenance operation form
        id: ID of an already created preventive maintenance operation
        eq_id: ID of the equipment in which the preventive maintenance operation will be added
    ---------------------------------------------------*/
    props: {
        number: {
            type: String,
            default: null
        },
        description: {
            type: String
        },
        periodicity: {
            type: String
        },
        symbolPeriodicity: {
            type: String
        },
        protocol: {
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
        reformDate: {
            type: String,
            default: null
        },
        import_id: {
            type: Number,
            default: null
        },
        reformMod: {
            type: Boolean,
            default: false
        },
        puttingIntoService: {
            type: Boolean,
            default: false
        },
        preventiveOperation: {
            type: Boolean,
            default: false
        },
        typeValidation: {
            type: Boolean,
            default: true
        }
    },
    /*--------Declaration of the different returned data:--------
        prvMtnOp_number:
        prvMtnOp_description:
        prvMtnOp_periodicity:
        prvMtnOp_symbolPeriodicity:
        prvMtnOp_protocol:
        prvMtnOp_validate: Validation option of the preventive maintenance operation
        prvMtnOp_id: ID of this preventive maintenance operation
        equipment_id_add: ID of the equipment in which the preventive maintenance operation will be added
        equipment_id_update: ID of the equipment in which the preventive maintenance operation will be updated
        enum_periodicity_symbol : an enum of the different periodicity symbol
        importedRisk : data of the imported risk if there is one, null otherwise
        errors: Object of errors in which will be stores the different error occurred when adding in database
        addSuccess: Boolean who tell if this preventive maintenance operation has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data() {
        return {
            prvMtnOp_number: this.number,
            prvMtnOp_description: this.description,
            prvMtnOp_periodicity: this.periodicity,
            prvMtnOp_symbolPeriodicity: this.symbolPeriodicity,
            prvMtnOp_protocol: this.protocol,
            prvMtnOp_validate: this.validate,
            prvMtnOp_reformDate: this.reformDate,
            prvMtnOp_id: this.id,
            prvMtnOp_puttingIntoService: this.puttingIntoService,
            prvMtnOp_preventiveOperation: this.preventiveOperation,
            equipment_id_add: this.eq_id,
            equipment_id_update: this.$route.params.id,
            enum_periodicity_symbol: [
                {id_enum: "SymbolPeriodicity", value: 'Y', number: this.prvMtnOp_id},
                {id_enum: "SymbolPeriodicity", value: 'M', number: this.prvMtnOp_id},
                {id_enum: "SymbolPeriodicity", value: 'D', number: this.prvMtnOp_id},
                {id_enum: "SymbolPeriodicity", value: 'H', number: this.prvMtnOp_id},
            ],
            existOptionPIS: [
                {id: 'PIS', value: true, text: 'Yes'},
                {id: 'PIS', value: false, text: 'No'}
            ],
            existOptionPO: [
                {id: 'PO', value: true, text: 'Yes'},
                {id: 'PO', value: false, text: 'No'}
            ],
            typeValidationOption: [
                {id: 'typeValidation', value: true, text: 'Technical'},
                {id: 'typeValidation', value: false, text: 'Quality'}
            ],
            importedOpRisk: [],
            errors: {},
            addSuccess: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInReformMod: this.reformMod,
            infos_prvMtnOp: [],
            true: true,
            false: false,
            SymbolPeriodicity: "SymbolPeriodicity",
        }
    },
    methods: {
        /*Sending to the controller all the information about the mme so that it can be added in the database
         @param savedAs Value of the validation option: drafted, to_be_validated or validated
         @param reason The reason of the modification
         @param lifesheet_created */
        addEquipmentPrvMtnOp(savedAs, reason, lifesheet_created) {
            if (!this.addSuccess) {
                /*ID of the equipment in which the preventive maintenance operation will be added*/
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
                axios.post('/prvMtnOp/verif', {
                    prvMtnOp_description: this.prvMtnOp_description,
                    prvMtnOp_periodicity: parseInt(this.prvMtnOp_periodicity),
                    prvMtnOp_symbolPeriodicity: this.prvMtnOp_symbolPeriodicity,
                    prvMtnOp_protocol: this.prvMtnOp_protocol,
                    prvMtnOp_validate: savedAs,
                    prvMtnOp_puttingIntoService: this.prvMtnOp_puttingIntoService,
                    prvMtnOp_preventiveOperation: this.prvMtnOp_preventiveOperation,
                    typeValidation: this.typeValidation
                }).then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the preventive maintenance operation in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/equipment/add/prvMtnOp', {
                        prvMtnOp_description: this.prvMtnOp_description,
                        prvMtnOp_periodicity: parseInt(this.prvMtnOp_periodicity),
                        prvMtnOp_symbolPeriodicity: this.prvMtnOp_symbolPeriodicity,
                        prvMtnOp_protocol: this.prvMtnOp_protocol,
                        prvMtnOp_validate: savedAs,
                        eq_id: id,
                        prvMtnOp_puttingIntoService: this.prvMtnOp_puttingIntoService,
                        prvMtnOp_preventiveOperation: this.prvMtnOp_preventiveOperation,
                        typeValidation: this.typeValidation

                    })
                        /*If the preventive maintenance operation is added successfully*/
                        .then(response => {
                            /*We test if a life sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifesheet_created == true) {
                                axios.post(`/history/add/equipment/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.successAlert.showAlert(`Equipment preventive maintenance operation added successfully and saved as ${savedAs}`);
                            /*If the user is not in modification mode*/
                            if (!this.modifMod) {
                                /*The form pass in consulting mode and addSuccess pass to True*/
                                this.isInConsultedMod = true;
                                this.addSuccess = true
                            }
                            /*the id of the preventive maintenance operation take the value of the newly created id*/
                            this.prvMtnOp_id = response.data;
                            /*The validate option of this preventive maintenance operation takes the value of savedAs(Params of the function)*/
                            this.prvMtnOp_validate = savedAs;
                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the mme so that it can be added in the database
         @param savedAs Value of the validation option: drafted, to_be_validated or validated
         @param reason The reason of the modification
         @param lifesheet_created */
        updateEquipmentPrvMtnOp(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The type, name, value, unit and validate option are sent to the controller*/
            axios.post('/prvMtnOp/verif', {
                prvMtnOp_description: this.prvMtnOp_description,
                prvMtnOp_periodicity: parseInt(this.prvMtnOp_periodicity),
                prvMtnOp_symbolPeriodicity: this.prvMtnOp_symbolPeriodicity,
                prvMtnOp_protocol: this.prvMtnOp_protocol,
                prvMtnOp_validate: savedAs,
                prvMtnOp_puttingIntoService: this.prvMtnOp_puttingIntoService,
                prvMtnOp_preventiveOperation: this.prvMtnOp_preventiveOperation,
                typeValidation: this.typeValidation
            })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the preventive maintenance operation in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller
                    In the post url the id correspond to the id of the preventive maintenance operation who will be updated*/
                    const consultUrl = (id) => `/equipment/update/prvMtnOp/${id}`;
                    axios.post(consultUrl(this.prvMtnOp_id), {
                        //prvMtnOp_number:this.prvMtnOp_number,
                        prvMtnOp_description: this.prvMtnOp_description,
                        prvMtnOp_periodicity: parseInt(this.prvMtnOp_periodicity),
                        prvMtnOp_symbolPeriodicity: this.prvMtnOp_symbolPeriodicity,
                        prvMtnOp_protocol: this.prvMtnOp_protocol,
                        eq_id: this.equipment_id_update,
                        prvMtnOp_validate: savedAs,
                        prvMtnOp_puttingIntoService: this.prvMtnOp_puttingIntoService,
                        prvMtnOp_preventiveOperation: this.prvMtnOp_preventiveOperation,
                        typeValidation: this.typeValidation
                    }).then(response => {
                        const id = this.equipment_id_update;
                        /*We test if a life sheet has been already created*/
                        /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                        if (lifesheet_created == true) {
                            axios.post(`/history/add/equipment/${id}`, {
                                history_reasonUpdate: reason,
                            });
                            window.location.reload();
                        }
                        this.$refs.successAlert.showAlert(`Equipment preventive maintenance operation updated successfully and saved as ${savedAs}`);
                        this.prvMtnOp_validate = savedAs;
                    }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        updateEnumPeriodicitySymbol(event) {
            this.prvMtnOp_symbolPeriodicity = event.target.value;
        },
        clearRadioError() {
            delete this.errors["prvMtnOp_puttingIntoService"]
            delete this.errors["prvMtnOp_preventiveOperation"]
        },
        /*Function for deleting a preventive maintenance operation from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            /*Emit to the parent component that we want to delete this component*/
            /*If the user is in update mode and the preventive maintenance operation exists in the database*/
            if (this.modifMod == true && this.prvMtnOp_id !== null) {
                const consultUrl = (id) => `/equipment/delete/prvMtnOp/${id}`;
                axios.post(consultUrl(this.prvMtnOp_id), {
                    eq_id: this.equipment_id_update,
                }).then(response => {
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created*/
                    /*If it's the case, we create a new enregistrement of history for saved the reason of the deleted*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    /*Send a post-request with the id of the preventive maintenance operation who will be deleted in the url*/
                    this.$emit('deletePrvMtnOp', '')
                    this.$refs.successAlert.showAlert(`Equipment preventive maintenance operation deleted successfully`);
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deletePrvMtnOp', '')
                this.$refs.successAlert.showAlert(`Empty Equipment preventive maintenance operation deleted successfully`);
            }
        },
        reformComponent(endDate) {
            if (this.$userId.user_makeReformRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            /*If the user is in update mode and the usage exists in the database,
            we Send a post-request with the id of the usage who will be deleted in the url*/
            const consultUrl = (id) => `/equipment/reform/prvMtnOp/${id}`;
            axios.post(consultUrl(this.prvMtnOp_id), {
                eq_id: this.equipment_id_update,
                prvMtnOp_reformDate: endDate
            }).then(response => {
                /*Emit to the parent component that we want to delete this component*/
                this.$emit('deletePrvMtnOp', '')
            }).catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.errors['prvMtnOp_reformDate'])
            });
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
    },
    created() {
        if (this.prvMtnOp_id !== null && this.addSuccess == false) {
            /*Make a get request to ask the controller the preventive maintenance operation corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/prvMtnOp/risk/send/${id}`;
            axios.get(consultUrl(this.prvMtnOp_id))
                .then(response => {
                    this.importedOpRisk = response.data;
                    axios.get('/info/send/preventiveMaintenanceOperation')
                        .then(response => {
                            this.infos_prvMtnOp = response.data;
                            this.loaded = true;
                        }).catch(error => {
                    });
                }).catch(error => {
            });
        }
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

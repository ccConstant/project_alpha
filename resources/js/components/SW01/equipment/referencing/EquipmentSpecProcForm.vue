<!--File name : EquipmentSpecProcForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the Form of the equipment special process who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <RadioGroupForm v-model="spProc_exist" :Errors="errors.spProc_exist"
                                :checkedOption="spProc_exist" :info_text="infos_spProc[0].info_value" :isDisabled="!!isInConsultedMod"
                                :options="existOption" label="ALPHA's Cat I processes?"
                                @clearRadioError="clearRadioError"/>
                <InputTextForm v-if="this.spProc_exist==true" v-model="spProc_name"
                               :Errors="errors.spProc_name" :info_text="infos_spProc[1].info_value" :isDisabled="!!isInConsultedMod"
                               inputClassName="form-control w-50" label="Special process name :"
                               name="spProc_name"/>
                <InputTextAreaForm v-model="spProc_remarksOrPrecaution" :Errors="errors.spProc_remarksOrPrecaution"
                                   :info_text="infos_spProc[2].info_value" :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50"
                                   label="Remarks :" name="spProc_remarksOrPrecaution"/>
                <!--If addSuccess is equal to false, the buttons appear -->
                <div v-if="this.addSuccess==false ">
                    <!--If this special process doesn't have a id the addEquipmentSpProc is called function else the updateEquipmentSpProc function is called -->
                    <div v-if="this.spProc_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="spProc_validate" @add="addEquipmentSpProc"
                                            @update="updateEquipmentSpProc"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="spProc_validate"
                                            @add="addEquipmentSpProc" @update="updateEquipmentSpProc"/>
                        </div>
                    </div>
                    <div v-else-if="this.spProc_id!==null">
                        <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                        :savedAs="spProc_validate" @add="addEquipmentSpProc"
                                        @update="updateEquipmentSpProc"/>
                    </div>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        RadioGroupForm,
        InputTextAreaForm,
        SaveButtonForm,
        InputTextForm,
        SucessAlert

    },
    /*--------Declaration of the different props:--------
        remarksOrPrecaution : Note or precaution related to the special process given by the database, we will put this data in the corresponding field as default value
        exist : Boolean if this special process is duplicated given by the database we will put this data in the corresponding field as default value
        name : Name of this special process given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the special process
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this special process form
        id: ID of an already created special process
        eq_id: ID of the equipment in which the special process will be added
    ---------------------------------------------------*/
    props: {
        remarksOrPrecaution: {
            type: String
        },
        name: {
            type: String,
        },
        exist: {
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
        eq_id: {
            type: Number
        }

    },
    /*--------Declaration of the different returned data:--------
        spProc_remarksOrPrecaution:
        spProc_exist:
        spProc_name :
        spProc_validate: Validation option of the special process
        spProc_id: ID oh this special process
        equipment_id_add: ID of the equipment in which the special process will be added
        equipment_id_update: ID of the equipment in which the special process will be updated
        existOption : Different exist option with their id and value
        errors: Object of errors in which will be stores the different error occurred when adding in database
        addSuccess: Boolean who tell if this special process has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data() {
        return {
            spProc_remarksOrPrecaution: this.remarksOrPrecaution,
            spProc_exist: this.exist,
            spProc_name: this.name,
            spProc_validate: this.validate,
            spProc_id: this.id,
            equipment_id_add: this.eq_id,
            equipment_id_update: this.$route.params.id,
            existOption: [
                {id: 'eq_specProcExist', value: true, text: 'Yes'},
                {id: 'eq_specProcExist', value: false, text: 'No'}
            ],
            errors: {},
            addSuccess: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            infos_spProc: []
        }
    },
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be added in the database
         @param savedAs Value of the validation option: drafted, to_be_validated or validated
         @param reason The reason of the modification
         @param lifesheet_created */
        addEquipmentSpProc(savedAs) {
            if (!this.addSuccess) {
                /*ID of the equipment in which the special process will be added*/
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
                axios.post('/spProc/verif', {
                    spProc_remarksOrPrecaution: this.spProc_remarksOrPrecaution,
                    spProc_exist: this.spProc_exist,
                    spProc_name: this.spProc_name,
                    spProc_validate: savedAs,
                }).then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the special process in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/equipment/add/spProc', {
                        spProc_remarksOrPrecaution: this.spProc_remarksOrPrecaution,
                        spProc_exist: this.spProc_exist,
                        spProc_name: this.spProc_name,
                        spProc_validate: savedAs,
                        eq_id: id
                    })
                        /*If the special process is added successfully*/
                        .then(response => {
                            this.$refs.sucessAlert.showAlert(`Equipment special process added successfully and saved as ${savedAs}`);
                            /*If the user is not in modification mode*/
                            if (!this.modifMod) {
                                /*The form pass in consulting mode and addSuccess pass to True*/
                                this.isInConsultedMod = true;
                                this.addSuccess = true
                            }
                            /*the id of the special process take the value of the newly created id*/
                            this.spProc_id = response.data;
                            /*The validate option of this special process takes the value of savedAs*/
                            this.spProc_validate = savedAs;
                        }).catch(error => this.errors = error.response.data.errors);
                }).catch(error => this.errors = error.response.data.errors);
            }

        },
        /*Sending to the controller all the information about the equipment so that it can be added in the database
          @param savedAs Value of the validation option: drafted, to_be_validated or validated
          @param reason The reason of the modification
          @param lifesheet_created */
        updateEquipmentSpProc(savedAs, reason, lifesheet_created) {

            /*The First post to verify if all the fields are filled correctly,
            The type, name, value, unit and validate option are sent to the controller*/
            axios.post('/spProc/verif', {
                spProc_remarksOrPrecaution: this.spProc_remarksOrPrecaution,
                spProc_exist: this.spProc_exist,
                spProc_name: this.spProc_name,
                spProc_validate: savedAs,
            }).then(response => {
                this.errors = {};
                /*If all the verifications passed, a new post this time to add the special process in the database
                The type, name, value, unit, validate option and id of the equipment are sent to the controller
                In the post url the id correspond to the id of the special process who will be updated*/
                const consultUrl = (id) => `/equipment/update/spProc/${id}`;
                axios.post(consultUrl(this.spProc_id), {
                    spProc_remarksOrPrecaution: this.spProc_remarksOrPrecaution,
                    spProc_exist: this.spProc_exist,
                    spProc_name: this.spProc_name,
                    spProc_validate: savedAs,
                    eq_id: this.equipment_id_update
                }).then(response => {
                    this.spProc_validate = savedAs;
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created
                    If it's the case we create a new enregistrement of history for saved the reason of the update*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.sucessAlert.showAlert(`Equipment special process updated successfully and saved as ${savedAs}`);
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        clearRadioError() {
            delete this.errors["spProc_exist"]
        }
    },
    created() {
        axios.get('/info/send/specialProcess')
            .then(response => {
                this.infos_spProc = response.data;
                this.loaded = true;
            }).catch(error => {
        });
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

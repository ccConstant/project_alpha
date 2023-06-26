<!--File name : EquipmentDimForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the Form of the equipment dimension who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputSelectForm v-model="dim_type" :Errors="errors.dim_type"
                                 :id_actual="dimensionType" :info_text="infos_dimension[0].info_value" :isDisabled="!!isInConsultedMod"
                                 :options="enum_dim_type" :selctedOption="this.dim_type"
                                 :selectedDivName="this.divClass" label="Dimension Type :" name="dim_type"
                                 selectClassName="form-select w-50" @clearSelectError='clearSelectError'/>
                <InputSelectForm v-model="dim_name" :Errors="errors.dim_name" :id_actual="dimensionName"
                                 :info_text="infos_dimension[1].info_value" :isDisabled="!!isInConsultedMod" :options="enum_dim_name"
                                 :selctedOption="this.dim_name" :selectedDivName="this.divClass"
                                 label="Dimension name :" name="dim_name"
                                 selectClassName="form-select w-50" @clearSelectError='clearSelectError'/>
                <InputTextForm v-model="dim_value" :Errors="errors.dim_value" :info_text="infos_dimension[2].info_value"
                               :isDisabled="!!isInConsultedMod" inputClassName="form-control w-50" label="Dimension value :"
                               name="dim_value"/>
                <InputSelectForm v-model="dim_unit" :Errors="errors.dim_unit" :id_actual="dimensionUnit"
                                 :info_text="infos_dimension[3].info_value"
                                 :isDisabled="!!isInConsultedMod" :options="enum_dim_unit" :selctedOption="this.dim_unit"
                                 :selectedDivName="this.divClass" label="Unit :"
                                 name="dim_unit" selectClassName="form-select w-50"
                                 @clearSelectError='clearSelectError'/>
                <!--If addSuccess is set on false, the buttons are show -->
                <div v-if="this.addSuccess==false ">
                    <div v-if="this.dim_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm :AddinUpdate="true" :consultMod="this.isInConsultedMod"
                                            :savedAs="dim_validate" @add="addEquipmentDim"
                                            @update="updateEquipmentDim"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm :consultMod="this.isInConsultedMod" :savedAs="dim_validate"
                                            @add="addEquipmentDim" @update="updateEquipmentDim"/>
                        </div>
                    </div>
                    <div v-else-if="this.dim_id!==null">
                        <SaveButtonForm :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                        :savedAs="dim_validate" @add="addEquipmentDim"
                                        @update="updateEquipmentDim"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :consultMod="this.isInConsultedMod" :validationMode="dim_validate"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <SuccessAlert ref="successAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SuccessAlert
    },
    /*--------Declaration of the different props:--------
        type : Dimension type given by the database we will put this data in the corresponding field as default value
        name : Dimension name reference given by the database we will put this data in the corresponding field as default value;
        value : Dimension value given by the database we will put this data in the corresponding field as default value
        unit : Dimension unit given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the dimension
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this Dimension form
        id: ID of an already created Dimension
        eq_id: ID of the equipment in which the dimension will be added
    ---------------------------------------------------*/
    props: {
        type: {
            type: String
        },
        name: {
            type: String
        },
        value: {
            type: String
        },
        unit: {
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
        }
    },
    /*--------Declaration of the different returned data:--------
        dim_type: Type of the dimension who  will be appear in the field and updated dynamically
        dim_name: Name of the dimension who  will be appear in the field and updated dynamically
        dim_value: Value of the dimension who  will be appear in the field and updated dynamically
        dim_unit: Unit of the dimension who  will be appear in the field and updated dynamically
        dim_validate: Validation option of the dimensions
        dim_id: Id oh this dimension
        equipment_id_add: Id of the equipment in which the dimensions will be added
        equipment_id_update: Id of the equipment in which the dimensions will be updated
        enum_dim_type : Different types of dimension gets from the database
        enum_dim_name : Different names of dimension gets from the database
        enum_dim_unit : Different unites of dimension gets from the database
        errors: Object of errors in which will be stores the different error occurred when adding in database
        addSuccess: Boolean who tell if this dimension has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data() {
        return {
            dim_type: this.type,
            dim_name: this.name,
            dim_value: this.value,
            dim_unit: this.unit,
            dim_validate: this.validate,
            dim_id: this.id,
            equipment_id_add: this.eq_id,
            equipment_id_update: this.$route.params.id,
            enum_dim_type: [],
            enum_dim_name: [],
            enum_dim_unit: [],
            errors: {},
            addSuccess: false,
            isInConsultedMod: this.consultMod,
            infos_dimension: [],
            loaded: false,
            dimensionName: "DimensionName",
            dimensionType: "DimensionType",
            dimensionUnit: "DimensionUnit",
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    created() {
        /*Ask for the controller different types of the dimension  */
        axios.get('/dimension/enum/type')
            .then(response => {
                this.enum_dim_type = response.data;
                /*Ask for the controller different names of the dimension  */
                axios.get('/dimension/enum/name')
                    .then(response => {
                        this.enum_dim_name = response.data;
                        /*Ask for the controller different units of the dimension  */
                        axios.get('/dimension/enum/unit')
                            .then(response => {
                                this.enum_dim_unit = response.data;
                                /* Ask for the data of one dimension  */
                                axios.get('/info/send/dimension')
                                    .then(response => {
                                        this.infos_dimension = response.data;
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
    methods: {
        /* Sending to the controller all the information about the equipment so that it can be added to the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addEquipmentDim(savedAs, reason, lifesheet_created) {
            if (!this.addSuccess) {
                /*ID of the equipment in which the dimensions will be added*/
                let id;
                /*If the user is not in the update menu, we set the id with the value of data equipment_id_add*/
                if (!this.modifMod) {
                    id = this.equipment_id_add
                    /*Else the user is not in the update menu, we set the id with the value of data equipment_id_update*/
                } else {
                    id = this.equipment_id_update;
                }
                /*The First post to verify if all the fields are filled correctly,
                the type, name, value, unit and validate option are sent to the controller*/
                axios.post('/dimension/verif', {
                    dim_type: this.dim_type,
                    dim_name: this.dim_name,
                    dim_value: this.dim_value,
                    dim_unit: this.dim_unit,
                    dim_validate: savedAs,
                })
                    .then(response => {
                        this.errors = {};
                        /*If all the verification passed, a new post this time to add the dimension in the database,
                        the type, name, value, unit, validate option and id of the equipment is sent to the controller*/
                        axios.post('/equipment/add/dim', {
                            dim_type: this.dim_type,
                            dim_name: this.dim_name,
                            dim_value: this.dim_value,
                            dim_unit: this.dim_unit,
                            dim_validate: savedAs,
                            eq_id: id
                        })
                            /*If the dimension is added successfully*/
                            .then(response => {
                                /*We test if a life sheet has been already created*/
                                /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                                if (lifesheet_created == true) {
                                    axios.post(`/history/add/equipment/${id}`, {
                                        history_reasonUpdate: reason,
                                    });
                                    window.location.reload();
                                }
                                this.$refs.successAlert.showAlert(`Equipment dimension added successfully and saved as ${savedAs}`);
                                /*If the user is not in modification mode*/
                                if (!this.modifMod) {
                                    /*The form pass in consulting mode and addSuccess pass to True*/
                                    this.isInConsultedMod = true;
                                    this.addSuccess = true
                                }
                                /*the id of the dimensions take the value of the newly created id*/
                                this.dim_id = response.data.dim_id;
                                /*The validate option of this dimension takes the value of savedAs*/
                                this.dim_validate = savedAs;
                            }).catch(error => this.errors = error.response.data.errors);
                    }).catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateEquipmentDim(savedAs, reason, lifesheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The type, name, value, unit and validate option is sent to the controller*/
            axios.post('/dimension/verif', {
                dim_type: this.dim_type,
                dim_name: this.dim_name,
                dim_value: this.dim_value,
                dim_unit: this.dim_unit,
                dim_validate: savedAs,
            }).then(response => {
                this.errors = {};
                /*If all the verification passed, a new post this time to add the dimension in the database
                    The type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the dimension who will be updated*/
                const consultUrl = (id) => `/equipment/update/dim/${id}`;
                axios.post(consultUrl(this.dim_id), {
                    dim_type: this.dim_type,
                    dim_name: this.dim_name,
                    dim_value: this.dim_value,
                    dim_unit: this.dim_unit,
                    eq_id: this.equipment_id_update,
                    dim_validate: savedAs
                }).then(response => {
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created*/
                    /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        }).then(response => {
                        });
                        window.location.reload();
                    }
                    this.$refs.successAlert.showAlert(`Equipment dimension updated successfully and saved as ${savedAs}`);
                    this.dim_validate = savedAs;
                }).catch(error => this.errors = error.response.data.errors);
            }).catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a dimension from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            /*If the user is in update mode and the dimension exist in the database*/
            if (this.modifMod == true && this.dim_id !== null) {
                /*Send a post-request with the id of the dimension who will be deleted in the url*/
                const consultUrl = (id) => `/equipment/delete/dim/${id}`;
                axios.post(consultUrl(this.dim_id), {
                    eq_id: this.equipment_id_update
                }).then(response => {
                    const id = this.equipment_id_update;
                    /*We test if a life sheet has been already created*/
                    /*If it's the case we create a new enregistrement of history for saved the reason of the deleting*/
                    if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.successAlert.showAlert(`Equipment dimension deleted successfully`);
                    /*Emit to the parent component that we want to delete this component*/
                    this.$emit('deleteDim', '')
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteDim', '')
                this.$refs.successAlert.showAlert(`Empty Equipment dimension deleted successfully`);
            }
        },
        clearSelectError(value) {
            delete this.errors[value];
        }
    },
}
</script>

<style lang="scss">
.titleForm {
    padding-left: 10px;
}

form {
    margin: 20px;
    margin-bottom: 100px;
}
</style>

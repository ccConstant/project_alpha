<!--File name : EquipmentFileForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 4 Apr 2023-->
<!--Vue Component of the Form of the equipment file who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" @keydown="clearError">
                <!--<InputSelectForm
                    v-if="this.articleType!=='cons'"
                    :name="'Criticality'"
                    :label="this.articleType.toUpperCase() + ' Criticality :'"
                    isRequired
                    :options="[
                        {id_enum: 'Criticality', value: 'NOT_CRITICAL', text: 'NOT_CRITICAL'},
                        {id_enum: 'Criticality', value: 'DETECTABLE', text: 'DETECTABLE'},
                        {id_enum: 'Criticality', value: 'CRITICAL', text: 'CRITICAL'},
                    ]"
                    :isDisabled="this.isInConsultMod"
                    v-model="crit_artCriticality"
                    :info_text="this.infos_crit[0].info_value"
                    :Errors="errors.crit_artCriticality"
                    :selctedOption="crit_artCriticality"
                    :id_actual="'Criticality'"
                />
                <InputSelectForm
                    :name="'MaterialContactCriticality'"
                    :label="this.articleType.toUpperCase() + ' Material Contact Criticality :'"
                    isRequired
                    :options="[
                        {id_enum: 'MaterialContactCriticality', value: 'NOT_CRITICAL', text: 'NOT_CRITICAL'},
                        {id_enum: 'MaterialContactCriticality', value: 'DETECTABLE', text: 'DETECTABLE'},
                        {id_enum: 'MaterialContactCriticality', value: 'CRITICAL', text: 'CRITICAL'},
                    ]"
                    :isDisabled="this.isInConsultMod"
                    v-model="crit_artMaterialContactCriticality"
                    :info_text="this.infos_crit[1].info_value"
                    :Errors="errors.crit_artMaterialContactCriticality"
                    :selctedOption="crit_artMaterialContactCriticality"
                    :id_actual="'MaterialContactCriticality'"
                />
                <InputSelectForm
                    :name="'MaterialFunctionCriticality'"
                    :label="this.articleType.toUpperCase() + ' Material Function Criticality :'"
                    isRequired
                    :options="[
                        {id_enum: 'MaterialFunctionCriticality', value: 'NOT_CRITICAL', text: 'NOT_CRITICAL'},
                        {id_enum: 'MaterialFunctionCriticality', value: 'DETECTABLE', text: 'DETECTABLE'},
                        {id_enum: 'MaterialFunctionCriticality', value: 'CRITICAL', text: 'CRITICAL'},
                    ]"
                    :isDisabled="this.isInConsultMod"
                    v-model="crit_artMaterialFunctionCriticality"
                    :info_text="this.infos_crit[2].info_value"
                    :Errors="errors.crit_artMaterialFunctionCriticality"
                    :selctedOption="crit_artMaterialFunctionCriticality"
                    :id_actual="'MaterialFunctionCriticality'"
                />
                <InputSelectForm
                    v-if="this.articleType!=='cons'"
                    :name="'ProcessCriticality'"
                    :label="this.articleType.toUpperCase() + ' Process Criticality :'"
                    isRequired
                    :options="[
                        {id_enum: 'ProcessCriticality', value: 'NOT_CRITICAL', text: 'NOT_CRITICAL'},
                        {id_enum: 'ProcessCriticality', value: 'DETECTABLE', text: 'DETECTABLE'},
                        {id_enum: 'ProcessCriticality', value: 'CRITICAL', text: 'CRITICAL'},
                    ]"
                    :isDisabled="this.isInConsultMod"
                    v-model="crit_artProcessCriticality"
                    :info_text="this.infos_crit[3].info_value"
                    :Errors="errors.crit_artProcessCriticality"
                    :selctedOption="crit_artProcessCriticality"
                    :id_actual="'ProcessCriticality'"
                />
                <InputTextForm
                    name="Justification"
                    label="Justification :"
                    v-model="crit_justification"
                    :isDisabled="isInConsultMod"
                    :info_text="null"
                    :min="0"
                    :max="255"
                    :inputClassName="null"
                    :Errors="errors.crit_justification"
                />-->
                <InputSelectForm
                    :name="ArticleContact"
                    :label="'What is the contact of the article with the patient or the user?'"
                    isRequired
                    :options="[
                        {id_enum: 'Criticality', value: 'No direct contact', number: this.crit_id},
                        {id_enum: 'Criticality', value: 'No contact but integrated in invasive Medical device', number: this.crit_id},
                        {id_enum: 'Criticality', value: 'Surface contact', number: this.crit_id},
                        {id_enum: 'Criticality', value: 'Invasive/Implantable', number: this.crit_id},
                    ]"
                    :isDisabled="this.isInConsultMod"
                    v-model="crit_artCriticality"
                    :info_text="this.infos_crit[0].info_value"
                    :Errors="errors.crit_artCriticality"
                    :selctedOption="crit_artCriticality"
                    :id_actual="'Criticality'"
                />
                <RadioGroupForm v-model="crit_performanceMedicalDevice" :Errors="errors.crit_performanceMedicalDevice"
                                :checkedOption="crit_performanceMedicalDevice"
                                :info_text="null" :isDisabled="!!isInConsultMod"
                                :options="PerformanceOption"
                                label="Is deficiencies of the article or of the material properties (hardness, grain size...) impact the performance of the medical device ?"
                                name="crit_performanceMedicalDevice"
                />
                <div v-if="this.crit_performanceMedicalDevice">
                    <div>Checked names: {{ checkedTests }}</div>

                    <input type="checkbox" id="dimTest" value="dimTest" v-model="checkedTests">
                    <label for="dimTest">Dimensional Test</label>

                    <div v-if="checkedTests.includes('dimTest')">
                        <input type="checkbox" id="dimTestAlpha" value="dimTestAlpha" v-model="checkedTests">
                        <label for="dimTestAlpha"> Alpha</label>

                        <input type="checkbox" id="dimTestSupplier" value="dimTestSupplier" v-model="checkedTests">
                        <label for="dimTestSupplier"> Supplier</label>

                        <input type="checkbox" id="dimTestBoth" value="dimTestBoth" v-model="checkedTests">
                        <label for="dimTestBoth"> Both</label>
                    </div>

                    <input type="checkbox" id="funcTest" value="funcTest" v-model="checkedTests">
                    <label for="funcTest">Functional Test</label>

                    <div v-if="checkedTests.includes('funcTest')">
                        <input type="checkbox" id="funcTestAlpha" value="funcTestAlpha" v-model="checkedTests">
                        <label for="funcTestAlpha"> Alpha</label>

                        <input type="checkbox" id="funcTestSupplier" value="funcTestSupplier" v-model="checkedTests">
                        <label for="funcTestSupplier"> Supplier</label>

                        <input type="checkbox" id="funcTestBoth" value="funcTestBoth" v-model="checkedTests">
                        <label for="funcTestBoth"> Both</label>
                    </div>

                    <input type="checkbox" id="aspTest" value="aspTest" v-model="checkedTests">
                    <label for="aspTest">Aspect Test </label>

                    <div v-if="checkedTests.includes('aspTest')">
                        <input type="checkbox" id="aspTestAlpha" value="aspTestAlpha" v-model="checkedTests">
                        <label for="aspTestAlpha"> Alpha</label>

                        <input type="checkbox" id="aspTestSupplier" value="aspTestSupplier" v-model="checkedTests">
                        <label for="aspTestSupplier"> Supplier</label>

                        <input type="checkbox" id="aspTestBoth" value="aspTestBoth" v-model="checkedTests">
                        <label for="aspTestBoth"> Both</label>
                    </div>

                    <input type="checkbox" id="docControl" value="docControl" v-model="checkedTests">
                    <label for="docControl">Documentary control </label>

                    <div v-if="checkedTests.includes('docControl')">
                        <input type="checkbox" id="docControlAlpha" value="docControlAlpha" v-model="checkedTests">
                        <label for="docControlAlpha"> Alpha</label>

                        <input type="checkbox" id="docControlSupplier" value="docControlSupplier" v-model="checkedTests">
                        <label for="docControlSupplier"> Supplier</label>

                        <input type="checkbox" id="docControlBoth" value="docControlBoth" v-model="checkedTests">
                        <label for="docControlBoth"> Both</label>
                    </div>

                    
                    <input type="checkbox" id="adminControl" value="adminControl" v-model="checkedTests">
                    <label for="adminControl">Administrative control </label>
                    <div v-if="checkedTests.includes('adminControl')">
                        <input type="checkbox" id="adminControlAlpha" value="adminControlAlpha" v-model="checkedTests">
                        <label for="adminControlAlpha"> Alpha</label>

                        <input type="checkbox" id="adminControlSupplier" value="adminControlSupplier" v-model="checkedTests">
                        <label for="adminControlSupplier"> Supplier</label>

                        <input type="checkbox" id="adminControlBoth" value="adminControlBoth" v-model="checkedTests">
                        <label for="adminControlBoth"> Both</label>
                    </div>
                </div>
                <InputTextForm
                    name="Remarks"
                    label="Remarks :"
                    v-model="crit_remarks"
                    :isDisabled="isInConsultMod"
                    :info_text="this.infos_crit[4].info_value"
                    :min="0"
                    :max="255"
                    :inputClassName="null"
                    :Errors="errors.crit_remarks"
                />
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this file doesn't have a id the addCriticality is called function else the updateCriticality function is called -->
                    <div v-if="this.crit_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addCriticality" @update="updateCriticality"
                                            :consultMod="this.isInConsultMod" :savedAs="crit_validate"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addCriticality" @update="updateCriticality"
                                            :consultMod="this.isInConsultMod" :savedAs="crit_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.crit_id!==null">
                        <SaveButtonForm @add="addCriticality" @update="updateCriticality"
                                        :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                                        :savedAs="crit_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="crit_validate" :consultMod="this.isInConsultMod"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'
import InputSelectForm from "../../../input/InputSelectForm.vue";
import InputNumberForm from "../../../input/SW03/InputNumberForm.vue";
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputNumberForm,
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        RadioGroupForm
    },
    /*--------Declaration of the different props:--------
        name : File name given by the database we will put this data in the corresponding field as default value
        location : File location given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the file
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this file form
        id: ID of an already created file
        article_id: ID of the equipment in which the file will be added
    ---------------------------------------------------*/
    props: {
        artCriticality: {
            type: String,
        },
        artMaterialContactCriticality: {
            type: String,
        },
        artMaterialFunctionCriticality: {
            type: String,
        },
        artProcessCriticality: {
            type: String,
        },
        justification: {
            type: String,
        },
        remarks: {
            type: String,
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
        articleID: {
            type: Number,
            default: null
        },
        articleType: {
            type: String,
            default: null
        },
        validate: {
            type: String
        },
        critPerformance:{
            type: Boolean,
            default: false
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
    isInConsultMod: data of the consultMod prop
-----------------------------------------------------------*/
    data() {
        return {
            crit_id: this.id,
            crit_artMaterialContactCriticality: this.artMaterialContactCriticality,
            crit_artMaterialFunctionCriticality: this.artMaterialFunctionCriticality,
            crit_artProcessCriticality: this.artProcessCriticality,
            crit_artCriticality: this.artCriticality,
            crit_justification: this.justification,
            crit_remarks: this.remarks,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            crit_validate: this.validate,
            infos_crit: null,
            PerformanceOption:[
                {id_enum: 'Performance', value: true, text: 'Yes'},
                {id_enum: 'Performance', value: false, text: 'No'},
            ],
            checkedTests: [],
            crit_performanceMedicalDevice: null,
        }
    },
    methods: {
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addCriticality(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                /*The First post to verify if all the fields are filled correctly
                Name, location and validate option is sent to the controller*/
                console.log("addCriticality");
                console.log("crit_artCriticality : " + this.crit_artCriticality);
                console.log("crit_artMaterialContactCriticality : " + this.crit_artMaterialContactCriticality);
                console.log("crit_artMaterialFunctionCriticality : " + this.crit_artMaterialFunctionCriticality);
                console.log("crit_artProcessCriticality : " + this.crit_artProcessCriticality);
                console.log("crit_remarks : " + this.crit_remarks);
                axios.post('/artFam/criticality/verif', {
                    crit_artCriticality: this.crit_artCriticality,
                    crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                    crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                    crit_artProcessCriticality: this.crit_artProcessCriticality,
                    crit_remarks: this.crit_remarks,
                    crit_validate: savedAs,
                    crit_articleType: this.data_article_type,
                    crit_articleID: this.data_article_id,
                    crit_justification: this.crit_justification,
                })
                .then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                    axios.post('/artFam/criticality/add', {
                        crit_artCriticality: this.crit_artCriticality,
                        crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                        crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                        crit_artProcessCriticality: this.crit_artProcessCriticality,
                        crit_remarks: this.crit_remarks,
                        crit_validate: savedAs,
                        crit_articleType: this.data_article_type,
                        crit_articleID: this.data_article_id,
                        crit_justification: this.crit_justification,
                    }).then(response => {
                        this.$snotify.success(`Criticality added successfully and saved as ${savedAs}`);
                        this.isInConsultMod = true;
                        this.addSucces = true
                    }).catch(error => {
                        this.errors = error.response.data.errors;
                        console.log(error.response.data);
                    });
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    console.log(error.response.data);
                });
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateCriticality(savedAs, reason, lifesheet_created) { // TODO: update
            axios.post('/artFam/criticality/verif', {
                crit_artCriticality: this.crit_artCriticality,
                crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                crit_artProcessCriticality: this.crit_artProcessCriticality,
                crit_remarks: this.crit_remarks,
                crit_validate: savedAs,
                crit_articleType: this.data_article_type,
                crit_articleID: this.data_article_id,
                crit_justification: this.crit_justification,
            }).then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    Type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                    const consultUrl = (id) => `/artFam/criticality/update/${id}`;
                    axios.post(consultUrl(this.crit_id), {
                        crit_artCriticality: this.crit_artCriticality,
                        crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                        crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                        crit_artProcessCriticality: this.crit_artProcessCriticality,
                        crit_remarks: this.crit_remarks,
                        crit_validate: savedAs,
                        crit_articleType: this.data_article_type,
                        crit_articleID: this.data_article_id,
                        crit_justification: this.crit_justification,
                    }).then(response => {
                            this.crit_validate = savedAs;
                            /*We test if a life sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (lifesheet_created == true) {
                                axios.post('/artFam/history/add/' + this.data_article_type.toLowerCase() + '/' + this.data_article_id, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Criticality updated successfully and saved as ${savedAs}`);
                            this.isInConsultMod = true;
                        }).catch(error => {
                        this.errors = error.response.data.errors;
                        console.log(error.response.data);
                        });
                }).catch(error => {
                this.errors = error.response.data.errors;
                console.log(error.response.data);
            });
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        /*Function for deleting a file from the view and the database*/
        deleteComponent(reason, lifesheet_created) {
            this.$emit('deleteCrit', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/crit')
            .then(response => {
                this.infos_crit = response.data;
                this.loaded = true;

            });
    }
}
</script>

<style>
</style>

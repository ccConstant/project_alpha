<!--File name : EquipmentFileForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 10 Jul 2023-->
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
                    :name="crit_artCriticality"
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
                    <table class="table">
                        <tr>
                            <td>
                                <input type="checkbox" id="dimTest" value="dimTest" v-model="checkedTests">
                                <label for="dimTest">Dimensional Test</label>
                            </td>
                            <td v-if="checkedTests.includes('dimTest')">
                                <input type="radio" id="dimTestAlpha" value="dimTestAlpha" v-model="checkedTestRadioDim">
                                <label for="dimTestAlpha"> Alpha</label>

                                <input type="radio" id="dimTestSupplier" value="dimTestSupplier" v-model="checkedTestRadioDim">
                                <label for="dimTestSupplier"> Supplier</label>

                                <input type="radio" id="dimTestBoth" value="dimTestBoth" v-model="checkedTestRadioDim">
                                <label for="dimTestBoth"> Both</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id="funcTest" value="funcTest" v-model="checkedTests">
                                <label for="funcTest">Functional Test</label>
                            </td>
                            <td v-if="checkedTests.includes('funcTest')">
                                <input type="radio" id="funcTestAlpha" value="funcTestAlpha" v-model="checkedTestRadioFunc">
                                <label for="funcTestAlpha"> Alpha</label>

                                <input type="radio" id="funcTestSupplier" value="funcTestSupplier" v-model="checkedTestRadioFunc">
                                <label for="funcTestSupplier"> Supplier</label>

                                <input type="radio" id="funcTestBoth" value="funcTestBoth" v-model="checkedTestRadioFunc">
                                <label for="funcTestBoth"> Both</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id="aspTest" value="aspTest" v-model="checkedTests">
                                <label for="aspTest">Aspect Test </label>
                            </td>
                            <td v-if="checkedTests.includes('aspTest')">
                                <input type="radio" id="aspTestAlpha" value="aspTestAlpha" v-model="checkedTestRadioAsp">
                                <label for="aspTestAlpha"> Alpha</label>
                                <input type="radio" id="aspTestSupplier" value="aspTestSupplier" v-model="checkedTestRadioAsp">
                                <label for="aspTestSupplier"> Supplier</label>
                                <input type="radio" id="aspTestBoth" value="aspTestBoth" v-model="checkedTestRadioAsp">
                                <label for="aspTestBoth"> Both</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id="docControl" value="docControl" v-model="checkedTests">
                                <label for="docControl">Documentary control </label>
                            </td>
                            <td v-if="checkedTests.includes('docControl')">
                                <input type="radio" id="docControlAlpha" value="docControlAlpha" v-model="checkedTestRadioDoc">
                                <label for="docControlAlpha"> Alpha</label>

                                <input type="radio" id="docControlSupplier" value="docControlSupplier" v-model="checkedTestRadioDoc">
                                <label for="docControlSupplier"> Supplier</label>

                                <input type="radio" id="docControlBoth" value="docControlBoth" v-model="checkedTestRadioDoc">
                                <label for="docControlBoth"> Both</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id="adminControl" value="adminControl" v-model="checkedTests">
                                <label for="adminControl">Administrative control </label>
                            </td>
                            <td v-if="checkedTests.includes('adminControl')">
                                <input type="radio" id="adminControlAlpha" value="adminControlAlpha" v-model="checkedTestRadioAdm">
                                <label for="adminControlAlpha"> Alpha</label>

                                <input type="radio" id="adminControlSupplier" value="adminControlSupplier" v-model="checkedTestRadioAdm">
                                <label for="adminControlSupplier"> Supplier</label>

                                <input type="radio" id="adminControlBoth" value="adminControlBoth" v-model="checkedTestRadioAdm">
                                <label for="adminControlBoth"> Both</label>
                            </td>
                        </tr>
                    </table>
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
import FuncTestIDForm from "../../incInsp/referencing/FuncTestIDForm.vue";
import DimTestIDForm from "../../incInsp/referencing/DimTestIDForm.vue";
import AspTestIDForm from "../../incInsp/referencing/AspTestIDForm.vue";
import DocControlIDForm from "../../incInsp/referencing/DocControlIDForm.vue";
import AdminControlIDForm from "../../incInsp/referencing/AdminControlIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputNumberForm,
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        RadioGroupForm,
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
        performanceMedicalDevice: {
            type: Boolean,
            default:false,
        },
        data_checkedTests: {
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
        data_checkedTestRadioFunc:{
            type: String,
            default: 'funcTestAlpha',
        },
        data_checkedTestRadioAsp:{
            type: String,
            default: 'aspTestAlpha',
        },
        data_checkedTestRadioDoc:{
            type: String,
            default: 'docControlAlpha',
        },
        data_checkedTestRadioAdm:{
            type: String,
            default: 'adminControlAlpha',
        },
        data_checkedTestRadioDim:{
            type: String,
            default: 'dimTestAlpha',
        },
        artSubFam_id: {
            type: Number
        },
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
            crit_artCriticality: this.artCriticality,
            crit_remarks: this.remarks,
            errors: {},
            components: [],
            addSucces: false,
            isInConsultMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_subFam_id: this.artSubFam_id,
            data_article_type: this.articleType.toLowerCase(),
            crit_validate: this.validate,
            infos_crit: null,
            PerformanceOption:[
                {id_enum: 'Performance', value: true, text: 'Yes'},
                {id_enum: 'Performance', value: false, text: 'No'},
            ],
            crit_performanceMedicalDevice: this.performanceMedicalDevice,
            checkedTestRadioDim: this.data_checkedTestRadioDim,
            checkedTestRadioFunc: this.data_checkedTestRadioFunc,
            checkedTestRadioAsp: this.data_checkedTestRadioAsp,
            checkedTestRadioDoc: this.data_checkedTestRadioDoc,
            checkedTestRadioAdm: this.data_checkedTestRadioAdm,
            checkedTestsString: '',
            checkedTests: [],
            checkedTestsAlpha: [],
            checkedTestsSupplier: [],
        }
    },
    methods: {
        /*Sending to the controller all the information about the crit so that it can be added in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        addCriticality(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                if (this.checkedTests!=null && this.checkedTests.length === 0) {
                    this.checkedTests=["nothing"];
                }
                if (this.checkedTests.includes('funcTest') ){
                    if (this.checkedTestRadioFunc=='funcTestAlpha' || this.checkedTestRadioFunc=='funcTestBoth'){
                       checkedTestsAlpha.push('funcTest');
                    }
                    if (this.checkedTestRadioFunc=='funcTestSupplier' || this.checkedTestRadioFunc=='funcTestBoth'){
                        checkedTestsSupplier.push('funcTest');
                    }
                }
                if (this.checkedTests.includes('dimTest') ){
                    if(this.checkedTestRadioDim=='dimTestAlpha' || this.checkedTestRadioDim=='dimTestBoth'){
                        checkedTestsAlpha.push('dimTest');
                    }
                    if(this.checkedTestRadioDim=='dimTestSupplier' || this.checkedTestRadioDim=='dimTestBoth'){
                        checkedTestsSupplier.push('dimTest');
                    }
                }
                if (this.checkedTests.includes('aspTest') ){

                    if(this.checkedTestRadioAsp=='aspTestAlpha' || this.checkedTestRadioAsp=='aspTestBoth'){
                        checkedTestsAlpha.push('aspTest');
                    }
                    if(this.checkedTestRadioAsp=='aspTestSupplier' || this.checkedTestRadioAsp=='aspTestBoth'){
                        checkedTestsSupplier.push('aspTest');
                    }
                }
                if (this.checkedTests.includes('docControl') ){
                    if(this.checkedTestRadioDoc=='docControlAlpha' || this.checkedTestRadioDoc=='docControlBoth'){
                        checkedTestsAlpha.push('docControl');
                    }
                    if(this.checkedTestRadioDoc=='docControlSupplier' || this.checkedTestRadioDoc=='docControlBoth'){
                        checkedTestsSupplier.push('docControl');
                    }
                }
                if (this.checkedTests.includes('adminControl') ){
                    if(this.checkedTestRadioAdm=='adminControlAlpha' || this.checkedTestRadioAdm=='adminControlBoth'){
                        checkedTestsAlpha.push('adminControl');
                    }
                    if(this.checkedTestRadioAdm=='adminControlSupplier' || this.checkedTestRadioAdm=='adminControlBoth'){
                        checkedTestsSupplier.push('adminControl');
                    }
                }
                if (this.checkedTestsAlpha!=null && this.checkedTestsAlpha.length === 0) {
                    this.checkedTestsAlpha=["nothing"];
                }
                if (this.checkedTestsSupplier!=null && this.checkedTestsSupplier.length === 0) {
                    this.checkedTestsSupplier=["nothing"];
                }
                this.$emit('checkedTestsAlpha', this.checkedTestsAlpha);
                this.$emit('checkedTestsSupplier', this.checkedTestsSupplier);
                /*The First post to verify if all the fields are filled correctly
                Name, location and validate option is sent to the controller*/
                this.checkedTests.forEach((test) => {
                    this.checkedTestsString += test + ',';
                });
                console.log("crit_checkedTestsString : " + this.checkedTestsString);
                if (this.articleID !== null) {
                    axios.post('/artFam/criticality/verif', {
                        crit_artCriticality: this.crit_artCriticality,
                        crit_remarks: this.crit_remarks,
                        crit_validate: savedAs,
                        crit_articleType: this.data_article_type,
                        crit_articleID: this.data_article_id,
                        crit_performanceMedicalDevice: this.crit_performanceMedicalDevice,
                        crit_checkedTests: this.checkedTestsString,
                        crit_checkedTestRadioDim: this.checkedTestRadioDim,
                        crit_checkedTestRadioFunc: this.checkedTestRadioFunc,
                        crit_checkedTestRadioAsp: this.checkedTestRadioAsp,
                        crit_checkedTestRadioDoc: this.checkedTestRadioDoc,
                        crit_checkedTestRadioAdm: this.checkedTestRadioAdm,
                    }).then(response => {
                        this.errors = {};
                        console.log("verif passed")
                        /*If all the verifications passed, a new post this time to add the file in the database
                        The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                        axios.post('/artFam/criticality/add', {
                            crit_artCriticality: this.crit_artCriticality,
                            crit_remarks: this.crit_remarks,
                            crit_validate: savedAs,
                            crit_articleType: this.data_article_type,
                            crit_articleID: this.data_article_id,
                            crit_performanceMedicalDevice: this.crit_performanceMedicalDevice,
                            crit_checkedTests: this.checkedTestsString,
                            crit_checkedTestRadioDim: this.checkedTestRadioDim,
                            crit_checkedTestRadioFunc: this.checkedTestRadioFunc,
                            crit_checkedTestRadioAsp: this.checkedTestRadioAsp,
                            crit_checkedTestRadioDoc: this.checkedTestRadioDoc,
                            crit_checkedTestRadioAdm: this.checkedTestRadioAdm,
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
                } else {
                    console.log("addCriticality/subFamily");
                    console.log("data_artType: " + this.data_article_type);
                    console.log("data_artID: " + this.data_article_id);
                    console.log("data_artSubFamID: " + this.data_article_subFam_id);
                    axios.post('/artFam/criticality/verif', {
                        crit_artCriticality: this.crit_artCriticality,
                        crit_remarks: this.crit_remarks,
                        crit_validate: savedAs,
                        crit_articleType: this.data_article_type,
                        crit_articleID: this.data_article_id,
                        crit_articleSubFamID: this.data_article_subFam_id,
                        crit_performanceMedicalDevice: this.crit_performanceMedicalDevice,
                        crit_checkedTests: this.checkedTestsString,
                        crit_checkedTestRadioDim: this.checkedTestRadioDim,
                        crit_checkedTestRadioFunc: this.checkedTestRadioFunc,
                        crit_checkedTestRadioAsp: this.checkedTestRadioAsp,
                        crit_checkedTestRadioDoc: this.checkedTestRadioDoc,
                        crit_checkedTestRadioAdm: this.checkedTestRadioAdm,
                    }).then(response => {
                        this.errors = {};
                        console.log("verif passed")
                        /*If all the verifications passed, a new post this time to add the file in the database
                        The type, name, value, unit, validate option and id of the equipment are sent to the controller*/
                        axios.post('/artSubFam/criticality/add', {
                            crit_artCriticality: this.crit_artCriticality,
                            crit_remarks: this.crit_remarks,
                            crit_validate: savedAs,
                            crit_articleType: this.data_article_type,
                            crit_articleID: this.data_article_id,
                            crit_articleSubFamID: this.data_article_subFam_id,
                            crit_performanceMedicalDevice: this.crit_performanceMedicalDevice,
                            crit_checkedTests: this.checkedTestsString,
                            crit_checkedTestRadioDim: this.checkedTestRadioDim,
                            crit_checkedTestRadioFunc: this.checkedTestRadioFunc,
                            crit_checkedTestRadioAsp: this.checkedTestRadioAsp,
                            crit_checkedTestRadioDoc: this.checkedTestRadioDoc,
                            crit_checkedTestRadioAdm: this.checkedTestRadioAdm,
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
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateCriticality(savedAs, reason, lifesheet_created) {
            if (this.articleID !== null) {
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
            } else {
                console.log("update subfam")
                axios.post('/artFam/criticality/verif', {
                    crit_artCriticality: this.crit_artCriticality,
                    crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                    crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                    crit_artProcessCriticality: this.crit_artProcessCriticality,
                    crit_remarks: this.crit_remarks,
                    crit_validate: savedAs,
                    crit_articleType: this.data_article_type,
                    crit_articleID: this.data_article_id,
                    crit_articleSubFamID: this.data_article_subFam_id,
                    crit_justification: this.crit_justification,
                }).then(response => {
                    this.errors = {};
                    /*If all the verifications passed, a new post this time to add the file in the database
                    Type, name, value, unit, validate option and id of the equipment is sent to the controller
                    In the post url the id correspond to the id of the file who will be updated*/
                    const consultUrl = (id) => `/artSubFam/criticality/update/${id}`;
                    axios.post(consultUrl(this.crit_id), {
                        crit_artCriticality: this.crit_artCriticality,
                        crit_artMaterialContactCriticality: this.crit_artMaterialContactCriticality,
                        crit_artMaterialFunctionCriticality: this.crit_artMaterialFunctionCriticality,
                        crit_artProcessCriticality: this.crit_artProcessCriticality,
                        crit_remarks: this.crit_remarks,
                        crit_validate: savedAs,
                        crit_articleType: this.data_article_type,
                        crit_articleID: this.data_article_id,
                        crit_articleSubFamID: this.data_article_subFam_id,
                        crit_justification: this.crit_justification,
                    }).then(response => {
                        this.crit_validate = savedAs;
                        /*We test if a life sheet has been already created*/
                        /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                        if (lifesheet_created == true) {
                            axios.post('/artSubFam/history/add/' + this.data_article_type.toLowerCase() + '/' + this.data_article_id, {
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
            }
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
        if (this.data_checkedTests!=null){
            this.checkedTests=this.data_checkedTests.split(',');
        }
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

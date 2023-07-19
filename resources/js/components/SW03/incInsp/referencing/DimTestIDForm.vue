<!--File name : DimTestIDForm.vue-->
<!--Creation date : 22 May 2023-->
<!--Update date : 22 May 2023-->
<!--Vue Component of the Form of a dimensional test for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    name="name"
                    label="Dimensional Test Name :"
                    v-model="dimTest_name"
                    :isDisabled="isInConsultedMod"
                    :info_text="this.info_dimTest[5].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_name"
                />
                <InputTextForm
                    name="specDoc"
                    label="Document specification :"
                    v-model="dimTest_specDoc"
                    :isDisabled="isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_specDoc"
                />
                <InputTextForm
                    name="expectedMethod"
                    label="Expected Method :"
                    v-model="dimTest_expectedMethod"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_dimTest[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_expectedMethod"
                />
                <InputTextForm
                    name="expectedValue"
                    label="Expected Acceptance Criteria :"
                    v-model="dimTest_expectedValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_dimTest[3].info_value"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_expectedValue"
                />
                <InputTextForm
                    name="unitValue"
                    label="Unit Value :"
                    v-model="dimTest_unitValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_dimTest[4].info_value"
                    :min="1"
                    :max="10"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_unitValue"
                />
                <InputSelectForm
                    :name="'Sampling'"
                    :label="'Sampling :'"
                    isRequired
                    :options="[
                        {id_enum: 'Sampling', value: 'Statistics', text: 'Statistics'},
                        {id_enum: 'Sampling', value: '100%', text: '100%'},
                        {id_enum: 'Sampling', value: 'Other', text: 'Other'}
                    ]"
                    :isDisabled="this.isInConsultedMod"
                    v-model="dimTest_sampling"
                    :info_text="null"
                    :Errors="errors.dimTest_sampling"
                    :selctedOption="dimTest_sampling"
                    :id_actual="'DimSampling'"
                />
                <InputSelectForm
                    v-if="this.dimTest_sampling === 'Statistics'"
                    name="SeverityLevel"
                    :Errors="errors.dimTest_severityLevel"
                    label="Severity Level :"
                    :options="[
                        {id_enum: 'DimSeverityLevel', value: 'I', text: 'I'},
                        {id_enum: 'DimSeverityLevel', value: 'II', text: 'II'},
                        {id_enum: 'DimSeverityLevel', value: 'III', text: 'III'},
                        {id_enum: 'DimSeverityLevel', value: 'IV', text: 'IV'}
                    ]"
                    :selctedOption="dimTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || dimTest_sampling !== 'Statistics'"
                    v-model="dimTest_severityLevel"
                    :info_text="this.info_dimTest[0].info_value"
                    :id_actual="'DimSeverityLevel'"
                />
                <InputSelectForm
                    v-if="this.dimTest_sampling === 'Statistics'"
                    :name="'ControlLevel'"
                    :label="'Control Level :'"
                    isRequired
                    :options="[
                        {id_enum: 'DimControlLevel', value: 'Reduced', text: 'Reduced'},
                        {id_enum: 'DimControlLevel', value: 'Normal', text: 'Normal'},
                        {id_enum: 'DimControlLevel', value: 'Reinforced', text: 'Reinforced'}
                    ]"
                    :isDisabled="this.isInConsultedMod || dimTest_sampling !== 'Statistics'"
                    v-model="dimTest_controlLevel"
                    :info_text="this.info_dimTest[1].info_value"
                    :Errors="errors.dimTest_levelOfControl"
                    :selctedOption="dimTest_controlLevel"
                    :id_actual="'DimControlLevel'"
                />
                <InputTextForm
                    v-if="dimTest_sampling === 'Other'"
                    name="desc"
                    label="Description :"
                    v-model="dimTest_desc"
                    :isDisabled="!!isInConsultedMod || dimTest_sampling !== 'Other'"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.dimTest_desc"
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addDimTest" @update="updateDimTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addDimTest" @update="updateDimTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addDimTest" @update="updateDimTest"
                                        :consultMod="this.isInConsultedMod" :modifMod="this.modifMod"
                                        :savedAs="'validated'"/>
                    </div>
                    <DeleteComponentButton :validationMode="'validated'" :consultMod="this.isInConsultedMod"
                                           @deleteOk="deleteComponent"/>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'
import InputSelectForm from "../../../input/InputSelectForm.vue";
import InputNumberForm from "../../../input/SW03/InputNumberForm.vue";

export default {
    components: {
        InputNumberForm,
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert
    },
    props: {
        severityLevel: {
            type: String
        },
        controlLevel: {
            type: String
        },
        expectedMethod: {
            type: String
        },
        expectedValue: {
            type: Number
        },
        name: {
            type: String
        },
        unitValue: {
            type: String
        },
        sampling: {
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
        incmgInsp_id: {
            type: Number,
            default: null
        },
        articleSubFam_id: {
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
        desc: {
            type: String,
            default: null
        },
        specDoc: {
            type: String,
            default: null
        },
    },
    data() {
        return {
            dimTest_id: this.id,
            dimTest_severityLevel: this.severityLevel,
            dimTest_controlLevel: this.controlLevel,
            dimTest_expectedMethod: this.expectedMethod,
            dimTest_expectedValue: this.expectedValue,
            dimTest_name: this.name,
            dimTest_unitValue: this.unitValue,
            dimTest_sampling: this.sampling,
            dimTest_desc: this.desc,
            dimTest_specDoc: this.specDoc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            data_subFam_id: this.articleSubFam_id,
            info_dimTest: []
        }
    },
    methods: {
        addDimTest(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                axios.post('/incmgInsp/dimTest/verif', {
                    dimTest_name: this.dimTest_name,
                    dimTest_severityLevel: this.dimTest_severityLevel,
                    dimTest_levelOfControl: this.dimTest_controlLevel,
                    dimTest_expectedMethod: this.dimTest_expectedMethod,
                    dimTest_expectedValue: this.dimTest_expectedValue,
                    incmgInsp_id: this.data_incmgInsp_id,
                    article_type: this.data_article_type,
                    article_id: this.data_article_id,
                    subFam_id : this.data_subFam_id,
                    dimTest_articleType: this.data_article_type,
                    dimTest_sampling: this.dimTest_sampling,
                    dimTest_unitValue: this.dimTest_unitValue,
                    dimTest_desc: this.dimTest_desc,
                    dimTest_specDoc: this.dimTest_specDoc,
                    reason: 'add',
                    id: this.dimTest_id,
                })
                .then(response => {
                    this.errors = {};
                    console.log("verif passed")
                    console.log(this.data_purSpe_id)
                    axios.post('/incmgInsp/dimTest/add', {
                        dimTest_name: this.dimTest_name,
                        dimTest_severityLevel: this.dimTest_severityLevel,
                        dimTest_levelOfControl: this.dimTest_controlLevel,
                        dimTest_expectedMethod: this.dimTest_expectedMethod,
                        dimTest_expectedValue: this.dimTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        article_type: this.data_article_type,
                        article_id: this.data_article_id,
                        subFam_id : this.data_subFam_id,
                        dimTest_articleType: this.data_article_type,
                        dimTest_sampling: this.dimTest_sampling,
                        dimTest_unitValue: this.dimTest_unitValue,
                        dimTest_desc: this.dimTest_desc,
                        dimTest_specDoc: this.dimTest_specDoc,
                        reason: 'add',
                        id: this.dimTest_id,
                    })
                    .then(response => {
                        this.$snotify.success(`Dimensional Test added successfully and saved as ${savedAs}`);
                        if (!this.modifMod) {
                            this.isInConsultedMod = true;
                            this.addSucces = true
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    });
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
            }
        },
        updateDimTest(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/dimTest/verif', {
                dimTest_name: this.dimTest_name,
                dimTest_severityLevel: this.dimTest_severityLevel,
                dimTest_levelOfControl: this.dimTest_controlLevel,
                dimTest_expectedMethod: this.dimTest_expectedMethod,
                dimTest_expectedValue: this.dimTest_expectedValue,
                incmgInsp_id: this.data_incmgInsp_id,
                dimTest_articleType: this.data_article_type,
                dimTest_sampling: this.dimTest_sampling,
                dimTest_unitValue: this.dimTest_unitValue,
                dimTest_desc: this.dimTest_desc,
                dimTest_specDoc: this.dimTest_specDoc,
                reason: 'update',
                id: this.dimTest_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/dimTest/update/' + this.dimTest_id, {
                        dimTest_name: this.dimTest_name,
                        dimTest_severityLevel: this.dimTest_severityLevel,
                        dimTest_levelOfControl: this.dimTest_controlLevel,
                        dimTest_expectedMethod: this.dimTest_expectedMethod,
                        dimTest_expectedValue: this.dimTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        dimTest_articleType: this.data_article_type,
                        dimTest_sampling: this.dimTest_sampling,
                        dimTest_unitValue: this.dimTest_unitValue,
                        dimTest_desc: this.dimTest_desc,
                        dimTest_specDoc: this.dimTest_specDoc,
                        reason: 'update',
                        id: this.dimTest_id,
                        article_id: this.data_article_id,
                    })
                        .then(response => {
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$snotify.success(`Dimensional Test successfully updated`);
                            this.isInConsultedMod = true;
                            this.addSucces = true
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        });
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
        },
        clearError(event) {
            delete this.errors[event.target.name];
        },
        deleteComponent(reason, lifesheet_created) {
            this.$emit('deleteDimTest', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/dimTest')
            .then(response => {
                this.info_dimTest = response.data;
                this.loaded = true;
            })
    }
}
</script>

<style>
</style>

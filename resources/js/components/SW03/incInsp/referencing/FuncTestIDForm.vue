<!--File name : FuncTestIDForm.vue-->
<!--Creation date : 22 May 2023-->
<!--Update date : 22 May 2023-->
<!--Vue Component of the Form of a functional test for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="name"
                    label="Functional Test Name :"
                    v-model="funcTest_name"
                    :isDisabled="isInConsultedMod"
                    :info_text="this.info_funcTest[5].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_name"
                />
                <InputTextForm
                    name="specDoc"
                    label="Document specification :"
                    v-model="funcTest_specDoc"
                    :isDisabled="isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_specDoc"
                />
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="expectedMethod"
                    label="Expected Method :"
                    v-model="funcTest_expectedMethod"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_funcTest[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_expectedMethod"
                />
                <InputNumberForm
                    v-if="data_article_type === 'comp'"
                    name="expectedValue"
                    label="Expected Acceptance Criteria :"
                    v-model="funcTest_expectedValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_funcTest[3].info_value"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_expectedValue"
                />
                <InputTextForm
                    v-if="data_article_type === 'comp'"
                    name="unitValue"
                    label="Unit Value :"
                    v-model="funcTest_unitValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_funcTest[4].info_value"
                    :min="1"
                    :max="10"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_unitValue"
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
                    v-model="funcTest_sampling"
                    :info_text="null"
                    :Errors="errors.funcTest_sampling"
                    :selctedOption="funcTest_sampling"
                    :id_actual="'Sampling'"
                />
                <InputSelectForm
                    v-if="this.funcTest_sampling === 'Statistics'"
                    name="SeverityLevel"
                    :Errors="errors.funcTest_severityLevel"
                    label="Severity Level :"
                    :options="[
                        {id_enum: 'SeverityLevel', value: 'I', text: 'I'},
                        {id_enum: 'SeverityLevel', value: 'II', text: 'II'},
                        {id_enum: 'SeverityLevel', value: 'III', text: 'III'},
                        {id_enum: 'SeverityLevel', value: 'IV', text: 'IV'}
                    ]"
                    :selctedOption="funcTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || funcTest_sampling !== 'Statistics'"
                    v-model="funcTest_severityLevel"
                    :info_text="this.info_funcTest[0].info_value"
                    :id_actual="'SeverityLevel'"
                />
                <InputSelectForm
                    v-if="this.funcTest_sampling === 'Statistics'"
                    :name="'ControlLevel'"
                    :label="'Control Level :'"
                    isRequired
                    :options="[
                        {id_enum: 'ControlLevel', value: 'Reduced', text: 'Reduced'},
                        {id_enum: 'ControlLevel', value: 'Normal', text: 'Normal'},
                        {id_enum: 'ControlLevel', value: 'Reinforced', text: 'Reinforced'}
                    ]"
                    :isDisabled="this.isInConsultedMod || funcTest_sampling !== 'Statistics'"
                    v-model="funcTest_controlLevel"
                    :info_text="this.info_funcTest[1].info_value"
                    :Errors="errors.funcTest_levelOfControl"
                    :selctedOption="funcTest_controlLevel"
                    :id_actual="'ControlLevel'"
                />
                <InputTextForm
                    v-if="funcTest_sampling === 'Other'"
                    name="desc"
                    label="Description :"
                    v-model="funcTest_desc"
                    :isDisabled="!!isInConsultedMod || funcTest_sampling !== 'Other'"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_desc"
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addFuncTest" @update="updateFuncTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addFuncTest" @update="updateFuncTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addFuncTest" @update="updateFuncTest"
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
            funcTest_id: this.id,
            funcTest_severityLevel: this.severityLevel,
            funcTest_controlLevel: this.controlLevel,
            funcTest_expectedMethod: this.expectedMethod,
            funcTest_expectedValue: this.expectedValue,
            funcTest_name: this.name,
            funcTest_unitValue: this.unitValue,
            funcTest_sampling: this.sampling,
            funcTest_desc: this.desc,
            funcTest_specDoc: this.specDoc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            info_funcTest: []
        }
    },
    methods: {
        addFuncTest(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                axios.post('/incmgInsp/funcTest/verif', {
                    funcTest_name: this.funcTest_name,
                    funcTest_severityLevel: this.funcTest_severityLevel,
                    funcTest_levelOfControl: this.funcTest_controlLevel,
                    funcTest_expectedMethod: this.funcTest_expectedMethod,
                    funcTest_expectedValue: this.funcTest_expectedValue,
                    incmgInsp_id: this.data_incmgInsp_id,
                    funcTest_articleType: this.data_article_type,
                    funcTest_sampling: this.funcTest_sampling,
                    funcTest_unitValue: this.funcTest_unitValue,
                    funcTest_desc: this.funcTest_desc,
                    funcTest_specDoc: this.funcTest_specDoc,
                    reason: 'add',
                    id: this.funcTest_id,
                    article_id: this.data_article_id,
                })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/funcTest/add', {
                        funcTest_name: this.funcTest_name,
                        funcTest_severityLevel: this.funcTest_severityLevel,
                        funcTest_levelOfControl: this.funcTest_controlLevel,
                        funcTest_expectedMethod: this.funcTest_expectedMethod,
                        funcTest_expectedValue: this.funcTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        funcTest_articleType: this.data_article_type,
                        funcTest_sampling: this.funcTest_sampling,
                        funcTest_unitValue: this.funcTest_unitValue,
                        funcTest_desc: this.funcTest_desc,
                        funcTest_specDoc: this.funcTest_specDoc,
                        reason: 'add',
                        id: this.funcTest_id,
                        article_id: this.data_article_id,
                    })
                    .then(response => {
                        this.$snotify.success(`Functional Test added successfully and saved as ${savedAs}`);
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
        updateFuncTest(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/funcTest/verif', {
                funcTest_name: this.funcTest_name,
                funcTest_severityLevel: this.funcTest_severityLevel,
                funcTest_levelOfControl: this.funcTest_controlLevel,
                funcTest_expectedMethod: this.funcTest_expectedMethod,
                funcTest_expectedValue: this.funcTest_expectedValue,
                incmgInsp_id: this.data_incmgInsp_id,
                funcTest_articleType: this.data_article_type,
                funcTest_sampling: this.funcTest_sampling,
                funcTest_unitValue: this.funcTest_unitValue,
                funcTest_desc: this.funcTest_desc,
                funcTest_specDoc: this.funcTest_specDoc,
                reason: 'update',
                id: this.funcTest_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/funcTest/update/' + this.funcTest_id, {
                        funcTest_name: this.funcTest_name,
                        funcTest_severityLevel: this.funcTest_severityLevel,
                        funcTest_levelOfControl: this.funcTest_controlLevel,
                        funcTest_expectedMethod: this.funcTest_expectedMethod,
                        funcTest_expectedValue: this.funcTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        funcTest_articleType: this.data_article_type,
                        funcTest_sampling: this.funcTest_sampling,
                        funcTest_unitValue: this.funcTest_unitValue,
                        funcTest_desc: this.funcTest_desc,
                        funcTest_specDoc: this.funcTest_specDoc,
                        reason: 'update',
                        id: this.funcTest_id,
                        article_id: this.data_article_id,
                    })
                        .then(response => {
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$snotify.success(`Functional Test successfully updated`);
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
            this.$emit('deleteFuncTest', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/funcTest')
            .then(response => {
                this.info_funcTest = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>
</style>

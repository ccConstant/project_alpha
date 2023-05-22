<!--File name : CompTestIDForm.vue-->
<!--Creation date : 22 May 2023-->
<!--Update date : 22 May 2023-->
<!--Vue Component of the Form of a complementary test for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    v-if="data_article_type === 'cons'"
                    name="name"
                    label="Complementary Test Name :"
                    v-model="compTest_name"
                    :isDisabled="isInConsultedMod"
                    :info_text="this.info_compTest[0].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_name"
                />
                <InputTextForm
                    name="specDoc"
                    label="Document specification :"
                    v-model="compTest_specDoc"
                    :isDisabled="isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_specDoc"
                />
                <InputTextForm
                    v-if="data_article_type === 'cons'"
                    name="expectedMethod"
                    label="Expected Method :"
                    v-model="compTest_expectedMethod"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_compTest[4].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_expectedMethod"
                />
                <InputNumberForm
                    v-if="data_article_type === 'cons'"
                    name="expectedValue"
                    label="Expected Acceptance Criteria :"
                    v-model="compTest_expectedValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_compTest[3].info_value"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_expectedValue"
                />
                <InputTextForm
                    v-if="data_article_type === 'cons'"
                    name="unitValue"
                    label="Unit Value :"
                    v-model="compTest_unitValue"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_compTest[5].info_value"
                    :min="1"
                    :max="10"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_unitValue"
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
                    v-model="compTest_sampling"
                    :info_text="null"
                    :Errors="errors.compTest_sampling"
                    :selctedOption="compTest_sampling"
                    :id_actual="'CompSampling'"
                />
                <InputSelectForm
                    v-if="this.compTest_sampling === 'Statistics'"
                    name="SeverityLevel"
                    :Errors="errors.compTest_severityLevel"
                    label="Severity Level :"
                    :options="[
                        {id_enum: 'CompSeverityLevel', value: 'I', text: 'I'},
                        {id_enum: 'CompSeverityLevel', value: 'II', text: 'II'},
                        {id_enum: 'CompSeverityLevel', value: 'III', text: 'III'},
                        {id_enum: 'CompSeverityLevel', value: 'IV', text: 'IV'}
                    ]"
                    :selctedOption="compTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || compTest_sampling !== 'Statistics'"
                    v-model="compTest_severityLevel"
                    :info_text="this.info_compTest[1].info_value"
                    :id_actual="'CompSeverityLevel'"
                />
                <InputSelectForm
                    v-if="this.compTest_sampling === 'Statistics'"
                    :name="'ControlLevel'"
                    :label="'Control Level :'"
                    isRequired
                    :options="[
                        {id_enum: 'CompControlLevel', value: 'Reduced', text: 'Reduced'},
                        {id_enum: 'CompControlLevel', value: 'Normal', text: 'Normal'},
                        {id_enum: 'CompControlLevel', value: 'Reinforced', text: 'Reinforced'}
                    ]"
                    :isDisabled="this.isInConsultedMod || compTest_sampling !== 'Statistics'"
                    v-model="compTest_controlLevel"
                    :info_text="this.info_compTest[2].info_value"
                    :Errors="errors.compTest_levelOfControl"
                    :selctedOption="compTest_controlLevel"
                    :id_actual="'CompControlLevel'"
                />
                <InputTextForm
                    v-if="compTest_sampling === 'Other'"
                    name="desc"
                    label="Description :"
                    v-model="compTest_desc"
                    :isDisabled="!!isInConsultedMod || compTest_sampling !== 'Other'"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.compTest_desc"
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addCompTest" @update="updateCompTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addCompTest" @update="updateCompTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addCompTest" @update="updateCompTest"
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
            compTest_id: this.id,
            compTest_severityLevel: this.severityLevel,
            compTest_controlLevel: this.controlLevel,
            compTest_expectedMethod: this.expectedMethod,
            compTest_expectedValue: this.expectedValue,
            compTest_name: this.name,
            compTest_unitValue: this.unitValue,
            compTest_sampling: this.sampling,
            compTest_desc: this.desc,
            compTest_specDoc: this.specDoc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            info_compTest: [],
        }
    },
    methods: {
        addCompTest(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                axios.post('/incmgInsp/compTest/verif', {
                    compTest_name: this.compTest_name,
                    compTest_severityLevel: this.compTest_severityLevel,
                    compTest_levelOfControl: this.compTest_controlLevel,
                    compTest_expectedMethod: this.compTest_expectedMethod,
                    compTest_expectedValue: this.compTest_expectedValue,
                    incmgInsp_id: this.data_incmgInsp_id,
                    compTest_articleType: this.data_article_type,
                    compTest_sampling: this.compTest_sampling,
                    compTest_unitValue: this.compTest_unitValue,
                    compTest_desc: this.compTest_desc,
                    compTest_specDoc: this.compTest_specDoc,
                    reason: 'add',
                    id: this.compTest_id,
                    article_id: this.data_article_id,
                })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/compTest/add', {
                        compTest_name: this.compTest_name,
                        compTest_severityLevel: this.compTest_severityLevel,
                        compTest_levelOfControl: this.compTest_controlLevel,
                        compTest_expectedMethod: this.compTest_expectedMethod,
                        compTest_expectedValue: this.compTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        compTest_articleType: this.data_article_type,
                        compTest_sampling: this.compTest_sampling,
                        compTest_unitValue: this.compTest_unitValue,
                        compTest_desc: this.compTest_desc,
                        compTest_specDoc: this.compTest_specDoc,
                        reason: 'add',
                        id: this.compTest_id,
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
        updateCompTest(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/compTest/verif', {
                compTest_name: this.compTest_name,
                compTest_severityLevel: this.compTest_severityLevel,
                compTest_levelOfControl: this.compTest_controlLevel,
                compTest_expectedMethod: this.compTest_expectedMethod,
                compTest_expectedValue: this.compTest_expectedValue,
                incmgInsp_id: this.data_incmgInsp_id,
                compTest_articleType: this.data_article_type,
                compTest_sampling: this.compTest_sampling,
                compTest_unitValue: this.compTest_unitValue,
                compTest_desc: this.compTest_desc,
                compTest_specDoc: this.compTest_specDoc,
                reason: 'update',
                id: this.compTest_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/compTest/update/' + this.compTest_id, {
                        compTest_name: this.compTest_name,
                        compTest_severityLevel: this.compTest_severityLevel,
                        compTest_levelOfControl: this.compTest_controlLevel,
                        compTest_expectedMethod: this.compTest_expectedMethod,
                        compTest_expectedValue: this.compTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        compTest_articleType: this.data_article_type,
                        compTest_sampling: this.compTest_sampling,
                        compTest_unitValue: this.compTest_unitValue,
                        compTest_desc: this.compTest_desc,
                        compTest_specDoc: this.compTest_specDoc,
                        reason: 'update',
                        id: this.compTest_id,
                        article_id: this.data_article_id,
                    })
                        .then(response => {
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$snotify.success(`Complementary Test successfully updated`);
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
            this.$emit('deleteCompTest', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/compTest')
            .then(response => {
                this.info_compTest = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>
</style>

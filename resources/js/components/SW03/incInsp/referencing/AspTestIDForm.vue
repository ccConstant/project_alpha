<!--File name : AspTestIDForm.vue-->
<!--Creation date : 22 May 2023-->
<!--Update date : 22 May 2023-->
<!--Vue Component of the Form of an aspect test for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    name="name"
                    label="Aspect Test Name :"
                    v-model="aspTest_name"
                    :isDisabled="isInConsultedMod"
                    :info_text="this.info_aspTest[3].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.aspTest_name"
                />
                <InputTextForm
                    name="specDoc"
                    label="Document specification :"
                    v-model="aspTest_specDoc"
                    :isDisabled="isInConsultedMod"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.aspTest_specDoc"
                />
                <InputTextForm
                    name="expectedAspect"
                    label="Expected Acceptance Criteria :"
                    v-model="aspTest_expectedAspect"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_aspTest[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.aspTest_expectedAspect"
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
                    v-model="aspTest_sampling"
                    :info_text="null"
                    :Errors="errors.aspTest_sampling"
                    :selctedOption="aspTest_sampling"
                    :id_actual="'Sampling'"
                />
                <InputSelectForm
                    v-if="this.aspTest_sampling === 'Statistics'"
                    name="SeverityLevel"
                    :Errors="errors.aspTest_severityLevel"
                    label="Severity Level :"
                    :options="[
                        {id_enum: 'SeverityLevel', value: 'I', text: 'I'},
                        {id_enum: 'SeverityLevel', value: 'II', text: 'II'},
                        {id_enum: 'SeverityLevel', value: 'III', text: 'III'},
                        {id_enum: 'SeverityLevel', value: 'IV', text: 'IV'}
                    ]"
                    :selctedOption="aspTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || aspTest_sampling !== 'Statistics'"
                    v-model="aspTest_severityLevel"
                    :info_text="this.info_aspTest[0].info_value"
                    :id_actual="'SeverityLevel'"
                />
                <InputSelectForm
                    v-if="this.aspTest_sampling === 'Statistics'"
                    :name="'ControlLevel'"
                    :label="'Control Level :'"
                    isRequired
                    :options="[
                        {id_enum: 'ControlLevel', value: 'Reduced', text: 'Reduced'},
                        {id_enum: 'ControlLevel', value: 'Normal', text: 'Normal'},
                        {id_enum: 'ControlLevel', value: 'Reinforced', text: 'Reinforced'}
                    ]"
                    :isDisabled="this.isInConsultedMod || aspTest_sampling !== 'Statistics'"
                    v-model="aspTest_controlLevel"
                    :info_text="this.info_aspTest[1].info_value"
                    :Errors="errors.aspTest_levelOfControl"
                    :selctedOption="aspTest_controlLevel"
                    :id_actual="'ControlLevel'"
                />
                <InputTextForm
                    v-if="this.aspTest_sampling === 'Other'"
                    name="desc"
                    label="Description :"
                    v-model="aspTest_desc"
                    :isDisabled="!!isInConsultedMod || aspTest_sampling !== 'Other'"
                    :info_text="null"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.aspTest_desc"
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addAspTest" @update="updateAspTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addAspTest" @update="updateAspTest"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addAspTest" @update="updateAspTest"
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

export default {
    components: {
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
        expectedAspect: {
            type: String
        },
        name: {
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
            aspTest_id: this.id,
            aspTest_severityLevel: this.severityLevel,
            aspTest_controlLevel: this.controlLevel,
            aspTest_expectedAspect: this.expectedAspect,
            aspTest_sampling: this.sampling,
            aspTest_name: this.name,
            aspTest_desc: this.desc,
            aspTest_specDoc: this.specDoc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            info_aspTest: [],
        }
    },
    methods: {
        addAspTest(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                axios.post('/incmgInsp/aspTest/verif', {
                    aspTest_name: this.aspTest_name,
                    aspTest_severityLevel: this.aspTest_severityLevel,
                    aspTest_levelOfControl: this.aspTest_controlLevel,
                    aspTest_expectedAspect: this.aspTest_expectedAspect,
                    aspTest_sampling: this.aspTest_sampling,
                    aspTest_desc: this.aspTest_desc,
                    aspTest_specDoc: this.aspTest_specDoc,
                    incmgInsp_id: this.data_incmgInsp_id,
                    reason: 'add',
                    id: this.aspTest_id,
                    article_id: this.data_article_id,
                    aspTest_articleType: this.data_article_type,
                })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/aspTest/add', {
                        aspTest_name: this.aspTest_name,
                        aspTest_severityLevel: this.aspTest_severityLevel,
                        aspTest_levelOfControl: this.aspTest_controlLevel,
                        aspTest_expectedAspect: this.aspTest_expectedAspect,
                        aspTest_sampling: this.aspTest_sampling,
                        aspTest_desc: this.aspTest_desc,
                        aspTest_specDoc: this.aspTest_specDoc,
                        incmgInsp_id: this.data_incmgInsp_id,
                        reason: 'add',
                        id: this.aspTest_id,
                        article_id: this.data_article_id,
                        aspTest_articleType: this.data_article_type,
                    })
                    .then(response => {
                        this.$snotify.success(`Aspect Test added successfully`);
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
        updateAspTest(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/aspTest/verif', {
                aspTest_name: this.aspTest_name,
                aspTest_severityLevel: this.aspTest_severityLevel,
                aspTest_levelOfControl: this.aspTest_controlLevel,
                aspTest_expectedAspect: this.aspTest_expectedAspect,
                aspTest_sampling: this.aspTest_sampling,
                aspTest_desc: this.aspTest_desc,
                aspTest_specDoc: this.aspTest_specDoc,
                incmgInsp_id: this.data_incmgInsp_id,
                aspTest_articleType: this.data_article_type,
                reason: 'update',
                id: this.aspTest_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/aspTest/update/' + this.aspTest_id, {
                        aspTest_name: this.aspTest_name,
                        aspTest_severityLevel: this.aspTest_severityLevel,
                        aspTest_levelOfControl: this.aspTest_controlLevel,
                        aspTest_expectedAspect: this.aspTest_expectedAspect,
                        aspTest_sampling: this.aspTest_sampling,
                        aspTest_desc: this.aspTest_desc,
                        aspTest_specDoc: this.aspTest_specDoc,
                        incmgInsp_id: this.data_incmgInsp_id,
                        aspTest_articleType: this.data_article_type,
                        reason: 'update',
                        id: this.aspTest_id,
                        article_id: this.data_article_id,
                    })
                        .then(response => {
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$snotify.success(`Aspect Test added successfully`);
                            this.isInConsultedMod = true;
                            this.addSucces = true
                        })
                        .catch(error => this.errors = error.response.data.errors);
                })
                .catch(error => this.errors = error.response.data.errors);
        },
        clearError(event) {
            delete this.errors[event.target.name];
        },
        deleteComponent(reason, lifesheet_created) {
            this.$emit('deleteAspTest', '')
            this.$refs.sucessAlert.showAlert(`Empty Aspect Test Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/aspectTest')
            .then(response => {
                this.info_aspTest = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>
</style>

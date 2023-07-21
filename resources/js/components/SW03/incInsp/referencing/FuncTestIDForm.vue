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
                    v-if="data_article_type === 'comp'"
                    name="expectedMethod"
                    label="Method :"
                    v-model="funcTest_expectedMethod"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_funcTest[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                    :Errors="errors.funcTest_expectedMethod"
                />
                <InputTextForm
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
                        {id_enum: 'SeverityLevel', value: 'I Safety Control', text: 'I SC'},
                        {id_enum: 'SeverityLevel', value: 'II Safety Control', text: 'II SC'},
                        {id_enum: 'SeverityLevel', value: 'III Safety Control', text: 'III SC'},
                        {id_enum: 'SeverityLevel', value: 'IV Safety Control', text: 'IV SC'},
                        {id_enum: 'SeverityLevel', value: 'I Business Control', text: 'I BC'},
                        {id_enum: 'SeverityLevel', value: 'II Business Control', text: 'II BC'},
                        {id_enum: 'SeverityLevel', value: 'III Business Control', text: 'III BC'},
                        {id_enum: 'SeverityLevel', value: 'IV Business Control', text: 'IV BC'}
                    ]"
                    :selctedOption="funcTest_severityLevel"
                    :isDisabled="this.isInConsultedMod || funcTest_sampling !== 'Statistics'"
                    v-model="funcTest_severityLevel"
                    :info_text="this.info_funcTest[0].info_value"
                    :id_actual="'SeverityLevel'"
                />
                <InputTextForm
                    v-if="funcTest_sampling === 'Other' || funcTest_sampling === 'Statistics'"
                    name="desc"
                    label="Description/Justification :"
                    v-model="funcTest_desc"
                    :isDisabled="!!isInConsultedMod || (funcTest_sampling !== 'Other' && funcTest_sampling!='Statistics')"
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
                            <SaveButtonForm @add="addFuncTest" @update="updateFuncTest" :modifMod="this.modifMod"
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
        articleSubFam_id: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            funcTest_id: this.id,
            funcTest_severityLevel: this.severityLevel,
            funcTest_expectedMethod: this.expectedMethod,
            funcTest_expectedValue: this.expectedValue,
            funcTest_name: this.name,
            funcTest_unitValue: this.unitValue,
            funcTest_sampling: this.sampling,
            funcTest_desc: this.desc,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            data_subFam_id: this.articleSubFam_id,
            info_funcTest: []
        }
    },
    methods: {
        addFuncTest(savedAs, reason, lifesheet_created) {
            if (!this.addSucces) {
                axios.post('/incmgInsp/funcTest/verif', {
                    funcTest_name: this.funcTest_name,
                    funcTest_severityLevel: this.funcTest_severityLevel,
                    funcTest_expectedMethod: this.funcTest_expectedMethod,
                    funcTest_expectedValue: this.funcTest_expectedValue,
                    funcTest_articleType: this.data_article_type,
                    funcTest_sampling: this.funcTest_sampling,
                    funcTest_unitValue: this.funcTest_unitValue,
                    funcTest_desc: this.funcTest_desc,
                    reason: 'add',
                    id: this.funcTest_id,
                    incmgInsp_id: this.data_incmgInsp_id,
                    article_type: this.data_article_type,
                    article_id: this.data_article_id,
                    subFam_id : this.data_subFam_id,
                })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/funcTest/add', {
                        funcTest_name: this.funcTest_name,
                        funcTest_severityLevel: this.funcTest_severityLevel,
                        funcTest_expectedMethod: this.funcTest_expectedMethod,
                        funcTest_expectedValue: this.funcTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        article_type: this.data_article_type,
                        article_id: this.data_article_id,
                        subFam_id : this.data_subFam_id,
                        funcTest_articleType: this.data_article_type,
                        funcTest_sampling: this.funcTest_sampling,
                        funcTest_unitValue: this.funcTest_unitValue,
                        funcTest_desc: this.funcTest_desc,
                        reason: 'add',
                        id: this.funcTest_id,
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
                funcTest_expectedMethod: this.funcTest_expectedMethod,
                funcTest_expectedValue: this.funcTest_expectedValue,
                incmgInsp_id: this.data_incmgInsp_id,
                funcTest_articleType: this.data_article_type,
                funcTest_sampling: this.funcTest_sampling,
                funcTest_unitValue: this.funcTest_unitValue,
                funcTest_desc: this.funcTest_desc,
                reason: 'update',
                id: this.funcTest_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/funcTest/update/' + this.funcTest_id, {
                        funcTest_name: this.funcTest_name,
                        funcTest_severityLevel: this.funcTest_severityLevel,
                        funcTest_expectedMethod: this.funcTest_expectedMethod,
                        funcTest_expectedValue: this.funcTest_expectedValue,
                        incmgInsp_id: this.data_incmgInsp_id,
                        funcTest_articleType: this.data_article_type,
                        funcTest_sampling: this.funcTest_sampling,
                        funcTest_unitValue: this.funcTest_unitValue,
                        funcTest_desc: this.funcTest_desc,
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
            if (this.modifMod == true && this.funcTest_id !== null) {
                /*Send a post-request with the id of the critilicaty who will be deleted in the url*/
                const consultUrl = (id) => `/incmgInsp/funcTest/delete/${id}`;
                axios.post(consultUrl(this.funcTest_id), {
                }).then(response => {
                    /*We test if a life sheet has been already created*/
                    /*If it's the case we create a new enregistrement of history for saved the reason of the deleting*/
                    /*if (lifesheet_created == true) {
                        axios.post(`/history/add/equipment/${id}`, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }*/
                    this.$refs.sucessAlert.showAlert(`Func Test deleted successfully`);
                    /*Emit to the parent component that we want to delete this component*/
                    this.$emit('deleteFuncTest', '')
                }).catch(error => this.errors = error.response.data.errors);
            } else {
                this.$emit('deleteFuncTest', '')
                this.$refs.successAlert.showAlert(`Empty Func Test Form deleted successfully`);
            }
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

<!--File name : DocControlIDForm.vue-->
<!--Creation date : 22 May 2023-->
<!--Update date : 22 May 2023-->
<!--Vue Component of the Form of a documentary control for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    :Errors="errors.docControl_name"
                    name="name"
                    label="Doc Control Name :"
                    v-model="docControl_name"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_docControl[1].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    :Errors="errors.docControl_reference"
                    name="reference"
                    label="Doc Control Reference :"
                    v-model="docControl_reference"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_docControl[0].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'raw' || this.data_article_type === 'comp'"
                    :Errors="errors.docControl_materialCertiSpec"
                    name="matCertifSpec"
                    label="Doc Control Material Certificate Specifications :"
                    v-model="docControl_materialCertiSpec"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_docControl[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'cons'"
                    :Errors="errors.docControl_fds"
                    name="fds"
                    label="Doc Control FDS :"
                    v-model="docControl_fds"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_docControl[3].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addDocControl" @update="updateDocControl"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addDocControl" @update="updateDocControl"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addDocControl" @update="updateDocControl"
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

export default {
    components: {
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert
    },
    props: {
        reference: {
            type: String
        },
        docControlName: {
            type: String
        },
        materialCertiSpec: {
            type: String
        },
        fds: {
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
        articleSubFam_id: {
            type: Number,
            default: null
        },
    },
    data() {
        return {
            docControl_id: this.id,
            docControl_reference: this.reference,
            docControl_name: this.docControlName,
            docControl_materialCertiSpec: this.materialCertiSpec,
            docControl_fds: this.fds,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            data_subFam_id: this.articleSubFam_id,
            info_docControl: []
        }
    },
    methods: {
        addDocControl(savedAs, reason, lifesheet_created) {
            if (this.data_article_id!=null) {
                //link to article family
            }else{
                if (this.data_subFam_id!=null) {
                    //link to article sub family
                }else{
                    if (this.data_incmgInsp_id!=null) {
                        //link to incoming inspection
                    }
                }
            }
            axios.post('/incmgInsp/docControl/verif', {
                docControl_articleType: this.data_article_type,
                docControl_reference: this.docControl_reference,
                docControl_name: this.docControl_name,
                docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                docControl_FDS: this.docControl_fds,
                incmgInsp_id: this.data_incmgInsp_id,
                article_id: this.data_article_id,
                subFam_id : this.data_subFam_id,
                reason: 'add',
                id: this.docControl_id,
                article_id: this.data_article_id,
            })
            .then(response => {
                this.errors = {};
                axios.post('/incmgInsp/docControl/add', {
                    docControl_articleType: this.data_article_type,
                    docControl_reference: this.docControl_reference,
                    docControl_name: this.docControl_name,
                    docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                    docControl_FDS: this.docControl_fds,
                    incmgInsp_id: this.data_incmgInsp_id,
                    article_type: this.data_article_type,
                    article_id: this.data_article_id,
                    subFam_id : this.data_subFam_id,
                    reason: 'add',
                    id: this.docControl_id,
                    article_id: this.data_article_id,
                })
                .then(response => {
                    this.$refs.sucessAlert.showAlert(`Documentary control added successfully`);
                    this.isInConsultedMod = true;
                    this.addSucces = true

                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
            })
            .catch(error => this.errors = error.response.data.errors);
        },
        updateDocControl(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/docControl/verif', {
                docControl_articleType: this.data_article_type,
                docControl_reference: this.docControl_reference,
                docControl_name: this.docControl_name,
                docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                docControl_FDS: this.docControl_fds,
                incmgInsp_id: this.data_incmgInsp_id,
                reason: 'update',
                id: this.docControl_id,
                article_id: this.data_article_id,
            })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/docControl/update/' + this.docControl_id, {
                        docControl_articleType: this.data_article_type,
                        docControl_reference: this.docControl_reference,
                        docControl_name: this.docControl_name,
                        docControl_materialCertifSpe: this.docControl_materialCertiSpec,
                        docControl_FDS: this.docControl_fds,
                        incmgInsp_id: this.data_incmgInsp_id,
                        reason: 'update',
                        id: this.docControl_id,
                        article_id: this.data_article_id,
                    })
                        .then(response => {
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            this.$snotify.success(`Documentary Control successfully updated`);
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
            this.$emit('deleteDocControl', '')
            this.$refs.sucessAlert.showAlert(`Documentary Control Form deleted successfully`);
        }
    },
    created() {
        axios.get('/info/send/docControl')
            .then(response => {
                this.info_docControl = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>
</style>

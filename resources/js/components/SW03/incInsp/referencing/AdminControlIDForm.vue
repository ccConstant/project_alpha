<!--File name : AdminControlIDForm.vue-->
<!--Creation date : 10 Jul 2023-->
<!--Update date : 10 Jul 2023-->
<!--Vue Component of the Form of a administrative control for an article-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" @keydown="clearError">
                <InputTextForm
                    :Errors="errors.adminControl_name"
                    name="name"
                    label="Doc Control Name :"
                    v-model="adminControl_name"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_adminControl[1].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    :Errors="errors.adminControl_reference"
                    name="reference"
                    label="Doc Control Reference :"
                    v-model="adminControl_reference"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_adminControl[0].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'raw' || this.data_article_type === 'comp'"
                    :Errors="errors.adminControl_materialCertiSpec"
                    name="matCertifSpec"
                    label="Doc Control Material Certificate Specifications :"
                    v-model="adminControl_materialCertiSpec"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_adminControl[2].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <InputTextForm
                    v-if="this.data_article_type === 'cons'"
                    :Errors="errors.adminControl_fds"
                    name="fds"
                    label="Doc Control FDS :"
                    v-model="adminControl_fds"
                    :isDisabled="!!isInConsultedMod"
                    :info_text="this.info_adminControl[3].info_value"
                    :min="2"
                    :max="255"
                    :inputClassName="null"
                    isRequired
                />
                <div v-if="this.addSucces===false ">
                    <div v-if="this.incmgInsp_id===null ">
                        <div v-if="modifMod===true">
                            <SaveButtonForm @add="addAdminControl" @update="updateAdminControl" :modifMod="this.modifMod"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addAdminControl" @update="updateAdminControl"
                                            :consultMod="this.isInConsultedMod" :savedAs="'validated'"/>
                        </div>
                    </div>
                    <div v-else-if="this.incmgInsp_id!==null">
                        <SaveButtonForm @add="addAdminControl" @update="updateAdminControl"
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
        adminControlName: {
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
        purSpe_id: {
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
    },
    data() {
        return {
            adminControl_id: this.id,
            adminControl_reference: this.reference,
            adminControl_name: this.adminControlName,
            adminControl_materialCertiSpec: this.materialCertiSpec,
            adminControl_fds: this.fds,
            errors: {},
            addSucces: false,
            isInConsultedMod: this.consultMod,
            loaded: false,
            isInModifMod: this.modifMod,
            data_article_id: this.articleID,
            data_article_type: this.articleType.toLowerCase(),
            data_incmgInsp_id: this.incmgInsp_id,
            data_purSpe_id: this.purSpe_id,
            info_adminControl: []
        }
    },
    methods: {
        addAdminControl(savedAs, reason, lifesheet_created) {
                axios.post('/incmgInsp/adminControl/verif', {
                    adminControl_articleType: this.data_article_type,
                    adminControl_reference: this.adminControl_reference,
                    adminControl_name: this.adminControl_name,
                    adminControl_materialCertifSpe: this.adminControl_materialCertiSpec,
                    adminControl_FDS: this.adminControl_fds,
                    incmgInsp_id: this.data_incmgInsp_id,
                    purSpe_id: this.purSpe_id,
                    reason: 'add',
                    article_id: this.data_article_id,
                })
                .then(response => {
                    this.errors = {};
                    axios.post('/incmgInsp/adminControl/add', {
                        adminControl_articleType: this.data_article_type,
                        adminControl_reference: this.adminControl_reference,
                        adminControl_name: this.adminControl_name,
                        adminControl_materialCertifSpe: this.adminControl_materialCertiSpec,
                        adminControl_FDS: this.adminControl_fds,
                        incmgInsp_id: this.data_incmgInsp_id,
                        purSpe_id: this.purSpe_id,
                        reason: 'add',
                        id: this.adminControl_id,
                        article_id: this.data_article_id,
                    })
                    .then(response => {
                        this.$refs.sucessAlert.showAlert(`Administrative control added successfully`);
                        this.isInConsultedMod = true;
                        this.addSucces = true

                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    });
                })
                .catch(error => this.errors = error.response.data.errors);
            },
        updateAdminControl(savedAs, reason, artSheet_created) {
            axios.post('/incmgInsp/adminControl/verif', {
                adminControl_articleType: this.data_article_type,
                adminControl_reference: this.adminControl_reference,
                adminControl_name: this.adminControl_name,
                adminControl_materialCertifSpe: this.adminControl_materialCertiSpec,
                adminControl_FDS: this.adminControl_fds,
                incmgInsp_id: this.data_incmgInsp_id,
                reason: 'update',
                id: this.adminControl_id,
                article_id: this.data_article_id,
            })
            .then(response => {
                this.errors = {};
                console.log("verif update passed")
                axios.post('/incmgInsp/adminControl/update/' + this.adminControl_id, {
                    adminControl_articleType: this.data_article_type,
                    adminControl_reference: this.adminControl_reference,
                    adminControl_name: this.adminControl_name,
                    adminControl_materialCertifSpe: this.adminControl_materialCertiSpec,
                    adminControl_FDS: this.adminControl_fds,
                    reason: 'update',
                    id: this.adminControl_id,
                    article_id: this.data_article_id,
                })
                .then(response => {
                    if (artSheet_created == true) {
                        axios.post('/artFam/history/add/' + this.articleType.toLowerCase() + '/' + this.articleID, {
                            history_reasonUpdate: reason,
                        });
                        window.location.reload();
                    }
                    this.$snotify.success(`Administrative Control successfully updated`);
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
            this.$emit('deleteAdminControl', '')
            this.$refs.sucessAlert.showAlert(`Admin Control Form deleted successfully`);
        }
    },
    created() {
        console.log("created admin control ID Form")
        axios.get('/info/send/adminControl')
            .then(response => {
                this.info_adminControl = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>
</style>

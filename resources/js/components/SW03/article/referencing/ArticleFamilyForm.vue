<!--File name : ArticleFamilyForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div class="articleFamily" v-if="loaded==true">
        <h2 class="titleForm1">Article ID : {{ artFam_ref }}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
            <InputSelectForm
                @clearSelectError='clearSelectError'
                name="artFam_type"
                :Errors="errors.artFam_type"
                label="Article Family Type :"
                :options="enumArticleFam_type"
                :selctedOption="artFam_type"
                :isDisabled="this.isInConsultMod"
                v-model="artFam_type"
                :info_text="null"
                :id_actual="ArticleFamilyType"
            />
            <div v-if="artFam_type!=''">
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_ref"
                    name="artFam_ref"
                    label="Article Reference"
                    :isDisabled="this.isInConsultMod"
                    isRequired
                    v-model="artFam_ref"
                    :info_text="this.infos_artFam[0].info_value"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_design"
                    name="artFam_design"
                    label="Article Designation"
                    :isDisabled="isInConsultMod"
                    isRequired
                    v-model="artFam_design"
                    :info_text="this.infos_artFam[1].info_value"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_drawingPath"
                    name="artFam_drawingPath"
                    label="Article Drawing Path"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_drawingPath"
                    :info_text="this.infos_artFam[2].info_value"
                    :max="255"
                />
                <InputTextForm v-if="artFam_type=='COMP' || artFam_type=='CONS'"
                    :inputClassName="null"
                    :Errors="errors.artFam_version"
                    name="artFam_version"
                    label="Article Version"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_version"
                    :info_text="this.infos_artFam[5].info_value"
                    :max="4"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    :name="'Active ?'"
                    :label="'Active'"
                    v-model="artFam_active"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="errors['Active']"
                    :checkedOption="isInModifMod ? artFam_active : true"
                />
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    name="artFam_purchasedBy"
                    :Errors="errors.artFam_purchasedBy"
                    label="Article Family Purchased By :"
                    :options="enum_purchasedBy"
                    :selctedOption="artFam_purchasedBy"
                    :isDisabled="!!isInConsultMod"
                    v-model="artFam_purchasedBy"
                    :info_text="this.infos_artFam[3].info_value"
                    :id_actual="purchasedBy"
                />
                <h2>
                    Generic Member Information
                </h2>
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_variablesCharac"
                    name="artFam_variablesCharac"
                    label="Article Variable Characteristic"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_variablesCharac"
                    :info_text="this.infos_artFam[4].info_value"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_variablesCharacDesign"
                    name="artFam_variablesCharacDesign"
                    label="Article Variable Characteristic Designations"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_variablesCharacDesign"
                    :info_text="null"
                    :max="255"
                />
                <p>
                    <table>
                        <tr>
                            <td style="width: 10%; text-align: right; vertical-align: bottom!important;">
                                {{ artFam_ref }}_
                            </td>
                            <td>
                                <InputTextForm
                                    :inputClassName="null"
                                    :Errors="errors.artFam_genRef"
                                    name="artFam_genDesign" label="Member Generic Reference"
                                    :isDisabled="isInConsultMod"
                                    v-model="artFam_genRef"
                                    :info_text="this.infos_artFam[6].info_value"
                                    :max="255"
                                />
                            </td>
                        </tr>
                    </table>
                </p>
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_genDesign"
                    name="artFam_genDesign" label="Member Generic Designation"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_genDesign"
                    :info_text="this.infos_artFam[7].info_value"
                    :max="255"
                />
                <SaveButtonForm v-if="this.addSuccess==false"
                    ref="saveButton"
                    @add="addArtFam"
                    @update="updateArtFam"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"/>
            </div>
        </form>
        <SuccessAlert ref="successAlert"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputNumberForm from '../../../input/SW03/InputNumberForm.vue'
import InputTextWithOptionForm from '../../../input/InputTextWithOptionForm.vue'
import RadioGroupForm from '../../../input/SW03/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputTextForm,
        InputSelectForm,
        InputNumberForm,
        InputTextWithOptionForm,
        InputTextAreaForm,
        RadioGroupForm,
        SaveButtonForm,
        SuccessAlert,


    },
    /*--------Declaration of the different props:--------
        Id : Id of the article family
        reference : Reference of the article family
        designation : Designation of the article family
        designation : Path to the directory containing the drawing representing the article family
        purchasedBy : Employee of the company that ordered this article family
        variablesCharac : Variable characteristic(s) of this article family
        validate : article validation status (drafted, to be validated or validated)
        version : Alpha version of this article family
        active : Is the article currently in use?
        consultMod : Is the article in consultation mode?
        modifMod : Is the article in modification mode?
        min : Minimum number of characters for a field
        max : Maximum number of characters for a field

    ---------------------------------------------------*/
    props: {
        reference: {
            type: String,
            default: null
        },
        designation: {
            type: String
        },
        drawingPath: {
            type: String
        },
        purchasedBy: {
            type: String
        },
        variablesCharac: {
            type: String
        },
        variablesCharacDesign: {
            type: String
        },
        version: {
            type: String
        },
        validate: {
            type: String
        },
        type: {
            type: String,
            default: ""
        },
        active: {
            type: Boolean,
            default: false
        },
        genDesign: {
            type: String
        },
        genRef: {
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
         min : {
            type: Number,
            default: 3
        },
        max : {
            type: Number,
            default: 255
        }
    },
    data() {
        return {
            /*--------Declaration of the different returned data:--------

                Id : Id of the article family
                artFam_ref : Reference of the article family which  will be appear in the field and updated dynamically
                artFam_design : Designation of the article family which  will be appear in the field and updated dynamically
                artFam_drawingPath : Path to the directory containing the drawing representing the article family which  will be appear in the field and updated dynamically
                artFam_purchasedBy : Employee of the company that ordered this article family which  will be appear in the field and updated dynamically
                artFam_variablesCharac : Variable characteristic(s) of this article family which  will be appear in the field and updated dynamically
                artFam_validate : Validation option selected by the user
                artFam_validates : Different validation option with values : drafted , to_be_validated  and validated
                artFam_version : Alpha version of this article family
                artFam_active : Is the article currently in use?
                enum_purchasedBy : Different employee option gets from the database
                errors : Errors due to a wrong input in the field, given by the controller
            -----------------------------------------------------------*/
            artFam_ref: this.reference,
            artFam_design: this.designation,
            artFam_drawingPath: this.drawingPath,
            artFam_purchasedBy: this.purchasedBy,
            artFam_variablesCharac: this.variablesCharac,
            artFam_variablesCharacDesign: this.variablesCharacDesign,
            artFam_version: this.version,
            artFam_storageCondition : this.storageCondition,
            artFam_active: this.active,
            artFam_validate: this.validate,
            artFam_genDesign: this.genDesign,
            artFam_genRef: this.genRef,
            isInConsultMod: this.consultMod,
            isInModifMod : this.modifMod,
            enum_purchasedBy: [],
            enum_storageCondition:[],
            artFam_id: this.$route.params.id,
            errors: {},
            addSuccess: false,
            infos_artFam: [],
            loaded: false,
            artFamPurchasedBy: "articleFamPurchasedBy",
            ArticleFamilyType: "articleFamType",
            artFam_type: this.type,
            enumArticleFam_type: [
                {id_enum: 'artFam_type', value: "COMP", text: 'COMP'},
                {id_enum: 'artFam_type', value: "RAW", text: 'RAW'},
                {id_enum: 'artFam_type', value: "CONS", text: 'CONS'}
            ],
            savedAs: this.validate
        }
    },
    created() {
        axios.get('/info/send/articleFamily')
            .then(response => {
                this.infos_artFam = response.data;
            })
        /*Ask for the controller different purchased by option */
        axios.get('/artFam/enum/purchasedBy')
            .then(response => this.enum_purchasedBy = response.data)
            .catch(error => this.errors = error.response.data.errors);

        /*Ask for the controller different storage condition option */
        axios.get('/artFam/enum/storageCondition')
            .then(response => {
                this.enum_storageCondition = response.data;
                if (this.artFam_genRef != null) {
                    this.artFam_genRef = this.artFam_genRef.substring(this.artFam_ref.length+1);
                }
                this.loaded=true;
            })
            .catch(error => this.errors = error.response.data.errors);
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the art family so that it can be added to the database */
        addArtFam(savedAs) {
            if (!this.addSuccess) {
                /*We begin by checking if the data entered by the user are correct*/
                if (this.artFam_type=="COMP"){
                    axios.post('/comp/family/verif', {
                        artFam_ref: this.artFam_ref,
                        artFam_design: this.artFam_design,
                        artFam_drawingPath: this.artFam_drawingPath,
                        artFam_purchasedBy: this.artFam_purchasedBy,
                        artFam_variablesCharac: this.artFam_variablesCharac,
                        artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                        artFam_version: this.artFam_version,
                        artFam_active: this.artFam_active,
                        artFam_validate: savedAs,
                        artFam_id: this.artFam_id,
                        artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                        artFam_genDesign: this.artFam_genDesign,
                    })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            this.errors = {};
                                axios.post('/comp/family/add', {
                                    artFam_ref: this.artFam_ref,
                                    artFam_design: this.artFam_design,
                                    artFam_drawingPath: this.artFam_drawingPath,
                                    artFam_purchasedBy: this.artFam_purchasedBy,
                                    artFam_variablesCharac: this.artFam_variablesCharac,
                                    artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                                    artFam_version: this.artFam_version,
                                    artFam_active: this.artFam_active,
                                    artFam_validate: savedAs,
                                    artFam_id: this.artFam_id,
                                    artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                                    artFam_genDesign: this.artFam_genDesign,

                                })
                                    /*If the data have been added in the database, we show a success message*/
                                    .then(response => {
                                        this.addSuccess = true;
                                        this.isInConsultMod = true;
                                        this.$snotify.success(`CompFam ID added successfully and saved as ${savedAs}`);
                                        this.artFam_id = response.data;
                                        this.$emit('ArtFamID', this.artFam_id);
                                        this.$emit('ArtFamType', this.artFam_type);
                                        if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                            this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                                        }
                                    })
                                    .catch(error => this.errors = error.response.data.errors);
                            })
                        .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.artFam_type=="RAW"){
                        axios.post('/raw/family/verif', {
                            artFam_ref: this.artFam_ref,
                            artFam_design: this.artFam_design,
                            artFam_drawingPath: this.artFam_drawingPath,
                            artFam_purchasedBy: this.artFam_purchasedBy,
                            artFam_variablesCharac: this.artFam_variablesCharac,
                            artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                            artFam_active: this.artFam_active,
                            artFam_validate: savedAs,
                            artFam_id: this.artFam_id,
                            artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                            artFam_genDesign: this.artFam_genDesign,
                        })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            this.errors = {};
                            axios.post('/raw/family/add', {
                                artFam_ref: this.artFam_ref,
                                artFam_design: this.artFam_design,
                                artFam_drawingPath: this.artFam_drawingPath,
                                artFam_purchasedBy: this.artFam_purchasedBy,
                                artFam_variablesCharac: this.artFam_variablesCharac,
                                artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                                artFam_active: this.artFam_active,
                                artFam_validate: savedAs,
                                artFam_id: this.artFam_id,
                                artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                                artFam_genDesign: this.artFam_genDesign,

                            })
                            /*If the data have been added in the database, we show a success message*/
                            .then(response => {
                                this.addSuccess = true;
                                this.isInConsultMod = true;
                                this.$snotify.success(`RawFam ID added successfully and saved as ${savedAs}`);
                                this.artFam_id = response.data;
                                this.$emit('ArtFamID', this.artFam_id);
                                this.$emit('ArtFamType', this.artFam_type);
                                if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                    this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                                }
                            })
                            .catch(error => this.errors = error.response.data.errors);
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    }else{
                        if (this.artFam_type=="CONS"){
                            axios.post('/cons/family/verif', {
                                artFam_ref: this.artFam_ref,
                                artFam_design: this.artFam_design,
                                artFam_drawingPath: this.artFam_drawingPath,
                                artFam_purchasedBy: this.artFam_purchasedBy,
                                artFam_version: this.artFam_version,
                                artFam_variablesCharac: this.artFam_variablesCharac,
                                artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                                artFam_active: this.artFam_active,
                                artFam_validate: savedAs,
                                artFam_id: this.artFam_id,
                                artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                                artFam_genDesign: this.artFam_genDesign,
                            })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                this.errors = {};
                                axios.post('/cons/family/add', {
                                    artFam_ref: this.artFam_ref,
                                    artFam_design: this.artFam_design,
                                    artFam_drawingPath: this.artFam_drawingPath,
                                    artFam_purchasedBy: this.artFam_purchasedBy,
                                    artFam_variablesCharac: this.artFam_variablesCharac,
                                    artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                                    artFam_active: this.artFam_active,
                                    artFam_version: this.artFam_version,
                                    artFam_validate: savedAs,
                                    artFam_id: this.artFam_id,
                                    artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                                    artFam_genDesign: this.artFam_genDesign,
                                })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.$snotify.success(`ConsFam ID added successfully and saved as ${savedAs}`);
                                    this.artFam_id = response.data;
                                    this.$emit('ArtFamID', this.artFam_id);
                                    this.$emit('ArtFamType', this.artFam_type);
                                    if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                        this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                                    }
                                })
                                .catch(error => this.errors = error.response.data.errors);
                            })
                            .catch(error => this.errors = error.response.data.errors);
                        }
                    }
                }
            }
        },
        /*Sending to the controller all the information about the art family  so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        updateArtFam(savedAs, reason, artSheet_created) {
            /*We begin by checking if the data entered by the user are correct*/
            axios.post('/' + this.artFam_type.toLowerCase() + '/family/verif', {
                artFam_ref: this.artFam_ref,
                artFam_design: this.artFam_design,
                artFam_drawingPath: this.artFam_drawingPath,
                artFam_purchasedBy: this.artFam_purchasedBy,
                artFam_variablesCharac: this.artFam_variablesCharac,
                artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                artFam_version: this.artFam_version,
                artFam_active: this.artFam_active,
                artFam_validate: savedAs,
                artFam_id: this.artFam_id,
                artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                artFam_genDesign: this.artFam_genDesign,
            })
                /*If the data are correct, we send them to the controller for update data in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => '/' + this.artFam_type.toLowerCase() + '/family/update/' + id;
                    axios.post(consultUrl(this.artFam_id), {
                        artFam_ref: this.artFam_ref,
                        artFam_design: this.artFam_design,
                        artFam_drawingPath: this.artFam_drawingPath,
                        artFam_purchasedBy: this.artFam_purchasedBy,
                        artFam_variablesCharac: this.artFam_variablesCharac,
                        artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,
                        artFam_version: this.artFam_version,
                        artFam_active: this.artFam_active,
                        artFam_validate: savedAs,
                        artFam_id: this.artFam_id,
                        artFam_genRef: this.artFam_ref+'_'+this.artFam_genRef,
                        artFam_genDesign: this.artFam_genDesign,
                    })
                        .then(response => {
                            const id = this.artFam_id;
                            /*We test if an article sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (artSheet_created == true) {
                                axios.post('/artFam/history/add/' + this.artFam_type.toLowerCase() + '/' + this.artFam_id, {
                                    history_reasonUpdate: reason,
                                }).catch(error => {
                                    this.errors = error.response.data.errors;
                                });
                                window.location.reload();
                            }
                            this.isInConsultMod = true;
                            /*If the data have been updated in the database, we show a success message*/
                            this.$snotify.success(`CompFam ID successfully updated and saved as ${savedAs}`);
                            this.artFam_validate = savedAs;
                            if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        });
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
        clearAllError() {
            this.errors = {};
        },
    },
    beforeUpdate() {
        if (this.artFam_ref === null && this.artFam_type !== "") {
            this.artFam_ref = 'G_' + this.artFam_type;
        }
    }
}
</script>

<style lang="scss">
    .titleForm1 {
        padding-left: 10px;
        right: 100px;
        position: fixed;
        background-color: aqua;
        top: 90px;
        z-index: 5;
    }
    table {
        width: 100%;
    }
    .container {
        margin-top: 50px;
    }
</style>

<!--File name : ArticleFamilyForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div v-if="loaded==true" class="articleFamily">
        <h2 class="titleForm1">Article ID : {{ artFam_ref }}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
            <InputSelectForm
                v-model="artFam_type"
                :Errors="errors.artFam_type"
                :id_actual="ArticleFamilyType"
                :info_text="null"
                :isDisabled="this.isInConsultMod"
                :options="enumArticleFam_type"
                :selctedOption="artFam_type"
                label="Article Family Type :"
                name="artFam_type"
                @clearSelectError='clearSelectError'
            />
            <div v-if="artFam_type!=''">
                <InputTextForm
                    v-model="artFam_ref"
                    :Errors="errors.artFam_ref"
                    :info_text="this.infos_artFam[0].info_value"
                    :inputClassName="null"
                    :isDisabled="this.isInConsultMod"
                    :max="255"
                    :min="3"
                    isRequired
                    label="Article Reference"
                    name="artFam_ref"
                />
                <InputTextForm
                    v-model="artFam_design"
                    :Errors="errors.artFam_design"
                    :info_text="this.infos_artFam[1].info_value"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :max="255"
                    :min="3"
                    isRequired
                    label="Article Designation"
                    name="artFam_design"
                />
                <InputTextForm
                    v-model="artFam_drawingPath"
                    :Errors="errors.artFam_drawingPath"
                    :info_text="this.infos_artFam[2].info_value"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :max="255"
                    label="Article Drawing Path"
                    name="artFam_drawingPath"
                />
                <InputTextForm v-if="artFam_type=='COMP' || artFam_type=='CONS'"
                               v-model="artFam_version"
                               :Errors="errors.artFam_version"
                               :info_text="this.infos_artFam[5].info_value"
                               :inputClassName="null"
                               :isDisabled="isInConsultMod"
                               :max="4"
                               label="Article Version"
                               name="artFam_version"
                />
                <RadioGroupForm
                    v-model="artFam_active"
                    :Errors="errors['Active']"
                    :checkedOption="isInModifMod ? artFam_active : true"
                    :info_text="null"
                    :inputClassName="null"
                    :label="'Active'"
                    :name="'Active ?'"
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                />
                <InputSelectForm
                    v-model="artFam_purchasedBy"
                    :Errors="errors.artFam_purchasedBy"
                    :id_actual="purchasedBy"
                    :info_text="this.infos_artFam[3].info_value"
                    :isDisabled="!!isInConsultMod"
                    :options="enum_purchasedBy"
                    :selctedOption="artFam_purchasedBy"
                    label="Article Family Purchased By :"
                    name="artFam_purchasedBy"
                    @clearSelectError='clearSelectError'
                />
                <InputTextForm
                    v-model="artFam_mainRef"
                    :Errors="errors.artFam_mainRef"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :max="255"
                    label="Main Reference of the Article Family"
                    name="artFam_mainRef"
                />
                <SaveButtonForm v-if="this.addSuccess==false"
                                ref="saveButton"
                                :consultMod="this.isInConsultMod"
                                :modifMod="this.isInModifMod"
                                :savedAs="validate"
                                @add="addArtFam"
                                @update="updateArtFam"/>
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
        mainRef: {
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
        min: {
            type: Number,
            default: 3
        },
        max: {
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
                artFam_validate : Validation option selected by the user
                artFam_validates : Different validation option with values : drafted , to_be_validated  and validated
                artFam_version : Alpha version of this article family
                artFam_active : Is the article currently in use?
                enum_purchasedBy: A different employee option gets from the database
                errors: Errors due to a wrong input in the field, given by the controller
            -----------------------------------------------------------*/
            artFam_ref: this.reference,
            artFam_design: this.designation,
            artFam_drawingPath: this.drawingPath,
            artFam_purchasedBy: this.purchasedBy,
            artFam_version: this.version,
            artFam_storageCondition: this.storageCondition,
            artFam_active: this.active,
            artFam_validate: this.validate,
            artFam_mainRef: this.mainRef,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            enum_purchasedBy: [],
            enum_storageCondition: [],
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
                this.loaded = true;
            })
            .catch(error => this.errors = error.response.data.errors);

    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the art family so that it can be added to the database */
        addArtFam(savedAs) {
            if (!this.addSuccess) {
                /*We begin by checking if the data entered by the user are correct*/
                axios.post('/'+ this.artFam_type.toLowerCase() +'/family/verif', {
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
                    artFam_genRef: this.artFam_genRef,
                    artFam_genDesign: this.artFam_genDesign,
                    artFam_mainRef: this.artFam_mainRef,
                })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        this.errors = {};
                        axios.post('/'+ this.artFam_type.toLowerCase() +'/family/add', {
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
                            artFam_genRef: this.artFam_genRef,
                            artFam_genDesign: this.artFam_genDesign,
                            artFam_mainRef: this.artFam_mainRef,
                        })
                            /*If the data have been added in the database, we show a success message*/
                            .then(response => {
                                this.addSuccess = true;
                                this.isInConsultMod = true;
                                this.$snotify.success(this.artFam_type.toLowerCase() +`Fam ID added successfully and saved as ${savedAs}`);
                                this.artFam_id = response.data;
                                this.$emit('ArtFamID', this.artFam_id);
                                this.$emit('ArtFamType', this.artFam_type);
                                if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                    this.$emit('generic', this.artFam_ref + '_' + this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                                }
                            })
                            .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
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
                /*artFam_variablesCharac: this.artFam_variablesCharac,
                artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,*/
                artFam_version: this.artFam_version,
                artFam_active: this.artFam_active,
                artFam_validate: savedAs,
                artFam_id: this.artFam_id,
               /*artFam_genRef: this.artFam_genRef,
                artFam_genDesign: this.artFam_genDesign,*/
                artFam_mainRef: this.artFam_mainRef,
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
                        /*artFam_variablesCharac: this.artFam_variablesCharac,
                        artFam_variablesCharacDesign: this.artFam_variablesCharacDesign,*/
                        artFam_version: this.artFam_version,
                        artFam_active: this.artFam_active,
                        artFam_validate: savedAs,
                        artFam_id: this.artFam_id,
                        /*artFam_genRef: this.artFam_genRef,
                        artFam_genDesign: this.artFam_genDesign,*/
                        artFam_mainRef: this.artFam_mainRef,
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
                            /*if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                this.$emit('generic', this.artFam_ref + '_' + this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                            }*/
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
        }
    },
    beforeUpdate() {
        if (this.artFam_ref === null && this.artFam_type !== "") {
            this.artFam_ref = 'G_' + this.artFam_type;
        }
        /*if (this.artFam_genRef !== undefined) {
            let a = this.artFam_genRef.split('_');
            if (a[2] !== undefined) {
                this.artFam_genRef = this.artFam_ref + '_' + a[2];
            } else {
                this.artFam_genRef = this.artFam_ref + '_';
            }
        } else {
            this.artFam_genRef = this.artFam_ref + '_';
        }*/
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

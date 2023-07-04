<!--File name : ArticleSubFamilyForm.vue-->
<!--Creation date : 3 Jul 2023-->
<!--Update date : 3 Jul 2023-->
<!--Vue Component of the Id card of the component sub family who call all the input component and send the data to the controllers-->

<template>
    <div class="articleSubFamily" v-if="loaded==true">
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
                <InputTextDoubleForm
                    :inputClassName="null"
                    :Errors="errors.artSubFam_ref"
                    name="artSubFam_ref"
                    label="Article Sub Family Reference"
                    :isDisabled="this.isInConsultMod"
                    isRequired
                    v-model="artSubFam_ref"
                    :famRef="this.data_artFam_ref"
                    :info_text="this.infos_artSubFam[0].info_value"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artSubFam_design"
                    name="artSubFam_design"
                    label="Article Sub Family Designation"
                    :isDisabled="isInConsultMod"
                    isRequired
                    v-model="artSubFam_design"
                    :info_text="this.infos_artSubFam[1].info_value"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artSubFam_drawingPath"
                    name="artSubFam_drawingPath"
                    label="Article Sub Family Drawing Path"
                    :isDisabled="isInConsultMod"
                    v-model="artSubFam_drawingPath"
                    :info_text="this.infos_artSubFam[2].info_value"
                    :max="255"
                />
                <InputTextForm v-if="this.data_artFam_type=='COMP' || this.data_artFam_type=='CONS'"
                    :inputClassName="null"
                    :Errors="errors.artSubFam_version"
                    name="artSubFam_version"
                    label="Article Sub Family Version"
                    :isDisabled="isInConsultMod"
                    v-model="artSubFam_version"
                    :info_text="this.infos_artSubFam[5].info_value"
                    :max="4"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    :name="'Active'"
                    :label="'Is this sub family is active ?'"
                    v-model="artSubFam_active"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="errors['Active']"
                    :checkedOption="isInModifMod ? artSubFam_active : true"
                />
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    name="artSubFam_purchasedBy"
                    :Errors="errors.artSubFam_purchasedBy"
                    label="Article Sub Family Purchased By :"
                    :options="enum_purchasedBy"
                    :selctedOption="artSubFam_purchasedBy"
                    :isDisabled="!!isInConsultMod"
                    v-model="artSubFam_purchasedBy"
                    :info_text="this.infos_artSubFam[3].info_value"
                    :id_actual="purchasedBy"
                />
                <SaveButtonForm v-if="this.addSuccess==false"
                    ref="saveButton"
                    @add="addArtSubFam"
                    @update="updateArtSubFam"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"/>
        </form>
        <SuccessAlert ref="successAlert"/>
        <div v-if="this.artSubFam_id!==null && modifMod==false && consultMod==false && this.addSuccess" >
                <ReferenceAnArticleFamilyMember :artType="this.data_artFam_type"  :artFam_ref="this.data_artFam_ref" :artSubFam_ref="this.artSubFam_ref"  :artSubFam_id="this.artSubFam_id"/>
            </div>
            <div v-else-if="this.artSubFam_id!==null && modifMod==true && this.addSuccess">
                <ReferenceAnArticleFamilyMember :consultMod="!!isInConsultMod" :artType="this.data_artFam_type" :artFam_ref="this.data_artFam_ref" :artSubFam_ref="this.artSubFam_ref" :artSubFam_id="this.artSubFam_id"
                                :modifMod="!!this.modifMod"/>
            </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputNumberForm from '../../../input/SW03/InputNumberForm.vue'
import InputTextDoubleForm from '../../../input/SW03/InputTextDoubleForm.vue'
import RadioGroupForm from '../../../input/SW03/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SuccessAlert from '../../../alert/SuccesAlert.vue'
import ReferenceAnArticleFamilyMember from './ReferenceAnArticleFamilyMember.vue'
export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        InputTextForm,
        InputSelectForm,
        InputNumberForm,
        InputTextDoubleForm,
        InputTextAreaForm,
        RadioGroupForm,
        SaveButtonForm,
        SuccessAlert,
        ReferenceAnArticleFamilyMember


    },
    /*--------Declaration of the different props:--------
        Id : Id of the article sub family
        reference : Reference of the article sub family
        designation : Designation of the article sub family
        purchasedBy : Employee of the company that ordered this article sub family
        validate : article validation status (drafted, to be validated or validated)
        version : Alpha version of this article sub family
        active : Is the article currently in use?
        consultMod : Is the article in consultation mode?
        modifMod : Is the article in modification mode?
        min : Minimum number of characters for a field
        max : Maximum number of characters for a field

    ---------------------------------------------------*/
    props: {
        art_ref:{
            type: String,
            default: null
        },
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
        },
        art_type: {
            type: String,
            default: ""
        },
        art_id: {
            type: Number,
            default: null
        },
    },
    data() {
        return {
            /*--------Declaration of the different returned data:--------

                Id : Id of the article family
                artSubFam_ref : Reference of the article sub family which  will be appear in the field and updated dynamically
                artSubFam_design : Designation of the article sub family which  will be appear in the field and updated dynamically
                artSubFam_drawingPath : Path to the directory containing the drawing representing the article sub family which  will be appear in the field and updated dynamically
                artSubFam_purchasedBy : Employee of the company that ordered this article sub family which  will be appear in the field and updated dynamically
                artSubFam_validate : Validation option selected by the user
                artSubFam_validates : Different validation option with values : drafted , to_be_validated  and validated
                artSubFam_version : Alpha version of this article sub family
                artSubFam_active : Is the article sub family currently in use?
                enum_purchasedBy : Different employee option gets from the database
                errors : Errors due to a wrong input in the field, given by the controller
            -----------------------------------------------------------*/
            artSubFam_ref: this.reference,
            artSubFam_design: this.designation,
            artSubFam_drawingPath: this.drawingPath,
            artSubFam_purchasedBy: this.purchasedBy,
            artSubFam_version: this.version,
            artSubFam_active: this.active,
            artSubFam_validate: this.validate,
            isInConsultMod: this.consultMod,
            isInModifMod : this.modifMod,
            artSubFam_id: this.$route.params.id,
            errors: {},
            addSuccess: false,
            infos_artSubFam: [],
            loaded: false,
            artSubFamPurchasedBy: "articleFamPurchasedBy",
            ArticleFamilyType: "articleFamType",
            savedAs: this.validate,
            data_artFam_ref: this.art_ref,
            data_artFam_type: this.art_type,
            enum_purchasedBy: [],
            data_artFam_id: this.art_id,
        }
    },
    created() {
        console.log(this.artSubFam_id)
        axios.get('/info/send/articleFamily')
            .then(response => {
                this.infos_artSubFam = response.data;
                this.loaded=true;
            })

        /*Ask for the controller different purchased by option */
        axios.get('/artFam/enum/purchasedBy')
            .then(response => this.enum_purchasedBy = response.data)
            .catch(error => this.errors = error.response.data.errors);
            

    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the art family so that it can be added to the database */
        addArtSubFam(savedAs) {
            if (!this.addSuccess) {
                if (this.data_artFam_type=="COMP"){
                    /*We begin by checking if the data entered by the user are correct*/
                    axios.post('/comp/subFam/verif', {
                        artSubFam_ref: this.data_artFam_ref+this.artSubFam_ref,
                        artSubFam_design: this.artSubFam_design,
                        artSubFam_drawingPath: this.artSubFam_drawingPath,
                        artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                        artSubFam_version: this.artSubFam_version,
                        artSubFam_active: this.artSubFam_active,
                        artSubFam_validate: savedAs,
                        reason:"add"
                    })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                        console.log("verif")
                        this.errors = {};
                        console.log(this.data_artFam_id)
                        axios.post('/comp/subFam/add', {
                            artSubFam_ref: this.data_artFam_ref+this.artSubFam_ref,
                            artSubFam_design: this.artSubFam_design,
                            artSubFam_drawingPath: this.artSubFam_drawingPath,
                            artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                            artSubFam_version: this.artSubFam_version,
                            artSubFam_active: this.artSubFam_active,
                            artSubFam_validate: savedAs,
                            artFam_id: this.data_artFam_id,

                        })
                        /*If the data have been added in the database, we show a success message*/
                        .then(response => {
                            console.log("add")
                            this.addSuccess = true;
                            this.isInConsultMod = true;
                            this.$snotify.success(`CompSubFam ID added successfully and saved as ${savedAs}`);
                            this.artSubFam_id = response.data;
                           /* if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                            }*/
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    })
                    .catch(error => this.errors = error.response.data.errors);
                }else{
                    if (this.data_artFam_type=="RAW"){
                        axios.post('/raw/subFam/verif', {
                            artSubFam_ref: this.data_artFam_ref+this.artSubFam_ref,
                            artSubFam_design: this.artSubFam_design,
                            artSubFam_drawingPath: this.artSubFam_drawingPath,
                            artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                            artSubFam_active: this.artSubFam_active,
                            artSubFam_validate: savedAs,
                            artFam_id: this.data_artFam_id,
                            reason:"add"
                        })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            this.errors = {};
                            console.log("verif")
                            axios.post('/raw/subFam/add', {
                                artSubFam_ref: this.data_artFam_ref+this.artSubFam_ref,
                                artSubFam_design: this.artSubFam_design,
                                artSubFam_drawingPath: this.artSubFam_drawingPath,
                                artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                                artSubFam_active: this.artSubFam_active,
                                artSubFam_validate: savedAs,
                                artFam_id: this.data_artFam_id,

                            })
                            /*If the data have been added in the database, we show a success message*/
                            .then(response => {
                                console.log("add")
                                this.addSuccess = true;
                                this.isInConsultMod = true;
                                this.$snotify.success(`RawSubFam ID added successfully and saved as ${savedAs}`);
                                this.artSubFam_id = response.data;
                                /*if (this.artFam_genRef !== null && this.artFam_genDesign !== null && this.artFam_variablesCharac !== null && this.artFam_variablesCharacDesign !== null) {
                                    this.$emit('generic', this.artFam_ref+'_'+this.artFam_genRef, this.artFam_genDesign, this.artFam_variablesCharac, this.artFam_variablesCharacDesign);
                                }*/
                            })
                            .catch(error => this.errors = error.response.data.errors);
                        })
                        .catch(error => this.errors = error.response.data.errors);
                    }else{
                        if (this.data_artFam_type=="CONS"){
                            axios.post('/cons/subFam/verif', {
                                artSubFam_ref: this.data_artFam_ref+this.artSubFam_ref,
                                artSubFam_design: this.artSubFam_design,
                                artSubFam_drawingPath: this.artSubFam_drawingPath,
                                artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                                artSubFam_active: this.artSubFam_active,
                                artSubFam_validate: savedAs,
                                artFam_id: this.data_artFam_id,
                                artSubFam_version: this.artSubFam_version,
                                reason:"add"
                            })
                            /*If the data are correct, we send them to the controller for add them in the database*/
                            .then(response => {
                                this.errors = {};
                                axios.post('/cons/subFam/add', {
                                    artSubFam_ref: this.data_artFam_ref+"---"+this.artSubFam_ref,
                                    artSubFam_design: this.artSubFam_design,
                                    artSubFam_drawingPath: this.artSubFam_drawingPath,
                                    artSubFam_purchasedBy: this.artSubFam_purchasedBy,
                                    artSubFam_active: this.artSubFam_active,
                                    artSubFam_validate: savedAs,
                                    artFam_id: this.data_artFam_id,
                                    artSubFam_version: this.artSubFam_version,
                                })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.$snotify.success(`ConsSubFam ID added successfully and saved as ${savedAs}`);
                                    this.artSubFam_id = response.data;
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
        updateArtSubFam(savedAs, reason, lifesheet_created) {
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
                artFam_genRef: this.artFam_genRef,
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
                        artFam_genRef: this.artFam_genRef,
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
        checkRef() {
            /*if (this.artFam_genRef !== undefined) {
                let a = this.artFam_genRef.split('_');
                this.artFam_genRef = this.artFam_ref + '_' + a[2];
            } else {
                this.artFam_genRef = this.artFam_ref + '_';
            }*/
        }
    },
    beforeUpdate() {
        if (this.artFam_ref === null && this.artFam_type !== "") {
            this.artFam_ref = 'G_' + this.artFam_type;
        }
        if (this.artFam_genRef !== undefined) {
            let a = this.artFam_genRef.split('_');
            if (a[2] !== undefined) {
                this.artFam_genRef = this.artFam_ref + '_' + a[2];
            } else {
                this.artFam_genRef = this.artFam_ref + '_';
            }
        } else {
            this.artFam_genRef = this.artFam_ref + '_';
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


<!--File name : ArticleFamilyForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div class="articleFamily" v-if="loaded==true">
        <h2 class="titleForm1">Article ID : {{ articleFam_ref }}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
            <InputSelectForm 
                @clearSelectError='clearSelectError' 
                name="articleFam_type" 
                :Errors="errors.articleFam_type"
                label="Article Family Type :" 
                :options="enumArticleFam_type" 
                :selctedOption="articleFam_type"
                :isDisabled="!!isInConsultMod" 
                v-model="articleFam_type"
                :info_text="'Article Family Type'" 
                :id_actual="ArticleFamilyType"/>



            
            
            
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.articleFam_ref"
                name="articleFam_ref" label="Component Reference"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="articleFam_ref"
                :info_text="'Reference of the component'"
                :min="min"
                :max="max"
            />
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.articleFam_design"
                name="articleFam_design" label="Article Designation"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="articleFam_design"
                :info_text="'Designation of the component'"
                :min="min"
                :max="max"
            />
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.articleFam_drawingPath"
                name="articleFam_drawingPath" label="Article Drawing Path"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="articleFam_drawingPath"
                :info_text="'Drawing path of the article'"
                :min="min"
                :max="max"
            />
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.articleFam_variablesCharac"
                name="articleFam_variablesCharac" label="Article Variable Characteristic"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="articleFam_variablesCharac"
                :info_text="'Variables Characteristic of the article'"
                :min="min"
                :max="max"
            />
            <InputTextForm
                :inputClassName="null"
                :Errors="errors.articleFam_version"
                name="articleFam_version" label="Article Version"
                :isDisabled="isInConsultMod"
                isRequired
                v-model="articleFam_version"
                :info_text="'Version of the article'"
                :min="min"
                :max="max"
            />
            <RadioGroupForm
                :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                :name="'Active ?'"
                :label="'Active'"
                :value="articleFam_active"
                :info_text="'Is article currently use?'"
                :inputClassName="null"
                :Errors="errors['Active']"
                :checked-option="true"
            />
            <InputSelectForm 
                @clearSelectError='clearSelectError' 
                name="articleFam_purchasedBy" 
                :Errors="errors.articleFam_purchasedBy"
                label="Article Family Purchased By :" 
                :options="articleFam_purchasedBy" 
                :selctedOption="articleFam_purchasedBy"
                :isDisabled="!!isInConsultMod" 
                v-model="articleFam_purchasedBy"
                :info_text="'Article Family Purchased By'" 
                :id_actual="articleFamPurchasedBy"/>

            <SaveButtonForm v-if="this.articleFam_type=='COMP' && this.addSuccess==false"
                ref="saveButton"
                @add="addCompFam"
                @update="updateCompFam"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
                :savedAs="validate"/>

            <!--<SaveButtonForm v-if="this.articleFam_type=='CONS' && this.addSuccess==false"
                ref="saveButton"
                @add="addConsFam"
                @update="updateConsFam"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
                :savedAs="validate"/>

            <SaveButtonForm v-if="this.articleFam_type=='RAW' && this.addSuccess==false"
                ref="saveButton"
                @add="addRawFam"
                @update="updateRawFam"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
                :savedAs="validate"/>-->
        </form>
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
        SuccessAlert
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
            type: String
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
        version: {
            type: String
        },
        validate: {
            type: String
        },
        active: {
            type: Boolean
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
                articleFam_ref : Reference of the article family which  will be appear in the field and updated dynamically
                articleFam_design : Designation of the article family which  will be appear in the field and updated dynamically
                articleFam_drawingPath : Path to the directory containing the drawing representing the article family which  will be appear in the field and updated dynamically
                articleFam_purchasedBy : Employee of the company that ordered this article family which  will be appear in the field and updated dynamically
                articleFam_variablesCharac : Variable characteristic(s) of this article family which  will be appear in the field and updated dynamically
                articleFam_validate : Validation option selected by the user
                articleFam_validates : Different validation option with values : drafted , to_be_validated  and validated
                articleFam_version : Alpha version of this article family
                articleFam_active : Is the article currently in use?
                enum_purchasedBy : Different employee option gets from the database
                errors : Errors due to a wrong input in the field, given by the controller
            -----------------------------------------------------------*/
            articleFam_ref: this.reference,
            articleFam_design: this.designation,
            articleFam_drawingPath: this.drawingPath,
            articleFam_purchasedBy: this.purchasedBy,
            articleFam_variablesCharac: this.variablesCharac,
            articleFam_version: this.version,
            articleFam_active: this.active,
            articleFam_validate: this.validate,
            isInConsultMod: this.consultMod,
            isInModifMod : this.modifMod,
            enum_purchasedBy: [],
            articleFam_id: this.$route.params.id,
            errors: {},
            addSuccess: false,
            infos_articleFam: [],
            loaded: false,
            articleFamPurchasedBy: "articleFamPurchasedBy",
            ArticleFamilyType: "articleFamType",
            articleFam_type:"",
            enumArticleFam_type: [
                {id: 'articleFam_type', value: "COMP", text: 'COMP'},
                {id: 'articleFam_type', value: "RAW", text: 'RAW'},
                {id: 'articleFam_type', value: "CONS", text: 'CONS'}
            ],
            savedAs: this.validate
        }
    },
    created() {
        this.loaded=true
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the article family so that it can be added to the database */
        addCompFam(savedAs) {
            if (!this.addSuccess) {
                /*We begin by checking if the data entered by the user are correct*/
                console.log("before verif")
                console.log("ref"+this.articleFam_ref)
                console.log("design"+this.articleFam_design)
                console.log("drawingPath"+this.articleFam_drawingPath)
                console.log("purchasedBy"+this.articleFam_purchasedBy)
                console.log("variablesCharac"+this.articleFam_variablesCharac)
                console.log("version"+this.articleFam_version)
                console.log("active"+this.articleFam_active)
                console.log("validate"+savedAs)
                axios.post('/comp/family/verif', {
                    compFam_ref: this.articleFam_ref,
                    compFam_design: this.articleFam_design,
                    compFam_drawingPath: this.articleFam_drawingPath,
                    compFam_purchasedBy: this.articleFam_purchasedBy,
                    compFam_variablesCharac: this.articleFam_variablesCharac,
                    compFam_version: this.articleFam_version,
                    compFam_active: this.articleFam_active,
                    compFam_validate: savedAs,
                })
                    /*If the data are correct, we send them to the controller for add them in the database*/
                    .then(response => {
                         console.log("after verif")
                        this.errors = {};
                            axios.post('/comp/family/add', {
                                compFam_ref: this.articleFam_ref,
                                compFam_design: this.articleFam_design,
                                compFam_drawingPath: this.articleFam_drawingPath,
                                compFam_purchasedBy: this.articleFam_purchasedBy,
                                compFam_variablesCharac: this.articleFam_variablesCharac,
                                compFam_version: this.articleFam_version,
                                compFam_active: this.articleFam_active,
                                compFam_validate: savedAs,

                            })
                                /*If the data have been added in the database, we show a success message*/
                                .then(response => {
                                     console.log("after add")
                                    this.$refs.SuccessAlert.showAlert(`CompFam ID added successfully and saved as ${savedAs}`);
                                    this.addSuccess = true;
                                    this.isInConsultMod = true;
                                    this.articleFam_id = response.data;
                                    this.$emit('CompFamID', this.articleFam_id);
                                })
                                .catch(error => this.errors = error.response.data.errors);
                        })
                    .catch(error => this.errors = error.response.data.errors);
            }
        },
        /*Sending to the controller all the information about the article family  so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param articleSheet_created */
        updateCompFam(savedAs, reason, articleSheet_created) {
            /*We begin by checking if the data entered by the user are correct*/
            axios.post('/compFam/verif', {

            })
                /*If the data are correct, we send them to the controller for update data in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => `/compFam/update/${id}`;
                    axios.post(consultUrl(this.articleFam_id), {
                       
                    })
                        .then(response => {
                            const id = this.articleFam_id;
                            /*We test if an article sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (articleSheet_created == true) {
                                axios.post(`/history/add/compFam/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            /*If the data have been updated in the database, we show a success message*/
                            this.$refs.SuccessAlert.showAlert(`CompFam ID card updated successfully and saved as ${savedAs}`);
                            this.articleFam_validate = savedAs;
                        })
                        .catch(error => this.errors = error.response.data.errors);
                })
                .catch(error => this.errors = error.response.data.errors);
        },
        /*Clears all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
        clearAllError() {
            console.log("ERROR:", this.errors)
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

form {
    margin: 20px;
    margin-bottom: 50px;
}

.container {
    margin-top: 50px;
}
</style>

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
                :info_text="'Article Family Type'" 
                :id_actual="ArticleFamilyType"/>
            
            
            
            <div v-if="artFam_type!=''"> 
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_ref"
                    name="artFam_ref" label="Component Reference"
                    :isDisabled="this.isInConsultMod"
                    isRequired
                    v-model="artFam_ref"
                    :info_text="'Reference of the component'"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_design"
                    name="artFam_design" label="Article Designation"
                    :isDisabled="isInConsultMod"
                    isRequired
                    v-model="artFam_design"
                    :info_text="'Designation of the component'"
                    :min="3"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_drawingPath"
                    name="artFam_drawingPath" label="Article Drawing Path"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_drawingPath"
                    :info_text="'Drawing path of the article'"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_variablesCharac"
                    name="artFam_variablesCharac" label="Article Variable Characteristic"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_variablesCharac"
                    :info_text="'Variables Characteristic of the article'"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_version"
                    name="artFam_version" label="Article Version"
                    :isDisabled="isInConsultMod"
                    v-model="artFam_version"
                    :info_text="'Version of the art'"
                    :max="4"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    :name="'Active ?'"
                    :label="'Active'"
                    :value="artFam_active"
                    :info_text="'Is article currently use?'"
                    :inputClassName="null"
                    :Errors="errors['Active']"
                    :checked-option="true"
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
                    :info_text="'Article Family Purchased By'" 
                    :id_actual="artFamPurchasedBy"/>

                <SaveButtonForm v-if="this.addSuccess==false"
                    ref="saveButton"
                    @add="addArtFam"
                    @update="updateArtFam"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.isInModifMod"
                    :savedAs="validate"/>
            </div>
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
            artFam_version: this.version,
            artFam_storageCondition : this.storageCondition,
            artFam_active: this.active,
            artFam_validate: this.validate,
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
            artFam_type:"",
            enumArticleFam_type: [
                {id: 'artFam_type', value: "COMP", text: 'COMP'},
                {id: 'artFam_type', value: "RAW", text: 'RAW'},
                {id: 'artFam_type', value: "CONS", text: 'CONS'}
            ],
            savedAs: this.validate
        }
    },
    created() {
        /*Ask for the controller different purchased by option */
        axios.get('/artFam/enum/purchasedBy')
            .then(response => this.enum_purchasedBy = response.data)
            .catch(error => console.log(error));

        /*Ask for the controller different storage condition option */
        axios.get('/artFam/enum/storageCondition')
            .then(response => this.enum_storageCondition = response.data)
            .catch(error => console.log(error));
            this.loaded=true
    },

    /*--------Declaration of the different methods:--------*/
    methods: {
        /*Sending to the controller all the information about the art family so that it can be added to the database */
        addArtFam(savedAs) {
            if (!this.addSuccess) {
                /*We begin by checking if the data entered by the user are correct*/
                console.log("before verif")
                console.log("ref"+this.artFam_ref)
                console.log("design"+this.artFam_design)
                console.log("drawingPath"+this.artFam_drawingPath)
                console.log("purchasedBy"+this.artFam_purchasedBy)
                console.log("variablesCharac"+this.artFam_variablesCharac)
                console.log("version"+this.artFam_version)
                console.log("active"+this.artFam_active)
                console.log("validate"+savedAs)
                if (this.artFam_type=="COMP"){
                    axios.post('/comp/family/verif', {
                        artFam_ref: this.artFam_ref,
                        artFam_design: this.artFam_design,
                        artFam_drawingPath: this.artFam_drawingPath,
                        artFam_purchasedBy: this.artFam_purchasedBy,
                        artFam_variablesCharac: this.artFam_variablesCharac,
                        artFam_version: this.artFam_version,
                        artFam_active: this.artFam_active,
                        artFam_validate: savedAs,
                    })
                        /*If the data are correct, we send them to the controller for add them in the database*/
                        .then(response => {
                            console.log("after verif")
                            this.errors = {};
                                axios.post('/comp/family/add', {
                                    artFam_ref: this.artFam_ref,
                                    artFam_design: this.artFam_design,
                                    artFam_drawingPath: this.artFam_drawingPath,
                                    artFam_purchasedBy: this.artFam_purchasedBy,
                                    artFam_variablesCharac: this.artFam_variablesCharac,
                                    artFam_version: this.artFam_version,
                                    artFam_active: this.artFam_active,
                                    artFam_validate: savedAs,

                                })
                                    /*If the data have been added in the database, we show a success message*/
                                    .then(response => {
                                        console.log("after add")
                                        console.log(response.data)
                                        this.addSuccess = true;
                                        this.isInConsultMod = true;
                                        console.log(this.addSuccess)
                                        console.log(this.isInConsultMod)
                                        this.$snotify.success(`CompFam ID added successfully and saved as ${savedAs}`);
                                        this.artFam_id = response.data;
                                        this.$emit('CompFamID', this.artFam_id);
                                    })
                                    .catch(error => this.errors = error.response.data.errors);
                            })
                        .catch(error => this.errors = error.response.data.errors);
                }
            }
        },
        /*Sending to the controller all the information about the art family  so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        updateArtFam(savedAs, reason, artSheet_created) {
            /*We begin by checking if the data entered by the user are correct*/
            axios.post('/compFam/verif', {

            })
                /*If the data are correct, we send them to the controller for update data in the database*/
                .then(response => {
                    this.errors = {};
                    const consultUrl = (id) => `/compFam/update/${id}`;
                    axios.post(consultUrl(this.artFam_id), {
                       
                    })
                        .then(response => {
                            const id = this.artFam_id;
                            /*We test if an article sheet has been already created*/
                            /*If it's the case we create a new enregistrement of history for saved the reason of the update*/
                            if (artSheet_created == true) {
                                axios.post(`/history/add/compFam/${id}`, {
                                    history_reasonUpdate: reason,
                                });
                                window.location.reload();
                            }
                            /*If the data have been updated in the database, we show a success message*/
                            this.$refs.SuccessAlert.showAlert(`CompFam ID card updated successfully and saved as ${savedAs}`);
                            this.artFam_validate = savedAs;
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

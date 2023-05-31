<!--File name : ArticleSubFamilyForm.vue -->
<!--Creation date : 31 May 2023-->
<!--Update date : 31 May 2023 -->
<!--Vue Component of the Form of the article sub family who call all the input component-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container" >
                <div class="row">
                    <div class="col-12">
                        <h3>Article Sub Family</h3>
                    </div>
                </div>
                <InputTextForm
                    v-model="data_drawingPath"
                    :Errors="errors.artFam_drawingPath"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :max="255"
                    label="Article Drawing Path"
                    name="artFam_drawingPath"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_variablesCharac"
                    name="artFam_variablesCharac"
                    label="Article Variable Characteristic"
                    :isDisabled="isInConsultMod"
                    v-model="data_varCharac"
                    :info_text="null"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_variablesCharacDesign"
                    name="artFam_variablesCharacDesign"
                    label="Article Variable Characteristic Designations"
                    :isDisabled="isInConsultMod"
                    v-model="data_varCharacDesign"
                    :info_text="null"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_genRef"
                    name="artFam_genDesign"
                    label="Member Generic Reference"
                    :isDisabled="isInConsultMod"
                    v-model="data_genRef"
                    :info_text="null"
                    :max="255"
                />
                <InputTextForm
                    :inputClassName="null"
                    :Errors="errors.artFam_genDesign"
                    name="artFam_genDesign"
                    label="Member Generic Designation"
                    :isDisabled="isInConsultMod"
                    v-model="data_genDesign"
                    :info_text="null"
                    :max="255"
                />
                <SaveButtonForm v-if="this.addSucces==false"
                                ref="saveButton"
                                @add="addArticleMember"
                                @update="updateArticleMember"
                                :consultMod="this.isInConsultMod"
                                :modifMod="this.isInModifMod"
                                :savedAs="validate"/>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>

</template>

<script>
/*Importation of the other Components who will be used here*/
import InputTextForm from '../../../input/SW03/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";



export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        SucessAlert,
        InputSelectForm

    },
    /*--------Declaration of the different props:--------
        name : File name given by the database we will put this data in the corresponding field as default value
        location : File location given by the database we will put this data in the corresponding field as default value
        validate: Validation option of the file
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        divClass: Class name of this file form
        id: ID of an already created file
        eq_id: ID of the equipment in which the file will be added
    ---------------------------------------------------*/
    props: {
        validate: {
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
        art_id: {
            type: Number
        },
        art_type:{
            type: String
        },
        genRef: {
            type: String
        },
        genDesign: {
            type: String
        },
        varCharac: {
            type: String
        },
        varCharacDesign: {
            type: String
        },
        drawingPath: {
            type: String
        },
    },
    /*--------Declaration of the different returned data:--------
    file_name: Name of the file who will be appeared in the field and updated dynamically
    file_location: Location of the file who will be appeared in the field and updated dynamically
    file_validate: Validation option of the file
    file_id: ID oh this file
    equipment_id_add: ID of the equipment in which the file will be added
    equipment_id_update: ID of the equipment in which the file will be updated
    errors: Object of errors in which will be stores the different error occurred when adding in database
    addSucces: Boolean who tell if this file has been added successfully
    isInConsultedMod: data of the consultMod prop
-----------------------------------------------------------*/
    data() {
        return {
            art_id_add: this.art_id,
            artFam_type:this.art_type,
            art_id_update: this.$route.params.id,
            errors: {},
            addSucces: false,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            loaded: false,
            infos_artFamMember: [],
            data_genRef: this.genRef,
            data_genDesign: this.genDesign,
            data_varCharac: this.varCharac,
            data_varCharacDesign: this.varCharacDesign,
            data_drawingPath: this.drawingPath,
        }
    },
    created(){
        axios.get('/info/send/article_member')
            .then(response => {
                this.infos_artFamMember = response.data;
                this.loaded=true;
            })
            .catch(error => {
            });
    },
    methods: {
        /*Sending to the controller all the information about the article member so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param artSheet_created */
        addArticleMember(savedAs, reason, artSheet_created) {
            if (!this.addSucces) {
                /*ID of the equipment in which the file will be added*/
                let id;
                /*If the user is not in modification mode, we set the id with the value of data art_id_add*/
                if (!this.modifMod) {
                    id = this.art_id_add;
                    /*Else the user is in the update menu, we allocate to the id the value of the id get in the url*/
                } else {
                    id = this.art_id_update;
                }
                /*We begin by checking if the data entered by the user are correct*/
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        @param savedAs Value of the validation option: drafted, to_be_validated or validated
        @param reason The reason of the modification
        @param lifesheet_created */
        updateArticleMember(savedAs, reason, artSheet_created) {
            /*The First post to verify if all the fields are filled correctly,
            The name, location and validate option are sent to the controller*/
            let id = this.artFamMember_id;
            /*We begin by checking if the data entered by the user are correct*/
        },
        clearSelectError(value) {
            delete this.errors[value];
        },
    },
}
</script>

<style>
</style>

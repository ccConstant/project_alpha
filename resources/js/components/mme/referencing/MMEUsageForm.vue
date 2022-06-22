<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.usg_measurementType" name="usg_measurementType" label="Measurement type :" :isDisabled="!!isInConsultedMod" v-model="usg_measurementType" />
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.usg_precision" name="usg_precision" label="Precision :" :isDisabled="!!isInConsultedMod" v-model="usg_precision" />
                
                
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this preventive maintenance operation doesn't have a id the addEquipmentUsage is called function else the updateEquipmentUsage function is called -->
                    <div v-if="this.usg_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentUsage" @update="updateEquipmentUsage" :consultMod="this.isInConsultedMod" :savedAs="usg_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentUsage" @update="updateEquipmentUsage" :consultMod="this.isInConsultedMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.usg_id!==null && reformMod==false ">
                        <div v-if="usg_refromDate!=null" >
                            <p>Refrom by {{usg_refromBy}} at {{usg_refromDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addEquipmentUsage" @update="updateEquipmentUsage" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="usg_validate" :Errors="errors.usg_delete" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && usg_refromDate===null">
                        <ReformComponentButton :reformBy="usg_refromBy" :reformDate="usg_refromDate" :reformMod="this.isInReformMod" @reformOk="reformComponent"/>
                    </div>


                </div>       
            </form>
            <div v-if="this.usg_id!==null && modifMod==false & consultMod==false && import_id==null " >
                <ReferenceAMMEPrecaution :eq_id="this.eq_id" :usg_id="this.usg_id" :riskForEq="false"/>
            </div>
            <div v-else-if="this.usg_id!==null && modifMod==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :importedRisk="importedOpRisk" :eq_id="this.eq_id" :usg_id="this.usg_id" :riskForEq="false" :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
            <div v-else-if="loaded==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :importedRisk="importedOpRisk" :eq_id="this.eq_id" :usg_id="this.usg_id" :riskForEq="false" :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
            <ErrorAlert ref="errorAlert"/>
        </div>

    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import ErrorAlert from '../../alert/ErrorAlert.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import ReferenceAMMEPrecaution from './ReferenceAMMEPrecaution.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../button/ReformComponentButton.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        SaveButtonForm,
        InputTextAreaForm,
        ReferenceAMMEPrecaution,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert


    },
}
</script>

<style>

</style>
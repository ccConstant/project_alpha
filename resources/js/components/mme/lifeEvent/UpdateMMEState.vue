<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <ErrorAlert ref="errorAlert"/>
            <form class="container state-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInModifMod">
                    <h2 >Update the state</h2>
                </div>
                <div v-else>
                    <h2>Change the State</h2>
                    <div v-if="isInConsultMod==false">
                        <InputTextForm  inputClassName="form-control w-50" name="current_state" label="Current state :" :isDisabled="true"   v-model="current_state" />
                        <InputTextForm  inputClassName="form-control w-50" name="current_startDate" label="Current state start Date :" :isDisabled="true"   v-model="current_startDate"  />

                    </div>
                </div>
                <div v-if="state_id!==undefined || isInConsultMod==true">
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name" isDisabled :selctedOption="state_name" v-model="state_name" />
                </div>
                <div v-else>
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name"  :selctedOption="state_name" v-model="state_name" />
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.state_remarks" name="state_remarks" label="Remarks :" :isDisabled="!!isInConsultMod" v-model="state_remarks" />
                <div class="input-group">
                    <InputTextForm  inputClassName="form-control" :Errors="errors.state_startDate" name="state_startDate" label="Start date :" :isDisabled="true" v-model="state_startDate" />
                    <InputDateForm @clearDateError="clearDateError" inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod" v-model="selected_startDate" />
                </div>
                <RadioGroupForm label="is Ok?:" :options="isOkOptions" :Errors="errors.state_isOk" :checkedOption="state_isOk" :isDisabled="!!isInConsultMod" v-model="state_isOk" /> 
                <SaveButtonForm :is_state="true" v-if="this.addSucces==false" @add="addMmeState" @update="updateMmeState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
            </form>


            <div v-if="state_name=='Downgraded'">
                <div v-if="state_validate=='validated'">
                    <div v-if="!isEmpty(mme_idCard)">
                        <MmeIdForm :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
                            :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
                            :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                            consultMod/>
                    </div>
                    <div v-else>
                        <RadioGroupForm label="Do you want to reference a new equipment ?:" :options="radioOption" v-model="new_eq"/>
                        <MmeIdForm disableImport="true" v-if="new_eq==true" :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
                        :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
                        :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                        consultMod/>


                        <EquipmentIDForm :disableImport="true" v-if="new_eq==true" :state_id="state_id"
                        :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                        :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                        :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                        :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"/>
                    </div>
                </div>
            </div>
            <div v-if="state_name=='Reform' && isInModifMod==true && isInConsultMod==false">

                <div v-if="deleteEqOrMmeRight==true">
                    <button type="button" class="btn btn-danger" @click="warningDelete()">Delete the equipment</button>
                </div>
                <div v-else>
                    <b-button disabled variant="primary">Delete the equipment</b-button> 
                    <p class="enum_add_right_red"> You dont have the right to delete the equipment.</p>
                </div>
                <b-modal :id="`modal-deleteWarning-${_uid}`" @ok="deleteEquipment()"  >
                    <p class="my-4">Are you sur you want to delete </p>
                </b-modal>
            </div>

        </div>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->

    </div>
</template>

<script>
export default {

}
</script>

<style>

</style>
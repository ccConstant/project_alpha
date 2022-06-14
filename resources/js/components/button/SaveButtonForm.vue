<template>
    <div class="save_button">
        <div v-if="!reformMod">
            <div v-if="!modifMod">
                <div v-if="!consultMod">

                    <div v-if="in_life_sheet_data==true">
                        <div class="save_button_draft_tbv" v-if="updateDataInDraftRight==true">
                            <b-button variant="primary" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</b-button>
                            <b-button variant="primary" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >save as to be validated</b-button>
                        </div>
                        <div class="save_button_draft_tbv" v-else>
                            <b-button variant="primary" disabled >save as draft</b-button>
                            <b-button variant="primary" disabled >save as to be validated</b-button>
                        </div>
                        <div class="save_button_validated" v-if="validateDescriptiveLifeSheetDataRight==true">
                            <b-button variant="primary" @click="$bvModal.show(`modal-validated-${_uid}`)" >save as validated</b-button>
                        </div>
                        <div class="save_button_validated" v-else>
                            <b-button variant="primary" disabled>save as validated</b-button>
                        </div>
                        <p v-if="updateDataInDraftRight==false" class="text-danger">You don't have the right to save as draft or as to be validated</p>
                        <p v-if="validateDescriptiveLifeSheetDataRight == false" class="text-danger">You don't have the right to save as validated </p>
                        
                    </div>
                    <div v-else>
                        <div class="save_button_draft_tbv">
                            <b-button variant="primary" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</b-button>
                            <b-button variant="primary" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >save as to be validated</b-button>
                        </div>
                        <div class="save_button_validated" v-if="validateOtherDataRight==true">
                            <b-button variant="primary" @click="$bvModal.show(`modal-validated-${_uid}`)" >save as validated</b-button>
                        </div>
                        <div class="save_button_validated" v-else>
                            <b-button variant="primary" disabled>save as validated</b-button>
                        </div>
                        <p class="text-danger" v-if="validateOtherDataRight == false">You don't have the right to save as validated </p>
                    </div>

                    

                    <b-modal :id="`modal-draft-${_uid}`"  @ok="addDraft">
                        <p class="my-4">Are you sure you want to save as draft </p>
                        <p v-if="AddinUpdate==false" class="my-4">You will no longer be able to modify the information on this page</p>
                    </b-modal>

                    <b-modal  :id="`modal-to_be_validated-${_uid}`"  @ok="addToBeValidated">
                        <p class="my-4">Are you sure you want to save as to be validated </p>
                        <p v-if="AddinUpdate==false" class="my-4">You will no longer be able to modify the information on this page</p>
                    </b-modal>


                    <b-modal :id="`modal-validated-${_uid}`"  @ok="addValidated">
                        <p class="my-4">Are you sure you want to save as validated</p>
                        <p v-if="AddinUpdate==false" class="my-4">You will no longer be able to modify the information on this page</p>
                    </b-modal>
                </div>
                <div v-else>
                    <p v-if="this.saveAll==false">Saved as : {{savedAs}}</p>
                 

                </div>
            </div>
        <!--Else if the form is in a modif mode we call this div with update option-->
            <div v-else-if="modifMod">
                <div v-if="savedAs=='validated' ">
                    <div class="save_button_draft_tbv">
                        <button class="save btn btn-primary" disabled type="button"  value="drafted" @click="update($event)" >save as draft</button>
                        <button class="save btn btn-primary" disabled type="button" value="to_be_validated" @click="update($event)" >to be validated</button>   
                    </div>
                    <div v-if="validateDescriptiveLifeSheetDataRight==true" class="save_button_validated"  >
                        <button class="save btn btn-primary" type="button" value="validated" @click="update($event)" >validate</button>
                    </div>
                    <div v-else class="save_button_validated">
                        <button class="save btn btn-primary" type="button" disabled>validate</button>
                    </div>
                </div>
                <div v-else>
                    <div v-if="in_life_sheet_data==true">
                        <div class="save_button_draft_tbv" v-if="updateDataInDraftRight==true"  >
                            <button class="save btn btn-primary" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                            <button class="save btn btn-primary" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                        </div>
                        <div class="save_button_draft_tbv" v-else>
                            <button class="save btn btn-primary" type="button" disabled >save as draft</button>
                            <button class="save btn btn-primary" type="button" disabled >to be validated</button>
                        </div>
                        <div v-if="validateDescriptiveLifeSheetDataRight==true" class="save_button_validated"  >
                            <button class="save btn btn-primary" type="button" value="validated" @click="update($event)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-primary" type="button" disabled >validate</button>
                        </div>
                    </div>
                    <div v-else>
                        <div class="save_button_draft_tbv">
                            <button class="save btn btn-primary" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                            <button class="save btn btn-primary" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                        </div>
                        <div v-if="validateOtherDataRight==true" class="save_button_validated"  >
                            <button class="save btn btn-primary" type="button" value="validated" @click="update($event)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-primary" type="button" disabled >validate</button>
                        </div>
                    </div>

                    
                </div>
                    <div v-if="saveAll!==true">
                        <p >Actually saved as : {{savedAs}}</p>
                        <div v-if="hasError(this.Errors)" class="error_savebutton">
                            <p>{{this.Errors[0]}}</p>
                        </div>
                        <p class="text-danger" v-if="updateDataInDraftRight==false">You don't have the right to save as draft or as to be validated</p>
                        <p class="text-danger" v-if="validateDescriptiveLifeSheetDataRight == false">You don't have the right to save as validated </p>
                        <p class="text-danger" v-if="validateOtherDataRight == false">You don't have the right to save as validated </p>

                    </div>    
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        modifMod:{
            type :Boolean
        },
        consultMod:{
            type: Boolean
        },
        AddinUpdate:{
            type: Boolean
        },
        savedAs:{
            type:String
        },
        saveAll:{
            type:Boolean
        },
        Errors:{
            type:Array,
            default: () => ([])
        },
        reformMod:{
            type:Boolean,
            default:false
        },
        in_life_sheet:{
            type:Boolean,
            default:true
        }
    },
    data(){
        return{
            sucess:false,
            updateDataInDraftRight:this.$userId.user_updateDataInDraftRight,
            validateDescriptiveLifeSheetDataRight:this.$userId.user_validateDescriptiveLifeSheetDataRight,
            validateOtherDataRight:this.$userId.user_validateOtherDataRight,
            in_life_sheet_data:this.in_life_sheet
        }
    },
    methods:{
        addDraft(){
            this.$emit('add',"drafted")
        },
        addToBeValidated(){
            this.$emit('add',"to_be_validated")
        },
        addValidated(){
            this.$emit('add',"validated")
        },
        update(e){
            this.$emit('update',e.target.value)
        },
        hasError(errors){
            return(errors.length>0);
        }
    }

}
</script>

<style lang="scss">
    .save_button{
        padding-top: 10px;
        .save_button_draft_tbv{
            display: inline-block;
        }
        .save_button_validated{
            display: inline-block;
        }
    };
    .error_savebutton{
        p{
            color:red
        }
    }


</style>
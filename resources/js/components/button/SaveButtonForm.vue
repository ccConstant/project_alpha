<template>
    <div class="save_button">
        <div v-if="!reformMod">
            <div v-if="!modifMod">
                <div v-if="!consultMod">
                    <div v-if="is_op_data==true">
                        <div v-if="makeEqOpValidationRight==true" class="save_button_validated">
                            <button class="save btn btn-primary" type="button"  value="drafted" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</button>
                            <button class="save btn btn-primary" type="button" value="to_be_validated" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >to be validated</button>
                            <button class="save btn btn-primary" type="button" value="validated" @click="$bvModal.show(`modal-validated-${_uid}`)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-primary" type="button" disabled >save as draft</button>
                            <button class="save btn btn-primary" type="button" disabled>to be validated</button>
                            <button class="save btn btn-primary" type="button" disabled >validate</button>
                        </div>
                            <p class="text-danger" v-if="makeEqOpValidationRight == false">You don't have the right to record an operation</p>
                    </div>

                    <div v-else-if="is_op_data!=true && is_state_data!=true">
                        <div v-if="this.lifesheet_created!=true">
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
                        <div v-else-if="this.lifesheet_created==true">
                            <div v-if="updateDescriptiveLifeSheetDataSignedRight==true" class="save_button_draft_tbv">
                                <b-button variant="primary" @click="$bvModal.show(`modal-approved-add-drafted${_uid}`)" >save as draft</b-button>
                                <b-button variant="primary" @click="$bvModal.show(`modal-approved-add-to-be-validated${_uid}`)" >save as to be validated</b-button>
                                <b-button variant="primary" @click="$bvModal.show(`modal-approved-add-validated${_uid}`)" >save as validated</b-button>
                                
                                <b-modal :id="`modal-approved-add-to-be-validated${_uid}`"  @ok="addToBeValidated()">
                                <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                <input v-model="reason" placeholder="Reason for the update" />
                                </b-modal>
                                <b-modal :id="`modal-approved-add-drafted${_uid}`"  @ok="addDraft()">
                                    <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                    <input v-model="reason" placeholder="Reason for the update" />
                                </b-modal>
                                <b-modal :id="`modal-approved-add-validated${_uid}`"  @ok="addValidated()">
                                    <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                    <input v-model="reason" placeholder="Reason for the update" />
                                </b-modal>
                            </div>
                            
                            <div v-else>
                                <b-button variant="primary" disabled>save as draft</b-button>
                                <b-button variant="primary"  disabled>save as to be validated</b-button>
                                <b-button variant="primary" disabled>save as validated</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <b-button variant="primary" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</b-button>
                        <b-button variant="primary" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >save as to be validated</b-button>
                        <b-button variant="primary" @click="$bvModal.show(`modal-validated-${_uid}`)" >save as validated</b-button>
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
                <div v-if="savedAs=='validated'">
                    <div v-if="this.lifesheet_created==true">
                        <div class="save_button_draft_tbv" v-if="updateDescriptiveLifeSheetDataSignedRight==true"  >
                            <button class="save btn btn-info" type="button"  disabled>save as draft</button>
                            <button class="save btn btn-info" type="button" disabled>to be validated</button>
                            <button class="save btn btn-info" type="button" @click="$bvModal.show(`modal-approved-validated${_uid}`)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-info" type="button"  disabled >save as draft</button>
                            <button class="save btn btn-info" type="button" disabled >to be validated</button>
                            <button class="save btn btn-info" type="button" disabled >validate</button>
                        </div>
                    </div>
                    <div v-else>
                        <div class="save_button_draft_tbv">
                            <button class="save btn btn-info" disabled type="button"  value="drafted" >save as draft</button>
                            <button class="save btn btn-info" disabled type="button" value="to_be_validated" >to be validated</button>   
                        </div>
                        <div v-if="user_updateDataValidatedButNotSignedRight==true" class="save_button_validated"  >
                            <button class="save btn btn-info" type="button" value="validated" @click="update($event)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-info" type="button" disabled>validate</button>
                        </div>
                    </div>
                    <b-modal :id="`modal-approved-validated${_uid}`"  @ok="updateValidated()">
                        <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                        <input v-model="reason" placeholder="Reason for the update" />
                    </b-modal>
                </div>
                <div v-else>
                    <div v-if="is_op_data!=true && is_state_data!=true">
                        <div v-if="this.lifesheet_created!=true">
                            <div class="save_button_draft_tbv" v-if="updateDataInDraftRight==true"  >
                                <button class="save btn btn-info" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                                <button class="save btn btn-info" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                            </div>
                            <div class="save_button_draft_tbv" v-else>
                                <button class="save btn btn-info" type="button" disabled >save as draft</button>
                                <button class="save btn btn-warning" type="button" disabled >to be validated</button>
                            </div>
                            <div v-if="user_updateDataValidatedButNotSignedRight==true" class="save_button_validated"  >
                                <button class="save btn btn-info" type="button" value="validated" @click="update($event)" >validate</button>
                            </div>
                            <div v-else class="save_button_validated">
                                <button class="save btn btn-info" type="button" disabled >validate</button>
                            </div>
                        </div>
                        <div v-else-if="this.lifesheet_created==true">
                            <div class="save_button_update" v-if="updateDescriptiveLifeSheetDataSignedRight==true"  >
                                <button class="save btn btn-info" type="button"  value="drafted" @click="$bvModal.show(`modal-approved-drafted${_uid}`)" >save as draft</button>
                                <button class="save btn btn-info" type="button" value="to_be_validated" @click="$bvModal.show(`modal-approved-to-be-validated${_uid}`)" >to be validated</button>
                                <button class="save btn btn-info" type="button" value="validated" @click="$bvModal.show(`modal-approved-validated${_uid}`)" >validate</button>
                            </div>
                            <div v-else class="save_button_validated">
                                <button class="save btn btn-info" type="button"  disabled >save as draft</button>
                                <button class="save btn btn-info" type="button" disabled >to be validated</button>
                                <button class="save btn btn-info" type="button" disabled >validate</button>
                            </div>

                            <b-modal :id="`modal-approved-to-be-validated${_uid}`"  @ok="updateToBeValidated()">
                                <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                <input v-model="reason" placeholder="Reason for the update" />
                            </b-modal>
                            <b-modal :id="`modal-approved-drafted${_uid}`"  @ok="updateDrafted()">
                                <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                <input v-model="reason" placeholder="Reason for the update" />
                            </b-modal>
                            <b-modal :id="`modal-approved-validated${_uid}`"  @ok="updateValidated()">
                                <p class="my-4">Are you sure you want to update this approved data? You will have to sign them again </p>
                                <input v-model="reason" placeholder="Reason for the update" />
                            </b-modal>
                        </div>

                    </div>
                    <div v-else-if="is_op_data==true">
                        <div v-if="makeEqOpValidationRight==true" class="save_button_validated"  >
                            <button class="save btn btn-info" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                            <button class="save btn btn-info" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                            <button class="save btn btn-info" type="button" value="validated" @click="update($event)" >validate</button>
                        </div>
                        <div v-else class="save_button_validated">
                            <button class="save btn btn-info" type="button" disabled >save as draft</button>
                            <button class="save btn btn-info" type="button" disabled>to be validated</button>
                            <button class="save btn btn-info" type="button" disabled >validate</button>
                        </div>
                    </div>
                    <div v-else>
                        <button class="save btn btn-info" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                        <button class="save btn btn-info" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                        <button class="save btn btn-info" type="button" value="validated" @click="update($event)" >validate</button>
                    </div>

                    
                </div>
                    <div v-if="saveAll!==true">
                        <p >Actually saved as : {{savedAs}}</p>
                        <p class="text-danger" v-if="updateDescriptiveLifeSheetDataSignedRight==false && lifesheet_created==true">You don't have the right to update a signed life sheet</p>
                        <p class="text-danger" v-if="updateDataInDraftRight==false  && lifesheet_created==false">You don't have the right to save as draft or as to be validated</p>
                        <p class="text-danger" v-if="user_updateDataValidatedButNotSignedRight == false && lifesheet_created==false">You don't have the right to save as validated  </p>
                        <p class="text-danger" v-if="makeEqOpValidationRight == false && is_op_data==true">You don't have the right to record an operation </p>
                    </div>    
            </div>
            <div v-if="hasError(this.Errors)" class="error_savebutton">
                <p>{{this.Errors[0]}}</p>
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
        is_op:{
            type:Boolean,
            default:false
        },
        is_state:{
            type:Boolean,
            default:false
        },
    },
    data(){
        return{
            sucess:false,
            updateDataInDraftRight:this.$userId.user_updateDataInDraftRight,
            user_updateDataValidatedButNotSignedRight:this.$userId.user_updateDataValidatedButNotSignedRight,
            validateDescriptiveLifeSheetDataRight:this.$userId.user_validateDescriptiveLifeSheetDataRight,
            makeEqOpValidationRight:this.$userId.user_makeEqOpValidationRight,
            updateDescriptiveLifeSheetDataSignedRight:this.$userId.user_updateDescriptiveLifeSheetDataSignedRight,
            lifesheet_created:this.$route.query.signed,
            is_op_data:this.is_op,
            is_state_data:this.is_state,
            reason:""

        }
    },
    created(){
        console.log("signed"+this.$route.query.signed)
    },
    methods:{

        //for approved data
        addDraft(){
            this.$emit('add',"drafted", this.reason, this.lifesheet_created)
        },
        addToBeValidated(){
            this.$emit('add',"to_be_validated", this.reason, this.lifesheet_created)
        },
        addValidated(){
            this.$emit('add',"validated", this.reason, this.lifesheet_created)
        },
        updateValidated(){
            this.$emit('update','validated', this.reason, this.lifesheet_created)
        },
        updateToBeValidated(){
            this.$emit('update','to_be_validated', this.reason, this.lifesheet_created)
        },
        updateDrafted(){
            this.$emit('update','drafted', this.reason, this.lifesheet_created)
        },

        add(e){
            this.$emit('add',e.target.value)
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
        .save_button_update{
            color:green;
        }
        .save_button_group{
            color:orange;
        }
    };
    .error_savebutton{
        p{
            color:red
        }
    }


</style>
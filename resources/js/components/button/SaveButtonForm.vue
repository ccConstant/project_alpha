<template>
    <div class="save_button">
        <div v-if="!modifMod">
            <div v-if="!consultMod">
                <b-button variant="primary" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</b-button>
                <b-modal :id="`modal-draft-${_uid}`"  @ok="addDraft">
                    <p class="my-4">Are you sure you want to save as draft </p>
                    <p v-if="AddinUpdate==false" class="my-4">You will no longer be able to modify the information on this page</p>
                </b-modal>

                <b-button variant="primary" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >save as to be validated</b-button>
                <b-modal  :id="`modal-to_be_validated-${_uid}`"  @ok="addToBeValidated">
                    <p class="my-4">Are you sure you want to save as to be validated </p>
                    <p v-if="AddinUpdate==false" class="my-4">You will no longer be able to modify the information on this page</p>
                </b-modal>

                <b-button variant="primary" @click="$bvModal.show(`modal-validated-${_uid}`)" >save as validated</b-button>
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
            <button class="save btn btn-primary" type="button"  value="drafted" @click="update($event)" >save as draft</button>
                <button class="save btn btn-primary" type="button" value="to_be_validated" @click="update($event)" >to be validated</button>
                <button class="save btn btn-primary" type="button" value="validated" @click="update($event)" >validate</button>
                <div v-if="saveAll!==true">
                    <p >Actually saved as : {{savedAs}}</p>
                    <div v-if="hasError(this.Errors)" class="error_savebutton">
                        <p>{{this.Errors[0]}}</p>
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
    },
    data(){
        return{
            sucess:false
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
    };
    .error_savebutton{
        p{
            color:red
        }
    }


</style>
<template>
    <div class="save_button">
        <div v-if="!modifMod">
            <div v-if="!consultMod">
                
                <b-button variant="primary" @click="$bvModal.show(`modal-draft-${_uid}`)" >save as draft</b-button>
                <b-modal :id="`modal-draft-${_uid}`"  @ok="addDraft">
                    <p class="my-4">Are you sure you want to save as draft you will no longer be able to modify the information on this page</p>
                </b-modal>

                <b-button variant="primary" @click="$bvModal.show(`modal-to_be_validated-${_uid}`)" >save as to be validated</b-button>
                <b-modal  :id="`modal-to_be_validated-${_uid}`"  @ok="addToBeValidated">
                    <p class="my-4">Are you sure you want to save as to be validated you will no longer be able to modify the information on this page</p>
                </b-modal>

                <b-button variant="primary" @click="$bvModal.show(`modal-validated-${_uid}`)" >save as validated</b-button>
                <b-modal :id="`modal-validated-${_uid}`"  @ok="addValidated">
                    <p class="my-4">Are you sure you want to save as validated you will no longer be able to modify the information on this page</p>
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
        savedAs:{
            type:String
        },
        saveAll:{
            type:Boolean
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
        }
    }

}
</script>

<style lang="scss">
    .save_button{
        padding-top: 10px;
    }

</style>
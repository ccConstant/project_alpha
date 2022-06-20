<template>
    <div>
        <div v-if="lifesheet_created!=true">
            <div v-if=" validationMode!='' && deleteDataNotValidatedLinkedToEqOrMmeRight!=true">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Delete</b-button>
                <p class="enum_add_right_red"> You dont have the right to delete an element.</p>
            </div>
            <div v-else-if="validationMode=='validated' && deleteDataValidatedLinkedToEqOrMmeRight!=true">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Delete</b-button>
                <p class="enum_add_right_red"> You dont have the right to delete valideted element.</p>
            </div>
            <div v-else>
                <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete_component-${_uid}`)" >Delete</b-button>
            </div>
        </div>
        <div v-else-if="lifesheet_created==true">
            <div v-if="deleteDataSignedLinkedToEqOrMmeRight!=true">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Delete</b-button>
                <p class="enum_add_right_red"> You dont have the right to delete a signed element.</p>
            </div>
            <div v-else>
                <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete_component-${_uid}`)" >Delete</b-button>
            </div>
        </div>

       
        <b-modal :id="`modal-delete_component-${_uid}`"  @ok="deleteConfirmation">
            <p class="my-4">Are you sure you want to delete 
                if the information is imported it can no longer be re-imported
            </p>
        </b-modal>
        <div v-if="hasError(this.Errors)" class="error_deleteButton">
            <p>{{this.Errors[0]}}</p>
        </div>
    </div>
    
</template>

<script>
export default {
    props:{
        consultMod:{
            type:Boolean
        },
        Errors:{
            type:Array,
            default: () => ([])
        },
        validationMode:{
            type:String,
            default:''
        }
    },
    methods:{
        deleteConfirmation(){
            this.$emit('deleteOk',"")
        },
        hasError(errors){
            return(errors.length>0);
        }
    },
    data(){
        return{
            deleteDataValidatedLinkedToEqOrMmeRight:this.$userId.user_deleteDataValidatedLinkedToEqOrMmeRight,
            deleteDataNotValidatedLinkedToEqOrMmeRight:this.$userId.user_deleteDataNotValidatedLinkedToEqOrMmeRight,
            deleteDataSignedLinkedToEqOrMmeRight:this.$userId.user_deleteDataSignedLinkedToEqOrMmeRight,
            lifesheet_created:this.$route.query.signed,
        }
        
    }

}
</script>

<style lang="scss">
    .error_deleteButton{
        p{
            color:red
        }
    }
</style>
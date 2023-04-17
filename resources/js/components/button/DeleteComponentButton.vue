<!--File name : DeleteComponentButton.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the delete button-->

<template>
    <div v-if="unlink_value==false">
        <div v-if="lifesheet_created!=true">
            <div v-if=" validationMode!='' && (deleteDataNotValidatedLinkedToEqOrMmeRight!=true && deleteDataValidatedLinkedToEqOrMmeRight!=true)">
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
                <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete-approved${_uid}`)" >Delete</b-button>
            </div>
            <b-modal :id="`modal-delete-approved${_uid}`"  @ok="deleteConfirmation">
                <p class="my-4">Are you sure you want to delete this approved data? You will have to sign all the data again </p>
                <input v-model="reason" placeholder="Reason for the delete" />
            </b-modal>
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
    <div v-else>
        <div v-if="lifesheet_created!=true">
            <div v-if=" validationMode!='' && (deleteDataNotValidatedLinkedToEqOrMmeRight!=true && deleteDataValidatedLinkedToEqOrMmeRight!=true)">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Unlink</b-button>
                <p class="enum_add_right_red"> You dont have the right to unlink an element.</p>
            </div>
            <div v-else-if="validationMode=='validated' && deleteDataValidatedLinkedToEqOrMmeRight!=true">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Unlink</b-button>
                <p class="enum_add_right_red"> You dont have the right to unlink valideted element.</p>
            </div>
            <div v-else>
                <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete_component-${_uid}`)" >Unlink</b-button>
            </div>
        </div>
        <div v-else-if="lifesheet_created==true">
            <div v-if="deleteDataSignedLinkedToEqOrMmeRight!=true">
                <b-button v-if="this.consultMod==false" variant="danger" disabled >Unlink</b-button>
                <p class="enum_add_right_red"> You dont have the right to unlink a signed element.</p>
            </div>
            <div v-else>
                <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete-approved${_uid}`)" >Unlink</b-button>
            </div>
            <b-modal :id="`modal-delete-approved${_uid}`"  @ok="deleteConfirmation">
                <p class="my-4">Are you sure you want to unlink this approved data? You will have to sign all the data again </p>
                <input v-model="reason" placeholder="Reason for the unlink" />
            </b-modal>
        </div>
        <b-modal :id="`modal-delete_component-${_uid}`"  @ok="deleteConfirmation">
            <p class="my-4">Are you sure you want to unlink
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
        },
        unlink:{
            type:Boolean,
            default:false
        }
    },
    methods:{
        deleteConfirmation(){
            this.$emit('deleteOk', this.reason, this.lifesheet_created);
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
            reason:'',
            unlink_value:this.unlink
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

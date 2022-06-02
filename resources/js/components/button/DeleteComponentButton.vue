<template>
    <div>
        <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete_component-${_uid}`)" >Delete</b-button>
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
        }
    },
    methods:{
        deleteConfirmation(){
            this.$emit('deleteOk',"")
        },
        hasError(errors){
            return(errors.length>0);
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
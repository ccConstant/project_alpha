<template>
    <div>
        <b-button v-if="this.consultMod==false" variant="danger" @click="$bvModal.show(`modal-delete_component-${_uid}`)" >Delete</b-button>
        <b-modal :id="`modal-delete_component-${_uid}`"  @ok="deleteConfirmation">
            <p class="my-4">Are you sure you want to delete 
                if the information is imported it can no longer be re-imported
            </p>
        </b-modal>
    </div>
    
</template>

<script>
export default {
    props:{
        consultMod:{
            type:Boolean
        }
    },
    methods:{
        deleteConfirmation(){
            this.$emit('deleteOk',"")
        }
    }

}
</script>

<style>

</style>
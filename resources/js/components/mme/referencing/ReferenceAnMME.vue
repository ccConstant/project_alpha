<template>
  <div>
        <MmeIdForm @MMEID="put_mme_id" @importFromMMEID="put_import_id"/>
  </div>
</template>

<script>
import MmeIdForm from './MmeIdForm.vue'
export default {
    components: {
        MmeIdForm
    },
    data(){
        return{
            //Id de l'equipement qui vien detre crée
            eq_id:null,
            //Id de l'equiepement depuis lequel importer
            importation_id:null
        }
    },
    methods:{
        put_mme_id(value){
            this.eq_id=value;
        },
        put_import_id(value){
            this.importation_id=value;
        }
    }

}
</script>

<style>

</style>
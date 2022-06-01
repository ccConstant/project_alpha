<template>
  <div>
    <EquipmentIDForm @EqID="put_equipment_id" @importFromEqID="put_import_id"/>
    <div v-if="this.eq_id!=null">
      <ReferenceADim :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceAPow :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceASpecProc :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceAUsage :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceAFile :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceAPrvMtnOp :eq_id="this.eq_id" :import_id="this.importation_id"/>
      <ReferenceARisk :eq_id="this.eq_id" :import_id="this.importation_id" :riskForEq="true"/>



    </div>

  </div>
  

</template>

<script>
import EquipmentDimForm from './EquipmentDimForm.vue'
import EquipmentIDForm from './EquipmentIDForm.vue'
import ReferenceADim from './ReferenceADim.vue'
import ReferenceAPow from './ReferenceAPow.vue'
import ReferenceASpecProc from './ReferenceASpecProc.vue'
import ReferenceAUsage from './ReferenceAUsage.vue'
import ReferenceAFile from './ReferenceAFile.vue'
import ReferenceAPrvMtnOp from './ReferenceAPrvMtnOp.vue'
import ReferenceARisk from './ReferenceARisk.vue'




export default {
  components: {
    EquipmentIDForm,
    EquipmentDimForm,
    ReferenceADim,
    ReferenceAPow,
    ReferenceASpecProc,
    ReferenceAUsage,
    ReferenceAFile,
    ReferenceAPrvMtnOp,
    ReferenceARisk
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
    put_equipment_id(value){
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


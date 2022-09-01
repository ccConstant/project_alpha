<template>
    <div>
        <EquipmentIDForm @EqID="put_equipment_id" @importFromEqID="put_import_id"/>
        <div v-if="this.eq_id!=null">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Equipment Dimension(s)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceADim :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Equipment Power source(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceAPow :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Equipment Special Procces
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <ReferenceASpecProc :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Equipment Usage
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <ReferenceAUsage :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Equipment File
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <ReferenceAFile :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            Equipment's due Risk(s)
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">

                            <ReferenceARisk :eq_id="this.eq_id" :import_id="this.importation_id" :riskForEq="true"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Equipment Preventive maintenace operation
                        </button>
                    </h2>
                    <div id="collapseSeven"  class="accordion-collapse collapse" aria-labelledby="headingSeven">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOp :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingHeight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            Equipment Associated MME (Create a new MME )
                        </button>
                    </h2>
                    <div id="collapseEight"  class="accordion-collapse collapse" aria-labelledby="headingHeight">
                        <div class="accordion-body">
                            <ReferenceAMme :eq_id="this.eq_id"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            Equipment Associated MME (Adding already created MMEs to an equipment )
                        </button>
                    </h2>
                    <div id="collapseNine"  class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <ReferenceAMmeList :eq_id="this.eq_id"/>
                        </div>
                    </div>
                </div>
            </div>
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
import ReferenceAMme from './ReferenceAMme.vue'
import ReferenceAMmeList from './ReferenceAMmeList.vue'




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
    ReferenceARisk,
    ReferenceAMme,
    ReferenceAMmeList,
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


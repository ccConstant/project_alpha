<!--File name :ReferenceAnEquipment.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to reference an other equipment in the equipment-->

<template>
    <div>
        <EquipmentIDForm @EqID="put_equipment_id" @importFromEqID="put_import_id"/>
        <div v-if="this.eq_id!=null">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Equipment Characteristic(s)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceADim :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment Power source(s)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAPow :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment Special Procces
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceASpecProc :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment Usage
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAUsage :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment File
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAFile :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment's due Risk(s)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">

                            <ReferenceARisk :eq_id="this.eq_id" :import_id="this.importation_id" :riskForEq="true"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment Preventive maintenance operation
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOp :eq_id="this.eq_id" :import_id="this.importation_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Equipment Associated MME
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAMme :eq_id="this.eq_id"/>
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
    },
    data() {
        return {
            /*ID of the equipment created*/
            eq_id: null,
            /*ID of the equipment imported*/
            importation_id: null
        }
    },
    methods: {
        put_equipment_id(value) {
            this.eq_id = value;
        },
        put_import_id(value) {
            this.importation_id = value;
        }
    }
}
</script>

<style>
</style>


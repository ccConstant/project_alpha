<!--File name : EquipmentUpdate.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the update of equipment-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="equipment_update">
            <h1>Equipment Update</h1>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Equipment Id Card
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <EquipmentIDForm :construct="eq_idCard.eq_constructor"
                                             :externalReference="eq_idCard.eq_externalReference"
                                             :internalReference="eq_idCard.eq_internalReference"
                                             :location="eq_idCard.eq_location"
                                             :mass="eq_idCard.eq_mass"
                                             :massUnit="eq_idCard.eq_massUnit" :mobility="eq_idCard.eq_mobility"
                                             :name="eq_idCard.eq_name"
                                             :remarks="eq_idCard.eq_remarks" :serialNumber="eq_idCard.eq_serialNumber"
                                             :set="eq_idCard.eq_set" :type="eq_idCard.eq_type"
                                             :validate="eq_idCard.eq_validate"
                                             modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Characteristic(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceADim :importedDim="eq_dimensions" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Power source(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAPow :importedPow="eq_powers" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Special Process
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceASpecProc :importedSpProc="eq_spProc" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Usage
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAUsage :importedUsg="eq_usg" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment File
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAFile :importedFile="eq_file" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment's due Risk(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">

                            <ReferenceARisk :importedRisk="eq_risk" :riskForEq="true" modifMod/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Preventive maintenance Operation
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOp :importedPrvMtnOp="eq_prvMtnOp" modifMod/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Associated MME
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMme :importedMme="eq_mme" modifMod/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ReferenceADim from '../referencing/ReferenceADim.vue'
import ReferenceAPow from '../referencing/ReferenceAPow.vue'
import ReferenceASpecProc from '../referencing/ReferenceASpecProc.vue'
import ReferenceAUsage from '../referencing/ReferenceAUsage.vue'
import ReferenceAFile from '../referencing/ReferenceAFile.vue'
import ReferenceAPrvMtnOp from '../referencing/ReferenceAPrvMtnOp.vue'
import ReferenceARisk from '../referencing/ReferenceARisk.vue'
import ReferenceAMme from '../referencing/ReferenceAMme.vue'


export default {
    components: {
        EquipmentIDForm,
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
            eq_id: this.$route.params.id,
            eq_idCard: null,
            eq_dimensions: null,
            eq_powers: null,
            eq_spProc: null,
            eq_usg: null,
            eq_file: null,
            eq_prvMtnOp: null,
            eq_risk: null,
            loaded: false,
            eq_mme: null,
            eq_lifeSheetCreated: false,
        }
    },

    created() {
        const consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then(response => {
                this.eq_idCard = response.data
                this.$router.push({
                    name: "url_eq_update",
                    params: {id: this.eq_id},
                    query: {signed: response.data.eq_lifeSheetCreated}
                }).catch(() => {
                });
                if (response.data.eq_lifeSheetCreated == true &&
                    (this.$userId.user_updateDescriptiveLifeSheetDataSignedRight != true &&
                        this.$userId.user_deleteDataSignedLinkedToEqOrMmeRight != true)) {
                    this.eq_lifeSheetCreated = response.data.eq_lifeSheetCreated;
                    this.$router.push({name: "home"})
                }
                const consultUrlDim = (id) => `/dimension/send/${id}`;
                axios.get(consultUrlDim(this.eq_id))
                    .then(response => {
                        this.eq_dimensions = response.data;
                        const consultUrlPow = (id) => `/power/send/${id}`;
                        axios.get(consultUrlPow(this.eq_id))
                            .then(response => {
                                this.eq_powers = response.data;
                                const consultUrlSpProc = (id) => `/spProc/send/${id}`;
                                axios.get(consultUrlSpProc(this.eq_id))
                                    .then(response => {
                                        if (response.data == "") {
                                            this.eq_spProc = [];
                                        } else {
                                            this.eq_spProc = response.data;
                                        }
                                        const consultUrlUsg = (id) => `/usage/send/${id}`;
                                        axios.get(consultUrlUsg(this.eq_id))
                                            .then(response => {
                                                this.eq_usg = response.data;
                                                const consultUrlFile = (id) => `/file/send/${id}`;
                                                axios.get(consultUrlFile(this.eq_id))
                                                    .then(response => {
                                                        this.eq_file = response.data;
                                                        const consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
                                                        axios.get(consultUrlPrvMtnOp(this.eq_id))
                                                            .then(response => {
                                                                this.eq_prvMtnOp = response.data;
                                                                const consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
                                                                axios.get(consultUrlRisk(this.eq_id))
                                                                    .then(response => {
                                                                        this.eq_risk = response.data;
                                                                        const consultUrlMme = (id) => `/mme/send/${id}`;
                                                                        axios.get(consultUrlMme(this.eq_id))
                                                                            .then(response => {
                                                                                this.eq_mme = response.data
                                                                                this.loaded = true;
                                                                            }).catch(error => {
                                                                        });
                                                                    }).catch(error => {
                                                                });
                                                            }).catch(error => {
                                                        });
                                                    }).catch(error => {
                                                });
                                            }).catch(error => {
                                        });
                                    }).catch(error => {
                                });
                            }).catch(error => {
                        });
                    }).catch(error => {
                });
            }).catch(error => {
        });
    }
}


</script>

<style lang="scss">
.equipment_update {
    .green_card {
        background-color: #b0f2b6;
    }

    .yellow_card {
        background-color: lightyellow;
    }

    h1 {
        text-align: center;
    }
}

</style>

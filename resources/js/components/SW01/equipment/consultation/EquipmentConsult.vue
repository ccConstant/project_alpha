<!--File name : EquipmentConsult.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the consultation of equipments-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="equipment_consultation">
            <ErrorAlert ref="errorAlert"/>
            <SuccesAlert ref="successAlert"/>
            <h1>Equipment Consultation</h1>
            <ValidationButton :Errors="errors" :eq_id="eq_id" :validationMethod="validationMethod"
                              @ValidatePressed="Validate"/>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Equipment Id
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
                                             consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_dimensions.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Characteristic(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceADim :importedDim="eq_dimensions" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_powers.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Power source(s)
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAPow :importedPow="eq_powers" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_spProc.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Special Process
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceASpecProc :importedSpProc="eq_spProc" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_usg.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Usage
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAUsage :importedUsg="eq_usg" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_file.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment File
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAFile :importedFile="eq_file" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_risk.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment's due Risk(s)

                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceARisk :importedRisk="eq_risk" :riskForEq="true" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_prvMtnOp.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Preventive Maintenance Operation
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOp :importedPrvMtnOp="eq_prvMtnOp" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="eq_mme.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Associated MME
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMme :importedMme="eq_mme" consultMod/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ReferenceADim from '../referencing/ReferenceADim.vue'
import ReferenceAPow from '../referencing/ReferenceAPow.vue'
import ReferenceASpecProc from '../referencing/ReferenceASpecProc.vue'
import ReferenceAUsage from '../referencing/ReferenceAUsage.vue'
import ReferenceAFile from '../referencing/ReferenceAFile.vue'
import ReferenceAPrvMtnOp from '../referencing/ReferenceAPrvMtnOp.vue'
import ReferenceARisk from '../referencing/ReferenceARisk.vue'
import ReferenceAMme from '../referencing/ReferenceAMme.vue'
import ValidationButton from '../../../button/ValidationButton.vue'


export default {
    components: {
        EquipmentIDForm,
        ReferenceADim,
        ReferenceAPow,
        ReferenceAMme,
        ReferenceASpecProc,
        ReferenceAUsage,
        ReferenceAFile,
        ReferenceAPrvMtnOp,
        ReferenceARisk,
        ValidationButton,
        ErrorAlert,
        SuccesAlert
    },
    data() {
        return {
            eq_id: this.$route.params.id.toString(),
            eq_idCard: null,
            eq_dimensions: null,
            eq_powers: null,
            eq_spProc: null,
            eq_usg: null,
            eq_file: null,
            eq_prvMtnOp: null,
            eq_risk: null,
            eq_mme: null,
            loaded: false,
            validationMethod: this.$route.query.method,
            errors: []
        }
    },

    created() {
        const consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then(response => {
                this.eq_idCard = response.data;
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
                                                                this.eq_prvMtnOp = response.data
                                                                const consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
                                                                axios.get(consultUrlRisk(this.eq_id))
                                                                    .then(response => {
                                                                        this.eq_risk = response.data
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
    },
    methods: {
        Validate() {
            if (this.validationMethod == 'technical' && this.$userId.user_makeTechnicalValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else if (this.validationMethod == 'quality' && this.$userId.user_makeQualityValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                const validVerifUrl = (id) => `/equipment/verifValidation/${id}`;
                axios.post(validVerifUrl(this.eq_id), {
                    reason: this.validationMethod
                })
                    .then(response => {
                        const techVeriftUrl = (id) => `/equipment/validation/${id}`;
                        axios.post(techVeriftUrl(this.eq_id), {
                            reason: this.validationMethod,
                            enteredBy_id: this.$userId.id
                        })
                            .then(response => {
                                this.$refs.successAlert.showAlert(`${this.validationMethod} made succesfully`);
                                this.$router.replace({name: "url_eq_list"})
                            })
                            //If the controller sends errors we put it in the errors object
                            .catch(error => this.errors = error.response.data.errors);
                    })
                    //If the controller sends errors we put it in the errors object
                    .catch(error => {
                        this.errors = error.response.data.errors
                    });
            }

        }
    },
    mounted() {
        if (this.validationMethod == 'technical' && this.$userId.user_makeTechnicalValidationRight != true) {
            this.$refs.errorAlert.showAlert("You don't have the technical right to validate this equipment");
            this.$router.replace({name: "url_eq_list"})
        } else if (this.validationMethod == 'quality' && this.$userId.user_makeQualityValidationRight != true) {
            this.$refs.errorAlert.showAlert("You don't have the quality right to validate this equipment");
            this.$router.replace({name: "url_eq_list"})
        }
    }
}


</script>

<style lang="scss">
.technical_validate_button {
    display: block;
    margin: auto auto 15px;
}

.quality_validate_button {
    display: block;
    margin: auto auto 15px;
}

.equipment_consultation {
    h1 {
        text-align: center;
    }
}


</style>

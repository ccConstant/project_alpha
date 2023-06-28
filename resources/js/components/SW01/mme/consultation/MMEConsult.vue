<!--File name : MMEConsult.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to consult a IDCard of a MME-->

<template>
    <div>
        <ErrorAlert ref="errorAlert"/>
        <SuccesAlert ref="successAlert"/>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="mme_consultation">
            <h1>MME Consultation</h1>
            <div v-if="this.mme_eq.length>0">
                <p class="mme_linked"> The MME is linked to the equipment:
                    <router-link :to="{name:'url_eq_consult',params:{id:this.mme_eq[0].eq_id} }">
                        {{ this.mme_eq[0].eq_internalReference }}
                    </router-link>
                </p>
            </div>
            <ValidationButton :Errors="errors" :mme_id="mme_id" :validationMethod="validationMethod"
                              @ValidatePressed="Validate"/>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            MME Id Card
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <MmeIdForm :construct="mme_idCard.mme_constructor"
                                       :externalReference="mme_idCard.mme_externalReference"
                                       :internalReference="mme_idCard.mme_internalReference"
                                       :location="mme_idCard.mme_location"
                                       :name="mme_idCard.mme_name"
                                       :remarks="mme_idCard.mme_remarks" :serialNumber="mme_idCard.mme_serialNumber"
                                       :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                                       consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="mme_files.length>0" class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            MME File
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMMEFile :importedFile="mme_files" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="mme_verifs.length>0" class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            MME Verification
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMMEVerif :importedVerif="mme_verifs" consultMod/>
                        </div>
                    </div>
                </div>
                <div v-if="mme_usages.length>0" class="accordion-item">
                    <h2 id="headingFour" class="accordion-header">
                        <button aria-controls="collapseFour" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                            MME Usage
                        </button>
                    </h2>
                    <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ReferenceAMMEUsage :importedUsage="mme_usages" consultMod/>
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
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ValidationButton from '../../../button/ValidationButton.vue'
import ReferenceAMMEFile from '../referencing/ReferenceAMMEFile.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from '../referencing/ReferenceAMMEUsage.vue'

export default {
    components: {
        MmeIdForm,
        ErrorAlert,
        SuccesAlert,
        ValidationButton,
        ReferenceAMMEFile,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage
    },
    data() {
        return {
            mme_id: this.$route.params.id.toString(),
            mme_idCard: null,
            mme_files: null,
            mme_verifs: null,
            mme_usages: null,
            loaded: false,
            validationMethod: this.$route.query.method,
            errors: [],
            mme_eq: [],
        }
    },
    created() {
        let consultUrl = (id) => `/mme/eq_linked/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then(response => {
                this.mme_eq = response.data;
                consultUrl = (id) => `/mme/${id}`;
                axios.get(consultUrl(this.mme_id))
                    .then(response => {
                        this.mme_idCard = response.data;
                        consultUrl = (id) => `/file/send/mme/${id}`;
                        axios.get(consultUrl(this.mme_id))
                            .then(response => {
                                this.mme_files = response.data;
                                consultUrl = (id) => `/verifs/send/${id}`;
                                axios.get(consultUrl(this.mme_id))
                                    .then(response => {
                                        this.mme_verifs = response.data;
                                        consultUrl = (id) => `/mme_usage/send/${id}`;
                                        axios.get(consultUrl(this.mme_id))
                                            .then(response => {
                                                this.mme_usages = response.data
                                                this.loaded = true
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
                const validVerifUrl = (id) => `/mme/verifValidation/${id}`;
                axios.post(validVerifUrl(this.mme_id), {
                    reason: this.validationMethod,
                    user_id: this.$userId.id
                }).then(response => {
                    const techVeriftUrl = (id) => `/mme/validation/${id}`;
                    axios.post(techVeriftUrl(this.mme_id), {
                        reason: this.validationMethod,
                        enteredBy_id: this.$userId.id
                    }).then(response => {
                        this.$refs.successAlert.showAlert(`${this.validationMethod} made succesfully`);
                        this.$router.replace({name: "url_mme_list"})
                    }).catch(error => this.errors = error.response.data.errors);

                }).catch(error => {
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
    margin: auto;
    margin-bottom: 15px;

}

;

.quality_validate_button {
    display: block;
    margin: auto;
    margin-bottom: 15px;
}

;

.mme_consultation {
    h1 {
        text-align: center;
    }
}

;

.mme_linked {
    font-size: 18px;
    font-style: italic;
    color: deepskyblue;


}

;


</style>

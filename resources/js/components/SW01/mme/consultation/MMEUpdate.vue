<!--File name : MMEUpdate.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to update the IDCard of a MME-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="mme_update">
            <h1>MME Update</h1>
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
                                       :internalReference="mme_idCard.mme_internalReference" :location="mme_idCard.mme_location"
                                       :name="mme_idCard.mme_name"
                                       :remarks="mme_idCard.mme_remarks" :serialNumber="mme_idCard.mme_serialNumber"
                                       :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                                       modifMod/>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 id="headingTwo" class="accordion-header">
                            <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                    data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                                MME File
                            </button>
                        </h2>
                        <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <ReferenceAMMEFile :importedFile="mme_files" modifMod/>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 id="headingThree" class="accordion-header">
                            <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                    data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                                MME Verification
                            </button>
                        </h2>
                        <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <ReferenceAMMEVerif :importedVerif="mme_verifs" modifMod/>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 id="headingFour" class="accordion-header">
                            <button aria-controls="collapseFour" aria-expanded="false" class="accordion-button collapsed"
                                    data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                                MME Usage
                            </button>
                        </h2>
                        <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <ReferenceAMMEUsage :importedUsage="mme_usages" modifMod/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ReferenceAMMEFile from '../referencing/ReferenceAMMEFile.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from '../referencing/ReferenceAMMEUsage.vue'

export default {
    components: {
        MmeIdForm,
        ReferenceAMMEFile,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage
    },

    data() {
        return {
            mme_id: this.$route.params.id,
            mme_idCard: null,
            mme_files: null,
            mme_verifs: null,
            loaded: false,
        }
    },
    created() {
        let consultUrl = (id) => `/mme/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then(response => {
                this.mme_idCard = response.data
                this.$router.push({
                    name: "url_mme_update",
                    params: {id: this.mme_id},
                    query: {signed: response.data.mme_lifeSheetCreated}
                }).catch(() => {
                });
                if (response.data.mme_lifeSheetCreated == true &&
                    (this.$userId.user_updateDescriptiveLifeSheetDataSignedRight != true &&
                        this.$userId.user_deleteDataSignedLinkedTommeOrMmeRight != true)) {
                    this.mme_lifeSheetCreated = response.data.mme_lifeSheetCreated;
                    this.$router.push({name: "home"})
                }
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
                                        this.mme_usages = response.data;
                                        this.loaded = true;
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
.mme_update {
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

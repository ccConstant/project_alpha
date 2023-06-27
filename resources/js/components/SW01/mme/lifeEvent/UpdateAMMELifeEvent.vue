<!--File name : UpdateAMMELifeEvent.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to update a life event linked to one MME-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Verifications record
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <div v-if="mme_verifRlz.length>0" class="all_preventive_ope">
                                <h3>Recorded verifications</h3>
                                <li v-for="(verifRlz,index) in mme_verifRlz " :key="index" class="list-group-item">
                                    <div>
                                        Operation Number : {{ verifRlz.verif_number }} <br>
                                        Expected Result : {{ verifRlz.verif_expectedResult }} <br>
                                        Non Compliance Limit : {{ verifRlz.verif_nonComplianceLimit }} <br>
                                        Description : {{ verifRlz.verif_description }} <br>
                                        Protocol : {{ verifRlz.verif_protocol }} <br>
                                        Report Number : {{ verifRlz.verifRlz_reportNumber }} <br>
                                        Comment : {{ verifRlz.verifRlz_comment }} <br>
                                    </div>
                                    <div v-if="verifRlz.verifRlz_isPassed==true">
                                        Passed : Yes
                                    </div>
                                    <div v-else>
                                        Passed : false
                                    </div>
                                    <div>
                                        Start Date : {{ verifRlz.verifRlz_startDate }} <br>
                                        End date : {{ verifRlz.verifRlz_endDate }} <br>
                                        Saved as : {{ verifRlz.verifRlz_validate }} <br>
                                        Entered by : {{ verifRlz.enteredBy_lastName }}
                                        {{ verifRlz.enteredBy_firstName }} <br>
                                    </div>
                                    <div v-if="verifRlz.realizedBy_lastName!=null">
                                        Realized by : {{ verifRlz.realizedBy_firstName }}
                                        {{ verifRlz.realizedBy_lastName }} <br>
                                    </div>
                                    <div v-else>
                                        Realized by : - <br>
                                    </div>
                                    <div v-if="verifRlz.approvedBy_lastName!=null">
                                        Approved by : {{ verifRlz.approvedBy_firstName }}
                                        {{ verifRlz.approvedBy_lastName }} <br>
                                    </div>
                                    <div v-else>
                                        Approved by : - <br>
                                    </div>

                                    <MMEVerifModal :approvedBy_lastName="verifRlz.approvedBy_lastName" :mme_id="mme_id"
                                                   :realizedBy_lastName="verifRlz.realizedBy_lastName"
                                                   :state_id="state_id"
                                                   :verifRlz_comment="verifRlz.verifRlz_comment"
                                                   :verifRlz_endDate="verifRlz.verifRlz_endDate"
                                                   :verifRlz_id="verifRlz.id"
                                                   :verifRlz_isPassed="verifRlz.verifRlz_isPassed"
                                                   :verifRlz_reportNumber="verifRlz.verifRlz_reportNumber"
                                                   :verifRlz_startDate="verifRlz.verifRlz_startDate"
                                                   :verifRlz_validate="verifRlz.verifRlz_validate"
                                                   :verif_description="verifRlz.verif_description" :verif_expectedResult="verifRlz.verif_expectedResult"
                                                   :verif_id="verifRlz.verif_id" :verif_nonComplianceLimit="verifRlz.verif_nonComplianceLimit"
                                                   :verif_number="verifRlz.verif_number"
                                                   :verif_protocol="verifRlz.verif_protocol"
                                                   @okReload="reloadPage"
                                    />
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Curative maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <div v-if="mme_curMtnOp.length>0" class="all_preventive_ope">
                                <h3>Recorded Curative maintenance operation</h3>
                                <li v-for="(curMtnOp,index) in mme_curMtnOp " :key="index" class="list-group-item">
                                    <div>
                                        Operation Number : {{ curMtnOp.curMtnOp_number }} <br>
                                        Report Number : {{ curMtnOp.curMtnOp_reportNumber }} <br>
                                        Description : {{ curMtnOp.curMtnOp_description }} <br>
                                        Start Date : {{ curMtnOp.curMtnOp_startDate }} <br>
                                        End date : {{ curMtnOp.curMtnOp_endDate }} <br>
                                        Saved as : {{ curMtnOp.curMtnOp_validate }} <br>
                                        Entered by : {{ curMtnOp.enteredBy_lastName }}
                                        {{ curMtnOp.enteredBy_firstName }} <br>
                                    </div>
                                    <div v-if="curMtnOp.realizedBy_lastName!=null">
                                        Realized by : {{ curMtnOp.realizedBy_firstName }}
                                        {{ curMtnOp.realizedBy_lastName }} <br>
                                    </div>
                                    <div v-else>
                                        Realized by : - <br>
                                    </div>
                                    <div v-if="curMtnOp.qualityVerifier_lastName!=null">
                                        Quality verifier : {{ curMtnOp.qualityVerifier_lastName }}
                                        {{ curMtnOp.qualityVerifier_firstName }} <br>
                                    </div>
                                    <div v-else>
                                        Quality verifier : - <br>
                                    </div>
                                    <div v-if="curMtnOp.technicalVerifier_lastName!=null">
                                        Technical verifier : {{ curMtnOp.technicalVerifier_lastName }}
                                        {{ curMtnOp.technicalVerifier_firstName }} <br>
                                    </div>
                                    <div v-else>
                                        Technical verifier : - <br>
                                    </div>

                                    <MMECurMtnOpModal :curMtnOp_description="curMtnOp.curMtnOp_description"
                                                      :curMtnOp_endDate="curMtnOp.curMtnOp_endDate"
                                                      :curMtnOp_id="curMtnOp.id"
                                                      :curMtnOp_reportNumber="curMtnOp.curMtnOp_reportNumber"
                                                      :curMtnOp_startDate="curMtnOp.curMtnOp_startDate"
                                                      :curMtnOp_validate="curMtnOp.curMtnOp_validate" :mme_id="mme_id"
                                                      :qualityVerifier_lastName="curMtnOp.qualityVerifier_lastName" :realizedBy_lastName="curMtnOp.realizedBy_lastName"
                                                      :state_id="state_id"
                                                      :technicalVerifier_lastName="curMtnOp.technicalVerifier_lastName"
                                                      @okReload="reloadPage"/>
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MMEVerifModal from './MMEVerifModal.vue'
import MMECurMtnOpModal from './MMECurMtnOpModal.vue'


export default {
    components: {
        MMEVerifModal,
        MMECurMtnOpModal
    },
    data() {
        return {
            mme_curMtnOp: null,
            mme_verifRlz: null,
            loaded: false,
            mme_id: parseInt(this.$route.params.id),
            state_id: parseInt(this.$route.params.state_id)
        }
    },
    created() {
        const consultUrlPrvMtnOpRlz = (id) => `/mme_state/verifRlz/send/${id}`;
        axios.get(consultUrlPrvMtnOpRlz(this.state_id))
            .then(response => {
                this.mme_verifRlz = response.data
            }).catch(error => {
        });

        const consultUrlCurMtnOp = (id) => `/mme_state/curMtnOp/send/${id}`;
        axios.get(consultUrlCurMtnOp(this.state_id))
            .then(response => {
                this.mme_curMtnOp = response.data
                this.loaded = true
            }).catch(error => {
        });
    },
    methods: {
        reloadPage() {
            window.location.reload();
        }
    }
}
</script>

<style>

</style>

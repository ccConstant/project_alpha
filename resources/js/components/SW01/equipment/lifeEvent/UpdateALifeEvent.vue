<!--File name : UpdateALifeEvent.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to update a life event-->

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
                            Preventive maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <div v-if="eq_prvMtnOpRlz.length>0" class="all_preventive_ope">
                                <h3>Recorded Preventive maintenance operation</h3>
                                <li v-for="(prvMtnOpRlz,index) in eq_prvMtnOpRlz " :key="index" class="list-group-item">
                                    <div>
                                        <p>
                                            Operation Number : {{ prvMtnOpRlz.prvMtnOp_number }}
                                        </p>
                                        <p>
                                            Description : {{ prvMtnOpRlz.prvMtnOp_description }}
                                        </p>
                                        <p>
                                            Protocol : {{ prvMtnOpRlz.prvMtnOp_protocol }}
                                        </p>
                                        <p>
                                            Report Number : {{ prvMtnOpRlz.prvMtnOpRlz_reportNumber }}
                                        </p>
                                        <p>
                                            Comments : {{ prvMtnOpRlz.prvMtnOpRlz_comment }}
                                        </p>
                                        <p v-if="prvMtnOpRlz.prvMtnOpRlz_startDate!=null">
                                            Start Date :
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_startDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_startDate.slice(5, 7), prvMtnOpRlz.prvMtnOpRlz_startDate.slice(8)).getDate()
                                            }}
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_startDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_startDate.slice(5, 7) - 1, prvMtnOpRlz.prvMtnOpRlz_startDate.slice(8)).toDateString().slice(4, 7)
                                            }}
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_startDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_startDate.slice(5, 7), prvMtnOpRlz.prvMtnOpRlz_startDate.slice(8)).getFullYear()
                                            }}
                                        </p>
                                        <p v-if="prvMtnOpRlz.prvMtnOpRlz_endDate!=null">
                                            End date :
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_endDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_endDate.slice(5, 7), prvMtnOpRlz.prvMtnOpRlz_endDate.slice(8)).getDate()
                                            }}
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_endDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_endDate.slice(5, 7) - 1, prvMtnOpRlz.prvMtnOpRlz_endDate.slice(8)).toDateString().slice(4, 7)
                                            }}
                                            {{
                                                new Date(prvMtnOpRlz.prvMtnOpRlz_endDate.slice(0, 4), prvMtnOpRlz.prvMtnOpRlz_endDate.slice(5, 7), prvMtnOpRlz.prvMtnOpRlz_endDate.slice(8)).getFullYear()
                                            }}
                                        </p>
                                        <p>
                                            Saved as : {{ prvMtnOpRlz.prvMtnOpRlz_validate }}
                                        </p>
                                        <p>
                                            Entered by : {{ prvMtnOpRlz.enteredBy_firstName }}
                                            {{ prvMtnOpRlz.enteredBy_lastName }}
                                        </p>
                                    </div>
                                    <div v-if="prvMtnOpRlz.realizedBy_lastName!=null">
                                        <p>
                                            Realized by : {{ prvMtnOpRlz.realizedBy_firstName }}
                                            {{ prvMtnOpRlz.realizedBy_lastName }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>
                                            Realized by : -
                                        </p>
                                    </div>
                                    <div v-if="prvMtnOpRlz.approvedBy_lastName!=null">
                                        <p>
                                            Approved by : {{ prvMtnOpRlz.approvedBy_firstName }}
                                            {{ prvMtnOpRlz.approvedBy_lastName }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>
                                            Approved by : -
                                        </p>
                                    </div>
                                    <PrvMtnOpRlzManagmentModal :approvedBy_lastName="prvMtnOpRlz.approvedBy_lastName"
                                                               :eq_id="eq_id"
                                                               :prvMtnOpRlz_endDate="prvMtnOpRlz.prvMtnOpRlz_endDate"
                                                               :prvMtnOpRlz_id="prvMtnOpRlz.id"
                                                               :prvMtnOpRlz_reportNumber="prvMtnOpRlz.prvMtnOpRlz_reportNumber"
                                                               :prvMtnOpRlz_startDate="prvMtnOpRlz.prvMtnOpRlz_startDate"
                                                               :prvMtnOpRlz_validate="prvMtnOpRlz.prvMtnOpRlz_validate"
                                                               :prvMtnOp_description="prvMtnOpRlz.prvMtnOp_description"
                                                               :prvMtnOp_id="prvMtnOpRlz.prvMtnOp_id"
                                                               :prvMtnOp_number="prvMtnOpRlz.prvMtnOp_number" :prvMtnOp_protocol="prvMtnOpRlz.prvMtnOp_protocol"
                                                               :realizedBy_lastName="prvMtnOpRlz.realizedBy_lastName"
                                                               :state_id="state_id"
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
                            <div v-if="eq_curMtnOp.length>0" class="all_preventive_ope">
                                <h3>Recorded Curative maintenance operation</h3>
                                <li v-for="(curMtnOp,index) in eq_curMtnOp " :key="index" class="list-group-item">
                                    <div>
                                        <p>
                                            Operation Number : {{ curMtnOp.curMtnOp_number }}
                                        </p>
                                        <p>
                                            Report Number : {{ curMtnOp.curMtnOp_reportNumber }}
                                        </p>
                                        <p>
                                            Description : {{ curMtnOp.curMtnOp_description }}
                                        </p>
                                        <p v-if="curMtnOp.curMtnOp_startDate!=null ">
                                            Start Date :
                                            {{
                                                new Date(curMtnOp.curMtnOp_startDate.slice(0, 4), curMtnOp.curMtnOp_startDate.slice(5, 7), curMtnOp.curMtnOp_startDate.slice(8)).getDate()
                                            }}
                                            {{
                                                new Date(curMtnOp.curMtnOp_startDate.slice(0, 4), curMtnOp.curMtnOp_startDate.slice(5, 7) - 1, curMtnOp.curMtnOp_startDate.slice(8)).toDateString().slice(4, 7)
                                            }}
                                            {{
                                                new Date(curMtnOp.curMtnOp_startDate.slice(0, 4), curMtnOp.curMtnOp_startDate.slice(5, 7), curMtnOp.curMtnOp_startDate.slice(8)).getFullYear()
                                            }}
                                        </p>
                                        <p v-if="curMtnOp.curMtnOp_endDate!=null">
                                            End date :
                                            {{
                                                new Date(curMtnOp.curMtnOp_endDate.slice(0, 4), curMtnOp.curMtnOp_endDate.slice(5, 7), curMtnOp.curMtnOp_endDate.slice(8)).getDate()
                                            }}
                                            {{
                                                new Date(curMtnOp.curMtnOp_endDate.slice(0, 4), curMtnOp.curMtnOp_endDate.slice(5, 7) - 1, curMtnOp.curMtnOp_endDate.slice(8)).toDateString().slice(4, 7)
                                            }}
                                            {{
                                                new Date(curMtnOp.curMtnOp_endDate.slice(0, 4), curMtnOp.curMtnOp_endDate.slice(5, 7), curMtnOp.curMtnOp_endDate.slice(8)).getFullYear()
                                            }}
                                        </p>
                                        <p>
                                            Saved as : {{ curMtnOp.curMtnOp_validate }}
                                        </p>
                                        <p>
                                            Entered by : {{ curMtnOp.enteredBy_firstName }}
                                            {{ curMtnOp.enteredBy_lastName }}
                                        </p>
                                    </div>
                                    <div v-if="curMtnOp.realizedBy_lastName!=null">
                                        <p>
                                            Realized by : {{ curMtnOp.realizedBy_firstName }}
                                            {{ curMtnOp.realizedBy_lastName }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>
                                            Realized by : -
                                        </p>
                                    </div>
                                    <div v-if="curMtnOp.qualityVerifier_lastName!=null">
                                        <p>
                                            Quality verifier : {{ curMtnOp.qualityVerifier_firstName }}
                                            {{ curMtnOp.qualityVerifier_lastName }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>
                                            Quality verifier : -
                                        </p>
                                    </div>
                                    <div v-if="curMtnOp.technicalVerifier_lastName!=null">
                                        <p>
                                            Technical verifier : {{ curMtnOp.technicalVerifier_firstName }}
                                            {{ curMtnOp.technicalVerifier_lastName }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>
                                            Technical verifier : -
                                        </p>
                                    </div>
                                    <CurMtnOpModal :curMtnOp_description="curMtnOp.curMtnOp_description"
                                                   :curMtnOp_endDate="curMtnOp.curMtnOp_endDate"
                                                   :curMtnOp_id="curMtnOp.id"
                                                   :curMtnOp_reportNumber="curMtnOp.curMtnOp_reportNumber"
                                                   :curMtnOp_startDate="curMtnOp.curMtnOp_startDate"
                                                   :curMtnOp_validate="curMtnOp.curMtnOp_validate" :eq_id="eq_id"
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
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import PrvMtnOpRlzManagmentModal from './PrvMtnOpRlzManagmentModal.vue'
import CurMtnOpModal from './CurMtnOpModal.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'


export default {
    components: {
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz,
        PrvMtnOpRlzManagmentModal,
        CurMtnOpModal
    },
    data() {
        return {
            eq_curMtnOp: null,
            eq_prvMtnOpRlz: null,
            loaded: false,
            eq_id: parseInt(this.$route.params.id),
            state_id: parseInt(this.$route.params.state_id)
        }
    },
    created() {
        const consultUrlPrvMtnOpRlz = (id) => `/state/prvMtnOpRlz/send/${id}`;
        axios.get(consultUrlPrvMtnOpRlz(this.state_id))
            .then(response => {
                this.eq_prvMtnOpRlz = response.data;
                const consultUrlCurMtnOp = (id) => `/state/curMtnOp/send/${id}`;
                axios.get(consultUrlCurMtnOp(this.state_id))
                    .then(response => {
                        this.eq_curMtnOp = response.data
                        this.loaded = true
                    }).catch(error => {
                });
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

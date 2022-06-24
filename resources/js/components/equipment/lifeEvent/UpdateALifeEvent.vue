<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Preventive maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <div v-if="eq_prvMtnOpRlz.length>0" class="all_preventive_ope">
                                <h3>Recorded Preventive maintenance operation</h3>
                                <li class="list-group-item" v-for="(prvMtnOpRlz,index) in eq_prvMtnOpRlz " :key="index"  >
                                    <div>
                                        Operation Numner : {{prvMtnOpRlz.prvMtnOp_number}} <br>
                                        Description : {{prvMtnOpRlz.prvMtnOp_description}} <br>
                                        Protocol : {{prvMtnOpRlz.prvMtnOp_protocol}} <br>
                                        Report Numner : {{prvMtnOpRlz.prvMtnOpRlz_reportNumber}} <br>
                                        Start Date : {{prvMtnOpRlz.prvMtnOpRlz_startDate}} <br>
                                        End date : {{prvMtnOpRlz.prvMtnOpRlz_endDate}} <br>
                                        Saved as : {{prvMtnOpRlz.prvMtnOpRlz_validate}} <br>
                                        Entered by : {{prvMtnOpRlz.enteredBy_lastName}} {{prvMtnOpRlz.enteredBy_firstName}} <br>
                                    </div>
                                    <div v-if="prvMtnOpRlz.realizedBy_lastName!=null">
                                        Realized by : {{prvMtnOpRlz.realizedBy_firstName}} {{prvMtnOpRlz.realizedBy_lastName}} <br>
                                    </div>
                                    <div v-else>
                                        Realized by : - <br>
                                    </div>
                                    <div v-if="prvMtnOpRlz.approvedBy_lastName!=null">
                                        Approved by : {{prvMtnOpRlz.approvedBy_firstName}} {{prvMtnOpRlz.approvedBy_lastName}} <br>
                                    </div>
                                    <div v-else>
                                        Approved by : - <br>
                                    </div>
                                    
                                    <PrvMtnOpRlzManagmentModal :prvMtnOp_id="prvMtnOpRlz.prvMtnOp_id" :prvMtnOp_number="prvMtnOpRlz.prvMtnOp_number" 
                                    :prvMtnOp_description="prvMtnOpRlz.prvMtnOp_description" :prvMtnOp_protocol="prvMtnOpRlz.prvMtnOp_protocol"
                                    :prvMtnOpRlz_id="prvMtnOpRlz.id" :prvMtnOpRlz_reportNumber="prvMtnOpRlz.prvMtnOpRlz_reportNumber"
                                    :prvMtnOpRlz_startDate="prvMtnOpRlz.prvMtnOpRlz_startDate" :prvMtnOpRlz_endDate="prvMtnOpRlz.prvMtnOpRlz_endDate"
                                    :prvMtnOpRlz_validate="prvMtnOpRlz.prvMtnOpRlz_validate" :eq_id="eq_id" :state_id="state_id" @okReload="reloadPage"
                                    :approvedBy_lastName="prvMtnOpRlz.approvedBy_lastName" :realizedBy_lastName="prvMtnOpRlz.realizedBy_lastName"
                                     />
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Curative maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <div v-if="eq_curMtnOp.length>0" class="all_preventive_ope">
                                <h3>Recorded Curative maintenance operation</h3>
                                <li class="list-group-item" v-for="(curMtnOp,index) in eq_curMtnOp " :key="index"  >
                                    <div>
                                        Operation Numner : {{curMtnOp.curMtnOp_number}} <br>
                                        Report Numner : {{curMtnOp.curMtnOp_reportNumber}} <br>
                                        Description : {{curMtnOp.curMtnOp_description}} <br>
                                        Start Date : {{curMtnOp.curMtnOp_startDate}} <br>
                                        End date : {{curMtnOp.curMtnOp_endDate}} <br>
                                        Saved as : {{curMtnOp.curMtnOp_validate}} <br>
                                        Entered by : {{curMtnOp.enteredBy_lastName}} {{curMtnOp.enteredBy_firstName}} <br>
                                    </div>
                                    <div v-if="curMtnOp.realizedBy_lastName!=null">
                                        Realized by : {{curMtnOp.realizedBy_firstName}} {{curMtnOp.realizedBy_lastName}} <br>
                                    </div>
                                    <div v-else>
                                        Realized by : - <br>
                                    </div>
                                    <div v-if="curMtnOp.qualityVerifier_lastName!=null">
                                        Quality verifier : {{curMtnOp.qualityVerifier_lastName}} {{curMtnOp.qualityVerifier_firstName}} <br>
                                    </div>
                                    <div v-else>
                                        Quality verifier : - <br>
                                    </div>
                                    <div v-if="curMtnOp.technicalVerifier_lastName!=null">
                                        Technical verifier : {{curMtnOp.technicalVerifier_lastName}} {{curMtnOp.technicalVerifier_firstName}} <br>
                                    </div>
                                    <div v-else>
                                        Technical verifier : - <br>
                                    </div>
                                    
                                    <CurMtnOpModal :curMtnOp_id="curMtnOp.id" :curMtnOp_reportNumber="curMtnOp.curMtnOp_reportNumber"
                                    :curMtnOp_startDate="curMtnOp.curMtnOp_startDate" :curMtnOp_endDate="curMtnOp.curMtnOp_endDate"
                                    :curMtnOp_description="curMtnOp.curMtnOp_description"
                                    :curMtnOp_validate="curMtnOp.curMtnOp_validate" :eq_id="eq_id" :state_id="state_id" @okReload="reloadPage"
                                    :realizedBy_lastName="curMtnOp.realizedBy_lastName" :qualityVerifier_lastName="curMtnOp.qualityVerifier_lastName"
                                    :technicalVerifier_lastName="curMtnOp.technicalVerifier_lastName" />
                                </li>
                            </div>
                            <!--<ReferenceACurMtnOp v-if="eq_curMtnOp.length>0" :importedCurMtnOp="eq_curMtnOp" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>-->
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
    components:{
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz,
        PrvMtnOpRlzManagmentModal,
        CurMtnOpModal
    },
    data(){
        return{
            state_id:this.$route.params.state_id,
            eq_curMtnOp:null,
            eq_prvMtnOpRlz:null,
            loaded:false,
            eq_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id)
        }
    },
    created(){
        var consultUrlPrvMtnOpRlz = (id) => `/state/prvMtnOpRlz/send/${id}`;
        axios.get(consultUrlPrvMtnOpRlz(this.state_id))
            .then (response=>{
                console.log(response.data)
                this.eq_prvMtnOpRlz=response.data
            })
            .catch(error => console.log(error)) ;

        var consultUrlCurMtnOp = (id) => `/state/curMtnOp/send/${id}`;
        axios.get(consultUrlCurMtnOp(this.state_id))
            .then (response=>{
                console.log(response.data)
                this.eq_curMtnOp=response.data
                this.loaded=true
            })
            .catch(error => console.log(error)) ;


    },
    methods:{
        reloadPage(){
            window.location.reload();
        }
    }


}
</script>

<style>

</style>
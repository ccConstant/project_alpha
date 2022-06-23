<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" >
            <h2>Life event List</h2>
            <b-button variant="primary" @click="generateReport" >Export PDF</b-button>
            <div class="container" id="page">
                <div class="accordion all_event">
                    <h1>{{eq_internalReference}}</h1>
                    <div class="accordion-item" v-for="(list,index) in states " :key="index">
                        <h2 class="accordion-header" :id="'heading'+index">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse'+index" aria-expanded="true" :aria-controls="'collapse'+index">
                                State : {{list.state_name}}
                                Start date : {{list.state_startDate}}
                                End date : {{list.state_endDate}}
                            </button>
                        </h2>
                        <div :id="'collapse'+index" class="accordion-collapse collapse show" :aria-labelledby="'heading'+index">
                            <div class="accordion-body">
                                <div v-if="list.curMtnOp.length>0" class="all_curative_ope">
                                    <h3>Recorded curative maintenance operation</h3>
                                    <li class="list-group-item" v-for="(curMtnOp,index) in list.curMtnOp " :key="index"  >
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
                                    </li>
                                </div>
                                <div v-if="list.prvMtnOpRlz.length>0" class="all_preventive_ope">
                                    <h3>Recorded Preventive maintenance operation</h3>
                                    <li class="list-group-item" v-for="(prvMtnOpRlz,index) in list.prvMtnOpRlz " :key="index"  >
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
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>
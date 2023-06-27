<!--File name : EquipmentAllEventList.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date :27 Jun 2023-->
<!--Vue Component to show the different event for an equipment-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div id="page" class="container">
                <p>'</p>
                <div class="le_eq_top_infos">
                    <div class="le_eq_pdf_logo">
                        <img alt="Alpha logo" class="le_eq_logo" src="/images/logo.jpg">
                    </div>

                    <div class="le_eq_pdf_titre">
                        <h2 id="le_eq_titre">EQUIPMENT LIFE SHEET EVENT PART</h2>
                    </div>

                    <div class="le_eq_pdf_index">
                        <h5>{{ eq_internalReference }}_LS-R_{{ this.chaine }}</h5>
                    </div>

                    <div class="le_eq_internalReference_pdf">
                        <p>Equipment internal reference:</p>
                        <h5 class="text-primary">{{ eq_internalReference }}</h5>
                    </div>
                </div>
                <div class="le_eq_accordion_all_event">
                    <div v-for="(list,index) in states " :key="index" class="accordion-item">
                        <h2 :id="'heading'+index" class="accordion-header">
                            <button :aria-controls="'collapse'+index" :data-bs-target="'#collapse'+index"
                                    aria-expanded="true"
                                    class="accordion-button" data-bs-toggle="collapse"
                                    type="button">
                                State : {{ list.state_name }}
                                <br>
                                Start date : {{ list.state_startDate }}
                                <br>
                                End date : {{ list.state_endDate }}
                                <br>
                                Remarks : {{ list.state_remarks }}
                            </button>
                        </h2>
                        <div :id="'collapse'+index" :aria-labelledby="'heading'+index"
                             class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div v-if="list.curMtnOp.length>0" class="all_curative_ope">
                                    <h3>Recorded curative maintenance operation</h3>
                                    <li v-for="(curMtnOp,index) in list.curMtnOp " :key="index" class="list-group-item">
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
                                    </li>
                                </div>
                                <div v-if="list.prvMtnOpRlz.length>0" class="all_preventive_ope">
                                    <h3>Recorded Preventive maintenance operation</h3>
                                    <li v-for="(prvMtnOpRlz,index) in list.prvMtnOpRlz " :key="index"
                                        class="list-group-item">
                                        <div>
                                            Operation Number : {{ prvMtnOpRlz.prvMtnOp_number }} <br>
                                            Description : {{ prvMtnOpRlz.prvMtnOp_description }} <br>
                                            Protocol : {{ prvMtnOpRlz.prvMtnOp_protocol }} <br>
                                            Report Number : {{ prvMtnOpRlz.prvMtnOpRlz_reportNumber }} <br>
                                            Start Date : {{ prvMtnOpRlz.prvMtnOpRlz_startDate }} <br>
                                            End date : {{ prvMtnOpRlz.prvMtnOpRlz_endDate }} <br>
                                            Saved as : {{ prvMtnOpRlz.prvMtnOpRlz_validate }} <br>
                                            Entered by : {{ prvMtnOpRlz.enteredBy_lastName }}
                                            {{ prvMtnOpRlz.enteredBy_firstName }} <br>
                                        </div>
                                        <div v-if="prvMtnOpRlz.realizedBy_lastName!=null">
                                            Realized by : {{ prvMtnOpRlz.realizedBy_firstName }}
                                            {{ prvMtnOpRlz.realizedBy_lastName }} <br>
                                        </div>
                                        <div v-else>
                                            Realized by : - <br>
                                        </div>
                                        <div v-if="prvMtnOpRlz.approvedBy_lastName!=null">
                                            Approved by : {{ prvMtnOpRlz.approvedBy_firstName }}
                                            {{ prvMtnOpRlz.approvedBy_lastName }} <br>
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

                <div class="le_eq_recordTemplateRefPdf">
                    <div class="le_eq_table_recordTemplateRefPdf">
                        <div class="le_eq_index_recordTemplateRefPdf">
                            Record Template Ref : REC-IWE13_V1.0
                        </div>
                        <div class="le_eq_confidential_recordTemplateRefPdf">
                            This document contains CONFIDENTIAL information
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <br>
        <b-button variant="primary" @click="generateReport">Export PDF</b-button>
    </div>
</template>

<script>
import moment from 'moment'
import html2PDF from 'jspdf-html2canvas';

export default {
    data() {
        return {
            eq_id: this.$route.params.id,
            states: null,
            loaded: false,
            states_sort: null,
            chaine: null,
            eq_internalReference: this.$route.query.internalReference,
        }
    },
    methods: {

        generateReport() {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width: 100
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                },
                imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 40,
                    right: 10,
                    bottom: 40,
                    left: 10,
                },
                output: this.eq_internalReference + '_LS-R_' + this.chaine + '.pdf',
            });
        },

        Date() {
            const date = new Date();
            const month = date.getMonth() + 1;
            this.chaine = date.getFullYear() + '-' + month + '-' + date.getDate();
        }

    },
    created() {
        const UrlState = (id) => `/states/send/${id}`;
        axios.get(UrlState(this.eq_id))
            .then(response => {
                this.states = response.data
                for (let i = 0; i < this.states.length; i++) {
                    this.states[i].state_startDate = moment(this.states[i].state_startDate).format('D MMM YYYY');
                    if (this.states[i].state_endDate === null) {
                        this.states[i].state_endDate = "-"
                    } else {
                        this.states[i].state_endDate = moment(this.states[i].state_endDate).format('D MMM YYYY');
                    }
                    for (let j = 0; j < this.states[i].curMtnOp.length; j++) {
                        this.states[i].curMtnOp[j].curMtnOp_startDate = moment(this.states[i].curMtnOp[j].curMtnOp_startDate).format('D MMM YYYY');
                        if (this.states[i].curMtnOp[j].curMtnOp_endDate === null) {
                            this.states[i].curMtnOp[j].curMtnOp_endDate = "-"
                        } else {
                            this.states[i].curMtnOp[j].curMtnOp_endDate = moment(this.states[i].curMtnOp[j].curMtnOp_endDate).format('D MMM YYYY');
                        }
                    }
                    for (let k = 0; k < this.states[i].prvMtnOpRlz.length; k++) {
                        this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_startDate = moment(this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_startDate).format('D MMM YYYY');
                        if (this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate === null) {
                            this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate = "-"
                        } else {
                            this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate = moment(this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate).format('D MMM YYYY');
                        }
                    }
                }
                this.loaded = true;
                this.states.sort(function compare(a, b) {
                    if (a.startDate > b.startDate)
                        return 1;
                    else
                        return -1;
                });
            }).catch(error => {
        });
    },
    mounted() {
        this.Date();
    }

}
</script>


<style lang="scss">
#page {
    width: 1329px;
    font-size: 20px;

    .text-primary {
        font-size: 25px;
    }

    .le_eq_top_infos {

        h5 {
            margin-top: auto;
            width: auto;
            font-size: 25px;
            text-align: center;
            font-weight: bold;
        }

        position: absolute;
        margin-top: 0px;

        .le_eq_pdf_logo {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 200px;
            height: 170px;
            margin-left: 100px;
            margin-top: 0px;

            .le_eq_logo {
                margin-top: 30px;
            }

        }

        .le_eq_pdf_titre {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 642px;
            top: 0px;
            left: 300px;
            height: 87px;
            text-align: center;
        }

        .le_eq_titre {
            text-align: center;
            position: relative;
        }

        .le_eq_pdf_index {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left: 942px;
            top: 0px;
            height: 86px;
            width: 200px;
            text-align: center
        }

        .le_eq_internalReference_pdf {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left: 942px;
            top: 86px;
            width: 200px;
            height: 84px;
            text-align: center;
        }
    }

    .le_eq_recordTemplateRefPdf {
        position: block;


        .le_eq_table_recordTemplateRefPdf {
            position: block;
            width: 1042px;
            margin-top: 70px;
            margin-left: 100px;

            .le_eq_confidential_recordTemplateRefPdf {
                border: solid 1px black;
                background-color: lightgrey;
                text-align: center;
                height: auto;
                font-size: 20px;
                font-style: italic;
            }

            .le_eq_index_recordTemplateRefPdf {
                background-color: lightgrey;
                border: solid 1px black;
                text-align: center;
                height: auto;
                font-size: 20px;
            }
        }


    }

    .container {
        display: block;
        margin-top: 0px;
    }


    .all_curative_ope {
        margin-left: 20px;
    }

    .all_preventive_ope {
        margin-left: 20px;
    }

    .le_eq_accordion_all_event {
        display: block;
        margin-top: 250px;
        width: 1042px;
        margin-left: 100px;
    }
}


</style>

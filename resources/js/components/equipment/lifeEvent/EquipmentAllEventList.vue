<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" >
            <b-button variant="primary" @click="generateReport" >Export PDF</b-button>
            <div class="container" id="page">
                <p>'</p>
                <div class="top_infos">
                    <div class=" equipement_pdf_logo ">
                    <p class="text-primary" text-align="center"> ALPHA </p>
                    </div>

                    <div class="equipement_pdf_titre">
                        <h2 id="equipement_life_event_titre">LIST OF REGISTERED LIFE EVENTS</h2>
                    </div>

                    <div class="equipement_pdf_index">
                        <h2>{{eq_internalReference}}_LS-R_{{this.chaine}}</h2>
                    </div>

                    <div class="eq_internalReference_pdf">
                        <p>Equipment internal reference:</p>
                        <h5 class="text-primary">{{eq_internalReference}}</h5>
                    </div>
                </div>
                <div class="accordion_all_event">
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
                                            Operation Number : {{curMtnOp.curMtnOp_number}} <br>
                                            Report Number : {{curMtnOp.curMtnOp_reportNumber}} <br>
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
                                            Operation Number : {{prvMtnOpRlz.prvMtnOp_number}} <br>
                                            Description : {{prvMtnOpRlz.prvMtnOp_description}} <br>
                                            Protocol : {{prvMtnOpRlz.prvMtnOp_protocol}} <br>
                                            Report Number : {{prvMtnOpRlz.prvMtnOpRlz_reportNumber}} <br>
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

                <div class="recordTemplateRefPdf">
                <div class="table_recordTemplateRefPdf">
                     <div class="index_recordTemplateRefPdf">
                        Record Template Ref :  REC-IWE01
                    </div>
                    <div class="confidential_recordTemplateRefPdf">
                        This document contains CONFIDENTIAL information
                    </div>
                </div>
             </div>
            </div>
            
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import html2PDF from 'jspdf-html2canvas';
export default {
    data(){
        return{
            eq_id:this.$route.params.id,
            states:null,
            loaded:false,
            states_sort:null,
            chaine:null,
            eq_internalReference:this.$route.query.internalReference,
        }
    },
    methods:{
        
        generateReport () {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width : 100
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                },
                imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                },
                output: this.eq_internalReference+'_LS-R_'+this.chaine+'.pdf', 
            });
        },

        Date(){
            var date = new Date();
            var month=date.getMonth()+1;
            this.chaine=date.getFullYear()+'/'+month+'/'+date.getDate();
        }
        
    },
    created(){
        var UrlState = (id)=> `/states/send/${id}`;
        axios.get(UrlState(this.eq_id))
            .then (response=>{
                console.log(response.data)
                this.states=response.data
                for (var i=0;i<this.states.length;i++) {
                    this.states[i].state_startDate=moment(this.states[i].state_startDate).format('D MMM YYYY');
                    if(this.states[i].state_endDate===null){
                        this.states[i].state_endDate="-"
                    }else{
                        this.states[i].state_endDate=moment(this.states[i].state_endDate).format('D MMM YYYY'); 
                    }
                    for(var j=0;j<this.states[i].curMtnOp.length;j++){
                        this.states[i].curMtnOp[j].curMtnOp_startDate=moment(this.states[i].curMtnOp[j].curMtnOp_startDate).format('D MMM YYYY'); 
                        if(this.states[i].curMtnOp[j].curMtnOp_endDate===null){
                            this.states[i].curMtnOp[j].curMtnOp_endDate="-"
                        }else{
                            this.states[i].curMtnOp[j].curMtnOp_endDate=moment(this.states[i].curMtnOp[j].curMtnOp_endDate).format('D MMM YYYY'); 
                        }
                    }
                    
                    for(var k=0;k<this.states[i].prvMtnOpRlz.length;k++){
                        this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_startDate=moment(this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_startDate).format('D MMM YYYY'); 
                        if(this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate===null){
                            this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate="-"
                        }else{
                            this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate=moment(this.states[i].prvMtnOpRlz[k].prvMtnOpRlz_endDate).format('D MMM YYYY'); 
                        }
                    } 
                }
                this.loaded=true;
                this.states.sort(function compare(a, b) {
                    if (a.startDate > b.startDate)
                        return 1;
                    else
                        return -1;
                    return 0;
                });
            })
    
            .catch(error => console.log(error)) ;

        
    },
    mounted(){
        this.Date();
    }

}
</script>


<style lang="scss">
    #page{
        width:1329px;
        font-size : 20px ;
        .text-primary{
            font-size : 25px ;
        }

        .recordTemplateRefPdf{
            position: block;
            width: 1200px;
            margin-left:-100px;
            

            .table_recordTemplateRefPdf{
                position:block;
                left:-200px;
                 width: 1120px;
                margin-top:70px ;
                margin-left:-100px;
                .confidential_recordTemplateRefPdf{
                    border: solid 1px black;
                    background-color: lightgrey;
                    text-align: center;
                }
                .index_recordTemplateRefPdf{
                    background-color: lightgrey;
                    border: solid 1px black;
                    text-align: center;
                }
            }


        }
    }
   .container{
    display:block;
    margin-top: 0px;
   }
    .top_infos{
        margin-left:-70px;
        .equipement_pdf_logo{
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 200px;
            height: 170px;
            text-align:center;
            //left:-100px;
            
            
        }
        .equipement_pdf_titre{
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 642px;
            top: 100px;
            height: 87px;
            text-align:center;
        }
        .equipement_life_event_titre{
            text-align: center;
            position: relative;
        }
        .equipement_pdf_index{
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left: 942px;
            top: 100px;
            height: 86px;
            width: 200px;
            text-align:center
        }
        
        .eq_internalReference_pdf{
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left :942px;
            top: 186px;
            width: 200px;
            height: 84px;
            h5{
                margin: 0 auto;
                width: auto;
            }
            text-align:center;
        }
    }

    .all_curative_ope{
        margin-left:20px ;
    }
    .all_preventive_ope{
         margin-left:20px ;
    }
    .accordion_all_event{
        display:block ;
        margin-top : 200px;
    }

       

</style>
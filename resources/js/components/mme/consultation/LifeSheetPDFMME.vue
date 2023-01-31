<!--
* Filename : LifeSheetPDFMME.vue
* Creation date : 11 Jul 2022
* Update date : 9 Jan 2022
* The document allows us to create the life sheet of a mme and to export it in PDF.
-->

<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>'</p>
            <div class="mme_top_infos">
                <div class="mme_pdf_logo ">
                  <img src="/images/logo.png" alt="Alpha logo" class="logo"/>
                </div>

                <div class="mme_pdf_titre">
                    <h2 id="mme_fiche_de_vie_titre">MME LIFE SHEET DESCRIPTIVE PART</h2>
                </div>

                <div class="mme_pdf_index">
                    <h5>Version : MME LS-D {{mme_idCard.mme_version}}</h5>
                </div>
                <div class="mme_revued_by">
                    <p >Technical Review <b class="text-primary">{{ mme_idCard.mme_technicalVerifier_firstName}} {{mme_idCard.mme_technicalVerifier_lastName}} </b></p>
                </div>

                <div class="mme_approuved_by">
                    <p>Quality Review <b class="text-primary">{{ mme_idCard.mme_qualityVerifier_firstName}} {{mme_idCard.mme_qualityVerifier_lastName}} </b></p>
                </div>

                <div class="mme_internalReference_pdf">
                    <p>MME unique ID:</p>
                    <h5 class="text-primary">{{mme_idCard.mme_internalReference}}</h5>
                </div>
                

            </div>

            <div class="mme_identification_infos_pdf">
                    <div class="title_identification_pdf">
                        <p>IDENTIFICATION</p>
                    </div>
                    
                    <div class="mme_designation_type_pdf">
                        <p>
                            Designation : <b class="text-primary"> {{ mme_idCard.mme_name}}</b>
                        </p>
                        <p>
                            
                        </p>
                    </div>
                    <div class="mme_externalReference_pdf">
                        <p>
                            External Reference : <b class="text-primary">{{mme_idCard.mme_externalReference}}</b>
                        </p>
                    </div>
                    <div class="mme_constructor_pdf">
                        <p>Constructor: <b class="text-primary">{{mme_idCard.mme_constructor}}</b></p>
                    </div>
                    <div class="mme_serialNumber_pdf">
                        <p>Serial Number : <b class="text-primary">{{mme_idCard.mme_serialNumber}}</b></p>
                    </div>
            </div>


            <div class="mme_usage_infos_pdf">
                <div class="title_usage_pdf">
                    <p>USAGE(s)</p>
                </div>
                <div v-if="mme_usg.length>0">
                    <div class="usg_type_and_precaution_pdf" v-for="(usg,index) in mme_usg " :key="index">
                        <div class="usg_table">
                            <b-row>
                                <b-col cols="1" class="mme_usage_id_pdf">
                                    Id
                                </b-col>
                                <b-col cols="1" class="mme_usage_measurement_type_pdf">
                                    Measurement Type
                                </b-col>
                                <b-col cols="2" class="mme_usage_precision_pdf">
                                    Precision
                                </b-col>
                                <b-col cols="5"  class="mme_usage_application_pdf">
                                Application
                                </b-col>
                                <b-col cols="6"  class="mme_usage_metrologicalLevel_pdf">
                                    Metrological Level
                                </b-col>             
                            </b-row>
                            <div v-for="(usage,index) in mme_usg " :key="index">
                                <b-row>
                                    <b-col cols="1" class="mme_usage_id_pdf">
                                    <p class="text-primary"> {{usage.id}} </p>
                                    </b-col>
                                    <b-col cols="1" class="mme_usage_measurement_type_pdf">
                                    <p class="text-primary"> {{usage.usg_measurementType}} </p>
                                    </b-col>
                                    <b-col cols="1" class="mme_usage_precision_pdf">
                                    <p class="text-primary"> {{usage.usg_precision}} </p>
                                    </b-col>
                                    <b-col cols="4"  class="mme_usage_application_pdf">
                                    <p class="text-primary"> {{usage.usg_application}}</p>
                                    </b-col>
                                    <b-col cols="4"  class="mme_usage_metrologicalLevel_pdf">
                                    <p class="text-primary"> {{usage.usg_metrologicalLevel}}</p>
                                    </b-col>               
                                </b-row>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mme_precaution_infos_pdf">
                <div class="title_precaution_pdf">
                    <p>Precaution(s) (associated to usage)</p>
                </div>
                <div v-if="mme_usg_prctn.length>0">
                    <div class="prctn_pdf" v-for="(usg,index) in mme_usg " :key="index">
                        <div class="prctn_table">
                            <b-row>
                                <b-col cols="1" class="mme_usg_prctn_id_pdf">
                                    Id of the usage
                                </b-col>
                                <b-col cols="1" class="mme_usg_prctn_description_pdf">
                                    Description
                                </b-col>                
                            </b-row>
                            <div v-for="(precaution,index) in mme_usg_prctn " :key="index">
                                <b-row>
                                    <b-col cols="1" class="mme_usg_prctn_id_pdf">
                                    <p class="text-primary"> {{precaution.usg_id}} </p>
                                    </b-col>
                                    <b-col cols="1" class="mme_usg_prctn_description_pdf">
                                    <p class="text-primary"> {{precaution.prctn_description}} </p>
                                    </b-col>             
                                </b-row>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="mme_file_infos_pdf">
                <div class="title_file_pdf">
                    <p>ASSOCIATED FILE(s), SET AND REMARK</p>
                </div>
                <div class="mme_file_assoc_pdf" >
                    <p>
                        Name of the file : Location
                    </p>
                    <p class="text-primary" v-for="(file,index) in mme_file " :key="index">
                        {{file.file_name}} : {{file.file_location}}<br>

                    </p>
                </div>   
            </div>

            <div class="mme_carac_infos_pdf">
                <div class="mme_set_pdf">
                    <div class="mme_set_pdf">
                        <p >Set : <b class="text-primary">{{mme_idCard.mme_set}}</b></p>
                    </div>
                </div>

                <div class="mme_remark_pdf" >
                    <p >
                        Remarks
                    </p>
                    <p class="text-primary">
                        {{mme_idCard.mme_remarks}}
                    </p>
                </div>   
            </div>

            <div class="mme_verif_infos_pdf">
                <div class="title_verif_pdf">
                    VERIFICATION(s)
                </div>
                <div class="verif_table">
                    <b-row>
                        <b-col cols="1" class="verif_table_number">
                            N°
                        </b-col>
                        <b-col cols="7" class="verif_puttingIntoService_pdf">
                            For PIS? 
                        </b-col >   
                        <b-col cols="2" class="verif_table_name">
                            Name of the verification
                        </b-col>
                        <b-col cols="5"  class="verif_table_expectedResult">
                            Expected Result
                        </b-col>
                         <b-col cols="6"  class="verif_table_nonComplianceLimit">
                            Non compliance limit
                        </b-col>
                        <b-col cols="7" class="verif_table_periodicity">
                            Periodicity
                        </b-col >
                        <b-col cols="1" class="verif_table_requiredSkill">
                            Required Skill
                        </b-col>    
                        <b-col cols="7" class="mme_verifAcceptanceAuthority_pdf">
                                    Verification Acceptance Authority
                        </b-col >  
                        <b-col cols="7" class="mme_verifReformed">
                                Reformed?
                        </b-col >                     
                    </b-row>
                    <div v-for="(verif,index) in mme_verif " :key="index">
                        <b-row>
                            <b-col cols="1" class="verif_table_number">
                               <p class="text-primary"> {{verif.verif_number}} </p>
                            </b-col>
                            <b-col  cols="1" class="verif_puttingIntoService_pdf">
                                        <p class="text-primary">{{verif.verif_puttingIntoService}} </p>
                            </b-col>  
                            <b-col cols="1" class="verif_table_name">
                               <p class="text-primary"> {{verif.verif_name}} </p>
                            </b-col>
                            <b-col cols="4"  class="verif_table_expectedResult">
                               <p class="text-primary"> {{verif.verif_expectedResult}}</p>
                            </b-col>
                            <b-col cols="4"  class="verif_table_nonComplianceLimit">
                               <p class="text-primary"> {{verif.verif_nonComplianceLimit}}</p>
                            </b-col>
                            <b-col  cols="1" class="verif_table_periodicity">
                                <p class="text-primary">{{verif.verif_periodicity}} {{verif.verif_symbolPeriodicity}} </p>

                            </b-col>
                            <b-col  cols="1" class="verif_table_requiredSkill">
                                <p class="text-primary">{{verif.verif_requiredSkill}}</p>
                            </b-col>  
                             <b-col  cols="1" class="mme_verifAcceptanceAuthority_pdf">
                                        <p class="text-primary">{{verif.verif_verifAcceptanceAuthority}} </p>
                            </b-col>  
                            <b-col  cols="1" class="mme_verifReformed">
                                        <p class="text-primary">{{verif.verif_reformed}} </p>
                            </b-col>                     
                        </b-row>
                    </div>
                </div>
            </div>



             <div class="recordTemplateRefPdf">
                <div class="table_recordTemplateRefPdf">
                     <div class="index_recordTemplateRefPdf">
                        Record Template Ref :  REC-IWE02
                    </div>
                    <div class="confidential_recordTemplateRefPdf">
                        This document contains CONFIDENTIAL information
                    </div>
                </div>
             </div>



        </div>
        <button @click="generateReport" class="btn btn-primary">Generate PDF</button>
    </div>  

</template>

<script>
import html2PDF from 'jspdf-html2canvas';
export default { 
    data(){
        return{
            mme_id:this.$route.params.id,
            mme_idCard:null,
            mme_usg:[],
            mme_file:null,
            mme_verif:null,
            mme_usg_prctn:null,
            loaded:false,
        }
    },

    components: {
        
    },
    methods: { 
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
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10,
                },
                output: this.mme_idCard.mme_internalReference+'MME LS-D'+'_'+this.mme_idCard.mme_version+'.pdf', 
            });
        }
    },
    created(){
        var consultUrl = (id) => `/mme/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then (response => {
                this.mme_idCard=response.data;
                console.log(this.mme_idCard)
            })
            .catch(error => console.log(error));
        
        var consultUrlUsg = (id) => `/mme_usage/send/${id}`;
        console.log(this.mme_id)
        axios.get(consultUrlUsg(this.mme_id))
            .then (response=>this.mme_usg=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlFile = (id) => `/file/send/mme/${id}`;
        axios.get(consultUrlFile(this.mme_id))
            .then (response=>this.mme_file=response.data)
            .catch(error => console.log(error)) ;
    
        
        
        var consultUrlVerif = (id) => `/verifs/send/lifesheet/${id}`;
        axios.get(consultUrlVerif(this.mme_id))
            .then (response=>{
                this.mme_verif=response.data
                console.log(response.data)
            })
            .catch(error => console.log(error)) ;
        

        var consultUrlPrctn = (id) => `/prctn/send/pdf/${id}`;
        axios.get(consultUrlPrctn(this.mme_id))
            .then (response=>{
                this.mme_usg_prctn=response.data
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
        }
}

</script>

<style lang="scss">

        #page{
            width:1329px;
            font-size : 10px ;
            .text-primary{
                font-size : 20px ;
            }
            .mme_top_infos{
                position: absolute;
                margin-top: 0px;
                margin-left:50px;

                h5{
                        margin-top :auto;
                        width: auto;
                        font-size:25px;
                        text-align:center;
                        font-weight: bold;

                    }
                .mme_pdf_logo{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 200px;
                    height: 170px;
                    margin-left:100px ;
                    margin-top: 0px;
                      .logo{
                        margin-top:30px;
                    }
                    
                }
                .mme_pdf_titre{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 0px;
                    left:300px;
                    height: 87px;
                    text-align:center;
                   
                    
                    
                }
                .mme_fiche_de_vie_titre{
                    text-align: center;
                }
                .mme_pdf_index{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left: 942px;
                    top: 0px;
                    height: 86px;
                    width: 200px;
                }
                .mme_revued_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;

                    left :300px;
                    top: 86px;
                    height: 84px;
                    width: 400px;

                }
                .mme_approuved_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :700px;
                    top: 86px;
                    height: 84px;
                    width: 242px;
                }
                .mme_internalReference_pdf{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :942px;
                    top: 86px;
                    width: 200px;
                    height: 84px;
                }

            }
            .mme_identification_infos_pdf{
                position: relative;
                margin-top: 240px;
                margin-bottom: 60px;
                margin-left: 150px;
                .title_identification_pdf{
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                    p{
                        margin-top : 220px;
                        margin-bottom : 0px ;
                    }
                }
            
                
                .mme_designation_type_pdf{
                    border: solid 1px black;
                    width: 500px;
                    height: 60px;
                    margin-bottom: 20px;
                    float: left;
                    
                }
                .mme_externalReference_pdf{
                    border: solid 1px black;
                    margin-left: 42px;
                    width: 500px;
                    height: 60px;
                    margin-bottom: 50px;
                    float: left;
                }
                .mme_constructor_pdf{
                    border: solid 1px black;
                    width: 500px;
                    height: 60px;
                    float: left;
                }
                .mme_serialNumber_pdf{
                    border: solid 1px black;
                    margin-left: 42px;
                    width: 500px;
                    height: 60px;
                    float: left; 
                }

            }
            .mme_usage_infos_pdf{
                position: relative;
                .title_usage_pdf{
                   margin-left: 150px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                    p{
                        margin-top : 200px;
                        margin-bottom : 0px ;
                    }
                }
                

                .usg_table{
                    margin-left: 150px;
                    .mme_usage_measurement_type_pdf{
                        border: solid 1px black;
                        text-align: center;
                        width:245px;
                        height:auto;
                    }

                    .mme_usage_precision_pdf{
                        border: solid 1px black;
                        text-align: center;
                        width:200px;
                        height:auto;
                    }

                    .mme_usage_application_pdf{
                         border: solid 1px black;
                        text-align: center;
                        width:330px;
                        height:auto;
                    }
                    .mme_usage_id_pdf{
                        margin-left:10px;
                        border: solid 1px black;
                        text-align: center;
                        width:10px;
                        height:auto;
                    }

                    .mme_usage_metrologicalLevel_pdf{
                         border: solid 1px black;
                        text-align: center;
                        width:243px;
                        height:auto;
                    }
                }
            }

                .mme_precaution_infos_pdf{
                position: relative;
                .title_precaution_pdf{
                   margin-left: 150px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                    p{
                        margin-top : 20px;
                        margin-bottom : 0px ;
                    }
                }
                

                .prctn_table{
                    margin-left: 150px;
                    .mme_usg_prctn_id_pdf{
                         margin-left:10px;
                        border: solid 1px black;
                        text-align: center;
                        width:70px;
                        height:auto;
                    }

                    .mme_usg_prctn_description_pdf{
                        border: solid 1px black;
                        text-align: center;
                        width:972px;
                        height:auto;
                    }
                }
            }
            .mme_file_infos_pdf{
                position: relative;
                .title_file_pdf{
                    margin-left: 150px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                    p{
                        margin-top : 30px;
                        margin-bottom : 0px ;
                    }
                }
                .mme_file_assoc_pdf{
                    border: solid 1px black;
                    margin-left: 150px;
                    position: relative;
                    margin-bottom: 20px;
                    height: auto;
                    width: 1042px;
                }

            }

            .mme_carac_infos_pdf{
                position: relative;
                .title_carac_pdf{
                    margin-left: 150px;
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                    margin-bottom:0px;
                }
               
    
                
                .mme_set_pdf{
                    position: relative;
                    
                    p{
                        ///margin-top: 3px;
                         margin-bottom:0px;
                        margin-left: 10px;
                        
                    }
                    .mme_set_pdf{
                        display: inline-block;
                        border: solid 1px black;
                        margin-bottom: 20px;
                        height: 40px;
                        margin-left: 150px;
                        width: 250px;
                    }
                }

                .mme_remark_pdf{
                    border: solid 1px black;
                    margin-left: 150px;
                    position: relative;
                    margin-bottom: 20px;
                    height: auto;
                    width: 1042px;
                }
            }
            
            .mme_verif_infos_pdf{
                position: relative;
                margin-top:10px ;
                margin-bottom:-45px;

                .title_verif_pdf{
                    margin-left: 150px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .verif_table{
                    margin-left: 150px;
                    .verif_table_number{
                        margin-left:10px;
                        border: solid 1px black;
                        text-align: center;
                        width:10px;
                        height:auto;
                    }
                    .verif_table_name{
                        border: solid 1px black;
                        text-align: center;
                        width:194.4px;
                        height:auto;
                    }
                    .verif_puttingIntoService_pdf{
                        border: solid 1px black;
                        text-align: center;
                        width:60px;
                        height:auto;
                    }
                    .verif_table_nonComplianceLimit{
                        border: solid 1px black;
                        text-align: center;
                        width:166px;
                        height:auto;
                    }
                    .verif_table_expectedResult{
                        border: solid 1px black;
                        text-align: center;
                        width : 140px;
                        height:auto;
                    }
                    .verif_table_requiredSkill{
                        border: solid 1px black;
                        text-align: center;
                        width:174.4px;
                        height:auto;
                    }                  
                    .verif_table_periodicity{
                        border: solid 1px black;
                        width:65px;
                        text-align: center;
                        height:auto;
                    
                    }
                    .mme_verifAcceptanceAuthority_pdf{
                         border: solid 1px black;
                        text-align: center;
                        width:150px;
                        height:auto;
                    }
                    .mme_verifReformed{
                         border: solid 1px black;
                        text-align: center;
                        width:70px;
                        height:auto;
                    }
                }
            }
            .recordTemplateRefPdf{
                position: relative;
                margin-top:60px ;
                width: 1046px;
                 height: auto;
                font-size: 20px;
                margin-left: 150px;

                .table_recordTemplateRefPdf{
                    width:1046px;

                    .confidential_recordTemplateRefPdf{
                        border: solid 1px black;
                        background-color: lightgrey;
                        text-align: center;
                        height: auto;
                        font-size: 20px;
                        font-style : italic;
                    }
                    .index_recordTemplateRefPdf{
                        background-color: lightgrey;
                        border: solid 1px black;
                        text-align: center;
                        height: auto;
                        font-size: 20px;
                    }
                }
            }
        }

        
        

    

</style>
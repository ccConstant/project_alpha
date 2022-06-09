<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>a</p>
            <div class="top_infos">
                <div class=" equipement_pdf_logo ">
                    LOGO<br>
                    LOGO<br>
                    LOGO<br>
                    LOGO<br>
                    LOGO<br>
                </div>

                <div class="equipement_pdf_titre">
                    <h2 id="equipement_fiche_de_vie_titre">FICHE DE VIE</h2>
                    <h2>DESCRIPTION DE L'EQUIPEMENT</h2>
                </div>

                <div class="equipement_pdf_version">
                    <h2>ENR QA-25</h2>
                </div>

                <div class="equipment_revued_by">
                    <p>Revue par (date & visa)</p>
                </div>

                <div class="equipment_approuved_by">
                    <p>Approuvé par (date & visa)</p>
                </div>

                <div class="eq_internalReference_pdf">
                    <p>Réf interne EQ:</p>
                    <h5>{{eq_idCard.eq_internalReference}}</h5>
                </div>
                

            </div>

            <div class="eq_identification_infos_pdf">
                <div class="eq_indentification_pdf">
                    <div class="title_identification_pdf">
                        <p>IDENTIFICATION</p>
                    </div>
                    <div class="Ecme_assoc_pdf">
                        <p>
                            Fiche ECME associé
                        </p>
                        <div class="content_Ecme_assoc_pdf" >
                            <p v-for="(ecme,index) in eq_ecme " :key="index">
                                {{ecme.name}} : {{ecme.description}} <br>
        
                            </p>

                        </div>
                    </div>
                    <div class="Ecme_designation_type_pdf">
                        <p>
                            Désignation et type : <b>{{eq_idCard.eq_name}} {{eq_idCard.eq_type}}</b>
                        </p>
                        <p>
                            
                        </p>
                    </div>
                    <div class="eq_externalReference_pdf">
                        <p>
                            External Reference : <b>{{eq_idCard.eq_externalReference}}</b>
                        </p>
                    </div>
                    <div class="eq_constructor_pdf">
                        <p>Constructor: <b>{{eq_idCard.eq_constructor}}</b></p>
                    </div>
                    <div class="eq_serialNumber_pdf">
                        <p>Serial Number : <b>{{eq_idCard.eq_serialNumber}}</b></p>
                    </div>
                </div>  
            </div>


            <div class="eq_usage_infos_pdf">
                <div class="title_usage_pdf">
                    <p>USAGE</p>
                </div>
                <div class="usg_type_and_precaution_pdf" v-for="(usg,index) in eq_usg " :key="index">
                    <div class="eq_usage_type_pdf">
                        <p>
                            Type of the operation realized by/with the equipment :
                        </p>
                        <br>
                        <p>
                            {{usg.usg_type}}
                        </p>
                    </div>
                    <div class="eq_usage_precaution_pdf">
                        <p>
                            Precaution :
                        </p>
                        <br>
                        <p>
                            {{usg.usg_precaution}}
                        </p>
                    </div>
                </div>
            </div>

            <div class="eq_file_infos_pdf">
                <div class="title_file_pdf">
                    <p>ASSOCIETED FILE</p>
                </div>
                <div class="eq_file_assoc_pdf" >
                    <p>
                        Name of the file : Location
                    </p>
                    <p v-for="(file,index) in eq_file " :key="index">
                        {{file.file_name}} : {{file.file_location}}<br>

                    </p>
                </div>   
            </div>

            <div class="eq_carac_infos_pdf">
                <div class="title_carac_pdf">
                    <p>CARACTERISTIQUES</p>
                </div>
                <div class="power_title_pdf">
                    <p>Power supply :</p>
                        <div>
                            <div class="eq_power_pdf"  v-for="(power,index) in eq_powers " :key="index">
                                <div class="eq_power_type_pdf" v-if="power.powers.length>0">
                                    <p>{{power.type}}</p>
                                    <div class ="eq_power_content_pdf" v-for="(power_elemnt,index) in power.powers " :key="index">
                                        <div class="eq_power_name_pdf">
                                            <p>Power name{{power_elemnt.pow_name}}</p>
                                        </div>
                                        <div class="eq_power_consumption_pdf">
                                            <p>Power consumption</p>
                                            <p>{{power_elemnt.pow_consumptionValue}}  {{power_elemnt.pow_consumptionUnit}} </p>
                                        </div>
                                        <div class="eq_power_value_pdf">
                                            <p>Power value</p>
                                            <p>{{power_elemnt.pow_value}}  {{power_elemnt.pow_unit}} </p>
                                        </div>
    
                                    </div>
                                </div>
                                <div>
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="dimension_title_pdf">
                    <p>Dimension :</p>
                        <div>
                            <div class="eq_dimension_pdf"  v-for="(dimension,index) in eq_dimensions " :key="index">
                                <div class="eq_dimension_type_pdf" v-if="dimension.dimensions.length>0">
                                    <p>{{dimension.type}}</p>
                                    <div class ="eq_dimension_content_pdf" v-for="(dim_elemnt,index) in dimension.dimensions " :key="index">
                                        <div class="eq_dimension_name_value_pdf">
                                            <p>{{dim_elemnt.dim_name}}  {{dim_elemnt.dim_value}}  {{dim_elemnt.dim_unit}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                <div class="eq_mass_set_mobility_pdf">
                    <div class="eq_mass_pdf">
                        <p >Masse : {{eq_idCard.eq_mass}} {{eq_idCard.eq_massUnit}}</p>
                    </div>
                    <div class="eq_mobility_pdf">
                        <div v-if="eq_idCard.eq_mobility==true">
                            <p>Mobil? : Yes</p>
                        </div>
                        <div v-else-if="eq_idCard.eq_mobility==false">
                            <p>Mobil? : No</p>
                        </div>
                    </div>
                    <div class="eq_set_pdf">
                        <p >Set : {{eq_idCard.eq_set}}</p>
                    </div>
                </div>

                <div class="eq_remark_pdf" >
                    <p>
                        Remarques
                    </p>
                    <p>
                        {{eq_idCard.eq_remarks}}
                    </p>
                </div>   
            </div>

            <div class="eq_risk_infos_pdf">
                <div class="title_risk_pdf">
                    <p>RISK RELATED TO THE EQUIPMENT</p>
                </div>
                <div v-for="(risk,index) in eq_risk " :key="index" class="eq_risk_pdf" >
                    
                    <div class="eq_risk_for_pdf">
                        Risk for{{risk.risk_for}}
                    </div>
                    <div class="eq_risk_wayOfControl_pdf">
                        <p>
                            Way of control
                        </p>
                        <p>
                            {{risk.risk_wayOfControl}}
                        </p>
                    </div>
                    <div class="eq_risk_remarks_pdf">
                        <p>
                            Remarks
                        </p>
                        <p>
                            {{risk.risk_remarks}}
                        </p>
                    </div>                    
                </div> 
            </div>

            <div class="eq_specProc_infos_pdf">
                <div class="title_spProc_pdf">
                    Special process ?
                </div>
                <div class="eq_specProc_pdf" v-for="(spProc,index) in eq_spProc " :key="index">
                    <div v-if="spProc.spProc_exist==true">
                        <p>
                            Yes <br>
                            {{spProc.spProc_name}}<br>
                            {{spProc.spProc_remarksOrPrecaution}}


                        </p>
                    </div>
                    <div v-if="spProc.spProc_exist==false">
                        <p>
                            No<br>
                            {{spProc.spProc_remarksOrPrecaution}}
                        </p>
                    </div>
                </div> 
            </div>
            <div class="eq_prvMtnOp_infos_pdf">
                <div class="title_prvMtnOp_pdf">
                    Preventive Maintenance Operation
                </div>
                <!--<div class="table-header table-row">
                    <div class="table-col">N°</div>
                    <div class="table-col"> Type od operation to realize</div>
                    <div class="table-col"> Peridicity</div>
                    <div class="table-col">Negative influence?</div>
                </div>
                <div class="member table-row" v-for="(prvMtnOp,index) in eq_prvMtnOp " :key="index">
                    <div class="table-col prvMtnOp_number">ez </div>
                    <div class="table-col prvMtnOp_type"> zee</div>
                    <div class="table-col prvMtnOp_periodicity"> ezfsd</div>
                    <div class="table-col prvMtnOp_risk"> dsdfs</div>
                </div>-->
                <b-table :items="eq_prvMtnOp"></b-table>
            </div>
        </div>
        <button @click="generateReport" class="btn btn-primary">Generate</button>
    </div>  

</template>

<script>
import html2PDF from 'jspdf-html2canvas';
export default { 
    data(){
        return{
            eq_id:this.$route.params.id,
            eq_idCard:null,
            eq_dimensions:null,
            eq_powers:null,
            eq_spProc:null,
            eq_usg:[],
            eq_file:null,
            eq_prvMtnOp:null,
            eq_risk:null,
            eq_ecme:[
                {name: 'TRAC01-F', description :'Ceci est la descritpion dun ecme elle peut aller vraiment loin'},
                {name: 'TRAC01-F', description :'Ceci est la descritpion dun ecme elle peut aller vraiment loin'},
                {name: 'TRAC01-F', description :'Ceci est la descritpion dun ecme elle peut aller vraiment loin'}
            ],
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
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                },
                output: 'jspdf-generate.pdf', 
            });
        }
    },
    created(){
        var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response => {this.eq_idCard=response.data; console.log(this.eq_idCard)})
            .catch(error => console.log(error));


        var consultUrlDim = (id) => `/dimension/send/ByType/${id}`;
        axios.get(consultUrlDim(this.eq_id))
            .then (response=> {this.eq_dimensions=response.data;console.log(response.data)})
            .catch(error => console.log(error)) ;
        
        var consultUrlPow = (id) => `/power/send/ByType/${id}`;
        axios.get(consultUrlPow(this.eq_id))
            .then (response=> {this.eq_powers=response.data;console.log(this.eq_powers) })
            .catch(error => console.log(error)) ;

        var consultUrlSpProc = (id) => `/spProc/send/${id}`;
        axios.get(consultUrlSpProc(this.eq_id))
            .then (response=>{
                if(response.data==""){
                    this.eq_spProc=[];
                }else{
                    this.eq_spProc=response.data;
                }
                console.log(response.data)
            })
            .catch(error => console.log(error)) ;
        
        var consultUrlUsg = (id) => `/usage/send/${id}`;
        console.log(this.eq_id)
        axios.get(consultUrlUsg(this.eq_id))
            .then (response=>this.eq_usg=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlFile = (id) => `/file/send/${id}`;
        axios.get(consultUrlFile(this.eq_id))
            .then (response=>this.eq_file=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
        axios.get(consultUrlPrvMtnOp(this.eq_id))
            .then (response=>this.eq_prvMtnOp=response.data)
            .catch(error => console.log(error)) ;
        
        var consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
        axios.get(consultUrlRisk(this.eq_id))
            .then (response=>{
                this.eq_risk=response.data
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    }
}
</script>

<style lang="scss">
        #page{
            width:1329px;
            
            .top_infos{
               
                position: absolute;
                top: 0px;
                .equipement_pdf_logo{
                    border: solid 1px black;
                    margin: auto;
                    position: absolute;
                    width: 200px;
                    height: 170px;
                    margin-left:100px ;
                    margin-top: 100px;
                    
                    
                }
                .equipement_pdf_titre{
                    border: solid 1px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 100px;
                    left:300px;
                    
                    
                }
                .equipement_fiche_de_vie_titre{
                    text-align: center;
                    position: relative;
                    

                }
                .equipement_pdf_version{
                    border: solid 1px black;
                    margin: auto;
                    position: absolute;

                    left: 942px;
                    top: 100px;
                    height: 87px;
                    width: 200px;
                }
                .equipment_revued_by{
                    border: solid 1px black;
                    margin: auto;
                    position: absolute;

                    left :300px;
                    top: 186px;
                    height: 84px;
                    width: 400px;

                }
                .equipment_approuved_by{
                    border: solid 1px black;
                    margin: auto;
                    position: absolute;


                    left :700px;
                    top: 186px;
                    height: 84px;
                    width: 242px;
                }
                .eq_internalReference_pdf{
                    border: solid 1px black;
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
                }

            }
            .eq_identification_infos_pdf{
                position: relative;
                margin-top: 240px;
                margin-bottom: 60px;
                .title_identification_pdf{
                    margin-left: 100px;
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                }
            
                .Ecme_assoc_pdf{
                    border: solid 1px black;
                    margin-left: 100px;
                    position: relative;
                    margin-bottom: 20px;
                    height: auto;
                    width: 1042px;
                }
                .Ecme_designation_type_pdf{
                    border: solid 1px black;
                    margin-left: 100px;
                    position: relative;
                    width: 500px;
                    height: 30px;
                    margin-bottom: 20px;
                    float: left;
                    
                }
                .eq_externalReference_pdf{
                    border: solid 1px black;
                    margin-left: 42px;
                    position: relative;
                    width: 500px;
                    height: 30px;
                    margin-bottom: 20px;
                    float: left;
                }
                .eq_constructor_pdf{
                    border: solid 1px black;
                    margin-left: 100px;
                    width: 500px;
                    height: 30px;
                    float: left;
                }
                .eq_serialNumber_pdf{
                    border: solid 1px black;
                    margin-left: 42px;
                    width: 500px;
                    height: 30px;
                    float: left; 
                }

            }
            .eq_usage_infos_pdf{
                position: relative;
                .title_usage_pdf{
                    margin-left: 100px;
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .usg_type_and_precaution_pdf{
                    position: relative;
                    .eq_usage_type_pdf{
                        border: solid 1px black;
                        margin-left: 100px;
                        float: left;
                        margin-bottom: 20px;
                        height: 170px;
                        width: 521px;
                    }
                    .eq_usage_precaution_pdf{
                        border: solid 1px black;
                        margin-bottom: 20px;
                        height: 170px;
                        width: 521px;
                        float: left; 
                    }
                }
            }
            .eq_file_infos_pdf{
                position: relative;
                .title_file_pdf{
                    margin-left: 100px;
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .eq_file_assoc_pdf{
                    border: solid 1px black;
                    margin-left: 100px;
                    position: relative;
                    margin-bottom: 20px;
                    height: auto;
                    width: 1042px;
                }

            }

            .eq_carac_infos_pdf{
                position: relative;
                .title_carac_pdf{
                    margin-left: 100px;
                    width: 200px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .power_title_pdf{
                    position: relative;
                    margin-left: 100px;
                    margin-top: -10px;
                    p{
                        font-size : 18px;
                        font-weight: bold;
                    }

                }
                .eq_power_pdf{
                    position: relative;
                    height: auto;
                    width: 1042px;   
                    margin-bottom: 30px;
                    .eq_power_type_pdf{
                        p{
                            font-size : 15px;
                            font-weight: bold;
                        }
                        position: relative;
                        height: auto;
                        .eq_power_content_pdf{
                            position: relative;
                            height: auto;
                            margin-bottom: 10px;
                            p{
                                font-size: 13px;
                                font-weight: normal;
                            }
                            .eq_power_name_pdf{
                                border: solid 1px black;
                                position: relative;
                                height: 20px;
                                width: 1042px;   
                                
                            }
                            .eq_power_value_pdf{
                                border: solid 1px black;
                                position: relative;
                                height: auto;
                                width: 521px;   
                            }
                            .eq_power_consumption_pdf{
                                border: solid 1px black;
                                position: relative;
                                height: auto;
                                width: 521px;   
                                float: right;
                            }
                        }   
                    }
                }
                .dimension_title_pdf{
                    position: relative;
                    margin-left: 100px;
                    margin-top: -10px;
                    p{
                        font-size : 18px;
                        font-weight: bold;
                    }

                }
                .eq_dimension_pdf{
                    position: relative;
                    height: auto;
                    width: 1042px;   
                    margin-bottom: 30px;
                    .eq_dimension_type_pdf{
                        p{
                            font-size : 15px;
                            font-weight: bold;
                        }
                        position: relative;
                        height: auto;
                        .eq_dimension_content_pdf{
                            position: relative;
                            height: auto;
                            display: inline-block;
                            margin-bottom: 10px;
                            p{
                                font-size: 20px;
                                font-weight: normal;
                                margin-left: 150px;
                                margin-top: 5px;
                            }
                            .eq_dimension_name_value_pdf{
                                border: solid 1px black;
                                position: relative;
                                height: 40px;
                                width: 510px;
                                margin-right: 11px;   
                                
                            }
                            
                        }   
                    }
                }
                .eq_mass_set_mobility_pdf{
                    position: relative;
                    p{
                        font-size: 20px;
                        margin-top: 3px;
                        margin-left: 10px;
                        
                    }
                    .eq_mass_pdf{
                        border: solid 1px black;
                        display: inline-block;
                        margin-bottom: 20px;
                        height: 40px;
                        margin-left: 100px;
                        width: 250px;
                    }
                    .eq_mobility_pdf{
                        border: solid 1px black;
                        display: inline-block;
                        margin-bottom: 20px;
                        height: 40px;
                        margin-left: 100px;
                        width: 250px;
                    }
                    .eq_set_pdf{
                        display: inline-block;
                        border: solid 1px black;
                        margin-bottom: 20px;
                        height: 40px;
                        margin-left: 100px;
                        width: 250px;
                    }
                }

                .eq_remark_pdf{
                    border: solid 1px black;
                    margin-left: 100px;
                    position: relative;
                    margin-bottom: 20px;
                    height: auto;
                    width: 1042px;
                }
            }
            .eq_risk_infos_pdf{
                position: relative;
                .title_risk_pdf{
                    margin-left: 100px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .eq_risk_pdf{
                    
                    position: relative;
                    height: auto;
                    width: 1042px;   
                    margin-bottom: 30px;
                }
                .eq_risk_for_pdf{
                    border: solid 1px black;
                    position: relative;
                    height: auto;
                    width: 1042px;   
                    margin-left: 100px;
                }
                .eq_risk_remarks_pdf{
                    border: solid 1px black;
                    position: relative;
                    height: auto;
                    width: 521px;   
                    margin-left: 100px;
                    
                }
                .eq_risk_wayOfControl_pdf{
                    border: solid 1px black;
                    position: relative;
                    height: auto;
                    width: 521px;   
                    margin-right: -100px;
                    float: right;
                }

            }
            .eq_specProc_infos_pdf{
                position: relative;
                .title_spProc_pdf{
                    margin-left: 100px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                }
                .eq_specProc_pdf{
                    border: solid 1px black;
                    position: relative;
                    height: auto;
                    width: 1042px;   
                    margin-left: 100px;
                }
            }
            .eq_prvMtnOp_infos_pdf{
                position: relative;
                margin-top:10px ;
                .title_prvMtnOp_pdf{
                    margin-left: 100px;
                    width: 400px;
                    font-size : 20px;
                    font-weight: bold;
                }
            }
        }

        
        

    

</style>
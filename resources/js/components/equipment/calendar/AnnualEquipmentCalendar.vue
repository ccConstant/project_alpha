<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>'</p>
            <div class="list_eq_top_infos">
                <div class="list_eq_pdf_logo ">
                  <img src="/images/logo.png" alt="Alpha logo" class="logo" >
                </div>

                <div class="list_eq_pdf_titre">
                    <h2 id="list_eq_fiche_de_vie_titre"> Planning of equipment calibration and maintenance </h2>
                </div>

                <div class="list_eq_pdf_index">
                    <h5> Version </h5>
                </div>
                <div class="equipment_revued_by">
                    <p >Technical Review <b class="text-primary"> </b></p>
                </div>
                <div class="equipment_approuved_by">
                    <p>Quality Review <b class="text-primary"> </b></p>
                </div>
                <div class="eq_internalReference_pdf">
                    <p>Date : </p>
                    <h5 class="text-primary"></h5>
                </div>
            </div>


            <div class="eq_planning_annual_table">
                <b-row>
                    <b-col cols="1" class="eq_planning_annual_internalReference">
                        Internal Reference
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_name">
                        Name
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_prvMtnOp">
                        Operations
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_plannification">
                        Annual Planification 
                        <b-row>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[0].month}} {{eq[1].periode[0].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[1].month}} {{eq[1].periode[1].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[2].month}} {{eq[1].periode[2].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[3].month}} {{eq[1].periode[3].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[4].month}} {{eq[1].periode[4].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[5].month}} {{eq[1].periode[5].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[6].month}} {{eq[1].periode[6].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[7].month}} {{eq[1].periode[7].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[8].month}} {{eq[1].periode[8].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[9].month}} {{eq[1].periode[9].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[10].month}} {{eq[1].periode[10].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[11].month}} {{eq[1].periode[11].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[12].month}} {{eq[1].periode[12].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[13].month}} {{eq[1].periode[13].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[14].month}} {{eq[1].periode[14].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[15].month}} {{eq[1].periode[15].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[16].month}} {{eq[1].periode[16].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{eq[1].periode[17].month}} {{eq[1].periode[17].year}}
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_periodicity">
                        Freq.
                    </b-col>
                </b-row>

                <div v-for="(eq,index1) in eq" :key="index1">
                    <div v-if="eq.preventive_maintenance_operations.length!=0">
                        <div v-for="(eq_prvMtnOp,index2) in eq.preventive_maintenance_operations" :key="index2">
                            <b-row>
                                <b-col cols="1" class="eq_planning_annual_internalReference">
                                <p class="info"> {{eq.internalReference}} </p>
                                </b-col>
                                <b-col cols="1" class="eq_planning_annual_name">
                                    <p class="info">{{eq.name}}</p>
                                </b-col>
                                <b-col cols="1" class="eq_planning_annual_prvMtnOp">
                                        <p class="info">Operation {{eq_prvMtnOp.prvMtnOp_number}}</p>
                                </b-col>
                                
                                <b-col v-if="maintenancePlanned(eq.periode[0].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[1].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">  
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col  v-if="maintenancePlanned(eq.periode[2].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">   
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col  v-if="maintenancePlanned(eq.periode[3].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[4].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">                                   
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[5].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">      
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[6].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[7].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[8].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[9].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[10].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[11].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[12].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[13].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[14].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[15].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[16].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(eq.periode[17].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                        
            
                                <b-col cols="1"  class="eq_planning_annual_periodicity">
                                <p class="info"> {{eq_prvMtnOp.prvMtnOp_periodicity}} {{eq_prvMtnOp.prvMtnOp_symbolPeriodicity}}</p>
                                </b-col>                  
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <b-row>
                            <b-col cols="1" class="eq_planning_annual_internalReference">
                                <p class="info"> {{eq.internalReference}} </p>
                            </b-col>
                            <b-col cols="1" class="eq_planning_annual_name">
                                <p class="info">{{eq.name}}</p>
                            </b-col>
                            <b-col cols="1" class="eq_planning_annual_prvMtnOp">
                                    <p class="info">No Operation</p>
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date_value">
                                    
                            </b-col>
                            <b-col cols="1"  class="eq_planning_annual_periodicity">
                                <p class="info"> / </p>
                            </b-col>                  
                        </b-row>
                    </div>    
                </div>
            </div>
         <button class="btn btn-primary" @click="generateReport" >Generate PDF</button>
        </div>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
export default { 
    data(){
        return{
            eq_id:this.$route.params.id,
            loaded:true,
            eq:[],

        }
    },

    components: {
        
    },
    methods: { 

        maintenancePlanned(id_periode, AllnextDate){
            let res=false ; 
            AllnextDate.forEach(nextdate => {
                if (nextdate == id_periode){
                    res=true;
                }
            });
            return res;
        },

        generateReport () {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    orientation : 'landscape',
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
                    top: 20,
                    right: 10,
                    bottom: 20,
                    left: 10,
                },
                output: 'AnnualEquipmentCalendar.pdf', 
            });
        },



        
    },
    created(){
        axios.get('/equipment/prvMtnOp/planning')
            .then (response=>{
                this.eq = response.data;
                console.log(response.data);
        })

	},
}
</script>

<style lang="scss">
        #page{
            width:1329px;
            font-size : 10px ;
            .text-primary{
                font-size : 10px ;
            }
            .info{
                font-size : 10px ;
                color:black;
            }
            .list_eq_top_infos{
                position: absolute;
                margin-top: 0px;
                margin-left:90px;

                h5{
                        margin-top :auto;
                        width: auto;
                        font-size:25px;
                        text-align:center;
                        font-weight: bold;
                    }

                .list_eq_pdf_logo{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 200px;
                    height: 170px;
                    margin-left:50px ;
                    margin-top: 0px;
                    
                    .logo{
                        margin-top:30px;
                    }
                    
                }
                .list_eq_pdf_titre{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 00px;
                    left:250px;
                    height: 87px;
                    text-align:center;
                   
                    
                    
                }
                .list_eq_fiche_de_vie_titre{
                    text-align: center;
                    position: relative;
                }
                .list_eq_pdf_index{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left: 892px;
                    top: 0px;
                    height: 86px;
                    width: 200px;
                    
                }
                .equipment_revued_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;

                    left :250px;
                    top: 86px;
                    height: 84px;
                    width: 400px;

                }
                .equipment_approuved_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :650px;
                    top: 86px;
                    height: 84px;
                    width: 242px;
                }
                .eq_internalReference_pdf{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :892px;
                    top: 86px;
                    width: 200px;
                    height: 84px;
                }

            }
            
            
    .eq_infos_pdf{
        position: relative;
        margin-top:300px ;
        width : 1112px;
        
        .title_eq_pdf{
            width: 400px;
            font-size : 20px;
            font-weight: bold;
            margin-left:150px;
        }
    }

    .eq_planning_annual_table{
            margin-top:200px;
            margin-left:30px;
            width:1300px;
            .eq_planning_annual_internalReference{
                border: solid 1px black;
                text-align: center;
                width:125px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }
                .eq_planning_annual_name{
                border: solid 1px black;
                text-align: center;
                width:125px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }
            .eq_planning_annual_prvMtnOp{
                border: solid 1px black;
                text-align: center;
                width:90px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }

            .eq_planning_annual_plannification{
                border: solid 1px black;
                text-align: center;
                width:866px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }

            .eq_planning_annual_plannification_date{
                border: solid 1px black;
                text-align: center;
                width:50px;
                height:auto;
                font-size : 10px;
                color:#20bbd8; 
                display: inline-table;
                margin-left:-2px;
            }

            .eq_planning_annual_plannification_date_value{
                border: solid 1px black;
                text-align: center;
                width:48.02px;
                height:auto;
                font-size : 10px;
                color:#20bbd8; 
                display: inline-table;
                margin-left:0px;
            }

            .eq_planning_annual_plannification_date_value_color{
                border: solid 1px black;
                text-align: center;
                width:48.02px;
                height:auto;
                font-size : 10px;
                color:#20bbd8; 
                display: inline-table;
                margin-left:0px;
                background-color: #20bbd8;
            }
            





            .eq_planning_annual_periodicity{
                border: solid 1px black;
                text-align: center;
                width:50px;
                font-size : 15px;
                color:#20bbd8
            }   
        
        }
    }

        
        

    

</style>
<!--File name : AnnualEquipmentPlanningV1.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the annual maintenance of equipment-->

<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>'</p>
            <div class="list_eq_top_infos">
                <div class="list_eq_pdf_logo ">
                  <img src="/images/logo.jpg" alt="Alpha logo" class="logo" >
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
                        Operations planned
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_plannification">
                        Annual Planification
                        <b-row>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[0].month}} {{periode[0].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[1].month}} {{periode[1].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[2].month}} {{periode[2].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[3].month}} {{periode[3].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[4].month}} {{periode[4].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[5].month}} {{periode[5].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[6].month}} {{periode[6].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[7].month}} {{periode[7].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[8].month}} {{periode[8].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[9].month}} {{periode[9].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[10].month}} {{periode[10].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[11].month}} {{periode[11].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[12].month}} {{periode[12].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[13].month}} {{periode[13].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[14].month}} {{periode[14].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[15].month}} {{periode[15].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[16].month}} {{periode[16].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[17].month}} {{periode[17].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[18].month}} {{periode[18].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[19].month}} {{periode[19].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[20].month}} {{periode[20].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[21].month}} {{periode[21].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[22].month}} {{periode[22].year}}
                            </b-col>
                            <b-col cols="4" class="eq_planning_annual_plannification_date">
                                    {{periode[23].month}} {{periode[23].year}}
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col cols="1" class="eq_planning_annual_periodicity_title">
                        Freq.
                    </b-col>
                </b-row>

                <div v-for="(eq,index1) in eq" :key="index1">
                    <div v-if="eq.preventive_maintenance_operations.length!=0">
                        <div v-for="(eq_prvMtnOp,index2) in eq.preventive_maintenance_operations" :key="index2">
                            <b-row>
                                <b-col cols="1" class="eq_planning_annual_internalReference_value">
                                <p class="info"> {{eq.internalReference}} </p>
                                </b-col>
                                <b-col cols="1" class="eq_planning_annual_name_value">
                                    <p class="info">{{eq.name}}</p>
                                </b-col>
                                <b-col cols="1" class="eq_planning_annual_prvMtnOp_value">
                                        <p class="info">Operation {{eq_prvMtnOp.prvMtnOp_number}}</p>
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[0].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[0])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[1].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[1])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col  v-if="maintenancePlanned(periode[2].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                        @click="handleClick(eq, periode[2])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col  v-if="maintenancePlanned(periode[3].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                        @click="handleClick(eq, periode[3])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[4].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[4])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[5].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[5])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[6].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[6])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[7].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[7])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[8].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[8])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[9].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[9])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[10].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[10])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[11].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[11])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[12].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[12])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[13].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[13])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[14].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[14])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[15].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[15])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[16].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[16])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[17].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[17])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[18].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[18])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[19].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[19])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[20].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[20])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[21].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[21])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[22].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[22])">
                                </b-col>
                                <b-col v-else cols="4" class="eq_planning_annual_plannification_date_value">
                                </b-col>

                                <b-col v-if="maintenancePlanned(periode[23].id,eq_prvMtnOp.prvMtnOp_nextDate)" cols="4" class="eq_planning_annual_plannification_date_value_color"
                                       @click="handleClick(eq, periode[23])">
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
        </div>
        <button class="btn btn-primary" @click="generateReport" >Generate PDF</button>
        <EventDetailsModal ref="event_details" :prvMtnOp="prvMtnOp" @modalClosed="modalClosed"></EventDetailsModal>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
import EventDetailsModal from "./EventDetailsModal.vue";
export default {
    data(){
        return{
            eq_id:this.$route.params.id,
            loaded:true,
            eq:[],
            periode:[],
            prvMtnOp:[],
        }
    },

    components: {
        EventDetailsModal

    },
    methods: {
        maintenancePlanned(id_periode, AllnextDate){
            let res=false ;
            if (AllnextDate.length==0)
                return res;
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
        handleClick(eq, index) {
            console.log(eq);
            console.log(index);
            this.prvMtnOp.push({
                    eq_internalReference: eq.internalReference,
                    prvMtnOp_number: eq.preventive_maintenance_operations[0].prvMtnOp_number,
                    eq_id:eq.id,
                    state_id:eq.state_id,
                    prvMtnOp_description:eq.preventive_maintenance_operations[0].prvMtnOp_description,
                    prvMtnOp_protocol:eq.preventive_maintenance_operations[0].prvMtnOp_protocol,
                    prvMtnOp_nextDate:eq.preventive_maintenance_operations[0].prvMtnOp_day + '-' + index.id,
                    prvMtnOp_periodicity:eq.preventive_maintenance_operations[0].prvMtnOp_periodicity,
                    prvMtnOp_symbolPeriodicity:eq.preventive_maintenance_operations[0].prvMtnOp_symbolPeriodicity,
                });
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed(){
            this.prvMtnOp=[]
        },
    },
    created(){
        axios.get('/equipment/prvMtnOp/planning')
            .then (response=>{
                this.eq = response.data;
                console.log(response.data);
        });
        axios.get('/send/equipment/planning/periode')
        .then (response=>{
                this.periode = response.data;
                console.log(response.data);
        });

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
                font-size : 8px ;
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
            width:1380px;

            .eq_planning_annual_internalReference{
                border: solid 0.5px black;
                text-align: center;
                width:100px;
                height:49.5px;
                font-size : 10px;
                color:#20bbd8 ;
                line-height: 25px;
            }
                .eq_planning_annual_name{
                border: solid 0.5px black;
                text-align: center;
                width:85px;
                height:49.5px;
                font-size : 8px;
                color:#20bbd8;
                line-height: 25px;
            }
            .eq_planning_annual_prvMtnOp{
                border: solid 0.5px black;
                text-align: center;
                width:75px;
                height:49.5px;
                font-size : 8px;
                color:#20bbd8;
                line-height: 25px;
            }

            .eq_planning_annual_internalReference_value{
                border: solid 0.5px black;
                text-align: center;
                width:100px;
                height:auto;
                font-size : 9px;
                color:#20bbd8 ;
            }
                .eq_planning_annual_name_value{
                border: solid 0.5px black;
                text-align: center;
                width:85px;
                height:auto;
                font-size : 9px;
                color:#20bbd8;
            }
            .eq_planning_annual_prvMtnOp_value{
                border: solid 0.5px black;
                text-align: center;
                width:75px;
                height:auto;
                font-size : 9px;
                color:#20bbd8;
            }

            .eq_planning_annual_plannification{
                border: solid 0.5px black;
                text-align: center;
                width:1007px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }

            .eq_planning_annual_plannification_date{
                border: solid 0.5px black;
                text-align: center;
                width:35px;
                height:auto;
                font-size : 7px;
                color:#20bbd8;
                display: inline-table;
                margin-left:0px;
                margin-top:1px;
            }

            .eq_planning_annual_plannification_date_value{
                border: solid 0.5px black;
                text-align: center;
                width:41.95px;
                height:auto;
                font-size : 7px;
                color:#20bbd8;
                display: inline-table;
                margin-left:0px;
            }

            .eq_planning_annual_plannification_date_value_color{
                border: solid 0.5px black;
                text-align: center;
                width:41.95px;
                height:auto;
                font-size : 10px;
                color:#20bbd8;
                display: inline-table;
                margin-left:0px;
                background-color: #20bbd8;
            }






            .eq_planning_annual_periodicity{
                border: solid 0.5px black;
                text-align: center;
                width:40px;
                font-size : 15px;
                color:#20bbd8
            }

            .eq_planning_annual_periodicity_title{
                border: solid 0.5px black;
                text-align: center;
                width:40px;
                font-size : 10px;
                color:#20bbd8;
                margin-left: 0px;
                line-height: 50px;
                height:49.5px;
            }

        }
    }






</style>

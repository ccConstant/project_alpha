<!--File name : MonthlyMMEPlanning.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to show the annually planning of maintenance linked to the different MME-->

<template>
    <div v-if="loaded==true">
        <div id="page" class="page">
            <table class="header">
                <tbody>
                <tr class="ignored">
                    <td rowspan="2" style="text-align: center; vertical-align: middle;" class="ignored">
                        <img alt="logo Alpha" src="/images/logo.jpg"
                             style="width: max-content; height: max-content">
                    </td>
                    <td colspan="2">
                        <h2 style="text-align: center">
                            Planning of mme calibration and maintenance
                        </h2>
                    </td>
                    <td>
                        <h2>
                            Version :
                        </h2>
                        <h2>

                        </h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h2>
                            Technical Review :
                        </h2>
                        <p>

                        </p>
                    </td>
                    <td>
                        <h2>
                            Quality Review :
                        </h2>
                        <p>

                        </p>
                    </td>
                    <td>
                        <h2>
                            Date :
                        </h2>
                        <p>

                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <table class="table-bordered">
                <thead style="text-align: center;">
                <tr>
                    <th rowspan="2">
                        Internal Reference
                    </th>
                    <th rowspan="2">
                        Name
                    </th>
                    <th rowspan="2">
                        Operations planned
                    </th>
                    <th colspan="24">
                        <h2>
                            Annual Planification
                        </h2>
                    </th>
                    <th rowspan="2">
                        Frequency
                    </th>
                </tr>
                <tr>
                    <th v-for="p in periode" class="eq_planning_annual_plannification_date">
                        {{p.month}} {{p.year}}
                    </th>
                </tr>
                </thead>
                <tbody v-for="m in mme">
                <tr v-for="mme_verif in m.verifications">
                    <td>
                        {{m.internalReference}}
                    </td>
                    <td>
                        {{m.name}}
                    </td>
                    <td>
                        Operation {{mme_verif.verif_number}}
                    </td>
                    <td v-for="p in periode"
                        v-if="maintenancePlanned(p.id,mme_verif.verif_nextDate)"
                        @click="handleClick(equipment, p)"
                        class="maintenance"
                    >
                    </td>
                    <td v-else>
                    </td>
                    <td>
                        {{mme_verif.verif_periodicity}} {{mme_verif.verif_symbolPeriodicity}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary" @click="generateReport" >Generate PDF</button>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
export default {
    data(){
        return{
            mme_id:this.$route.params.id,
            loaded:true,
            mme:[],
            periode:[],

        }
    },

    components: {
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
                output: 'AnnualMMECalendar.pdf',
            });
        },
        handleClick(mme, index) {
            /*let tmp = eq.preventive_maintenance_operations[0].prvMtnOp_day < 10 ? '0' + eq.preventive_maintenance_operations[0].prvMtnOp_day : eq.preventive_maintenance_operations[0].prvMtnOp_day;
            this.prvMtnOp.push({
                eq_internalReference: eq.internalReference,
                prvMtnOp_number: eq.preventive_maintenance_operations[0].prvMtnOp_number,
                eq_id:eq.id,
                state_id:eq.state_id,
                prvMtnOp_description:eq.preventive_maintenance_operations[0].prvMtnOp_description,
                prvMtnOp_protocol:eq.preventive_maintenance_operations[0].prvMtnOp_protocol,
                prvMtnOp_nextDate: tmp + '-' + index.id,
                prvMtnOp_periodicity:eq.preventive_maintenance_operations[0].prvMtnOp_periodicity,
                prvMtnOp_symbolPeriodicity:eq.preventive_maintenance_operations[0].prvMtnOp_symbolPeriodicity,
            });
            this.$refs.event_details.$bvModal.show('modal-event_details');*/
        },
        modalClosed(){
            /*this.prvMtnOp=[]*/
        },
    },
    created(){
        axios.get('/mme/verif/planning')
            .then (response=>{
                this.mme = response.data;
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

<style scoped>
    /*
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
            .list_mme_top_infos{
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

                .list_mme_pdf_logo{
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
                .list_mme_pdf_titre{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 00px;
                    left:250px;
                    height: 87px;
                    text-align:center;



                }
                .list_mme_fiche_de_vie_titre{
                    text-align: center;
                    position: relative;
                }
                .list_mme_pdf_index{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left: 892px;
                    top: 0px;
                    height: 86px;
                    width: 200px;

                }
                .mme_revued_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;

                    left :250px;
                    top: 86px;
                    height: 84px;
                    width: 400px;

                }
                .mme_approuved_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :650px;
                    top: 86px;
                    height: 84px;
                    width: 242px;
                }
                .mme_internalReference_pdf{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :892px;
                    top: 86px;
                    width: 200px;
                    height: 84px;
                }

            }


    .mme_infos_pdf{
        position: relative;
        margin-top:300px ;
        width : 1112px;

        .title_mme_pdf{
            width: 400px;
            font-size : 20px;
            font-weight: bold;
            margin-left:150px;
        }
    }

    .mme_planning_annual_table{
            margin-top:200px;
            margin-left:30px;
            width:1300px;
            .mme_planning_annual_internalReference{
                border: solid 0.5px black;
                text-align: center;
                width:100px;
                height:auto;
                font-size : 10px;
                 color:#20bbd8
            }
                .mme_planning_annual_name{
                border: solid 0.5px black;
                text-align: center;
                width:85px;
                height:auto;
                font-size : 10px;
                 color:#20bbd8
            }
            .mme_planning_annual_verif{
                border: solid 0.5px black;
                text-align: center;
                width:77px;
                height:auto;
                font-size : 10px;
                 color:#20bbd8
            }

            .mme_planning_annual_plannification{
                border: solid 0.5px black;
                text-align: center;
                width:1004.7px;
                height:auto;
                font-size : 15px;
                 color:#20bbd8
            }

            .mme_planning_annual_plannification_date{
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

            .mme_planning_annual_plannification_date_value{
                border: solid 0.5px black;
                text-align: center;
                width:41.85px;
                height:auto;
                font-size : 10px;
                color:#20bbd8;
                display: inline-table;
                margin-left:0px;
            }

            .mme_planning_annual_plannification_date_value_color{
                border: solid 0.5px black;
                text-align: center;
                width:41.85px;
                height:auto;
                font-size : 10px;
                color:#20bbd8;
                display: inline-table;
                margin-left:0px;
                background-color: #20bbd8;
            }

            .mme_planning_annual_periodicity{
                border: solid 0.5px black;
                text-align: center;
                width:40px;
                font-size : 15px;
                color:#20bbd8
            }

            .mme_planning_annual_internalReference_title{
                border: solid 0.5px black;
                text-align: center;
                width:100px;
                height:49.5px;
                font-size : 10px;
                color:#20bbd8 ;
                line-height: 25px;
            }
                .mme_planning_annual_name_title{
                border: solid 0.5px black;
                text-align: center;
                width:85px;
                height:49.5px;
                font-size : 10px;
                color:#20bbd8;
                line-height: 50px;
            }
            .mme_planning_annual_verif_title{
                border: solid 0.5px black;
                text-align: center;
                width:77px;
                height:49.5px;
                font-size : 10px;
                color:#20bbd8 ;
                line-height: 25px;
            }

            .mme_planning_annual_periodicity_title{
                border: solid 0.5px black;
                text-align: center;
                width:40px;
                font-size : 10px;
                color:#20bbd8;
                margin-left: 0px;
                height:49.5px;
                line-height: 50px;
            }

        }
    }
    */
.page {
    display: block;
    margin: auto;
    page-break-inside: auto;
}
table {
    border: 1px solid black;
}
table tr:not(.ignored) {
    border: 1px solid black;
}
table td:not(.ignored) {
    border: 1px solid black;
}
table th:not(.ignored) {
    border: 1px solid black;
}
h2 {
    margin: auto;
}
p {
    font-size: 20px;
    font-style: normal;
    font-weight: normal;
    font-family: Calibri;
    marign-left: 10px;
}
.maintenance {
    background-color: #6d7ef1;
}
</style>

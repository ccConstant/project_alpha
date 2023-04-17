<!--File name : MonthlyMMEPlanning.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to show the monthly planning of maintenance linked to the different MME-->

<template>
    <div class="eq_maintenance_page">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--<div class="remind_containers">
                <div class="remindOpeLate_container">
                    <h2>Maintenance late</h2>
                    <li v-for="(prvMtnOp, index) in  pageOfItems_LimitPassed" :key="index" class="list-group-item"
                    @click="handleListClick(prvMtnOp.id,prvMtnOp.internalReference,prvMtnOp.state_id,prvMtnOp.preventive_maintenance_operations)">
                        {{prvMtnOp.internalReference}}
                    </li>
                    <jw-pagination class="eq_list_pagination" :pageSize=7 :items="prvMtnOp_LimitPassed" @changePage="onChangePage_limitPassed"></jw-pagination>
                </div>

                <div class="remindOpeToDo_container">
                    <h2>Maintenance to do</h2>
                    <li v-for="(prvMtnOp, index) in  pageOfItems_ToDo" :key="index" class="list-group-item"
                    @click="handleListClick(prvMtnOp.id,prvMtnOp.internalReference,prvMtnOp.state_id,prvMtnOp.preventive_maintenance_operations)">
                        {{prvMtnOp.internalReference}}
                    </li>
                    <jw-pagination class="eq_list_pagination" :pageSize=7 :items="prvMtnOp_ToDo" @changePage="onChangePage_ToDo"></jw-pagination>
                </div>
            </div>-->

            <div class="eq_planning_pdf">
                <div class="title_planning_pdf">
                    MONTHLY MME PLANNING
                </div>
                <div class="planning_table">
                    <b-row>
                        <b-col cols="1" class="planning_table_internalReference">
                            Internal Reference
                        </b-col>
                        <b-col cols="1" class="planning_table_name">
                            Name
                        </b-col>
                        <b-col cols="4" class="planning_table_number">
                           VERIF Number
                        </b-col>
                        <b-col cols="4"  class="planning_table_nextDate">
                            VERIF NextDate
                        </b-col>
                    </b-row>
                    <div v-for="(verif,index) in mme_nextMonth " :key="index">
                        <b-row>
                            <b-col cols="1"   class="planning_table_internalReference">
                                 <p class="text-primary">{{verif.Internal_Ref}}</p>
                            </b-col>
                            <b-col cols="4" class="planning_table_name">
                                <p class="text-primary">{{verif.Description}}</p>
                            </b-col>
                            <b-col cols="4"  class="planning_table_number">
                               <p class="text-primary" @click="handleListClick(verif.mme_id,verif.Internal_Ref,verif.state_id,verif.Number, verif.Description, verif.Protocol, verif.nextDate, verif.verif_periodicity, verif.verif_symbolPeriodicity, verif.verif_expectedResult, verif.verif_nonComplianceLimit)"> {{verif.Number}}</p>
                            </b-col>
                            <b-col cols="4"  class="planning_table_nextDate">
                                <p class="text-primary"> {{verif.nextDate}}</p>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>
            <MMEEventDetailsModal ref="event_details" :verif="verif" @modalClosed="modalClosed"></MMEEventDetailsModal>
        </div>
    </div>
</template>

<script>



import '@fullcalendar/core/vdom' // solves a problem with Vite
import FullCalendar from '@fullcalendar/vue'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import MMEEventDetailsModal from './MMEEventDetailsModal.vue'
import listPlugin from '@fullcalendar/list';
import momentPlugin from '@fullcalendar/moment';
import moment from 'moment'
export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available
        MMEEventDetailsModal
    },
    data() {
        return {
            verif:[],
            verif_LimitPassed:[],
            verif_ToDo:[],
            pageOfItems_LimitPassed: [],
            pageOfItems_ToDo: [],
            loaded:false,
            eq_nextMonth:[],
        }
    },
    methods: {
        handleEventClick(arg) {
            this.prvMtnOp.push({eq_internalReference:arg.event.title, prvMtnOp_number:arg.event.extendedProps.number,
                eq_id:arg.event.extendedProps.eq_id,state_id:arg.event.extendedProps.state_id,
                prvMtnOp_description:arg.event.extendedProps.description,prvMtnOp_protocol:arg.event.extendedProps.protocol,
                prvMtnOp_nextDate:arg.event.extendedProps.operation_date });
            console.log(arg)
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        handleListClick(mme_id,mme_internalReference,state_id,number, description, protocol, next_date, periodicity, symbolPeriodicity, expectedResult, nonComplianceLimit) {
                this.verif.push({mme_internalReference:mme_internalReference,verif_number:number,
                mme_id:mme_id,state_id:state_id,verif_description:description,
                verif_protocol:protocol,verif_nextDate:next_date, verif_periodicity:periodicity, verif_symbolPeriodicity:symbolPeriodicity, verif_expectedResult:expectedResult, verif_nonComplianceLimit:nonComplianceLimit});
                this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed(){
            this.verif=[]
        },
        onChangePage_limitPassed(pageOfItems) {
            // update page of items
            this.pageOfItems_LimitPassed = pageOfItems;
		},
        onChangePage_ToDo(pageOfItems){
            this.pageOfItems_ToDo = pageOfItems;
        }
    },
    created(){
        /*axios.get('/equipment/prvMtnOp/revisionLimitPassed')
        .then (response=>{
            console.log(response.data)
            this.prvMtnOp_LimitPassed=response.data;

        });
        axios.get('/equipment/prvMtnOp/revisionDatePassed')
        .then (response=>{
            console.log(response.data)
            this.prvMtnOp_ToDo=response.data;
        })*/

        axios.get('/mme/verif/planning_monthly')
        .then (response=>{
            console.log(response.data)
            this.mme_nextMonth=response.data;
            this.loaded=true;
        })

    }

}
</script>

<style lang="scss">
.eq_maintenance_page{
    .remind_containers{
        float:left;
        margin-left:10px ;
        width: auto;
        .remindOpeLate_container{
            width: 100%;
            height: auto;
            border:solid 1px lightcoral;
            border-radius: 15px;
            background-color:lightcoral ;
            margin-right: 10px;
            h2{
                text-align:center ;
                color:red;
            }
        }
        .remindOpeToDo_container{
            width: 100%;
            height: auto;
            border:solid 1px lightblue;
            border-radius: 15px;
            background-color:lightblue ;
            margin-right: 10px;
            h2{
                text-align:center ;
                color:grey;
            }
        }
        .eq_list_pagination{
            display: block;
            margin-right: 10px;
        }
    }
    .calendar_container{
        margin-top :50px;
        margin-left: 100px ;
    }
    .eq_planning_pdf{
        position: relative;
        margin-top:10px ;
        width : 1112px;
        .title_planning_pdf{
            width: 400px;
            font-size : 20px;
            font-weight: bold;
            margin-left:150px;
        }
        .planning_table{
            margin-left:163px;
            width:1042px;
            .planning_table_internalReference{
                border: none ;
                text-align: center;
                width:200px;
            }
            .planning_table_name{
                border: none ;
                text-align: center;
                width:200px;
            }
            .planning_table_number{
                border: none ;
                text-align: center;
                width: 200px;
            }
            .planning_table_nextDate{
                border: none;
                text-align: center;
                width : 300px;
            }
        }
    }
}
</style>

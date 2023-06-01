<!--File name : MonthlyEquipmentPlanning.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the monthly planning of maintenances-->

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
                    MONTHLY EQUIPMENT PLANNING
                </div>
                <p style="margin-left: 150px;">
                    Click on line concerned to record a preventive maintenance operation
                </p>
                <div class="planning_table">
                    <b-row>
                        <b-col cols="1" class="planning_table_internalReference">
                            Internal Reference
                        </b-col>
                        <b-col cols="1" class="planning_table_name">
                            Name
                        </b-col>
                        <b-col cols="4" class="planning_table_number">
                           OP Number
                        </b-col>
                        <b-col cols="4"  class="planning_table_nextDate">
                            OP NextDate
                        </b-col>
                    </b-row>
                    <div v-for="(prvMtnOp,index) in eq_nextMonth " :key="index">
                        <b-row @click="handleListClick(prvMtnOp.eq_id,prvMtnOp.Internal_Ref,prvMtnOp.state_id,prvMtnOp.Number, prvMtnOp.Description, prvMtnOp.Protocol, prvMtnOp.nextDate, prvMtnOp.prvMtnOp_periodicity, prvMtnOp.prvMtnOp_symbolPeriodicity)">
                            <b-col cols="1"   class="planning_table_internalReference">
                                 <p class="text-primary">{{prvMtnOp.Internal_Ref}}</p>
                            </b-col>
                            <b-col cols="4" class="planning_table_name">
                                <p class="text-primary">{{prvMtnOp.Description}}</p>
                            </b-col>
                            <b-col cols="4"  class="planning_table_number">
                               <p class="text-primary"> {{prvMtnOp.Number}}</p>
                            </b-col>
                            <b-col cols="4"  class="planning_table_nextDate">
                                <p class="text-primary">
                                    {{ new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getDate() }}
                                    {{ new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).toDateString().slice(4, 7) }}
                                    {{ new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getFullYear() }}
                                </p>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>
            <EventDetailsModal ref="event_details" :prvMtnOp="prvMtnOp" @modalClosed="modalClosed"></EventDetailsModal>
        </div>
    </div>
</template>

<script>



import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import EventDetailsModal from './EventDetailsModal.vue'
import listPlugin from '@fullcalendar/list';
import momentPlugin from '@fullcalendar/moment';
import moment from 'moment'
export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available
        EventDetailsModal
    },
    data() {
        return {
            prvMtnOp:[],
            prvMtnOp_LimitPassed:[],
            prvMtnOp_ToDo:[],
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
        handleListClick(eq_id,eq_internalReference,state_id,number, description, protocol, next_date, periodicity, symbolPeriodicity) {
                this.prvMtnOp.push({eq_internalReference:eq_internalReference,prvMtnOp_number:number,
                eq_id:eq_id,state_id:state_id,prvMtnOp_description:description,
                prvMtnOp_protocol:protocol,prvMtnOp_nextDate:next_date, prvMtnOp_periodicity:periodicity, prvMtnOp_symbolPeriodicity:symbolPeriodicity});
                this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed(){
            this.prvMtnOp=[]
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
        axios.get('/equipment/prvMtnOp/planning')
            .then (response=>{
                console.log(response.data)
                for (const data of response.data) {
                    this.calendarOptions.resources.push({title:data.internalReference,id:data.internalReference});
                    for(const operation of data.preventive_maintenance_operations){
                        this.calendarOptions.events.push({title:data.internalReference,date:operation.prvMtnOp_nextDate,
                         eq_id:data.id,state_id:data.state_id,
                         number:operation.prvMtnOp_number,id:operation.id,
                         description:operation.prvMtnOp_description,
                         operation_date:moment(operation.prvMtnOp_nextDate).format('D MMM YYYY hh:mm a'),
                         protocol:operation.prvMtnOp_protocol,
                         resourceId:data.internalReference});
                    }
                }
        })
        axios.get('/equipment/prvMtnOp/revisionLimitPassed')
        .then (response=>{
            console.log(response.data)
            this.prvMtnOp_LimitPassed=response.data;

        });
        axios.get('/equipment/prvMtnOp/revisionDatePassed')
        .then (response=>{
            console.log(response.data)
            this.prvMtnOp_ToDo=response.data;
        })

        axios.get('/equipment/prvMtnOp/planning_monthly')
        .then (response=>{
            console.log(response.data)
            this.eq_nextMonth=response.data;
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

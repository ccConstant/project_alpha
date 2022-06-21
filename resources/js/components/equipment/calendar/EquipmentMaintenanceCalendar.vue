<template>
    <div>
        <div class="remind_containers">
            <div class="remindOpeLate_container">
                <h2>Maintenance late</h2>
                <li v-for="(prvMtnOp, index) in  prvMtnOp_LimitPassed" :key="index" class="list-group-item"
                @click="handleListClick(prvMtnOp.id,prvMtnOp.internalReference,prvMtnOp.state_id,prvMtnOp.preventive_maintenance_operations)">
                    {{prvMtnOp.internalReference}}
                </li>
            </div>
            <div class="remindOpeToDo_container">
                <h2>Maintenance to do</h2>
                <li v-for="(prvMtnOp, index) in  prvMtnOp_LimitPassed" :key="index" class="list-group-item"
                @click="handleListClick(prvMtnOp.id,prvMtnOp.internalReference,prvMtnOp.state_id,prvMtnOp.preventive_maintenance_operations)">
                    {{prvMtnOp.internalReference}}
                </li>
            </div>
        </div>

        <EventDetailsModal ref="event_details" @modalClosed="modalClosed" :prvMtnOps="prvMtnOp"/>
        <div class='calendar_container'>
            <div class='calendar'>
                <FullCalendar  :options="calendarOptions"/>
            </div>
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
            calendarOptions: {
                plugins: [ dayGridPlugin,interactionPlugin,listPlugin,momentPlugin,resourceTimelinePlugin],
                initialView: 'resourceTimelineYear',
                headerToolbar: {
                    left: 'listMonth,listWeek,resourceTimelineMonth',
                    center: 'title',
                    right :'prev today next',
                },
                eventClick:this.handleEventClick,
                dateClick:this.handleDateClick,
                events:[],
                displayEventTime : false,
                resources: [
                ],
                titleFormat: 'D MMM YYYY',
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives'
            },
            prvMtnOp:[],
            prvMtnOp_LimitPassed:[]
            
           
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
        handleListClick(eq_id,eq_internalReference,state_id,prvMtnOps) {
            for(const prvMtnOp of prvMtnOps){
                this.prvMtnOp.push({eq_internalReference:eq_internalReference,prvMtnOp_number:prvMtnOp.prvMtnOp_number,
                eq_id:eq_id,state_id:state_id,prvMtnOp_description:prvMtnOp.prvMtnOp_description,
                prvMtnOp_protocol:prvMtnOp.prvMtnOp_protocol,prvMtnOp_nextDate:prvMtnOp.prvMtnOp_nextDate});
            }
            
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed(){
            this.prvMtnOp=[]
        }
    },
    created(){
        axios.get('/equipment/prvMtnOp/planning')
            .then (response=>{
                this.prvMtnOp_LimitPassed=response.data;
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

            })
    }

}
</script>

<style lang="scss">
.remind_containers{
        float:left;
        .remindOpeLate_container{
        width: 300px;
        height: auto;
        border:solid 1px lightcoral;
        border-radius: 30px;
        background-color:lightcoral ;
        margin-right: 10px;
            h2{
                text-align:center ;
                color:red;
            }
        }
        .remindOpeLate_container{
            width: 300px;
            height: auto;
            border:solid 1px lightcoral;
            border-radius: 30px;
            background-color:lightcoral ;
            margin-right: 10px;

         }   
}

.calendar_container{
   margin-top :50px;
   width: auto;
   height: auto;
}

</style>
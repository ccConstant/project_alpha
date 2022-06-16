<template>
    <div>
        <EventDetailsModal ref="event_details" @modalClosed="modalClosed" :prvMtnOps="prvMtnOp"/>
        <div class='container calendar_container'>
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
                initialView: 'listMonth',
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
        modalClosed(){
            this.prvMtnOp=[]
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
    }

}
</script>

<style lang="scss">
</style>
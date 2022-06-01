<template>
    <div>
        <EventDetailsModal ref="event_details" @modalClosed="modalClosed" :prvMtnOps="prvMtnOp"/>
        <div class='container'>
            <div class='calendar'>
                <FullCalendar  :options="calendarOptions" />
            </div>
        </div>
        

    </div>


</template>

<script>



import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import EventDetailsModal from './EventDetailsModal.vue'

export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available
        EventDetailsModal
    },
    data() {
        return {
            eventList:[],
            calendarOptions: {
                plugins: [ dayGridPlugin,interactionPlugin  ],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right :'prev today next'
                },
                eventClick:this.handleEventClick,
                dateClick:this.handleDateClick,
                events:[],
            },
            prvMtnOp:[]
            
           
        }
    },
    methods: {
        handleEventClick(arg) {
            this.$nextTick(() => {
                this.prvMtnOp.push({eq_internalReference:arg.event.title, prvMtnOp_number:arg.event.extendedProps.number,
                 prvMtnOp_description:arg.event.extendedProps.description,prvMtnOp_protocol:arg.event.extendedProps.protocol });
                this.$refs.event_details.$bvModal.show('modal-event_details');
            })

            

        },
        modalClosed(){
            this.prvMtnOp=[]
        }
    },
    created(){
        axios.get('/equipment/prvMtnOp/planning')
            .then (response=>{
                for (const data of response.data) {
                    for(const operation of data.preventive_maintenance_operations){
                        this.calendarOptions.events.push({title:data.internalReference,date:operation.prvMtnOp_nextDate,number:operation.prvMtnOp_number,id:operation.id,
                            description:operation.prvMtnOp_description,protocol:operation.prvMtnOp_protocol})
                    }
                }
            })
            .catch(error => console.log(error)) ;
    }

}
</script>

<style>

</style>
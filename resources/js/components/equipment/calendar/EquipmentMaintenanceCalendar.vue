<template>
    <div>
        <FullCalendar :options="calendarOptions" />
    </div>
</template>

<script>



import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
export default {
    components: {
        FullCalendar // make the <FullCalendar> tag available
    },
    data() {
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin,interactionPlugin  ],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right :'prev today next'
                },
                dateClick: this.handleDateClick,
                events: [
                    { title: 'event 1', date: '2022-05-05' },
                    { title: 'event 2', date: '2022-05-06' }
                ]
            },
           
        }
    },
    methods: {
        handleDateClick: function(arg) {
        alert('date click! ' + arg.dateStr)
        }
    }

}
</script>

<style>

</style>
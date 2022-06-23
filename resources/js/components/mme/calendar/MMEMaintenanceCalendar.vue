<template>
    <div class="mme_maintenance_page">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="remind_containers">
                <div class="remindOpeLate_container">
                    <h2>Maintenance late</h2>
                    <li v-for="(verif, index) in  pageOfItems_LimitPassed" :key="index" class="list-group-item"
                    @click="handleListClick(verif.id,verif.internalReference,verif.state_id,verif.preventive_maintenance_operations)">
                        {{verif.internalReference}}
                    </li>
                    <jw-pagination class="mme_list_pagination" :pageSize=7 :items="verif_LimitPassed" @changePage="onChangePage_limitPassed"></jw-pagination>
                </div>

                <div class="remindOpeToDo_container">
                    <h2>Maintenance to do</h2>
                    <li v-for="(verif, index) in  pageOfItems_ToDo" :key="index" class="list-group-item"
                    @click="handleListClick(verif.id,verif.internalReference,verif.state_id,verif.preventive_maintenance_operations)">
                        {{verif.internalReference}}
                    </li>
                    <jw-pagination class="mme_list_pagination" :pageSize=7 :items="verif_ToDo" @changePage="onChangePage_ToDo"></jw-pagination>
                </div>
            </div>

            <MMEEventDetailsModal ref="event_details" @modalClosed="modalClosed" :verifs="verif"/>
            <div class='container-xxl'>
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
                height: 'auto',
                displayEventTime : false,
                resources: [
                ],
                titleFormat: 'D MMM YYYY',
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives'
            },
            verif:[],
            verif_LimitPassed:[],
            verif_ToDo:[],
            pageOfItems_LimitPassed: [],
            pageOfItems_ToDo: [],
            loaded:false
        }
    },
    methods:{
        handleEventClick(arg) {
            this.verif.push({mme_internalReference:arg.event.title, verif_number:arg.event.extendedProps.number,
                mme_id:arg.event.extendedProps.mme_id,state_id:arg.event.extendedProps.state_id,
                verif_expectedResult:arg.event.extendedProps.expectedResult,verif_nonComplianceLimit:arg.event.extendedProps.nonComplianceLimit,
                verif_description:arg.event.extendedProps.description,verif_protocol:arg.event.extendedProps.protocol,
                verif_nextDate:arg.event.extendedProps.operation_date });
            console.log(arg)
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        handleListClick(mme_id,mme_internalReference,state_id,verifs) {
            for(const verif of verifs){
                this.verif.push({mme_internalReference:mme_internalReference,verif_number:verif.verif_number,
                mme_id:mme_id,state_id:state_id,verif_description:verif.verif_description,
                verif_expectedResult:verif.verif_expectedResult,verif_nonComplianceLimit:verif.verif_nonComplianceLimit,
                verif_protocol:verif.verif_protocol,verif_nextDate:verif.verif_nextDate});
            }
            
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
        axios.get('/mme/verif/planning')
            .then (response=>{
                console.log("verif")
                console.log(response.data)
                for (const data of response.data) {
                    this.calendarOptions.resources.push({title:data.internalReference,id:data.internalReference});
                    for(const operation of data.preventive_maintenance_operations){
                        this.calendarOptions.events.push({title:data.internalReference,date:operation.verif_nextDate,
                         mme_id:data.id,state_id:data.state_id,
                         number:operation.verif_number,id:operation.id,
                         description:operation.verif_description, expectedResult:operation.verif_expectedResult,
                         nonComplianceLimit:operation.verif_nonComplianceLimit,
                         operation_date:moment(operation.verif_nextDate).format('D MMM YYYY hh:mm a'),
                         protocol:operation.verif_protocol,
                         resourceId:data.internalReference});
                    }
                }
        })
        axios.get('/verif/send/revisionTimeLimitPassed')
        .then (response=>{
            console.log(response.data)
            this.verif_LimitPassed=response.data;

        });
        axios.get('/verif/send/revisionDatePassed')
        .then (response=>{
            console.log(response.data)
            this.verif_ToDo=response.data;
            this.loaded=true;
        })
        
    }
}
</script>

<style lang="scss">
.mme_maintenance_page{
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
        .mme_list_pagination{
            display: block;
            margin-right: 10px;
        } 
    }

    .calendar_container{
    margin-top :50px;
    margin-left: 100px ;

    }

}

</style>
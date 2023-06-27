<!--File name : MonthlyEquipmentPlanning.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the monthly planning of maintenances-->

<template>
    <div class="eq_maintenance_page">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="eq_planning_pdf">
                <div class="title_planning_pdf">
                    MONTHLY EQUIPMENT PLANNING
                </div>
                <p style="margin-left: 150px;">
                    Click on line concerned to record a preventive maintenance operation
                </p>
                <div class="planning_table">
                    <b-row>
                        <b-col class="planning_table_internalReference" cols="1">
                            Internal Reference
                        </b-col>
                        <b-col class="planning_table_name" cols="1">
                            Name
                        </b-col>
                        <b-col class="planning_table_number" cols="4">
                            OP Number
                        </b-col>
                        <b-col class="planning_table_nextDate" cols="4">
                            OP NextDate
                        </b-col>
                    </b-row>
                    <div v-for="(prvMtnOp,index) in eq_nextMonth " :key="index">
                        <div v-if="prvMtnOp.passed==true">
                            <b-row
                                @click="handleListClick(prvMtnOp.eq_id,prvMtnOp.Internal_Ref,prvMtnOp.state_id,prvMtnOp.Number, prvMtnOp.Description, prvMtnOp.Protocol, prvMtnOp.nextDate, prvMtnOp.prvMtnOp_periodicity, prvMtnOp.prvMtnOp_symbolPeriodicity)">
                                <b-col class="planning_table_internalReference" cols="1">
                                    <p class="redText">{{ prvMtnOp.Internal_Ref }}</p>
                                </b-col>
                                <b-col class="planning_table_name" cols="4">
                                    <p class="redText">{{ prvMtnOp.Name }}</p>
                                </b-col>
                                <b-col class="planning_table_number" cols="4">
                                    <p class="redText"> {{ prvMtnOp.Number }}</p>
                                </b-col>
                                <b-col class="planning_table_nextDate" cols="4">
                                    <p class="redText">
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getDate()
                                        }}
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5) - 1, prvMtnOp.nextDate.slice(0, 2)).toDateString().slice(4, 7)
                                        }}
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getFullYear()
                                        }}
                                    </p>
                                </b-col>
                            </b-row>
                        </div>
                        <div v-else>
                            <b-row
                                @click="handleListClick(prvMtnOp.eq_id,prvMtnOp.Internal_Ref,prvMtnOp.state_id,prvMtnOp.Number, prvMtnOp.Description, prvMtnOp.Protocol, prvMtnOp.nextDate, prvMtnOp.prvMtnOp_periodicity, prvMtnOp.prvMtnOp_symbolPeriodicity)">
                                <b-col class="planning_table_internalReference" cols="1">
                                    <p class="text-primary">{{ prvMtnOp.Internal_Ref }}</p>
                                </b-col>
                                <b-col class="planning_table_name" cols="4">
                                    <p class="text-primary">{{ prvMtnOp.Description }}</p>
                                </b-col>
                                <b-col class="planning_table_number" cols="4">
                                    <p class="text-primary"> {{ prvMtnOp.Number }}</p>
                                </b-col>
                                <b-col class="planning_table_nextDate" cols="4">
                                    <p class="text-primary">
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getDate()
                                        }}
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5) - 1, prvMtnOp.nextDate.slice(0, 2)).toDateString().slice(4, 7)
                                        }}
                                        {{
                                            new Date(prvMtnOp.nextDate.slice(6), prvMtnOp.nextDate.slice(3, 5), prvMtnOp.nextDate.slice(0, 2)).getFullYear()
                                        }}
                                    </p>
                                </b-col>
                            </b-row>
                        </div>
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
import EventDetailsModal from './EventDetailsModal.vue'

export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available
        EventDetailsModal
    },
    data() {
        return {
            prvMtnOp: [],
            prvMtnOp_LimitPassed: [],
            prvMtnOp_ToDo: [],
            pageOfItems_LimitPassed: [],
            pageOfItems_ToDo: [],
            loaded: false,
            eq_nextMonth: [],
        }
    },
    methods: {
        handleEventClick(arg) {
            this.prvMtnOp.push({
                eq_internalReference: arg.event.title,
                prvMtnOp_number: arg.event.extendedProps.number,
                eq_id: arg.event.extendedProps.eq_id,
                state_id: arg.event.extendedProps.state_id,
                prvMtnOp_description: arg.event.extendedProps.description,
                prvMtnOp_protocol: arg.event.extendedProps.protocol,
                prvMtnOp_nextDate: arg.event.extendedProps.operation_date
            });
            console.log(arg)
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        handleListClick(eq_id, eq_internalReference, state_id, number, description, protocol, next_date, periodicity, symbolPeriodicity) {
            this.prvMtnOp.push({
                eq_internalReference: eq_internalReference,
                prvMtnOp_number: number,
                eq_id: eq_id,
                state_id: state_id,
                prvMtnOp_description: description,
                prvMtnOp_protocol: protocol,
                prvMtnOp_nextDate: next_date,
                prvMtnOp_periodicity: periodicity,
                prvMtnOp_symbolPeriodicity: symbolPeriodicity
            });
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed() {
            this.prvMtnOp = []
        },
    },
    created() {

        axios.get('/equipment/prvMtnOp/planning_monthly')
            .then(response => {
                this.eq_nextMonth = response.data;
                this.loaded = true;
            })

    }

}
</script>

<style lang="scss">

.redText {
    color: red;
}

.eq_maintenance_page {
    .remind_containers {
        float: left;
        margin-left: 10px;
        width: auto;

        .remindOpeLate_container {
            width: 100%;
            height: auto;
            border: solid 1px lightcoral;
            border-radius: 15px;
            background-color: lightcoral;
            margin-right: 10px;

            h2 {
                text-align: center;
                color: red;
            }
        }

        .remindOpeToDo_container {
            width: 100%;
            height: auto;
            border: solid 1px lightblue;
            border-radius: 15px;
            background-color: lightblue;
            margin-right: 10px;

            h2 {
                text-align: center;
                color: grey;
            }
        }

        .eq_list_pagination {
            display: block;
            margin-right: 10px;
        }
    }

    .calendar_container {
        margin-top: 50px;
        margin-left: 100px;

    }

    .eq_planning_pdf {
        position: relative;
        margin-top: 10px;
        width: 1112px;

        .title_planning_pdf {
            width: 400px;
            font-size: 20px;
            font-weight: bold;
            margin-left: 150px;
        }

        .planning_table {
            margin-left: 163px;
            width: 1042px;

            .planning_table_internalReference {
                border: none;
                text-align: center;
                width: 200px;
            }

            .planning_table_name {
                border: none;
                text-align: center;
                width: 200px;
            }

            .planning_table_number {
                border: none;
                text-align: center;
                width: 200px;
            }

            .planning_table_nextDate {
                border: none;
                text-align: center;
                width: 300px;
            }
        }
    }

}


</style>

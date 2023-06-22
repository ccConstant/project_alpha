<!--File name : MonthlyMMEPlanning.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to show the monthly planning of maintenance linked to the different MME-->

<template>
    <div class="eq_maintenance_page">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>

            <div class="eq_planning_pdf">
                <div class="title_planning_pdf">
                    MONTHLY MME PLANNING
                </div>
                <div class="planning_table">
                    <b-row>
                        <b-col class="planning_table_internalReference" cols="1">
                            Internal Reference
                        </b-col>
                        <b-col class="planning_table_name" cols="1">
                            Name
                        </b-col>
                        <b-col class="planning_table_number" cols="4">
                            VERIF Number
                        </b-col>
                        <b-col class="planning_table_nextDate" cols="4">
                            VERIF NextDate
                        </b-col>
                    </b-row>
                    <div v-for="(verif,index) in mme_nextMonth " :key="index">
                        <div v-if="verif.passed==true">
                            <b-row
                                @click="handleListClick(verif.mme_id,verif.Internal_Ref,verif.state_id,verif.Number, verif.Description, verif.Protocol, verif.nextDate, verif.verif_periodicity, verif.verif_symbolPeriodicity, verif.verif_expectedResult, verif.verif_nonComplianceLimit)">
                                <b-col class="planning_table_internalReference" cols="1">
                                    <p class="redText">{{ verif.Internal_Ref }}</p>
                                </b-col>
                                <b-col class="planning_table_name" cols="4">
                                    <p class="redText">{{ verif.Name }}</p>
                                </b-col>
                                <b-col class="planning_table_number" cols="4">
                                    <p class="redText"> {{ verif.Number }}</p>
                                </b-col>
                                <b-col class="planning_table_nextDate" cols="4">
                                    <p v-if="verif.nextDate != null">
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7), verif.nextDate.slice(8)).getDate() }}
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7)-1, verif.nextDate.slice(8)).toDateString().slice(4, 7) }}
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7), verif.nextDate.slice(8)).getFullYear() }}
                                    </p>
                                    <p v-else>/</p>
                                </b-col>
                            </b-row>
                        </div>
                        <div v-else>
                            <b-row
                                @click="handleListClick(verif.mme_id,verif.Internal_Ref,verif.state_id,verif.Number, verif.Description, verif.Protocol, verif.nextDate, verif.verif_periodicity, verif.verif_symbolPeriodicity, verif.verif_expectedResult, verif.verif_nonComplianceLimit)">
                            <b-col class="planning_table_internalReference" cols="1">
                                    <p class="text-primary">{{ verif.Internal_Ref }}</p>
                                </b-col>
                                <b-col class="planning_table_name" cols="4">
                                    <p class="text-primary">{{ verif.Name }}</p>
                                </b-col>
                                <b-col class="planning_table_number" cols="4">
                                    <p class="text-primary"
                                       @click="handleListClick(verif.mme_id,verif.Internal_Ref,verif.state_id,verif.Number, verif.Description, verif.Protocol, verif.nextDate, verif.verif_periodicity, verif.verif_symbolPeriodicity, verif.verif_expectedResult, verif.verif_nonComplianceLimit)">
                                        {{ verif.Number }}</p>
                                </b-col>
                                <b-col class="planning_table_nextDate" cols="4">
                                    <p v-if="verif.nextDate != null">
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7), verif.nextDate.slice(8)).getDate() }}
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7)-1, verif.nextDate.slice(8)).toDateString().slice(4, 7) }}
                                        {{ new Date(verif.nextDate.slice(0, 4), verif.nextDate.slice(5, 7), verif.nextDate.slice(8)).getFullYear() }}
                                    </p>
                                    <p v-else>/</p>
                                </b-col>
                            </b-row>
                        </div>
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
import MMEEventDetailsModal from './MMEEventDetailsModal.vue'

export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available
        MMEEventDetailsModal
    },
    data() {
        return {
            verif: [],
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
        handleListClick(mme_id, mme_internalReference, state_id, number, description, protocol, next_date, periodicity, symbolPeriodicity, expectedResult, nonComplianceLimit) {
            this.verif.push({
                mme_internalReference: mme_internalReference,
                verif_number: number,
                mme_id: mme_id,
                state_id: state_id,
                verif_description: description,
                verif_protocol: protocol,
                verif_nextDate: next_date,
                verif_periodicity: periodicity,
                verif_symbolPeriodicity: symbolPeriodicity,
                verif_expectedResult: expectedResult,
                verif_nonComplianceLimit: nonComplianceLimit
            });
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed() {
            this.verif = []
        },
    },
    created() {
        axios.get('/mme/verif/planning_monthly')
            .then(response => {
                console.log(response.data)
                this.mme_nextMonth = response.data;
                this.loaded = true;
            })

    }

}
</script>

<style lang="scss">
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

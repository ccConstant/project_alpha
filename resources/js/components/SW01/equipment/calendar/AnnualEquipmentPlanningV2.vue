<!--File name : AnnualEquipmentPlanningV2.vue-->
<!--Creation date : 02 Jun 2023-->
<!--Update date : 02 Jun 2023-->
<!--Vue Component of the annual maintenance of equipment-->

<template>
    <div v-if="loaded==true">
        <div id="page" class="page">
            <table class="header">
                <tbody>
                <tr class="ignored">
                    <td class="ignored" rowspan="2" style="text-align: center; vertical-align: middle;">
                        <img alt="logo Alpha" src="/images/logo.jpg"
                             style="width: max-content; height: max-content">
                    </td>
                    <td colspan="2">
                        <h2 style="text-align: center">
                            Planning of equipment calibration and maintenance
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
                        {{ p.month }} {{ p.year }}
                    </th>
                </tr>
                </thead>
                <tbody v-for="equipment in eq">
                <tr v-for="eq_prvMtnOp in equipment.preventive_maintenance_operations">
                    <td>
                        {{ equipment.internalReference }}
                    </td>
                    <td>
                        {{ equipment.name }}
                    </td>
                    <td>
                        Operation {{ eq_prvMtnOp.prvMtnOp_number }}
                    </td>
                    <td v-for="p in periode"
                        v-if="maintenancePlanned(p.id,eq_prvMtnOp.prvMtnOp_nextDate)"
                        class="maintenance"
                        @click="handleClick(equipment, p)"
                    >
                    </td>
                    <td v-else>
                    </td>
                    <td>
                        {{ eq_prvMtnOp.prvMtnOp_periodicity }} {{ eq_prvMtnOp.prvMtnOp_symbolPeriodicity }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary" @click="generateReport">Generate PDF</button>
        <EventDetailsModal ref="event_details" :prvMtnOp="prvMtnOp" @modalClosed="modalClosed"/>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
import EventDetailsModal from "./EventDetailsModal.vue";

export default {
    data() {
        return {
            eq_id: this.$route.params.id,
            loaded: true,
            eq: [],
            periode: [],
            prvMtnOp: [],
        }
    },

    components: {
        EventDetailsModal

    },
    methods: {
        maintenancePlanned(id_periode, AllnextDate) {
            let res = false;
            if (AllnextDate.length == 0)
                return res;
            AllnextDate.forEach(nextdate => {
                if (nextdate == id_periode) {
                    res = true;
                }
            });
            return res;
        },
        generateReport() {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    orientation: 'landscape',
                    unit: 'px',
                    format: 'a4',
                    width: 100,
                    dpi: 600,
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: false,
                    useCORS: false,
                    dpi: 600,
                },
                imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 20,
                    right: 10,
                    bottom: 20,
                    left: 10,
                },
                output: 'AnnualEquipmentCalendar.pdf',
            });
        },
        handleClick(eq, index) {
            console.log(eq);
            console.log(index);
            let tmp = eq.preventive_maintenance_operations[0].prvMtnOp_day < 10 ? '0' + eq.preventive_maintenance_operations[0].prvMtnOp_day : eq.preventive_maintenance_operations[0].prvMtnOp_day;
            this.prvMtnOp.push({
                eq_internalReference: eq.internalReference,
                prvMtnOp_number: eq.preventive_maintenance_operations[0].prvMtnOp_number,
                eq_id: eq.id,
                state_id: eq.state_id,
                prvMtnOp_description: eq.preventive_maintenance_operations[0].prvMtnOp_description,
                prvMtnOp_protocol: eq.preventive_maintenance_operations[0].prvMtnOp_protocol,
                prvMtnOp_nextDate: tmp + '-' + index.id,
                prvMtnOp_periodicity: eq.preventive_maintenance_operations[0].prvMtnOp_periodicity,
                prvMtnOp_symbolPeriodicity: eq.preventive_maintenance_operations[0].prvMtnOp_symbolPeriodicity,
                prvMtnOp_lastComment: eq.prvMtnOp_lastComment,
            });
            this.$refs.event_details.$bvModal.show('modal-event_details');
        },
        modalClosed() {
            this.prvMtnOp = []
        },
    },
    created() {
        axios.get('/equipment/prvMtnOp/planning')
            .then(response => {
                this.eq = response.data;
                axios.get('/send/equipment/planning/periode')
                    .then(response => {
                        this.periode = response.data;
                    });
            });
    },
}
</script>

<style scoped>
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

<!--File name : ListOfEquipmentPDF.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component to generate a pdf version of the equipment list-->

<template>
    <div v-if="loaded==true">
        <div id="page" class="page">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Filters
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <table class="ignored">
                                <tbody>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <input v-model="searchTermInternRef"
                                               class="form-control search_bar align-self-center"
                                               placeholder="Filter the equipments by the internal ref" type="text">
                                    </td>
                                </tr>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <input v-model="searchTermExternRef"
                                               class="form-control search_bar align-self-center"
                                               placeholder="Filter the equipments by the external ref" type="text">
                                    </td>
                                </tr>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <input v-model="searchTermName"
                                               class="form-control search_bar align-self-center"
                                               placeholder="Filter the equipments by the designation" type="text">
                                    </td>
                                </tr>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <select v-model="searchStatus"
                                                class="form-control search_bar align-self-center">
                                            <option :value="-1">Filter the equipments by the actual state</option>
                                            <option :value="1">Waiting_for_referencing</option>
                                            <option :value="2">Waiting_for_installation</option>
                                            <option :value="3">In_use</option>
                                            <option :value="4">Under_maintenance</option>
                                            <option :value="5">On_hold</option>
                                            <option :value="6">Under_repair</option>
                                            <option :value="7">Broken</option>
                                            <option :value="8">Downgraded</option>
                                            <option :value="9">Reformed</option>
                                            <option :value="10">Lost</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <select v-model="searchSaveStatus"
                                                class="form-control search_bar align-self-center">
                                            <option :value="-1">Filter the equipments by the save status</option>
                                            <option :value="1">Drafted</option>
                                            <option :value="2">To Be Validated</option>
                                            <option :value="3">Validated</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="ignored">
                                    <td class="ignored">
                                        <select v-model="searchSigned"
                                                class="form-control search_bar align-self-center">
                                            <option :value="-1">Filter the equipments by the validation status</option>
                                            <option :value="0">Signed</option>
                                            <option :value="1">Not Signed</option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="printable">
                <table style="border: 1px solid black; text-align: center;">
                    <tbody>
                    <tr style="border: 1px solid black;">
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            <img alt="logo Alpha" src="/images/logo.jpg"
                                 style="width: max-content; height: max-content">
                        </td>
                        <td style="border: 1px solid black;">
                            <h2>
                                LIST OF EQUIPMENT USED BY ALPHA
                            </h2>
                        </td>
                        <td style="border: 1px solid black;">
                            <p>
                                Date :
                            </p>
                            <h2>
                                {{ new Date().getDate() }} {{ new Date().toString().substring(4, 7) }}
                                {{ new Date().getFullYear() }}
                            </h2>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p>
                </p>
                <table style="border: 1px solid black; text-align: center;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th style="border: 1px solid black;">
                            <h2>
                                Internal Reference
                            </h2>
                        </th>
                        <th style="border: 1px solid black;">
                            <h2>
                                External Reference
                            </h2>
                        </th>
                        <th style="border: 1px solid black;">
                            <h2>
                                Designation
                            </h2>
                        </th>
                        <th style="border: 1px solid black;">
                            <h2>
                                State
                            </h2>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(eq,index) in pageOfItems " :key="index" style="color: blue; border: 1px solid black;">
                        <td style="border: 1px solid black;">
                            <p>
                                {{ eq.eq_internalReference }}
                            </p>
                        </td>
                        <td style="border: 1px solid black;">
                            <p>
                                {{ eq.eq_externalReference }}
                            </p>
                        </td>
                        <td style="border: 1px solid black;">
                            <p>
                                {{ eq.eq_name }}
                            </p>
                        </td>
                        <td style="border: 1px solid black;">
                            <p>
                                {{ eq.eq_state }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <jw-pagination :items="filterByTerm" :pageSize=25 class="eq_list_pagination"
                       @changePage="onChangePage"></jw-pagination>
        <button class="btn btn-primary" @click="generateReport">Generate PDF</button>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
import ReferenceAnArticleFamilyMember from "../../../SW03/article/referencing/ReferenceAnArticleFamilyMember.vue";

export default {
    data() {
        return {
            eq_id: this.$route.params.id,
            list_eq: null,
            pageOfItems: [],
            loaded: false,
            searchTermInternRef: "",
            searchTermExternRef: "",
            searchTermName: "",
            searchStatus: -1,
            searchSaveStatus: -1,
            searchSigned: -1,
            outputName: "",
        }
    },

    components: {ReferenceAnArticleFamilyMember},
    methods: {
        generateReport() {
            let page = document.getElementById('printable');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width: 100
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                },
                imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 40,
                    right: 10,
                    bottom: 40,
                    left: 10,
                },
                output: this.outputName,
            });
        },
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
    },
    created() {
        axios.get('/equipment/equipments')
            .then(response => {
                this.list_eq = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    },
    computed: {
        filterByTerm() {
            this.outputName = 'EQ_LIST'
                + (this.searchTermInternRef !== "" ? '_internRef' : '')
                + (this.searchTermExternRef !== "" ? '_externRef' : '')
                + (this.searchTermName !== "" ? '_name' : '')
                + (this.searchStatus !== -1 ? '_status' : '')
                + (this.searchSaveStatus !== -1 ? '_saveStatus' : '')
                + (this.searchSigned !== -1 ? '_signed' : '')
                + '.pdf'
            let res = this.list_eq;
            if (this.searchTermInternRef !== "") {
                res = this.list_eq.filter(option => {
                    if (option.eq_internalReference !== null) {
                        return option.eq_internalReference.toLowerCase().includes(this.searchTermInternRef.toLowerCase());
                    }
                    return false;
                });
            }
            if (this.searchTermExternRef !== "") {
                res = this.list_eq.filter(option => {
                    if (option.eq_externalReference !== null) {
                        return option.eq_externalReference.toLowerCase().includes(this.searchTermExternRef.toLowerCase());
                    }
                    return false;
                });
            }
            if (this.searchTermName !== "") {
                res = this.list_eq.filter(option => {
                    if (option.eq_name !== null) {
                        return option.eq_name.toLowerCase().includes(this.searchTermName.toLowerCase());
                    }
                    return false;
                });
            }
            if (this.searchStatus !== -1) {
                res = res.filter(option => {
                    if (this.searchStatus === 1)
                        return option.eq_state === 'Waiting_for_referencing';
                    else if (this.searchStatus === 2)
                        return option.eq_state === 'Waiting_for_installation';
                    else if (this.searchStatus === 3)
                        return option.eq_state === 'In_use';
                    else if (this.searchStatus === 4)
                        return option.eq_state === 'Under_maintenance';
                    else if (this.searchStatus === 5)
                        return option.eq_state === 'On_hold';
                    else if (this.searchStatus === 6)
                        return option.eq_state === 'Under_repair';
                    else if (this.searchStatus === 7)
                        return option.eq_state === 'Broken';
                    else if (this.searchStatus === 8)
                        return option.eq_state === 'Downgraded';
                    else if (this.searchStatus === 9)
                        return option.eq_state === 'Reformed';
                    else if (this.searchStatus === 10)
                        return option.eq_state === 'Lost';
                });
            }
            if (this.searchSaveStatus !== -1) {
                res = res.filter(option => {
                    if (this.searchSaveStatus === 1)
                        return option.validated === 'drafted';
                    else if (this.searchSaveStatus === 2)
                        return option.validated === 'to_be_validated';
                    else if (this.searchSaveStatus === 3)
                        return option.validated === 'validated';
                });
            }
            if (this.searchSigned !== -1) {
                res = res.filter(option => {
                    if (this.searchSigned === 0)
                        return option.signed;
                    else if (this.searchSigned === 1)
                        return option.signed === false;
                });
            }
            return res;
        }
    },
}
</script>

<style scoped>
.page {
    display: block;
    margin: auto;
    page-break-inside: auto;
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
</style>

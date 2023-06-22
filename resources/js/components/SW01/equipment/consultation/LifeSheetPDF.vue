<!--
* Filename : LifeSheetPDF.vue
* Creation date : 6 Jul 2022
* Update date : 25 May 2023
* The document allows us to create the life sheet of an equipment and to export it in PDF.
-->

<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>'</p>
            <table style="border: solid 1px black; margin-left:50px; width: auto;">
                <tbody>
                <tr style="border: solid 1px black">
                    <td rowspan="3" style="border: solid 1px black">
                        <img alt="Alpha logo" class="logo" src="/images/logo.jpg">
                    </td>
                    <td colspan="2" style="border: solid 1px black">
                        <h2>
                            EQUIPMENT LIFE SHEET DESCRIPTIVE PART
                        </h2>
                    </td>
                    <td style="border: solid 1px black">
                        <h5>
                            Version :
                            {{ eq_idCard.eq_internalReference }}_LS-D_V{{
                                eq_idCard.eq_version[1]
                            }}.{{ eq_idCard.eq_version[0] }}
                        </h5>
                    </td>
                </tr>
                <tr style="border: solid 1px black">
                    <td style="border: solid 1px black">
                        <p>
                            Technical Review <b class="text-primary">{{ eq_idCard.eq_technicalVerifier_firstName }}
                            {{ eq_idCard.eq_technicalVerifier_lastName }} </b>
                        </p>
                    </td>
                    <td style="border: solid 1px black">
                        <p>
                            Quality Review <b class="text-primary">{{ eq_idCard.eq_qualityVerifier_firstName }}
                            {{ eq_idCard.eq_qualityVerifier_lastName }} </b>
                        </p>
                    </td>
                    <td style="border: solid 1px black">
                        <p>
                            Equipment unique ID : <b class="text-primary">{{ eq_idCard.eq_internalReference }}</b>
                        </p>
                    </td>
                </tr>
                <tr style="border: solid 1px black">
                    <td colspan="2" style="border: solid 1px black">
                        <p>
                            Date of signature :
                            <b v-if="eq_idCard.eq_signatureDate !== null" class="text-primary">
                                {{
                                    new Date(eq_idCard.eq_signatureDate.slice(0, 4), eq_idCard.eq_signatureDate.slice(5, 7), eq_idCard.eq_signatureDate.slice(8)).getDate()
                                }}
                                {{
                                    new Date(eq_idCard.eq_signatureDate.slice(0, 4), eq_idCard.eq_signatureDate.slice(5, 7) - 1, eq_idCard.eq_signatureDate.slice(8)).toDateString().slice(4, 7)
                                }}
                                {{
                                    new Date(eq_idCard.eq_signatureDate.slice(0, 4), eq_idCard.eq_signatureDate.slice(5, 7), eq_idCard.eq_signatureDate.slice(8)).getFullYear()
                                }}
                            </b>
                            <b v-else>
                                /
                            </b>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--            <div class="eq_top_infos">
                            <div class=" equipement_pdf_logo ">
                              <img src="/images/logo.jpg" alt="Alpha logo" class="logo" >
                            </div>

                            <div class="equipement_pdf_titre">
                                <h2 id="equipement_fiche_de_vie_titre">EQUIPMENT LIFE SHEET DESCRIPTIVE PART</h2>
                            </div>

                            <div class="equipement_pdf_index">
                                <h5>Version : {{eq_idCard.eq_internalReference}}_LS-D_V{{eq_idCard.eq_version[1]}}.{{eq_idCard.eq_version[0]}} </h5>
                            </div>
                            <div class="equipment_revued_by">
                                <p >Technical Review <b class="text-primary">{{ eq_idCard.eq_technicalVerifier_firstName}} {{eq_idCard.eq_technicalVerifier_lastName}} </b></p>
                            </div>

                            <div class="equipment_approuved_by">
                                <p>Quality Review <b class="text-primary">{{ eq_idCard.eq_qualityVerifier_firstName}} {{eq_idCard.eq_qualityVerifier_lastName}} </b></p>
                            </div>

                            <div class="eq_internalReference_pdf">
                                <p>Equipment unique ID :</p>
                                <h5 class="text-primary">{{eq_idCard.eq_internalReference}}</h5>
                            </div>
                        </div>-->

            <div class="eq_identification_infos_pdf">
                <div class="title_identification_pdf">
                    <p>IDENTIFICATION</p>
                </div>
                <div class="Ecme_assoc_pdf">
                    <b>
                        MME Linked
                    </b>
                    <div class="content_Ecme_assoc_pdf">
                        <b v-for="(mme,index) in eq_mme " :key="index" class="text-primary">
                            {{ mme.mme_internalReference }} : {{ mme.mme_name }} <br>

                        </b>

                    </div>
                </div>
                <div class="eq_serialNumber_pdf">
                    <p>Serial Number : <b class="text-primary">{{ eq_idCard.eq_serialNumber }}</b></p>
                </div>
                <div class="eq_externalReference_pdf">
                    <p>
                        External Reference : <b class="text-primary">{{ eq_idCard.eq_externalReference }}</b>
                    </p>
                </div>
                <div class="eq_constructor_pdf">
                    <p>Constructor: <b class="text-primary">{{ eq_idCard.eq_constructor }}</b></p>
                </div>
                <div class="eq_designation_type_pdf">
                    <p>
                        Designation and type : <b class="text-primary"> {{ eq_idCard.eq_name }} -
                        {{ eq_idCard.eq_type }} </b>
                    </p>
                    <p>

                    </p>
                </div>
            </div>


            <div class="eq_usage_infos_pdf">
                <div class="title_usage_pdf">
                    <p>USAGE</p>
                </div>
                <div v-if="eq_usg.length>0">
                    <div v-for="(usg,index) in eq_usg " :key="index" class="usg_type_and_precaution_pdf">
                        <div class="eq_usage_type_pdf">
                            <p>
                                Type of the operation realized by/with the equipment:
                            </p>
                            <br>
                            <b class="text-primary">
                                {{ usg.usg_type }}
                            </b>
                        </div>
                        <div class="eq_usage_precaution_pdf">
                            <b>
                                Associated Precaution:
                            </b>
                            <br>
                            <b class="text-primary">
                                {{ usg.usg_precaution }}
                            </b>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="usg_type_and_precaution_pdf">
                        <div class="eq_usage_type_pdf">
                            <p>
                                Type of the operation realized by/with the equipment :
                            </p>
                            <br>
                        </div>
                        <div class="eq_usage_precaution_pdf">
                            <p>
                                Special Precaution (if needed) :
                            </p>
                            <br>
                        </div>
                    </div>
                </div>
            </div>


            <div class="eq_file_infos_pdf">
                <div class="title_file_pdf">
                    <p>ASSOCIATED FILE(s)</p>
                </div>
                <div class="eq_file_assoc_pdf">
                    <p>
                        Name of the file : Location
                    </p>
                    <p v-for="(file,index) in eq_file " :key="index" class="text-primary">
                        {{ file.file_name }} : {{ file.file_location }}<br>

                    </p>
                </div>
            </div>

            <div class="eq_carac_infos_pdf">
                <div class="title_carac_pdf">
                    <p>CARACTERISTICS</p>
                </div>
                <div class="power_title_pdf">
                    <p class="caracteristic_name">Power supply :</p>
                    <div>
                        <div v-for="(power,index) in eq_powers " :key="index" class="eq_power_pdf">
                            <div v-if="power.powers.length>0" class="eq_power_type_pdf">
                                <p>{{ power.type }}</p>
                                <div v-for="(power_elemnt,index) in power.powers " :key="index"
                                     class="eq_power_content_pdf">
                                    <div class="eq_power_name_pdf">
                                        <b>Power name : <b class="text-primary">{{ power_elemnt.pow_name }}</b></b>
                                    </div>
                                    <div class="eq_power_consumption_pdf">
                                        <b>Power consumption</b>
                                        <b class="text-primary">{{ power_elemnt.pow_consumptionValue }}
                                            {{ power_elemnt.pow_consumptionUnit }} </b>
                                    </div>
                                    <div class="eq_power_value_pdf">
                                        <b>Power value</b>
                                        <b class="text-primary">{{ power_elemnt.pow_value }}
                                            {{ power_elemnt.pow_unit }} </b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dimension_title_pdf">
                    <p class=caracteristic_name> Dimension(s) :</p>
                    <div>
                        <div v-for="(dimension,index) in eq_dimensions " :key="index" class="eq_dimension_pdf">
                            <div v-if="dimension.dimensions.length>0" class="eq_dimension_type_pdf">
                                <p>{{ dimension.type }}</p>
                                <div v-for="(dim_elemnt,index) in dimension.dimensions "
                                     :key="index" class="eq_dimension_content_pdf">
                                    <div class="eq_dimension_name_value_pdf">
                                        <p class="text-primary">{{ dim_elemnt.dim_name }} {{ dim_elemnt.dim_value }}
                                            {{ dim_elemnt.dim_unit }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="eq_mass_set_mobility_pdf">
                    <div class="eq_mass_pdf">
                        <p>Masse : <b class="text-primary">{{ eq_idCard.eq_mass }} {{ eq_idCard.eq_massUnit }} </b></p>
                    </div>
                    <div class="eq_mobility_pdf">
                        <div v-if="eq_idCard.eq_mobility==true">
                            <p>Mobil? : <b class="text-primary">Yes</b></p>
                        </div>
                        <div v-else-if="eq_idCard.eq_mobility==false">
                            <p>Mobil? : <b class="text-primary">No</b></p>
                        </div>
                    </div>
                    <div class="eq_set_pdf">
                        <p>Set : <b class="text-primary">{{ eq_idCard.eq_set }}</b></p>
                    </div>
                </div>

                <div class="eq_remark_pdf">
                    <p>
                        Remarks
                    </p>
                    <p class="text-primary">
                        {{ eq_idCard.eq_remarks }}
                    </p>
                </div>
            </div>

            <div class="eq_specProc_infos_pdf">
                <div class="title_spProc_pdf">
                    SPECIAL PROCESS?
                </div>
                <div v-for="(spProc,index) in eq_spProc " :key="index" class="eq_specProc_pdf">
                    <div v-if="spProc.spProc_exist==true">
                        <p class="text-primary">
                            Yes <br>
                            {{ spProc.spProc_name }}<br>
                            {{ spProc.spProc_remarksOrPrecaution }}


                        </p>
                    </div>
                    <div v-if="spProc.spProc_exist==false">
                        <p class="text-primary">
                            No<br>
                            {{ spProc.spProc_remarksOrPrecaution }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="eq_risk_infos_pdf">
                <div class="title_risk_pdf">
                    <p>RISK RELATED TO THE EQUIPMENT</p>
                </div>
                <div v-for="(risk,index) in eq_risk " :key="index" class="eq_risk_pdf">

                    <div class="eq_risk_for_pdf">
                        <p> Risk for <b class="text-primary"> {{ risk.risk_for }}</b></p>
                    </div>
                    <div class="eq_risk_wayOfControl_pdf">
                        <p>
                            Way of control
                        </p>
                        <p class="text-primary">
                            {{ risk.risk_wayOfControl }}
                        </p>
                    </div>
                    <div class="eq_risk_remarks_pdf">
                        <p>
                            Remarks
                        </p>
                        <p class="text-primary">
                            {{ risk.risk_remarks }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="eq_prvMtnOp_infos_pdf">
                <div class="title_prvMtnOp_pdf">
                    PREVENTIVE MAINTENANCE OPERATION
                </div>
                <div class="prvMtnop_table">
                    <b-row>
                        <b-col class="prvMtnOp_table_number" cols="1">
                            N°
                        </b-col>
                        <b-col class="prvMtnOp_table_puttingIntoService" cols="1">
                            PIS?
                        </b-col>
                        <b-col class="prvMtnOp_table_description" cols="4">
                            Type of operations to be carried out
                        </b-col>
                        <b-col class="prvMtnOp_table_protocol" cols="4">
                            Protocol
                        </b-col>
                        <b-col class="prvMtnOp_table_periodicity" cols="1">
                            Frequency of intervention
                        </b-col>
                        <b-col class="prvMtnOp_table_risk" cols="1">
                            Assoc Risk identified?
                        </b-col>
                        <b-col class="prvMtnOp_table_reformed" cols="1">
                            Reformed?
                        </b-col>
                    </b-row>
                    <div v-for="(prvMtnOp,index) in eq_prvMtnOp " :key="index">
                        <b-row>
                            <b-col class="prvMtnOp_table_number" cols="1">
                                <p class="text-primary"> {{ prvMtnOp.Number }} </p>
                            </b-col>
                            <b-col class="prvMtnOp_table_puttingIntoService" cols="1">
                                <p class="text-primary">{{ prvMtnOp.PuttingIntoService }}</p>
                            </b-col>
                            <b-col class="prvMtnOp_table_description" cols="4">
                                <p class="text-primary">{{ prvMtnOp.Description }}</p>
                            </b-col>
                            <b-col class="prvMtnOp_table_protocol" cols="4">
                                <p class="text-primary"> {{ prvMtnOp.Protocol }}</p>
                            </b-col>
                            <b-col class="prvMtnOp_table_periodicity" cols="1">
                                <p class="text-primary">{{ prvMtnOp.Periodicity }} {{ prvMtnOp.Symbol }} </p>

                            </b-col>
                            <b-col class="prvMtnOp_table_risk" cols="1">
                                <p class="text-primary">{{ prvMtnOp.Risk }}</p>
                            </b-col>
                            <b-col class="prvMtnOp_table_reformed" cols="1">
                                <p class="text-primary">{{ prvMtnOp.Reformed }}</p>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>


            <div class="eq_risk_prvMtnOp_infos_pdf">
                <div class="title_risk_prvMtnOp_pdf">
                    Risk related to the preventive maintenance operation
                </div>
                <div class="risk_prvMtnOp_table">
                    <b-row>
                        <b-col class="risk_prvMtnOp_table_number" cols="1">
                            related mtn op N°
                        </b-col>
                        <b-col class="risk_prvMtnOp_table_description" cols="6">
                            Inventory of possible effects on product, manufacturing environment or safety following
                            periodic OP maintenance
                        </b-col>
                        <b-col class="risk_prvMtnOp_table_wayOfControl" cols="4">
                            Way of control
                        </b-col>
                    </b-row>
                    <div v-for="(prvMtnOp_risk,index) in eq_prvMtnOp_risk " :key="index">
                        <b-row>
                            <b-col class="risk_prvMtnOp_table_number" cols="1">
                                <p class="text-primary"> {{ prvMtnOp_risk.prvMtnOp_number }} </p>
                            </b-col>
                            <b-col class="risk_prvMtnOp_table_description" cols="6">
                                <p class="text-primary">{{ prvMtnOp_risk.risk_remarks }}</p>
                            </b-col>
                            <b-col class="risk_prvMtnOp_table_wayOfControl" cols="4">
                                <p class="text-primary">{{ prvMtnOp_risk.risk_wayOfControl }}</p>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>

            <div class="eq_history_pdf">
                <div class="title_history_pdf">
                    History Version
                </div>
                <div class="history_table">
                    <b-row>
                        <b-col class="history_table_versionFrom" cols="1">
                            Version before update
                        </b-col>
                        <b-col class="history_table_reason" cols="1">
                            Reason for the update of version
                        </b-col>
                        <b-col class="history_table_versionTo" cols="1">
                            Version after update
                        </b-col>
                        <b-col class="history_table_Date" cols="4">
                            Date of the update
                        </b-col>
                    </b-row>
                    <div v-for="(history,index) in eq_history " :key="index">
                        <b-row>
                            <b-col class="history_table_versionFrom" cols="1">
                                <p class="text-primary"> {{ history.history_numVersion }} </p>
                            </b-col>
                            <b-col class="history_table_reason" cols="1">
                                <p class="text-primary">{{ history.history_reasonUpdate }}</p>
                            </b-col>
                            <b-col class="history_table_versionTo" cols="4">
                                <p class="text-primary">{{ history.history_numVersion + 1 }}</p>
                            </b-col>
                            <b-col class="history_table_Date" cols="4">
                                <p class="text-primary"> {{ history.history_date }}</p>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>


            <div class="eq_historyRecordTemplateRefPdf">
                <div class="eqHistory_table_recordTemplateRefPdf">
                    <div class="eqHistory_index_recordTemplateRefPdf">
                        Record Template Ref : REC-IWE12_V1.0
                    </div>
                    <div class="eqHistory_confidential_recordTemplateRefPdf">
                        This document contains CONFIDENTIAL information
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" @click="generateReport()">Generate PDF</button>
    </div>

</template>

<script>
import html2PDF from 'jspdf-html2canvas';

export default {
    data() {
        return {
            eq_id: this.$route.params.id,
            eq_idCard: null,
            eq_dimensions: null,
            eq_powers: null,
            eq_spProc: null,
            eq_usg: [],
            eq_file: null,
            eq_prvMtnOp: null,
            eq_risk: null,
            eq_prvMtnOp_risk: null,
            eq_mme: [],
            loaded: false,
            eq_history: null,
        }
    },

    components: {},
    methods: {

        generateReport() {
            let page = document.getElementById('page');
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
                output: this.eq_idCard.eq_internalReference + '_LS-D' + '_V' + this.eq_idCard.eq_version + '.pdf',
            });
        },

        addMargin() {

            const tab_className = [7];
            tab_className[0] = "eq_top_infos";
            tab_className[1] = "eq_identification_infos_pdf";
            tab_className[2] = "eq_usage_infos_pdf";
            tab_className[3] = "eq_file_infos_pdf";
            tab_className[4] = "eq_carac_infos_pdf";
            tab_className[5] = "eq_specProc_infos_pdf";
            tab_className[6] = "eq_risk_infos_pdf";
            tab_className[7] = "eq_prvMtnOp_infos_pdf";
            tab_className[8] = "eq_risk_prvMtnOp_infos_pdf";
            tab_className[9] = "eq_recordTemplateRefPdf";

            tab_className.forEach(function (element) {
                console.log(element);
                let elem = document.getElementsByClassName(element)[0];
                const rect = elem.getBoundingClientRect();
                let top = rect.top + window.scrollY;
                let bottom = rect.bottom + window.scrollY;


                //case of an element that is between the first page and the second page
                if (top > 0 && top < 1810 && bottom > 1780 && bottom < 3300) {
                    let marginTop = 1810 - top + 30;
                    elem.style.marginTop = marginTop + "px";
                }
                if (top > 2000 && top < 3520 && bottom > 3550 && bottom < 4800) {
                    let marginTop = 3550 - top;
                    elem.style.marginTop = marginTop + "px";
                }
            });
            this.generateReport();
        },

    },
    created() {
        const consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then(response => {
                this.eq_idCard = response.data;
                const consultUrlDim = (id) => `/dimension/send/ByType/${id}`;
                axios.get(consultUrlDim(this.eq_id))
                    .then(response => {
                        this.eq_dimensions = response.data;
                        const consultUrlPow = (id) => `/power/send/ByType/${id}`;
                        axios.get(consultUrlPow(this.eq_id))
                            .then(response => {
                                this.eq_powers = response.data;
                                const consultUrlSpProc = (id) => `/spProc/send/${id}`;
                                axios.get(consultUrlSpProc(this.eq_id))
                                    .then(response => {
                                        if (response.data == "") {
                                            this.eq_spProc = [];
                                        } else {
                                            this.eq_spProc = response.data;
                                        }
                                        const consultUrlUsg = (id) => `/usage/send/${id}`;
                                        axios.get(consultUrlUsg(this.eq_id))
                                            .then(response => {
                                                this.eq_usg = response.data;
                                                const consultUrlFile = (id) => `/file/send/${id}`;
                                                axios.get(consultUrlFile(this.eq_id))
                                                    .then(response => {
                                                        this.eq_file = response.data;
                                                        const consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
                                                        axios.get(consultUrlRisk(this.eq_id))
                                                            .then(response => {
                                                                this.eq_risk = response.data;
                                                                const consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/lifesheet/${id}`;
                                                                axios.get(consultUrlPrvMtnOp(this.eq_id))
                                                                    .then(response => {
                                                                        this.eq_prvMtnOp = response.data;
                                                                        const consultUrlRiskPDF = (id) => `/prvMtnOp/risk/send/pdf/${id}`;
                                                                        axios.get(consultUrlRiskPDF(this.eq_id))
                                                                            .then(response => {
                                                                                this.eq_prvMtnOp_risk = response.data;
                                                                                const consultUrlMme = (id) => `/mme/send/${id}`;
                                                                                axios.get(consultUrlMme(this.eq_id))
                                                                                    .then(response => {
                                                                                        this.eq_mme = response.data;
                                                                                        const consultUrlHist = (id) => `/history/send/equipment/${id}`;
                                                                                        axios.get(consultUrlHist(this.eq_id))
                                                                                            .then(response => {
                                                                                                this.eq_history = response.data;
                                                                                                this.loaded = true;
                                                                                            }).catch(error => {
                                                                                        });
                                                                                    }).catch(error => {
                                                                                });
                                                                            }).catch(error => {
                                                                        });
                                                                    }).catch(error => {
                                                                });
                                                            }).catch(error => {
                                                        });
                                                    }).catch(error => {
                                                });
                                            }).catch(error => {
                                        });
                                    }).catch(error => {
                                });
                            }).catch(error => {
                        });
                    }).catch(error => {
                });
            }).catch(error => {
        });
    }
}
</script>

<style lang="scss">
#page {
    width: 1329px;
    font-size: 10px;

    .text-primary {
        font-size: 20px;
    }

    .eq_top_infos {
        position: absolute;
        margin-top: 0px;
        margin-left: 50px;

        h5 {
            margin-top: auto;
            width: auto;
            font-size: 25px;
            text-align: center;
            font-weight: bold;
        }

        .equipement_pdf_logo {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 200px;
            height: 170px;
            margin-left: 100px;
            margin-top: 00px;

            .logo {
                margin-top: 30px;
            }

        }

        .equipement_pdf_titre {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            width: 642px;
            top: 00px;
            left: 300px;
            height: 87px;
            text-align: center;


        }

        .equipement_fiche_de_vie_titre {
            text-align: center;
            position: relative;
        }

        .equipement_pdf_index {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left: 942px;
            top: 0px;
            height: 86px;
            width: 200px;

        }

        .equipment_revued_by {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;

            left: 300px;
            top: 86px;
            height: 84px;
            width: 321px;

        }

        .equipment_approuved_by {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;


            left: 621px;
            top: 86px;
            height: 84px;
            width: 321px;
        }

        .eq_internalReference_pdf {
            border: solid 0.5px black;
            margin: auto;
            position: absolute;
            left: 942px;
            top: 86px;
            width: 200px;
            height: 84px;
        }

    }

    .eq_identification_infos_pdf {
        position: relative;
        margin-top: 240px;
        margin-bottom: 60px;
        margin-left: 150px;

        .title_identification_pdf {
            width: 200px;
            font-size: 20px;
            font-weight: bold;

            p {
                margin-top: 220px;
                margin-bottom: 0px;
            }
        }

        .Ecme_assoc_pdf {
            border: solid 0.5px black;
            position: relative;
            margin-bottom: 20px;
            height: auto;
            width: 1042px;
        }

        .eq_designation_type_pdf {
            border: solid 1px black;
            width: 500px;
            height: auto;
            margin-bottom: 20px;
            float: left;
            margin-left: 42px;

        }

        .eq_externalReference_pdf {
            border: solid 1px black;
            margin-left: 42px;
            width: 500px;
            height: 60px;
            margin-bottom: 50px;
            float: left;
        }

        .eq_constructor_pdf {
            border: solid 1px black;
            width: 500px;
            height: 60px;
            float: left;
        }

        .eq_serialNumber_pdf {
            border: solid 1px black;
            width: 500px;
            height: 60px;
            float: left;
        }

    }

    .eq_usage_infos_pdf {
        position: relative;
        margin-left: 150px;

        .title_usage_pdf {
            width: 200px;
            font-size: 20px;
            font-weight: bold;

            p {
                margin-top: 220px;
                margin-bottom: 0px;
            }
        }

        .usg_type_and_precaution_pdf {
            position: relative;

            .eq_usage_type_pdf {
                border: solid 1px black;
                position: relative;
                height: 170px;
                width: 1042px;
            }

            .eq_usage_precaution_pdf {
                border: solid 1px black;
                margin-bottom: 20px;
                height: auto;
                width: 1042px;
                position: relative;
            }
        }
    }

    .eq_file_infos_pdf {
        position: relative;
        margin-left: 150px;

        .title_file_pdf {
            width: 200px;
            font-size: 20px;
            font-weight: bold;

            p {
                margin-top: 30px;
                margin-bottom: 0px;
            }
        }

        .eq_file_assoc_pdf {
            border: solid 1px black;
            position: relative;
            margin-bottom: 20px;
            height: auto;
            width: 1042px;
        }

    }

    .eq_carac_infos_pdf {
        position: relative;
        margin-left: 150px;

        .title_carac_pdf {
            width: 200px;
            font-size: 20px;
            font-weight: bold;
        }

        .power_title_pdf {
            position: relative;
            margin-top: 0px;


            .eq_power_pdf {
                position: relative;
                height: auto;
                width: 1042px;
                margin-bottom: 30px;

                .eq_power_type_pdf {

                    position: relative;
                    height: auto;
                    margin-bottom: 0px;
                    font-size: 15px;
                    font-weight: bold;

                    .eq_power_content_pdf {
                        position: relative;
                        height: auto;
                        margin-bottom: 0px;

                        .eq_power_name_pdf {
                            border: solid 1px black;
                            position: relative;
                            height: auto;
                            width: 1042px;

                        }

                        .eq_power_value_pdf {
                            border: solid 1px black;
                            position: relative;
                            height: auto;
                            width: 521px;
                        }

                        .eq_power_consumption_pdf {
                            border: solid 1px black;
                            position: relative;
                            height: auto;
                            width: 521px;
                            float: right;
                        }
                    }
                }
            }
        }

        .caracteristic_name {
            text-decoration: underline;
            font-size: 18px;
            font-weight: bold;
        }

        .dimension_title_pdf {
            position: relative;
            margin-top: -10px;

            p {
                font-size: 18px;
                font-weight: bold;
            }

        }

        .eq_dimension_pdf {
            position: relative;
            height: auto;
            width: 1060px;
            margin-bottom: 30px;

            .eq_dimension_type_pdf {
                p {
                    font-size: 15px;
                    font-weight: bold;
                }

                position: relative;
                height: auto;

                .eq_dimension_content_pdf {
                    position: relative;
                    height: auto;
                    display: inline-block;
                    margin-bottom: 10px;

                    p {
                        font-size: 20px;
                        font-weight: normal;
                        margin-left: 100px;
                        margin-top: 5px;
                    }

                    .eq_dimension_name_value_pdf {
                        border: solid 1px black;
                        position: relative;
                        height: auto;
                        width: 340px;
                        margin-right: 10px;

                    }

                }
            }
        }

        .eq_mass_set_mobility_pdf {
            position: relative;

            p {
                font-size: 20px;
                margin-top: 3px;
                margin-left: 10px;

            }

            .eq_mass_pdf {
                border: solid 1px black;
                display: inline-block;
                margin-bottom: 20px;
                height: 40px;
                width: 340px;
            }

            .eq_mobility_pdf {
                border: solid 1px black;
                display: inline-block;
                margin-bottom: 20px;
                height: 40px;
                margin-left: 8.5px;
                width: 340px;
            }

            .eq_set_pdf {
                display: inline-block;
                border: solid 1px black;
                margin-bottom: 20px;
                height: 40px;
                margin-left: 8.5px;
                width: 340px;
            }
        }

        .eq_remark_pdf {
            border: solid 1px black;
            position: relative;
            margin-bottom: 20px;
            height: auto;
            width: 1042px;
        }
    }

    .eq_risk_infos_pdf {
        position: relative;
        margin-left: 150px;

        .title_risk_pdf {
            margin-bottom: 0px;
            margin-top: 30px;
            width: 400px;
            font-size: 20px;
            font-weight: bold;
        }

        .eq_risk_pdf {
            position: relative;
            height: auto;
            width: 1042px;
        }

        .eq_risk_for_pdf {
            border: solid 1px black;
            position: relative;
            height: auto;
            width: 1042px;
        }

        .eq_risk_remarks_pdf {
            border: solid 1px black;
            position: relative;
            height: auto;
            width: 521px;

        }

        .eq_risk_wayOfControl_pdf {
            border: solid 1px black;
            position: relative;
            height: auto;
            width: 521px;
            float: right;
        }

    }

    .eq_specProc_infos_pdf {
        position: relative;
        margin-left: 150px;

        .title_spProc_pdf {
            width: 400px;
            font-size: 20px;
            font-weight: bold;
        }

        .eq_specProc_pdf {
            border: solid 1px black;
            position: relative;
            height: auto;
            width: 1042px;
        }
    }

    .eq_prvMtnOp_infos_pdf {
        position: relative;
        margin-top: 10px;
        width: 1112px;

        .title_prvMtnOp_pdf {
            width: 400px;
            font-size: 20px;
            font-weight: bold;
            margin-left: 150px;
        }

        .prvMtnop_table {
            margin-left: 163px;
            width: 1042px;

            .prvMtnOp_table_number {
                border: solid 1px black;
                text-align: center;
                width: 30px;
            }

            .prvMtnOp_table_puttingIntoService {
                border: solid 1px black;
                text-align: center;
                width: 45px;
            }

            .prvMtnOp_table_description {
                border: solid 1px black;
                text-align: center;
                width: 380px;
            }

            .prvMtnOp_table_protocol {
                border: solid 1px black;
                text-align: center;
                width: 380px;
            }

            .prvMtnOp_table_periodicity {
                border: solid 1px black;
                text-align: center;
                width: 70px;
            }

            .prvMtnOp_table_risk {
                border: solid 1px black;
                text-align: center;
                width: 68px;
            }

            .prvMtnOp_table_reformed {
                border: solid 1px black;
                text-align: center;
                width: 70px;
            }
        }
    }

    .eq_risk_prvMtnOp_infos_pdf {
        position: relative;
        margin-top: 10px;
        width: 1112px;

        .title_risk_prvMtnOp_pdf {
            width: 500px;
            font-size: 20px;
            font-weight: bold;
            margin-left: 150px;
        }

        .risk_prvMtnOp_table {
            margin-left: 163px;
            width: 1112px;

            .risk_prvMtnOp_table_number {
                border: solid 1px black;
                text-align: center;
            }

            .risk_prvMtnOp_table_description {
                border: solid 1px black;
                text-align: center;
            }

            .risk_prvMtnOp_table_wayOfControl {
                border: solid 1px black;
                text-align: center;
            }
        }
    }

    .eq_recordTemplateRefPdf {
        position: relative;
        left: 150px;
        margin-top: 10px;
        width: 1042px;


        .eq_table_recordTemplateRefPdf {
            .eq_confidential_recordTemplateRefPdf {
                border: solid 1px black;
                background-color: lightgrey;
                text-align: center;
                height: auto;
                font-size: 20px;
                font-style: italic;
            }

            .eq_index_recordTemplateRefPdf {
                background-color: lightgrey;
                border: solid 1px black;
                text-align: center;
                height: auto;
                font-size: 20px;
            }
        }
    }

    .eq_history_pdf {
        position: relative;
        margin-top: 10px;
        width: 1112px;
        margin-top: 50px;

        .title_history_pdf {
            width: 400px;
            font-size: 20px;
            font-weight: bold;
            margin-left: 150px;
        }

        .history_table {
            margin-left: 163px;
            width: 1042px;

            .history_table_versionFrom {
                border: solid 1px black;
                text-align: center;
                width: 150px;
            }

            .history_table_reason {
                border: solid 1px black;
                text-align: center;
                width: 540px;
                text-wrap: normal;
            }

            .history_table_versionTo {
                border: solid 1px black;
                text-align: center;
                width: 150px;
            }

            .history_table_Date {
                border: solid 1px black;
                text-align: center;
                width: 200px;
            }
        }
    }

    .eq_historyRecordTemplateRefPdf {
        position: relative;
        left: 150px;
        margin-top: 10px;
        width: 1042px;


        .eqHistory_table_recordTemplateRefPdf {
            .eqHistory_confidential_recordTemplateRefPdf {
                border: solid 1px black;
                background-color: lightgrey;
                text-align: center;
                height: auto;
                font-size: 20px;
                font-style: italic;
            }

            .eqHistory_index_recordTemplateRefPdf {
                background-color: lightgrey;
                border: solid 1px black;
                text-align: center;
                height: auto;
                font-size: 20px;
            }
        }
    }
}


</style>

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="contentArticle">
            <div id="page">
                <!-- Header -->
                <table class="header">
                    <tbody>
                    <tr rowspan="3" class="ignored">
                        <td rowspan="3" style="text-align: center; vertical-align: middle" class="ignored">
                            <img alt="logo Alpha" src="/images/logo.png" style="width: max-content; height: max-content">
                        </td>
                        <td class="lightGray">
                            <p>
                                Type of documentation :
                            </p>
                            <h2>
                                SPECIFICATIONS
                            </h2>
                        </td>
                        <td class="lightGray">
                            <p>
                                Language :
                            </p>
                            <h2>
                                EN
                            </h2>
                        </td>
                        <td class="lightGray">
                            <p>
                                Reference & Version :
                            </p>
                            <h2>
                                {{this.articleType.toUpperCase()}}-ART_v{{this.articleData.nbrVersion}}.0
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h2>
                                ARTICLE SHEET
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; vertical-align: text-top">
                            <p class="ignored">
                                Verified by (Name and Date) :
                            </p>
                            <p id="verifier">
                            </p>
                        </td>
                        <td colspan="2" style="text-align: left; vertical-align: top">
                            <p class="ignored">
                                Approved by (Name and Date) :
                            </p>
                            <p id="approver">
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Article ID card -->
                <p></p>
                <table>
                    <tbody>
                    <tr>
                        <td class="tableName" rowspan="8">
                            <h2>
                                Article Identification
                            </h2>
                        </td>
                        <td class="tableDesc">
                            <p>
                                Article or Family references
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="references">
                                {{ articleData.ref === null ? "/" : articleData.ref }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Designation
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="designation">
                                {{ articleData.design === null ? "/" : articleData.design }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Version
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="version">
                                {{ articleData.version === null ? "/" : articleData.version }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Severity of the article
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="severity">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Type of article
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="type">
                                {{ articleType.toUpperCase() }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Name of supplier
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="supplierName">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Supplier article reference
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="supplierRef">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableDesc">
                            <p>
                                Purchased By
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="purchased">
                                {{ articleData.purchasedBy === null ? "/" : articleData.purchasedBy }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Storage Condition and Purchasing Specification -->
                <p></p>
                <table class="header">
                    <tbody>
                    <tr>
                        <td style="width: 40%; text-align: center" class="lightGray">
                            <p>
                                Storage Conditions
                            </p>
                        </td>
                        <td>
                            <ul>
                                <li v-for="storage in stoConds" :key="storage.id">
                                    {{ storage.value }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; text-align: center" class="lightGray">
                            <p>
                                Purchasing specifications
                            </p>
                        </td>
                        <td>
                            <ul>
                                <li v-for="spec in purSpes" :key="spec.id">
                                    {{ spec.purSpe_requiredDoc }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Family Members -->
                <p></p>
                <table class="header">
                    <thead>
                    <tr>
                        <td colspan="4" style="text-align: center" class="gray">
                            <h2>
                                Family Identification
                            </h2>
                        </td>
                    </tr>
                    </thead>

                    <tbody>
                    <tr class="lightGray">
                        <td :rowspan="articleData.familyMembers.length + 1" style="text-align: center">
                            <p>
                                Family Member
                            </p>
                        </td>
                        <td>
                            <p>
                                Reference
                            </p>
                        </td>
                        <td>
                            <p>
                                Designation
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ articleData.variablesCharac === null ? "/" : articleData.variablesCharac }}
                            </p>
                        </td>
                    </tr>
                    <tr v-for="member in articleData.familyMembers">
                        <td>
                            <p>
                                {{ member.ref === null ? "/" : member.ref }}
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ member.design === null ? "/" : member.design }}
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ member.variablesCharac === null ? "/" : member.variablesCharac }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p></p>
                <h2 style="text-align: center">
                    INCOMING INSPECTION SPECIFICATIONS
                </h2>
                <!-- Documentary Control -->
                <table>
                    <tbody>
                    <tr>
                        <td class="tableName">
                            <h2>
                                Documentary Control
                            </h2>
                        </td>
                        <td class="tableDesc">
                            <p>
                                Method
                            </p>
                        </td>
                        <td class="tableValue">
                            <p>
                                {{ articleData.docControlMethod === null ? "/" : articleData.docControlMethod }}
                                <!-- TODO: mettre un v-for -->
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p></p>
                <!-- Aspect Test -->
                <div v-for="test in aspTests">
                    <table >
                        <tbody>
                        <tr>
                            <td class="tableName" rowspan="7">
                                <h2>
                                    Aspect Test
                                </h2>
                            </td>
                            <td class="tableDesc">
                                <p>
                                    Name
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_name === null ? "/" : test.aspTest_name }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Aspect
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_expectedAspect === null ? "/" : test.aspTest_expectedAspect }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Document specifications
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{ test.aspTest_docSpec === null ? "/" : test.aspTest_docSpec }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Sampling use
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_sampling === null ? "/" : test.aspTest_sampling }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.aspTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of severity
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_severityLevel === null ? "/" : test.aspTest_severityLevel }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.aspTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of control
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_levelOfControl === null ? "/" : test.aspTest_levelOfControl }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.aspTest_sampling === 'Other'">
                            <td class="tableDesc">
                                <p>
                                    Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.aspTest_desc === null ? "/" : test.aspTest_desc }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p></p>
                </div>
                <!-- Functional Test -->
                <div v-for="test in funcTests">
                    <table>
                        <tbody>
                        <tr>
                            <td class="tableName" rowspan="7">
                                <h2>
                                    Functional Test
                                </h2>
                            </td>
                            <td class="tableDesc">
                                <p>
                                    Name
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_name === null ? "/" : test.funcTest_name }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_expectedMethod === null ? "/" : test.funcTest_expectedMethod }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Acceptance Criteria
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_expectedValue === null ? "/" : test.funcTest_expectedValue }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Document specifications
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{ test.funcTest_docSpec === null ? "/" : test.funcTest_docSpec }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Sampling use
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_sampling === null ? "/" : test.funcTest_sampling }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.funcTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of severity
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_severityLevel === null ? "/" : test.funcTest_severityLevel }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.funcTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of control
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_levelOfControl === null ? "/" : test.funcTest_levelOfControl }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.funcTest_sampling === 'Other'">
                            <td class="tableDesc">
                                <p>
                                    Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.funcTest_desc === null ? "/" : test.funcTest_desc }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p></p>
                </div>
                <!-- Dimensional Test -->
                <div v-for="test in dimTests">
                    <table >
                        <tbody>
                        <tr>
                            <td class="tableName" rowspan="7">
                                <h2>
                                    Dimensional Test
                                </h2>
                            </td>
                            <td class="tableDesc">
                                <p>
                                    Name
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_name === null ? "/" : test.dimTest_name }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_expectedMethod === null ? "/" : test.dimTest_expectedMethod }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Value
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_expectedValue === null ? "/" : test.dimTest_expectedValue }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Document specifications
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{ test.dimTest_docSpec === null ? "/" : test.dimTest_docSpec }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Sampling use
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_sampling === null ? "/" : test.dimTest_sampling }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.dimTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of severity
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_severityLevel === null ? "/" : test.dimTest_severityLevel }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.dimTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of control
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_levelOfControl === null ? "/" : test.dimTest_levelOfControl }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.dimTest_sampling === 'Other'">
                            <td class="tableDesc">
                                <p>
                                    Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.dimTest_desc === null ? "/" : test.dimTest_desc }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p></p>
                </div>
                <!-- Complementary Test -->
                <div v-for="test in compTests">
                    <table>
                        <tbody>
                        <tr>
                            <td class="tableName" rowspan="7">
                                <h2>
                                    Complementary Test
                                </h2>
                                <h2>
                                    (If applicable)
                                </h2>
                            </td>
                            <td class="tableDesc">
                                <p>
                                    Name
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_name === null ? "/" : test.compTest_name }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_expectedMethod === null ? "/" : test.compTest_expectedMethod }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Expected Value
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_expectedValue === null ? "/" : test.compTest_expectedValue }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Document specifications
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{ test.compTest_docSpec === null ? "/" : test.compTest_docSpec }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Sampling use
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_sampling === null ? "/" : test.compTest_sampling }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.compTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of severity
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_severityLevel === null ? "/" : test.compTest_severityLevel }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.compTest_sampling === 'Statistics'">
                            <td class="tableDesc">
                                <p>
                                    Level of control
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_levelOfControl === null ? "/" : test.compTest_levelOfControl }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="test.compTest_sampling === 'Other'">
                            <td class="tableDesc">
                                <p>
                                    Method
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.compTest_desc === null ? "/" : test.compTest_desc }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p></p>
                </div>
                <!-- History -->
                <table style="text-align: center; vertical-align: middle" class="header">
                    <thead class="lightGray">
                    <th>
                        <h2>
                            Version
                        </h2>
                    </th>
                    <th>
                        <h2>
                            Implementation Date
                        </h2>
                    </th>
                    <th>
                        <h2>
                            Change Identification
                        </h2>
                    </th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <p>
                                1.0
                            </p>
                        </td>
                        <td>
                            <p>
                                {{new Date(articleData.created_at).getDate()}} {{new Date(articleData.created_at).toDateString().slice(4, 7)}} {{new Date(articleData.created_at).getFullYear()}}
                            </p>
                        </td>
                        <td>
                            <p>
                                1st version
                            </p>
                        </td>
                    </tr>
                    <tr v-for="hist in histories">
                        <td>
                            <p>
                                {{ hist.history_numVersion }}
                            </p>
                        </td>
                        <td>
                            {{new Date(hist.created_at).getDate()}} {{new Date(hist.created_at).toDateString().slice(4, 7)}} {{new Date(hist.created_at).getFullYear()}}
                        </td>
                        <td>
                            <p>
                                {{ hist.history_reasonUpdate }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary" @click="generateReport">Generate Report</button>
        </div>
    </div>
</template>

<!--v-if="articleType === 'comp' || articleType === 'raw'"-->
<script>
import html2PDF from "jspdf-html2canvas";

export default {
    name: "ArticleConsult",
    data() {
        return {
            loaded: false,
            articleType: this.$route.params.type.toLowerCase(),
            articleId: this.$route.params.id,
            articleData: null,
            incmgInsps: [],
            docControls: [],
            aspTests: [],
            funcTests: [],
            dimTests: [],
            compTests: [],
            purSpes: [],
            stoConds: [],
            histories: [],
            supplier: null
        }
    },
    methods: {
        generateReport () {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width: 300
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                },
                pagebreak: {
                    avoid: ['tr', 'td', 'table'],
                    mode: ['avoid-all', 'css', 'legacy']
                },
                imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 40,
                    bottom: 40,
                    left: 10,
                    right: 10,
                },
                output: this.articleType + '_' + this.articleId + '_export.pdf',
            });
        }
    },
    mounted() {
    },
    created() {
        if (this.articleType === "raw") {
            axios.get('/raw/family/send/' + this.articleId)
                .then(response => {
                    this.articleData = {
                        active: response.data.rawFam_active,
                        design: response.data.rawFam_design,
                        drawingPath: response.data.rawFam_drawingPath,
                        nbrVersion: response.data.rawFam_nbrVersion,
                        purchasedBy: response.data.rawFam_purchasedBy,
                        qualityApproverID: response.data.rawFam_qualityApproverId,
                        ref: response.data.rawFam_ref,
                        signatureDate: response.data.rawFam_signatureDate,
                        technicalReviewerID: response.data.rawFam_technicalReviewerId,
                        validate: response.data.rawFam_validate,
                        variablesCharac: response.data.rawFam_variablesCharac,
                        version: response.data.rawFam_version,
                        created_at: response.data.rawFam_created_at,
                        familyMembers: []
                    };
                })
                .catch(error => {
                    console.log(error);
                });
        } else if (this.articleType === "comp") {
            axios.get('/comp/family/send/' + this.articleId)
                .then(response => {
                    this.articleData = {
                        active: response.data.compFam_active,
                        design: response.data.compFam_design,
                        drawingPath: response.data.compFam_drawingPath,
                        nbrVersion: response.data.compFam_nbrVersion,
                        purchasedBy: response.data.compFam_purchasedBy,
                        qualityApproverID: response.data.compFam_qualityApproverId,
                        ref: response.data.compFam_ref,
                        signatureDate: response.data.compFam_signatureDate,
                        technicalReviewerID: response.data.compFam_technicalReviewerId,
                        validate: response.data.compFam_validate,
                        variablesCharac: response.data.compFam_variablesCharac,
                        version: response.data.compFam_version,
                        created_at: response.data.compFam_created_at,
                        familyMembers: []
                    };
                })
                .catch(error => {
                    console.log(error);
                });
        } else if (this.articleType === "cons") {
            axios.get('/cons/family/send/' + this.articleId)
                .then(response => {
                    this.articleData = {
                        active: response.data.consFam_active,
                        design: response.data.consFam_design,
                        drawingPath: response.data.consFam_drawingPath,
                        nbrVersion: response.data.consFam_nbrVersion,
                        purchasedBy: response.data.consFam_purchasedBy,
                        qualityApproverID: response.data.consFam_qualityApproverId,
                        ref: response.data.consFam_ref,
                        signatureDate: response.data.consFam_signatureDate,
                        technicalReviewerID: response.data.consFam_technicalReviewerId,
                        validate: response.data.consFam_validate,
                        variablesCharac: response.data.consFam_variablesCharac,
                        version: response.data.consFam_version,
                        created_at: response.data.consFam_created_at,
                        familyMembers: []
                    };
                })
                .catch(error => {
                    console.log(error);
                });
        }
        axios.get('/incmgInsp/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.incmgInsps = response.data;
                for (let i = 0; i < this.incmgInsps.length; i++) {
                    axios.get('/incmgInsp/docControl/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.docControls.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    axios.get('/incmgInsp/aspTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.aspTests.push(response.data[j]);
                            }
                            console.log(this.aspTests);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    axios.get('/incmgInsp/funcTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.funcTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    axios.get('/incmgInsp/dimTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.dimTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    axios.get('/incmgInsp/compTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.compTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            })
            .catch(error => {
                console.log(error);
            });
        axios.get('/artFam/enum/storageCondition/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.stoConds = response.data;
                console.log(this.stoConds);
            }).catch(error => {
                console.log(error);
            });
        axios.get('/purSpe/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.purSpes = response.data;
                this.loaded = true;
                console.log(this.purSpes);
            })
            .catch(error => {
                console.log(error);
            });
    }
}

</script>

<style scoped>
    table {
        width: 100%;
        page-break-before: always!important;
        page-break-after: always!important;
        page-break-inside: avoid!important;
        position: relative;
        border: 1px solid black;
    }
    div {
        page-break-inside: auto!important;
    }
    tr {
        width: auto;
    }
    .header tr:not(.ignored) {
        border: 1px solid black;
    }
    td {
        width: auto;
    }
    .header td:not(.ignored) {
        border: 1px solid black;
    }
    .gray {
        background-color: gray;
    }
    .lightGray {
        background-color: lightgray;
    }
    .contentArticle {
        display: block;
        margin: auto;
        border-collapse: collapse;
        width: 80%;
    }
    .tableName {
        vertical-align: middle;
        /*transform: rotate(-90deg);*/
        width: 15%;
        background: gray;
    }
    .tableDesc {
        width: 25%;
        background-color: lightgray;
        border: 1px solid black;
    }
    .tableValue {
        width: 60%;
        border: 1px solid black;
    }
    p {
        font-size: 14px;
        font-style: normal;
        font-weight: normal;
        font-family: Calibri;
    }
    .tableDesc p:not(.ignored) {
        text-align: center;
        vertical-align: center;
    }
    .tableValue p:not(.ignored) {
        text-align: justify;
        vertical-align: center;
    }
    h2 {
        text-align: center;
    }
    li {
        font-size: 14px;
        font-style: normal;
        font-weight: normal;
        font-family: Calibri;
        list-style: none;
    }
    .ignored {
        border: none!important;
    }
</style>

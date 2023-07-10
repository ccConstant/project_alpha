<!--File name : ArticleConsultV3.vue-->
<!--Creation date : 25 May 2023-->
<!--Update date : 5 Jul 2023-->
<!--Vue Component of the consultation sheet of an article-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="contentArticle">
            <ErrorAlert ref="errorAlert"/>
            <SuccessAlert ref="successAlert"/>
            <p class="reportButton">
                <button class="btn btn-primary" @click="generateReport">Generate Report</button>
                <button v-if="articleData.technicalReviewerID === null" class="btn btn-primary"
                        @click="validate('technical')">Technical Review
                </button>
                <button v-if="articleData.qualityApproverID === null" class="btn btn-primary"
                        @click="validate('quality')">Quality Approval
                </button>
            </p>
            <div id="page" class="page">
                <!-- Header -->
                <p></p>
                <table class="header">
                    <tbody>
                    <tr v-if="articleData.signatureDate === null">
                        <td colspan="5" style="background-color: yellow; text-align: center">
                            <h1>
                                FOR CONSULTATION ONLY - APPROVAL IN PROGRESS
                            </h1>
                        </td>
                    </tr>
                    <tr class="ignored" rowspan="3">
                        <td class="ignored" rowspan="3"
                            style="text-align: center; vertical-align: middle; border: none!important;">
                            <img alt="logo Alpha" src="/images/logo.jpg"
                                 style="width: max-content; height: max-content">
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
                                Reference & Designation :
                            </p>
                            <h2>
                                {{ this.articleData.ref }}
                            </h2>
                            <h2>
                                {{ this.articleData.design }}
                            </h2>
                        </td>
                        <td class="lightGray">
                            <p>
                                Version :
                            </p>
                            <h2>
                                v{{ this.articleData.nbrVersion }}.0
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <h2>
                                ARTICLE SHEET
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left; vertical-align: text-top">
                            <p class="ignored">
                                Verified by (Name and Date) :
                                <span v-if="articleData.signatureDate !== null">
                                    {{ articleData.technicalReviewerName }}
                                </span>
                                <span v-if="articleData.signatureDate !== null">
                                    {{
                                        new Date(articleData.signatureDate).getDate()
                                    }} {{
                                        new Date(articleData.signatureDate).toDateString().slice(4, 7)
                                    }} {{ new Date(articleData.signatureDate).getFullYear() }}
                                </span>
                            </p>
                        </td>
                        <td colspan="2" style="text-align: left; vertical-align: top">
                            <p class="ignored">
                                Approved by (Name and Date) :
                                <span v-if="articleData.signatureDate !== null">
                                    {{ articleData.qualityApproverName }}
                                </span>
                                <span v-if="articleData.signatureDate !== null">
                                    {{
                                        new Date(articleData.signatureDate).getDate()
                                    }} {{
                                        new Date(articleData.signatureDate).toDateString().slice(4, 7)
                                    }} {{ new Date(articleData.signatureDate).getFullYear() }}
                                </span>
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
                                Family Article Identification
                            </h2>
                        </td>
                        <td class="tableDesc">
                            <p>
                                Family reference
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
                                Purchased By
                            </p>
                        </td>
                        <td class="tableValue">
                            <p id="purchased">
                                {{ articleData.purchasedBy === null ? "/" : articleData.purchasedBy }}
                            </p>
                        </td>
                    </tr>
                    <tr v-if="articleType !== 'raw'">
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
                            <p v-if="criticality.length > 0" id="severity">
                                {{ criticality[0].crit_artCriticality === null ? "/" : criticality[0].crit_artCriticality }}
                            </p>
                            <p v-else>
                                /
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Article Member Description -->
                <p></p>
                <table v-if="articleData.variablesCharac !== null" class="header" style="text-align: center">
                    <thead>
                    <tr>
                        <td colspan="3">
                            <h2>
                                Article Variable Caracteristics
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2>
                                N°
                            </h2>
                        </td>
                        <td>
                            <h2>
                                Reference
                            </h2>
                        </td>
                        <td>
                            <h2>
                                Designation
                            </h2>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <!--                        <tr v-for="(variable, index) in articleData.variablesCharac">
                                                <td>
                                                    <p>
                                                        {{ index + 1 }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        {{ variable.variable }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        {{ variable.design }}
                                                    </p>
                                                </td>
                                            </tr>-->
                    <tr>
                        <td>
                            <p>
                                1
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ this.articleData.variablesCharac }}
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ this.articleData.variablesCharacDesign }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Family Members -->
                <p></p>
                <table v-if="subFamilies.length > 0" class="header">
                    <thead>
                    <tr>
                        <td class="gray" colspan="4" style="text-align: center">
                            <h2>
                                Sub Family List
                            </h2>
                        </td>
                    </tr>
                    </thead>
                    <!--<tbody>
                    <tr class="lightGray">
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
                    <tr v-for="member in familyMembers">
                        <td>
                            <p>
                                {{
                                    articleData.genRef.replace('(' + articleData.variablesCharac + ')', member.dimension)
                                }}
                            </p>
                        </td>
                        <td>
                            <p v-if="member.sameValues">
                                {{
                                    articleData.genDesign.replace('(' + articleData.variablesCharac + ')', member.dimension)
                                }}
                            </p>
                            <p v-else>
                                {{
                                    articleData.genDesign.replace('(' + articleData.variablesCharac + ')', member.designation)
                                }}
                            </p>
                        </td>
                        <td>
                            <p v-if="member.sameValues">
                                {{ member.dimension }}
                            </p>
                            <p v-else>
                                {{ member.designation }}
                            </p>
                        </td>
                    </tr>
                    </tbody>-->
                </table>
                <p></p>
                <!-- Storage Condition and Purchasing Specification -->
                <p></p>
                <table class="header">
                    <tbody>
                    <tr colspan="2">
                        <td class="tableName" :rowspan="3*purSpes.length" style="text-align: center;">
                            <h2>
                                Purchasing specifications
                            </h2>
                        </td>
                    </tr>
                    <div v-for="spec in purSpes" :key="spec.id" style="margin: auto; width: 100%; display: table">
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Name of supplier
                                </p>
                            </td>
                            <td class="tableValue">
                                <p id="supplierName">
                                    {{ spec.purSpe_supplier_id }}
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
                                    {{ spec.purSpe_supplier_id !== "Alpha" ? spec.purSpe_supplier_ref : "/" }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Required document(s)
                                </p>
                            </td>
                            <td class="tableValue">
                                <p id="supplierRef">
                                    {{ spec.purSpe_supplier_id !== "Alpha" ? spec.purSpe_requiredDoc : "/" }}
                                </p>
                            </td>
                        </tr>
                    </div>
                    <tr>
                        <td class="gray" style="text-align: center;">
                            <h2>
                                Storage Conditions
                            </h2>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h2 v-if="docControls.length > 0 ||
                            aspTests.length > 0 ||
                            funcTests.length > 0 ||
                            dimTests.length > 0 ||
                            compTests.length > 0" style="text-align: center">
                    INCOMING INSPECTION SPECIFICATIONS
                </h2>
                <p></p>
                <!-- Documentary Control -->
                <div v-for="test in docControls">
                    <table>
                        <tbody>
                        <tr>
                            <td class="tableName" rowspan="7">
                                <h2>
                                    Documentary Control
                                </h2>
                            </td>
                            <td class="tableDesc">
                                <p>
                                    Name
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.docControl_name === null ? "/" : test.docControl_name }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Reference
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.docControl_reference === null ? "/" : test.docControl_reference }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    Material Certificate Specification
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{
                                        test.docControl_materialCertifSpe === null ? "/" : test.docControl_materialCertifSpe
                                    }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="tableDesc">
                                <p>
                                    FDS or MSDS
                                </p>
                            </td>
                            <td class="tableValue">
                                <p>
                                    {{ test.docControl_FDS === null ? "/" : test.docControl_FDS }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p></p>
                </div>
                <p></p>
                <!-- Aspect Test -->
                <div v-for="test in aspTests">
                    <table>
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
                                    {{ test.aspTest_specDoc === null ? "/" : test.aspTest_specDoc }}
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
                                    {{ test.funcTest_unitValue === null ? "/" : test.funcTest_unitValue }}
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
                                    {{ test.funcTest_specDoc === null ? "/" : test.funcTest_specDoc }}
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
                    <table>
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
                                    {{ test.dimTest_unitValue === null ? "/" : test.dimTest_unitValue }}
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
                                    {{ test.dimTest_specDoc === null ? "/" : test.dimTest_specDoc }}
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
                                    {{ test.compTest_unitValue === null ? "/" : test.compTest_unitValue }}
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
                                    {{ test.compTest_specDoc === null ? "/" : test.compTest_specDoc }}
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
                <table class="header" style="text-align: center; vertical-align: middle">
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
                                {{ new Date(articleData.created_at).getDate() }}
                                {{ new Date(articleData.created_at).toDateString().slice(4, 7) }}
                                {{ new Date(articleData.created_at).getFullYear() }}
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
                                {{ hist.history_numVersion }}.0
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ new Date(hist.created_at).getDate() }}
                                {{ new Date(hist.created_at).toDateString().slice(4, 7) }}
                                {{ new Date(hist.created_at).getFullYear() }}
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ hist.history_reasonUpdate }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p></p>
            </div>
            <p class="reportButton">
                <button class="btn btn-primary" @click="generateReport">Generate Report</button>
                <button v-if="articleData.technicalReviewerID === null" class="btn btn-primary"
                        @click="validate('technical')">Technical Review
                </button>
                <button v-if="articleData.qualityApproverID === null" class="btn btn-primary"
                        @click="validate('quality')">Quality Approval
                </button>
            </p>
        </div>
    </div>
</template>

<script>
import html2PDF from "jspdf-html2canvas";
import ErrorAlert from "../../../alert/ErrorAlert.vue";
import SuccessAlert from "../../../alert/SuccesAlert.vue";

export default {
    name: "ArticleConsult",
    components: {
        SuccessAlert,
        ErrorAlert
    },
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
            criticality: [],
            supplier: null,
            familyMembers: [],
            validationMethod: null,
        }
    },
    methods: {
        generateReport() {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width: 300,
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                    dpi: 1200,
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
                output: this.articleData.ref + '_v' + this.articleData.nbrVersion + '.0_export.pdf',
            });
        },
        validate(reason) {
            if (reason === 'technical' && this.$userId.user_makeTechnicalValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else if (reason === 'quality' && this.$userId.user_makeQualityValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                const validVerifUrl = (type, id) => `/${type}/verifValidation/${id}`;
                axios.post(validVerifUrl(this.articleType, this.articleId), {})
                    .then(response => {
                        const techVeriftUrl = (type, id) => `/${type}/validation/${id}`;
                        axios.post(techVeriftUrl(this.articleType, this.articleId), {
                            reason: reason,
                            user_id: this.$userId.id,
                        })
                            .then(response => {
                                if (reason === 'technical') {
                                    this.$refs.successAlert.showAlert(`Technical review made successfully`);
                                } else if (reason === 'quality') {
                                    this.$refs.successAlert.showAlert(`Quality approving made successfully`);
                                }
                                window.location.reload();
                            })
                            //If the controller sends errors we put it in the errors object
                            .catch(error => this.errors = error.response.data.errors);
                    })
                    //If the controller sends errors we put it in the errors object
                    .catch(error => {
                        this.$refs.errorAlert.showAlert(error.response.data['error']);
                    });
            }
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
                        qualityApproverName: response.data.rawFam_qualityApproverName,
                        ref: response.data.rawFam_ref,
                        signatureDate: response.data.rawFam_signatureDate,
                        technicalReviewerID: response.data.rawFam_technicalReviewerId,
                        technicalReviewerName: response.data.rawFam_technicalReviewerName,
                        validate: response.data.rawFam_validate,
                        version: '/',
                    };
                })
                .catch(error => {
                });
            axios.get('/comp/subFam/send/' + this.articleId)
                .then(response => {
                    this.subFamilies = response.data;
                })
                .catch(error => {
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
                        qualityApproverName: response.data.compFam_qualityApproverName,
                        ref: response.data.compFam_ref,
                        signatureDate: response.data.compFam_signatureDate,
                        technicalReviewerID: response.data.compFam_technicalReviewerId,
                        technicalReviewerName: response.data.compFam_technicalReviewerName,
                        validate: response.data.compFam_validate,
                        version: response.data.compFam_version,
                    };
                })
                .catch(error => {
                });
            axios.get('/comp/subFam/send/' + this.articleId)
                .then(response => {
                    this.subFamilies = response.data;
                })
                .catch(error => {
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
                        qualityApproverName: response.data.consFam_qualityApproverName,
                        ref: response.data.consFam_ref,
                        signatureDate: response.data.consFam_signatureDate,
                        technicalReviewerID: response.data.consFam_technicalReviewerId,
                        technicalReviewerName: response.data.consFam_technicalReviewerName,
                        validate: response.data.consFam_validate,
                        version: response.data.consFam_version,
                    };
                })
                .catch(error => {
                });
            axios.get('/cons/subFam/send/' + this.articleId)
                .then(response => {
                    this.subFamilies = response.data;
                })
                .catch(error => {
                });
        }
        axios.get('/artFam/criticality/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.criticality = response.data;
            })
            .catch(error => {
            });
        axios.get('/artFam/history/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.histories = response.data;
            })
            .catch(error => {
            });
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
                        });
                    axios.get('/incmgInsp/aspTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.aspTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                        });
                    axios.get('/incmgInsp/funcTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.funcTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                        });
                    axios.get('/incmgInsp/dimTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.dimTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                        });
                    axios.get('/incmgInsp/compTest/sendFromIncmgInsp/' + this.incmgInsps[i].id)
                        .then(response => {
                            for (let j = 0; j < response.data.length; j++) {
                                this.compTests.push(response.data[j]);
                            }
                        })
                        .catch(error => {
                        });
                }
            })
            .catch(error => {
            });
        axios.get('/artFam/enum/storageCondition/sendUsageByType/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.stoConds = response.data;
            }).catch(error => {
        });
        axios.get('/purSpe/send/' + this.articleType + '/' + this.articleId)
            .then(response => {
                this.purSpes = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}

</script>

<style scoped>
table:not(.ignored) {
    width: 100%;
    page-break-before: always !important;
    page-break-after: always !important;
    page-break-inside: avoid !important;
    position: relative;
    border: 1px solid black;
}

.ignored {
    border: none !important;
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
    background-color: #999898;
}

.lightGray {
    background-color: #e3e2e2;
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
    background: #999898;
}

.tableDesc {
    width: 25%;
    background-color: #e3e2e2;
    border: 1px solid black;
}

.tableValue {
    width: 60%;
    border: 1px solid black;
}

p {
    font-size: 20px;
    font-style: normal;
    font-weight: normal;
    font-family: Calibri;
    marign-left: 10px;
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
    font-size: 20px;
    font-style: normal;
    font-weight: normal;
    font-family: Calibri;
    list-style: none;
}

li:before {
    content: "-";
    color: #999898;
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
}

img {
    width: 100% !important;
    height: auto;
}

.reportButton {
    display: block;
    text-align: center;
    margin: auto;
}

.page {
    display: block;
    margin: auto;
    page-break-inside: auto;
}
</style>

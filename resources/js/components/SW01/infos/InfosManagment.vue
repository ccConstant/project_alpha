<!--File name : InfosManagement.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component with all infos called in the info menu-->

<template>
    <div class="infoManagment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Equipment ID card infos
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <InfoElement title="Internal Reference" :info_content="infos_idCard[0].info_value" :info_id="infos_idCard[0].id"/>
                            <InfoElement title="External Reference" :info_content="infos_idCard[1].info_value" :info_id="infos_idCard[1].id"/>
                            <InfoElement title="Name" :info_content="infos_idCard[2].info_value" :info_id="infos_idCard[2].id"/>
                            <InfoElement title="Type" :info_content="infos_idCard[3].info_value" :info_id="infos_idCard[3].id"/>
                            <InfoElement title="Serial number" :info_content="infos_idCard[4].info_value" :info_id="infos_idCard[4].id"/>
                            <InfoElement title="Constructor" :info_content="infos_idCard[5].info_value" :info_id="infos_idCard[5].id"/>
                            <InfoElement title="Mass" :info_content="infos_idCard[6].info_value" :info_id="infos_idCard[6].id"/>
                            <InfoElement title="Mass unit" :info_content="infos_idCard[7].info_value" :info_id="infos_idCard[7].id"/>
                            <InfoElement title="Mobility" :info_content="infos_idCard[8].info_value" :info_id="infos_idCard[8].id"/>
                            <InfoElement title="Remarks" :info_content="infos_idCard[9].info_value" :info_id="infos_idCard[9].id"/>
                            <InfoElement title="Set" :info_content="infos_idCard[10].info_value" :info_id="infos_idCard[10].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Equipment dimension infos
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <InfoElement title="Title" :info_content="infos_dimension[4].info_value" :info_id="infos_dimension[0].id"/>
                            <InfoElement title="Type" :info_content="infos_dimension[0].info_value" :info_id="infos_dimension[0].id"/>
                            <InfoElement title="Name" :info_content="infos_dimension[1].info_value" :info_id="infos_dimension[1].id"/>
                            <InfoElement title="Unit" :info_content="infos_dimension[2].info_value" :info_id="infos_dimension[2].id"/>
                            <InfoElement title="Value" :info_content="infos_dimension[3].info_value" :info_id="infos_dimension[3].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Equipment Power infos
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <InfoElement title="Type" :info_content="infos_power[0].info_value" :info_id="infos_power[0].id"/>
                            <InfoElement title="Name" :info_content="infos_power[1].info_value" :info_id="infos_power[1].id"/>
                            <InfoElement title="Value" :info_content="infos_power[2].info_value" :info_id="infos_power[2].id"/>
                            <InfoElement title="Unit" :info_content="infos_power[3].info_value" :info_id="infos_power[3].id"/>
                            <InfoElement title="Consumption value" :info_content="infos_power[4].info_value" :info_id="infos_power[4].id"/>
                            <InfoElement title="Consumption unit" :info_content="infos_power[5].info_value" :info_id="infos_power[5].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Equipment Special process infos
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <InfoElement title="Exist" :info_content="infos_spProc[0].info_value" :info_id="infos_spProc[0].id"/>
                            <InfoElement title="Remarks" :info_content="infos_spProc[1].info_value" :info_id="infos_spProc[1].id"/>
                            <InfoElement title="Name" :info_content="infos_spProc[2].info_value" :info_id="infos_spProc[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Equipment Usage infos
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <InfoElement title="Type" :info_content="infos_usage[0].info_value" :info_id="infos_usage[0].id"/>
                            <InfoElement title="Precaution" :info_content="infos_usage[1].info_value" :info_id="infos_usage[1].id"/>
                            <InfoElement title="Reform date" :info_content="infos_usage[2].info_value" :info_id="infos_usage[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Equipment File infos
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">
                            <InfoElement title="Name" :info_content="infos_file[0].info_value" :info_id="infos_file[0].id"/>
                            <InfoElement title="Location" :info_content="infos_file[1].info_value" :info_id="infos_file[1].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        Equipment preventive maintenance operation infos
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                        <div class="accordion-body">
                            <InfoElement title="Description" :info_content="infos_prvMtnOp[0].info_value" :info_id="infos_prvMtnOp[0].id"/>
                            <InfoElement title="Periodicty" :info_content="infos_prvMtnOp[1].info_value" :info_id="infos_prvMtnOp[1].id"/>
                            <InfoElement title="Symbol periodicity" :info_content="infos_prvMtnOp[2].info_value" :info_id="infos_prvMtnOp[2].id"/>
                            <InfoElement title="Number" :info_content="infos_prvMtnOp[3].info_value" :info_id="infos_prvMtnOp[3].id"/>
                            <InfoElement title="Protocol" :info_content="infos_prvMtnOp[4].info_value" :info_id="infos_prvMtnOp[4].id"/>
                            <InfoElement title="Reform date" :info_content="infos_prvMtnOp[5].info_value" :info_id="infos_prvMtnOp[5].id"/>
                            <InfoElement title="Putting into Service" :info_content="infos_prvMtnOp[6].info_value" :info_id="infos_prvMtnOp[6].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        Equipment risk infos
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                        <div class="accordion-body">
                            <InfoElement title="Origin" :info_content="infos_risk[0].info_value" :info_id="infos_risk[0].id"/>
                            <InfoElement title="Remarks" :info_content="infos_risk[1].info_value" :info_id="infos_risk[1].id"/>
                            <InfoElement title="Way of control" :info_content="infos_risk[2].info_value" :info_id="infos_risk[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                        Equipment state infos
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <InfoElement title="Name" :info_content="infos_state[0].info_value" :info_id="infos_state[0].id"/>
                            <InfoElement title="Remarks" :info_content="infos_state[1].info_value" :info_id="infos_state[1].id"/>
                            <InfoElement title="Start date" :info_content="infos_state[2].info_value" :info_id="infos_state[2].id"/>
                            <InfoElement title="Is ok" :info_content="infos_state[3].info_value" :info_id="infos_state[3].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                        Equipment Preventive maintenance operation realized infos
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                        <div class="accordion-body">
                            <InfoElement title="Report number" :info_content="infos_prvMtnOpRlz[0].info_value" :info_id="infos_prvMtnOpRlz[0].id"/>
                            <InfoElement title="Start date" :info_content="infos_prvMtnOpRlz[1].info_value" :info_id="infos_prvMtnOpRlz[1].id"/>
                            <InfoElement title="End date" :info_content="infos_prvMtnOpRlz[2].info_value" :info_id="infos_prvMtnOpRlz[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEleven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                        Equipment Curative Maintenace operation infos
                        </button>
                    </h2>
                    <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven">
                        <div class="accordion-body">
                            <InfoElement title="Number" :info_content="infos_curMtnOp[0].info_value" :info_id="infos_curMtnOp[0].id"/>
                            <InfoElement title="Report number" :info_content="infos_curMtnOp[1].info_value" :info_id="infos_curMtnOp[1].id"/>
                            <InfoElement title="Description" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>
                            <InfoElement title="Start date" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>
                            <InfoElement title="End date" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwelve">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                            User infos
                        </button>
                    </h2>
                    <div id="collapseTwelve" class="accordion-collapse collapse" aria-labelledby="headingTwelve">
                        <div class="accordion-body">
                            <InfoElement title="First name" :info_content="infos_person[0].info_value" :info_id="infos_person[0].id"/>
                            <InfoElement title="Last name" :info_content="infos_person[1].info_value" :info_id="infos_person[1].id"/>
                            <InfoElement title="Initial" :info_content="infos_person[2].info_value" :info_id="infos_person[2].id"/>
                            <InfoElement title="Signature path" :info_content="infos_person[3].info_value" :info_id="infos_person[3].id"/>
                            <InfoElement title="User name" :info_content="infos_person[4].info_value" :info_id="infos_person[4].id"/>
                            <InfoElement title="Password" :info_content="infos_person[5].info_value" :info_id="infos_person[5].id"/>
                            <InfoElement title="Confirmation password" :info_content="infos_person[6].info_value" :info_id="infos_person[6].id"/>
                            <InfoElement title="End date" :info_content="infos_person[7].info_value" :info_id="infos_person[7].id"/>
                            <InfoElement title="MenuUserAccessRight" :info_content="infos_person[8].info_value" :info_id="infos_person[8].id"/>
                            <InfoElement title="ResetUserPasswordRight" :info_content="infos_person[9].info_value" :info_id="infos_person[9].id"/>
                            <InfoElement title="UpdateDataInDraftRight" :info_content="infos_person[10].info_value" :info_id="infos_person[10].id"/>
                            <InfoElement title="ValidateDescriptiveLifeSheetDataRight" :info_content="infos_person[11].info_value" :info_id="infos_person[11].id"/>
                            <InfoElement title="ValidateOtherDataRight" :info_content="infos_person[12].info_value" :info_id="infos_person[12].id"/>
                            <InfoElement title="UpdateDataValidatedButNotSignedRight" :info_content="infos_person[13].info_value" :info_id="infos_person[13].id"/>
                            <InfoElement title="UpdateDescriptiveLifeSheetDataSignedRight" :info_content="infos_person[14].info_value" :info_id="infos_person[14].id"/>
                            <InfoElement title="MakeQualityValidationRight" :info_content="infos_person[15].info_value" :info_id="infos_person[15].id"/>
                            <InfoElement title="MakeTechnicalValidationRight" :info_content="infos_person[16].info_value" :info_id="infos_person[16].id"/>
                            <InfoElement title="MakeEqRespValidationRight" :info_content="infos_person[17].info_value" :info_id="infos_person[17].id"/>
                            <InfoElement title="MakeOpValidationRight" :info_content="infos_person[18].info_value" :info_id="infos_person[18].id"/>
                            <InfoElement title="UpdateEnumRight" :info_content="infos_person[19].info_value" :info_id="infos_person[19].id"/>
                            <InfoElement title="DeleteEnumRight" :info_content="infos_person[20].info_value" :info_id="infos_person[20].id"/>
                            <InfoElement title="AddEnumRight" :info_content="infos_person[21].info_value" :info_id="infos_person[21].id"/>
                            <InfoElement title="DeleteDataNotValidatedLinkedToEqOrMmeRight" :info_content="infos_person[22].info_value" :info_id="infos_person[22].id"/>
                            <InfoElement title="DeleteDataValidatedLinkedToEqOrMmeRight" :info_content="infos_person[23].info_value" :info_id="infos_person[23].id"/>
                            <InfoElement title="DeleteDataSignedLinkedToEqOrEcmeRight" :info_content="infos_person[24].info_value" :info_id="infos_person[24].id"/>
                            <InfoElement title="DeleteEqOrMmeRight" :info_content="infos_person[25].info_value" :info_id="infos_person[25].id"/>
                            <InfoElement title="UpdateInformationRight" :info_content="infos_person[26].info_value" :info_id="infos_person[26].id"/>
                            <InfoElement title="PersonTrainedToGeneralPrinciplesOfEqManagementRight" :info_content="infos_person[27].info_value" :info_id="infos_person[27].id"/>
                            <InfoElement title="FormationEqDate" :info_content="infos_person[28].info_value" :info_id="infos_person[28].id"/>
                            <InfoElement title="PersonTrainedToGeneralPrinciplesOfMMEManagementRight" :info_content="infos_person[29].info_value" :info_id="infos_person[29].id"/>
                            <InfoElement title="FormationMmeDate" :info_content="infos_person[30].info_value" :info_id="infos_person[30].id"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThirteen">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirteen" aria-expanded="true" aria-controls="collapseThirteen">
                            MME ID card infos
                        </button>
                    </h2>
                    <div id="collapseThirteen" class="accordion-collapse collapse show" aria-labelledby="headingThirteen">
                        <div class="accordion-body">
                            <InfoElement title="Internal Reference" :info_content="infos_mme_idCard[0].info_value" :info_id="infos_mme_idCard[0].id"/>
                            <InfoElement title="External Reference" :info_content="infos_mme_idCard[1].info_value" :info_id="infos_mme_idCard[1].id"/>
                            <InfoElement title="Name" :info_content="infos_mme_idCard[2].info_value" :info_id="infos_mme_idCard[2].id"/>
                            <InfoElement title="Serial number" :info_content="infos_mme_idCard[3].info_value" :info_id="infos_mme_idCard[3].id"/>
                            <InfoElement title="Constructor" :info_content="infos_mme_idCard[4].info_value" :info_id="infos_mme_idCard[4].id"/>
                            <InfoElement title="Remarks" :info_content="infos_mme_idCard[5].info_value" :info_id="infos_mme_idCard[5].id"/>
                            <InfoElement title="Set" :info_content="infos_mme_idCard[6].info_value" :info_id="infos_mme_idCard[6].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFourteen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFourteen" aria-expanded="false" aria-controls="collapseFourteen">
                        MME state infos
                        </button>
                    </h2>
                    <div id="collapseFourteen" class="accordion-collapse collapse" aria-labelledby="headingFourteen">
                        <div class="accordion-body">
                            <InfoElement title="Name" :info_content="infos_mme_state[0].info_value" :info_id="infos_state[0].id"/>
                            <InfoElement title="Remarks" :info_content="infos_mme_state[1].info_value" :info_id="infos_state[1].id"/>
                            <InfoElement title="Start date" :info_content="infos_mme_state[2].info_value" :info_id="infos_state[2].id"/>
                            <InfoElement title="Is ok" :info_content="infos_mme_state[3].info_value" :info_id="infos_state[3].id"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFifteen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFifteen" aria-expanded="false" aria-controls="collapseFifteen">
                        MME Verifications infos
                        </button>
                    </h2>
                    <div id="collapseFifteen" class="accordion-collapse collapse" aria-labelledby="headingFifteen">
                        <div class="accordion-body">
                            <InfoElement title="Putting Into Service" :info_content="infos_mme_verif[0].info_value" :info_id="infos_mme_verif[0].id"/>
                              <InfoElement title="Preventive Operation" :info_content="infos_mme_verif[1].info_value" :info_id="infos_mme_verif[1].id"/>
                            <InfoElement title="Number" :info_content="infos_mme_verif[2].info_value" :info_id="infos_mme_verif[2].id"/>
                            <InfoElement title="Name" :info_content="infos_mme_verif[3].info_value" :info_id="infos_mme_verif[3].id"/>
                            <InfoElement title="Expected Result" :info_content="infos_mme_verif[4].info_value" :info_id="infos_mme_verif[4].id"/>
                            <InfoElement title="Non compliance limit" :info_content="infos_mme_verif[5].info_value" :info_id="infos_mme_verif[5].id"/>
                            <InfoElement title="Required skill" :info_content="infos_mme_verif[6].info_value" :info_id="infos_mme_verif[6].id"/>
                            <InfoElement title="Verification acceptance authority" :info_content="infos_mme_verif[7].info_value" :info_id="infos_mme_verif[7].id"/>
                            <InfoElement title="Periodicty" :info_content="infos_mme_verif[8].info_value" :info_id="infos_mme_verif[8].id"/>
                            <InfoElement title="Symbol periodicity" :info_content="infos_mme_verif[9].info_value" :info_id="infos_mme_verif[9].id"/>
                             <InfoElement title="Description" :info_content="infos_mme_verif[10].info_value" :info_id="infos_mme_verif[10].id"/>
                            <InfoElement title="Protocol" :info_content="infos_mme_verif[11].info_value" :info_id="infos_mme_verif[11].id"/>
                            <InfoElement title="Reform date" :info_content="infos_mme_verif[12].info_value" :info_id="infos_mme_verif[12].id"/>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSixTeen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSixTeen" aria-expanded="false" aria-controls="collapseSixTeen">
                        MME Verification realized infos
                        </button>
                    </h2>
                    <div id="collapseSixTeen" class="accordion-collapse collapse" aria-labelledby="headingSixTeen">
                        <div class="accordion-body">
                            <InfoElement title="Report number" :info_content="infos_mme_verifRlz[0].info_value" :info_id="infos_mme_verifRlz[0].id"/>
                            <InfoElement title="Start date" :info_content="infos_mme_verifRlz[1].info_value" :info_id="infos_mme_verifRlz[1].id"/>
                            <InfoElement title="End date" :info_content="infos_mme_verifRlz[2].info_value" :info_id="infos_mme_verifRlz[2].id"/>
                            <InfoElement title="Is passed" :info_content="infos_mme_verifRlz[3].info_value" :info_id="infos_mme_verifRlz[3].id"/>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSevenTeen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSevenTeen" aria-expanded="false" aria-controls="collapseSevenTeen">
                        MME Usage infos
                        </button>
                    </h2>
                    <div id="collapseSevenTeen" class="accordion-collapse collapse" aria-labelledby="headingSevenTeen">
                        <div class="accordion-body">
                            <InfoElement title="Measurement Type" :info_content="infos_mme_usg[0].info_value" :info_id="infos_mme_usg[0].id"/>
                            <InfoElement title="Precision" :info_content="infos_mme_usg[1].info_value" :info_id="infos_mme_usg[1].id"/>
                             <InfoElement title="Application" :info_content="infos_mme_usg[2].info_value" :info_id="infos_mme_usg[2].id"/>
                            <InfoElement title="Metrological level" :info_content="infos_mme_usg[3].info_value" :info_id="infos_mme_usg[3].id"/>
                            <InfoElement title="Reform date" :info_content="infos_mme_usg[4].info_value" :info_id="infos_mme_usg[4].id"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingHeightTeen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHeightTeen" aria-expanded="false" aria-controls="collapseHeightTeen">
                        MME Precaution infos
                        </button>
                    </h2>
                    <div id="collapseHeightTeen" class="accordion-collapse collapse" aria-labelledby="headingHeightTeen">
                        <div class="accordion-body">
                            <InfoElement title="Type" :info_content="infos_mme_prctn[0].info_value" :info_id="infos_mme_prctn[0].id"/>
                            <InfoElement title="Description" :info_content="infos_mme_prctn[1].info_value" :info_id="infos_mme_prctn[1].id"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InfoElement from './InfoElement.vue'
export default {
    components:{
        InfoElement
    },
    data(){
        return{
            infos:[],
            infos_idCard:[],
            infos_dimension:[],
            infos_power:[],
            infos_spProc:[],
            infos_usage:[],
            infos_file:[],
            infos_prvMtnOp:[],
            infos_risk:[],
            infos_state:[],
            infos_prvMtnOpRlz:[],
            infos_curMtnOp:[],
            infos_enum:[],
            infos_person:[],
            infos_mme_idCard:[],
            infos_mme_state:[],
            infos_mme_verif:[],
            infos_mme_verifRlz:[],
            infos_mme_usg:[],
            infos_mme_prctn:[],
            loaded:false
        }
    },
    created(){
            if(this.$userId.user_updateInformationRight!=true){
                this.$router.replace({ name: "home" })
            }
            axios.get('/info/send/eqIdCard')
            .then (response=> {
                this.infos_idCard=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/dimension')
            .then (response=> {
                this.infos_dimension=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/power')
            .then (response=> {
                this.infos_power=response.data;

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/specialProcess')
            .then (response=> {
                this.infos_spProc=response.data;
            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/usage')
            .then (response=> {
                this.infos_usage=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/file')
            .then (response=> {
                this.infos_file=response.data;
            })
            .catch(error => console.log(error)) ;


            axios.get('/info/send/preventiveMaintenanceOperation')
            .then (response=> {
                this.infos_prvMtnOp=response.data;
            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/risk')
            .then (response=> {
                this.infos_risk=response.data;
            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/state')
            .then (response=> {
                this.infos_state=response.data;
                })
            .catch(error => console.log(error)) ;

           axios.get('/info/send/preventiveMaintenanceOperationRealized')
            .then (response=> {
                this.infos_prvMtnOpRlz=response.data;
                })
            .catch(error => console.log(error)) ;

           axios.get('/info/send/curativeMaintenanceOperation')
            .then (response=> {
                this.infos_curMtnOp=response.data;
                })
            .catch(error => console.log(error)) ;

           axios.get('/info/send/person')
            .then (response=> {
                console.log(response.data)
                this.infos_person=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/mme')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_idCard=response.data;
                })
            .catch(error => console.log(error)) ;
            axios.get('/info/send/mme_state')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_state=response.data;
                })
            .catch(error => console.log(error)) ;
            axios.get('/info/send/verif')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_verif=response.data;
                })
            .catch(error => console.log(error)) ;
            axios.get('/info/send/verifRlz')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_verifRlz=response.data;
                })
            .catch(error => console.log(error)) ;
            axios.get('/info/send/mme_usage')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_usg=response.data;
                })
            .catch(error => console.log(error)) ;
            axios.get('/info/send/mme_precaution')
            .then (response=> {
                console.log(response.data)
                this.infos_mme_prctn=response.data;
                this.loaded=true;
                })
            .catch(error => console.log(error)) ;

    }
}
</script>

<style>

</style>

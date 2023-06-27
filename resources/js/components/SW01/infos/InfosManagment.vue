<!--File name : InfosManagement.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component with all infos called in the info menu-->

<template>
    <div class="infoManagment">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Equipment ID card infos
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_idCard[0].info_value" :info_id="infos_idCard[0].id"
                                         title="Internal Reference"/>
                            <InfoElement :info_content="infos_idCard[1].info_value" :info_id="infos_idCard[1].id"
                                         title="External Reference"/>
                            <InfoElement :info_content="infos_idCard[2].info_value" :info_id="infos_idCard[2].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_idCard[3].info_value" :info_id="infos_idCard[3].id"
                                         title="Type"/>
                            <InfoElement :info_content="infos_idCard[4].info_value" :info_id="infos_idCard[4].id"
                                         title="Serial number"/>
                            <InfoElement :info_content="infos_idCard[5].info_value" :info_id="infos_idCard[5].id"
                                         title="Constructor"/>
                            <InfoElement :info_content="infos_idCard[6].info_value" :info_id="infos_idCard[6].id"
                                         title="Mass"/>
                            <InfoElement :info_content="infos_idCard[7].info_value" :info_id="infos_idCard[7].id"
                                         title="Mass unit"/>
                            <InfoElement :info_content="infos_idCard[8].info_value" :info_id="infos_idCard[8].id"
                                         title="Mobility"/>
                            <InfoElement :info_content="infos_idCard[9].info_value" :info_id="infos_idCard[9].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_idCard[10].info_value" :info_id="infos_idCard[10].id"
                                         title="Set"/>
                            <InfoElement :info_content="infos_idCard[11].info_value" :info_id="infos_idCard[11].id"
                            title="Location"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment dimension infos
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_dimension[4].info_value" :info_id="infos_dimension[0].id"
                                         title="Title"/>
                            <InfoElement :info_content="infos_dimension[0].info_value" :info_id="infos_dimension[0].id"
                                         title="Type"/>
                            <InfoElement :info_content="infos_dimension[1].info_value" :info_id="infos_dimension[1].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_dimension[2].info_value" :info_id="infos_dimension[2].id"
                                         title="Unit"/>
                            <InfoElement :info_content="infos_dimension[3].info_value" :info_id="infos_dimension[3].id"
                                         title="Value"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            Equipment Power infos
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_power[0].info_value" :info_id="infos_power[0].id"
                                         title="Type"/>
                            <InfoElement :info_content="infos_power[1].info_value" :info_id="infos_power[1].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_power[2].info_value" :info_id="infos_power[2].id"
                                         title="Value"/>
                            <InfoElement :info_content="infos_power[3].info_value" :info_id="infos_power[3].id"
                                         title="Unit"/>
                            <InfoElement :info_content="infos_power[4].info_value" :info_id="infos_power[4].id"
                                         title="Consumption value"/>
                            <InfoElement :info_content="infos_power[5].info_value" :info_id="infos_power[5].id"
                                         title="Consumption unit"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFour" class="accordion-header">
                        <button aria-controls="collapseFour" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                            Equipment Special process infos
                        </button>
                    </h2>
                    <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_spProc[0].info_value" :info_id="infos_spProc[0].id"
                                         title="Exist"/>
                            <InfoElement :info_content="infos_spProc[1].info_value" :info_id="infos_spProc[1].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_spProc[2].info_value" :info_id="infos_spProc[2].id"
                                         title="Name"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFive" class="accordion-header">
                        <button aria-controls="collapseFive" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFive" data-bs-toggle="collapse" type="button">
                            Equipment Usage infos
                        </button>
                    </h2>
                    <div id="collapseFive" aria-labelledby="headingFive" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_usage[0].info_value" :info_id="infos_usage[0].id"
                                         title="Type"/>
                            <InfoElement :info_content="infos_usage[1].info_value" :info_id="infos_usage[1].id"
                                         title="Precaution"/>
                            <InfoElement :info_content="infos_usage[2].info_value" :info_id="infos_usage[2].id"
                                         title="Reform date"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSix" class="accordion-header">
                        <button aria-controls="collapseSix" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSix" data-bs-toggle="collapse" type="button">
                            Equipment File infos
                        </button>
                    </h2>
                    <div id="collapseSix" aria-labelledby="headingSix" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_file[0].info_value" :info_id="infos_file[0].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_file[1].info_value" :info_id="infos_file[1].id"
                                         title="Location"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSeven" class="accordion-header">
                        <button aria-controls="collapseSeven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSeven" data-bs-toggle="collapse" type="button">
                            Equipment preventive maintenance operation infos
                        </button>
                    </h2>
                    <div id="collapseSeven" aria-labelledby="headingSeven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_prvMtnOp[0].info_value" :info_id="infos_prvMtnOp[0].id"
                                         title="Description"/>
                            <InfoElement :info_content="infos_prvMtnOp[1].info_value" :info_id="infos_prvMtnOp[1].id"
                                         title="Periodicty"/>
                            <InfoElement :info_content="infos_prvMtnOp[2].info_value" :info_id="infos_prvMtnOp[2].id"
                                         title="Symbol periodicity"/>
                            <InfoElement :info_content="infos_prvMtnOp[3].info_value" :info_id="infos_prvMtnOp[3].id"
                                         title="Number"/>
                            <InfoElement :info_content="infos_prvMtnOp[4].info_value" :info_id="infos_prvMtnOp[4].id"
                                         title="Protocol"/>
                            <InfoElement :info_content="infos_prvMtnOp[5].info_value" :info_id="infos_prvMtnOp[5].id"
                                         title="Reform date"/>
                            <InfoElement :info_content="infos_prvMtnOp[6].info_value" :info_id="infos_prvMtnOp[6].id"
                                         title="Putting into Service"/>
                            <InfoElement :info_content="infos_prvMtnOp[7].info_value" :info_id="infos_prvMtnOp[7].id"
                            title="Preventive operation"/>
                            <InfoElement :info_content="infos_prvMtnOp[8].info_value" :info_id="infos_prvMtnOp[8].id"
                            title="Type validation required"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingNine" class="accordion-header">
                        <button aria-controls="collapseEight" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEight" data-bs-toggle="collapse" type="button">
                            Equipment risk infos
                        </button>
                    </h2>
                    <div id="collapseEight" aria-labelledby="headingEight" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_risk[0].info_value" :info_id="infos_risk[0].id"
                                         title="Origin"/>
                            <InfoElement :info_content="infos_risk[1].info_value" :info_id="infos_risk[1].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_risk[2].info_value" :info_id="infos_risk[2].id"
                                         title="Way of control"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingNine" class="accordion-header">
                        <button aria-controls="collapseNine" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseNine" data-bs-toggle="collapse" type="button">
                            Equipment state infos
                        </button>
                    </h2>
                    <div id="collapseNine" aria-labelledby="headingNine" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_state[0].info_value" :info_id="infos_state[0].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_state[1].info_value" :info_id="infos_state[1].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_state[2].info_value" :info_id="infos_state[2].id"
                                         title="Start date"/>
                            <InfoElement :info_content="infos_state[3].info_value" :info_id="infos_state[3].id"
                                         title="Is ok"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTen" class="accordion-header">
                        <button aria-controls="collapseTen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTen" data-bs-toggle="collapse" type="button">
                            Equipment Preventive maintenance operation realized infos
                        </button>
                    </h2>
                    <div id="collapseTen" aria-labelledby="headingTen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_prvMtnOpRlz[0].info_value" :info_id="infos_prvMtnOpRlz[0].id"
                                         title="Report number"/>
                            <InfoElement :info_content="infos_prvMtnOpRlz[1].info_value" :info_id="infos_prvMtnOpRlz[1].id"
                                         title="Start date"/>
                            <InfoElement :info_content="infos_prvMtnOpRlz[2].info_value" :info_id="infos_prvMtnOpRlz[2].id"
                                         title="End date"/>
                            <InfoElement :info_content="infos_prvMtnOpRlz[3].info_value" :info_id="infos_prvMtnOpRlz[3].id"
                                        title="Remarks"/>
                            <InfoElement :info_content="infos_prvMtnOpRlz[4].info_value" :info_id="infos_prvMtnOpRlz[4].id"
                                        title="Password"/>
                             <InfoElement :info_content="infos_prvMtnOpRlz[5].info_value" :info_id="infos_prvMtnOpRlz[5].id"
                                        title="Realize"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingEleven" class="accordion-header">
                        <button aria-controls="collapseEleven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEleven" data-bs-toggle="collapse" type="button">
                            Equipment Curative Maintenace operation infos
                        </button>
                    </h2>
                    <div id="collapseEleven" aria-labelledby="headingEleven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_curMtnOp[0].info_value" :info_id="infos_curMtnOp[0].id"
                                         title="Number"/>
                            <InfoElement :info_content="infos_curMtnOp[1].info_value" :info_id="infos_curMtnOp[1].id"
                                         title="Report number"/>
                            <InfoElement :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"
                                         title="Description"/>
                            <InfoElement :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"
                                         title="Start date"/>
                            <InfoElement :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"
                                         title="End date"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwelve" class="accordion-header">
                        <button aria-controls="collapseTwelve" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwelve" data-bs-toggle="collapse" type="button">
                            User infos
                        </button>
                    </h2>
                    <div id="collapseTwelve" aria-labelledby="headingTwelve" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_person[0].info_value" :info_id="infos_person[0].id"
                                         title="First name"/>
                            <InfoElement :info_content="infos_person[1].info_value" :info_id="infos_person[1].id"
                                         title="Last name"/>
                            <InfoElement :info_content="infos_person[2].info_value" :info_id="infos_person[2].id"
                                         title="Initial"/>
                            <InfoElement :info_content="infos_person[3].info_value" :info_id="infos_person[3].id"
                                         title="Signature path"/>
                            <InfoElement :info_content="infos_person[4].info_value" :info_id="infos_person[4].id"
                                         title="User name"/>
                            <InfoElement :info_content="infos_person[5].info_value" :info_id="infos_person[5].id"
                                         title="Password"/>
                            <InfoElement :info_content="infos_person[6].info_value" :info_id="infos_person[6].id"
                                         title="Confirmation password"/>
                            <InfoElement :info_content="infos_person[7].info_value" :info_id="infos_person[7].id"
                                         title="End date"/>
                            <InfoElement :info_content="infos_person[8].info_value" :info_id="infos_person[8].id"
                                         title="MenuUserAccessRight"/>
                            <InfoElement :info_content="infos_person[9].info_value" :info_id="infos_person[9].id"
                                         title="ResetUserPasswordRight"/>
                            <InfoElement :info_content="infos_person[10].info_value" :info_id="infos_person[10].id"
                                         title="UpdateDataInDraftRight"/>
                            <InfoElement :info_content="infos_person[11].info_value"
                                         :info_id="infos_person[11].id" title="ValidateDescriptiveLifeSheetDataRight"/>
                            <InfoElement :info_content="infos_person[12].info_value" :info_id="infos_person[12].id"
                                         title="ValidateOtherDataRight"/>
                            <InfoElement :info_content="infos_person[13].info_value"
                                         :info_id="infos_person[13].id" title="UpdateDataValidatedButNotSignedRight"/>
                            <InfoElement :info_content="infos_person[14].info_value"
                                         :info_id="infos_person[14].id" title="UpdateDescriptiveLifeSheetDataSignedRight"/>
                            <InfoElement :info_content="infos_person[15].info_value" :info_id="infos_person[15].id"
                                         title="MakeQualityValidationRight"/>
                            <InfoElement :info_content="infos_person[16].info_value"
                                         :info_id="infos_person[16].id" title="MakeTechnicalValidationRight"/>
                            <InfoElement :info_content="infos_person[17].info_value" :info_id="infos_person[17].id"
                                         title="MakeEqRespValidationRight"/>
                            <InfoElement :info_content="infos_person[18].info_value" :info_id="infos_person[18].id"
                                         title="MakeOpValidationRight"/>
                            <InfoElement :info_content="infos_person[19].info_value" :info_id="infos_person[19].id"
                                         title="UpdateEnumRight"/>
                            <InfoElement :info_content="infos_person[20].info_value" :info_id="infos_person[20].id"
                                         title="DeleteEnumRight"/>
                            <InfoElement :info_content="infos_person[21].info_value" :info_id="infos_person[21].id"
                                         title="AddEnumRight"/>
                            <InfoElement :info_content="infos_person[22].info_value"
                                         :info_id="infos_person[22].id" title="DeleteDataNotValidatedLinkedToEqOrMmeRight"/>
                            <InfoElement :info_content="infos_person[23].info_value"
                                         :info_id="infos_person[23].id" title="DeleteDataValidatedLinkedToEqOrMmeRight"/>
                            <InfoElement :info_content="infos_person[24].info_value"
                                         :info_id="infos_person[24].id" title="DeleteDataSignedLinkedToEqOrEcmeRight"/>
                            <InfoElement :info_content="infos_person[25].info_value" :info_id="infos_person[25].id"
                                         title="DeleteEqOrMmeRight"/>
                            <InfoElement :info_content="infos_person[26].info_value" :info_id="infos_person[26].id"
                                         title="UpdateInformationRight"/>
                            <InfoElement :info_content="infos_person[27].info_value"
                                         :info_id="infos_person[27].id" title="PersonTrainedToGeneralPrinciplesOfEqManagementRight"/>
                            <InfoElement :info_content="infos_person[28].info_value" :info_id="infos_person[28].id"
                                         title="FormationEqDate"/>
                            <InfoElement :info_content="infos_person[29].info_value"
                                         :info_id="infos_person[29].id" title="PersonTrainedToGeneralPrinciplesOfMMEManagementRight"/>
                            <InfoElement :info_content="infos_person[30].info_value" :info_id="infos_person[30].id"
                                         title="FormationMmeDate"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingThirteen" class="accordion-header">
                        <button aria-controls="collapseThirteen" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseThirteen" data-bs-toggle="collapse"
                                type="button">
                            MME ID card infos
                        </button>
                    </h2>
                    <div id="collapseThirteen" aria-labelledby="headingThirteen"
                         class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_idCard[0].info_value" :info_id="infos_mme_idCard[0].id"
                                         title="Internal Reference"/>
                            <InfoElement :info_content="infos_mme_idCard[1].info_value" :info_id="infos_mme_idCard[1].id"
                                         title="External Reference"/>
                            <InfoElement :info_content="infos_mme_idCard[2].info_value" :info_id="infos_mme_idCard[2].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_mme_idCard[3].info_value" :info_id="infos_mme_idCard[3].id"
                                         title="Serial number"/>
                            <InfoElement :info_content="infos_mme_idCard[4].info_value" :info_id="infos_mme_idCard[4].id"
                                         title="Constructor"/>
                            <InfoElement :info_content="infos_mme_idCard[5].info_value" :info_id="infos_mme_idCard[5].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_mme_idCard[6].info_value" :info_id="infos_mme_idCard[6].id"
                                         title="Set"/>
                            <InfoElement :info_content="infos_mme_idCard[7].info_value" :info_id="infos_mme_idCard[7].id"
                            title="Location"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFourteen" class="accordion-header">
                        <button aria-controls="collapseFourteen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFourteen" data-bs-toggle="collapse"
                                type="button">
                            MME state infos
                        </button>
                    </h2>
                    <div id="collapseFourteen" aria-labelledby="headingFourteen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_state[0].info_value" :info_id="infos_state[0].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_mme_state[1].info_value" :info_id="infos_state[1].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_mme_state[2].info_value" :info_id="infos_state[2].id"
                                         title="Start date"/>
                            <InfoElement :info_content="infos_mme_state[3].info_value" :info_id="infos_state[3].id"
                                         title="Is ok"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingFifteen" class="accordion-header">
                        <button aria-controls="collapseFifteen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFifteen" data-bs-toggle="collapse" type="button">
                            MME Verifications infos
                        </button>
                    </h2>
                    <div id="collapseFifteen" aria-labelledby="headingFifteen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_verif[0].info_value" :info_id="infos_mme_verif[0].id"
                                         title="Putting Into Service"/>
                            <InfoElement :info_content="infos_mme_verif[1].info_value" :info_id="infos_mme_verif[1].id"
                                         title="Preventive Operation"/>
                            <InfoElement :info_content="infos_mme_verif[2].info_value" :info_id="infos_mme_verif[2].id"
                                         title="Number"/>
                            <InfoElement :info_content="infos_mme_verif[3].info_value" :info_id="infos_mme_verif[3].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_mme_verif[4].info_value" :info_id="infos_mme_verif[4].id"
                                         title="Expected Result"/>
                            <InfoElement :info_content="infos_mme_verif[5].info_value" :info_id="infos_mme_verif[5].id"
                                         title="Non compliance limit"/>
                            <InfoElement :info_content="infos_mme_verif[6].info_value" :info_id="infos_mme_verif[6].id"
                                         title="Required skill"/>
                            <InfoElement :info_content="infos_mme_verif[7].info_value"
                                         :info_id="infos_mme_verif[7].id"
                                         title="Verification acceptance authority"/>
                            <InfoElement :info_content="infos_mme_verif[8].info_value" :info_id="infos_mme_verif[8].id"
                                         title="Periodicty"/>
                            <InfoElement :info_content="infos_mme_verif[9].info_value" :info_id="infos_mme_verif[9].id"
                                         title="Symbol periodicity"/>
                            <InfoElement :info_content="infos_mme_verif[10].info_value" :info_id="infos_mme_verif[10].id"
                                         title="Description"/>
                            <InfoElement :info_content="infos_mme_verif[11].info_value" :info_id="infos_mme_verif[11].id"
                                         title="Protocol"/>
                            <InfoElement :info_content="infos_mme_verif[12].info_value" :info_id="infos_mme_verif[12].id"
                                         title="Reform date"/>
                            <InfoElement :info_content="infos_mme_verif[13].info_value" :info_id="infos_mme_verif[13].id"
                                        title="Mesure range"/>
                            <InfoElement :info_content="infos_mme_verif[14].info_value" :info_id="infos_mme_verif[14].id"
                                        title="Mesure Uncertainty"/>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingSixTeen" class="accordion-header">
                        <button aria-controls="collapseSixTeen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSixTeen" data-bs-toggle="collapse" type="button">
                            MME Verification realized infos
                        </button>
                    </h2>
                    <div id="collapseSixTeen" aria-labelledby="headingSixTeen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_verifRlz[0].info_value" :info_id="infos_mme_verifRlz[0].id"
                                         title="Report number"/>
                            <InfoElement :info_content="infos_mme_verifRlz[1].info_value" :info_id="infos_mme_verifRlz[1].id"
                                         title="Start date"/>
                            <InfoElement :info_content="infos_mme_verifRlz[2].info_value" :info_id="infos_mme_verifRlz[2].id"
                                         title="End date"/>
                            <InfoElement :info_content="infos_mme_verifRlz[3].info_value" :info_id="infos_mme_verifRlz[3].id"
                                         title="Is passed"/>
                            <InfoElement :info_content="infos_mme_verifRlz[4].info_value" :info_id="infos_mme_verifRlz[4].id"
                                        title="Comments"/>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingSevenTeen" class="accordion-header">
                        <button aria-controls="collapseSevenTeen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSevenTeen" data-bs-toggle="collapse"
                                type="button">
                            MME Usage infos
                        </button>
                    </h2>
                    <div id="collapseSevenTeen" aria-labelledby="headingSevenTeen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_usg[0].info_value" :info_id="infos_mme_usg[0].id"
                                         title="Measurement Type"/>
                            <InfoElement :info_content="infos_mme_usg[1].info_value" :info_id="infos_mme_usg[1].id"
                                         title="Precision"/>
                            <InfoElement :info_content="infos_mme_usg[2].info_value" :info_id="infos_mme_usg[2].id"
                                         title="Application"/>
                            <InfoElement :info_content="infos_mme_usg[3].info_value" :info_id="infos_mme_usg[3].id"
                                         title="Metrological level"/>
                            <InfoElement :info_content="infos_mme_usg[4].info_value" :info_id="infos_mme_usg[4].id"
                                         title="Reform date"/>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 id="headingHeightTeen" class="accordion-header">
                        <button aria-controls="collapseHeightTeen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseHeightTeen" data-bs-toggle="collapse"
                                type="button">
                            MME Precaution infos
                        </button>
                    </h2>
                    <div id="collapseHeightTeen" aria-labelledby="headingHeightTeen"
                         class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_mme_prctn[0].info_value" :info_id="infos_mme_prctn[0].id"
                                         title="Type"/>
                            <InfoElement :info_content="infos_mme_prctn[1].info_value" :info_id="infos_mme_prctn[1].id"
                                         title="Description"/>
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
    components: {
        InfoElement
    },
    data() {
        return {
            infos: [],
            infos_idCard: [],
            infos_dimension: [],
            infos_power: [],
            infos_spProc: [],
            infos_usage: [],
            infos_file: [],
            infos_prvMtnOp: [],
            infos_risk: [],
            infos_state: [],
            infos_prvMtnOpRlz: [],
            infos_curMtnOp: [],
            infos_enum: [],
            infos_person: [],
            infos_mme_idCard: [],
            infos_mme_state: [],
            infos_mme_verif: [],
            infos_mme_verifRlz: [],
            infos_mme_usg: [],
            infos_mme_prctn: [],
            loaded: false
        }
    },
    created() {
        if (this.$userId.user_updateInformationRight != true) {
            this.$router.replace({name: "home"})
        }
        axios.get('/info/send/eqIdCard')
            .then(response => {
                this.infos_idCard = response.data;
                axios.get('/info/send/dimension')
                    .then(response => {
                        this.infos_dimension = response.data;
                        axios.get('/info/send/power')
                            .then(response => {
                                this.infos_power = response.data;
                                axios.get('/info/send/specialProcess')
                                    .then(response => {
                                        this.infos_spProc = response.data;
                                        axios.get('/info/send/usage')
                                            .then(response => {
                                                this.infos_usage = response.data;
                                                axios.get('/info/send/file')
                                                    .then(response => {
                                                        this.infos_file = response.data;
                                                        axios.get('/info/send/preventiveMaintenanceOperation')
                                                            .then(response => {
                                                                this.infos_prvMtnOp = response.data;
                                                                axios.get('/info/send/risk')
                                                                    .then(response => {
                                                                        this.infos_risk = response.data;
                                                                        axios.get('/info/send/state')
                                                                            .then(response => {
                                                                                this.infos_state = response.data;
                                                                                axios.get('/info/send/preventiveMaintenanceOperationRealized')
                                                                                    .then(response => {
                                                                                        this.infos_prvMtnOpRlz = response.data;
                                                                                        axios.get('/info/send/curativeMaintenanceOperation')
                                                                                            .then(response => {
                                                                                                this.infos_curMtnOp = response.data;
                                                                                                axios.get('/info/send/person')
                                                                                                    .then(response => {
                                                                                                        this.infos_person = response.data;
                                                                                                        axios.get('/info/send/mme')
                                                                                                            .then(response => {
                                                                                                                this.infos_mme_idCard = response.data;
                                                                                                                axios.get('/info/send/mme_state')
                                                                                                                    .then(response => {
                                                                                                                        this.infos_mme_state = response.data;
                                                                                                                        axios.get('/info/send/verif')
                                                                                                                            .then(response => {
                                                                                                                                this.infos_mme_verif = response.data;
                                                                                                                                axios.get('/info/send/verifRlz')
                                                                                                                                    .then(response => {
                                                                                                                                        this.infos_mme_verifRlz = response.data;
                                                                                                                                        axios.get('/info/send/mme_usage')
                                                                                                                                            .then(response => {
                                                                                                                                                this.infos_mme_usg = response.data;
                                                                                                                                                axios.get('/info/send/mme_precaution')
                                                                                                                                                    .then(response => {
                                                                                                                                                        this.infos_mme_prctn = response.data;
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

<style>

</style>

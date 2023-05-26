<!--File name : InfosManagement.vue-->
<!--Creation date : 15 May 2023-->
<!--Update date : 15 May 2023-->
<!--Vue Component with all infos called in the info menu-->

<template>
    <div class="infoManagement">
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Article Family infos
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_artFam[0].info_value" :info_id="infos_artFam[0].id"
                                         title="Article Family Reference"/>
                            <InfoElement :info_content="infos_artFam[1].info_value" :info_id="infos_artFam[1].id"
                                         title="Article Family Designation"/>
                            <InfoElement :info_content="infos_artFam[2].info_value" :info_id="infos_artFam[2].id"
                                         title="Article Family Drawing Path"/>
                            <InfoElement :info_content="infos_artFam[3].info_value" :info_id="infos_artFam[3].id"
                                         title="Article Family Purchased By"/>
                            <InfoElement :info_content="infos_artFam[4].info_value" :info_id="infos_artFam[4].id"
                                         title="Article Family Variable Characteristic"/>
                            <InfoElement :info_content="infos_artFam[5].info_value" :info_id="infos_artFam[5].id"
                                         title="Article Family Version"/>
                            <InfoElement :info_content="infos_artFam[6].info_value" :info_id="infos_artFam[6].id"
                                         title="Article Family Generic Designation"/>
                            <InfoElement :info_content="infos_artFam[7].info_value" :info_id="infos_artFam[7].id"
                                         title="Article Family Generic Reference"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Article Family Member infos
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_artMember[0].info_value" :info_id="infos_artMember[0].id"
                                         title="Dimension"/>
                            <InfoElement :info_content="infos_artMember[1].info_value" :info_id="infos_artMember[1].id"
                                         title="Same values"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            Article Purchase Specifications infos
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_purSpe[0].info_value" :info_id="infos_purSpe[0].id"
                                         title="Required doc"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFour" class="accordion-header">
                        <button aria-controls="collapseFour" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                            Article Criticality infos
                        </button>
                    </h2>
                    <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_crit[0].info_value" :info_id="infos_crit[0].id"
                                         title="Article Criticality"/>
                            <InfoElement :info_content="infos_crit[1].info_value" :info_id="infos_crit[1].id"
                                         title="Article material Contact Criticality"/>
                            <InfoElement :info_content="infos_crit[2].info_value" :info_id="infos_crit[2].id"
                                         title="Article Material Function Criticality doc"/>
                            <InfoElement :info_content="infos_crit[3].info_value" :info_id="infos_crit[3].id"
                                         title="Article Process Criticality doc"/>
                            <InfoElement :info_content="infos_crit[4].info_value" :info_id="infos_crit[4].id"
                                         title="Remarks"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFive" class="accordion-header">
                        <button aria-controls="collapseFive" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFive" data-bs-toggle="collapse" type="button">
                            Supplier infos
                        </button>
                    </h2>
                    <div id="collapseFive" aria-labelledby="headingFive" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_supplier[0].info_value" :info_id="infos_supplier[0].id"
                                         title="Supplier Name"/>
                            <InfoElement :info_content="infos_supplier[1].info_value" :info_id="infos_supplier[1].id"
                                         title="Supplier Reception Number"/>
                            <InfoElement :info_content="infos_supplier[2].info_value" :info_id="infos_supplier[2].id"
                                         title="Supplier Alpha form Id"/>
                            <InfoElement :info_content="infos_supplier[3].info_value" :info_id="infos_supplier[3].id"
                                         title="Supplier agreement number"/>
                            <InfoElement :info_content="infos_supplier[4].info_value" :info_id="infos_supplier[4].id"
                                         title="Supplier quality certificate number"/>
                            <InfoElement :info_content="infos_supplier[5].info_value" :info_id="infos_supplier[5].id"
                                         title="Supplier specifics instructions"/>
                            <InfoElement :info_content="infos_supplier[6].info_value" :info_id="infos_supplier[6].id"
                                         title="Supplier version"/>
                            <InfoElement :info_content="infos_supplier[7].info_value" :info_id="infos_supplier[7].id"
                                         title="Supplier siret"/>
                            <InfoElement :info_content="infos_supplier[8].info_value" :info_id="infos_supplier[8].id"
                                         title="Supplier website"/>
                            <InfoElement :info_content="infos_supplier[9].info_value" :info_id="infos_supplier[9].id"
                                         title="Supplier activity"/>
                            <InfoElement :info_content="infos_supplier[10].info_value" :info_id="infos_supplier[10].id"
                                         title="Supplier real"/>
                            <InfoElement :info_content="infos_supplier[11].info_value" :info_id="infos_supplier[11].id"
                                         title="Supplier VAT number"/>
                            <InfoElement :info_content="infos_supplier[12].info_value" :info_id="infos_supplier[12].id"
                                         title="Supplier critical"/>
                            <InfoElement :info_content="infos_supplier[13].info_value" :info_id="infos_supplier[13].id"
                                         title="Supplier end link folder"/>
                            <InfoElement :info_content="infos_supplier[14].info_value" :info_id="infos_supplier[14].id"
                                         title="Supplier end active"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSix" class="accordion-header">
                        <button aria-controls="collapseSix" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSix" data-bs-toggle="collapse" type="button">
                            Supplier Address infos
                        </button>
                    </h2>
                    <div id="collapseSix" aria-labelledby="headingSix" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_supplierAdr[0].info_value" :info_id="infos_supplierAdr[0].id"
                                         title="Supplier Address Name"/>
                            <InfoElement :info_content="infos_supplierAdr[1].info_value" :info_id="infos_supplierAdr[1].id"
                                         title="Supplier Address Street"/>
                            <InfoElement :info_content="infos_supplierAdr[2].info_value" :info_id="infos_supplierAdr[2].id"
                                         title="Supplier Address Town"/>
                            <InfoElement :info_content="infos_supplierAdr[3].info_value" :info_id="infos_supplierAdr[3].id"
                                         title="Supplier Address Country"/>
                            <InfoElement :info_content="infos_supplierAdr[4].info_value" :info_id="infos_supplierAdr[4].id"
                                         title="Supplier Address PostalCode"/>
                            <InfoElement :info_content="infos_supplierAdr[5].info_value" :info_id="infos_supplierAdr[5].id"
                                         title="Supplier Address principal"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSeven" class="accordion-header">
                        <button aria-controls="collapseSeven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSeven" data-bs-toggle="collapse" type="button">
                            Supplier Contact infos
                        </button>
                    </h2>
                    <div id="collapseSeven" aria-labelledby="headingSeven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_supplierContact[0].info_value" :info_id="infos_supplierContact[0].id"
                                         title="Supplier Contact Name"/>
                            <InfoElement :info_content="infos_supplierContact[1].info_value" :info_id="infos_supplierContact[1].id"
                                         title="Supplier Contact Function"/>
                            <InfoElement :info_content="infos_supplierContact[2].info_value" :info_id="infos_supplierContact[2].id"
                                         title="Supplier Contact PhoneNumber"/>
                            <InfoElement :info_content="infos_supplierContact[3].info_value" :info_id="infos_supplierContact[3].id"
                                         title="Supplier Contact Email"/>
                            <InfoElement :info_content="infos_supplierContact[4].info_value" :info_id="infos_supplierContact[4].id"
                                         title="Supplier Contact principam"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingEight" class="accordion-header">
                        <button aria-controls="collapseEight" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEight" data-bs-toggle="collapse" type="button">
                            Incoming Inspection infos
                        </button>
                    </h2>
                    <div id="collapseEight" aria-labelledby="headingEight" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_incmgInsp[0].info_value" :info_id="infos_incmgInsp[0].id"
                                         title="Remarks"/>
                            <InfoElement :info_content="infos_incmgInsp[1].info_value" :info_id="infos_incmgInsp[1].id"
                                         title="Part Material Compliance Certificate"/>
                            <InfoElement :info_content="infos_incmgInsp[2].info_value" :info_id="infos_incmgInsp[2].id"
                                         title="Raw Material Compliance Certificate"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingNine" class="accordion-header">
                        <button aria-controls="collapseNine" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseNine" data-bs-toggle="collapse" type="button">
                            Aspect Test infos
                        </button>
                    </h2>
                    <div id="collapseNine" aria-labelledby="headingNine" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_aspTest[0].info_value" :info_id="infos_aspTest[0].id"
                                         title="Severity Level"/>
                            <InfoElement :info_content="infos_aspTest[1].info_value" :info_id="infos_aspTest[1].id"
                                         title="Level of Control"/>
                            <InfoElement :info_content="infos_aspTest[2].info_value" :info_id="infos_aspTest[2].id"
                                         title="Expected Aspect"/>
                            <InfoElement :info_content="infos_aspTest[3].info_value" :info_id="infos_aspTest[3].id"
                                         title="Name"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTen" class="accordion-header">
                        <button aria-controls="collapseTen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTen" data-bs-toggle="collapse" type="button">
                            Dimensional test infos
                        </button>
                    </h2>
                    <div id="collapseTen" aria-labelledby="headingTen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_dimTest[0].info_value" :info_id="infos_dimTest[0].id"
                                         title="Severity Level"/>
                            <InfoElement :info_content="infos_dimTest[1].info_value" :info_id="infos_dimTest[1].id"
                                         title="Level of Control"/>
                            <InfoElement :info_content="infos_dimTest[2].info_value" :info_id="infos_dimTest[2].id"
                                         title="Expected Method"/>
                            <InfoElement :info_content="infos_dimTest[3].info_value" :info_id="infos_dimTest[3].id"
                                         title="Expected Value"/>
                            <InfoElement :info_content="infos_dimTest[4].info_value" :info_id="infos_dimTest[4].id"
                                         title="Unit Value"/>
                            <InfoElement :info_content="infos_dimTest[5].info_value" :info_id="infos_dimTest[5].id"
                                         title="Name"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingEleven" class="accordion-header">
                        <button aria-controls="collapseEleven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEleven" data-bs-toggle="collapse" type="button">
                            Functionnal Test infos
                        </button>
                    </h2>
                    <div id="collapseEleven" aria-labelledby="headingEleven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_funcTest[0].info_value" :info_id="infos_funcTest[0].id"
                                         title="Severity Level"/>
                            <InfoElement :info_content="infos_funcTest[1].info_value" :info_id="infos_funcTest[1].id"
                                         title="Level of Control"/>
                            <InfoElement :info_content="infos_funcTest[2].info_value" :info_id="infos_funcTest[2].id"
                                         title="Expected Method"/>
                            <InfoElement :info_content="infos_funcTest[3].info_value" :info_id="infos_funcTest[3].id"
                                         title="Expected Value"/>
                            <InfoElement :info_content="infos_funcTest[4].info_value" :info_id="infos_funcTest[4].id"
                                         title="Unit Value"/>
                            <InfoElement :info_content="infos_funcTest[5].info_value" :info_id="infos_funcTest[5].id"
                                         title="Name"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwelve" class="accordion-header">
                        <button aria-controls="collapseTwelve" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwelve" data-bs-toggle="collapse" type="button">
                            Doc control infos
                        </button>
                    </h2>
                    <div id="collapseTwelve" aria-labelledby="headingTwelve" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_docControl[0].info_value" :info_id="infos_docControl[0].id"
                                         title="Reference"/>
                            <InfoElement :info_content="infos_docControl[1].info_value" :info_id="infos_docControl[1].id"
                                         title="Name"/>
                            <InfoElement :info_content="infos_docControl[2].info_value" :info_id="infos_docControl[2].id"
                                         title="Material certif spe"/>
                            <InfoElement :info_content="infos_docControl[3].info_value" :info_id="infos_docControl[3].id"
                                         title="FDS "/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingThirteen" class="accordion-header">
                        <button aria-controls="collapseThirteen" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseThirteen" data-bs-toggle="collapse"
                                type="button">
                            Complementary Test infos
                        </button>
                    </h2>
                    <div id="collapseThirteen" aria-labelledby="headingThirteen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <InfoElement :info_content="infos_compTest[0].info_value" :info_id="infos_compTest[0].id"
                                         title="Severity Level"/>
                            <InfoElement :info_content="infos_compTest[1].info_value" :info_id="infos_compTest[1].id"
                                         title="Level of Control"/>
                            <InfoElement :info_content="infos_compTest[2].info_value" :info_id="infos_compTest[2].id"
                                         title="Expected Method"/>
                            <InfoElement :info_content="infos_compTest[3].info_value" :info_id="infos_compTest[3].id"
                                         title="Expected Value"/>
                            <InfoElement :info_content="infos_compTest[4].info_value" :info_id="infos_compTest[4].id"
                                         title="Unit Value"/>
                            <InfoElement :info_content="infos_compTest[5].info_value" :info_id="infos_compTest[5].id"
                                         title="Name"/>
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
            infos_artFam: [],
            infos_artMember: [],
            infos_purSpe: [],
            infos_crit: [],
            infos_supplier: [],
            infos_supplierAdr: [],
            infos_supplierContact: [],
            infos_incmgInsp: [],
            infos_dimTest: [],
            infos_funcTest: [],
            infos_docControl: [],
            infos_compTest: [],
            infos_aspTest: [],
            loaded: false
        }
    },
    created() {
        if (this.$userId.user_updateInformationRight != true) {
            this.$router.replace({name: "home"})
        }
        axios.get('/info/send/articleFamily')
            .then(response => {
                this.infos_artFam = response.data;
            })
            .catch(error => {
            });

        axios.get(' /info/send/article_member')
            .then(response => {
                this.infos_artMember = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/purSpe')
            .then(response => {
                this.infos_purSpe = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/crit')
            .then(response => {
                this.infos_crit = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/supplier')
            .then(response => {
                this.infos_supplier = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/supplierAdr')
            .then(response => {
                this.infos_supplierAdr = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/supplierContact')
            .then(response => {
                this.infos_supplierContact = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/IncmgInsp')
            .then(response => {
                this.infos_incmgInsp = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/aspectTest')
            .then(response => {
                this.infos_aspTest = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/dimTest')
            .then(response => {
                this.infos_dimTest = response.data;
            })
            .catch(error => {
            });
        axios.get('/info/send/funcTest')
            .then(response => {
                this.infos_funcTest = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/docControl')
            .then(response => {
                this.infos_docControl = response.data;
            })
            .catch(error => {
            });

        axios.get('/info/send/compTest')
            .then(response => {
                this.infos_compTest = response.data;
                this.loaded = true;
            })
            .catch(error => {
            });
    }
}
</script>

<style>

</style>

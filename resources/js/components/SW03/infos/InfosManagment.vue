<!--File name : InfosManagement.vue-->
<!--Creation date : 15 May 2023-->
<!--Update date : 15 May 2023-->
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
                            Article Family infos
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <InfoElement title="Article Family Reference" :info_content="infos_artFam[0].info_value" :info_id="infos_artFam[0].id"/>
                            <InfoElement title="Article Family Designation" :info_content="infos_artFam[1].info_value" :info_id="infos_artFam[1].id"/>
                            <InfoElement title="Article Family Drawing Path" :info_content="infos_artFam[2].info_value" :info_id="infos_artFam[2].id"/>
                             <InfoElement title="Article Family Purchased By" :info_content="infos_artFam[3].info_value" :info_id="infos_artFam[3].id"/>
                            <InfoElement title="Article Family Variable Characteristic" :info_content="infos_artFam[4].info_value" :info_id="infos_artFam[4].id"/>
                            <InfoElement title="Article Family Version" :info_content="infos_artFam[5].info_value" :info_id="infos_artFam[5].id"/>
                            <InfoElement title="Article Family Generic Designation" :info_content="infos_artFam[6].info_value" :info_id="infos_artFam[6].id"/>
                            <InfoElement title="Article Family Generic Reference" :info_content="infos_artFam[7].info_value" :info_id="infos_artFam[7].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Article Family Member infos
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <InfoElement title="Dimension" :info_content="infos_artMember[0].info_value" :info_id="infos_artMember[0].id"/>
                            <InfoElement title="Same values" :info_content="infos_artMember[1].info_value" :info_id="infos_artMember[1].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Article Purchase Specifications infos
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <InfoElement title="Required doc" :info_content="infos_purSpe[0].info_value" :info_id="infos_purSpe[0].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Article Criticality infos
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <InfoElement title="Article Criticality" :info_content="infos_crit[0].info_value" :info_id="infos_crit[0].id"/>
                            <InfoElement title="Article material Contact Criticality" :info_content="infos_crit[1].info_value" :info_id="infos_crit[1].id"/>
                            <InfoElement title="Article Material Function Criticality doc" :info_content="infos_crit[2].info_value" :info_id="infos_crit[2].id"/>
                            <InfoElement title="Article Process Criticality doc" :info_content="infos_crit[3].info_value" :info_id="infos_crit[3].id"/>
                            <InfoElement title="Remarks" :info_content="infos_crit[4].info_value" :info_id="infos_crit[4].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Supplier infos
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <InfoElement title="Supplier Name" :info_content="infos_supplier[0].info_value" :info_id="infos_supplier[0].id"/>
                            <InfoElement title="Supplier Reception Number" :info_content="infos_supplier[1].info_value" :info_id="infos_supplier[1].id"/>
                            <InfoElement title="Supplier Alpha form Id" :info_content="infos_supplier[2].info_value" :info_id="infos_supplier[2].id"/>
                            <InfoElement title="Supplier agreement number" :info_content="infos_supplier[3].info_value" :info_id="infos_supplier[3].id"/>
                            <InfoElement title="Supplier quality certificate number" :info_content="infos_supplier[4].info_value" :info_id="infos_supplier[4].id"/>
                            <InfoElement title="Supplier specifics instructions" :info_content="infos_supplier[5].info_value" :info_id="infos_supplier[5].id"/>
                            <InfoElement title="Supplier version" :info_content="infos_supplier[6].info_value" :info_id="infos_supplier[6].id"/>
                            <InfoElement title="Supplier siret" :info_content="infos_supplier[7].info_value" :info_id="infos_supplier[7].id"/>
                            <InfoElement title="Supplier website" :info_content="infos_supplier[8].info_value" :info_id="infos_supplier[8].id"/>
                            <InfoElement title="Supplier activity" :info_content="infos_supplier[9].info_value" :info_id="infos_supplier[9].id"/>
                            <InfoElement title="Supplier real" :info_content="infos_supplier[10].info_value" :info_id="infos_supplier[10].id"/>
                            <InfoElement title="Supplier VAT number" :info_content="infos_supplier[11].info_value" :info_id="infos_supplier[11].id"/>
                            <InfoElement title="Supplier critical" :info_content="infos_supplier[12].info_value" :info_id="infos_supplier[12].id"/>
                            <InfoElement title="Supplier end link folder" :info_content="infos_supplier[13].info_value" :info_id="infos_supplier[13].id"/>
                            <InfoElement title="Supplier end active" :info_content="infos_supplier[14].info_value" :info_id="infos_supplier[14].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Supplier Address infos
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">
                            <InfoElement title="Supplier Address Name" :info_content="infos_supplierAdr[0].info_value" :info_id="infos_supplierAdr[0].id"/>
                            <InfoElement title="Supplier Address Street" :info_content="infos_supplierAdr[1].info_value" :info_id="infos_supplierAdr[1].id"/>
                            <InfoElement title="Supplier Address Town" :info_content="infos_supplierAdr[2].info_value" :info_id="infos_supplierAdr[2].id"/>
                            <InfoElement title="Supplier Address Country" :info_content="infos_supplierAdr[3].info_value" :info_id="infos_supplierAdr[3].id"/>
                            <InfoElement title="Supplier Address PostalCode" :info_content="infos_supplierAdr[4].info_value" :info_id="infos_supplierAdr[4].id"/>
                            <InfoElement title="Supplier Address principal" :info_content="infos_supplierAdr[5].info_value" :info_id="infos_supplierAdr[5].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        Supplier Contact infos
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                        <div class="accordion-body">
                            <InfoElement title="Supplier Contact Name" :info_content="infos_supplierContact[0].info_value" :info_id="infos_supplierContact[0].id"/>
                            <InfoElement title="Supplier Contact Function" :info_content="infos_supplierContact[1].info_value" :info_id="infos_supplierContact[1].id"/>
                            <InfoElement title="Supplier Contact PhoneNumber" :info_content="infos_supplierContact[2].info_value" :info_id="infos_supplierContact[2].id"/>
                            <InfoElement title="Supplier Contact Email" :info_content="infos_supplierContact[3].info_value" :info_id="infos_supplierContact[3].id"/>
                            <InfoElement title="Supplier Contact principam" :info_content="infos_supplierContact[4].info_value" :info_id="infos_supplierContact[4].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        Incoming Inspection infos
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                        <div class="accordion-body">
                            <InfoElement title="Remarks" :info_content="infos_incmgInsp[0].info_value" :info_id="infos_incmgInsp[0].id"/>
                            <InfoElement title="Part Material Compliance Certificate" :info_content="infos_incmgInsp[1].info_value" :info_id="infos_incmgInsp[1].id"/>
                            <InfoElement title="Raw Material Compliance Certificate" :info_content="infos_incmgInsp[2].info_value" :info_id="infos_incmgInsp[2].id"/>
                        </div>
                    </div>
                </div>
                 <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                        Aspect Test infos
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <InfoElement title="Severity Level" :info_content="infos_aspTest[0].info_value" :info_id="infos_aspTest[0].id"/>
                            <InfoElement title="Level of Control" :info_content="infos_aspTest[1].info_value" :info_id="infos_aspTest[1].id"/>
                            <InfoElement title="Expected Aspect" :info_content="infos_aspTest[2].info_value" :info_id="infos_aspTest[2].id"/>
                            <InfoElement title="Name" :info_content="infos_aspTest[3].info_value" :info_id="infos_aspTest[3].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                        Dimensional test infos
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                        <div class="accordion-body">
                           <InfoElement title="Severity Level" :info_content="infos_dimTest[0].info_value" :info_id="infos_dimTest[0].id"/>
                            <InfoElement title="Level of Control" :info_content="infos_dimTest[1].info_value" :info_id="infos_dimTest[1].id"/>
                            <InfoElement title="Expected Method" :info_content="infos_dimTest[2].info_value" :info_id="infos_dimTest[2].id"/>
                            <InfoElement title="Expected Value" :info_content="infos_dimTest[3].info_value" :info_id="infos_dimTest[3].id"/>
                            <InfoElement title="Unit Value" :info_content="infos_dimTest[4].info_value" :info_id="infos_dimTest[4].id"/>
                            <InfoElement title="Name" :info_content="infos_dimTest[5].info_value" :info_id="infos_dimTest[5].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEleven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                        Functionnal Test infos
                        </button>
                    </h2>
                    <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven">
                        <div class="accordion-body">
                             <InfoElement title="Severity Level" :info_content="infos_funcTest[0].info_value" :info_id="infos_funcTest[0].id"/>
                            <InfoElement title="Level of Control" :info_content="infos_funcTest[1].info_value" :info_id="infos_funcTest[1].id"/>
                            <InfoElement title="Expected Method" :info_content="infos_funcTest[2].info_value" :info_id="infos_funcTest[2].id"/>
                            <InfoElement title="Expected Value" :info_content="infos_funcTest[3].info_value" :info_id="infos_funcTest[3].id"/>
                            <InfoElement title="Unit Value" :info_content="infos_funcTest[4].info_value" :info_id="infos_funcTest[4].id"/>
                            <InfoElement title="Name" :info_content="infos_funcTest[5].info_value" :info_id="infos_funcTest[5].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwelve">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                            Doc control infos
                        </button>
                    </h2>
                    <div id="collapseTwelve" class="accordion-collapse collapse" aria-labelledby="headingTwelve">
                        <div class="accordion-body">
                             <InfoElement title="Reference" :info_content="infos_docControl[0].info_value" :info_id="infos_docControl[0].id"/>
                            <InfoElement title="Name" :info_content="infos_docControl[1].info_value" :info_id="infos_docControl[1].id"/>
                            <InfoElement title="Material certif spe" :info_content="infos_docControl[2].info_value" :info_id="infos_docControl[2].id"/>
                            <InfoElement title="FDS " :info_content="infos_docControl[3].info_value" :info_id="infos_docControl[3].id"/>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThirteen">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirteen" aria-expanded="true" aria-controls="collapseThirteen">
                            Complementary Test infos
                        </button>
                    </h2>
                    <div id="collapseThirteen" class="accordion-collapse collapse show" aria-labelledby="headingThirteen">
                        <div class="accordion-body">
                             <InfoElement title="Severity Level" :info_content="infos_compTest[0].info_value" :info_id="infos_compTest[0].id"/>
                            <InfoElement title="Level of Control" :info_content="infos_compTest[1].info_value" :info_id="infos_compTest[1].id"/>
                            <InfoElement title="Expected Method" :info_content="infos_compTest[2].info_value" :info_id="infos_compTest[2].id"/>
                            <InfoElement title="Expected Value" :info_content="infos_compTest[3].info_value" :info_id="infos_compTest[3].id"/>
                            <InfoElement title="Unit Value" :info_content="infos_compTest[4].info_value" :info_id="infos_compTest[4].id"/>
                            <InfoElement title="Name" :info_content="infos_compTest[5].info_value" :info_id="infos_compTest[5].id"/>
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
            infos_artFam:[],
            infos_artMember:[],
            infos_purSpe:[],
            infos_crit:[],
            infos_supplier:[],
            infos_supplierAdr:[],
            infos_supplierContact:[],
            infos_incmgInsp:[],
            infos_dimTest:[],
            infos_funcTest:[],
            infos_docControl:[],
            infos_compTest:[],
            infos_aspTest:[],
            loaded:false
        }
    },
    created(){
            if(this.$userId.user_updateInformationRight!=true){
                this.$router.replace({ name: "home" })
            }
            axios.get('/info/send/articleFamily')
            .then (response=> {
                this.infos_artFam=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get(' /info/send/article_member')
            .then (response=> {
                this.infos_artMember=response.data;
                })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/purSpe')
            .then (response=> {
                this.infos_purSpe=response.data;

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/crit')
            .then (response=> {
                this.infos_crit=response.data;

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/supplier')
            .then (response=> {
                this.infos_supplier=response.data;
                
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/supplierAdr')
            .then (response=> {
                this.infos_supplierAdr=response.data;
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/supplierContact')
            .then (response=> {
                this.infos_supplierContact=response.data;
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/IncmgInsp')
            .then (response=> {
                this.infos_incmgInsp=response.data;

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/aspectTest')
            .then (response=> {
                this.infos_aspTest=response.data;
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/dimTest')
            .then (response=> {
                this.infos_dimTest=response.data;
                

            })
            .catch(error => console.log(error)) ;


            axios.get('/info/send/funcTest')
            .then (response=> {
                this.infos_funcTest=response.data;
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/docControl')
            .then (response=> {
                this.infos_docControl=response.data;
                

            })
            .catch(error => console.log(error)) ;

            axios.get('/info/send/compTest')
            .then (response=> {
                this.infos_compTest=response.data;
                this.loaded=true;
                

            })
            .catch(error => console.log(error)) ;
            
            
            
            
        

    }
}
</script>

<style>

</style>

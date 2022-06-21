<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="equipment_update">
            <h1>Equipment Update</h1>
             <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Equipment Id Card
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <EquipmentIDForm :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                            :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                            :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                            :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"
                            modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Equipment Dimension
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceADim  :importedDim="eq_dimensions" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Equipment Power
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <ReferenceAPow  :importedPow="eq_powers" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Equipment Special Process
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <ReferenceASpecProc  :importedSpProc="eq_spProc" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Equipment Usage
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <ReferenceAUsage  :importedUsg="eq_usg" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Equipment File
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">
                            <ReferenceAFile  :importedFile="eq_file" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        Equipment Preventive maintenace Operation
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOp  :importedPrvMtnOp="eq_prvMtnOp" modifMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                        Equipment Risk
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <ReferenceARisk :importedRisk="eq_risk" :riskForEq="true" modifMod/>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
</template>

<script>
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ReferenceADim from '../referencing/ReferenceADim.vue'
import ReferenceAPow from '../referencing/ReferenceAPow.vue'
import ReferenceASpecProc from '../referencing/ReferenceASpecProc.vue'
import ReferenceAUsage from '../referencing/ReferenceAUsage.vue'
import ReferenceAFile from '../referencing/ReferenceAFile.vue'
import ReferenceAPrvMtnOp from '../referencing/ReferenceAPrvMtnOp.vue'
import ReferenceARisk from '../referencing/ReferenceARisk.vue'






export default {
    components: {
        EquipmentIDForm,
        ReferenceADim,
        ReferenceAPow,
        ReferenceASpecProc,
        ReferenceAUsage,
        ReferenceAFile,
        ReferenceAPrvMtnOp,
        ReferenceARisk
    },
    data(){
        return{
            eq_id:this.$route.params.id,
            eq_idCard:null,
            eq_dimensions:null,
            eq_powers:null,
            eq_spProc:null,
            eq_usg:null,
            eq_file:null,
            eq_prvMtnOp:null,
            eq_risk:null,
            loaded:false,
        }
    },

    created(){

        var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response =>{
                this.eq_idCard=response.data
                this.$router.push({ name: "url_eq_update", params: {id:this.eq_id}, query: {signed:response.data.eq_lifeSheetCreated }}).catch(()=>{});
                if(response.data.eq_lifeSheetCreated==true && 
                 (this.$userId.user_updateDescriptiveLifeSheetDataSignedRight!=true &&
                  this.$userId.user_deleteDataSignedLinkedToEqOrMmeRight!=true) ){

                    this.eq_lifeSheetCreated=response.data.eq_lifeSheetCreated;
                    this.$router.push({ name: "home"})
                    console.log("coco")
                }
            })
            .catch(error => console.log(error));

        var consultUrlDim = (id) => `/dimension/send/${id}`;
        axios.get(consultUrlDim(this.eq_id))
            .then (response=> this.eq_dimensions=response.data)
            .catch(error => console.log(error)) ;
        
        var consultUrlPow = (id) => `/power/send/${id}`;
        axios.get(consultUrlPow(this.eq_id))
            .then (response=> this.eq_powers=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlSpProc = (id) => `/spProc/send/${id}`;
        axios.get(consultUrlSpProc(this.eq_id))
            .then (response=>{
                if(response.data==""){
                    this.eq_spProc=[];
                }else{
                    this.eq_spProc=response.data;
                }
            })
            .catch(error => console.log(error)) ;
        
        var consultUrlUsg = (id) => `/usage/send/${id}`;
        axios.get(consultUrlUsg(this.eq_id))
            .then (response=>this.eq_usg=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlFile = (id) => `/file/send/${id}`;
        axios.get(consultUrlFile(this.eq_id))
            .then (response=>this.eq_file=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
        axios.get(consultUrlPrvMtnOp(this.eq_id))
            .then (response=>this.eq_prvMtnOp=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
        axios.get(consultUrlRisk(this.eq_id))
            .then (response=>{
                    this.eq_risk=response.data
                    this.loaded=true;
                })
            .catch(error => console.log(error)) ;
        }
        
        
}


</script>

<style lang="scss">
    .equipment_update{
            .green_card{
                background-color: #b0f2b6;
            }
            .yellow_card{
                background-color:lightyellow;
            }
            h1{
                text-align: center;
            }
    }

</style>

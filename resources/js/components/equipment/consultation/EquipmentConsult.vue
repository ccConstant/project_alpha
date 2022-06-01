<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="equipment_consultation">
            <EquipmentIDForm :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"
                consultMod/>
            <ReferenceADim v-if="eq_dimensions.length>0" :importedDim="eq_dimensions" consultMod/>
            <ReferenceAPow v-if="eq_powers.length>0" :importedPow="eq_powers" consultMod/>
            <ReferenceASpecProc v-if="eq_spProc.length>0" :importedSpProc="eq_spProc" consultMod/>
            <ReferenceAUsage v-if="eq_usg.length>0" :importedUsg="eq_usg" consultMod/>
            <ReferenceAFile v-if="eq_file.length>0" :importedFile="eq_file" consultMod/>
            <ReferenceAPrvMtnOp v-if="eq_prvMtnOp.length>0" :importedPrvMtnOp="eq_prvMtnOp" consultMod/>
            <ReferenceARisk v-if="eq_risk.length>0" :importedRisk="eq_risk" :riskForEq="true" consultMod/>
            <div v-if="validationMethod=='technical'">
                <button type="button" class="btn btn-primary technical_validate_button">Technical Validate </button>
            </div>
            <div v-else-if="validationMethod=='quality'">
                <button type="button" class="btn btn-primary quality_validate_button" >Quality Validate</button>
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
            validationMethod:this.$route.query.method
        }
    },

    created(){

        var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response => this.eq_idCard=response.data)
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
    .technical_validate_button {
        display: block;
        margin : auto;
        margin-bottom: 15px;
        
    };

    .quality_validate_button{
        display: block;
        margin : auto;
        margin-bottom: 15px;
    }
</style>

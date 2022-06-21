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
            <ReferenceAUsage v-if="eq_usg.length>0" :importedUsg="eq_usg" consultMod :reformMod="true"/>
            <ReferenceAPrvMtnOp v-if="eq_prvMtnOp.length>0" :importedPrvMtnOp="eq_prvMtnOp" consultMod :reformMod="true"/>

            
            


        </div>
    </div>
</template>

<script>
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ReferenceAUsage from '../referencing/ReferenceAUsage.vue'
import ReferenceAPrvMtnOp from '../referencing/ReferenceAPrvMtnOp.vue'
import ValidationButton from '../../button/ValidationButton.vue'
import moment from 'moment'






export default {
    components: {
        EquipmentIDForm,
        ReferenceAUsage,
        ReferenceAPrvMtnOp,
        ValidationButton
    },
    data(){
        return{
            eq_id:this.$route.params.id.toString(),
            eq_idCard:null,
            eq_usg:null,
            eq_prvMtnOp:null,
            loaded:false,
            errors:{}
        }
    },

    created(){
        if( this.$userId.user_makeReformRight!=true){
            this.$router.replace({ name: "home" })
        }
        var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response => this.eq_idCard=response.data)
            .catch(error => console.log(error));

        var consultUrlUsg = (id) => `/usage/send/${id}`;
        axios.get(consultUrlUsg(this.eq_id))
            .then (response=>this.eq_usg=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
        axios.get(consultUrlPrvMtnOp(this.eq_id))
            .then (response=>{
                this.eq_prvMtnOp=response.data
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
        
    }  
}


</script>

<style lang="scss">
</style>

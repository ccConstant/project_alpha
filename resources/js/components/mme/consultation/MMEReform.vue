<template>
    <div>       
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="mme_consultation">
            <MmeIdForm :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
            :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
            :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
            consultMod/>
            <ReferenceAMMEVerif v-if="mme_verifs.length>0" :importedVerif="mme_verifs" consultMod :reformMod="true"/>  
            <ReferenceAMMEUsage v-if="mme_usages.length>0" :importedUsage="mme_usages" consultMod :reformMod="true"/>  

        </div>                     
                        
                        
                        
                        
                        

                        
    </div>
</template>

<script>
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from '../referencing/ReferenceAMMEUsage.vue'


export default {
    components:{
        MmeIdForm,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage
    },
    data(){
        return{
            mme_id:this.$route.params.id.toString(),
            mme_idCard:null,
            mme_verifs:null,
            mme_usages:null,
            loaded:false,
            errors:{}
        }
    },
    created(){
        if( this.$userId.user_makeReformRight!=true){
            this.$router.replace({ name: "home" })
        }
        var consultUrl = (id) => `/mme/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then (response => {
                this.mme_idCard=response.data;
            })
            .catch(error => console.log(error));

        var consultUrl = (id) => `/verifs/send/${id}`;
            axios.get(consultUrl(this.mme_id))
                .then (response=> {
                    this.mme_verifs=response.data
                })
                .catch(error => console.log(error)) ;
    
        var consultUrl = (id) => `/mme_usage/send/${id}`;
            axios.get(consultUrl(this.mme_id))
            .then (response=> {
                this.mme_usages=response.data
                this.loaded=true
            })
            .catch(error => console.log(error)) ;


        
    }  

}
</script>

<style>

</style>
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
        </div>                     
                        
                        
                        
                        
                        

                        
    </div>
</template>

<script>
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'

export default {
    components:{
        MmeIdForm,
        ReferenceAMMEVerif
    },
    data(){
        return{
            mme_id:this.$route.params.id.toString(),
            mme_idCard:null,
            mme_verifs:null,
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
                    this.loaded=true
                })
                .catch(error => console.log(error)) ;

        
    }  

}
</script>

<style>

</style>
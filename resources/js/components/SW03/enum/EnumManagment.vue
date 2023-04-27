<!--File name : EnumManagement.vue-->
<!--Creation date : 26 Apr 2023-->
<!--Update date : 27 Apr 2023-->
<!--Vue Component to show the different categories of enumerates-->

<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div class="enumManagment" v-if="loaded==true">
            <h1>ENUM MANAGEMENT</h1>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Article Family Purchased By
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_purchased_by' :enumList="enum_purchased_by" title="Article Fam Purchased By" url="/artFam/enum/purchasedBy/" info_text=" info of enum Article fam purchased by"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Article Family Storage Conditions
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_storageConditions' :enumList="enum_storageConditions" title="Article Fam Storage Conditions " url="#" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EnumElement from './EnumElement.vue'
export default {
    components:{
        EnumElement
    },
    data(){
        return{
            enum_purchased_by:[],
            enum_storageConditions:[],
            loaded:false

        }
    },
    created(){
        if( this.$userId.user_addEnumRight!=true
        &&  this.$userId.user_deleteEnumRight!=true
        &&  this.$userId.user_updateEnumRight!=true ){
                this.$router.replace({ name: "home" })
        }
        /*Ask for the controller the different equipment type option */
        axios.get('/artFam/enum/purchasedBy')
            .then (response=> {
                this.enum_purchased_by=response.data,
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>

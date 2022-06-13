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
                            Equipment ID card information
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <InfoElement title="internal Reference" :info_content="infos_idCard[0].info_value" :info_id="infos_idCard[0].id"/>
                          

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
            loaded:false
        }
    },
    created(){
            /*axios.get('/info/send/all')
            .then (response=> {
                this.infos=response.data;
                console.log("ALL :")
                console.log(this.infos)
                }) 
            .catch(error => console.log(error)) ;*/

            axios.get('/info/send/eqIdCard')
            .then (response=> {
                this.infos_idCard=response.data;
                console.log("\n Infos ID CARD");
                console.log(this.infos_idCard);
                this.loaded=true;
                
                }) 
            .catch(error => console.log(error)) ;

            /*axios.get('/info/send/dimension')
            .then (response=> {
                console.log("\n Infos dimension")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/power')
            .then (response=> {
                console.log("\n Infos power")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/specialProcess')
            .then (response=> {
                console.log("\n Infos specialProcess")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/usage')
            .then (response=> {
                console.log("\n Infos dimension")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/file')
            .then (response=> {
                console.log("\n Infos dimension")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;


            axios.get('/info/send/preventiveMaintenanceOperation')
            .then (response=> {
                console.log("\n Infos preventiveMaintenanceOperation")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/risk')
            .then (response=> {
                console.log("\n Infos risk")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/state')
            .then (response=> {
                console.log("\n Infos state")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/preventiveMaintenanceOperationRealized')
            .then (response=> {
                console.log("\n Infos preventiveMaintenanceOperationRealized")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/curativeMaintenanceOperation')
            .then (response=> {
                console.log("\n Infos curativeMaintenanceOperation")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/enum')
            .then (response=> {
                console.log("\n Infos enum")
                console.log(response.data)
                this.loaded=true;
                }) 
            .catch(error => console.log(error)) ;*/
    }
}
</script>

<style>

</style>
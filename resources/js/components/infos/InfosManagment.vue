<template>
    <div class="infoManagment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <InfoElement :info_prop="{eq_internalReference:'zozoz',id:1}"/>
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
            loaded:true
        }
    },
    created(){
            axios.get('/info/send/all')
            .then (response=> {
                this.infos=response.data;
                console.log("ALL :")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/eqIdCard')
            .then (response=> {
                this.infos_idCard=response.data;
                console.log("\n Infos ID CARD")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/dimension')
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
                console.log("\n Infos usage")
                console.log(response.data)
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/file')
            .then (response=> {
                console.log("\n Infos file")
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
                }) 
            .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>
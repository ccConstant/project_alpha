<template>
    <div class="infoManagment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
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
            loaded:true
        }
    },
    created(){
            axios.get('/info/send/all')
            .then (response=> console.log(response.data) )
            .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>
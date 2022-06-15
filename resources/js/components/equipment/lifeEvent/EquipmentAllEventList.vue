<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" >
            <h2>Liste des evenement de vie</h2>
            <ul>
                <li class="list-group-item" v-for="(list,index) in states " :key="index" >
                    Start date : {{list.state_startDate}}
                    End date : {{list.state_endDate}}
                    State : {{list.state_name}}
                    <router-link :to="{name:'url_life_event_all_consult',params:{id: eq_id,state_id:list.id} }">Consult</router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
export default {
    data(){
        return{
            eq_id:this.$route.params.id,
            states:null,
            loaded:false
        }
    },
    created(){
        var UrlState = (id)=> `/states/send/${id}`;
        axios.get(UrlState(this.eq_id))
            .then (response=>{
                console.log(response.data)
                this.states=response.data
                for (var i=0;i<this.states.length;i++) {
                    this.states[i].state_startDate=moment(this.states[i].state_startDate).format('D MMM YYYY');
                    if(this.states[i].state_endDate===null){
                        this.states[i].state_endDate="-"
                    }else{
                        this.states[i].state_endDate=moment(this.states[i].state_endDate).format('D MMM YYYY'); 
                    }
                    
                }
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    }

}
</script>


<style>

</style>
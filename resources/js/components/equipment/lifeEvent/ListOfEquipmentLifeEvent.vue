<template>
  <div class="listOfEquipment">
      <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
      </div>
      <div v-if="loaded==true" >
        <h2>Liste des equipment</h2>
      <input v-model="searchTerm" type="text">
      <ErrorAlert ref="errorAlert"/>
      <ul>
        <li class="list-group-item" v-for="(list,index) in filterByTerm " :key="index" >
          {{list.eq_internalReference}} Current state : {{list.eq_state}}
          <router-link :to="{name:'url_life_event_update_state',params:{id: list.id,state_id:list.state_id} }">Update the state</router-link>
          <!--<router-link :to="{name:'url_life_event_change_state',params:{id: list.id} }">Change the state</router-link>-->
          <a href="#" @click="verifBeforeAddState(list.id,list.state_id)">Change the state</a>
          <router-link :to="{name:'url_life_event_all',params:{id: list.id} }">All Event</router-link>
          <a href="#" @click="verifBeforeAddOpe(list.id,list.state_id)">Reference a maintenance operation</a>
          <!--<router-link :to="{name:'url_life_event_reference',params:{id: list.id,state_id:list.state_id} }">Reference a maintenance operation</router-link>-->
          <router-link :to="{name:'url_life_event_update',params:{id: list.id,state_id:list.state_id} }">Update maintenance operation</router-link>
         



        </li>
        
      </ul>
      </div>

  </div>

</template>

<script>
import ErrorAlert from '../../alert/ErrorAlert.vue'
export default {
  components:{
        ErrorAlert

  },
  data(){
    return{
      equipments:[],
      searchTerm: "",
      loaded:false,
      currentState:'' 
    }
  },
  methods:{
    verifBeforeAddState(eq_id,state_id){        
        var consultUrl = (id) => `/state/verif/beforeChangingState/${id}`;
        axios.post(consultUrl(state_id),{
        })
        .then(response =>{
            this.$router.replace({ name: "url_life_event_change_state", params: {id:eq_id}, query: {currentState: state_id } })
        ;})
        //If the controller sends errors we put it in the errors object 
        .catch(error => {
          console.log(error.response.data.errors)
          this.$refs.errorAlert.showAlert(error.response.data.errors.state_verif);
        }) ;
    },
    verifBeforeAddOpe(eq_id_to_send,state_id){
        console.log('state:',state_id)
        
        var consultUrl = (state_id) => `/state/verif/beforeReferenceOp/${state_id}`;
        axios.post(consultUrl(state_id),{
          eq_id:eq_id_to_send
        })
        .then(response =>{
            this.$router.replace({ name: "url_life_event_reference", params: {id:eq_id_to_send,state_id:state_id }})
        ;})
        //If the controller sends errors we put it in the errors object 
        .catch(error => {
          this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
        });
    }


  },
  computed: {
      filterByTerm() {
          return this.equipments.filter(option => {
              return option.eq_internalReference.toLowerCase().includes(this.searchTerm);
          });
      }
  },
  

  created(){
      axios.get('/equipment/equipments')
          .then (response=>{
            this.equipments=response.data;
            this.loaded=true;
          })
          .catch(error => console.log(error));
      
  },
  
  


}
</script>

<style>

</style>

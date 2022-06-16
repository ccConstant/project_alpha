<template>
  <div class="listOfEquipmentLifeEvent">
      <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
      </div>
      <div v-if="loaded==true" >
        <h1>Equipment Life Record</h1>
        <input placeholder="Search an equipment by his Alpha Reference" v-model="searchTerm" class="form-control w-50 search_bar" type="text">
        <ErrorAlert ref="errorAlert"/>
        <ul>
          <div class="one_element_list" v-for="(list,index) in pageOfItems " :key="index">
            <li class="list-group-item" :class="'element'+index%2">
              <div class="eq_list_internalReference_state">
                  <b>{{list.eq_internalReference}}</b>
              </div>
              <div class="eq_list_current_state">
                Current state : {{list.eq_state}}
              </div>
              <div class="eq_list_option_state">
                <router-link :to="{name:'url_life_event_update_state',params:{id: list.id,state_id:list.state_id} }">Update the state</router-link>
                <a href="#" @click="verifBeforeAddState(list.id,list.state_id)">Change the state</a>
                <router-link :to="{name:'url_life_event_all',params:{id: list.id},query:{internalReference:list.eq_internalReference} }">All Event</router-link>
                <a href="#" @click="verifBeforeAddOpe(list.id,list.state_id)">Record a maintenance operation</a>
                <router-link :to="{name:'url_life_event_update',params:{id: list.id,state_id:list.state_id} }">Update maintenance record</router-link>

              </div>
            </li>
          </div>
        </ul>
        <jw-pagination :pageSize=10 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>
      </div>


  </div>

</template>

<script>
import InputTextForm from '../../input/InputTextForm.vue'
import ErrorAlert from '../../alert/ErrorAlert.vue'
export default {
  components:{
        ErrorAlert,
  },
  data(){
    return{
      equipments:[],
      searchTerm: "",
      loaded:false,
      currentState:'',
      pageOfItems: []
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
            this.$router.replace({ name: "url_life_event_reference", params: {id:eq_id_to_send,state_id:state_id }, query: {type:"curative"}})
        ;})
        //If the controller sends errors we put it in the errors object 
        .catch(error => {
          this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
        });
    },
    onChangePage(pageOfItems) {
            console.log(pageOfItems)
            // update page of items
            this.pageOfItems = pageOfItems;
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

<style lang="scss">
  .listOfEquipmentLifeEvent{
    .element0{
      background-color: #ccc;
    }
    h1{
        text-align:center;
    }
    .search_bar{
      margin-left:30px;
      margin-bottom: 20px;
    }
  }
  .eq_list_internalReference_state{
    display: inline-block;
  }
  .eq_list_current_state{
    display: block;
    margin-left: 200px;
    margin-top: -20px;
  }
  .eq_list_option_state{
      display: block;
      margin-left: 460px;
      margin-top: -22.5px;
  }

</style>

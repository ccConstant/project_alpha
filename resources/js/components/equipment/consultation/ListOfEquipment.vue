<template>
  <div class="listOfEquipment">
      <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
      </div>
      <div v-if="loaded==true" >
        <h2>Liste des equipment</h2>
      <input v-model="searchTerm" type="text">
      <ul>
        <li class="list-group-item" v-for="(list,index) in filterByTerm " :key="index" >
          {{list.eq_internalReference}}
          <router-link :to="{name:'url_eq_consult',params:{id: list.id} }">Consult</router-link>
          <router-link :to="{name:'url_eq_update',params:{id: list.id} }">Update</router-link>
          <a href="#" @click="technicalValidation(list.id)">Technical validation</a>
          <a href="#" @click="qualityValidation(list.id)">Quality validation</a>

        </li>
      </ul>
      </div>

  </div>

</template>

<script>
export default {
  data(){
    return{
      equipments:[],
      searchTerm: "",
      loaded:false 
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
  methods:{
    technicalValidation(id){
      this.$router.replace({ name: "url_eq_consult", params: {id}, query: {method:"technical" } })
    },
    qualityValidation(id){
      this.$router.replace({ name: "url_eq_consult", params: {id}, query: {method:"quality" } })
    }
  }
  
  


}
</script>

<style>

</style>

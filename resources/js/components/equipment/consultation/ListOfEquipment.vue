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

            <a href="#" @click="warningUpdate(list.id,list.alreadyValidatedTechnical,list.alreadyValidatedQuality)">Update</a>
            <a v-if="list.alreadyValidatedTechnical===false" href="#" @click="technicalValidation(list.id)">Technical validation</a>
            <a v-if="list.alreadyValidatedQuality===false" href="#" @click="qualityValidation(list.id)">Quality validation</a>
            <a v-if="list.alreadyValidatedTechnical===true && list.alreadyValidatedQuality===true">Statut : Validated</a>
            <a @click="reformEquipment(list.id)" href="#">Reform</a>
            <a href="#" @click="warningDelete(list.id,list.eq_internalReference)">Delete</a>
            <router-link  :to="{name:'url_lifesheet_pdf',params:{id: list.id} }">Generate PDF</router-link>
          </li>
        </ul>
      </div>
      <b-modal :id="`modal-updateWarning-${_uid}`"  @ok="warningUpdate(eq_id,technical,quality,true)">
          <p class="my-4">Your equipment has a validated life sheet, If you update your equipment you will have to 
            revalidate </p>
      </b-modal>

      <b-modal :id="`modal-deleteWarning-${_uid}`" @ok="deleteEquipment(modal_eq_id)"  @hidden="resetModal" >
          <p class="my-4">Are you sur you want to delete {{modal_eq_internalReference}} </p>
      </b-modal>
  </div>

</template>

<script>
export default {
  data(){
    return{
      equipments:[],
      searchTerm: "",
      loaded:false,
      eq_id:null,
      technical:null,
      quality:null,

      modal_eq_internalReference:'',
      modal_eq_id:null

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
          console.log(response.data)
          this.equipments=response.data;
          this.loaded=true;
        })
        .catch(error => console.log(error));

    
  },
  methods:{
    technicalValidation(id){
      this.$router.replace({ name: "url_eq_consult", params: {id:id}, query: {method:"technical" } })
    },
    qualityValidation(id){
      this.$router.replace({ name: "url_eq_consult", params: {id:id}, query: {method:"quality" } })
    },
    reformEquipment(id){
      this.$router.replace({ name: "url_eq_reform", params: {id:id}})
    },
    warningUpdate(id,technical,quality,redirect){
      this.eq_id=id;
      this.technical=technical;
      this.quality=quality;
      if(technical==true && quality ==true){
        this.$bvModal.show(`modal-updateWarning-${this._uid}`)
      }else{
        this.$router.replace({ name: "url_eq_update", params: {id}})
      }
      if(redirect==true){
        this.$router.replace({ name: "url_eq_update", params: {id}})
      } 
    },
    warningDelete(id,refernece){
      this.modal_eq_id=id;
      this.modal_eq_internalReference=refernece;
      this.$bvModal.show(`modal-deleteWarning-${this._uid}`);
    },
    deleteEquipment(eq_id_to_send){
        console.log(eq_id_to_send);
        var consultUrl = (id) => `/equipment/delete/${id}`;
        axios.post(consultUrl(eq_id_to_send),{
        })
        .then(response =>{console.log("deleted")})
        //If the controller sends errors we put it in the errors object 
        .catch(error => {});
    },
    resetModal(){
      this.modal_eq_internalReference='';
      this.modal_eq_id=null
    }

  }
  
  


}
</script>

<style>

</style>

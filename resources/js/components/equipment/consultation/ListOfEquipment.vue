<template>
  <div class="listOfEquipment">
      <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
      </div>
      <div v-if="loaded==true" >
        <h2>Equipment List</h2>
        <input v-model="searchTerm" type="text">
        <ul>
          <div class="one_element_list" v-for="(list,index) in pageOfItems " :key="index">
            <li class="list-group-item" :class="'element'+index%2"  >
                <div class="eq_list_internalReference">
                  <b>{{list.eq_internalReference}}</b>
                </div>
                <div class="eq_list_option">
                  <router-link :to="{name:'url_eq_consult',params:{id: list.id} }">Consult</router-link>
                  <a href="#" @click="warningUpdate(list.id,list.alreadyValidatedTechnical,list.alreadyValidatedQuality)">Update</a>
                  <a v-if="list.alreadyValidatedTechnical===false" href="#" @click="technicalValidation(list.id)">Technical validation</a>
                  <a v-if="list.alreadyValidatedQuality===false" href="#" @click="qualityValidation(list.id)">Quality validation</a>
                  <a v-if="list.alreadyValidatedTechnical===true && list.alreadyValidatedQuality===true">Statut : Validated</a>
                  <a @click="reformEquipment(list.id)" href="#">Reform</a>
                  <router-link  :to="{name:'url_lifesheet_pdf',params:{id: list.id} }">Generate PDF</router-link>
                </div>
            </li>
          </div>
          <jw-pagination :pageSize=10 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>

        </ul>
      </div>
      <b-modal :id="`modal-updateWarning-${_uid}`"  @ok="warningUpdate(eq_id,technical,quality,true)">
          <p class="my-4">Your equipment has a validated life sheet, If you update your equipment you will have to 
            revalidate </p>
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
      pageOfItems: [],
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
    resetModal(){
      this.modal_eq_internalReference='';
      this.modal_eq_id=null
    },
    onChangePage(pageOfItems) {
            console.log(pageOfItems)
            // update page of items
            this.pageOfItems = pageOfItems;
    }

  }
  
  


}
</script>

<style lang="scss">
  .listOfEquipment{
    .element0{
      background-color: #ccc;
    }
  }
  .eq_list_internalReference{
    display: inline-block;
    
  }
  .eq_list_option{
    display: block;
    margin-left: 200px;
    margin-top: -20px;
  }

</style>

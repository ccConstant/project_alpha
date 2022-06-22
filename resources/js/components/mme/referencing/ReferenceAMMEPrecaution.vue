<template>
    <div class="MmePrctn">
        <div v-if="this.components.length>0">
            <h3 class="titleForm">Precaution</h3>
        </div>
        
        <!--Adding to the vue MMEPrecautionForm by going through the components array with the v-for-->
        <!--ref="ask_prctn_data" is used to call the child elements in this component-->
        <!--The emitted deletePrctn is catched here and call the function getContent -->
        <MMEPrecautionForm ref="ask_prctn_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :type="component.type" :description="component.description"
            :divClass="component.className" :id="component.id" :usg_id="data_usg_id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :mme_id="data_mme_id"
            @deletePrctn="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another precaution button appear -->
            <button v-on:click="addComponent">Add Precaution</button>
            <!--If prctns array is not empty and if the user is not in modifacion mode -->
            <div v-if="this.prctns!==null">
                <!--The importation button appear-->
                <button v-if="!modifMod " v-on:click="importPrctn">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>
  
</template>

<script>
/*Importation of the others Components who will be used here*/
import MMEPrecautionForm from './MMEPrecautionForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationAlert from '../../alert/ImportationAlert.vue'
export default {
    /*--------Declartion of the others Components:--------*/
    components:{
        MMEPrecautionForm,
        SaveButtonForm,
        ImportationAlert

    },
    props:{
        consultMod:{
            type:Boolean,
            default:false
        },
        modifMod:{
            type:Boolean,
            default:false
        },
        importedPrctn:{
            type:Array,
            default:null
        },
        mme_id:{
            type:Number
        },
        usg_id:{
            type:Number
        },
        import_id:{
            type:Number,
            default:null
        }
    },
    data() {
      return {
        components: [],
        uniqueKey:0,
        prctns:this.importedPrctn,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_mme_id:this.mme_id,
        data_usg_id:this.usg_id,
      };
    },
    methods:{
        //Function for adding a new empty precaution form 
        addComponent() {
            this.components.push({
                comp:'MMEPrecautionForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding imported precaution form with his data
        addImportedComponent(prctn_type,prctn_description,prctn_validate,prctn_className,id) {
            this.components.push({
                comp:'MMEPrecautionForm',
                key : this.uniqueKey++,
                type :prctn_type,
                description:prctn_description,
                className:prctn_className,
                validate:prctn_validate,
                id:id
            });
        },
        //Suppresion of a precaution component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported precaution
        importPrctn(){
            if(this.prctns.length==0 && !this.isInModifMod ){
                this.$refs.importAlert.showAlert();
            }else{
                for (const prctn of this.prctns) {
                    var className="importedPrctn"+prctn.id
                    this.addImportedComponent(prctn.prctn_type,prctn.prctn_description,prctn.prctn_validate,className,prctn.id);
                }
            }
            this.prctns=null
        },
        //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_prctn_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the precaution doesn't have an id
                    if(component.prctn_id==null ){
                        //AddMmePrctn is used
                        component.AddMmePrctn(savedAs);
                    }else
                    //Else if the precaution have an id and addSucces is equal to true 
                    if(component.prctn_id!=null || component.addSucces==true){
                        //updateMmePrctn is used
                        if(component.prctn_validate!=="validated"){
                            component.updateMmePrctn(savedAs);
                        }
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.AddMmePrctn(savedAs);
                }
                

            }
        },
        /*All function inside the created option is called after the component has been created.*/
        created(){
            //If the user choose an importation equipment
            if(this.import_id!==null ){
                //Make a get request to ask to the controller the risk corresponding to the id of the equipment with which data will be imported
                var consultUrl = (id) => `/equipment/prctn/send/${id}`;
                axios.get(consultUrl(this.import_id))
                    .then (response=>this.prctns=response.data)
                    .catch(error => console.log(error)) ;
        
            }

        },
        /*All function inside the created option is called after the component has been mounted.*/
        mounted(){
            //If the user is in consultation or modification mode risk will be added to the vue automatically
            if(this.prctns!==null ){
                this.importRisk();
            }
        }
    }

}
</script>

<style>

</style>
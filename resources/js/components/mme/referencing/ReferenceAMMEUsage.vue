<template>
      <div class="MMEUsage" >
        <h2 class="titleForm">MME Usage</h2>
        <MMEUsageForm ref="ask_usage_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :measurementType="component.measurementType" :precision="component.precision" :application="component.application"
            :reformMod="isInReformMod" :divClass="component.className" :id="component.id" :import_id="component.import"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :mme_id="data_mme_id"
            :reformDate="component.reformDate" :reformBy="component.reformBy"  
            @deleteUsage="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another preventive maintenance operation button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If preventive maintenance operation array is not empty and if the user is not in modifacion mode -->
            <div v-if="this.usages!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importUsage">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import MMEUsageForm from './MMEUsageForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationAlert from '../../alert/ImportationAlert.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components:{
        MMEUsageForm,
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
        importedUsage:{
            type:Array,
            default:null
        },
        mme_id:{
            type:Number
        },
        import_id:{
            type:Number,
            default:null
        },
        reformMod:{
            type:Boolean,
            default:false
        }
    },
    data() {
      return {
        components: [],
        uniqueKey:0,
        usages:this.importedUsage,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        isInReformMod:this.reformMod,
        data_mme_id:this.mme_id
      };
    },
    methods:{
        //Function for adding a new empty preventive maintenance operation form 
        addComponent() {
            this.components.push({
                comp:'MMEUsageForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding imported preventive maintenance operation form with his data
        addImportedComponent(usg_measurementType,usg_precision,usg_metrologicalLevel,
        usg_application,usg_validate,usg_className,id,usg_reformDate,usg_reformBy) {
            this.components.push({
                comp:'MMEUsageForm',
                key : this.uniqueKey++,
                measurementType:usg_measurementType,
                precision :usg_precision,
                metrologicalLevel:usg_metrologicalLevel,
                application:usg_application,
                className:usg_className,
                validate:usg_validate,
                import:this.import_id,
                id:id,
                reformDate:usg_reformDate,
                reformBy:usg_reformBy
            });
        },
        //Suppresion of a preventive maintenance operation component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported preventive maintenance operation
        importUsage(){
            if(this.usages.length==0 && !this.isInModifMod){
                this.$refs.importAlert.showAlert();
            }else{
                for (const usage of this.usages) {
                    var className="importedUsage"+usage.id
                    this.addImportedComponent(usage.usg_measurementType,usage.usg_precision,usage.usg_metrologicalLevel,
                       usage.usg_application,usage.usg_validate,className,usage.id,usage.usg_reformDate,usage.usg_reformBy);
                }
            }
            this.usages=null
        },
                //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_usage_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the preventive maintenance operation doesn't have an id
                    if(component.usg_id==null ){
                        component.addMmeUsage(savedAs);
                    }else
                    //Else if the preventive maintenance operation have an id and addSucces is equal to true 
                    if(component.usg_id!=null || component.addSucces==true){
                        if(component.usg_validate!=="validated"){
                            component.updateMmeUsage(savedAs);
                        }
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addMmeUsage(savedAs);
                }
                

            }
        }
    },
    /*All function inside the created option is called after the component has been created.*/
    created(){
        //If the user choose an importation equipment
        if(this.import_id!==null){
            //Make a get request to ask to the controller the preventive maintenance operation corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/mme_usage/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then (response=> this.usages=response.data)
                .catch(error => console.log(error)) ;
        }

    },
    /*All function inside the created option is called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode preventive maintenance operation will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importUsage();
        }
    }

}
</script>

<style>

</style>
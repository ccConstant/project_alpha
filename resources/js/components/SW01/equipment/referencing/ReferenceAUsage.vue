<!--File name :ReferenceAUsg.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a usage in the equipment-->

<template>
<div class="equipmentUsg" >
        <h2 class="titleForm">Equipment Usage</h2>
        <!--Adding to the vue EquipmentUsgForm by going through the components array with the v-for-->
        <!--ref="ask_usg_data" is used to call the child elements in this component-->
        <!--The emitted deleteUsg is caught here and call the function getContent -->
        <EquipmentUsgForm ref="ask_usg_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp"  :type="component.type" :precuation="component.precaution"  :divClass="component.className" :id="component.id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            :reformMod="isInReformMod" :reformDate="component.reformDate" :reformBy="component.reformBy"
            @deleteUsg="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another usage button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If usage array is not empty and if the user is not in modification mode -->
            <div v-if="this.usages!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importUsg">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>

</template>

<script>
import EquipmentUsgForm from './EquipmentUsgForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'

export default {
/*--------Declaration of the others Components:--------*/
    components:{
        EquipmentUsgForm,
        SaveButtonForm,
        ImportationAlert
    },
    /*--------Declaration of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedUsg: All usages imported from the database
        eq_id: ID of the equipment in which the usage will be added
        import_id: ID of the equipment with which usage will be imported
    ---------------------------------------------------*/
    props:{
        consultMod:{
            type:Boolean,
            default:false
        },
        modifMod:{
            type:Boolean,
            default:false
        },
        importedUsg:{
            type:Array,
            default:null
        },
        eq_id:{
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
        /*--------Declaration of the differents returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    usages: Array of all imported usages
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
      return {
        components: [],
        uniqueKey:0,
        usages:this.importedUsg,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        isInReformMod:this.reformMod,
        data_eq_id:this.eq_id
      };
    },
    methods:{
        //Function for adding a new empty usage form
        addComponent() {
            this.components.push({
                comp:'EquipmentUsgForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding an imported usage form with his data
        addImportedComponent(usg_type,usg_precuation,usg_validate,usg_className,id,usg_reformDate,usg_reformBy) {
            this.components.push({
                comp:'EquipmentUsgForm',
                key : this.uniqueKey++,
                type :usg_type,
                precaution:usg_precuation,
                className:usg_className,
                validate:usg_validate,
                id:id,
                reformDate:usg_reformDate,
                reformBy:usg_reformBy
            });
        },
        //Suppression of a usage component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported usages
        importUsg(){
            if(this.usages.length==0 && !this.isInModifMod){
                console.log("ALert")
                this.$refs.importAlert.showAlert();
            }else{
                for (const usage of this.usages) {
                    console.log(usage)
                    const className = "importedUsg" + usage.id;
                    this.addImportedComponent(usage.usg_type,usage.usg_precaution,usage.usg_validate,className,usage.id,usage.usg_reformDate,usage.usg_reformBy);
                }
            }
            this.usages=null
        },
         //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_usg_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the usage doesn't have, an id
                    if(component.usg_id==null ){
                        //AddequipmentUsg is used
                        component.addEquipmentUsg(savedAs);
                    }else
                    //Else if the usage has an id and addSucces is equal to true
                    if(component.usg_id!=null || component.addSucces==true){
                        //updateEquipmentUsg is used
                        component.updateEquipmentUsg(savedAs);
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addEquipmentUsg(savedAs);
                }
            }
        }
    },
    created(){
        //If the user chooses importation equipment
        if(this.import_id!==null){
            //Make a get request to ask the controller the usage corresponding to the id of the equipment with which data will be imported
            const consultUrl = (id) => `/usage/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then (response=> this.usages=response.data)
                .catch(error => console.log(error)) ;
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode, usages will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importUsg();
        }
    }
}
</script>

<style>

</style>

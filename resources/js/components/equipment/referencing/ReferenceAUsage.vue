<!--File name :ReferenceAUsg.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 17 May 2022-->
<!--Vue Component related to the usage of the who call all the input component and send the data to the controllers-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        EquipmentUsgForm : type, precaution, validate, consultMod, modifMod, divClass, id, eq_id
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
<div class="equipmentUsg" >
        <h2 class="titleForm">Equipment Usage</h2>
        <span class="hr"></span>
        <!--Adding to the vue EquipmentUsgForm by going through the components array with the v-for-->
        <!--ref="ask_usg_data" is used to call the child elements in this component-->
        <!--The emitted deleteUsg is catched here and call the function getContent -->
        <EquipmentUsgForm ref="ask_usg_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp"  :type="component.type" :precuation="component.precaution"  :divClass="component.className" :id="component.id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            @deleteUsg="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another usage button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If usage array is not empty and if the user is not in modifacion mode -->
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
/*Importation of the others Components who will be used here*/
import EquipmentUsgForm from './EquipmentUsgForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationAlert from '../../alert/ImportationAlert.vue'

export default {
/*--------Declartion of the others Components:--------*/
    components:{
        EquipmentUsgForm,
        SaveButtonForm,
        ImportationAlert


    },
    /*--------Declartion of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        importedUsg: All usages imported from the database
        eq_id: Id of the equipment in which the usage will be added
        import_id: Id of the equipment with which usage will be imported
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
        }
    },
        /*--------Declartion of the differents returned data:--------
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
        //Function for adding imported usage form with his data
        addImportedComponent(usg_type,usg_precuation,usg_validate,usg_className,id) {
            this.components.push({
                comp:'EquipmentUsgForm',
                key : this.uniqueKey++,
                type :usg_type,
                precaution:usg_precuation,
                className:usg_className,
                validate:usg_validate,
                id:id
            });
        },
        //Suppresion of a usages component from the vue
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
                    var className="importedUsg"+usage.id
                    this.addImportedComponent(usage.usg_type,usage.usg_precaution,usage.usg_validate,className,usage.id);
                }
            }
            this.usages=null
        },
         //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_usg_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the usage doesn't have an id
                    if(component.usg_id==null ){
                        //AddequipmentUsg is used
                        component.addEquipmentUsg(savedAs);
                    }else
                    //Else if the usage have an id and addSucces is equal to true 
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
    /*All function inside the created option is called after the component has been created.*/
    created(){
        //If the user choose an importation equipment
        if(this.import_id!==null){
            //Make a get request to ask to the controller the usage corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/usage/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then (response=> this.usages=response.data)
                .catch(error => console.log(error)) ;
        }

    },
    /*All function inside the created option is called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode usages will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importUsg();
        }

    }

}
</script>

<style>

</style>
<!--File name :ReferenceADim.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 17 May 2022-->
<!--Vue Component related to the dimension of the who call all the input component and send the data to the controllers-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        EquipmentDimForm : type, name, value, unit, validate, consultMod, modifMod, divClass, id, eq_id
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
    <div class="equipmentDim" >
        <h2 class="titleForm">Equipment Dimension</h2>
        <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
        <!--ref="ask_dim_data" is used to call the child elements in this component-->
        <!--The emitted deleteDim is catched here and call the function getContent -->
        <EquipmentDimForm ref="ask_dim_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :name="component.dimName" :type="component.type"
            :value="component.value" :unit="component.unit" :divClass="component.className" :id="component.id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            @deleteDim="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If dimensions array is not empty and if the user is not in modifacion mode -->
            <div v-if="this.dimensions!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importDim">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>

  
</template>

<script>
/*Importation of the others Components who will be used here*/
import EquipmentDimForm from './EquipmentDimForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationAlert from '../../alert/ImportationAlert.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components:{
        EquipmentDimForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declartion of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        importedDim: All dimensions imported from the database
        eq_id: Id of the equipment in which the dimension will be added
        import_id: Id of the equipment with which dimensions will be imported
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
        importedDim:{
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
        dimensions: Array of all imported dimensions
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
      return {
        components: [],
        uniqueKey:0,
        dimensions:this.importedDim,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_eq_id:this.eq_id,
        all_dim_validate:[]
      };
    },
    methods: {
        //Function for adding a new empty dimension form 
        addComponent() {
            this.components.push({
                comp:'EquipmentDimForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding imported dimension form with his data
        addImportedComponent(dim_type,dim_name,dim_value,dim_unit,dim_validate,dim_className,id) {
            this.components.push({
                comp:'EquipmentDimForm',
                key : this.uniqueKey++,
                type :dim_type,
                dimName:dim_name,
                value:dim_value.toString(),
                unit:dim_unit,
                className:dim_className,
                validate:dim_validate,
                id:id
            });
        },
        //Suppresion of a dimension component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported dimensions
        importDim(){
            if(this.dimensions.length==0 && !this.isInModifMod){
                this.$refs.importAlert.showAlert();
            }else{
                for (const dimension of this.dimensions) {
                    var className="importedDim"+dimension.id
                    this.addImportedComponent(dimension.dim_type,dimension.dim_name,dimension.dim_value,dimension.dim_unit,dimension.dim_validate,className,dimension.id);
                }
            }
            

            this.dimensions=null
        },
        //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_dim_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the dimension doesn't have an id
                    if(component.dim_id==null ){
                        //AddequipmentDim is used
                        component.addEquipmentDim(savedAs);
                    }else
                    //Else if the dimension have an id and addSucces is equal to true 
                    if(component.dim_id!=null || component.addSucces==true){
                        //updateEquipmentDim is used
                        if(component.dim_validate!=="validated"){
                            component.updateEquipmentDim(savedAs);
                        }

                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addEquipmentDim(savedAs);
                }
                

            }
        }
    },
    /*All function inside the created option is called after the component has been created.*/
    created(){
        //If the user choose an importation equipment
        if(this.import_id!==null){
            //Make a get request to ask to the controller the dimension corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/dimension/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then (response=>this.dimensions=response.data)
                .catch(error => console.log(error)) ;
    
        }

    },
    /*All function inside the created option is called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode dimensions will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importDim();
        }
    }
    

}
</script>

<style>

</style>
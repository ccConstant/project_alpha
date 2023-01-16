<!--File name :ReferenceAMme.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 12 Jul 2022-->
<!--Vue Component related to the mme of the who call all the input component and send the data to the controllers-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        EquipmentMmeForm : mme id, mme internal ref, eq_id
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
    <div class="equipmentMme" >
        <h2 class="titleForm">Equipment Mme(s)</h2>
        <!--Adding to the vue EquipmentMmeForm by going through the components array with the v-for-->
        <!--ref="ask_mme_data" is used to call the child elements in this component-->
        <!--The emitted deleteMme is catched here and call the function getContent -->
        <EquipmentMmeForm ref="ask_mme_data" v-for="(component, key) in components" :key="component.key"
           :internalReference="component.internalReference" :name="component.name" :externalReference="component.externalReference" 
            :divClass="component.className" :id="component.id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            @deleteMme="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add MME</button>
            <!--If dimensions array is not empty and if the user is not in modifacion mode -->
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
    </div>

  
</template>

<script>
/*Importation of the others Components who will be used here*/
import EquipmentMmeForm from './EquipmentMmeForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components:{
        EquipmentMmeForm,
        SaveButtonForm,
    },
    /*--------Declartion of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        eq_id: Id of the equipment in which the dimension will be added
    ---------------------------------------------------*/
    props:{
        consultMod:{
            type:Boolean,
            default:false
        },
        updateMod:{
            type:Boolean,
            default:false
        },
        modifMod:{
            type:Boolean,
            default:false
        },
        eq_id:{
            type:Number
        },
        importedMme:{
            type:Array,
            default:null
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
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_eq_id:this.eq_id,
        all_dim_validate:[],
        mmes: this.importedMme,
        
      };
    },
    methods: {
        //Function for adding a new empty mme form 
        addComponent() {
            this.components.push({
                comp:'EquipmentMmeForm',
                key : this.uniqueKey++,
            });
        },

        //Function for adding imported mme form with his data
        addImportedComponent(mme_internalReference, mme_name, mme_externalReference, mme_validate, id) {
            this.components.push({
                comp:'EquipmentMmeForm',
                key : this.uniqueKey++,
                internalReference :  mme_internalReference,
                name : mme_name,
                externalReference :  mme_externalReference,
                validate : mme_validate,
                id : id,

            });
        },
        //Function for adding to the vue the imported mmes
        importMme(){
            if(this.mmes.length==0 && !this.isInModifMod){
                this.$refs.importAlert.showAlert();
            }else{
                for (const mme of this.mmes) {
                    this.addImportedComponent(mme.mme_internalReference, mme.mme_name, mme.mme_externalReference, mme.mme_validate, mme.id);
                }
                this.mmes=null
            }
        },

       //Suppresion of a mme component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },

        //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_mme_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the mme doesn't have an id
                    if(component.mme_id==null ){
                        //AddequipmentDim is used
                        component.addEquipmentMme(savedAs);
                    }else
                    //Else if the mme have an id and addSucces is equal to true 
                    if(component.mme_id!=null || component.addSucces==true){
                        //updateEquipmentMme is used
                        if(component.mme_validate!=="validated"){
                            component.updateEquipmentMme(savedAs);
                        }

                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addEquipmentMme(savedAs);
                }
            }
        }
    },
/*All function inside the created option is called after the component has been created.*/
    created(){
        //If the user choose an importation equipment
        if(this.import_id!==null){
            //Make a get request to ask to the controller the file corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/mme/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then (response=> this.mmes=response.data)

                .catch(error => console.log(error)) ;

        }
    },
    /*All function inside the created option is called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode dimensions will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importMme();
        }
    }
}
</script>

<style>

</style>
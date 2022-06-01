<!--File name : EquipmentIDForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 10 May 2022-->
<!--Vue Component of the Id card of the equipment who call all the input component and send the data to the controllers-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputNumberForm : name, label, isRequired, value, info_text, stepOfInput, isDisabled, inputClassName, Errors
        InputSelectForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        InputTextAreaForm : name, label, isRequired, value, info_text, numberOfRow, isDisabled, inputClassName, Errors
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        InputTextWithOptionForm : name, label, isRequired, value, info_text, isDisabled, options, inputClassName, Errors
        RadioGroupForm : options, label, isRequired, checkedOption, isDisabled
        SaveButton : modifMod, consultMod, saveAll, savedAs
        ImportationModal
-------------------------------------------------------------->
<template>
    <div class="equipmentID ">
        <h2 class="titleForm">Equipment ID</h2>
        <span class="hr"></span>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <!--Call of the different component with their props-->
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.eq_internalReference" name="eq_internalReference" label="Alpha reference :" :isDisabled="!!isInConsultMod"  isRequired v-model="eq_internalReference"/>
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.eq_externalReference" name="eq_externalReference" label="External reference :" :isDisabled="!!isInConsultMod"  isRequired  v-model="eq_externalReference" />
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.eq_name" name="eq_name" label="Equipment name :" :isDisabled="!!isInConsultMod" v-model="eq_name" />
            <InputSelectForm selectClassName="form-select w-50" :Errors="errors.eq_type"  name="eq_type" label="Type :" :options="enum_type" :isDisabled="!!isInConsultMod" :selctedOption="eq_type" v-model="eq_type"/>
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.eq_serialNumber" name="eq_serialNumber" label="Equipment serial Number :" :isDisabled="!!isInConsultMod" v-model="eq_serialNumber" />
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.eq_constructor" name="eq_constructor" label="Equipment constructor :" :isDisabled="!!isInConsultMod" v-model="eq_constructor" />
            <div class="input-group">
                <InputNumberForm  inputClassName="form-control" :Errors="errors.eq_mass" name="eq_mass" label="Equipment mass :" :stepOfInput="0.01" v-model="eq_mass" :isDisabled="!!isInConsultMod" />
                <InputSelectForm   name="eq_massUnit" :Errors="errors.eq_massUnit"  label="Unit :" :options="enum_massUnit" :selctedOption="eq_massUnit" :isDisabled="!!isInConsultMod" v-model="eq_massUnit"/>
            </div>
            <RadioGroupForm label="Mobil?:" :options="eq_mobilityOption" :Errors="errors.eq_mobilityOption" :checkedOption="eq_mobility" :isDisabled="!!isInConsultMod" v-model="eq_mobility"/> 
            <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.eq_remarks" name="eq_remarks" label="Remarks :" :isDisabled="!!isInConsultMod" v-model="eq_remarks"/>
            <InputTextWithOptionForm inputClassName="form-control w-50" :Errors="errors.eq_set" name="eq_set" label="Equipment Set" :isDisabled="!!isInConsultMod" :options="enum_sets" v-model="eq_set"/>
            <InputTextForm v-if="this.eq_importFrom!== undefined " inputClassName="form-control w-50" name="eq_importFrom" label="Import From :" isDisabled v-model="eq_importFrom"/>
            <SaveButtonForm v-if="this.addSucces==false" @add="addEquipment" @update="updateEquipment" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="eq_validate"/>
            <div v-if="this.modifMod!=true">
                <div v-if="this.isInConsultMod!=true">
                    <ImportationModal :set="this.eq_set" @choosedEq="importFrom"/>
                </div>
            </div>
            
            

        </form>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputTextForm from '../../input/InputTextForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputNumberForm from '../../input/InputNumberForm.vue'
import InputTextWithOptionForm from '../../input/InputTextWithOptionForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationModal from './ImportationModal.vue'



export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputTextForm,
        InputSelectForm,
        InputNumberForm,
        InputTextWithOptionForm,
        InputTextAreaForm,
        RadioGroupForm,
        SaveButtonForm,
        ImportationModal
    },
    /*--------Declartion of the differents props:--------
        internalReference : Internal reference given by the data base we will put this data in the corresponding field as default value
        externalReference : External reference given by the data base we will put this data in the corresponding field as default value
        name : Name given by the data base we will put this data in the corresponding field as default value
        type : Type given by the data base we will put this data in the corresponding field as default value
        serialNumber : SerialNumber given by the data base we will put this data in the corresponding field as default value
        construct : Constructor given by the data base we will put this data in the corresponding field as default value
        mass : Mass given by the data base we will put this data in the corresponding field as default value
        massUnit : Unit of the mass given by the data base we will put this data in the corresponding field as default value
        mobility : Mobility given by the data base we will put this data in the corresponding field as default value
        Remarks : Remarks given by the data base we will put this data in the corresponding field as default value
        Set: Set given by the data base we will put this data in the corresponding field as default value
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        
    ---------------------------------------------------*/
    props:{
        internalReference:{
            type:String
        },
        externalReference:{
            type:String
        },
        name:{
            type:String
        },
        type:{
            type:String
        },
        serialNumber:{
            type:String
        },
        construct:{
            type:String
        },
        mass:{
            type:String
        },
        massUnit:{
            type:String
        },
        mobility:{
            type:Boolean
        },
        remarks:{
            type:String
        },
        set:{
            type:String
        },
        validate:{
            type:String
        },
        consultMod:{
            type:Boolean,
            default:false
        },
        modifMod:{
            type:Boolean,
            default :false
        }
    },
    data(){
        return{
    /*--------Declartion of the differents returned data:--------
        eq_internalReference: Internal reference of the equipment who  will be apear in the field and updated dynamically
        eq_externalReference: External reference of the equipment who  will be apear in the field and updated dynamically
        eq_name: Name of the equipment who  will be apear in the field and updated dynamically
        eq_type: Type of the equipment who  will be apear in the field and updated dynamically
        eq_serialNumber: Serial Number of the equipment who  will be apear in the field and updated dynamically
        eq_constructor: Constructor of the equipment who  will be apear in the field and updated dynamically
        eq_remarks : Remarks about the equipment who  will be apear in the field and updated dynamically
        eq_validate: Validation option selected by the user
        eq_validates : Different validation option with values : drafted , to_be_validated  and validated 
        eq_mobilityOption : Different mobility option with their names and values
        enum_type: Different type option gets from the database
        enum_massUnit : Different unit of mass option gets from the database
        enum_sets : Other equipments sets gets from the database
        eq_id : id of the current equipment get from the url
        errors : errors due to a wrong input in the field, given by the controller
    -----------------------------------------------------------*/
            eq_internalReference : this.internalReference,
            eq_externalReference :this.externalReference,
            eq_name :this.name,
            eq_type:this.type,
            eq_serialNumber: this.serialNumber,
            eq_constructor :this.construct,
            eq_mass :this.mass,
            eq_massUnit: this.massUnit,
            eq_mobility :this.mobility,
            eq_remarks: this.remarks,
            eq_set : this.set,
            eq_validate:this.validate,
            eq_importFrom:undefined,
            eq_mobilityOption :[
                {id: 'Yes', value:true},
                {id : 'No', value:false}
            ],
            isInConsultMod:this.consultMod,
            enum_type : [], 
            enum_massUnit : [],
            enum_sets:[],
            eq_id:this.$route.params.id,
            errors:{},
            addSucces:false
        }
    },
    /*All function inside the created option is called after the component has been created.*/
    created(){
        /*Ask for the controller different type option */
        axios.get('/equipment/enum/type')
            .then (response=> this.enum_type=response.data) 
            .catch(error => console.log(error)) ;
        /*Ask for the controller different type option */
        axios.get('/equipment/enum/massUnit')
            .then (response=> this.enum_massUnit=response.data) 
            .catch(error => console.log(error)) ; 
        /*Ask for the controller other equipments sets */
        axios.get('/equipment/sets')
            .then (response=> this.enum_sets=response.data) 
            .catch(error => console.log(error)) ; 
    },

    /*--------Declartion of the differents methods:--------*/
    methods: {
        equipment_save(){
            console.log("ENREGISTRER EN BROUILLON\n")
            console.log(this.eq_internalReference,"\n",this.eq_externalReference,"\n"
            ,this.eq_name,"\n",this.eq_type,"\n",this.eq_serialNumber,"\n",
            this.eq_constructor,"\n",this.eq_mass,"\n",this.eq_massUnit,"\n",
            this.mobility,"\n",this.eq_remarks,"\n",this.eq_set,"\n",this.eq_validate,"\nID:",this.eq_id);
        }, 
        /*Sending to the controller all the information about the equipment so that it can be added to the database */ 
        addEquipment(savedAs){
            if(!this.addSucces){
                 axios.post('/equipment/verif',{
                    eq_internalReference : this.eq_internalReference, 
                    eq_externalReference : this.eq_externalReference,
                    eq_name : this.eq_name,
                    eq_type : this.eq_type, 
                    eq_serialNumber : this.eq_serialNumber,
                    eq_constructor : this.eq_constructor,
                    eq_mass : this.eq_mass,
                    eq_massUnit : this.eq_massUnit,
                    eq_mobility : this.eq_mobility,
                    eq_remarks : this.eq_remarks,
                    eq_set : this.eq_set,
                    eq_validate : savedAs,
                    reason:'add'
                })
                .then(response =>{
                        axios.post('/equipment/add',{
                            eq_internalReference : this.eq_internalReference, 
                            eq_externalReference : this.eq_externalReference,
                            eq_name : this.eq_name,
                            eq_type : this.eq_type, 
                            eq_serialNumber : this.eq_serialNumber,
                            eq_constructor : this.eq_constructor,
                            eq_mass : this.eq_mass,
                            eq_massUnit : this.eq_massUnit,
                            eq_mobility : this.eq_mobility,
                            eq_remarks : this.eq_remarks,
                            eq_set : this.eq_set,
                            eq_validate : savedAs,
                        })
                        .then(response =>{
                                this.addSucces=true;
                                this.isInConsultMod=true;
                                this.eq_id=response.data;
                                this.$emit('EqID',this.eq_id);
                                
                            })
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ; 
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated to the database */ 
        updateEquipment(savedAs){
                    axios.post('/equipment/verif',{
                    eq_internalReference : this.eq_internalReference, 
                    eq_externalReference : this.eq_externalReference,
                    eq_name : this.eq_name,
                    eq_type : this.eq_type, 
                    eq_serialNumber : this.eq_serialNumber,
                    eq_constructor : this.eq_constructor,
                    eq_mass : this.eq_mass,
                    eq_massUnit : this.eq_massUnit,
                    eq_mobility : this.eq_mobility,
                    eq_remarks : this.eq_remarks,
                    eq_set : this.eq_set,
                    eq_validate : savedAs,
                    eq_id:this.eq_id,
                    reason:'update'
                })
                .then(response =>{
                        console.log("update") ;
                        var consultUrl = (id) => `/equipment/update/${id}`;
                        axios.post(consultUrl(this.eq_id),{
                            eq_internalReference : this.eq_internalReference, 
                            eq_externalReference : this.eq_externalReference,
                            eq_name : this.eq_name,
                            eq_type : this.eq_type, 
                            eq_serialNumber : this.eq_serialNumber,
                            eq_constructor : this.eq_constructor,
                            eq_mass : this.eq_mass,
                            eq_massUnit : this.eq_massUnit,
                            eq_mobility : this.eq_mobility,
                            eq_remarks : this.eq_remarks,
                            eq_set : this.eq_set,
                            eq_validate : savedAs,
                        })
                        .then(response => console.log(response.data))
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ;
                
                
                

        },
        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        importFrom(value){
            this.eq_importFrom=value.eq_internalReference;
            this.$emit('importFromEqID',value.id);
        }
    }
}
</script>

<style lang="scss">
    .hr {
        display: block;
        flex: 1;
        height: 3px;
        background: #D4D4D4;
    }
    .titleForm{
        padding-left: 10px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }
</style>

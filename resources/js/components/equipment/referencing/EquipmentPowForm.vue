<!--File name : EquipmentPowForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 17 May 2022-->
<!--Vue Component of the Form of the equipment power who call all the input component-->


<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        InputSelectForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
    <div :class="divClass">
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError" >
            <!--Call of the different component with their props-->
            <InputSelectForm  @clearSelectError='clearSelectError' selectClassName="form-select w-50" :Errors="errors.pow_type" name="pow_type" label="Power Type :" :options="enum_pow_type" :isDisabled="!!isInConsultedMod" :selctedOption="pow_type" :selectedDivName="divClass" v-model="pow_type"/>
            <InputTextWithOptionForm inputClassName="form-control w-50" :Errors="errors.pow_name" name="pow_name" label="Power Name " :options="pow_names" v-model="pow_name" :isDisabled="!!isInConsultedMod"/>
            <div class="input-group">
                <InputNumberForm  inputClassName="form-control" :Errors="errors.pow_value" name="pow_value" label="Power value :" :stepOfInput="0.01" v-model="pow_value" :isDisabled="!!isInConsultedMod"/>
                <InputTextForm inputClassName="form-control" name="pow_unit"  label="Unit :" :Errors="errors.pow_unit" :isDisabled="!!isInConsultedMod" v-model="pow_unit"/>
            </div>
            <div class="input-group">
                <InputNumberForm  inputClassName="form-control" :Errors="errors.pow_consumptionValue" name="pow_consumptionValue" label="Consumption value :" :stepOfInput="0.01" v-model="pow_consumptionValue" :isDisabled="!!isInConsultedMod"/>
                <InputTextForm   inputClassName="form-control" name="pow_consumptionUnit"  label="Unit :" :Errors="errors.pow_consumptionUnit" :isDisabled="!!isInConsultedMod" v-model="pow_consumptionUnit"/>
            </div>
            <!--If addSucces is equal to false, the buttons appear -->
            <div v-if="this.addSucces==false ">
                <!--If this power doesn't have a id the addEquipmentPow is called function else the updateEquipmentPow function is called -->
                <div v-if="this.pow_id==null || this.addSucces==true">
                    <div v-if="modifMod==true">
                        <SaveButtonForm @add="addEquipmentPow" @update="updateEquipmentPow" :consultMod="this.isInConsultedMod" :savedAs="pow_validate" :AddinUpdate="true"/>
                    </div>
                    <div v-else>
                        <SaveButtonForm @add="addEquipmentPow" @update="updateEquipmentPow" :consultMod="this.isInConsultedMod" :savedAs="pow_validate"/>
                    </div>
                </div>
                <div v-else-if="this.pow_id!==null">
                    <SaveButtonForm @add="addEquipmentPow" @update="updateEquipmentPow" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="pow_validate"/>
                </div>
                <!-- If the user is not in the consultation mode, the delete button appear -->
                <DeleteComponentButton :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                
            </div>
        </form>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputNumberForm from '../../input/InputNumberForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputTextWithOptionForm from '../../input/InputTextWithOptionForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'


export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputSelectForm,
        InputNumberForm,
        SaveButtonForm,
        InputTextForm,
        InputTextWithOptionForm,
        DeleteComponentButton


    },
    /*--------Declartion of the differents props:--------
        type : Power type given by the data base we will put this data in the corresponding field as default value
        name : Power name reference given by the data base we will put this data in the corresponding field as default value
        value : Power value given by the data base we will put this data in the corresponding field as default value
        unit : Power unit given by the data base we will put this data in the corresponding field as default value
        consumptionValue: Consumption value given by the data base we will put this data in the corresponding field as default value
        consumptionUnit: Consumption unit given by the data base we will put this data in the corresponding field as default value
        validate: Validation option of the power
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this Power form
        id: Id of an already created Power 
        eq_id: Id of the equipment in which the power will be added
        
    ---------------------------------------------------*/
    props:{
        type:{
            type:String
        },
        name:{
            type:String
        },
        value:{
            type:String
        },
        unit:{
            type:String
        },
        consumptionValue:{
            type:String
        },
        consumptionUnit:{
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
        },
        divClass:{
            type:String
        },
        id:{
            type:Number,
            default:null
        },
        eq_id:{
            type:Number
        }

    },
    /*--------Declartion of the differents returned data:--------
        pow_type: Type of the power who  will be apear in the field and updated dynamically
        pow_name: Name of the power who  will be apear in the field and updated dynamically
        pow_value: Value of the power who  will be apear in the field and updated dynamically
        pow_unit: Unit of the power who  will be apear in the field and updated dynamically
        pow_consumptionValue:
        pow_consumptionUnit:
        pow_validate: Validation option of the power
        pow_id: Id oh this power
        equipment_id_add: Id of the equipment in which the power will be added
        equipment_id_update: Id of the equipment in which the power will be updated
        enum_pow_type : Different types of power gets from the database
        pow_names : Different names of power gets from the database
        enum_pow_unit : Different unites of power gets from the database
        enum_pow_consumptionUnit: Different consumption unites of power gets from the database
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this power has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            pow_type:this.type,
            pow_name:this.name,
            pow_value:this.value,
            pow_unit:this.unit,
            pow_consumptionValue:this.consumptionValue,
            pow_consumptionUnit:this.consumptionUnit,
            pow_validate:this.validate,
            pow_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            enum_pow_type : [],
            pow_names:[],
            enum_pow_unit:[],
            enum_pow_consumptionUnit:[],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod
        }
    },
    /*All function inside the created option is called after the component has been mounted.*/
    created(){
        /*Ask for the controller different types of the power  */
        axios.get('/power/enum/type')
            .then (response=> this.enum_pow_type=response.data) 
            .catch(error => console.log(error)) ;
        /*Ask for the controller different names of the power  */
        axios.get('/power/names')
            .then (response=> this.pow_names=response.data) 
            .catch(error => console.log(error)) ;   
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentPow(savedAs){
            if(!this.addSucces){
                //Id of the equipment in which the power will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                if(!this.modifMod){
                        id=this.equipment_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.equipment_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                Type, name, value, unit, consumption unit, consumption value and validate option is sended to the controller*/
                axios.post('/power/verif',{
                    pow_type : this.pow_type,
                    pow_name : this.pow_name,
                    pow_value : this.pow_value,
                    pow_unit : this.pow_unit,
                    pow_consumptionValue : this.pow_consumptionValue,
                    pow_consumptionUnit : this.pow_consumptionUnit, 
                    pow_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the power in the data base
                    Type, name, value, unit, consumption unit, consumption value ,validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/pow',{
                        pow_type : this.pow_type,
                        pow_name : this.pow_name,
                        pow_value : this.pow_value,
                        pow_unit : this.pow_unit, 
                        pow_consumptionValue : this.pow_consumptionValue,
                        pow_consumptionUnit : this.pow_consumptionUnit,
                        pow_validate :savedAs,
                        eq_id:id
                
                    })
                    //If the power is added succesfuly
                    .then(response =>{
                        console.log(response);
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the powers take the value of the newlly created id
                        this.pow_id=response.data;
                        //The validate option of this power take the value of savedAs(Params of the function)
                        this.pow_validate=savedAs;
                        
                    })
                    //If the controller sends errors we put it in the errors object 
                    .catch(error => this.errors=error.response.data.errors) ;
                ;})
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        updateEquipmentPow(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit, consumption unit, consumption value and validate option is sended to the controller*/
            axios.post('/power/verif',{
                    pow_type : this.pow_type,
                    pow_name : this.pow_name,
                    pow_value : this.pow_value,
                    pow_unit : this.pow_unit,
                    pow_consumptionValue : this.pow_consumptionValue,
                    pow_consumptionUnit : this.pow_consumptionUnit, 
                    pow_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    console.log("update dans la base")
                    /*If all the verif passed, a new post this time to add the power in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the power who will be update*/
                    var consultUrl = (id) => `/equipment/update/pow/${id}`;
                    axios.post(consultUrl(this.pow_id),{
                        pow_type : this.pow_type,
                        pow_name : this.pow_name,
                        pow_value : this.pow_value,
                        pow_unit : this.pow_unit,
                        pow_consumptionValue : this.pow_consumptionValue,
                        pow_consumptionUnit : this.pow_consumptionUnit, 
                        eq_id:this.equipment_id_update,
                        pow_validate : savedAs
                    })
                    .then(response =>{this.pow_validate=savedAs;})
                    //If the controller sends errors we put it in the errors object 
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a power from the view and the database
        deleteComponent(){

            if(this.modifMod==true && this.pow_id!==null){
                console.log("supression");
                //Send a post request with the id of the power who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/pow/${id}`;
                axios.post(consultUrl(this.pow_id),{
                })
                .then(response =>{
                    this.$emit('deletePow','');
                    //If the user is in update mode and the power exist in the database
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }
        },
        clearSelectError(value){
            delete this.errors[value];
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
<!--File name : EquipmentMmeForm.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 12 Jul 2022-->
<!--Vue Component of the Form of the equipment mme who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        InputSelectForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                 <InputSelectForm @clearSelectError='clearSelectError' selectClassName="form-select w-50" :Errors="errors.mme" name="mme_internalReference" label="Mme Internal Reference :" :options="this.list_mme" :isDisabled="!!isInConsultedMod" :selctedOption="this.mme_internalReference" :selectedDivName="this.divClass" v-model="internalRef" />
                <!--<InputSelectForm @clearSelectError='clearSelectError' selectClassName="form-select w-50" :Errors="errors.mme" name="mme_name" label="Mme :" :options="this.list_mme" :isDisabled="!!isInConsultedMod" :selctedOption="this.internalReference" :selectedDivName="this.divClass" v-model="internalReference" />-->
                <!--If addSucces is equal to false, the buttons appear -->         
            </form>
            <div v-if="this.internalRef">
                <SaveButtonForm @add="addEquipmentMme" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="this.mme_validate"/>
                <DeleteComponentButton :validationMode="mme_validate" :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
            </div>
            <SucessAlert ref="sucessAlert"/>
        </div>

    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'




export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert


    },
    /*--------Declartion of the differents props:--------
        type : Dimension type given by the data base we will put this data in the corresponding field as default value
        name : Dimension name reference given by the data base we will put this data in the corresponding field as default value
        value : Dimension value given by the data base we will put this data in the corresponding field as default value
        unit : Dimension unit given by the data base we will put this data in the corresponding field as default value
        validate: Validation option of the dimension
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this Dimension form
        id: Id of an already created Dimension 
        eq_id: Id of the equipment in which the dimension will be added
        
    ---------------------------------------------------*/
    props:{
        internalRef:{
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
        dim_type: Type of the dimension who  will be apear in the field and updated dynamically
        dim_name: Name of the dimension who  will be apear in the field and updated dynamically
        dim_value: Value of the dimension who  will be apear in the field and updated dynamically
        dim_unit: Unit of the dimension who  will be apear in the field and updated dynamically
        dim_validate: Validation option of the dimensions
        dim_id: Id oh this dimension
        equipment_id_add: Id of the equipment in which the dimensions will be added
        equipment_id_update: Id of the equipment in which the dimensions will be updated
        enum_dim_type : Different types of dimension gets from the database
        enum_dim_name : Different names of dimension gets from the database
        enum_dim_unit : Different unites of dimension gets from the database
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this dimension has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            list_mme : [],
           equipment_id_add:this.eq_id,
            /*equipment_id_update:this.$route.params.id,
          */
            mme_internalReference : this.internalRef,
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            mme_validate:null,
        }
    },
    /*All function inside the created option is called after the component has been mounted.*/
    created(){
        /*Ask for the controller different types of the dimension  */
        axios.get('/mme/mmes_not_linked')
            .then (response=> {
                this.list_mme=response.data ; 
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentMme(savedAs){
            if(!this.addSucces){
                //Id of the equipment in which the dimensions will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                id=this.equipment_id_add
                var urlLinkMmetoEq = (id) => `/mme/link_to_eq/${id}`;
                axios.post(urlLinkMmetoEq(id),{
                    'mme_internalReference' : this.internalRef,
                })
                //If the mme is added succesfuly
                .then(response =>{
                    this.$refs.sucessAlert.showAlert(`Equipment mme saved as ${savedAs} successfully`);
                    //If we the user is not in modifMod
                    if(!this.modifMod){
                        //The form pass in consulting mode and addSucces pass to True
                        this.isInConsultedMod=true;
                        this.addSucces=true
                    }   
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },

        extract_mme_refs(mme_list){
            let refs=[] ; 
            for(let mme of mme_list){
                refs.push( {type: { value : mme.value}, index:refs.length}) ;
            }
            return refs ; 
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        //updateEquipmentMme(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
           /* axios.post('/dimension/verif',{
                    dim_type : this.dim_type,
                    dim_name : this.dim_name,
                    dim_value : this.dim_value,
                    dim_unit : this.dim_unit, 
                    dim_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    console.log("update dans la base")
                    console.log(this.dim_type,"\n",this.dim_name,"\n"
                    ,this.dim_value,"\n",this.dim_unit,"\n",savedAs,"\n",this.equipment_id_update,"\n",this.dim_id);
                    
                    /*If all the verif passed, a new post this time to add the dimension in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the dimension who will be update
                    var consultUrl = (id) => `/equipment/update/dim/${id}`;
                    axios.post(consultUrl(this.dim_id),{
                        dim_type : this.dim_type,
                        dim_name : this.dim_name,
                        dim_value : this.dim_value,
                        dim_unit : this.dim_unit,
                        eq_id:this.equipment_id_update,
                        dim_validate : savedAs
                    })
                    .then(response =>{
                        this.$refs.sucessAlert.showAlert(`Equipment dimension updated as ${savedAs} successfully`);
                        this.dim_validate=savedAs;
                    })
                    //If the controller sends errors we put it in the errors object 
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
        },*/
        //Clear all the error of the targeted field
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a dimension from the view and the database
        deleteComponent(){
            //If the user is in update mode and the mme has been updated
            if(this.modifMod==true){
                /*console.log("supression");
                //Send a post request with the id of the dimension who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/dim/${id}`;
                axios.post(consultUrl(this.dim_id),{
                    eq_id:this.equipment_id_update
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteMme','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            */
            }else{
                this.$emit('deleteMme','')
            }
            
        },
        clearSelectError(value){
            delete this.errors[value];
        }
    },
    

}
</script>

<style lang="scss">
    .titleForm{
        padding-left: 10px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }
</style>

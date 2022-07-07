<!--File name : EquipmentUsgForm.vue-->
<!--Creation date : 18 May 2022-->
<!--Update date : 18 May 2022-->
<!--Vue Component of the Form of the usage  who call all the input component-->
<template>
    <div :class="divClass" @keydown="clearError">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container" >
                <!--Call of the different component with their props-->
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.usg_type" name="usg_type" label="Type :" :isDisabled="!!isInConsultedMod" v-model="usg_type" :info_text="infos_usage[0].info_value"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.usg_precaution" name="usg_precaution" label="Precaution :" :isDisabled="!!isInConsultedMod" v-model="usg_precaution" :info_text="infos_usage[1].info_value"/>
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this usage doesn't have a id the addEquipmentUsg is called function else the updateEquipmentUsg function is called -->
                    <div v-if="this.usg_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentUsg" @update="updateEquipmentUsg" :consultMod="this.isInConsultedMod" :savedAs="usg_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentUsg" @update="updateEquipmentUsg" :consultMod="this.isInConsultedMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.usg_id!==null && reformMod==false">
                        <div v-if="usg_reformDate!=null" >
                            <p>Reformed at {{usg_reformDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addEquipmentUsg" @update="updateEquipmentUsg" :reformMod="this.isInReformMod" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="usg_validate" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && usg_reformDate===null">
                        <ReformComponentButton :reformedBy="usg_reformedBy" :reformDate="usg_reformDate" :reformMod="this.isInReformMod" @reformOk="reformComponent" :info="infos_usage[2].info_value"/>
                    </div>
                </div>  
            </form>
            <ErrorAlert ref="errorAlert"/>
            
        </div>
       
    </div>
</template>

<script>
import ErrorAlert from '../../alert/ErrorAlert.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../button/ReformComponentButton.vue'


export default {
    components:{
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert


    },
    /*--------Declartion of the differents props:--------
        type : 
        precaution : 
        validate: Validation option of the Usage
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this Usage form
        id: Id of an already created Usage 
        eq_id: Id of the equipment in which the Usage will be added
        
    ---------------------------------------------------*/
    props:{
        type:{
            type:String
        },
        precuation:{
            type:String
        },
        value:{
            type:String
        },
        unit:{
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
        reformDate:{
            type:String,
            default:null
        },
        eq_id:{
            type:Number
        },
        reformMod:{
            type:Boolean,
            default:false
        }

    },
    /*--------Declartion of the differents returned data:--------
        usg_type: 
        usg_precaution: 
        usg_validate: Validation option of the usage
        usg_id: Id oh this usage
        equipment_id_add: Id of the equipment in which the usage will be added
        equipment_id_update: Id of the equipment in which the usage will be updated
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this usage has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            usg_type:this.type,
            usg_precaution:this.precuation,
            usg_validate:this.validate,
            usg_reformDate:this.reformDate,
            usg_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            isInReformMod:this.reformMod,
            infos_usage:[],
            loaded:false

        }
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentUsg(savedAs){
            if(!this.addSucces){
                //Id of the equipment in which the usage will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                if(!this.modifMod){
                        id=this.equipment_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.equipment_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
                axios.post('/usage/verif',{
                    usg_type : this.usg_type,
                    usg_precaution : this.usg_precaution,
                    usg_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the usage in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/usg',{
                        usg_type : this.usg_type,
                        usg_precaution : this.usg_precaution,
                        usg_validate :savedAs,
                        eq_id:id
                
                    })
                    //If the usage is added succesfuly
                    .then(response =>{
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the usage take the value of the newlly created id
                        this.usg_id=response.data;
                        //The validate option of this usage take the value of savedAs(Params of the function)
                        this.usg_validate=savedAs;
                        
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
        updateEquipmentUsg(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            axios.post('/usage/verif',{
                    usg_type : this.usg_type,
                    usg_precaution : this.usg_precaution,
                    usg_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    
                    /*If all the verif passed, a new post this time to add the usage in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the usage who will be update*/
                    var consultUrl = (id) => `/equipment/update/usg/${id}`;
                    axios.post(consultUrl(this.usg_id),{
                        usg_type : this.usg_type,
                        usg_precaution : this.usg_precaution,
                        usg_validate :savedAs,
                        eq_id:this.equipment_id_update,

                    })
                    .then(response =>{this.usg_validate=savedAs;})
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
        //Function for deleting a usage from the view and the database
        deleteComponent(){

            //If the user is in update mode and the usage exist in the database
            if(this.modifMod==true && this.usg_id!==null){
                //Send a post request with the id of the usage who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/usg/${id}`;
                axios.post(consultUrl(this.usg_id),{
                    eq_id:this.equipment_id_update
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteUsg','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }else{
                this.$emit('deleteUsg','')

            }
            
        },
        reformComponent(endDate){
            //If the user is in update mode and the usage exist in the database
                //Send a post request with the id of the usage who will be deleted in the url
            if(this.$userId.user_makeReformRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            var consultUrl = (id) => `/equipment/reform/usg/${id}`;
            axios.post(consultUrl(this.usg_id),{
                eq_id:this.equipment_id_update,
                usg_reformDate:endDate 
            })
            .then(response =>{
                //Emit to the parent component that we want to delete this component
                this.$emit('deleteUsg','')
            })
            //If the controller sends errors we put it in the errors object 
            .catch(error => {this.$refs.errorAlert.showAlert(error.response.data.errors['usg_reformDate'])}) ;
        
            
        }
        
    },
    created(){
        axios.get('/info/send/usage')
        .then (response=> {
            this.infos_usage=response.data;
            this.loaded=true;
            }) 
        .catch(error => console.log(error)) ;

    }

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
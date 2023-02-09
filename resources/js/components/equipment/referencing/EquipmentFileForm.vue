<!--File name : EquipmentFileForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 2 Feb 2023-->
<!--Vue Component of the Form of the equipment file who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
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
                <InputTextForm  inputClassName="form-control" :Errors="errors.file_name" name="file_name" label="File name :" v-model="file_name" :isDisabled="!!isInConsultedMod" :info_text="infos_file[0].info_value"/>
                <InputTextForm  inputClassName="form-control" :Errors="errors.file_location" name="file_location" label="File location :" v-model="file_location" :isDisabled="!!isInConsultedMod" :info_text="infos_file[1].info_value"/>
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this file doesn't have a id the addEquipmentFile is called function else the updateEquipmentFile function is called -->
                    <div v-if="this.file_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentFile" @update="updateEquipmentFile" :consultMod="this.isInConsultedMod" :savedAs="file_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentFile" @update="updateEquipmentFile" :consultMod="this.isInConsultedMod" :savedAs="file_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.file_id!==null">
                        <SaveButtonForm @add="addEquipmentFile" @update="updateEquipmentFile" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="file_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="file_validate" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>

                </div>       
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
        
    </div>
  
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputTextForm from '../../input/InputTextForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert

    },
    /*--------Declartion of the differents props:--------
        name : File name given by the data base we will put this data in the corresponding field as default value
        location : File location given by the data base we will put this data in the corresponding field as default value
        validate: Validation option of the file
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this file form
        id: Id of an already created file 
        eq_id: Id of the equipment in which the file will be added
        
    ---------------------------------------------------*/
    props:{
        name:{
            type:String
        },
        location:{
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
        file_name: Name of the file who  will be apear in the field and updated dynamically
        file_location: Location of the file who will be apear in the field and updated dynamically
        file_validate: Validation option of the file
        file_id: Id oh this file
        equipment_id_add: Id of the equipment in which the file will be added
        equipment_id_update: Id of the equipment in which the file will be updated
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this file has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            file_name:this.name,
            file_location:this.location,
            file_validate:this.validate,
            file_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            infos_file:[]
        }
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentFile(savedAs, reason, lifesheet_created){
            if(!this.addSucces){
                //Id of the equipment in which the file will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                if(!this.modifMod){
                        id=this.equipment_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.equipment_id_update;
                }
                
                /*First post to verify if all the fields are filled correctly
                Name, location and validate option is sended to the controller*/
                axios.post('/file/verif',{
                    file_name : this.file_name,
                    file_location : this.file_location,
                    file_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the file in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/file',{
                        file_name : this.file_name,
                        file_location : this.file_location,
                        file_validate :savedAs,
                        eq_id:id
                
                    })
                    //If the file is added succesfuly
                    .then(response =>{
                         //We test if a life sheet have been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
                        if (lifesheet_created==true){
                            axios.post(`/history/add/equipment/${id}`,{
                                history_reasonUpdate :reason, 
                            });
                        }
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the file take the value of the newlly created id
                        this.file_id=response.data;
                        //The validate option of this file take the value of savedAs(Params of the function)
                        this.file_validate=savedAs;
                        
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
        updateEquipmentFile(savedAs, reason, lifesheet_created){
            
            /*First post to verify if all the fields are filled correctly
                name, location and validate option is sended to the controller*/
            axios.post('/file/verif',{
                    file_name : this.file_name,
                    file_location : this.file_location,
                    file_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    
                    /*If all the verif passed, a new post this time to add the file in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the file who will be update*/
                    var consultUrl = (id) => `/equipment/update/file/${id}`;
                    axios.post(consultUrl(this.file_id),{
                    file_name : this.file_name,
                    file_location : this.file_location,
                        eq_id:this.equipment_id_update,
                        file_validate : savedAs
                    })
                    .then(response =>{
                        this.file_validate=savedAs;
                        //We test if a life sheet have been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
                        var id = this.equipment_id_update;
                        if (lifesheet_created==true){
                            axios.post(`/history/add/equipment/${id}`,{
                                history_reasonUpdate :reason, 
                            });
                        }
                    })
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
        //Function for deleting a file from the view and the database
        deleteComponent(reason, lifesheet_created){
            //If the user is in update mode and the file exist in the database
            if(this.modifMod==true && this.file_id!==null){
                console.log("supression");
                //Send a post request with the id of the file who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/file/${id}`;
                axios.post(consultUrl(this.file_id),{
                    eq_id:this.equipment_id_update
                })
                .then(response =>{
                    var id=this.equipment_id_update
                    //We test if a life sheet have been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the delete
                    if (lifesheet_created==true){
                        axios.post(`/history/add/equipment/${id}`,{
                            history_reasonUpdate :reason, 
                        });
                    }
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteFile','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deleteFile','')
            }
            
        }
    },
    created(){
        axios.get('/info/send/file')
        .then (response=> {
            this.infos_file=response.data;
            this.loaded=true;
        }) 
        .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>
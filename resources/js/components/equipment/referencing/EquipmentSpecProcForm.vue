<!--File name : EquipmentSpecProcForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 2 Feb 2023-->
<!--Vue Component of the Form of the equipment special process who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextAreaForm : name, label, isRequired, value, info_text, numberOfRow, isDisabled, inputClassName, Errors
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        RadioGroupForm : options, label, isRequired, checkedOption, isDisabled
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
                <RadioGroupForm @clearRadioError="clearRadioError" label="ALPHA's Cat I processes?" :options="existOption" :Errors="errors.spProc_exist"  :checkedOption="spProc_exist" :isDisabled="!!isInConsultedMod" v-model="spProc_exist" :info_text="infos_spProc[0].info_value"/>
                <InputTextForm v-if="this.spProc_exist==true" inputClassName="form-control w-50" :Errors="errors.spProc_name" name="spProc_name" label="Special process name :" v-model="spProc_name" :isDisabled="!!isInConsultedMod" :info_text="infos_spProc[1].info_value"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.spProc_remarksOrPrecaution" name="spProc_remarksOrPrecaution" label="Remarks :" :isDisabled="!!isInConsultedMod" v-model="spProc_remarksOrPrecaution" :info_text="infos_spProc[2].info_value"/>
                
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this special process doesn't have a id the addEquipmentSpProc is called function else the updateEquipmentSpProc function is called -->
                    <div v-if="this.spProc_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentSpProc" @update="updateEquipmentSpProc" :consultMod="this.isInConsultedMod" :savedAs="spProc_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentSpProc" @update="updateEquipmentSpProc" :consultMod="this.isInConsultedMod" :savedAs="spProc_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.spProc_id!==null">
                        <SaveButtonForm @add="addEquipmentSpProc" @update="updateEquipmentSpProc" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="spProc_validate"/>
                    </div>
                </div>       
            </form>
        </div>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        RadioGroupForm,
        InputTextAreaForm,
        SaveButtonForm,
        InputTextForm

    },
    /*--------Declartion of the differents props:--------
        remarksOrPrecaution : Note or precaution related to the special process given by the data base we will put this data in the corresponding field as default value
        exist : Boolean if this special process is applicated given by the data base we will put this data in the corresponding field as default value
        name : Name of this special process given by the data base we will put this data in the corresponding field as default value
        validate: Validation option of the special process
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this special process form
        id: Id of an already created special process 
        eq_id: Id of the equipment in which the special process will be added
        
    ---------------------------------------------------*/
    props:{
        remarksOrPrecaution:{
            type:String
        },
        name:{
            type:String,
        },
        exist:{
            type:Boolean,
            default:null
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
        spProc_remarksOrPrecaution: 
        spProc_exist: 
        spProc_name :
        spProc_validate: Validation option of the special process
        spProc_id: Id oh this special process
        equipment_id_add: Id of the equipment in which the special process will be added
        equipment_id_update: Id of the equipment in which the special process will be updated
        existOption : Different exist option with their id and value
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this special process has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            spProc_remarksOrPrecaution:this.remarksOrPrecaution,
            spProc_exist:this.exist,
            spProc_name:this.name,
            spProc_validate:this.validate,
            spProc_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            existOption :[
                {id: 'eq_specProcExist', value:true, text:'Yes'},
                {id : 'eq_specProcExist', value:false, text:'No'}
            ],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            infos_spProc:[]
        }
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentSpProc(savedAs){
            if(!this.addSucces){
                //Id of the equipment in which the special process will be added
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
                axios.post('/spProc/verif',{
                    spProc_remarksOrPrecaution : this.spProc_remarksOrPrecaution,
                    spProc_exist : this.spProc_exist,
                    spProc_name: this.spProc_name,
                    spProc_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the special process in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/spProc',{
                        spProc_remarksOrPrecaution : this.spProc_remarksOrPrecaution,
                        spProc_exist : this.spProc_exist,
                        spProc_name: this.spProc_name,  
                        spProc_validate :savedAs,
                        eq_id:id
                
                    })
                    //If the special process is added succesfuly
                    .then(response =>{
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the special process take the value of the newlly created id
                        this.spProc_id=response.data;
                        //The validate option of this special process take the value of savedAs(Params of the function)
                        this.spProc_validate=savedAs;
                        
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
        updateEquipmentSpProc(savedAs, reason, lifesheet_created){
            
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
           
            axios.post('/spProc/verif',{
                    spProc_remarksOrPrecaution : this.spProc_remarksOrPrecaution,
                    spProc_exist : this.spProc_exist,
                    spProc_name: this.spProc_name,
                    spProc_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the special process in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the special process who will be update*/
                    var consultUrl = (id) => `/equipment/update/spProc/${id}`;
                    axios.post(consultUrl(this.spProc_id),{
                        spProc_remarksOrPrecaution : this.spProc_remarksOrPrecaution,
                        spProc_exist : this.spProc_exist,
                        spProc_name: this.spProc_name,
                        spProc_validate :savedAs,
                        eq_id:this.equipment_id_update
                    })
                    .then(response =>{
                        this.spProc_validate=savedAs;
                        var id=this.equipment_id_update;
                        //We test if a life sheet have been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
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
        clearRadioError(){
            delete this.errors["spProc_exist"]
        }
    },
    created(){
        axios.get('/info/send/specialProcess')
        .then (response=> {
            this.infos_spProc=response.data;
            this.loaded=true;
            }) 
        .catch(error => console.log(error)) ;
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
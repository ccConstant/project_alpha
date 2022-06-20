<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container curMtnOp-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInConsultMod==true && this.curMtnOp_number!==null || this.modifMod==true && this.curMtnOp_number!==null">
                    <InputNumberForm  inputClassName="form-control w-25" :Errors="errors.curMtnOp_number" name="curMtnOp_number" label="Number :" :stepOfInput="1" v-model="curMtnOp_number" isDisabled :info_text="infos_curMtnOp[0].info_value"/>
                </div>
                <InputTextForm inputClassName="form-control w-50" :Errors="errors.curMtnOp_reportNumber" name="curMtnOp_reportNumber" label="Report number :" :isDisabled="!!isInConsultMod"  isRequired v-model="curMtnOp_reportNumber" :info_text="infos_curMtnOp[1].info_value"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.curMtnOp_description" name="curMtnOp_description" label="Description :" :isDisabled="!!isInConsultMod" v-model="curMtnOp_description" :info_text="infos_curMtnOp[2].info_value"/>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.curMtnOp_startDate" name="curMtnOp_startDate" label="Start date :" :isDisabled="true"  isRequired v-model="curMtnOp_startDate" :info_text="infos_curMtnOp[3].info_value"/>
                    <InputDateForm inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod"  isRequired v-model="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.curMtnOp_endDate" name="curMtnOp_endDate" label="End date :" :isDisabled="true"  isRequired v-model="curMtnOp_endDate" :info_text="infos_curMtnOp[4].info_value"/>
                    <InputDateForm inputClassName="form-control date-selector" name="selected_endDate"  :isDisabled="!!isInConsultMod"  isRequired v-model="selected_endDate"/>
                </div>
                <div v-if="this.addSucces==false ">
                    <!--If this preventive maintenance operation doesn't have a id the addEquipmentCurMtnOp is called function else the updateEquipmentCurMtnOp function is called -->
                    <div v-if="this.curMtnOp_id==null ">
                        <SaveButtonForm :in_life_sheet="false" :Errors="errors.curMtnOp_validate" @add="addEquipmentCurMtnOp" @update="updateEquipmentCurMtnOp" :consultMod="this.isInConsultMod" :savedAs="curMtnOp_validate"/>
                    </div>
                    <div v-else-if="this.curMtnOp_id!==null">
                        <SaveButtonForm :in_life_sheet="false" :Errors="errors.curMtnOp_validate"  @add="addEquipmentCurMtnOp" @update="updateEquipmentCurMtnOp" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="curMtnOp_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <div v-if="isInModifMod==true">
                        <DeleteComponentButton :Errors="errors.curMtnOp_delete"  :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
                    </div>
                </div>  
            </form>
        </div>
    </div>
</template>

<script>
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputNumberForm from '../../input/InputNumberForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputDateForm from '../../input/InputDateForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import moment from 'moment'
export default {
    components : {
        InputTextAreaForm,
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputNumberForm,
        SaveButtonForm,
        DeleteComponentButton
    },
     /*--------Declartion of the differents props:--------
        number : 
        description : 
        validate: Validation option of the curative maintenance operation
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this curative maintenance operation form
        id: Id of an already created curative maintenance operation 
        eq_id: Id of the equipment in which the curative maintenance operation will be added
        
    ---------------------------------------------------*/
    props:{
        number:{
            type:String,
            default:null
        },
        reportNumber:{
            type:String
        },
        description:{
            type:String
        },
        startDate:{
            type:String,
            default:null
        },
        endDate:{
            type:String,
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
        },
        state_id:{
            type:Number
        }

    },
    data(){
        return{
            curMtnOp_number:this.number,
            curMtnOp_reportNumber:this.reportNumber,
            curMtnOp_description:this.description,
            selected_startDate:this.startDate,
            selected_endDate:this.endDate,
            curMtnOp_startDate :'',
            curMtnOp_endDate:'',
            curMtnOp_validate:this.validate,
            curMtnOp_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_state_id:this.state_id,
            equipment_id_update:this.$route.params.id,
            errors:{},
            addSucces:false,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            loaded:false,
            infos_curMtnOp:[]
        }
    },
    mounted() {
        if(this.selected_startDate!==null){
            this.curMtnOp_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.curMtnOp_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.curMtnOp_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.curMtnOp_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    created(){
        axios.get('/info/send/curativeMaintenanceOperation')
        .then (response=> {
            this.infos_curMtnOp=response.data;
            this.loaded=true;
            }) 
        .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentCurMtnOp(savedAs){
            if(!this.addSucces){
                //Id of the equipment in which the preventive maintenance operation will be added
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
                axios.post('/curMtnOp/verif',{
                    curMtnOp_reportNumber:this.curMtnOp_reportNumber,
                    curMtnOp_description:this.curMtnOp_description,
                    curMtnOp_startDate:this.selected_startDate,
                    curMtnOp_endDate:this.selected_endDate,
                    curMtnOp_validate :savedAs,
                    curMtnOp_id:this.curMtnOp_id,
                    eq_id:id,
                    state_id:this.equipment_state_id,
                    reason:'add'
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/state/curMtnOp',{
                        curMtnOp_reportNumber:this.curMtnOp_reportNumber,
                        curMtnOp_description:this.curMtnOp_description,
                        curMtnOp_startDate:this.selected_startDate,
                        curMtnOp_endDate:this.selected_endDate,
                        curMtnOp_validate :savedAs,
                        eq_id:id,
                        state_id:this.equipment_state_id,
                        enteredBy_id:this.$userId.id

                
                    })
                    //If the preventive maintenance operation is added succesfuly
                    .then(response =>{
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultMod=true;
                            this.addSucces=true
                                this.$emit('addSucces','');
                            
                        }
                        //the id of the preventive maintenance operation take the value of the newlly created id
                        this.curMtnOp_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.curMtnOp_validate=savedAs;
                        
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
        updateEquipmentCurMtnOp(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
                console.log(this.curMtnOp_id)
            axios.post('/curMtnOp/verif',{
                    curMtnOp_reportNumber:this.curMtnOp_reportNumber,
                    curMtnOp_description:this.curMtnOp_description,
                    curMtnOp_startDate:this.selected_startDate,
                    curMtnOp_endDate:this.selected_endDate,
                    curMtnOp_validate :savedAs,
                    curMtnOp_id:this.curMtnOp_id,
                    eq_id:this.equipment_id_update,
                    state_id:this.equipment_state_id,
                    reason:'update'
                })
                .then(response =>{
                    console.log("update dans la base");
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/equipment/update/state/curMtnOp/${id}`;
                    axios.post(consultUrl(this.curMtnOp_id),{
                        curMtnOp_reportNumber:this.curMtnOp_reportNumber,
                        curMtnOp_description:this.curMtnOp_description,
                        curMtnOp_startDate:this.selected_startDate,
                        curMtnOp_endDate:this.selected_endDate,
                        curMtnOp_validate :savedAs,
                        eq_id:this.equipment_id_update,
                        curMtnOp_validate :savedAs,
                        state_id:this.equipment_state_id

                    })
                    .then(response =>{this.curMtnOp_validate=savedAs;})
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
        //Function for deleting a preventive maintenance operation from the view and the database
        deleteComponent(){
            //If the user is in update mode and the preventive maintenance operation exist in the database
            if(this.modifMod==true && this.curMtnOp_id!==null){
                //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                var consultUrl = (id) => `/state/delete/curMtnOp/${id}`;
                axios.post(consultUrl(this.curMtnOp_id),{
                    eq_id:this.equipment_id_update,
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteCurMtnOp','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }
            
        }
    }

}
</script>

<style>

</style>
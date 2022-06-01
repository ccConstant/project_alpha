<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <form class="container prvMtnOpRlz-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <PrvMtnOpChooseModal :prvMtnOps="prvMtnOps" @choosedOpe="choosedOpe"/>
                <div v-if="prvMtnOp_number!==null">
                    <InputTextForm inputClassName="form-control w-50"  name="prvMtnOp_number" label="Number :" isDisabled  isRequired v-model="prvMtnOp_number"/>
                    <InputTextAreaForm inputClassName="form-control w-50" name="prvMtnOp_description" label="Description :" isDisabled  isRequired v-model="prvMtnOp_description"/>
                    <InputTextAreaForm inputClassName="form-control w-50" name="prvMtnOp_protocol" label="Protocol :" isDisabled  isRequired v-model="prvMtnOp_protocol"/>
                </div>


                
                <InputTextForm inputClassName="form-control w-50" :Errors="errors.prvMtnOpRlz_reportNumber" name="prvMtnOpRlz_reportNumber" label="Report number :" :isDisabled="!!isInConsultMod"  isRequired v-model="prvMtnOpRlz_reportNumber"/>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.prvMtnOpRlz_startDate" name="prvMtnOpRlz_startDate" label="Start date :" :isDisabled="true"  isRequired v-model="prvMtnOpRlz_startDate"/>
                    <InputDateForm inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod"  isRequired v-model="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.prvMtnOpRlz_endDate" name="prvMtnOpRlz_endDate" label="End date :" :isDisabled="true"  isRequired v-model="prvMtnOpRlz_endDate"/>
                    <InputDateForm inputClassName="form-control date-selector" name="selected_endDate"  :isDisabled="!!isInConsultMod"  isRequired v-model="selected_endDate"/>
                </div>
                <div v-if="this.prvMtnOp_id!==null">
                    <div v-if="this.addSucces==false">
                        <!--If this preventive maintenance operation doesn't have a id the addEquipmentPrvMtnOpRlzMtnOp is called function else the updateEquipmentPrvMtnOpRlz function is called -->
                        <div v-if="this.prvMtnOpRlz_id==null ">
                            <SaveButtonForm @add="addEquipmentPrvMtnOpRlz" @update="updateEquipmentPrvMtnOpRlz" :consultMod="this.isInConsultMod" :savedAs="prvMtnOpRlz_validate"/>
                        </div>
                        <div v-else-if="this.prvMtnOpRlz_id!==null">
                            <SaveButtonForm  @add="addEquipmentPrvMtnOpRlz" @update="updateEquipmentPrvMtnOpRlz" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="prvMtnOpRlz_validate"/>
                        </div>
                        <!-- If the user is not in the consultation mode, the delete button appear -->
                        <div v-if="isInModifMod==true">
                            <DeleteComponentButton :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
                        </div>
                    </div>  

                </div>

            </form>
        </div>
    </div>
</template>

<script>
import InputTextForm from '../../input/InputTextForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputDateForm from '../../input/InputDateForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import PrvMtnOpChooseModal from './PrvMtnOpChooseModal.vue'
import moment from 'moment'
export default {
    components : {
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        PrvMtnOpChooseModal
    },
         /*--------Declartion of the differents props:--------
        number : 
        description : 

        
    ---------------------------------------------------*/
    props:{
        number:{
            type:String,
            default:null
        },
        reportNumber:{
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
            prvMtnOpRlz_number:this.number,
            prvMtnOpRlz_reportNumber:this.reportNumber,
            selected_startDate:this.startDate,
            selected_endDate:this.endDate,
            prvMtnOpRlz_startDate :'',
            prvMtnOpRlz_endDate:'',
            prvMtnOpRlz_validate:this.validate,
            prvMtnOpRlz_id:this.id,
            prvMtnOps:[],
            equipment_id_add:this.eq_id,
            equipment_state_id:this.state_id,
            equipment_id_update:this.$route.params.id,
            errors:{},
            addSucces:false,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            loaded:false,
            prvMtnOp_number:null,
            prvMtnOp_description:null,
            prvMtnOp_protocol:null,
            prvMtnOp_id:null

        }
    },
    mounted() {
        if(this.selected_startDate!==null){
            this.prvMtnOpRlz_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.prvMtnOpRlz_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.prvMtnOpRlz_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.prvMtnOpRlz_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentPrvMtnOpRlz(savedAs){
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
                console.log("REport number:",this.prvMtnOpRlz_reportNumber,"\n","validate :",savedAs,"\nEQ ID: ",id,"\nState id:",this.equipment_state_id,"\nprvmtnOp:",this.prvMtnOp_id)
                axios.post('/prvMtnOpRlz/verif',{
                    prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                    prvMtnOpRlz_startDate:this.selected_startDate,
                    prvMtnOpRlz_endDate:this.selected_endDate,
                    prvMtnOpRlz_validate :savedAs,
                    state_id:this.equipment_state_id,
                    prvMtnOp_id:this.prvMtnOp_id
                })
                .then(response =>{
                    console.log("ajout dans la base")
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/state/prvMtnOpRlz',{
                        prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                        prvMtnOpRlz_startDate:this.selected_startDate,
                        prvMtnOpRlz_endDate:this.selected_endDate,
                        prvMtnOpRlz_validate :savedAs,
                        eq_id:id,
                        state_id:this.equipment_state_id,
                        prvMtnOp_id:this.prvMtnOp_id
                
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
                        this.prvMtnOpRlz_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.prvMtnOpRlz_validate=savedAs;
                        
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
        updateEquipmentPrvMtnOpRlz(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            axios.post('/prvMtnOpRlz/verif',{
                    prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                    prvMtnOpRlz_startDate:this.selected_startDate,
                    prvMtnOpRlz_endDate:this.selected_endDate,
                    prvMtnOpRlz_validate :savedAs,
                    state_id:this.equipment_state_id,
                    prvMtnOp_id:this.prvMtnOp_id
                })
                .then(response =>{
                    console.log("update dans la base");
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/equipment/update/state/prvMtnOpRlz/${id}`;
                    axios.post(consultUrl(this.prvMtnOpRlz_id),{
                        prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                        prvMtnOpRlz_startDate:this.selected_startDate,
                        prvMtnOpRlz_endDate:this.selected_endDate,
                        prvMtnOpRlz_validate :savedAs,
                        eq_id:this.equipment_id_update,
                        prvMtnOpRlz_validate :savedAs,
                        state_id:this.equipment_state_id,
                        prvMtnOp_id:this.prvMtnOp_id

                    })
                    .then(response =>{this.prvMtnOpRlz_validate=savedAs;})
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
            //Emit to the parent component that we want to delete this component
            this.$emit('deletePrvMtnOpRlz','')
            //If the user is in update mode and the preventive maintenance operation exist in the database
            if(this.modifMod==true && this.curMtnOp_id!==null){
                console.log("supression");
                //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/prvMtnOpRlz/${id}`;
                axios.post(consultUrl(this.prvMtnOpRlz_id),{
                    eq_id:this.equipment_id_update,
                })
                .then(response =>{})
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }
            
        },
        choosedOpe(value){
            
            this.prvMtnOp_number=value.prvMtnOp_number;
            this.prvMtnOp_description=value.prvMtnOp_description;
            this.prvMtnOp_protocol=value.prvMtnOp_protocol,
            this.prvMtnOp_id=value.id
            console.log(value)
        }
    },
    created(){
        var consultUrl = (id) => `/prvMtnOp/send/validated/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response =>{
                this.prvMtnOps=response.data;
                this.loaded=true;
                })
            .catch(error => console.log(error));

    }

}
</script>

<style>

</style>
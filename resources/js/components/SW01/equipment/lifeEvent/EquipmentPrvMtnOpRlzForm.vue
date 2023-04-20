<!--File name : EquipmentPrvMtnOpRlz.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component to show the form of a preventive maintenance already realized-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <form class="container prvMtnOpRlz-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <PrvMtnOpChooseModal v-if="isInModifMod==false && isInConsultMod==false" :prvMtnOps="prvMtnOps" @choosedOpe="choosedOpe"/>
                <div v-if="prvMtnOp_number!==null  ">
                    <InputTextForm inputClassName="form-control w-50"  name="prvMtnOp_number" label="Number :" isDisabled  isRequired v-model="prvMtnOp_number" :info_text="infos_prvMtnOp[3].info_value"/>
                    <InputTextAreaForm inputClassName="form-control w-50" name="prvMtnOp_description" label="Description :" isDisabled  isRequired v-model="prvMtnOp_description" :info_text="infos_prvMtnOp[0].info_value"/>
                    <InputTextAreaForm inputClassName="form-control w-50" name="prvMtnOp_protocol" label="Protocol :" isDisabled  isRequired v-model="prvMtnOp_protocol" :info_text="infos_prvMtnOp[4].info_value"/>
                </div>



                <InputTextForm inputClassName="form-control w-50" :Errors="errors.prvMtnOpRlz_reportNumber" name="prvMtnOpRlz_reportNumber" label="Report number :" :isDisabled="!!isInConsultMod"  isRequired v-model="prvMtnOpRlz_reportNumber" :info_text="infos_prvMtnOpRlz[0].info_value"/>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :placeholer="'Operation date :'+prvMtnOp_startDate_placeholer" :Errors="errors.prvMtnOpRlz_startDate" name="prvMtnOpRlz_startDate" label="Start date :" :isDisabled="true"  isRequired v-model="prvMtnOpRlz_startDate" :info_text="infos_prvMtnOpRlz[1].info_value"/>
                    <InputDateForm  inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod"  isRequired v-model="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.prvMtnOpRlz_endDate" name="prvMtnOpRlz_endDate" label="End date :" :isDisabled="true"  isRequired v-model="prvMtnOpRlz_endDate" :info_text="infos_prvMtnOpRlz[2].info_value"/>
                    <InputDateForm  inputClassName="form-control date-selector" name="selected_endDate"  :isDisabled="!!isInConsultMod"  isRequired v-model="selected_endDate"/>
                </div>
                <div v-if="this.prvMtnOp_id!==null">
                    <div v-if="this.addSucces==false">
                        <!--If this preventive maintenance operation doesn't have a id the addEquipmentPrvMtnOpRlzMtnOp is called function else the updateEquipmentPrvMtnOpRlz function is called -->
                        <div v-if="this.prvMtnOpRlz_id==null ">
                            <SaveButtonForm :is_op="true" :Errors="errors.prvMtnOpRlz_validate" @add="addEquipmentPrvMtnOpRlz" @update="updateEquipmentPrvMtnOpRlz" :consultMod="this.isInConsultMod" :savedAs="prvMtnOpRlz_validate"/>
                        </div>
                        <div v-else-if="this.prvMtnOpRlz_id!==null">
                            <SaveButtonForm :is_op="true" :Errors="errors.prvMtnOpRlz_validate"  @add="addEquipmentPrvMtnOpRlz" @update="updateEquipmentPrvMtnOpRlz" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="prvMtnOpRlz_validate"/>
                        </div>
                        <!-- If the user is not in the consultation mode, the delete button appear -->
                        <div v-if="isInModifMod==true">
                            <DeleteComponentButton :Errors="errors.prvMtnOpRlz_delete" :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
                        </div>
                    </div>

                </div>

            </form>
        </div>
        <SuccesAlert ref="succesAlert"/>
    </div>
</template>

<script>
import InputDateForm from '../../../input/InputDateForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import PrvMtnOpChooseModal from './PrvMtnOpChooseModal.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'
import moment from 'moment'
export default {
    components : {
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        PrvMtnOpChooseModal,
        SuccesAlert
    },
         /*--------Declaration of the differents props:--------
        number: Unused prop
        reportNumber: The number of the report linked to the preventive maintenance operation realized
        startDate: The date when begin the current preventive maintenance operation realized
        endDate: The date when will finish the preventive maintenance operation realized
        validate: It can be "drafted", "to_be_validated" or "validated"
        consultMod: Set at true if the form is in consultation mode, in case we set all the component at disabled, and we hide the save button
        modifMod: Set at true if the form is in modification mode, else it is in add mode
        divClass: The class of the preventive maintenance operation
        id: The identification number of the preventive maintenance operation realized
        eq_id: The identification number of the affected equipment by the preventive maintenance operation realized
        state_id: The identification number of the state of the affected equipment by the preventive maintenance operation realized
        prvMtnOp_number_prop: The number the preventive maintenance operation
        prvMtnOp_description_prop: The description of the preventive maintenance operation
        prvMtnOp_protocol_prop: The protocol applicable during the preventive maintenance operation
        prvMtnOp_id_prop: The id of the preventive maintenance operation
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
        },
        prvMtnOp_number_prop:{
            type:String,
            default:null
        },
        prvMtnOp_description_prop:{
            type:String,
            default:null
        },
        prvMtnOp_protocol_prop:{
            type:String,
            default:null
        },
        prvMtnOp_id_prop:{
            type:Number,
            default:null
        }

    },
    data(){
        return{
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
            prvMtnOp_number:this.prvMtnOp_number_prop,
            prvMtnOp_description:this.prvMtnOp_description_prop,
            prvMtnOp_protocol:this.prvMtnOp_protocol_prop,
            prvMtnOp_id:this.prvMtnOp_id_prop,
            infos_prvMtnOp:[],
            infos_prvMtnOpRlz:[],
            prvMtnOp_startDate_placeholer:''
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
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated */
        addEquipmentPrvMtnOpRlz(savedAs){
            if(!this.addSucces){
                //ID of the equipment affected by the preventive maintenance operation
                let id;
                //If the user is not in the modification mode, we allocate to the id the value of the id get with the data equipment_id_add
                if(!this.modifMod){
                        id=this.equipment_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.equipment_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                The reportNumber, the start and end date, validate enum, the id of the state, the id of the affected equipment and the id of maintenance operation are sent to the controller*/
                // console.log("Report number:",this.prvMtnOpRlz_reportNumber,"\n","validate :",savedAs,"\nEQ ID: ",id,"\nState id:",this.equipment_state_id,"\nprvmtnOp:",this.prvMtnOp_id)
                axios.post('/prvMtnOpRlz/verif',{
                    prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                    prvMtnOpRlz_startDate:this.selected_startDate,
                    prvMtnOpRlz_endDate:this.selected_endDate,
                    prvMtnOpRlz_validate :savedAs,
                    state_id:this.equipment_state_id,
                    prvMtnOp_id:this.prvMtnOp_id,
                    eq_id:id,
                    reason:'add'
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verification passed, a new post this time to add the preventive maintenance operation in the database
                    The reportNumber, the start and end date, validate enum, the id of the state, the id of the affected equipment
                    and the id of maintenance operation are sent to the controller*/
                    axios.post('/equipment/add/state/prvMtnOpRlz',{
                        prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                        prvMtnOpRlz_startDate:this.selected_startDate,
                        prvMtnOpRlz_endDate:this.selected_endDate,
                        prvMtnOpRlz_validate :savedAs,
                        eq_id:id,
                        state_id:this.equipment_state_id,
                        prvMtnOp_id:this.prvMtnOp_id,
                        enteredBy_id:this.$userId.id

                    })
                    //If the preventive maintenance operation is added succesfuly
                    .then(response =>{
                        this.$refs.succesAlert.showAlert(`Equipment preventive maintenance operation realized added successfully and saved as ${savedAs}`);
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
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params :
            savedAs : Value of the validation option : drafted, to_be_validated or validated  */
        updateEquipmentPrvMtnOpRlz(savedAs){
            /*First post to verify if all the fields are filled correctly
               The reportNumber, the start and end date, validate enum, the id of the state, the id of the affected equipment and the id of maintenance operation are sent to the controller*/
            axios.post('/prvMtnOpRlz/verif',{
                    prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                    prvMtnOpRlz_startDate:this.selected_startDate,
                    prvMtnOpRlz_endDate:this.selected_endDate,
                    prvMtnOpRlz_validate :savedAs,
                    state_id:this.equipment_state_id,
                    prvMtnOp_id:this.prvMtnOp_id,
                    prvMtnOpRlz_id:this.prvMtnOpRlz_id,
                    eq_id:this.equipment_id_update,
                    reason:'update'
                })
                .then(response =>{
                    console.log("update dans la base");
                    /*If all the verification passed, a new post this time to add the preventive maintenance operation in the data base
                    The reportNumber, the start and end date, validate enum, the id of the state, the id of the affected equipment and the id of maintenance operation are sent to the controller
                    In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/equipment/update/state/prvMtnOpRlz/${id}`;
                    axios.post(consultUrl(this.prvMtnOpRlz_id),{
                        prvMtnOpRlz_reportNumber:this.prvMtnOpRlz_reportNumber,
                        prvMtnOpRlz_startDate:this.selected_startDate,
                        prvMtnOpRlz_endDate:this.selected_endDate,
                        prvMtnOpRlz_validate :savedAs,
                        eq_id:this.equipment_id_update,
                        state_id:this.equipment_state_id,
                        prvMtnOp_id:this.prvMtnOp_id

                    })
                    .then(response =>{
                        this.prvMtnOpRlz_validate=savedAs;
                        this.$refs.succesAlert.showAlert(`Equipment preventive maintenance operation realized updated successfully and saved as ${savedAs}`);
                        })
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a preventive maintenance operation from the view and the database
        deleteComponent(){

            //If the user is in update mode and the preventive maintenance operation exist in the database
            if(this.modifMod==true && this.prvMtnOpRlz_id!==null){
                console.log("supression");
                //Send a post-request with the id of the preventive maintenance operation who will be deleted in the url
                const consultUrl = (id) => `/state/delete/prvMtnOpRlz/${id}`;
                axios.post(consultUrl(this.prvMtnOpRlz_id),{
                    eq_id:this.equipment_id_update,
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deletePrvMtnOpRlz','')
                })
                //If the controller sends errors we put it in the errors object
                .catch(error => this.errors=error.response.data.errors) ;

            }

        },
        choosedOpe(value){
            this.prvMtnOp_number=value.prvMtnOp_number;
            this.prvMtnOp_description=value.prvMtnOp_description;
            this.prvMtnOp_protocol=value.prvMtnOp_protocol;
            this.prvMtnOp_startDate_placeholer=value.prvMtnOp_nextDate;
            this.prvMtnOp_id=value.id;
        }
    },
    created(){
        if(this.isInConsultMod==false && this.isInModifMod==false){
            console.log("creation");
            console.log(this.eq_id);
            const consultUrl = (id) => `/prvMtnOp/send/validated/${id}`;
            axios.get(consultUrl(this.eq_id))
                .then (response =>{
                    this.prvMtnOps=response.data;
                    console.log(response.data);
                    })
                .catch(error => console.log(error));
        }
        axios.get('/info/send/preventiveMaintenanceOperation')
        .then (response=> {
            this.infos_prvMtnOp=response.data;
        })
        .catch(error => console.log(error)) ;

        axios.get('/info/send/preventiveMaintenanceOperationRealized')
            .then (response=> {
                this.infos_prvMtnOpRlz=response.data;
                this.loaded=true;
                })
            .catch(error => console.log(error)) ;

    }

}
</script>

<style>

</style>

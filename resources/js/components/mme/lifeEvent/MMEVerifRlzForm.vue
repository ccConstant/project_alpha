<template>
      <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <form class="container verifRlz-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <VerifChooseModal v-if="isInModifMod==false && isInConsultMod==false" :verifs="verifs" @choosedOpe="choosedOpe"/>
                <div v-if="verif_number!==null  ">
                    <InputTextForm :info_text="infos_verif[0].info_value" inputClassName="form-control w-50"  name="verif_number" label="Number :" isDisabled  v-model="verif_number"/>
                    <InputTextAreaForm :info_text="infos_verif[2].info_value" inputClassName="form-control w-50" name="verif_expectedResult" label="Expected Result :" isDisabled   v-model="verif_expectedResult" />
                    <InputTextAreaForm :info_text="infos_verif[3].info_value" inputClassName="form-control w-50" name="verif_nonComplianceLimit" label="Non Compliance Limit :" isDisabled   v-model="verif_nonComplianceLimit" />
                    <InputTextAreaForm :info_text="infos_verif[7].info_value" inputClassName="form-control w-50" name="verif_description" label="Description :" isDisabled   v-model="verif_description" />
                    <InputTextAreaForm :info_text="infos_verif[8].info_value" inputClassName="form-control w-50" name="verif_protocol" label="Protocol :" isDisabled   v-model="verif_protocol"/>
                </div>


                
                <InputTextForm :info_text="infos_verifRlz[0].info_value" inputClassName="form-control w-50" :Errors="errors.verifRlz_reportNumber" name="verifRlz_reportNumber" label="Report number :" :isDisabled="!!isInConsultMod"  v-model="verifRlz_reportNumber"/>
                <div class="input-group">
                    <InputTextForm :info_text="infos_verifRlz[1].info_value" inputClassName="form-control" :placeholer="'Operation date :'+verif_startDate_placeholer" :Errors="errors.verifRlz_startDate" name="verifRlz_startDate" label="Start date :" :isDisabled="true" v-model="verifRlz_startDate" />
                    <InputDateForm  inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod"  v-model="selected_startDate"/>
                </div>
                <div class="input-group">
                    <InputTextForm :info_text="infos_verifRlz[2].info_value" inputClassName="form-control" :Errors="errors.verifRlz_endDate" name="verifRlz_endDate" label="End date :" :isDisabled="true" v-model="verifRlz_endDate" />
                    <InputDateForm inputClassName="form-control date-selector" name="selected_endDate"  :isDisabled="!!isInConsultMod" v-model="selected_endDate"/>
                </div>
                <RadioGroupForm :info_text="infos_verifRlz[3].info_value" label="is Passed?:"  :options="isPassedOption" :Errors="errors.verifRlz_isPassed" :checkedOption="verifRlz_isPassed" :isDisabled="!!isInConsultMod" v-model="verifRlz_isPassed"/> 
                <div v-if="this.verif_id!==null">
                    <div v-if="this.addSucces==false">
                        <!--If this preventive maintenance operation doesn't have a id the addMmeVerifRlz is called function else the updateMmeVerifRlz function is called -->
                        <div v-if="this.verifRlz_id==null ">
                            <SaveButtonForm :is_op="true" :Errors="errors.verifRlz_validate" @add="addMmeVerifRlz" @update="updateMmeVerifRlz" :consultMod="this.isInConsultMod" :savedAs="verifRlz_validate"/>
                        </div>
                        <div v-else-if="this.verifRlz_id!==null">
                            <SaveButtonForm :is_op="true" :Errors="errors.verifRlz_validate"  @add="addMmeVerifRlz" @update="updateMmeVerifRlz" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="verifRlz_validate"/>
                        </div>
                        <!-- If the user is not in the consultation mode, the delete button appear -->
                        <div v-if="isInModifMod==true">
                            <DeleteComponentButton :Errors="errors.verifRlz_delete" :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
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
import InputDateForm from '../../input/InputDateForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import VerifChooseModal from './VerifChooseModal.vue'
import moment from 'moment'
export default {
        components : {
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        VerifChooseModal
    },
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
        isPassed:{
            type:Boolean
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
        mme_id:{
            type:Number
        },
        state_id:{
            type:Number
        },
        verif_expectedResult_prop:{
            type:String,
            default:null
        },
        verif_nonComplianceLimit_prop:{
            type:String,
            default:null
        },
        verif_number_prop:{
            type:String,
            default:null
        },
        verif_description_prop:{
            type:String,
            default:null
        },
        verif_protocol_prop:{
            type:String,
            default:null
        },
        verif_id_prop:{
            type:Number,
            default:null
        }

    },
    data(){
        return{
            verifRlz_reportNumber:this.reportNumber,
            selected_startDate:this.startDate,
            selected_endDate:this.endDate,
            verifRlz_startDate :'',
            verifRlz_endDate:'',
            verifRlz_isPassed:this.isPassed,
            verifRlz_validate:this.validate,
            verifRlz_id:this.id,
            verifs:[],
            isPassedOption:[
                {id: 'Yes', value:true},
                {id : 'No', value:false}
            ],
            mme_id_add:this.mme_id,
            mme_state_id:this.state_id,
            mme_id_update:this.$route.params.id,
            errors:{},
            addSucces:false,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            loaded:false,
            verif_number:this.verif_number_prop,
            verif_expectedResult:this.verif_expectedResult_prop,
            verif_nonComplianceLimit:this.verif_nonComplianceLimit_prop,
            verif_description:this.verif_description_prop,
            verif_protocol:this.verif_protocol_prop,
            verif_id:this.verif_id_prop,
            infos_verif:[],
            infos_verifRlz:[],
            verif_startDate_placeholer:''
        }
    },
    mounted() {
        if(this.selected_startDate!==null){
            this.verifRlz_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.verifRlz_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.verifRlz_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
        if(this.selected_endDate!==null){
            this.verifRlz_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    methods:{
        /*Sending to the controller all the information about the mme so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addMmeVerifRlz(savedAs){
            if(!this.addSucces){
                //Id of the mme in which the preventive maintenance operation will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data mme_id_add 
                if(!this.modifMod){
                        id=this.mme_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.mme_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
                console.log(this.verif_id)
                console.log(this.mme_state_id)
                console.log(savedAs)


                axios.post('/verifRlz/verif',{
                    verifRlz_reportNumber:this.verifRlz_reportNumber,
                    verifRlz_startDate:this.selected_startDate,
                    verifRlz_endDate:this.selected_endDate,
                    verifRlz_isPassed:this. verifRlz_isPassed,
                    verifRlz_validate :savedAs,
                    state_id:this.mme_state_id,
                    verif_id:this.verif_id,
                    mme_id:id,
                    reason:'add'
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the mmme is sended to the controller*/
                    axios.post('/mme/add/mme_state/verifRlz',{
                        verifRlz_reportNumber:this.verifRlz_reportNumber,
                        verifRlz_startDate:this.selected_startDate,
                        verifRlz_endDate:this.selected_endDate,
                        verifRlz_isPassed:this. verifRlz_isPassed,
                        verifRlz_validate :savedAs,
                        mme_id:id,
                        state_id:this.mme_state_id,
                        verif_id:this.verif_id,
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
                        this.verifRlz_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.verifRlz_validate=savedAs;
                        
                    })
                    //If the controller sends errors we put it in the errors object 
                    .catch(error => this.errors=error.response.data.errors) ;
                ;})
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
        /*Sending to the controller all the information about the mme so that it can be updated in the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        updateMmeVerifRlz(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            axios.post('/verifRlz/verif',{
                    verifRlz_reportNumber:this.verifRlz_reportNumber,
                    verifRlz_startDate:this.selected_startDate,
                    verifRlz_endDate:this.selected_endDate,
                    verifRlz_isPassed:this. verifRlz_isPassed,
                    verifRlz_validate :savedAs,
                    verifRlz_id:this.verifRlz_id,
                    state_id:this.mme_state_id,
                    verif_id:this.verif_id,
                    mme_id:this.mme_id_update,
                    reason:'update'
                })
                .then(response =>{
                    console.log("update dans la base");
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the mme is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/mme/update/mme_state/verifRlz/${id}`;
                    axios.post(consultUrl(this.verifRlz_id),{
                        verifRlz_reportNumber:this.verifRlz_reportNumber,
                        verifRlz_startDate:this.selected_startDate,
                        verifRlz_endDate:this.selected_endDate,
                        verifRlz_isPassed:this. verifRlz_isPassed,
                        verifRlz_validate :savedAs,
                        mme_id:this.mme_id_update,
                        state_id:this.mme_state_id,
                        verif_id:this.verif_id

                    })
                    .then(response =>{this.verifRlz_validate=savedAs;})
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
            if(this.modifMod==true && this.verifRlz_id!==null){
                console.log("supression");
                //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                var consultUrl = (id) => `/mme_state/delete/verifRlz/${id}`;
                axios.post(consultUrl(this.verifRlz_id),{
                    mme_id:this.mme_id_update,
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteVerifRlz','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }
            
        },
        choosedOpe(value){
            this.verif_number=value.verif_number;
            this.verif_expectedResult=value.verif_expectedResult,
            this.verif_nonComplianceLimit=value.verif_nonComplianceLimit,
            this.verif_description=value.verif_description;
            this.verif_protocol=value.verif_protocol;
            this.verif_startDate_placeholer=value.verif_nextDate;
            this.verif_id=value.id;
        }
    },
    created(){
        if(this.isInConsultMod==false && this.isInModifMod==false){
            var consultUrl = (id) => `/verif/send/validated/${id}`;
            axios.get(consultUrl(this.mme_id))
                .then (response =>{
                    this.verifs=response.data;
                    console.log(response.data);
                    })
                .catch(error => console.log(error));
        }

        axios.get('/info/send/verif')
            .then (response=> {
                this.infos_verif=response.data;

                }) 
            .catch(error => console.log(error)) ;

        axios.get('/info/send/verifRlz')
            .then (response=> {
                this.loaded=true;
                this.infos_verifRlz=response.data;
                }) 
            .catch(error => console.log(error)) ;

    }


}
</script>

<style>

</style>
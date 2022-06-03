<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <form class="container state-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="state_id!==undefined || isInConsultedMod==true">
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name" isDisabled :selctedOption="state_name" v-model="state_name"/>
                </div>
                <div v-else>
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name"  :selctedOption="state_name" v-model="state_name"/>
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.state_remarks" name="state_remarks" label="Remarks :" :isDisabled="!!isInConsultedMod" v-model="state_remarks"/>
                <div class="input-group">
                    <InputTextForm inputClassName="form-control" :Errors="errors.state_startDate" name="state_startDate" label="Start date :" :isDisabled="true"  isRequired v-model="state_startDate"/>
                    <InputDateForm inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod"  isRequired v-model="selected_startDate"/>
                </div>
                <RadioGroupForm label="is Ok?:" :options="isOkOptions" :Errors="errors.state_isOk" :checkedOption="state_isOk" :isDisabled="!!isInConsultMod" v-model="state_isOk"/> 
                <SaveButtonForm v-if="this.addSucces==false" @add="addEquipmentState" @update="updateEquipmentState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
            </form>

        </div>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->

    </div>
</template>

<script>
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputDateForm from '../../input/InputDateForm.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import moment from 'moment'

export default {
    components : {
        InputTextAreaForm,
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm
    },
    props:{
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
    },
    data(){
        return{
            state_name:'',
            state_remarks: '',
            selected_startDate:null,
            state_startDate :'',
            state_validate:'',
            state_isOk:null,
            enum_state_name :[
                {value:"Waiting_to_be_in_use"},
                {value:"In_use"},
                {value:"Broken_down"},
                {value:"Broken"},
                {value:"Downgraded"},
                {value:"Reform"},
                {value:"Lost"},
                {value:"Return_to_service_use"},
                {value:"Waiting_for_referencing"},
            ],
            isOkOptions :[
                {id: 'Yes', value:true},
                {id : 'No', value:false}
            ],
            isInConsultedMod:this.consultMod,
            eq_id:this.$route.params.id,
            state_id:this.$route.params.state_id,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            errors:{},
            addSucces:false,
            loaded:false

        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.state_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
    },
    methods:{
        addEquipmentState(savedAs){
            if(!this.addSucces){
                console.log("ADD nom:", this.state_name,"\n","remark:", this.state_remarks,"\n","startDate:", this.selected_startDate
                ,"\n","\n","isOK:", this.state_isOk,"\n","validate:", savedAs,"\n",)
                axios.post('/state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:savedAs,
                    eq_id:this.eq_id,
                    reason:'add'
                })
                .then(response =>{
                    //console.log(response.data)
                        axios.post('/equipment/add/state',{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_isOk:this.state_isOk,
                            state_validate:savedAs,
                            eq_id:this.eq_id

                        })
                        .then(response =>{
                                this.addSucces=true;
                                this.isInConsultMod=true;
                                this.state_validate=savedAs
                                
                            })
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ; 
            }
        },
        /*Sending to the controller all the information about the equipment so that it can be updated to the database */ 
        updateEquipmentState(savedAs){
            console.log("UPDATE nom:", this.state_name,"\n","remark:", this.state_remarks,"\n","startDate:", this.selected_startDate
                ,"\n","isOK:", this.state_isOk,"\n","validate:", savedAs,"\n","state id",this.state_id,"eq id:",this.eq_id)
                axios.post('/state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:savedAs,
                    state_id:this.state_id,
                    eq_id:this.eq_id,
                    reason:'update'
                    

                })
                .then(response =>{
                        var consultUrl = (id) => `/equipment/update/state/${id}`;
                        axios.post(consultUrl(this.state_id),{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_isOk:this.state_isOk,
                            state_validate:savedAs,
                            eq_id:this.eq_id

                        })
                        .then(response => {this.state_validate=savedAs})
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        
        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
    },
    created(){
        /*Ask for the controller other equipments sets */
        if(this.state_id!=undefined){
            if(this.isInConsultedMod==false){
                this.isInModifMod=true;
            }
            
            var UrlState = (id) => `/state/send/${id}`;
            axios.get(UrlState(this.state_id))
                .then (response=>{
                    console.log(response.data)
                    this.state_name=response.data[0].state_name;
                    this.state_remarks=response.data[0].state_remarks;
                    this.selected_startDate=response.data[0].state_startDate;
                    this.state_isOk=response.data[0].state_isOk;
                    this.state_validate=response.data[0].state_validate;
                    this.loaded=true;
                })
                .catch(error => console.log(error)) ;
        }



    }
}
</script>

<style lang="scss">
    .state-form{
        .date-selector{
            width: 44px;
            margin-top:8px
        }    
    }
    

</style>
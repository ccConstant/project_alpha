<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <ErrorAlert ref="errorAlert"/>
            <SuccesAlert ref="succesAlert"/>
            <form class="container state-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInModifMod">
                    <h2 >Update the state</h2>
                </div>
                <div v-else>
                    <h2>Change the State</h2>
                    <div v-if="isInConsultMod==false">
                        <InputTextForm  inputClassName="form-control w-50" name="current_state" label="Current state :" :isDisabled="true"   v-model="current_state" />
                        <InputTextForm  inputClassName="form-control w-50" name="current_startDate" label="Current state start Date :" :isDisabled="true"   v-model="current_startDate"  />

                    </div>
                </div>
                <div v-if="state_id!==undefined || isInConsultMod==true">
                    <InputSelectForm selectClassName="form-select w-50" :info_text="infos_state[0].info_value" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name" isDisabled :selctedOption="state_name" v-model="state_name" />
                </div>
                <div v-else>
                    <InputSelectForm selectClassName="form-select w-50" :info_text="infos_state[0].info_value" :Errors="errors.state_name"  name="state_name" label="State name :" :options="enum_state_name"  :selctedOption="state_name" v-model="state_name" />
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :info_text="infos_state[1].info_value" :Errors="errors.state_remarks" name="state_remarks" label="Remarks :" :isDisabled="!!isInConsultMod" v-model="state_remarks" />
                <div class="input-group">
                    <InputTextForm  inputClassName="form-control" :info_text="infos_state[2].info_value" :Errors="errors.state_startDate" name="state_startDate" label="Start date :" :isDisabled="true" v-model="state_startDate" />
                    <InputDateForm @clearDateError="clearDateError" inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod" v-model="selected_startDate" />
                </div>
                <RadioGroupForm label="is Ok?:" :info_text="infos_state[4].info_value" :options="isOkOptions"  :Errors="errors.state_isOk" :checkedOption="state_isOk" :isDisabled="!!isInConsultMod" v-model="state_isOk" /> 
                <SaveButtonForm :is_state="true" :Errors="errors.mme_delete" v-if="this.addSucces==false" @add="addMmeState" @update="updateMmeState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
            </form>


            <div v-if="state_name=='Downgraded'">
                <div v-if="state_validate=='validated'">
                    <div v-if="!isEmpty(mme_idCard)">
                        <MmeIdForm :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
                            :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
                            :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                            consultMod />
                    </div>
                    <div v-else>
                        <RadioGroupForm label="Do you want to reference a new mme ?:" :options="radioOption" v-model="new_mme"/>
                        <MmeIdForm :disableImport="true" v-if="new_mme==true" :state_id="state_id"
                        :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
                        :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
                        :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"/>
                    </div>
                </div>
            </div>

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
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ErrorAlert from '../../alert/ErrorAlert.vue'
import SuccesAlert from '../../alert/SuccesAlert.vue'
export default {
    components : {
        InputTextAreaForm,
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        MmeIdForm,
        ErrorAlert,
        SuccesAlert
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
                {value:"Waiting_for_referencing"},
                {value:"Waiting_to_be_in_use"},
                {value:"In_use"},
                {value:"Under_verification"},
                {value:"In_quarantine"},
                {value:"Under_repair"},
                {value:"Broken"},
                {value:"Downgraded"},
                {value:"Reformed"},
                {value:"Lost"},
            ],
            isOkOptions :[
                {id: 'Yes', value:true},
                {id : 'No', value:false}
            ],
            isInConsultMod:this.consultMod,
            mme_id:this.$route.params.id,
            state_id:this.$route.params.state_id,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            errors:{},
            addSucces:false,
            loaded:false,
            current_state:'',
            current_startDate:'',
            radioOption :[
                {id: 'Yes', value:true},
                {id : 'No', value:false}
            ],
            new_mme:null,
            mme_idCard:[],
            infos_state:[],
            

        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.state_startDate=moment(this.selected_startDate).format('D MMM YYYY'); 
        };
    },
    methods:{
        addMmeState(savedAs){
            if (this.state_name=="Reformed" || this.state_name=="Broken" || this.state_name=="Downgraded"){
                
            }
            if(!this.addSucces){
                axios.post('/mme_state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:savedAs,
                    mme_id:this.mme_id,
                    reason:'add',
                    user_id:this.$userId.id
                })
                .then(response =>{
                    
                        this.errors={}
                        axios.post('/mme/add/state',{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_isOk:this.state_isOk,
                            state_validate:savedAs,
                            mme_id:this.mme_id,
                            enteredBy_id:this.$userId.id

                        })
                        .then(response =>{
                            console.log(response.data)
                                this.$refs.succesAlert.showAlert("State changed succesfully");
                                this.$router.replace({ name: "url_mme_life_event" })
                                this.addSucces=true;
                                this.isInConsultMod=true;
                                this.state_validate=savedAs
                                
                            })
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ; 
            }
        },
        /*Sending to the controller all the information about the mme so that it can be updated to the database */ 
        updateMmeState(savedAs){
             axios.post('/mme_state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:savedAs,
                    state_id:this.state_id,
                    mme_id:this.mme_id,
                    reason:'update'
                    

                })
                .then(response =>{
                        this.errors={}
                        var consultUrl = (id) => `/mme/update/state/${id}`;
                        axios.post(consultUrl(this.state_id),{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_isOk:this.state_isOk,
                            state_validate:savedAs,
                            mme_id:this.mme_id

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
        clearDateError(){
            delete this.errors['state_startDate'];
        },
        isEmpty(object) {
            for (const property in object) {
                return false;
            }
            return true;
        },
        warningDelete(id,refernece){
            this.$bvModal.show(`modal-deleteWarning-${this._uid}`);
        },
       
    },
    created(){
        /*Ask for the controller other mme sets */
        if(this.$userId.user_declareNewStateRight!=true){
            this.$router.push({ name: "home"})
            return;
        }


        if(this.state_id!=undefined){
            if(this.isInConsultMod==false){
                this.isInModifMod=true;
            }
            
            var UrlState = (id) => `/mme_state/send/${id}`;
            axios.get(UrlState(this.state_id))
                .then (response=>{
                    this.state_name=response.data[0].state_name;
                    this.state_remarks=response.data[0].state_remarks;
                    this.selected_startDate=response.data[0].state_startDate;
                    this.state_isOk=response.data[0].state_isOk;
                    this.state_validate=response.data[0].state_validate;
                    if(this.state_name=="Downgraded"){
                        var consultUrl = (state_id) => `/send/mme_state/mme/${state_id}`;
                        axios.get(consultUrl(this.state_id))
                            .then (response => {
                                this.mme_idCard=response.data;
                                console.log(response.data)
                                this.loaded=true})
                            .catch(error => console.log(error));
                    }else{
                        this.loaded=true
                        console.log("coco")
                    }

                })
               .catch(error => this.errors=error.response.data.errors) ;
            
        }else{
            
            var UrlState = (id) => `/mme_state/send/${id}`;
            axios.get(UrlState(this.$route.query.currentState))
                .then (response=>{
                    console.log(response.data)
                    this.current_state=response.data[0].state_name;
                    this.current_startDate=moment(response.data[0].state_startDate).format('D MMM YYYY');

                })
                .catch(error => console.log(error)) ;

        }

        axios.get('/info/send/mme_state')
            .then (response=> {
                this.infos_state=response.data;
                this.loaded=true;
            }) 
            .catch(error => console.log(error)) ;
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
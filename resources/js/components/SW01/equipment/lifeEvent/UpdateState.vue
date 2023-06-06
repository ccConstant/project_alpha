<!--File name : UpdateState.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to update a state-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <ErrorAlert ref="errorAlert"/>
            <form class="container state-form"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInModifMod">
                    <h2 >Update the state</h2>
                </div>
                <div v-else>
                    <h2>Change the State</h2>
                    <div v-if="isInConsultMod==false">
                        <InputTextForm  inputClassName="form-control w-50" name="current_state" label="Current state :" :isDisabled="true"   v-model="current_state" :info_text="infos_state[0].info_value"/>
                        <InputTextForm  inputClassName="form-control w-50" name="current_startDate" label="Current state start Date :" :isDisabled="true"   v-model="current_startDate" :info_text="infos_state[2].info_value" />
                    </div>
                </div>
                <div v-if="state_id!==undefined || isInConsultMod==true">
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  :id_actual="StateName" name="state_name" label="State name :" :options="enum_state_name" isDisabled :selctedOption="state_name" v-model="state_name" :info_text="infos_state[0].info_value"/>
                </div>
                <div v-else>
                    <InputSelectForm selectClassName="form-select w-50" :Errors="errors.state_name"  :id_actual="StateName" name="state_name" label="State name :" :options="enum_state_name"  :selctedOption="state_name" v-model="state_name" :info_text="infos_state[0].info_value"/>
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.state_remarks" name="state_remarks" label="Remarks :" :isDisabled="!!isInConsultMod" v-model="state_remarks" :info_text="infos_state[1].info_value"/>
                <div class="input-group">
                    <InputTextForm  inputClassName="form-control" :Errors="errors.state_startDate" name="state_startDate" label="Start date :" :isDisabled="true" v-model="state_startDate" :info_text="infos_state[2].info_value"/>
                    <InputDateForm @clearDateError="clearDateError" inputClassName="form-control  date-selector"  name="selected_startDate" :isDisabled="!!isInConsultMod" v-model="selected_startDate" />
                </div>
                <SaveButtonForm :is_state="true" v-if="this.addSucces==false" @add="addEquipmentState" @update="updateEquipmentState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
            </form>
            <SuccesAlert ref="succesAlert"/>
            <div v-if="state_name=='Downgraded'">
                <div v-if="state_validate=='validated'">
                    <div v-if="!isEmpty(eq_idCard)">
                        <EquipmentIDForm :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                        :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                        :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                        :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"
                        consultMod/>
                    </div>
                    <div v-else>
                        <RadioGroupForm
                            label="Do you want to reference a new equipment ?:"
                            :options="radioOption"
                            v-model="new_eq"
                            name="new_eq"
                        />
                        <EquipmentIDForm :disableImport="true" v-if="new_eq==true" :state_id="state_id"
                        :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                        :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                        :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                        :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"/>
                    </div>
                </div>
            </div>

        </div>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
    </div>
</template>

<script>
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputDateForm from '../../../input/InputDateForm.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import moment from 'moment'
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'



export default {
    components : {
        InputTextAreaForm,
        InputSelectForm,
        InputDateForm,
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        EquipmentIDForm,
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
            enum_state_name :[
                {id_enum:'StateName',value:"Waiting_for_referencing"},
                {id_enum:'StateName',value:"Waiting_for_installation"},
                {id_enum:'StateName',value:"In_use"},
                {id_enum:'StateName',value:"Under_maintenance"},
                {id_enum:'StateName',value:"On_hold"},
                {id_enum:'StateName',value:"Under_repair"},
                {id_enum:'StateName',value:"Broken"},
                {id_enum:'StateName',value:"Downgraded"},
                {id_enum:'StateName',value:"Reformed"},
                {id_enum:'StateName',value:"Lost"},
            ],
            isInConsultMod:this.consultMod,
            eq_id:this.$route.params.id,
            state_id:this.$route.params.state_id,
            isInModifMod:this.modifMod,
            errors:{},
            addSucces:false,
            loaded:false,
            current_state:'',
            current_startDate:'',
            new_eq:null,
            eq_idCard:[],
            infos_state:[],
            StateName:"StateName",
            radioOption:[
                {id_enum:'new_eq', value:true, text: 'Yes'},
                {id_enum:'new_eq', value:false, text: 'No'}
            ]
        }
    },
    updated() {
        if(this.selected_startDate!==null){
            this.state_startDate=moment(this.selected_startDate).format('D MMM YYYY');
        }
    },
    methods:{
        addEquipmentState(savedAs){
            if(this.$userId.user_declareNewStateRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right");
                return;
            }
            console.log(this.$userId.id)
            if(!this.addSucces){
                axios.post('/state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_validate:savedAs,
                    eq_id:this.eq_id,
                    reason:'add',
                    user_id:this.$userId.id
                })
                .then(response =>{
                        console.log(response.data)
                        this.errors={}
                        axios.post('/equipment/add/state',{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_validate:savedAs,
                            eq_id:this.eq_id,
                            enteredBy_id:this.$userId.id

                        })
                        .then(response =>{
                            this.$refs.succesAlert.showAlert(`Equipment state added successfully and saved as ${savedAs}`);
                            console.log(response.data)
                                this.$router.replace({ name: "url_eq_list" })
                                this.addSucces=true;
                                this.isInConsultMod=true;
                                this.state_validate=savedAs

                            })
                        .catch(error => this.errors=error.response.data.errors) ;
                    })
                .catch(error => this.errors=error.response.data.errors) ;
            }
        },
        /*Sending to the controller all the information about the state to check if all the fields are correctly filled */
        updateEquipmentState(savedAs){
                console.log("update")
                axios.post('/state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_validate:savedAs,
                    state_id:this.state_id,
                    eq_id:this.eq_id,
                    reason:'update'
                })
                .then(response =>{
                        this.errors={}
                    /* If the check does not produce error, we can update the value of the state */
                    const consultUrl = (id) => `/equipment/update/state/${id}`;
                    axios.post(consultUrl(this.state_id),{
                            state_name:this.state_name,
                            state_remarks:this.state_remarks,
                            state_startDate:this.selected_startDate,
                            state_validate:savedAs,
                            eq_id:this.eq_id
                        })
                        .then(response => {
                            this.$refs.succesAlert.showAlert(`Equipment state updated successfully and saved as ${savedAs}`);
                            this.state_validate=savedAs
                            })
                        .catch(error => this.errors=error.response.data.errors) ;
                    })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clears all the error of the targeted field*/
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
    },
    created(){
        /*Ask for the controller other equipments sets */
        if(this.$userId.user_declareNewStateRight!=true){
            this.$router.push({ name: "home"})
            return;
        }
        if(this.state_id!=undefined){
            if(this.isInConsultMod==false){
                this.isInModifMod=true;
            }

            const UrlState = (id) => `/state/send/${id}`;
            axios.get(UrlState(this.state_id))
                .then (response=>{
                    this.state_name=response.data[0].state_name;
                    this.state_remarks=response.data[0].state_remarks;
                    this.selected_startDate=response.data[0].state_startDate;
                    this.state_validate=response.data[0].state_validate;
                    if(this.state_name=="Downgraded"){
                        const consultUrl = (state_id) => `/send/state/equipment/${state_id}`;
                        axios.get(consultUrl(this.state_id))
                            .then (response => {
                                this.eq_idCard=response.data;
                                })
                            .catch(error => {
                            });
                    }
                })
                .catch(error => {
                });

        }else{
            const UrlState = (id) => `/state/send/${id}`;
            axios.get(UrlState(this.$route.query.currentState))
                .then (response=>{
                    this.current_state=response.data[0].state_name;
                    this.current_startDate=moment(response.data[0].state_startDate).format('D MMM YYYY');
                })
                .catch(error => {
                });
        }
        axios.get('/info/send/state')
            .then (response=> {
                this.infos_state=response.data;
                this.loaded=true;
            })
            .catch(error => {
            });
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

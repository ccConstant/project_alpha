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
                <RadioGroupForm
                    label="is Ok?:"
                    :options="isOkOptions"
                    :Errors="errors.state_isOk"
                    :checkedOption="state_isOk"
                    :isDisabled="!!isInConsultMod"
                    v-model="state_isOk"
                    :info_text="infos_state[3].info_value"
                />
                <div v-if="this.state_name=='Downgraded' || this.state_name=='Broken' || this.state_name=='Reformed'"> 
                    <SaveButtonForm :is_state="true" v-if="this.addSucces==false" @add="verifBeforeDelete" @update="updateEquipmentState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
                </div>
                <div v-else>
                    <SaveButtonForm :is_state="true" v-if="this.addSucces==false" @add="addEquipmentState" @update="updateEquipmentState" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" :savedAs="state_validate"/>
                </div>
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
                        <RadioGroupForm label="Do you want to reference a new equipment ?:" :options="radioOption" v-model="new_eq"/>
                        <EquipmentIDForm :disableImport="true" v-if="new_eq==true" :state_id="state_id"
                        :internalReference="eq_idCard.eq_internalReference" :externalReference="eq_idCard.eq_externalReference"
                        :name="eq_idCard.eq_name" :type="eq_idCard.eq_type" :serialNumber="eq_idCard.eq_serialNumber"
                        :construct="eq_idCard.eq_constructor" :mass="eq_idCard.eq_mass"  :massUnit="eq_idCard.eq_massUnit"
                        :mobility="eq_idCard.eq_mobility" :remarks="eq_idCard.eq_remarks" :set="eq_idCard.eq_set" :validate="eq_idCard.eq_validate"/>
                    </div>
                </div>
            </div>

            <b-modal :id="`modal-mme`"  @ok="addEquipmentStateDelete()">
                        <p class="my-4">Are you sure you want to go in this state? 
                            Equipment data will be deleted. What do we do about the following equipment related MMEs?  </p>

                        <div  v-for="(component, key) in this.mme" :key="component.key">
                            <p> Reference : {{component.mme_internalReference}} || Name : {{component.mme_name}}</p>
                        </div>
                        
                        <input type="radio" id="one" value="One" v-model="mme_decision" />
                        <label for="one">Unlink it</label>

                        <input type="radio" id="two" value="Two" v-model="mme_decision" />
                        <label for="two">Delete it</label>
            </b-modal>

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
            state_isOk:null,
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
            isOkOptions :[
                {text: 'Yes', value:true},
                {text : 'No', value:false}
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
            radioOption :[
                {id: 'Yes', value:true, text:'Yes'},
                {id : 'No', value:false, text:'No'}
            ],
            new_eq:null,
            eq_idCard:[],
            infos_state:[],
            StateName:"StateName",
            mme:[],
            mme_decision:'',
            savedAs:''
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
                    state_isOk:this.state_isOk,
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
                            state_isOk:this.state_isOk,
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

        verifBeforeDelete(savedAs){
            this.savedAs=savedAs
            axios.post('/state/verif',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:this.savedAs,
                    eq_id:this.eq_id,
                    reason:'add',
                    user_id:this.$userId.id
            })
            .then(response =>{
                console.log("deb function")
                if(this.$userId.user_declareNewStateRight!=true){
                    console.log("bad")
                    this.$refs.errorAlert.showAlert("You don't have the right");
                    return;
                }
                
                console.log("suite function")
                if (this.state_name=='Downgraded' || this.state_name=='Broken' || this.state_name=='Reformed'){
                    if (true){
                        this.$bvModal.show(`modal-mme`)
                    }   
                }
            });
        },

        addEquipmentStateDelete(){
            if (this.mme_decision=='One'){
                console.log("One") 
                this.mme.forEach(element => {
                    const deleteUrl = (id) => `/mme/delete/link_to_eq/${id}`;
                    axios.post(deleteUrl(element.id)),{
                    }
                });
                console.log("mme unlunked")
            }
            if (this.mme_decision=='Two'){
                console.log("Two") 
            }
            console.log(this.$userId.id)
            if(!this.addSucces){
                this.errors={}
                axios.post('/equipment/add/state',{
                    state_name:this.state_name,
                    state_remarks:this.state_remarks,
                    state_startDate:this.selected_startDate,
                    state_isOk:this.state_isOk,
                    state_validate:this.savedAs,
                    eq_id:this.eq_id,
                    enteredBy_id:this.$userId.id

                })
                .then(response =>{
                    console.log("state added")
                    this.$refs.succesAlert.showAlert(`Equipment state added successfully and saved as ${savedAs}`);
                    console.log(response.data)
                        this.$router.replace({ name: "url_eq_list" })
                        this.addSucces=true;
                        this.isInConsultMod=true;
                        this.state_validate=this.savedAs

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
                    state_isOk:this.state_isOk,
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
                            state_isOk:this.state_isOk,
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
                    console.log(response.data)
                    this.state_name=response.data[0].state_name;
                    console.log(this.state_name)
                    this.state_remarks=response.data[0].state_remarks;
                    this.selected_startDate=response.data[0].state_startDate;
                    this.state_isOk=response.data[0].state_isOk;
                    this.state_validate=response.data[0].state_validate;
                    if(this.state_name=="Downgraded"){
                        const consultUrl = (state_id) => `/send/state/equipment/${state_id}`;
                        axios.get(consultUrl(this.state_id))
                            .then (response => {this.eq_idCard=response.data;console.log(response.data)})
                            .catch(error => console.log(error));
                    }

                })
                .catch(error => console.log(error)) ;

        }else{
            const UrlState = (id) => `/state/send/${id}`;
            axios.get(UrlState(this.$route.query.currentState))
                .then (response=>{
                    console.log(response.data)
                    this.current_state=response.data[0].state_name;
                    this.current_startDate=moment(response.data[0].state_startDate).format('D MMM YYYY');
                })
                .catch(error => console.log(error)) ;
        }
        axios.get('/info/send/state')
            .then (response=> {
                this.infos_state=response.data;
            })
            .catch(error => console.log(error)) ;

        const UrlState = (id) => `/send/equipment/mme/${id}`;
        axios.get(UrlState(this.eq_id))
            .then (response=> {
                this.mme=response.data;
                console.log(response.data)
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

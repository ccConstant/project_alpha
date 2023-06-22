<!--File name : MMEVerificationForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to add or edit a verification-->

<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInConsultedMod==true && this.verif_number!==null || this.modifMod==true && this.verif_number!==null">
                    <InputNumberForm  inputClassName="form-control w-25" :Errors="errors.verif_number" name="verif_number" label="Number :" :stepOfInput="1" v-model="verif_number" isDisabled />
                </div>
                <!--<RadioGroupForm @clearRadioError="clearRadioError" label="Does the verification is realized during putting into service? :" :options="existOptionVerif" :Errors="errors.verif_puttingIntoService"  :info_text="infos_verif[0].info_value" :checkedOption="verif_puttingIntoService" :isDisabled="!!isInConsultedMod" v-model="verif_puttingIntoService"/>-->
                <RadioGroupForm label="Putting Into Service ?:" :options="existOptionPIS" :Errors="errors.verif_puttingIntoService"
                                :checkedOption="verif_puttingIntoService" :isDisabled="!!isInConsultMod" v-model="verif_puttingIntoService"
                                :info_text="infos_verif[0].info_value"
                                name="verif_puttingIntoService"
                />
                <RadioGroupForm label="Preventive Operation ?:" :options="existOptionPO" :Errors="errors.verif_preventiveOperation"
                                :checkedOption="verif_preventiveOperation" :isDisabled="!!isInConsultMod" v-model="verif_preventiveOperation"
                                :info_text="infos_verif[1].info_value"
                                name="verif_preventiveOperation"
                />
<!--                <div> {{infos_verif[0].info_value}}
                    <b-form-group>
                    <b-form-radio-group v-model="verif_puttingIntoService" :options="existOptionPIS" >
                    </b-form-radio-group>
                    </b-form-group>
                </div>-->
<!--                <div> {{infos_verif[1].info_value}}
                    <b-form-group>
                     <b-form-radio-group v-model="verif_preventiveOperation" :options="existOptionPO" >
                    </b-form-radio-group>
                    </b-form-group>
                </div>-->
                <InputTextForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[3].info_value"
                    :Errors="errors.verif_name"
                    name="verif_name"
                    label="Name :"
                    v-model="verif_name"
                    :isDisabled="!!isInConsultedMod"
                />
                <InputTextAreaForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[4].info_value"
                    :Errors="errors.verif_expectedResult"
                    name="verif_expectedResult"
                    label="Expected Result :"
                    :isDisabled="!!isInConsultedMod"
                    v-model="verif_expectedResult"
                />
                <InputTextAreaForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[5].info_value"
                    :Errors="errors.verif_nonComplianceLimit"
                    name="verif_nonComplianceLimit"
                    label="Non compliance limit :"
                    :isDisabled="!!isInConsultedMod"
                    v-model="verif_nonComplianceLimit"
                />
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    :info_text="infos_verif[6].info_value"
                    selectClassName="form-select w-50"
                    name="verif_requiredSkill"
                    label="Required Skill :"
                    :Errors="errors.verif_requiredSkill"
                    :options="enum_requiredSkill"
                    :selctedOption="this.verif_requiredSkill"
                    :isDisabled="!!isInConsultedMod"
                    :selectedDivName="this.divClass"
                    v-model="verif_requiredSkill"
                    :id_actual="RequiredSkill"
                />
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    :info_text="infos_verif[7].info_value"
                    selectClassName="form-select w-50"
                    name="verif_verifAcceptanceAuthority"
                    label="Verification acceptance authority :"
                    :Errors="errors.verif_verifAcceptanceAuthority"
                    :options="enum_verifAcceptanceAuthority"
                    :selctedOption="this.verif_verifAcceptanceAuthority"
                    :isDisabled="!!isInConsultedMod"
                    :selectedDivName="this.divClass"
                    v-model="verif_verifAcceptanceAuthority"
                    :id_actual="VerifAcceptanceAuthority"
                />
                <InputNumberForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[8].info_value"
                    :Errors="errors.verif_periodicity"
                    name="verif_periodicity"
                    label="Periodicity :"
                    :stepOfInput="1"
                    v-model="verif_periodicity"
                    :isDisabled="!!isInConsultedMod"
                />
                <InputSelectForm
                    @clearSelectError='clearSelectError'
                    selectClassName="form-control w-50"
                    :info_text="infos_verif[9].info_value"
                    name="verif_symbolPeriodicity"
                    label="Symbol :"
                    :Errors="errors.verif_symbolPeriodicity"
                    :options="enum_periodicity_symbol"
                    :selctedOption="this.verif_symbolPeriodicity"
                    :id_actual="SymbolPeriodicity"
                    :isDisabled="!!isInConsultedMod"
                    :selectedDivName="this.divClass"
                    v-model="verif_symbolPeriodicity"
                />
                <InputTextAreaForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[10].info_value"
                    :Errors="errors.verif_description"
                    name="verif_description"
                    label="Description :"
                    :isDisabled="!!isInConsultedMod"
                    v-model="verif_description"
                />
                <InputTextAreaForm
                    inputClassName="form-control w-50"
                    :info_text="infos_verif[11].info_value"
                    :Errors="errors.verif_protocol"
                    name="verif_protocol"
                    label="Protocol :"
                    :isDisabled="!!isInConsultedMod"
                    v-model="verif_protocol"
                />
                <InputTextForm
                    inputClassName="form-control w-50"
                    :info_text="null"
                    :Errors="errors.verif_mesureRange"
                    name="verif_mesureRange"
                    label="Measurement Range :"
                    v-model="verif_mesureRange"
                    :isDisabled="!!isInConsultedMod"
                />
                <InputTextForm
                    inputClassName="form-control w-50"
                    :info_text="null"
                    :Errors="errors.verif_mesureUncert"
                    name="verif_mesureUncert"
                    label="Measurement Uncertainty :"
                    v-model="verif_mesureUncert"
                    :isDisabled="!!isInConsultedMod"
                />
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this verification doesn't have a id the addMmeVerif is called function else the updateMmeVerif function is called -->
                    <div v-if="this.verif_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :savedAs="verif_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :savedAs="verif_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.verif_id!==null && reformMod==false ">
                        <div v-if="verif_reformDate!=null" >
                            <p>Reformed at {{verif_reformDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="verif_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="verif_validate" :Errors="errors.verif_delete" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && verif_reformDate===null">
                        <ReformComponentButton :reformBy="verif_reformBy" :reformDate="verif_reformDate" :reformMod="this.isInReformMod" @reformOk="reformComponent"/>
                    </div>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
            <ErrorAlert ref="errorAlert"/>
        </div>

    </div>
</template>
<script>
/*Importation of the other Components who will be used here*/
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import InputSelectForm from '../../../input/InputSelectForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../../input/InputTextAreaForm.vue'
import InputNumberForm from '../../../input/InputNumberForm.vue'
import DeleteComponentButton from '../../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../../button/ReformComponentButton.vue'
import RadioGroupForm from '../../../input/RadioGroupForm.vue'
import SucessAlert from '../../../alert/SuccesAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components : {
        InputSelectForm,
        RadioGroupForm,
        InputTextForm,
        SaveButtonForm,
        InputTextAreaForm,
        InputNumberForm,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert,
        SucessAlert
    },
    props:{
        verifAcceptanceAuthority:{
            type:String,
            default:''
        },
        number:{
            type:String,
            default:null
        },
        name:{
            type:String
        },
        expectedResult:{
            type:String
        },
        nonComplianceLimit:{
            type:String
        },
        description:{
            type:String
        },
        periodicity:{
            type:String
        },
        symbolPeriodicity:{
            type:String
        },
        requiredSkill:{
            type:String,
            default:''
        },
        protocol:{
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
        mme_id:{
            type:Number
        },
        reformDate:{
            type:String,
            default:null
        },
        reformBy:{
            type:String,
            dfault:null
        },
        reformMod:{
            type:Boolean,
            default:false
        },
        puttingIntoService:{
            type:Boolean,
            default:true,
        },
        preventiveOperation:{
            type:Boolean,
            default:true,
        },
        mesureUncert:{
            type:String,
            default:null
        },
        mesureRange:{
            type:String,
            default:null
        },
    },
    data(){
        return{
            verif_verifAcceptanceAuthority:this.verifAcceptanceAuthority,
            verif_number:this.number,
            verif_name:this.name,
            verif_puttingIntoService:this.puttingIntoService,
            verif_preventiveOperation:this.preventiveOperation,
            verif_expectedResult:this.expectedResult,
            verif_nonComplianceLimit:this.nonComplianceLimit,
            verif_description:this.description,
            verif_periodicity:this.periodicity,
            verif_symbolPeriodicity:this.symbolPeriodicity,
            verif_requiredSkill:this.requiredSkill,
            verif_protocol:this.protocol,
            verif_validate:this.validate,
            verif_reformDate:this.reformDate,
            verif_reformBy:this.reformBy,
            verif_mesureUncert: this.mesureUncert,
            verif_mesureRange: this.mesureRange,
            verif_id:this.id,
            mme_id_add:this.mme_id,
            mme_id_update:this.$route.params.id,
            enum_periodicity_symbol: [
                {id_enum:"SymbolPeriodicity",value:'Y'},
                {id_enum:"SymbolPeriodicity",value:'M'},
                {id_enum:"SymbolPeriodicity",value:'D'},
                {id_enum:"SymbolPeriodicity",value:'H'},
            ],
            existOptionPO :[
                {id: 'PO', value:true, text:'Yes'},
                {id : 'PO', value:false, text:'No'}
            ],
            existOptionPIS :[
                {id: 'PIS', value:true, text:'Yes'},
                {id : 'PIS', value:false, text:'No'}
            ],
            enum_verifAcceptanceAuthority: [],
            enum_requiredSkill:[],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            isInReformMod:this.reformMod,
            infos_verif:[],
            RequiredSkill:"RequiredSkill",
            VerifAcceptanceAuthority:"VerifAcceptanceAuthority",
            SymbolPeriodicity:"SymbolPeriodicity",
        }
    },
    created(){
        /*Ask for the controller different required skill  */
        axios.get('/verification/enum/requiredSkill')
            .then (response=>{
                this.enum_requiredSkill=response.data;
            } )
            .catch(error => console.log(error)) ;
        /*Ask for the controller different required skill  */
        axios.get('/verification/enum/verifAcceptanceAuthority')
            .then (response=>{
                this.enum_verifAcceptanceAuthority=response.data;
            } )
            .catch(error => console.log(error)) ;
        axios.get('/info/send/verif')
            .then (response=> {
                this.infos_verif=response.data;
                this.loaded=true;
                })
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the Mme so that it can be added to the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the validation
            lifesheet_created: Boolean to know if the lifesheet is created or not
            */
        addMmeVerif(savedAs, reason, lifesheet_created){
            if(!this.addSucces){
                //ID of the MME in which the verification will be added
                let id;
                //If the user is not in the modification mode, we set the id with the value of mme_id_add
                if(!this.modifMod){
                        id=this.mme_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.mme_id_update;
                }
                axios.post('/verif/verif',{
                    verif_name:this.verif_name,
                    verif_description:this.verif_description,
                    verif_expectedResult:this.verif_expectedResult,
                    verif_requiredSkill:this.verif_requiredSkill,
                    verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                    verif_periodicity:parseInt(this.verif_periodicity),
                    verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                    verif_protocol:this.verif_protocol,
                    verif_verifAcceptanceAuthority:this.verif_verifAcceptanceAuthority,
                    verif_validate :savedAs,
                    verif_puttingIntoService:this.verif_puttingIntoService,
                    verif_preventiveOperation:this.verif_preventiveOperation,
                    verif_mesureUncert: this.verif_mesureUncert,
                    verif_mesureRange: this.verif_mesureRange,
                })
                .then(response =>{
                    this.errors={};
                    axios.post('/mme/add/verif',{
                        verif_name:this.verif_name,
                        verif_verifAcceptanceAuthority:this.verif_verifAcceptanceAuthority,
                        verif_description:this.verif_description,
                        verif_expectedResult:this.verif_expectedResult,
                        verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                        verif_periodicity:parseInt(this.verif_periodicity),
                        verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                        verif_requiredSkill:this.verif_requiredSkill,
                        verif_protocol:this.verif_protocol,
                        verif_validate :savedAs,
                        mme_id:id,
                        verif_puttingIntoService:this.verif_puttingIntoService,
                        verif_preventiveOperation:this.verif_preventiveOperation,
                        verif_mesureUncert: this.verif_mesureUncert,
                        verif_mesureRange:this.verif_mesureRange,

                    })
                    //If the verification is added successfully
                    .then(response =>{
                        //We test if a life sheet has been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
                        if (lifesheet_created==true){
                            axios.post(`/history/add/mme/${id}`,{
                                history_reasonUpdate :reason,
                            });
                             window.location.reload();
                        }
                         this.$refs.sucessAlert.showAlert(`MME verification added successfully and saved as ${savedAs}`);
                        //If the user is not in the modification mode
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the verification take the value of the newly created id
                        this.verif_id=response.data;
                        //The validate option of this verification takes the value of savedAs(Params of the function)
                        this.verif_validate=savedAs;

                    })
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the validation
            lifesheet_created: Boolean to know if the lifesheet is created or not
            */
        updateMmeVerif(savedAs, reason, lifesheet_created){
            axios.post('/verif/verif',{
                    verif_name:this.verif_name,
                    verif_description:this.verif_description,
                    verif_expectedResult:this.verif_expectedResult,
                    verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                    verif_periodicity:parseInt(this.verif_periodicity),
                    verif_requiredSkill:this.verif_requiredSkill,
                    verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                    verif_protocol:this.verif_protocol,
                    verif_validate :savedAs,
                    verif_verifAcceptanceAuthority:this.verif_verifAcceptanceAuthority,
                    verif_puttingIntoService:this.verif_puttingIntoService,
                    verif_preventiveOperation:this.verif_preventiveOperation,
                    verif_mesureUncert: this.verif_mesureUncert,
                    verif_mesureRange:this.verif_mesureRange,
                })
                .then(response =>{
                    console.log("verif effectuées");
                    this.errors={};
                    const consultUrl = (id) => `/mme/update/verif/${id}`;
                    axios.post(consultUrl(this.verif_id),{
                        verif_name:this.verif_name,
                        verif_description:this.verif_description,
                        verif_expectedResult:this.verif_expectedResult,
                        verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                        verif_periodicity:parseInt(this.verif_periodicity),
                        verif_requiredSkill:this.verif_requiredSkill,
                        verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                        verif_protocol:this.verif_protocol,
                        verif_validate :savedAs,
                        mme_id:this.mme_id_update,
                        verif_verifAcceptanceAuthority:this.verif_verifAcceptanceAuthority,
                        verif_puttingIntoService:this.verif_puttingIntoService,
                        verif_preventiveOperation:this.verif_preventiveOperation,
                        verif_mesureUncert: this.verif_mesureUncert,
                        verif_mesureRange:this.verif_mesureRange,
                    })
                    .then(response =>{
                        console.log("update dans la base");
                        const id = this.mme_id_update;
                        //We test if a life sheet has been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
                        if (lifesheet_created==true){
                            axios.post(`/history/add/mme/${id}`,{
                                history_reasonUpdate :reason,
                            });
                             window.location.reload();
                        }
                        this.verif_validate=savedAs;
                         this.$refs.sucessAlert.showAlert(`MME verification updated successfully and saved as ${savedAs}`);

                    })
                    .catch(error => this.errors=error.response.data.errors) ;
                })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clears all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
         clearRadioError(){
            delete this.errors["verif_puttingIntoService"]
        },
         //Function for deleting verification from the view and the database
        deleteComponent(reason, lifesheet_created){
            //If the user is in update mode and the verification exist in the database
            if(this.modifMod==true && this.verif_id!==null){
                var consultUrl = (id) => `/mme/delete/verif/${id}`;
                axios.post(consultUrl(this.verif_id),{
                    mme_id:this.mme_id_update,
                })
                .then(response =>{
                    this.$emit('deleteVerif','')
                    var id=this.mme_id_update
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created==true){
                        axios.post(`/history/add/mme/${id}`,{
                            history_reasonUpdate :reason,
                        });
                         window.location.reload();
                    }
                    this.$refs.sucessAlert.showAlert(`MME verification deleted successfully`);
                })
                .catch(error => this.errors=error.response.data.errors) ;
            }else{
                this.$emit('deleteVerif','')
                this.$refs.sucessAlert.showAlert(`Empty MME verification deleted successfully`);
            }
        },
        reformComponent(endDate){
            if(this.$userId.user_makeReformRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            //Send a post-request with the id of the usage who will be deleted in the url
            const consultUrl = (id) => `/mme/reform/verif/${id}`;
            axios.post(consultUrl(this.verif_id),{
                eq_id:this.equipment_id_update,
                verif_reformDate:endDate
            })
            .then(response =>{
                //Emit to the parent component that we want to delete this component
                this.$emit('deleteVerif','')
            })
            .catch(error => {this.$refs.errorAlert.showAlert(error.response.data.errors['verif_reformDate'])}) ;
        },
        clearSelectError(value){
            delete this.errors[value];
        },
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
    p{
        margin-top : 15px;
        margin-bottom:0px;
    }
    .titleForm{
        padding-left: 10px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }
    .selectForm{
        margin-bottom:30px;
        margin-top:0px;
        select{
            width:350px;
            background-color: #D3D3D3;
        }
    }
</style>

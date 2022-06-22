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
                <InputTextForm  inputClassName="form-control w-50" :Errors="errors.verif_name" name="verif_name" label="Name :" v-model="verif_name" :isDisabled="!!isInConsultedMod"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.verif_expectedResult" name="verif_expectedResult" label="Expected Result :" :isDisabled="!!isInConsultedMod" v-model="verif_expectedResult" />
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.verif_nonComplianceLimit" name="verif_nonComplianceLimit" label="Non compliance limit :" :isDisabled="!!isInConsultedMod" v-model="verif_nonComplianceLimit" />
                <InputSelectForm @clearSelectError='clearSelectError'  name="verif_requiredSkill"  label="Required Skill :" :Errors="errors.verif_requiredSkill" :options="enum_requiredSkill" :selctedOption="this.verif_requiredSkill" :isDisabled="!!isInConsultedMod" :selectedDivName="this.divClass" v-model="verif_requiredSkill"/>
                <div class="input-group">
                    <InputNumberForm  inputClassName="form-control" :Errors="errors.verif_periodicity" name="verif_periodicity" label="Periodicity :" :stepOfInput="1" v-model="verif_periodicity" :isDisabled="!!isInConsultedMod"/>
                    <InputSelectForm @clearSelectError='clearSelectError'  name="verif_symbolPeriodicity"  label="Symbol :" :Errors="errors.verif_symbolPeriodicity" :options="enum_periodicity_symbol" :selctedOption="this.verif_symbolPeriodicity" :isDisabled="!!isInConsultedMod" :selectedDivName="this.divClass" v-model="verif_symbolPeriodicity"/>
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.verif_description" name="verif_description" label="Description :" :isDisabled="!!isInConsultedMod" v-model="verif_description" />
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.verif_protocol" name="verif_protocol" label="Protocol :" :isDisabled="!!isInConsultedMod" v-model="verif_protocol" />
               
               
               
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this preventive maintenance operation doesn't have a id the addMmeVerif is called function else the updateMmeVerif function is called -->
                    <div v-if="this.verif_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :savedAs="verif_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :savedAs="verif_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.verif_id!==null && reformMod==false ">
                        <div v-if="verif_refromDate!=null" >
                            <p>Refrom by {{verif_refromBy}} at {{verif_refromDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addMmeVerif" @update="updateMmeVerif" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="verif_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="verif_validate" :Errors="errors.verif_delete" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && verif_refromDate===null">
                        <ReformComponentButton :reformBy="verif_refromBy" :reformDate="verif_refromDate" :reformMod="this.isInReformMod" @reformOk="reformComponent"/>
                    </div>
                </div>       
            </form>
            <ErrorAlert ref="errorAlert"/>
        </div>

    </div>
</template>
<script>
/*Importation of the others Components who will be used here*/
import ErrorAlert from '../../alert/ErrorAlert.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputNumberForm from '../../input/InputNumberForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../button/ReformComponentButton.vue'
export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputSelectForm,
        InputTextForm,
        SaveButtonForm,
        InputTextAreaForm,
        InputNumberForm,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert
    },
    props:{
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
            type:String
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
        }

    },
    data(){
        return{
            verif_number:this.number,
            verif_name:this.name,
            verif_expectedResult:this.expectedResult,
            verif_nonComplianceLimit:this.nonComplianceLimit,
            verif_description:this.description,
            verif_periodicity:this.periodicity,
            verif_symbolPeriodicity:this.symbolPeriodicity,
            verif_requiredSkill:this.requiredSkill,
            verif_protocol:this.protocol,
            verif_validate:this.validate,
            verif_refromDate:this.reformDate,
            verif_refromBy:this.reformBy,
            verif_id:this.id,
            mme_id_add:this.mme_id,
            mme_id_update:this.$route.params.id,
            enum_periodicity_symbol: [
                {value:'Y'},
                {value:'M'},
                {value:'D'},
                {value:'H'},
            ],
            enum_requiredSkill:[],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            isInReformMod:this.reformMod,
            infos_verif:[],
            loaded:true

        }
    },
    created(){
        /*Ask for the controller different required skill  */
        axios.get('/verification/enum/requiredSkill')
            .then (response=> this.enum_dim_type=response.data) 
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the Mme so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addMmeVerif(savedAs){
            if(!this.addSucces){
                //Id of the Mme in which the preventive maintenance operation will be added
                var id;
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data Mme_id_add 
                if(!this.modifMod){
                        id=this.mme_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.mme_id_update;
                }
                /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/

                axios.post('/verif/verif',{
                    verif_name:this.verif_name,
                    verif_description:this.verif_description,
                    verif_expectedResult:this.verif_expectedResult,
                    veirf_requiredSkill:this.verif_requiredSkill,
                    verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                    verif_periodicity:parseInt(this.verif_periodicity),
                    verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                    verif_protocol:this.verif_protocol,
                    verif_validate :savedAs,
                })
                .then(response =>{
                    console.log(this.verif_name)
                    console.log(this.verif_description)
                    console.log(this.verif_expectedResult)
                    console.log(this.verif_nonComplianceLimit)
                    console.log(this.verif_periodicity)
                    console.log(this.verif_symbolPeriodicity)
                    console.log(this.verif_protocol)
                    console.log(savedAs)
                    console.log(id)

                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the mme is sended to the controller*/
                    axios.post('/mme/add/verif',{
                        verif_name:this.verif_name,
                        verif_description:this.verif_description,
                        verif_expectedResult:this.verif_expectedResult,
                        verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                        verif_periodicity:parseInt(this.verif_periodicity),
                        verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                        veirf_requiredSkill:this.verif_requiredSkill,
                        verif_protocol:this.verif_protocol,
                        verif_validate :savedAs,
                        mme_id:id
                
                    })
                    //If the preventive maintenance operation is added succesfuly
                    .then(response =>{
                        console.log(response)
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the preventive maintenance operation take the value of the newlly created id
                        this.mme_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.mme_validate=savedAs;
                        
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
        updateMmeVerif(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            console.log("update dans la base");
            axios.post('/verif/verif',{
                    verif_name:this.verif_name,
                    verif_description:this.verif_description,
                    verif_expectedResult:this.verif_expectedResult,
                    verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                    verif_periodicity:parseInt(this.verif_periodicity),
                    veirf_requiredSkill:this.verif_requiredSkill,
                    verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                    verif_protocol:this.verif_protocol,
                    verif_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/equipment/update/verif/${id}`;
                    axios.post(consultUrl(this.verif_id),{
                        verif_name:this.verif_name,
                        verif_description:this.verif_description,
                        verif_expectedResult:this.verif_expectedResult,
                        verif_nonComplianceLimit:this.verif_nonComplianceLimit,
                        verif_periodicity:parseInt(this.verif_periodicity),
                        veirf_requiredSkill:this.verif_requiredSkill,
                        verif_symbolPeriodicity:this.verif_symbolPeriodicity,
                        verif_protocol:this.verif_protocol,
                        verif_validate :savedAs,
                        mme_id:this.mme_id_update
                    })
                    .then(response =>{this.verif_validate=savedAs;})
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
            
            //If the user is in update mode and the preventive maintenance operation exist in the database
            if(this.modifMod==true && this.mme_id!==null){
                var consultUrl = (id) => `/mme/delete/verif/${id}`;
                axios.post(consultUrl(this.mme_id),{
                    mme_id:this.mme_id_update,
                })
                .then(response =>{
                    //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                    this.$emit('deleteVerif','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deleteVerif','')

            }
            
        },
        reformComponent(endDate){
            if(this.$userId.user_makeReformRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            //If the user is in update mode and the usage exist in the database
                //Send a post request with the id of the usage who will be deleted in the url
            var consultUrl = (id) => `/mme/reform/verif/${id}`;
            axios.post(consultUrl(this.verif_id),{
                eq_id:this.equipment_id_update,
                verif_reformDate:endDate
            })
            .then(response =>{
                //Emit to the parent component that we want to delete this component
                this.$emit('deleteVerif','')
            })
            //If the controller sends errors we put it in the errors object 
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
    .titleForm{
        padding-left: 10px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }
</style>
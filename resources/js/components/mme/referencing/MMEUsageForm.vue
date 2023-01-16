<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputTextAreaForm inputClassName="form-control w-50" :info_text="infos_usage[0].info_value" :Errors="errors.usg_measurementType" name="usg_measurementType" label="Measurement type :" :isDisabled="!!isInConsultedMod" v-model="usg_measurementType" />
                <InputTextAreaForm inputClassName="form-control w-50" :info_text="infos_usage[1].info_value" :Errors="errors.usg_precision" name="usg_precision" label="Precision :" :isDisabled="!!isInConsultedMod" v-model="usg_precision" />
                <InputTextAreaForm inputClassName="form-control w-50" :info_text="infos_usage[2].info_value" :Errors="errors.usg_application" name="usg_application" label="Application :" :isDisabled="!!isInConsultedMod" v-model="usg_application" />
                <InputSelectForm @clearSelectError='clearSelectError' :info_text="infos_usage[3].info_value" selectClassName="form-select w-50" name="usg_metrologicalLevel"  label="Metrological level :" :Errors="errors.usg_metrologicalLevel" :options="enum_metrologicalLevel" :selctedOption="this.usg_metrologicalLevel" :isDisabled="!!isInConsultedMod" :selectedDivName="this.divClass" v-model="usg_metrologicalLevel"/>
                
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <div v-if="this.usg_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addMmeUsage" @update="updateMmeUsage" :consultMod="this.isInConsultedMod" :savedAs="usg_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addMmeUsage" @update="updateMmeUsage" :consultMod="this.isInConsultedMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.usg_id!==null && reformMod==false ">
                        <div v-if="usg_reformDate!=null" >
                            <p>Reform at {{usg_reformDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addMmeUsage" @update="updateMmeUsage" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="usg_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="usg_validate" :Errors="errors.usg_delete" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && usg_reformDate===null">
                        <ReformComponentButton :reformBy="usg_reformBy" :reformDate="usg_reformDate" :reformMod="this.isInReformMod" @reformOk="reformComponent"/>
                    </div>


                </div>       
            </form>
            <div v-if="this.usg_id!==null && modifMod==false & consultMod==false && import_id==null " >
                <ReferenceAMMEPrecaution :mme_id="this.mme_id" :usg_id="this.usg_id"/>
            </div>
            <div v-else-if="this.usg_id!==null && modifMod==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :importedPrctn="importedUsgPrecaution" :mme_id="this.mme_id" :usg_id="this.usg_id"  :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
            <div v-else-if="loaded==true">
                <ReferenceAMMEPrecaution v-if="this.usg_id!=null" :importedPrctn="importedUsgPrecaution" :mme_id="this.mme_id" :usg_id="this.usg_id" :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
            <ErrorAlert ref="errorAlert"/>
        </div>

    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import ErrorAlert from '../../alert/ErrorAlert.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'

import ReferenceAMMEPrecaution from './ReferenceAMMEPrecaution.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import ReformComponentButton from '../../button/ReformComponentButton.vue'

export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        SaveButtonForm,
        InputTextAreaForm,
        InputSelectForm,
        ReferenceAMMEPrecaution,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert


    },
    props:{
        measurementType:{
            type:String,
            default:null
        },
        precision:{
            type:String
        },
        metrologicalLevel:{
            type:String
        },
        application:{
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
        import_id:{
            type: Number,
            default :null
        },
        reformMod:{
            type:Boolean,
            default:false
        }

    },
    data(){
        return{
            usg_measurementType:this.measurementType,
            usg_precision:this.precision,
            usg_metrologicalLevel:this.metrologicalLevel,
            usg_application:this.application,
            usg_validate:this.validate,
            usg_reformDate:this.reformDate,
            usg_reformBy:this.reformBy,
            usg_id:this.id,
            mme_id_add:this.mme_id,
            mme_id_update:this.$route.params.id,
            enum_metrologicalLevel: [],
            importedUsgPrecaution:[],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            isInReformMod:this.reformMod,
            infos_usage:[],
            loaded:false

        }
    },
    created(){
        axios.get('/usage/enum/metrologicalLevel')
            .then (response=>{
                this.enum_metrologicalLevel=response.data;
               
            } ) 
            .catch(error => console.log(error)) ;

        if(this.usg_id!==null && this.addSucces==false){
            //Make a get request to ask to the controller the preventive maintenance operation corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/precaution/send/${id}`;
            axios.get(consultUrl(this.usg_id))
                .then (response=> {
                    this.importedUsgPrecaution=response.data;
                    console.log(this.importedUsgPrecaution)
                    })
                .catch(error => console.log(error)) ; 
        }
        axios.get('/info/send/mme_usage')
            .then (response=> {
                console.log(response.data)
                this.infos_usage=response.data;
                this.loaded=true;
                }) 
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the   mme so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addMmeUsage(savedAs){
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


                axios.post('/mme_usage/verif',{
                    usg_measurementType:this.usg_measurementType,
                    usg_precision:this.usg_precision,
                    usg_metrologicalLevel:this.usg_metrologicalLevel,
                    usg_application:this.usg_application,
                    usg_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    console.log(this.usg_measurementType)
                    console.log(this.usg_precision)
                    console.log(this.usg_metrologicalLevel)
                    console.log(this.usg_application)
                    console.log(savedAs)
                    console.log(id)

                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the mme is sended to the controller*/
                    axios.post('/mme/add/usg',{
                        usg_measurementType:this.usg_measurementType,
                        usg_precision:this.usg_precision,
                        usg_metrologicalLevel:this.usg_metrologicalLevel,
                        usg_application:this.usg_application,
                        usg_validate :savedAs,
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
                        this.usg_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.usg_validate=savedAs;
                        
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
        updateMmeUsage(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            console.log("update dans la base");
            console.log(this.usg_metrologicalLevel)
            axios.post('/mme_usage/verif',{
                    usg_measurementType:this.usg_measurementType,
                    usg_precision:this.usg_precision,
                    usg_metrologicalLevel:this.usg_metrologicalLevel,
                    usg_application:this.usg_application,
                    usg_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the mme is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/mme/update/usg/${id}`;
                    axios.post(consultUrl(this.usg_id),{
                        usg_measurementType:this.usg_measurementType,
                        usg_precision:this.usg_precision,
                        usg_metrologicalLevel:this.usg_metrologicalLevel,
                        usg_application:this.usg_application,
                        usg_validate :savedAs,
                        mme_id:this.mme_id_update,
                    })
                    .then(response =>{this.usg_validate=savedAs;})
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
            if(this.modifMod==true && this.usg_id!==null){
                var consultUrl = (id) => `/mme/delete/usg/${id}`;
                axios.post(consultUrl(this.usg_id),{
                    mme_id:this.mme_id_update,
                })
                .then(response =>{
                    //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                    this.$emit('deleteUsage','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deleteUsage','')

            }
            
        },
        reformComponent(endDate){
            if(this.$userId.user_makeReformRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right to reform")
                return
            }
            //If the user is in update mode and the usage exist in the database
                //Send a post request with the id of the usage who will be deleted in the url
            var consultUrl = (id) => `/mme/reform/usg/${id}`;
            axios.post(consultUrl(this.usg_id),{
                mme_id:this.mme_id_update,
                usg_reformDate:endDate
            })
            .then(response =>{
                //Emit to the parent component that we want to delete this component
                this.$emit('deleteUsage','')
            })
            //If the controller sends errors we put it in the errors object 
            .catch(error => {this.$refs.errorAlert.showAlert(error.response.data.errors['usg_reformDate'])}) ;
        
            
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
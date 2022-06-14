<!--File name : EquipmentPrvMtnOpForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 17 May 2022-->
<!--Vue Component of the Form of the equipment preventive maintenance operation who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        InputTextAreaForm : name, label, isRequired, value, info_text, numberOfRow, isDisabled, inputClassName, Errors
        InputNumberForm : name, label, isRequired, value, info_text, stepOfInput, isDisabled, inputClassName, Errors
        InputSelectForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->

<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <div v-if="isInConsultedMod==true && this.prvMtnOp_number!==null || this.modifMod==true && this.prvMtnOp_number!==null">
                    <InputNumberForm  inputClassName="form-control w-25" :Errors="errors.prvMtnOp_number" name="prvMtnOp_number" label="Number :" :stepOfInput="1" v-model="prvMtnOp_number" isDisabled :info_text="infos_prvMtnOp[4].info_value"/>
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.prvMtnOp_description" name="prvMtnOp_description" label="Description :" :isDisabled="!!isInConsultedMod" v-model="prvMtnOp_description" :info_text="infos_prvMtnOp[0].info_value"/>
                <div class="input-group">
                    <InputNumberForm  inputClassName="form-control" :Errors="errors.prvMtnOp_periodicity" name="prvMtnOp_periodicity" label="Periodicity :" :stepOfInput="1" v-model="prvMtnOp_periodicity" :isDisabled="!!isInConsultedMod" :info_text="infos_prvMtnOp[1].info_value"/>
                    <InputSelectForm @clearSelectError='clearSelectError'  name="prvMtnOp_symbolPeriodicity"  label="Symbol :" :Errors="errors.prvMtnOp_symbolPeriodicity" :options="enum_periodicity_symbol" :selctedOption="this.prvMtnOp_symbolPeriodicity" :isDisabled="!!isInConsultedMod" :selectedDivName="this.divClass" v-model="prvMtnOp_symbolPeriodicity" :info_text="infos_prvMtnOp[2].info_value"/>
                </div>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.prvMtnOp_protocol" name="prvMtnOp_protocol" label="Protocol :" :isDisabled="!!isInConsultedMod" v-model="prvMtnOp_protocol" :info_text="infos_prvMtnOp[3].info_value"/>
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this preventive maintenance operation doesn't have a id the addEquipmentPrvMtnOp is called function else the updateEquipmentPrvMtnOp function is called -->
                    <div v-if="this.prvMtnOp_id===null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentPrvMtnOp" @update="updateEquipmentPrvMtnOp" :consultMod="this.isInConsultedMod" :savedAs="prvMtnOp_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentPrvMtnOp" @update="updateEquipmentPrvMtnOp" :consultMod="this.isInConsultedMod" :savedAs="prvMtnOp_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.prvMtnOp_id!==null && reformMod==false ">
                        <div v-if="prvMtnOp_refromDate!=null" >
                            <p>Refrom by {{prvMtnOp_refromBy}} at {{prvMtnOp_refromDate}}</p>
                        </div>
                        <div v-else>
                            <SaveButtonForm  @add="addEquipmentPrvMtnOp" @update="updateEquipmentPrvMtnOp" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="prvMtnOp_validate"/>
                        </div>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="prvMtnOp_validate" :Errors="errors.prvMtnOp_delete" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                    <div v-if="reformMod!==false && prvMtnOp_refromDate===null">
                        <ReformComponentButton :reformBy="prvMtnOp_refromBy" :reformDate="prvMtnOp_refromDate" :reformMod="this.isInReformMod" @reformOk="reformComponent" :info="infos_prvMtnOp[5].info_value"/>
                    </div>


                </div>       
            </form>
            <div v-if="this.prvMtnOp_id!==null && modifMod==false & consultMod==false && import_id==null " >
                <ReferenceARisk :eq_id="this.eq_id" :prvMtnOp_id="this.prvMtnOp_id" :riskForEq="false"/>
            </div>
            <div v-else-if="this.prvMtnOp_id!==null && modifMod==true">
                <ReferenceARisk v-if="this.prvMtnOp_id!=null" :importedRisk="importedOpRisk" :eq_id="this.eq_id" :prvMtnOp_id="this.prvMtnOp_id" :riskForEq="false" :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
            <div v-else-if="loaded==true">
                <ReferenceARisk v-if="this.prvMtnOp_id!=null" :importedRisk="importedOpRisk" :eq_id="this.eq_id" :prvMtnOp_id="this.prvMtnOp_id" :riskForEq="false" :consultMod="!!isInConsultedMod" :modifMod="!!this.modifMod"/>
            </div>
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
import ReferenceARisk from './ReferenceARisk.vue'
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
        ReferenceARisk,
        DeleteComponentButton,
        ReformComponentButton,
        ErrorAlert


    },
    /*--------Declartion of the differents props:--------
        number : 
        description : 
        periodicity : 
        symbolPeriodicity : 
        protocol :
        validate: Validation option of the preventive maintenance operation
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this preventive maintenance operation form
        id: Id of an already created preventive maintenance operation 
        eq_id: Id of the equipment in which the preventive maintenance operation will be added
        
    ---------------------------------------------------*/
    props:{
        number:{
            type:String,
            default:null
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
        eq_id:{
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
    /*--------Declartion of the differents returned data:--------
        prvMtnOp_number:
        prvMtnOp_description:
        prvMtnOp_periodicity:
        prvMtnOp_symbolPeriodicity:
        prvMtnOp_protocol:
        prvMtnOp_validate: Validation option of the preventive maintenance operation
        prvMtnOp_id: Id oh this preventive maintenance operation
        equipment_id_add: Id of the equipment in which the preventive maintenance operation will be added
        equipment_id_update: Id of the equipment in which the preventive maintenance operation will be updated
        enum_periodicity_symbol :
        importedRisk: 
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this preventive maintenance operation has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            prvMtnOp_number:this.number,
            prvMtnOp_description:this.description,
            prvMtnOp_periodicity:this.periodicity,
            prvMtnOp_symbolPeriodicity:this.symbolPeriodicity,
            prvMtnOp_protocol:this.protocol,
            prvMtnOp_validate:this.validate,
            prvMtnOp_refromDate:this.reformDate,
            prvMtnOp_refromBy:this.reformBy,
            prvMtnOp_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            enum_periodicity_symbol: [
                {value:'Y'},
                {value:'M'},
                {value:'D'},
                {value:'H'},
            ],
            importedOpRisk:[],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            isInReformMod:this.reformMod,
            infos_prvMtnOp:[],
            loaded:false

        }
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentPrvMtnOp(savedAs){
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
                console.log(this.prvMtnOp_periodicity)
                axios.post('/prvMtnOp/verif',{
                    prvMtnOp_description:this.prvMtnOp_description,
                    prvMtnOp_periodicity:parseInt(this.prvMtnOp_periodicity),
                    prvMtnOp_symbolPeriodicity:this.prvMtnOp_symbolPeriodicity,
                    prvMtnOp_protocol:this.prvMtnOp_protocol,
                    prvMtnOp_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                    Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                    axios.post('/equipment/add/prvMtnOp',{
                        prvMtnOp_description:this.prvMtnOp_description,
                        prvMtnOp_periodicity:parseInt(this.prvMtnOp_periodicity),
                        prvMtnOp_symbolPeriodicity:this.prvMtnOp_symbolPeriodicity,
                        prvMtnOp_protocol:this.prvMtnOp_protocol,
                        prvMtnOp_validate :savedAs,
                        eq_id:id
                
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
                        this.prvMtnOp_id=response.data;
                        //The validate option of this preventive maintenance operation take the value of savedAs(Params of the function)
                        this.prvMtnOp_validate=savedAs;
                        
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
        updateEquipmentPrvMtnOp(savedAs){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            console.log("update dans la base");
            axios.post('/prvMtnOp/verif',{
                    prvMtnOp_description:this.prvMtnOp_description,
                    prvMtnOp_periodicity:parseInt(this.prvMtnOp_periodicity),
                    prvMtnOp_symbolPeriodicity:this.prvMtnOp_symbolPeriodicity,
                    prvMtnOp_protocol:this.prvMtnOp_protocol,
                    prvMtnOp_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    /*If all the verif passed, a new post this time to add the preventive maintenance operation in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller
                        In the post url the id correspond to the id of the preventive maintenance operation who will be update*/
                    var consultUrl = (id) => `/equipment/update/prvMtnOp/${id}`;
                    axios.post(consultUrl(this.prvMtnOp_id),{
                        //prvMtnOp_number:this.prvMtnOp_number,
                        prvMtnOp_description:this.prvMtnOp_description,
                        prvMtnOp_periodicity:parseInt(this.prvMtnOp_periodicity),
                        prvMtnOp_symbolPeriodicity:this.prvMtnOp_symbolPeriodicity,
                        prvMtnOp_protocol:this.prvMtnOp_protocol,
                        eq_id:this.equipment_id_update,
                        prvMtnOp_validate :savedAs,
                    })
                    .then(response =>{this.prvMtnOp_validate=savedAs;})
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
            if(this.modifMod==true && this.prvMtnOp_id!==null){
                var consultUrl = (id) => `/equipment/delete/prvMtnOp/${id}`;
                axios.post(consultUrl(this.prvMtnOp_id),{
                    eq_id:this.equipment_id_update,
                })
                .then(response =>{
                    //Send a post request with the id of the preventive maintenance operation who will be deleted in the url
                    this.$emit('deletePrvMtnOp','')
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deletePrvMtnOp','')

            }
            
        },
        reformComponent(endDate){
            //If the user is in update mode and the usage exist in the database
                //Send a post request with the id of the usage who will be deleted in the url
            var consultUrl = (id) => `/equipment/reform/prvMtnOp/${id}`;
            axios.post(consultUrl(this.prvMtnOp_id),{
                eq_id:this.equipment_id_update,
                prvMtnOp_reformDate:endDate
            })
            .then(response =>{
                //Emit to the parent component that we want to delete this component
                this.$emit('deletePrvMtnOp','')
            })
            //If the controller sends errors we put it in the errors object 
            .catch(error => {this.$refs.errorAlert.showAlert(error.response.data.errors['prvMtnOp_reformDate'])}) ;
        
            
        },
        clearSelectError(value){
            delete this.errors[value];
        },
    },
    created(){
        if(this.prvMtnOp_id!==null && this.addSucces==false){
            //Make a get request to ask to the controller the preventive maintenance operation corresponding to the id of the equipment with which data will be imported
            var consultUrl = (id) => `/prvMtnOp/risk/send/${id}`;
            axios.get(consultUrl(this.prvMtnOp_id))
                .then (response=> {
                    this.importedOpRisk=response.data;
                    console.log( this.prvMtnOp_id)
                    })
                .catch(error => console.log(error)) ;
                
                
        }
        axios.get('/info/send/preventiveMaintenanceOperation')
        .then (response=> {
            this.infos_prvMtnOp=response.data;
            this.loaded=true;
        }) 
        .catch(error => console.log(error)) ;
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
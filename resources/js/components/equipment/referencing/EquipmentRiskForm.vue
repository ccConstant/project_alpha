<!--File name : EquipmentRiskForm.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 13 Feb 2023-->
<!--Vue Component of the Form of the equipment risk who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        InputSelectForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->

<template>
    <div :class="divClass">
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputSelectForm @clearSelectError='clearSelectError' selectClassName="form-select w-50" :Errors="errors.risk_for" name="risk_for" label="Risk for :" :options="enum_risk_for" :isDisabled="!!isInConsultedMod" :info_text="infos_risk[0].info_value" :selctedOption="this.risk_for" :selectedDivName="this.divClass" v-model="risk_for" :id_actual="riskFor"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.risk_remarks" name="risk_remarks" label="Remarks :" :isDisabled="!!isInConsultedMod" v-model="risk_remarks" :info_text="infos_risk[1].info_value"/>
                <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.risk_wayOfControl" name="risk_wayOfControl" label="Way of Control :" :isDisabled="!!isInConsultedMod" v-model="risk_wayOfControl" :info_text="infos_risk[2].info_value"/>
                <!--If addSucces is equal to false, the buttons appear -->
                <div v-if="this.addSucces==false ">
                    <!--If this risk doesn't have a id the addEquipmentRisk is called function else the updateEquipmentRisk function is called -->
                    <div v-if="this.risk_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addEquipmentRisk" @update="updateEquipmentRisk" :consultMod="this.isInConsultedMod" :savedAs="risk_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addEquipmentRisk" @update="updateEquipmentRisk" :consultMod="this.isInConsultedMod" :savedAs="risk_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.risk_id!==null">
                        <SaveButtonForm @add="addEquipmentRisk" @update="updateEquipmentRisk" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="risk_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="risk_validate" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                </div>       
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'

export default {
        /*--------Declartion of the others Components:--------*/
    components : {
        InputSelectForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert


    },
    /*--------Declartion of the differents props:--------
        for : 
        remarks : 
        wayOfControl : 
        validate: Validation option of the risk
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this risk form
        id: Id of an already created risk 
        eq_id: Id of the equipment in which the risk will be added
        RiskForEq:
        
    ---------------------------------------------------*/
    props:{
        for:{
            type:String
        },
        remarks:{
            type:String
        },
        wayOfControl:{
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
        prvMtnOp_id:{
            type:Number
        },
        riskForEq:{
            type:Boolean
        }

    },
    /*--------Declartion of the differents returned data:--------
        risk_for: 
        risk_remarks: 
        risk_wayOfControl: 
        risk_validate: Validation option of the risk
        risk_id: Id oh this risk
        equipment_id_add: Id of the equipment in which the risk will be added
        equipment_id_update: Id of the equipment in which the risk will be updated
        enum_risk_for : Different types of risk gets from the database
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this risk has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            risk_for:this.for,
            risk_remarks:this.remarks,
            risk_wayOfControl:this.wayOfControl,
            risk_validate:this.validate,
            risk_id:this.id,
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            enum_risk_for : [],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            infos_risk:[],
            riskFor:"RiskFor"
            
        }
        
    },
        /*All function inside the created option is called after the component has been mounted.*/
    created(){
        /*Ask for the controller different types of the risk  */
        axios.get('/risk/enum/riskfor')
            .then (response=> this.enum_risk_for=response.data) 
            .catch(error => console.log(error)) ;

        axios.get('/info/send/risk')
            .then (response=> {
                console.log(response.data)
                this.infos_risk=response.data;
                this.loaded=true;
            }) 
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentRisk(savedAs, reason, lifesheet_created){
            if(!this.addSucces){
                //Id of the equipment in which the risk will be added
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
                axios.post('/risk/verif',{
                    risk_for:this.risk_for,
                    risk_remarks:this.risk_remarks,
                    risk_wayOfControl:this.risk_wayOfControl,
                    risk_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    //Si ajoout equipement
                    if(this.riskForEq==true){
                        /*If all the verif passed, a new post this time to add the risk in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                        axios.post('/equipment/add/risk',{
                            risk_for:this.risk_for,
                            risk_remarks:this.risk_remarks,
                            risk_wayOfControl:this.risk_wayOfControl,
                            risk_validate :savedAs,
                            eq_id:id
                    
                        })
                        //If the risk is added succesfuly
                        .then(response =>{
                            //We test if a life sheet have been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/equipment/${id}`,{
                                    history_reasonUpdate :reason, 
                                });
                                 window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Equipment risk added successfully and saved as ${savedAs}`);
                            //If we the user is not in modifMod
                            if(!this.modifMod){
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod=true;
                                this.addSucces=true
                            }
                            //the id of the risk take the value of the newlly created id
                            this.risk_id=response.data;
                            //The validate option of this risk take the value of savedAs(Params of the function)
                            this.risk_validate=savedAs;
                            
                        })
                        //If the controller sends errors we put it in the errors object 
                        .catch(error => this.errors=error.response.data.errors) ;
                    }else{
                        /*If all the verif passed, a new post this time to add the risk in the data base
                        Type, name, value, unit, validate option and id of the equipment is sended to the controller*/
                        
                        axios.post("/equipment/add/prvMtnOp/risk",{
                            risk_for:this.risk_for,
                            risk_remarks:this.risk_remarks,
                            risk_wayOfControl:this.risk_wayOfControl,
                            risk_validate :savedAs,
                            eq_id:id,
                            prvMtnOp_id:this.prvMtnOp_id
                    
                        })
                        //If the risk is added succesfuly
                        .then(response =>{
                            var id=this.equipment_id_update;
                            //We test if a life sheet have been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/equipment/${id}`,{
                                    history_reasonUpdate :reason, 
                                });
                                 window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Preventive maintenance operation risk added successfully and saved as ${savedAs}`);
                            //If we the user is not in modifMod
                            if(!this.modifMod){
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod=true;
                                this.addSucces=true
                            }
                            //the id of the risk take the value of the newlly created id
                            this.risk_id=response.data;
                            //The validate option of this risk take the value of savedAs(Params of the function)
                            this.risk_validate=savedAs;
                            
                        })
                        //If the controller sends errors we put it in the errors object 
                        .catch(error => this.errors=error.response.data.errors) ;

                    }

                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
                /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        updateEquipmentRisk(savedAs, reason, lifesheet_created){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            axios.post('/risk/verif',{
                    risk_for:this.risk_for,
                    risk_remarks:this.risk_remarks,
                    risk_wayOfControl:this.risk_wayOfControl,
                    risk_validate :savedAs,
                })
                .then(response =>{
                    this.errors={};
                    if(this.riskForEq==true){
                        
                        /*If all the verif passed, a new post this time to add the risk in the data base
                            Type, name, value, unit, validate option and id of the equipment is sended to the controller
                            In the post url the id correspond to the id of the risk who will be update*/
                        var consultUrl = (id) => `/equipment/update/risk/${id}`;
                        axios.post(consultUrl(this.risk_id),{
                            risk_for:this.risk_for,
                            risk_remarks:this.risk_remarks,
                            risk_wayOfControl:this.risk_wayOfControl,
                            risk_validate :savedAs,
                            eq_id:this.equipment_id_update,
                        })
                        .then(response =>{
                            this.risk_validate=savedAs;
                            var id=this.equipment_id_update;
                            //We test if a life sheet have been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/equipment/${id}`,{
                                    history_reasonUpdate :reason, 
                                });
                                 window.location.reload();
                            }
                             this.$refs.sucessAlert.showAlert(`Equipment risk updated successfully and saved as ${savedAs}`);
                        })
                        //If the controller sends errors we put it in the errors object 
                        .catch(error => this.errors=error.response.data.errors) ;
                    }else{
                        /*If all the verif passed, a new post this time to add the risk in the data base
                            Type, name, value, unit, validate option and id of the equipment is sended to the controller
                            In the post url the id correspond to the id of the risk who will be update*/
                        var consultUrl = (id) => `/equipment/update/prvMtnOp/risk/${id}`;
                        axios.post(consultUrl(this.risk_id),{
                            risk_for:this.risk_for,
                            risk_remarks:this.risk_remarks,
                            risk_wayOfControl:this.risk_wayOfControl,
                            risk_validate :savedAs,
                            prvMtnOp_id:this.prvMtnOp_id,
                            eq_id:this.equipment_id_update,
                        })
                        .then(response =>{
                            this.risk_validate=savedAs;
                            var id=this.equipment_id_update;
                            //We test if a life sheet have been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/equipment/${id}`,{
                                    history_reasonUpdate :reason, 
                                });
                                 window.location.reload();
                            }
                             this.$refs.sucessAlert.showAlert(`Preventive maintenance operation risk updated successfully and saved as ${savedAs}`);
                        })
                        //If the controller sends errors we put it in the errors object 
                        .catch(error => this.errors=error.response.data.errors) ;

                    }
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
        },

        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a risk from the view and the database
        deleteComponent(reason, lifesheet_created){

            //If the user is in update mode and the risk exist in the database
            if(this.modifMod==true && this.risk_id!==null){
                console.log("supression");
                //Send a post request with the id of the risk who will be deleted in the url
                var consultUrl = (id) => `/equipment/delete/risk/${id}`;
                axios.post(consultUrl(this.risk_id),{
                    eq_id:this.equipment_id_update
                })
                .then(response =>{
                    var id=this.equipment_id_update;
                    //We test if a life sheet have been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the delete
                    if (lifesheet_created==true){
                        axios.post(`/history/add/equipment/${id}`,{
                            history_reasonUpdate :reason, 
                        });
                        window.location.reload();
                    }
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteRisk','')
                     this.$refs.sucessAlert.showAlert(`Risk deleted successfully`);
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deleteRisk','')
                 this.$refs.sucessAlert.showAlert(`Empty risk deleted successfully`);

            }
            
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
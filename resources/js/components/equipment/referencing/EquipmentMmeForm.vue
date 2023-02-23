<!--File name : EquipmentMmeForm.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 12 Jul 2022-->
<!--Vue Component of the Form of the equipment mme who call all the input component-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
        InputTextForm : name, label, isRequired, value, info_text, isDisabled, inputClassName, Errors
        SaveButtonForm : name, label, isRequired, value, info_text,  isDisabled, options, selectClassName, selectedOption
-------------------------------------------------------------->
<template>
    <div :class="divClass">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div v-if="mme_id!=null && isInConsultMod"> Consult all the information about the MME <router-link  :to="{name:'url_mme_consult',params:{id:this.mme_id} }"> here </router-link></div>
            <div v-if="mme_id!=null && isInModifMod"> Update all the information about the MME <router-link  :to="{name:'url_mme_update',params:{id:this.mme_id} }"> here </router-link></div>
            <div v-if="mme_id==null && this.equipment_id_add!=null"><AddMMEAlreadyCreated v-if="disableImport==false" @choosedMME="addInfoMmeAlreadyCreated" :eq_id="this.equipment_id_add" /></div>
             <div v-if="mme_id==null && this.equipment_id_update!=null"><AddMMEAlreadyCreated v-if="disableImport==false" @choosedMME="addInfoMmeAlreadyCreated" :eq_id="this.equipment_id_update"/></div>
            <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_internalReference" name="mme_internalReference" label="Alpha reference :" :isDisabled="!!isInConsultMod || isInModifMod && mme_id!=null" v-model="mme_internalReference" :info_text="infos_idCard[0].info_value" />
                <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_externalReference" name="mme_externalReference" label="External reference :" :isDisabled="!!isInConsultMod || isInModifMod && mme_id!=null"  v-model="mme_externalReference" :info_text="infos_idCard[1].info_value"/>
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_name" name="mme_name" label="MME name :" :isDisabled="!!isInConsultMod || isInModifMod && mme_id!=null" v-model="mme_name" :info_text="infos_idCard[2].info_value" />
            <InputTextForm v-if="this.mme_importFrom!== undefined " inputClassName="form-control w-50" name="mme_importFrom" label="Import From :" isDisabled v-model="mme_importFrom" />
                <!--If addSucces is equal to false, the buttons appear -->         
            </form>
            <div v-if="this.mme_id==null ">
                <div v-if="modifMod==true">
                    <SaveButtonForm @add="addEquipmentMme" @update="updateEquipmentMme" :consultMod="this.isInConsultMod" :savedAs="mme_validate" :AddinUpdate="true"/>
                </div>
                <div v-else>
                    <SaveButtonForm @add="addEquipmentMme" @update="updateEquipmentMme" :consultMod="this.isInConsultMod" :savedAs="mme_validate"/>
                </div>
            </div>
            <div v-else-if="this.mme_id!==null && !this.mmeAlreadyCreated">
                <SaveButtonForm @add="addEquipmentMme" @update="updateEquipmentMme" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="mme_validate"/>
            </div>
                <DeleteComponentButton :validationMode="mme_validate" :consultMod="this.isInConsultMod" @deleteOk="deleteComponent"/>
                <DeleteComponentButton :validationMode="mme_validate" :consultMod="this.isInConsultMod" @deleteOk="unlinkComponent" :unlink="this.unlink_true"/>
            <SucessAlert ref="sucessAlert"/>
        </div>

    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputTextForm from '../../input/InputTextForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputTextWithOptionForm from '../../input/InputTextWithOptionForm.vue'
import AddMMEAlreadyCreated from './AddMMEAlreadyCreated.vue'




export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputTextForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert,
        InputTextWithOptionForm,
        InputTextAreaForm,
        AddMMEAlreadyCreated,


    },
    /*--------Declartion of the differents props:--------
        type : Dimension type given by the data base we will put this data in the corresponding field as default value
        name : Dimension name reference given by the data base we will put this data in the corresponding field as default value
        value : Dimension value given by the data base we will put this data in the corresponding field as default value
        unit : Dimension unit given by the data base we will put this data in the corresponding field as default value
        validate: Validation option of the dimension
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modif mode we disable save button and show update button
        divClass: Class name of this Dimension form
        id: Id of an already created Dimension 
        eq_id: Id of the equipment in which the dimension will be added
        
    ---------------------------------------------------*/
    props:{
        internalReference:{
            type:String
        },
        externalReference:{
            type:String
        },
        name:{
            type:String
        },
        validate:{
            type:String
        },
        consultMod:{
            type:Boolean,
            default:false
        },
        divClass:{
            type:String
        },
        modifMod:{
            type:Boolean,
            default :false
        },
        disableImport:{
            type:Boolean,
            default:false
        },
        id:{
            type:Number,
            default:null
        },
        eq_id:{
            type:Number
        }



    },
    /*--------Declartion of the differents returned data:--------
        dim_type: Type of the dimension who  will be apear in the field and updated dynamically
        dim_name: Name of the dimension who  will be apear in the field and updated dynamically
        dim_value: Value of the dimension who  will be apear in the field and updated dynamically
        dim_unit: Unit of the dimension who  will be apear in the field and updated dynamically
        dim_validate: Validation option of the dimensions
        dim_id: Id oh this dimension
        equipment_id_add: Id of the equipment in which the dimensions will be added
        equipment_id_update: Id of the equipment in which the dimensions will be updated
        enum_dim_type : Different types of dimension gets from the database
        enum_dim_name : Different names of dimension gets from the database
        enum_dim_unit : Different unites of dimension gets from the database
        errors: Object of errors in wich will be stores the different erreur occured when adding in database
        addSucces: Boolean who tell if this dimension has been added successfully
        isInConsultedMod: data of the consultMod prop
    -----------------------------------------------------------*/
    data(){
        return{
            list_mme : [],
            equipment_id_add:this.eq_id,
            equipment_id_update:this.$route.params.id,
            mme_internalReference : this.internalReference,
            errors:{},
            addSucces:false,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            loaded:false,
            mme_validate:null,
            mme_internalReference : this.internalReference,
            mme_externalReference :this.externalReference,
            mme_name :this.name,
            mme_importFrom:undefined,
            mme_validate:this.validate,
            infos_idCard:[],
            info_mme_internalReference:'',
            mme_id: this.id,
            enum_sets:[],
            mmeAlreadyCreated:false,
            unlink_true:true,
        }
    },
    /*All function inside the created option is called after the component has been mounted.*/
    created(){

        /*Ask for the controller different types of the dimension  */
        axios.get('/mme/sets')
            .then (response=> this.enum_sets=response.data) 
            .catch(error => console.log(error)) ; 

        axios.get('/mme/mmes_not_linked')
            .then (response=> {
                this.list_mme=response.data ; 
            })
            .catch(error => console.log(error)) ;

        axios.get('/info/send/mme')
        .then (response=> {
            this.infos_idCard=response.data;
            this.loaded=true;
            }) 
        .catch(error => console.log(error)) ;
    },

    methods:{
        /*Sending to the controller all the information about the equipment so that it can be added to the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        addEquipmentMme(savedAs, reason, lifesheet_created){
             var id;
            if(!this.modifMod){
                id=this.equipment_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
            }else{
                id=this.equipment_id_update;
            }
            if(!this.addSucces){
            
                //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                axios.post('/mme/verif',{
                    mme_internalReference : this.mme_internalReference, 
                    mme_externalReference : this.mme_externalReference,
                    mme_name : this.mme_name,
                    mme_validate : savedAs,
                    reason:'add'
                })
                .then(response =>{
                    this.errors={};
                    axios.post('/mme/add',{
                        mme_internalReference : this.mme_internalReference, 
                        mme_externalReference : this.mme_externalReference,
                        mme_name : this.mme_name,
                        mme_validate : savedAs,
                    })
                    //If the mme is added succesfuly
                    .then(response =>{
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultMod=true;
                            this.addSucces=true
                        } 
                        this.mme_id=response.data;
                        //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                        var urlLinkMmetoEq = (id) => `/mme/link_to_eq/${id}`;
                        axios.post(urlLinkMmetoEq(id),{
                            'mme_internalReference' : this.mme_internalReference,
                        })
                        //If the mme is added succesfuly
                        .then(response =>{
                            //We test if a life sheet have been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/equipment/${id}`,{
                                    history_reasonUpdate :reason, 
                                });
                                window.location.reload();
                            }
                            this.$refs.sucessAlert.showAlert(`Equipment mme saved as ${savedAs} successfully`);
                            
                            
                            //If we the user is not in modifMod
                            if(!this.modifMod){
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod=true;
                                this.addSucces=true
                            }   
                        })
                    })
                })
                        
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
        addInfoMmeAlreadyCreated(value, reason, lifesheet_created){
            this.mme_internalReference=value.internalReference;
            this.mme_externalReference=value.externalReference;
            this.mme_name=value.name;
            this.addSucces=true;
            this.isInConsultMod=true;
            this.mme_id=value.id;
            this.mmeAlreadyCreated=true;
            
            var id;
            //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
            if(!this.modifMod){
                id=this.equipment_id_add
            //else the user is in the update menu, we allocate to the id the value of the id get in the url
            }else{
                id=this.equipment_id_update;
            }
            
            //We test if a life sheet have been already created
            //If it's the case we create a new enregistrement of history for saved the reason of the update
            if (lifesheet_created==true){
                axios.post(`/history/add/equipment/${id}`,{
                    history_reasonUpdate :reason, 
                });
                window.location.reload();
            }
            this.$refs.sucessAlert.showAlert(`Equipment mme linked successfully`);

        },

        extract_mme_refs(mme_list){
            let refs=[] ; 
            for(let mme of mme_list){
                refs.push( {type: { value : mme.value}, index:refs.length}) ;
            }
            return refs ; 
        },
        /*Sending to the controller all the information about the equipment so that it can be updated in the database
        Params : 
            savedAs : Value of the validation option : drafted, to_be_validater or validated  */ 
        updateEquipmentMme(savedAs, reason, lifesheet_created){
            /*First post to verify if all the fields are filled correctly
                Type, name, value, unit and validate option is sended to the controller*/
            //If the user is in the not in the update menu, we allocate to the id the value of the id get with the data equipment_id_add 
                axios.post('/mme/verif',{
                    mme_internalReference : this.mme_internalReference, 
                    mme_externalReference : this.mme_externalReference,
                    mme_name : this.mme_name,
                    mme_validate : savedAs,
                    mme_id : this.mme_id,
                    reason:'update'
                })
                .then(response =>{
                    this.errors={};
                    var updateUrl = (id) => `/mme/update/${id}`;
                    axios.post(updateUrl(this.mme_id),{
                        mme_internalReference : this.mme_internalReference, 
                        mme_externalReference : this.mme_externalReference,
                        mme_name : this.mme_name,
                        mme_validate : savedAs,
                    })
                    //If the mme is added succesfuly
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
                        this.errors={};
                        //If we the user is not in modifMod
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultMod=true;
                            this.addSucces=true
                        } 
                        this.$refs.sucessAlert.showAlert(`Equipment mme saved as ${savedAs} successfully`);
                            //If we the user is not in modifMod
                            if(!this.modifMod){
                                //The form pass in consulting mode and addSucces pass to True
                                this.isInConsultedMod=true;
                                this.addSucces=true
                            }   
                        })
                    })                        
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            
        },
        //Clear all the error of the targeted field
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a dimension from the view and the database
        deleteComponent(reason, lifesheet_created){
            //If the user is in update mode and the mme has been updated
            if(this.modifMod==true && this.mme_internalReference!==null){
                console.log("supression");
                //Send a post request with the id of the dimension who will be deleted in the url
                var consultUrl = (id) => `/mme/delete/${id}`;
                axios.post(consultUrl(this.mme_id),{
                    eq_id : this.equipment_id_update,
                })
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
                    this.errors={};
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteMme','')
                    this.$refs.sucessAlert.showAlert(`Equipment mme delete successfully`);
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            
            }else{
                this.$emit('deleteMme','')
            }
        },

        //Function for unlink a dimension from the view and the database
        unlinkComponent(reason, lifesheet_created){
            //If the user is in update mode and the mme has been updated
            console.log(this.mme_internalReference)
            if(this.modifMod==true && this.mme_internalReference!==null){
                console.log("supression");
                //Send a post request with the id of the dimension who will be deleted in the url
                console.log(this.mme_id)
                var consultUrl = (id) => `/mme/delete/link_to_eq/${id}`;
                axios.post(consultUrl(this.mme_id),{
                    eq_id:this.equipment_id_update,
                })
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
                    this.errors={};
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deleteMme','')
                    this.$refs.sucessAlert.showAlert(`Equipment mme unlink successfully`);
                })
                //If the controller sends errors we put it in the errors object 
                .catch(error => this.errors=error.response.data.errors) ;
            
            }else{
                this.$emit('deleteMme','')
            }
        },
        clearSelectError(value){
            delete this.errors[value];
        },
        clearAllError(){
            console.log("ERROR:",this.errors)
            
        }
    }
}
</script>

<style lang="scss">
    .titleForm{
        padding-left: 10px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }
</style>

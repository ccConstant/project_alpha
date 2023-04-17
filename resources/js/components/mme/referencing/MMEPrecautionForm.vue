<!--File name : MMEPrecautionForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to add or edit a precaution for a MME-->

<template>
    <div :class="divClass">
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <form class="container"  @keydown="clearError">
                <!--Call of the different component with their props-->
                <InputSelectForm @clearSelectError='clearSelectError' :info_text="infos_prctn[0].info_value" selectClassName="form-select w-50" :Errors="errors.prctn_type" name="prctn_type" label="Precaution type :" :options="enum_prctn_type" :isDisabled="!!isInConsultedMod" :selctedOption="this.prctn_type" :selectedDivName="this.divClass" v-model="prctn_type" :id_actual="precautionType"/>
                <InputTextAreaForm inputClassName="form-control w-50"  :info_text="infos_prctn[1].info_value" :Errors="errors.prctn_description" name="prctn_description" label="Description :" :isDisabled="!!isInConsultedMod" v-model="prctn_description" />
                <!--If addSucces is equal to false, the buttons appear -->

                <div v-if="this.addSucces==false ">
                    <!--If this precaution doesn't have a id the addMMEPrctn is called function else the updateMMEPrctn function is called -->
                    <div v-if="this.prctn_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addMMEPrctn" @update="updateMMEPrctn" :consultMod="this.isInConsultedMod" :savedAs="prctn_validate" :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addMMEPrctn" @update="updateMMEPrctn" :consultMod="this.isInConsultedMod" :savedAs="prctn_validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.prctn_id!==null">
                        <SaveButtonForm @add="addMMEPrctn" @update="updateMMEPrctn" :consultMod="this.isInConsultedMod" :modifMod="this.modifMod" :savedAs="prctn_validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <DeleteComponentButton :validationMode="prctn_validate" :consultMod="this.isInConsultedMod" @deleteOk="deleteComponent"/>
                </div>
            </form>
            <SucessAlert ref="sucessAlert"/>
        </div>
    </div>
</template>

<script>
import InputSelectForm from '../../input/InputSelectForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import DeleteComponentButton from '../../button/DeleteComponentButton.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'

export default {
    components : {
        InputSelectForm,
        InputTextAreaForm,
        SaveButtonForm,
        DeleteComponentButton,
        SucessAlert
    },
    props:{
        type:{
            type:String,
            default:null
        },
        description:{
            type:String,
            default:null
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
        usg_id:{
            type:Number
        }
    },
    data(){
        return{
            prctn_type:this.type,
            prctn_description:this.description,
            prctn_validate:this.validate,
            prctn_id:this.id,
            mme_id_add:this.mme_id,
            mme_id_update:this.$route.params.id,
            enum_prctn_type : [],
            errors:{},
            addSucces:false,
            isInConsultedMod:this.consultMod,
            loaded:false,
            infos_prctn:[],
            precautionType:"PrecautionType",
        }
    },
    created(){
        axios.get('/precaution/enum/type')
            .then (response=> {
                this.enum_prctn_type=response.data;
            })
            .catch(error => console.log(error)) ;

        axios.get('/info/send/mme_precaution')
            .then (response=> {
                console.log(response.data)
                this.infos_prctn=response.data;
                this.loaded=true;
                })
            .catch(error => console.log(error)) ;
    },
    methods:{
        /*Sending to the controller all the information about the mme precautions so that it can be added to the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated
            reason: Reason of the adding
            lifesheet_created: If a lifesheet already exist for this MME*/
        addMMEPrctn(savedAs, reason, lifesheet_created){
            if(!this.addSucces){
                //ID of the mme in which the precaution will be added
                let id;
                //If the user is not in the modification mode, we set the id with the value of mme_id_add
                if(!this.modifMod){
                        id=this.mme_id_add
                //else the user is in the update menu, we allocate to the id the value of the id get in the url
                }else{
                    id=this.mme_id_update;
                }
                /*The first post to verify if all the fields are filled correctly
                The type, description, validate options and the id of the usage are sent to the controller*/
                axios.post('/precaution/verif',{
                    prctn_type:this.prctn_type,
                    prctn_description:this.prctn_description,
                    prctn_validate :savedAs,
                    usage_id:this.usg_id,
                })
                .then(response =>{
                    this.errors={};
                    axios.post("/mme/add/usage/prctn",{
                        prctn_type:this.prctn_type,
                        prctn_description:this.prctn_description,
                        prctn_validate :savedAs,
                        mme_id:id,
                        usage_id:this.usg_id

                    })
                    //If the precaution is added successfully
                    .then(response =>{
                        //We test if a life sheet has been already created
                        //If it's the case we create a new enregistrement of history for saved the reason of the update
                        if (lifesheet_created==true){
                            axios.post(`/history/add/mme/${id}`,{
                                history_reasonUpdate :reason,
                            });
                             window.location.reload();
                        }
                        this.$refs.sucessAlert.showAlert(`Usage precaution added successfully and saved as ${savedAs}`);
                        //If the user is not in modification mode
                        if(!this.modifMod){
                            //The form pass in consulting mode and addSucces pass to True
                            this.isInConsultedMod=true;
                            this.addSucces=true
                        }
                        //the id of the precaution take the value of the newly created id
                        this.prctn_id=response.data;
                        //The validate option of this precaution takes the value of savedAs
                        this.prctn_validate=savedAs;

                    })
                    .catch(error => this.errors=error.response.data.errors) ;

                })
                .catch(error => this.errors=error.response.data.errors) ;
            }

        },
        /*Sending to the controller all the information about the precaution so that it can be updated in the database
        Params:
            savedAs: Value of the validation option: drafted, to_be_validated or validated  */
        updateMMEPrctn(savedAs, reason, lifesheet_created){
            /*The first post to verify if all the fields are filled correctly
                Type, description, validate options and the id of the usage are sent to the controller*/
            axios.post('/precaution/verif',{
                    prctn_type:this.prctn_type,
                    prctn_description:this.prctn_description,
                    prctn_validate :savedAs,
                    usage_id:this.usg_id,
                })
                .then(response =>{
                    this.errors={};
                        /*If all the fields are correctly filled, a new post this time to add the precaution in the database
                            Type, description, validate options and the id of the usage are sent to the controller
                            In the post url the id correspond to the id of the precaution who will be updated*/
                    const consultUrl = (id) => `/mme/update/prctn/${id}`;
                    axios.post(consultUrl(this.prctn_id),{
                            prctn_type:this.prctn_type,
                            prctn_description:this.prctn_description,
                            prctn_validate :savedAs,
                            usage_id:this.usg_id,
                            mme_id:this.mme_id_update,
                        })
                        .then(response =>{
                            const id = this.mme_id_update;
                            //We test if a life sheet has been already created
                            //If it's the case we create a new enregistrement of history for saved the reason of the update
                            if (lifesheet_created==true){
                                axios.post(`/history/add/mme/${id}`,{
                                    history_reasonUpdate :reason,
                                });
                                 window.location.reload();
                            }
                            this.prctn_validate=savedAs;
                            this.$refs.sucessAlert.showAlert(`Usage precaution updated successfully and saved as ${savedAs}`);
                        })
                        .catch(error => this.errors=error.response.data.errors) ;
                })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clears all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        //Function for deleting a precaution from the view and the database
        deleteComponent(reason, lifesheet_created){
            //If the user is in update mode and the precaution exist in the database
            if(this.modifMod==true && this.prctn_id!==null){
                console.log("supression");
                console.log(this.mme_id_update)
                //Send a post-request with the id of the precaution who will be deleted in the url
                const consultUrl = (id) => `/precaution/delete/${id}`;
                axios.post(consultUrl(this.prctn_id),{
                    mme_id:this.mme_id_update
                })
                .then(response =>{
                    //Emit to the parent component that we want to delete this component
                    this.$emit('deletePrctn','')
                    const id=this.mme_id_update
                    //We test if a life sheet has been already created
                    //If it's the case we create a new enregistrement of history for saved the reason of the update
                    if (lifesheet_created==true){
                        axios.post(`/history/add/mme/${id}`,{
                            history_reasonUpdate :reason,
                        });
                        window.location.reload();
                    }
                    this.$refs.sucessAlert.showAlert(`Usage precaution deleted successfully`);
                })
                .catch(error => this.errors=error.response.data.errors) ;

            }else{
                this.$emit('deletePrctn','')
                 this.$refs.sucessAlert.showAlert(`Empty Usage precaution deleted successfully`);
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

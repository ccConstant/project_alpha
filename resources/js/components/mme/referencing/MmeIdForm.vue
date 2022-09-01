<template>
      <div class="mmeID" v-if="loaded==true">
        <h2 class="titleForm1">MME ID : {{mme_internalReference}}</h2>
        <!--Creation of the form,If user press in any key in a field we clear all error of this field  -->
        <form class="container" @keydown="clearError">
            <br><br><!--Call of the different component with their props-->
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_internalReference" name="mme_internalReference" label="Alpha reference :" :isDisabled="!!isInConsultMod" v-model="mme_internalReference" :info_text="infos_idCard[0].info_value" />
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_externalReference" name="mme_externalReference" label="External reference :" :isDisabled="!!isInConsultMod"  v-model="mme_externalReference" :info_text="infos_idCard[1].info_value"/>
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_name" name="mme_name" label="MME name :" :isDisabled="!!isInConsultMod" v-model="mme_name" :info_text="infos_idCard[2].info_value" />
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_serialNumber" name="mme_serialNumber" label="MME serial Number :" :isDisabled="!!isInConsultMod" v-model="mme_serialNumber" :info_text="infos_idCard[3].info_value" />
            <InputTextForm inputClassName="form-control w-50" :Errors="errors.mme_constructor" name="mme_constructor" label="MME constructor :" :isDisabled="!!isInConsultMod" v-model="mme_constructor" :info_text="infos_idCard[4].info_value" />
            <InputTextAreaForm inputClassName="form-control w-50" :Errors="errors.mme_remarks" name="mme_remarks" label="Remarks :" :isDisabled="!!isInConsultMod" v-model="mme_remarks" :info_text="infos_idCard[5].info_value"/>
            <InputTextWithOptionForm inputClassName="form-control w-50" :Errors="errors.mme_set" name="mme_set" label="MME Set" :isDisabled="!!isInConsultMod" :options="enum_sets" v-model="mme_set"  :info_text="infos_idCard[6].info_value" />
            <InputTextForm v-if="this.mme_importFrom!== undefined " inputClassName="form-control w-50" name="mme_importFrom" label="Import From :" isDisabled v-model="mme_importFrom" />
            <SaveButtonForm ref="saveButton" v-if="this.addSucces==false" @add="addMME" @update="updateMME" :consultMod="this.isInConsultMod" :modifMod="this.modifMod" :savedAs="mme_validate" />
            <div v-if="this.modifMod!=true">
                <div v-if="this.isInConsultMod!=true">
                    <MMEImportationModal v-if="disableImport==false" :set="this.mme_set" @choosedMME="importFrom"/>
                </div>
            </div>
            <SucessAlert ref="sucessAlert"/>
        </form>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputTextForm from '../../input/InputTextForm.vue'
import InputTextAreaForm from '../../input/InputTextAreaForm.vue'
import InputNumberForm from '../../input/InputNumberForm.vue'
import InputTextWithOptionForm from '../../input/InputTextWithOptionForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import MMEImportationModal from './MMEImportationModal.vue'
import SucessAlert from '../../alert/SuccesAlert.vue'
export default {
        components : {
        InputTextForm,
        InputNumberForm,
        InputTextWithOptionForm,
        InputTextAreaForm,
        SaveButtonForm,
        MMEImportationModal,
        SucessAlert
    },
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
        serialNumber:{
            type:String
        },
        construct:{
            type:String
        },
        remarks:{
            type:String
        },
        set:{
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
        disableImport:{
            type:Boolean,
            default:false
        },
        state_id:{
            type:String,
            default:null
        }
    },
    data(){
        return{
            mme_internalReference : this.internalReference,
            mme_externalReference :this.externalReference,
            mme_name :this.name,
            mme_serialNumber: this.serialNumber,
            mme_constructor :this.construct,
            mme_remarks: this.remarks,
            mme_set : this.set,
            mme_validate:this.validate,
            mme_importFrom:undefined,
            isInConsultMod:this.consultMod,
            enum_sets:[],
            mme_id:this.$route.params.id,
            errors:{},
            addSucces:false,
            infos_idCard:[],
            info_mme_internalReference:'',
            loaded:false
        }
    },
    /*All function inside the created option is called after the component has been created.*/
    created(){
        axios.get('/mme/sets')
            .then (response=> this.enum_sets=response.data) 
            .catch(error => console.log(error)) ; 

        axios.get('/info/send/mme')
            .then (response=> {
                this.infos_idCard=response.data;
                this.loaded=true;
                }) 
            .catch(error => console.log(error)) ;
    },
    methods:{
        addMME(savedAs){
            if(!this.addSucces){
                 axios.post('/mme/verif',{
                    mme_internalReference : this.mme_internalReference, 
                    mme_externalReference : this.mme_externalReference,
                    mme_name : this.mme_name,
                    mme_serialNumber : this.mme_serialNumber,
                    mme_constructor : this.mme_constructor,
                    mme_remarks : this.mme_remarks,
                    mme_set : this.mme_set,
                    mme_validate : savedAs,
                    reason:'add'
                })
                .then(response =>{
                        this.errors={};
                        if(this.state_id!==null){
                            var consultUrl = (state_id) => `/state/mme/${state_id}`;
                            axios.post(consultUrl(this.state_id),{
                                mme_internalReference : this.mme_internalReference, 
                                mme_externalReference : this.mme_externalReference,
                                mme_name : this.mme_name,
                                mme_serialNumber : this.mme_serialNumber,
                                mme_constructor : this.mme_constructor,
                                mme_remarks : this.mme_remarks,
                                mme_set : this.mme_set,
                                mme_validate : savedAs,
                            })
                            .then(response =>{
                                    this.$refs.sucessAlert.showAlert(`ID card saved as ${savedAs} successfully`);
                                    this.addSucces=true; 
                                    this.isInConsultMod=true;
                                    this.mme_id=response.data;
                                    this.$emit('MMEID',this.mme_id);
                                })
                            .catch(error => this.errors=error.response.data.errors) ; 

                        }else{
                            axios.post('/mme/add',{
                                mme_internalReference : this.mme_internalReference, 
                                mme_externalReference : this.mme_externalReference,
                                mme_name : this.mme_name,
                                mme_serialNumber : this.mme_serialNumber,
                                mme_constructor : this.mme_constructor,
                                mme_remarks : this.mme_remarks,
                                mme_set : this.mme_set,
                                mme_validate : savedAs,
                                createdBy_id:this.$userId.id
                            })
                            .then(response =>{
                                    this.$refs.sucessAlert.showAlert(`ID card saved as ${savedAs} successfully`);
                                    this.addSucces=true; 
                                    this.isInConsultMod=true;
                                    this.mme_id=response.data;
                                    this.$emit('MMEID',this.mme_id);
                                })
                            .catch(error => this.errors=error.response.data.errors) ; 
                        }
                    })
                .catch(error => this.errors=error.response.data.errors) ; 
            }
        },
        /*Sending to the controller all the information about the mme so that it can be updated to the database */ 
        updateMME(savedAs){
                    axios.post('/mme/verif',{
                    mme_internalReference : this.mme_internalReference, 
                    mme_externalReference : this.mme_externalReference,
                    mme_name : this.mme_name,
                    mme_serialNumber : this.mme_serialNumber,
                    mme_constructor : this.mme_constructor,
                    mme_remarks : this.mme_remarks,
                    mme_set : this.mme_set,
                    mme_validate : savedAs,
                    mme_id:this.mme_id,
                    reason:'update'
                })
                .then(response =>{
                        this.errors={};
                        var consultUrl = (id) => `/mme/update/${id}`;
                        axios.post(consultUrl(this.mme_id),{
                            mme_internalReference : this.mme_internalReference, 
                            mme_externalReference : this.mme_externalReference,
                            mme_name : this.mme_name,
                            mme_serialNumber : this.mme_serialNumber,
                            mme_constructor : this.mme_constructor,
                            mme_remarks : this.mme_remarks,
                            mme_set : this.mme_set,
                            mme_validate : savedAs,
                        })
                        .then(response => {
                            this.$refs.sucessAlert.showAlert(`ID card updated as ${savedAs} successfully`);
                            this.mme_validate=savedAs;

                            })
                        .catch(error => this.errors=error.response.data.errors) ; 
                    })
                .catch(error => this.errors=error.response.data.errors) ;
        },
        /*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },
        importFrom(value){
            this.mme_importFrom=value.mme_internalReference;
            this.$emit('importFromMMEID',value.id);
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
        padding-left: 80px;
    }
    form{
        margin: 20px;
        margin-bottom: 100px;
    }

</style>
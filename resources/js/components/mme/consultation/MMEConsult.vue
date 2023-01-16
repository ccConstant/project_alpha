<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="mme_consultation">
            <ErrorAlert ref="errorAlert"/>
            <SuccesAlert ref="successAlert"/>
            <h1>MME Consultation</h1>
            <div v-if="this.mme_eq.length>0">
                <p class="mme_linked"> The MME is linked to the equipment: <router-link  :to="{name:'url_eq_consult',params:{id:this.mme_eq[0].eq_id} }"> {{this.mme_eq[0].eq_internalReference}} </router-link></p>
            </div>
            <ValidationButton @ValidatePressed="Validate" :mme_id="mme_id" :validationMethod="validationMethod" :Errors="errors"/>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            MME Id Card
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                        <MmeIdForm :internalReference="mme_idCard.mme_internalReference" :externalReference="mme_idCard.mme_externalReference"
                            :name="mme_idCard.mme_name" :serialNumber="mme_idCard.mme_serialNumber" :construct="mme_idCard.mme_constructor" 
                            :remarks="mme_idCard.mme_remarks" :set="mme_idCard.mme_set" :validate="mme_idCard.mme_validate"
                            consultMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item"  v-if="mme_files.length>0">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            MME File
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceAMMEFile :importedFile="mme_files" consultMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" v-if="mme_verifs.length>0">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            MME Verification
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <ReferenceAMMEVerif :importedVerif="mme_verifs" consultMod/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item" v-if="mme_usages.length>0">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            MME Usage
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <ReferenceAMMEUsage :importedUsage="mme_usages" consultMod/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ErrorAlert from '../../alert/ErrorAlert.vue'
import SuccesAlert from '../../alert/SuccesAlert.vue'
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ValidationButton from '../../button/ValidationButton.vue'
import ReferenceAMMEFile from '../referencing/ReferenceAMMEFile.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from '../referencing/ReferenceAMMEUsage.vue'
export default {
    components: {
        MmeIdForm,
        ErrorAlert,
        SuccesAlert,
        ValidationButton,
        ReferenceAMMEFile,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage
    },
    data(){
        return{
            mme_id:this.$route.params.id.toString(),
            mme_idCard:null,
            mme_files:null,
            mme_verifs:null,
            mme_usages:null,
            loaded:false,
            validationMethod:this.$route.query.method,
            errors:[],
            mme_eq:[],
        }
    },
    created(){
        if(this.validationMethod=='technical' && this.$userId.user_makeTechnicalValidationRight!=true){
            this.$router.replace({ name: "url_mme_list"})
        }else if(this.validationMethod=='quality' && this.$userId.user_makeQualityValidationRight!=true){
            this.$router.replace({ name: "url_mme_list"})
        }

         var consultUrl = (id) => `/mme/eq_linked/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then (response => {
                this.mme_eq=response.data;
                })
            .catch(error => console.log(error));


        var consultUrl = (id) => `/mme/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then (response => {
                this.mme_idCard=response.data;
                })
            .catch(error => console.log(error));

        var consultUrl = (id) => `/file/send/mme/${id}`;
            axios.get(consultUrl(this.mme_id))
                .then (response=> {
                    this.mme_files=response.data
                })
                .catch(error => console.log(error)) ;

        var consultUrl = (id) => `/verifs/send/${id}`;
            axios.get(consultUrl(this.mme_id))
                .then (response=> {
                    this.mme_verifs=response.data
                })
                .catch(error => console.log(error)) ;

        var consultUrl = (id) => `/mme_usage/send/${id}`;
            axios.get(consultUrl(this.mme_id))
            .then (response=> {
                this.mme_usages=response.data
                this.loaded=true})
            .catch(error => console.log(error)) ;



    },
    methods:{
        Validate(){
            if(this.validationMethod=='technical' && this.$userId.user_makeTechnicalValidationRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            }else if(this.validationMethod=='quality' && this.$userId.user_makeQualityValidationRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            }else{
                var validVerifUrl = (id) => `/mme/verifValidation/${id}`;
                axios.post(validVerifUrl(this.mme_id),{
                    })
                    .then(response =>{
                        var techVeriftUrl = (id) => `/mme/validation/${id}`;
                        axios.post(techVeriftUrl(this.mme_id),{
                            reason:this.validationMethod,
                            enteredBy_id:this.$userId.id
                        })
                        .then(response =>{
                            this.$refs.successAlert.showAlert(`${this.validationMethod} made succesfully`);
                            this.$router.replace({ name: "url_mme_list"}) 
                        })
                        //If the controller sends errors we put it in the errors object 
                        .catch(error => this.errors=error.response.data.errors) ;
                    ;})
                    //If the controller sends errors we put it in the errors object 
                .catch(error =>{
                    this.errors=error.response.data.errors
                });
            }

        }
    }

}
</script>

<style lang="scss">
    .technical_validate_button {
        display: block;
        margin : auto;
        margin-bottom: 15px;
        
    };

    .quality_validate_button{
        display: block;
        margin : auto;
        margin-bottom: 15px;
    };

    .mme_consultation{
        h1{
            text-align: center;
        }
    };

    .mme_linked{
        font-size : 18px;
        font-style: italic;
        color : deepskyblue;


    };





</style>
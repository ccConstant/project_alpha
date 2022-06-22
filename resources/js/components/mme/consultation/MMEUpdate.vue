<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="mme_update">
            <h1>MME Update</h1>
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
                            modifMod/>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            MME File
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                            <div class="accordion-body">
                                <ReferenceAMMEFile  :importedFile="mme_files" modifMod/>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            MME Verification
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                            <div class="accordion-body">
                                <ReferenceAMMEVerif  :importedVerif="mme_verifs" modifMod/>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            MME Usage
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                            <div class="accordion-body">
                                <ReferenceAMMEUsage :importedVerif="mme_usages" modifMod/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MmeIdForm from '../referencing/MmeIdForm.vue'
import ReferenceAMMEFile from '../referencing/ReferenceAMMEFile.vue'
import ReferenceAMMEVerif from '../referencing/ReferenceAMMEVerif.vue'
import ReferenceAMMEUsage from '../referencing/ReferenceAMMEUsage.vue'
export default {
        components: {
        MmeIdForm,
        ReferenceAMMEFile,
        ReferenceAMMEVerif,
        ReferenceAMMEUsage
    },

    data(){
        return{
            mme_id:this.$route.params.id,
            mme_idCard:null,
            mme_files:null,
            loaded:false,
        }
    },
    created(){
        var consultUrl = (id) => `/mme/${id}`;
        axios.get(consultUrl(this.mme_id))
            .then (response =>{
                this.mme_idCard=response.data
                this.$router.push({ name: "url_mme_update", params: {id:this.mme_id}, query: {signed:response.data.mme_lifeSheetCreated }}).catch(()=>{});
                if(response.data.mme_lifeSheetCreated==true && 
                (this.$userId.user_updateDescriptiveLifeSheetDataSignedRight!=true &&
                this.$userId.user_deleteDataSignedLinkedTommeOrMmeRight!=true) ){
                    this.mme_lifeSheetCreated=response.data.mme_lifeSheetCreated;
                    this.$router.push({ name: "home"})
                }
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
                    this.mme_verifs=response.data})
                .catch(error => console.log(error)) ;

        var consultUrl = (id) => `/mme_usage/send/${id}`;
            axios.get(consultUrl(this.mme_id))
            .then (response=> {
                this.mme_usages=response.data
                console.log(response.data)
                this.loaded=true})
            .catch(error => console.log(error)) ;
    }

}
</script>

<style lang="scss">
    .mme_update{
        .green_card{
            background-color: #b0f2b6;
        }
        .yellow_card{
            background-color:lightyellow;
        }
        h1{
            text-align: center;
        }
    }
</style>

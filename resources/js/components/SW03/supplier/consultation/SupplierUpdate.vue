<template>
    <div v-if="loaded===false" >
        <b-spinner variant="primary"></b-spinner>
    </div>
    <div v-else class="supplier_consultation">
        <ErrorAlert ref="errorAlert"/>
        <SuccessAlert ref="successAlert"/>
        <h1>Supplier Consultation</h1>
        <!--        <ValidationButton @ValidatePressed="Validate" :eq_id="eq_id" :validationMethod="validationMethod" :Errors="errors"/>-->
        <SupplierIDForm
            :ID="this.supplr_id"
            :Name="this.supplr_idCard.supplr_name"
            :ReceptionNumber="this.supplr_idCard.supplr_receptionNumber"
            :FormId="this.supplr_idCard.supplr_formId"
            :consFam_id="this.supplr_idCard.consFam_id"
            :compFam_id="this.supplr_idCard.compFam_id"
            :rawFam_id="this.supplr_idCard.rawFam_id"
            :agreementNumber="this.supplr_idCard.supplr_agreementNumber"
            :qualityCertificateNumber="this.supplr_idCard.supplr_qualityCertificateNumber"
            :specificsInstructions="this.supplr_idCard.supplr_specificsInstructions"
            :siret="this.supplr_idCard.supplr_siret"
            :webSite="this.supplr_idCard.supplr_webSite"
            :activity="this.supplr_idCard.supplr_activity"
            :real="this.supplr_idCard.supplr_real"
            :vatNumber="this.supplr_idCard.supplr_VATNumber"
            :critical="this.supplr_idCard.supplr_critical"
            :endTinkToFolder="this.supplr_idCard.supplr_endTinkToFolder"
            :active="this.supplr_idCard.supplr_active"
            modifMod
        />
        <div class="accordion" v-if="supplr_id != null">
            <div class="accordion-item"v-if="supplr_address.length > 0">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Supplier's Address
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <ReferenceAnAddress :importedAddress="supplr_address" modifMod/>
                    </div>
                </div>
            </div>
            <div class="accordion-item" v-if="supplr_contact.length > 0">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        Supplier's Contact
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                    <div class="accordion-body">
                        <ReferenceAContact :importedContact="supplr_contact" modifMod/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import ErrorAlert from "../../../alert/ErrorAlert.vue";
import SuccessAlert from "../../../alert/SuccesAlert.vue";
import ValidationButton from "../../../button/ValidationButton.vue";
import SupplierIDForm from "../referencing/SupplierIDForm.vue";
import ReferenceAnAddress from "../referencing/ReferenceAnAddress.vue";
import ReferenceAContact from "../referencing/ReferenceAContact.vue";
export default {
    components: {
        ReferenceAContact,
        ReferenceAnAddress,
        SupplierIDForm,
        ValidationButton,
        SuccessAlert,
        ErrorAlert
    },
    data() {
        return {
            supplr_id: Number(this.$route.params.id),
            supplr_idCard: null,
            supplr_contact: null,
            supplr_address: null,
            validationMethod: this.$route.query.method,
            errors: [],
            loaded: false
        }
    },
    created() {
        if(this.validationMethod === 'technical' && this.$userId.user_makeTechnicalValidationRight !== true){
            this.$router.replace({ name: "url_eq_list"})
        }
        axios.get('/supplier/send/' + this.supplr_id)
            .then(response => {
                this.supplr_idCard = response.data;
            })
            .catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.message);
            });

        axios.get('/supplier/contact/send/' + this.supplr_id)
            .then(response => {
                this.supplr_contact = response.data;
            })

        axios.get('/supplier/adr/send/' + this.supplr_id)
            .then(response => {
                this.supplr_address = response.data;
                this.loaded = true
            })
    },
    methods: {
        validate() {
            if(this.validationMethod=='technical' && this.$userId.user_makeTechnicalValidationRight!=true){
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                // TODO: Validation
            }
        }
    }
}
</script>

<style>

</style>

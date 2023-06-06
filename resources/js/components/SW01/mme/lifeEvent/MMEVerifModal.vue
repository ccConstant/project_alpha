<!--File name : MMEVerifModal.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to make a modal for actions on a verification who affect a MME-->

<template>
  <div class="op_verifRlz_modal_comp">
        <ErrorAlert ref="errorAlert"/>
        <SuccesAlert ref="succesAlert"/>

        <div class="verifRlz_button" v-if="this.$userId.user_makeMmeOpValidationRight==true">
            <b-button  @click="$bvModal.show(`modal-verifManagmentUpdate-${_uid}`)" variant="primary">Update</b-button>
        </div>
        <div class="verifRlz_button" v-else>
            <b-button  disabled variant="primary">Update</b-button>
        </div>
        <div class="verifRlz_button" v-if="this.$userId.user_makeMmeRespValidationRight==true">
            <b-button v-if="approvedBy_lastName==null" @click="$bvModal.show(`modal-verifManagmentApprove-${_uid}`)" variant="primary">Approve</b-button>
        </div>
        <div class="verifRlz_button" v-else>
            <b-button v-if="approvedBy_lastName==null" variant="primary" disabled>Approve</b-button>
        </div>
        <div class="verifRlz_button" v-if="this.$userId.user_makeMmeOpValidationRight==true">
            <b-button v-if="realizedBy_lastName==null"  @click="$bvModal.show(`modal-verifManagmentRealize-${_uid}`)" variant="primary">I realized it</b-button>
        </div>
        <div class="verifRlz_button" v-else>
            <b-button v-if="realizedBy_lastName==null" disabled variant="primary">I realized it</b-button>
        </div>
        <div v-if="this.$userId.user_makeMmeRespValidationRight==false">
            <p class="text-danger"> You don't have the right to approve a record</p>
        </div>


        <b-modal :id="`modal-verifManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
            <MMEVerifRlzForm modifMod  :mme_id="mme_id" :state_id="state_id" :id="verifRlz_id" :verif_id_prop="verif_id" :verif_number_prop="verif_number"
            :verif_expectedResult_prop="verif_expectedResult" :verif_nonComplianceLimit_prop="verif_nonComplianceLimit"
            :verif_description_prop="verif_description" :verif_protocol_prop="verif_protocol" :isPassed="verifRlz_isPassed"
            :reportNumber="verifRlz_reportNumber" :startDate="verifRlz_startDate"  :endDate="verifRlz_endDate"
                             :comment="verifRlz_comment"
            :validate="verifRlz_validate" @deleteverifRlz="closeModal()"/>
        </b-modal>

        <b-modal :id="`modal-verifManagmentApprove-${_uid}`" title="Approve the record" @ok="handleOkApprove" @hidden="resetModal">
            <MMEVerifRlzForm consultMod  :mme_id="mme_id" :state_id="state_id" :id="verifRlz_id" :verif_id_prop="verif_id" :verif_number_prop="verif_number"
            :verif_description_prop="verif_description" :verif_protocol_prop="verif_protocol" :isPassed="verifRlz_isPassed"
            :verif_expectedResult_prop="verif_expectedResult" :verif_nonComplianceLimit_prop="verif_nonComplianceLimit"
            :reportNumber="verifRlz_reportNumber" :startDate="verifRlz_startDate"  :endDate="verifRlz_endDate"
                             :comment="verifRlz_comment"
            :validate="verifRlz_validate" @deleteverifRlz="closeModal()"/>
            <h4>Please enter your Username and your password to approve</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>

        <b-modal :id="`modal-verifManagmentRealize-${_uid}`" title="Realize the record" @ok="handleOkRealize" @hidden="resetModal">
            <MMEVerifRlzForm consultMod  :mme_id="mme_id" :state_id="state_id" :id="verifRlz_id" :verif_id_prop="verif_id" :verif_number_prop="verif_number"
            :verif_description_prop="verif_description" :verif_protocol_prop="verif_protocol" :isPassed="verifRlz_isPassed"
            :verif_expectedResult_prop="verif_expectedResult" :verif_nonComplianceLimit_prop="verif_nonComplianceLimit"
            :reportNumber="verifRlz_reportNumber" :startDate="verifRlz_startDate"  :endDate="verifRlz_endDate"
                             :comment="verifRlz_comment"
            :validate="verifRlz_validate" @deleteverifRlz="closeModal()"/>
            <h4>Please enter your Username and your password to realize</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>
    </div>
</template>

<script>
import MMEVerifRlzForm  from'./MMEVerifRlzForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputPasswordForm from '../../../input/InputPasswordForm.vue'
import ErrorAlert from '../../../alert/ErrorAlert.vue'
import SuccesAlert from '../../../alert/SuccesAlert.vue'


export default {
    components:{
        MMEVerifRlzForm,
        InputTextForm,
        InputPasswordForm,
        ErrorAlert,
        SuccesAlert
    },
    props:{
        mme_id:{
            type:Number
        },
        state_id:{
            type:Number
        },
        verif_id:{
            type:Number
        },
        verif_number:{
            type:String
        },
        verif_description:{
            type:String
        },
        verif_expectedResult:{
            type:String
        },
        verif_nonComplianceLimit:{
            type:String
        },
        verif_protocol:{
            type:String
        },
        verifRlz_id:{
            type:Number
        },
        verifRlz_reportNumber:{
            type:String
        },
        verifRlz_isPassed:{
            type:Boolean
        },
        verifRlz_startDate:{
            type:String
        },
        verifRlz_endDate:{
            type:String
        },
        verifRlz_validate:{
            type:String
        },
        approvedBy_lastName:{
            type:String
        },
        realizedBy_lastName:{
            type:String
        },
        verifRlz_comment : {
            type:String
        },
    },
    data(){
        return{
            errors:{},
            user_pseudo:'',
            user_password:'',
            compId:this.$userId.id
        }
    },
    methods:{
        closeModal(modal){
            this.$bvModal.hide(modal);
            this.resetModal();
            this.$emit('okReload','')
        },
        handleOkUpdate(){
            this.$emit('okReload','')
        },
        resetModal(){
            this.errors={},
            this.user_pseudo='',
            this.user_password=''
        },
        handleOkApprove(bvModalEvent){
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            const postUrlAdd = (id) => `/verifRlz/approve/${id}`;
            axios.post(postUrlAdd(this.verifRlz_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.compId
            })
            .then(response =>{
                this.$refs.succesAlert.showAlert("Succesfully aprouved");
                this.closeModal(bvModalEvent.target.id);
            })
            .catch(error =>{this.errors=error.response.data.errors});
        },

        handleOkRealize(bvModalEvent){
            // Trigger submit handler
            bvModalEvent.preventDefault()
            const postUrlAdd = (id) => `/verifRlz/realize/${id}`;
            axios.post(postUrlAdd(this.verifRlz_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.compId
            })
            .then(response =>{
                this.$refs.succesAlert.showAlert("Succesfully realized");
                this.closeModal(bvModalEvent.target.id);
            })
            .catch(error =>{this.errors=error.response.data.errors});

        }
    }

}
</script>

<style lang="scss">
    .op_verifRlz_modal_comp{
        .verifRlz_button{
            display: inline-block;
        }
    }

</style>

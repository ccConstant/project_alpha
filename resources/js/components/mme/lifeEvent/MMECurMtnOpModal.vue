<template>
    <div class="op_cur_modal_comp"> 
        <div class="cur_button" v-if="this.$userId.user_makeEqOpValidationRight==true">
            <b-button  @click="$bvModal.show(`modal-curMtnOpManagmentUpdate-${_uid}`)" variant="primary">Update</b-button>
        </div>
        <div class="cur_button" v-else>
            <b-button  disabled variant="primary">Update</b-button>
        </div>  
        
        <div class="cur_button" v-if="this.$userId.user_makeEqOpValidationRight==true">
                <b-button v-if="realizedBy_lastName==null" @click="$bvModal.show(`modal-curMtnOpManagmentRealize-${_uid}`)" variant="primary">I realized it</b-button>
        </div>
        <div class="cur_button" v-else>
            <b-button v-if="realizedBy_lastName==null" disabled variant="primary">I realized it</b-button>
        </div>

        <div class="cur_button" v-if="this.$userId.user_makeQualityValidationRight==true">
            <b-button v-if="qualityVerifier_lastName==null" @click="$bvModal.show(`modal-curMtnOpManagmentQuality-${_uid}`)" variant="primary">Quality Check</b-button>
        </div>
        <div class="cur_button" v-else>
            <b-button v-if="qualityVerifier_lastName==null" disabled variant="primary">Quality Check</b-button>
        </div>

        <div class="cur_button" v-if="this.$userId.user_makeTechnicalValidationRight==true">
            <b-button v-if="technicalVerifier_lastName==null" @click="$bvModal.show(`modal-curMtnOpManagmentTechnical-${_uid}`)" variant="primary">Technical Check</b-button>
        </div>
        <div class="cur_button" v-else>
            <b-button v-if="technicalVerifier_lastName==null" disabled variant="primary">Technical Check</b-button>
        </div>

        <b-modal :id="`modal-curMtnOpManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
            <MmeCurMtnOpForm modifMod  :mme_id="mme_id" :state_id="state_id" :id="curMtnOp_id" 
            :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"  :endDate="curMtnOp_endDate"
            :validate="curMtnOp_validate" @deleteCurMtnOp="closeModal()"/>
        </b-modal>

        
        <b-modal :id="`modal-curMtnOpManagmentRealize-${_uid}`" title="Realize the record" @ok="handleOkRealize" @hidden="resetModal">
            <MmeCurMtnOpForm modifMod  :mme_id="mme_id" :state_id="state_id" :id="curMtnOp_id" 
            :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"  :endDate="curMtnOp_endDate"
            :validate="curMtnOp_validate" @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to realize</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>

        <b-modal :id="`modal-curMtnOpManagmentQuality-${_uid}`" title="Quality Check" @ok="handleOkQuality" @hidden="resetModal">
            <MmeCurMtnOpForm modifMod  :mme_id="mme_id" :state_id="state_id" :id="curMtnOp_id" 
            :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"  :endDate="curMtnOp_endDate"
            :validate="curMtnOp_validate" @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to make the quality check</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>

        <b-modal :id="`modal-curMtnOpManagmentTechnical-${_uid}`" title="Technical Check" @ok="handleOkTechnical" @hidden="resetModal">
            <MmeCurMtnOpForm modifMod  :mme_id="mme_id" :state_id="state_id" :id="curMtnOp_id" 
            :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"  :endDate="curMtnOp_endDate"
            :validate="curMtnOp_validate" @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to make the Technical check</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>
    </div>
</template>

<script>
import MmeCurMtnOpForm  from'./MmeCurMtnOpForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputPasswordForm from '../../input/InputPasswordForm.vue'
export default {
    components:{
        MmeCurMtnOpForm,
        InputTextForm,
        InputPasswordForm
    },
    props:{
        mme_id:{
            type:Number
        },
        state_id:{
            type:Number
        },
        curMtnOp_id:{
            type:Number
        },
        curMtnOp_reportNumber:{
            type:String
        },
        curMtnOp_startDate:{
            type:String
        },
        curMtnOp_endDate:{
            type:String
        },
        curMtnOp_validate:{
            type:String
        },
        realizedBy_lastName:{
            type:String
        },
        qualityVerifier_lastName:{
            type:String
        },
        technicalVerifier_lastName:{
            type:String
        },

    },
    data(){
        return{
            errors:{},
            user_pseudo:'',
            user_password:'',
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
            this.user_pseudo='',
            this.user_password=''
        },
        handleOkRealize(bvModalEvent){
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            var postUrlAdd = (id) => `/curMtnOp/realize/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.$userId.id
            })
            .then(response =>{ 
                this.closeModal(bvModalEvent.target.id);
            })
            .catch(error =>{this.errors=error.response.data.errors});
        },
        handleOkQuality(bvModalEvent){
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            
            var postUrlAdd = (id) => `/curMtnOp/qualityVerifier/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.$userId.id
                })
                .then(response =>{ 
                    this.closeModal(bvModalEvent.target.id);
                })
                .catch(error =>{this.errors=error.response.data.errors});
            
        },
        handleOkTechnical(bvModalEvent){
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            var postUrlAdd = (id) => `/curMtnOp/technicalVerifier/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.$userId.id
            })
            .then(response =>{ 
                this.closeModal(bvModalEvent.target.id);
            })
            .catch(error =>{this.errors=error.response.data.errors});
        }

    }

}
</script>

<style lang="scss">
    .op_cur_modal_comp{
        .cur_button{
            display: inline-block;
        }
    }
</style>
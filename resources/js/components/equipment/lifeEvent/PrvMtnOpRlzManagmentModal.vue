<template>
    <div class="op_prvrlz_modal_comp">   
        <ErrorAlert ref="errorAlert"/>

        <div class="prvrlz_button" v-if="this.$userId.user_makeEqOpValidationRight==true">
            <b-button  @click="$bvModal.show(`modal-prvMtnOpManagmentUpdate-${_uid}`)" variant="primary">Update</b-button>
        </div>
        <div class="prvrlz_button" v-else>
            <b-button  disabled variant="primary">Update</b-button>
        </div>
        <div class="prvrlz_button" v-if="this.$userId.user_makeEqRespValidationRight==true">
            <b-button v-if="approvedBy_lastName==null" @click="$bvModal.show(`modal-prvMtnOpManagmentApprove-${_uid}`)" variant="primary">Approve</b-button>
        </div>
        <div class="prvrlz_button" v-else>
            <b-button v-if="approvedBy_lastName==null" variant="primary" disabled>Approve</b-button>
        </div>
        <div class="prvrlz_button" v-if="this.$userId.user_makeEqOpValidationRight==true">
            <b-button v-if="realizedBy_lastName==null"  @click="$bvModal.show(`modal-prvMtnOpManagmentRealize-${_uid}`)" variant="primary">I realized it</b-button>
        </div>
        <div class="prvrlz_button" v-else>
            <b-button v-if="realizedBy_lastName==null" disabled variant="primary">I realized it</b-button>
        </div>
        <div v-if="this.$userId.user_makeEqRespValidationRight==false">
            <p class="text-danger"> You don't have the right to approve a record</p>
        </div>
        

        <b-modal :id="`modal-prvMtnOpManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
            <EquipmentPrvMtnOpRlzForm modifMod  :eq_id="eq_id" :state_id="state_id" :id="prvMtnOpRlz_id" :prvMtnOp_id_prop="prvMtnOp_id" :prvMtnOp_number_prop="prvMtnOp_number" 
            :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_protocol_prop="prvMtnOp_protocol"
            :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"  :endDate="prvMtnOpRlz_endDate"
            :validate="prvMtnOpRlz_validate" @deletePrvMtnOpRlz="closeModal()"/>
        </b-modal>

        <b-modal :id="`modal-prvMtnOpManagmentApprove-${_uid}`" title="Approve the record" @ok="handleOkApprove(`modal-prvMtnOpManagmentApprove-${_uid}`)" @hidden="resetModal">
            <EquipmentPrvMtnOpRlzForm consultMod  :eq_id="eq_id" :state_id="state_id" :id="prvMtnOpRlz_id" :prvMtnOp_id_prop="prvMtnOp_id" :prvMtnOp_number_prop="prvMtnOp_number" 
            :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_protocol_prop="prvMtnOp_protocol"
            :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"  :endDate="prvMtnOpRlz_endDate"
            :validate="prvMtnOpRlz_validate" @deletePrvMtnOpRlz="closeModal()"/>
            <h4>Please enter your Username and your password to approve</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>

        <b-modal :id="`modal-prvMtnOpManagmentRealize-${_uid}`" title="Realize the record" @ok="handleOkRealize(`modal-prvMtnOpManagmentRealize-${_uid}`)" @hidden="resetModal">
            <EquipmentPrvMtnOpRlzForm consultMod  :eq_id="eq_id" :state_id="state_id" :id="prvMtnOpRlz_id" :prvMtnOp_id_prop="prvMtnOp_id" :prvMtnOp_number_prop="prvMtnOp_number" 
            :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_protocol_prop="prvMtnOp_protocol"
            :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"  :endDate="prvMtnOpRlz_endDate"
            :validate="prvMtnOpRlz_validate" @deletePrvMtnOpRlz="closeModal()"/>
            <h4>Please enter your Username and your password to realize</h4>
            <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
            <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
        </b-modal>
    </div>

</template>

<script>
import EquipmentPrvMtnOpRlzForm  from'./EquipmentPrvMtnOpRlzForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputPasswordForm from '../../input/InputPasswordForm.vue'
import ErrorAlert from '../../alert/ErrorAlert.vue'

export default {
    components:{
        EquipmentPrvMtnOpRlzForm,
        InputTextForm,
        InputPasswordForm,
        ErrorAlert
    },
    props:{
        eq_id:{
            type:Number
        },
        state_id:{
            type:Number
        },
        prvMtnOp_id:{
            type:Number
        },
        prvMtnOp_number:{
            type:String
        },
        prvMtnOp_description:{
            type:String
        },
        prvMtnOp_protocol:{
            type:String
        },
        prvMtnOpRlz_id:{
            type:Number
        },
        prvMtnOpRlz_reportNumber:{
            type:String
        },
        prvMtnOpRlz_startDate:{
            type:String
        },
        prvMtnOpRlz_endDate:{
            type:String
        },
        prvMtnOpRlz_validate:{
            type:String
        },
        approvedBy_lastName:{
            type:String
        },
        realizedBy_lastName:{
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
            this.user_pseudo='',
            this.user_password=''
        },
        handleOkApprove(modal){
            // Prevent modal from closing

            console.log()
            var postUrlAdd = (id) => `/prvMtnOpRlz/approve/${id}`;
            axios.post(postUrlAdd(this.prvMtnOpRlz_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.compId
            })
            .then(response =>{ 
                this.closeModal(modal);
            })
            .catch(error =>{this.errors=error.response.data.errors});
        },

        handleOkRealize(modal){
            // Trigger submit handler
            var postUrlAdd = (id) => `/prvMtnOpRlz/realize/${id}`;
            axios.post(postUrlAdd(this.prvMtnOpRlz_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.compId
            })
            .then(response =>{ 
                this.closeModal(modal);
            })
            .catch(error =>{this.errors=error.response.data.errors});
            
        }
    }

}
</script>

<style lang="scss">
    .op_prvrlz_modal_comp{
        .prvrlz_button{
            display: inline-block;
        }
    }

</style>
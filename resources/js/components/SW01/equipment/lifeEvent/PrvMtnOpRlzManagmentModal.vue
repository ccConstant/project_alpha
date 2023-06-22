<!--File name : PrvMtnOpRlzManagementModal.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component to make the modal used to manage the different preventive maintenance operation already realized-->

<template>
    <div class="op_prvrlz_modal_comp">
        <ErrorAlert ref="errorAlert"/>

        <div v-if="this.$userId.user_makeEqOpValidationRight==true" class="prvrlz_button">
            <b-button variant="primary" @click="$bvModal.show(`modal-prvMtnOpManagmentUpdate-${_uid}`)">Update
            </b-button>
        </div>
        <div v-else class="prvrlz_button">
            <b-button disabled variant="primary">Update</b-button>
        </div>
        <div v-if="this.$userId.user_makeEqOpValidationRight==true" class="prvrlz_button">
            <b-button v-if="approvedBy_lastName==null" variant="primary"
                      @click="$bvModal.show(`modal-prvMtnOpManagmentApprove-${_uid}`)">Approve
            </b-button>
        </div>
        <div v-else class="prvrlz_button">
            <b-button v-if="approvedBy_lastName==null" disabled variant="primary">Approve</b-button>
        </div>
        <div v-if="this.$userId.user_makeEqOpValidationRight==true" class="prvrlz_button">
            <b-button v-if="realizedBy_lastName==null" variant="primary"
                      @click="$bvModal.show(`modal-prvMtnOpManagmentRealize-${_uid}`)">I realized it
            </b-button>
        </div>
        <div v-else class="prvrlz_button">
            <b-button v-if="realizedBy_lastName==null" disabled variant="primary">I realized it</b-button>
        </div>
        <div v-if="this.$userId.user_makeEqOpValidationRight==false">
            <p class="text-danger"> You don't have the right to approve a record</p>
        </div>
        <b-modal :id="`modal-prvMtnOpManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
            <EquipmentPrvMtnOpRlzForm :id="prvMtnOpRlz_id" :comment="prvMtnOpRlz_comment" :endDate="prvMtnOpRlz_endDate" :eq_id="eq_id"
                                      :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_id_prop="prvMtnOp_id"
                                      :prvMtnOp_number_prop="prvMtnOp_number"
                                      :prvMtnOp_protocol_prop="prvMtnOp_protocol"
                                      :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"
                                      :state_id="state_id"
                                      :validate="prvMtnOpRlz_validate"
                                      modifMod @deletePrvMtnOpRlz="closeModal()"/>
        </b-modal>
        <b-modal :id="`modal-prvMtnOpManagmentApprove-${_uid}`" title="Approve the record" @hidden="resetModal"
                 @ok="handleOkApprove">
            <EquipmentPrvMtnOpRlzForm :id="prvMtnOpRlz_id" :comment="prvMtnOpRlz_comment" :endDate="prvMtnOpRlz_endDate" :eq_id="eq_id"
                                      :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_id_prop="prvMtnOp_id"
                                      :prvMtnOp_number_prop="prvMtnOp_number"
                                      :prvMtnOp_protocol_prop="prvMtnOp_protocol"
                                      :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"
                                      :state_id="state_id"
                                      :validate="prvMtnOpRlz_validate"
                                      consultMod @deletePrvMtnOpRlz="closeModal()"/>
            <h4>Please enter your Username and your password to approve</h4>
            <InputTextForm v-model="user_pseudo" :Errors="errors.user_pseudo " inputClassName="form-control " label="Username :"
                           name="user_pseudo"/>
            <InputPasswordForm v-model="user_password" :Errors="errors.connexion" inputClassName="form-control "
                               label="Password :" name="user_password"/>
        </b-modal>
        <b-modal :id="`modal-prvMtnOpManagmentRealize-${_uid}`" title="Realize the record" @hidden="resetModal"
                 @ok="handleOkRealize">
            <EquipmentPrvMtnOpRlzForm :id="prvMtnOpRlz_id" :comment="prvMtnOpRlz_comment" :endDate="prvMtnOpRlz_endDate" :eq_id="eq_id"
                                      :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_id_prop="prvMtnOp_id"
                                      :prvMtnOp_number_prop="prvMtnOp_number"
                                      :prvMtnOp_protocol_prop="prvMtnOp_protocol"
                                      :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"
                                      :state_id="state_id"
                                      :validate="prvMtnOpRlz_validate"
                                      consultMod @deletePrvMtnOpRlz="closeModal()"/>
            <h4>Please enter your Username and your password to realize</h4>
            <InputTextForm v-model="user_pseudo" :Errors="errors.user_pseudo " inputClassName="form-control " label="Username :"
                           name="user_pseudo"/>
            <InputPasswordForm v-model="user_password" :Errors="errors.connexion" inputClassName="form-control "
                               label="Password :" name="user_password"/>
        </b-modal>
    </div>
</template>

<script>
import EquipmentPrvMtnOpRlzForm from './EquipmentPrvMtnOpRlzForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputPasswordForm from '../../../input/InputPasswordForm.vue'
import ErrorAlert from '../../../alert/ErrorAlert.vue'

export default {
    components: {
        EquipmentPrvMtnOpRlzForm,
        InputTextForm,
        InputPasswordForm,
        ErrorAlert
    },
    props: {
        eq_id: {
            type: Number
        },
        state_id: {
            type: Number
        },
        prvMtnOp_id: {
            type: Number
        },
        prvMtnOp_number: {
            type: String
        },
        prvMtnOp_description: {
            type: String
        },
        prvMtnOp_protocol: {
            type: String
        },
        prvMtnOpRlz_id: {
            type: Number
        },
        prvMtnOpRlz_reportNumber: {
            type: String
        },
        prvMtnOpRlz_startDate: {
            type: String
        },
        prvMtnOpRlz_endDate: {
            type: String
        },
        prvMtnOpRlz_validate: {
            type: String
        },
        approvedBy_lastName: {
            type: String
        },
        realizedBy_lastName: {
            type: String
        },
        prvMtnOpRlz_comment: {
            type: String
        },
    },
    data() {
        return {
            errors: {},
            user_pseudo: '',
            user_password: '',
            compId: this.$userId.id
        }
    },
    methods: {
        closeModal(modal) {
            this.$bvModal.hide(modal);
            this.resetModal();
            this.$emit('okReload', '');
        },
        handleOkUpdate() {
            this.$emit('okReload', '');
        },
        resetModal() {
            this.errors = {};
            this.user_pseudo = '';
            this.user_password = '';
        },
        handleOkApprove(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            const postUrlAdd = (id) => `/prvMtnOpRlz/approve/${id}`;
            axios.post(postUrlAdd(this.prvMtnOpRlz_id), {
                user_pseudo: this.user_pseudo,
                user_password: this.user_password,
                user_id: this.compId
            }).then(response => {
                this.closeModal(bvModalEvent.target.id);
            }).catch(error => {
                this.errors = error.response.data.errors
            });
        },
        handleOkRealize(bvModalEvent) {
            // Trigger submit handler
            bvModalEvent.preventDefault()
            const postUrlAdd = (id) => `/prvMtnOpRlz/realize/${id}`;
            axios.post(postUrlAdd(this.prvMtnOpRlz_id), {
                user_pseudo: this.user_pseudo,
                user_password: this.user_password,
                user_id: this.compId
            }).then(response => {
                this.closeModal(bvModalEvent.target.id);
            }).catch(error => {
                this.errors = error.response.data.errors
            });
        }
    }
}
</script>

<style lang="scss">
.op_prvrlz_modal_comp {
    .prvrlz_button {
        display: inline-block;
    }
}
</style>

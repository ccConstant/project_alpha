<!--File name : CurMtnOpModal.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component to show the modal for the current maintenance-->

<template>
    <div class="op_cur_modal_comp">
        <div v-if="this.$userId.user_makeEqOpValidationRight==true" class="cur_button">
            <b-button variant="primary" @click="$bvModal.show(`modal-curMtnOpManagmentUpdate-${_uid}`)">Update
            </b-button>
        </div>
        <div v-else class="cur_button">
            <b-button disabled variant="primary">Update</b-button>
        </div>

        <div v-if="this.$userId.user_makeEqOpValidationRight==true" class="cur_button">
            <b-button v-if="realizedBy_lastName==null" variant="primary"
                      @click="$bvModal.show(`modal-curMtnOpManagmentRealize-${_uid}`)">I realized it
            </b-button>
        </div>
        <div v-else class="cur_button">
            <b-button v-if="realizedBy_lastName==null" disabled variant="primary">I realized it</b-button>
        </div>

        <div v-if="this.$userId.user_makeQualityValidationRight==true" class="cur_button">
            <b-button v-if="qualityVerifier_lastName==null"
                      variant="primary" @click="$bvModal.show(`modal-curMtnOpManagmentQuality-${_uid}`)">Quality Check
            </b-button>
        </div>
        <div v-else class="cur_button">
            <b-button v-if="qualityVerifier_lastName==null" disabled variant="primary">Quality Check</b-button>
        </div>

        <div v-if="this.$userId.user_makeTechnicalValidationRight==true" class="cur_button">
            <b-button v-if="technicalVerifier_lastName==null"
                      variant="primary" @click="$bvModal.show(`modal-curMtnOpManagmentTechnical-${_uid}`)">Technical
                Check
            </b-button>
        </div>
        <div v-else class="cur_button">
            <b-button v-if="technicalVerifier_lastName==null" disabled variant="primary">Technical Check</b-button>
        </div>

        <b-modal :id="`modal-curMtnOpManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
            <EquipmentCurMtnOpForm :id="curMtnOp_id" :description="curMtnOp_description" :endDate="curMtnOp_endDate" :eq_id="eq_id"
                                   :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"
                                   :state_id="state_id" :validate="curMtnOp_validate"
                                   modifMod @deleteCurMtnOp="closeModal()"/>
        </b-modal>


        <b-modal :id="`modal-curMtnOpManagmentRealize-${_uid}`" title="Realize the record" @hidden="resetModal"
                 @ok="handleOkRealize">
            <EquipmentCurMtnOpForm :id="curMtnOp_id" :description="curMtnOp_description" :endDate="curMtnOp_endDate" :eq_id="eq_id"
                                   :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"
                                   :state_id="state_id"
                                   :validate="curMtnOp_validate" modifMod
                                   @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to realize</h4>
            <InputTextForm v-model="user_pseudo" :Errors="errors.user_pseudo " inputClassName="form-control " label="Username :"
                           name="user_pseudo"/>
            <InputPasswordForm v-model="user_password" :Errors="errors.connexion" inputClassName="form-control "
                               label="Password :" name="user_password"/>
        </b-modal>

        <b-modal :id="`modal-curMtnOpManagmentQuality-${_uid}`" title="Quality Check" @hidden="resetModal"
                 @ok="handleOkQuality">
            <EquipmentCurMtnOpForm :id="curMtnOp_id" :description="curMtnOp_description" :endDate="curMtnOp_endDate" :eq_id="eq_id"
                                   :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"
                                   :state_id="state_id"
                                   :validate="curMtnOp_validate" consultMod
                                   @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to make the quality check</h4>
            <InputTextForm v-model="user_pseudo" :Errors="errors.user_pseudo " inputClassName="form-control " label="Username :"
                           name="user_pseudo"/>
            <InputPasswordForm v-model="user_password" :Errors="errors.connexion" inputClassName="form-control "
                               label="Password :" name="user_password"/>
        </b-modal>

        <b-modal :id="`modal-curMtnOpManagmentTechnical-${_uid}`" title="Technical Check" @hidden="resetModal"
                 @ok="handleOkTechnical">
            <EquipmentCurMtnOpForm :id="curMtnOp_id" :description="curMtnOp_description" :endDate="curMtnOp_endDate" :eq_id="eq_id"
                                   :reportNumber="curMtnOp_reportNumber" :startDate="curMtnOp_startDate"
                                   :state_id="state_id"
                                   :validate="curMtnOp_validate" consultMod
                                   @deleteCurMtnOp="closeModal()"/>
            <h4>Please enter your Username and your password to make the Technical check</h4>
            <InputTextForm v-model="user_pseudo" :Errors="errors.user_pseudo " inputClassName="form-control " label="Username :"
                           name="user_pseudo"/>
            <InputPasswordForm v-model="user_password" :Errors="errors.connexion" inputClassName="form-control "
                               label="Password :" name="user_password"/>
        </b-modal>
    </div>
</template>

<script>
import EquipmentCurMtnOpForm from './EquipmentCurMtnOpForm.vue'
import InputTextForm from '../../../input/InputTextForm.vue'
import InputPasswordForm from '../../../input/InputPasswordForm.vue'

export default {
    components: {
        EquipmentCurMtnOpForm,
        InputTextForm,
        InputPasswordForm
    },
    props: {
        eq_id: {
            type: Number
        },
        state_id: {
            type: Number
        },
        curMtnOp_id: {
            type: Number
        },
        curMtnOp_reportNumber: {
            type: String
        },
        curMtnOp_description: {
            type: String
        },
        curMtnOp_startDate: {
            type: String
        },
        curMtnOp_endDate: {
            type: String
        },
        curMtnOp_validate: {
            type: String
        },
        realizedBy_lastName: {
            type: String
        },
        qualityVerifier_lastName: {
            type: String
        },
        technicalVerifier_lastName: {
            type: String
        },

    },
    methods: {
        closeModal(modal) {
            this.$bvModal.hide(modal);
            this.resetModal();
            this.$emit('okReload', '')
        },
        handleOkUpdate() {
            this.$emit('okReload', '')
        },
        resetModal() {
            this.user_pseudo = '';
            this.user_password = ''
        },
        handleOkRealize(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            var postUrlAdd = (id) => `/curMtnOp/realize/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id), {
                user_pseudo: this.user_pseudo,
                user_password: this.user_password,
                user_id: this.$userId.id,
                reason:'equipment'
            })
                .then(response => {
                    this.closeModal(bvModalEvent.target.id);
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                });
        },

        handleOkQuality(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()

            const postUrlAdd = (id) => `/curMtnOp/qualityVerifier/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id), {
                user_pseudo: this.user_pseudo,
                user_password: this.user_password,
                user_id: this.$userId.id
            })
                .then(response => {
                    this.closeModal(bvModalEvent.target.id);
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                });

        },

        handleOkTechnical(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            const postUrlAdd = (id) => `/curMtnOp/technicalVerifier/${id}`;
            axios.post(postUrlAdd(this.curMtnOp_id), {
                user_pseudo: this.user_pseudo,
                user_password: this.user_password,
                user_id: this.$userId.id
            })
                .then(response => {
                    this.closeModal(bvModalEvent.target.id);
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                });
        }
    },
    data() {
        return {
            errors: {},
            user_pseudo: '',
            user_password: '',
        }
    },

}
</script>

<style lang="scss">
.op_cur_modal_comp {
    .cur_button {
        display: inline-block;
    }
}
</style>

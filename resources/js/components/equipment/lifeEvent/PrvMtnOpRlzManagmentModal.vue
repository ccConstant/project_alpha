<template>
    <div>   
            <b-button  @click="$bvModal.show(`modal-prvMtnOpManagmentUpdate-${_uid}`)" variant="primary">Update</b-button>
            <b-button  @click="$bvModal.show(`modal-prvMtnOpManagmentApprouve-${_uid}`)" variant="primary">Approuve it</b-button>

            <b-modal :id="`modal-prvMtnOpManagmentUpdate-${_uid}`" title="Update the record" @ok="handleOkUpdate">
                <EquipmentPrvMtnOpRlzForm modifMod  :eq_id="eq_id" :state_id="state_id" :id="prvMtnOpRlz_id" :prvMtnOp_id_prop="prvMtnOp_id" :prvMtnOp_number_prop="prvMtnOp_number" 
                :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_protocol_prop="prvMtnOp_protocol"
                :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"  :endDate="prvMtnOpRlz_endDate"
                :validate="prvMtnOpRlz_validate" @deletePrvMtnOpRlz="closeModal()"/>
            </b-modal>

            <b-modal :id="`modal-prvMtnOpManagmentApprouve-${_uid}`" ref="approuve_modal" title="Approuve the record" @ok="handleOkApprouve">
                <EquipmentPrvMtnOpRlzForm consultMod  :eq_id="eq_id" :state_id="state_id" :id="prvMtnOpRlz_id" :prvMtnOp_id_prop="prvMtnOp_id" :prvMtnOp_number_prop="prvMtnOp_number" 
                :prvMtnOp_description_prop="prvMtnOp_description" :prvMtnOp_protocol_prop="prvMtnOp_protocol"
                :reportNumber="prvMtnOpRlz_reportNumber" :startDate="prvMtnOpRlz_startDate"  :endDate="prvMtnOpRlz_endDate"
                :validate="prvMtnOpRlz_validate" @deletePrvMtnOpRlz="closeModal()"/>
                <h4>Please enter your Username and your password to approuve</h4>
                <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control "/>
                <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control "/>
            </b-modal>
    </div>

</template>

<script>
import EquipmentPrvMtnOpRlzForm  from'./EquipmentPrvMtnOpRlzForm.vue'
import InputTextForm from '../../input/InputTextForm.vue'
import InputPasswordForm from '../../input/InputPasswordForm.vue'

export default {
    components:{
        EquipmentPrvMtnOpRlzForm,
        InputTextForm,
        InputPasswordForm
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
        }

    },
    data(){
        return{
            errors:{},
            user_pseudo:'',
            user_password:'',
        }
    },

    methods:{
        checkFormValidity() {
            const valid = this.$refs.approuve_modal.checkValidity()
            this.infoState = valid
            return valid
        },
        closeModal(){
            console.log("coco")
            this.$bvModal.hide(`modal-prvMtnOpManagment-${this._uid}`);
            this.$emit('okReload','')
        },
        handleOkUpdate(){
            this.$emit('okReload','')
        },

        handleOkApprouve(){
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            this.handleSubmitApprouve()
        },
        handleSubmitApprouve() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }

            var postUrlAdd = (id) => `/prvMtnOpRlz/approve/${id}`;
            axios.post(postUrlAdd(this.prvMtnOpRlz_id),{
                    user_pseudo:this.user_pseudo,
                    user_password:this.user_password,
                    user_id:this.$userId.id
                })
                .then(response =>{ 
                    this.closeModal();
                })
                .catch(error =>{this.errors=error.response.data.errors});
        },
    }

}
</script>

<style>

</style>
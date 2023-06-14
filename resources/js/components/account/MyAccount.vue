<!--File name : MyAccount.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the account management part-->

<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <SuccesAlert ref="successAlert"/>
            <div class="myaccouunt">
                <h2>Change my informations</h2>
                <b-form-group class="myaccount_form" @keydown="clearError">
                    <InputTextForm :Errors="errors.user_firstName " v-model="user_firstName" name="user_firstName" label="First name :" inputClassName="form-control" divClassName="user_text_field" :info_text="infos_person[0].info_value"/>
                    <InputTextForm :Errors="errors.user_lastName " v-model="user_lastName" name="user_lastName" label="Last name :" inputClassName="form-control" divClassName="user_text_field" :info_text="infos_person[1].info_value"/>
                    <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control" divClassName="user_text_field" :info_text="infos_person[4].info_value"/>
                    <InputTextForm :Errors="errors.user_signaturePath " v-model="user_signaturePath" name="user_signaturePath" label="Signature Path :" inputClassName="form-control" divClassName="user_text_field" />
                    <div class="input-group">
                        <InputTextForm :inputClassName="['form-control', !this.formation_eq_ok?'is-invalid':'']" :Errors="errors.user_eq_formation_date" name="user_eq_formation_date" label="Trained to general principles of equipment management since:" :isDisabled="true" divClassName="user_text_field" v-model="user_eq_formation_date"/>
                        <InputDateForm  inputClassName="form-control date-selector" name="selected_eq_formation_date"  isRequired v-model="selected_eq_formation_date"/>
                    </div>
                    <div v-if="this.formation_eq_ok==false">
                        <p class="train_alert">The equipment formation is no longer available </p>
                    </div>

                    <div class="input-group">
                        <InputTextForm :inputClassName="['form-control', !this.formation_mme_ok?'is-invalid':'']" :Errors="errors.user_mme_formation_date" name="user_eq_formation_date" label="Trained to general principles of mme management since:" :isDisabled="true" divClassName="user_text_field" v-model="user_mme_formation_date"/>
                        <InputDateForm  inputClassName="form-control date-selector" name="selected_mme_formation_date"  isRequired v-model="selected_mme_formation_date"/>
                    </div>
                    <div v-if="this.formation_mme_ok==false">
                        <p class="train_alert">The mme formation is no longer available </p>
                    </div>
                    <InputPasswordForm :Errors="errors.user_password" v-model="user_password" name="user_password" label="New password :" inputClassName="form-control" divClassName="user_text_field" :info_text="infos_person[5].info_value"/>
                    <InputPasswordForm :Errors="errors.user_confirmation_password" v-model="user_confirmation_password" name="user_confirmation_password" label="Confirm new password :" inputClassName="form-control" divClassName="user_text_field" />
                </b-form-group>
                <button type="button" @click="UpdateInfo()" class="save btn btn-primary save_button ">Save</button>
            </div>
        </div>
    </div>
</template>

<script>
import InputTextForm from '../input/InputTextForm.vue'
import InputDateForm from '../input/InputDateForm.vue'
import InputPasswordForm from '../input/InputPasswordForm.vue'
import SuccesAlert from '../alert/SuccesAlert.vue'

SuccesAlert
import moment from 'moment'

export default {
    components:{
        InputTextForm,
        InputPasswordForm,
        InputDateForm,
        SuccesAlert
    },
    data(){
        return{
            user_pseudo:this.$userId.user_pseudo,
            user_password:'',
            user_lastName:this.$userId.user_lastName,
            user_firstName:this.$userId.user_firstName,
            user_confirmation_password:'',
            user_signaturePath:this.$userId.user_signaturePath,
            errors:[],
            infos_person:[],
            loaded:false,
            user_eq_formation_date:'',
            user_mme_formation_date:'',
            selected_eq_formation_date:this.$userId.user_formationEqDate,
            selected_mme_formation_date:this.$userId.user_formationMmeDate,
            user_id:this.$userId.user_pseudo,
            formation_eq_ok:true,
            formation_mme_ok:true
        }
    },
    methods:{
        clearError(event){
            delete this.errors[event.target.name];
        },
        UpdateInfo(){
            console.log(this.selected_mme_formation_date)
            var postUrlUpdate = (id) => ` /user/update/myAccount/${id}`;
			axios.post(postUrlUpdate(this.$userId.id),{
                user_pseudo:this.user_pseudo,
                user_firstName:this.user_firstName,
                user_signaturePath:this.user_signaturePath,
                user_lastName:this.user_lastName,
                user_password:this.user_password,
                user_confirmation_password:this.user_confirmation_password,
                user_formationEqDate:this.selected_eq_formation_date,
                user_formationMmeDate:this.selected_mme_formation_date,
			})
			.then(response =>{
                this.$refs.successAlert.showAlert('Account information updated successfully');
                window.location.reload();
			})
			.catch(error =>{
				this.errors=error.response.data.errors;
			});
        }
    },
    created(){
        axios.get('/info/send/person')
        .then (response=> {
            this.infos_person=response.data;
            })
        .catch(error => console.log(error)) ;

        var getUrlFormationOk = (id) => ` /user/get/formationEqOk/${id}`;
        axios.get(getUrlFormationOk(this.$userId.id))
        .then (response=> {
            this.formation_eq_ok=response.data;
            })
        .catch(error => console.log(error)) ;

        var getUrlFormationOk = (id) => ` /user/get/formationMmeOk/${id}`;
        axios.get(getUrlFormationOk(this.$userId.id))
        .then (response=> {
            this.formation_mme_ok=response.data;
            this.loaded=true;
            })
        .catch(error => console.log(error)) ;
    },
    updated() {
        if(this.selected_eq_formation_date!==null){
            this.user_eq_formation_date=moment(this.selected_eq_formation_date).format('D MMM YYYY');
        }
        if(this.selected_mme_formation_date!==null){
            this.user_mme_formation_date=moment(this.selected_mme_formation_date).format('D MMM YYYY');
        }
    },

}
</script>

<style lang="scss" scoped>
    .myaccouunt{
        border: solid 1px grey;
        display: block;
        margin: 100px auto;
        width: 1000px;
        display: grid;
        border-radius: 30px;
        h2{
            margin: 10px auto 10px;
        }
        .save_button{
                position: relative;
                width:700px;
                margin: auto auto 20px;
        }
        .myaccount_form{

            .user_text_field{
            margin-left:130px ;
            width: 700px;
            position: relative;
            }
            .password{
                position: relative;
                margin-left:auto;
                margin-right:auto;
                width: 80%;
                margin-left:auto;
                margin-right:auto;
            }
            .input-group{

                .user_text_field{
                    width:660px;
                }

            }
            .train_alert{
                margin-left:130px ;
                color: red;
            }

        }
    }


</style>

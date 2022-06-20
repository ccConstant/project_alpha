<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="container myaccouunt">
                <h2>Change my informations</h2>
                <form class="myaccount_form" @keydown="clearError">
                    <InputTextForm :Errors="errors.user_firstName " v-model="user_firstName" name="user_firstName" label="Change my first name :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[0].info_value"/>
                    <InputTextForm :Errors="errors.user_lastName " v-model="user_lastName" name="user_lastName" label="Change my last name :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[1].info_value"/>
                    <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Change my username :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[4].info_value"/>
                    <InputPasswordForm :Errors="errors.user_password" v-model="user_password" name="user_password" label="Change my password :" inputClassName="form-control " divClassName="password" :info_text="infos_person[5].info_value"/>
                    <InputPasswordForm :Errors="errors.user_confirmation_password" v-model="user_confirmation_password" name="user_confirmation_password" label="Confirm the new password :" inputClassName="form-control " divClassName="password" />
                </form>
                <button type="button" @click="UpdateInfo()" class="save btn btn-primary save_button ">Save</button>

            </div>
        </div>
    </div>
</template>

<script>
import InputTextForm from '../input/InputTextForm.vue'
import InputPasswordForm from '../input/InputPasswordForm.vue'

export default {
    components:{
        InputTextForm,
        InputPasswordForm
    },
    data(){
        return{
            user_pseudo:this.$userId.user_pseudo,
            user_password:'',
            user_lastName:this.$userId.user_lastName,
            user_firstName:this.$userId.user_firstName,
            user_confirmation_password:'',
            errors:[],
            infos_person:[],
            loaded:false
        }
    },
    methods:{
        clearError(event){
            delete this.errors[event.target.name];
        },
        UpdateInfo(){
            var postUrlUpdate = (id) => `/user/update/infos/${id}`;
			axios.post(postUrlUpdate(this.modal_id),{
                user_pseudo:this.user_pseudo,
                user_firstName:this.user_firstName,
                user_lastName:this.user_lastName,
                user_password:this.user_password,
                user_confirmation_password:this.user_confirmation_password
			})
			.then(response =>{           

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
            this.loaded=true;
            }) 
        .catch(error => console.log(error)) ;
    }

}
</script>

<style lang="scss">
    .myaccouunt{
        border: solid 1px grey;
        display: block;
        margin: 100px auto;
        width: 60%;
        display: grid;
        height: 600px;
        border-radius: 30px;
        h2{
            margin: 10px  auto -80px;
        }
        .save_button{
                height: 50px;
                position: relative;
                width:80%;
                margin: -110px  auto 0px;
        }
        .myaccount_form{

            .user_text_field{
            margin: auto;
            width: 80%;
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

        }
    }


</style>
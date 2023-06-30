<!--File name : SignUp.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the creation of an account-->

<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="container register">
                <h2>Create a new account</h2>
                <form class="register_form" @keydown="clearError">
                    <InputTextForm :Errors="errors.user_firstName " v-model="user_firstName" name="user_firstName" label="First name :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[0].info_value"/>
                    <InputTextForm :Errors="errors.user_lastName " v-model="user_lastName" name="user_lastName" label="Last name :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[1].info_value"/>
                    <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[4].info_value"/>
                    <InputPasswordForm :Errors="errors.user_password" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control " divClassName="password" :info_text="infos_person[5].info_value"/>
                    <InputPasswordForm :Errors="errors.user_confirmation_password" v-model="user_confirmation_password" name="user_confirmation_password" label="Confirm password :" inputClassName="form-control " divClassName="password" :info_text="infos_person[6].info_value"/>
                </form>
                <button type="button" @click="create_account()" class="save btn btn-primary register_button ">Register</button>
                  <div> You have already an account? <router-link :to="{name:'url_sign_in',params:{} }">Please Sign In</router-link></div>

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
            user_pseudo:'',
            user_password:'',
            user_lastName:'',
            user_firstName:'',
            user_confirmation_password:'',
            errors:[],
            infos_person:[],
            loaded:false
        }
    },
    methods:{
        create_account(){
            axios.post('register',{
                user_firstName:this.user_firstName,
                user_lastName:this.user_lastName,
                user_pseudo:this.user_pseudo,
                user_password:this.user_password,
                user_confirmation_password:this.user_confirmation_password

            })
            .then(response =>{window.location.href = "/"})
            .catch(error => this.errors=error.response.data.errors) ;
        },
        clearError(event){
            delete this.errors[event.target.name];
        },
    },
    created(){
        axios.get('/info/send/person')
        .then (response=> {
            this.infos_person=response.data;
            this.loaded=true;
            }).catch(error => {
        }) ;
    }

}
</script>

<style lang="scss">
    .register{
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
        .register_button{
                height: 50px;
                position: relative;
                width:80%;
                margin: -110px  auto 0px;
        }
        .register_form{

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
        .save{
            display: block;
            margin: auto;
        }
    }


</style>

<!--File name : SignIn.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the connection menu-->

<template>
  <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="container login">
                <h2>Login</h2>
                <form class="login_form" @keydown="clearError">
                    <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="Username :" inputClassName="form-control " divClassName="user_text_field" :info_text="infos_person[4].info_value"/>
                    <InputPasswordForm :Errors="errors.connexion" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control " divClassName="password" :info_text="infos_person[5].info_value"/>
                </form>
                <button type="button" @click="create_account()" class="save btn btn-primary login_button ">Login</button>
                <div> You don't have any account? <router-link :to="{name:'url_sign_up',params:{} }">Please Sign Up</router-link></div>
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
            errors:[],
            infos_person:[],
            loaded:false
        }
    },
    methods:{
        create_account(){
            console.log(this.user_pseudo);
            console.log(this.user_password);
            axios.post('login',{
                user_pseudo:this.user_pseudo,
                user_password:this.user_password

            })
            .then(response =>{window.location.href = "/"})
            .catch(error =>this.errors=error.response.data.errors);
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
            })
        .catch(error => console.log(error)) ;
    }

}
</script>

<style lang="scss">
    .login{
        border: solid 1px grey;
        display: block;
        margin: 100px auto;
        width: 60%;
        display: grid;
        height: 400px;
        border-radius: 30px;
        h2{
            margin: 10px  auto -80px;
        }
        .login_button{
                height: 50px;
                position: relative;
                width:80%;
                margin: -110px  auto 0px;
        }
        .login_form{

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

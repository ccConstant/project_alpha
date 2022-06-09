<template>
  <div>
      <div class="container register">
          <h2>Create a new account</h2>
          <form class="register_form">
              <InputTextForm :Errors="errors.user_firstName " v-model="user_firstName" name="user_firstName" label="First name :" inputClassName="form-control " divClassName="user_text_field"/>
              <InputTextForm :Errors="errors.user_lastName " v-model="user_lastName" name="user_lastName" label="Last name :" inputClassName="form-control " divClassName="user_text_field"/>
              <InputTextForm :Errors="errors.user_initial " v-model="user_initial" name="user_initial" label="Initial :" inputClassName="form-control " divClassName="user_text_field"/>
              <InputTextForm :Errors="errors.user_pseudo " v-model="user_pseudo" name="user_pseudo" label="User name :" inputClassName="form-control " divClassName="user_text_field"/>
              <InputPasswordForm :Errors="errors.user_password" v-model="user_password" name="user_password" label="Password :" inputClassName="form-control " divClassName="password"/>
          </form>
          <button type="button" @click="create_account()" class="save btn btn-primary register_button ">Register</button>
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
            user_initial:'',
            errors:[]
        }
    },
    methods:{
        create_account(){
            console.log(this.user_firstName);
            console.log(this.user_lastName);
            console.log(this.user_initial);
            console.log(this.user_pseudo);
            console.log(this.user_password);
            axios.post('/login',{
            })
            //If the dimension is added succesfuly
            .then(response =>{console.log("compte cree")})
            .catch(error => this.errors=error.response.data.errors) ;
        }
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
    }


</style>
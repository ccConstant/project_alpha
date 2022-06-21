<template>
    <div class="row row_right">
        <ErrorAlert ref="errorAlert"/>
        <div class="w-100 row_right_tab"></div>
        <b-col class="col_right_tab_title" >{{right_title}}</b-col>
        <b-col class="col_right_tab" v-for="(user) in users" :key="key_letter+user.id">
            <div class="check_formed">
                <input type="checkbox" :id="right_name+user.id" :class="[right_name, 'right_checkbox']" :name="right_name" :value="user.id" @click="send_right_change($event,user.id,right_name)">
                <div v-if="training!=null && user.user_formationEqDate!=null">
                    <div v-if="formationEqOk(user.id)!=true">
                        <p  :class="['formed', 'text-danger']">
                            Since : {{user.user_formationEqDate}} training expired
                        </p>
                    </div>
                    <div v-else>
                        <p class="formed">
                            Since : {{user.user_formationEqDate}}</p>
                    </div>
                </div>
            </div>

                

        </b-col>
    </div>

</template>

<script>
import ErrorAlert from '../alert/ErrorAlert.vue'
export default {
    components:{
        ErrorAlert
    },
    props:{
        right_title:{
            type:String
        },
        users:{
            type:Array
        },
        key_letter:{
            type:String
        },
        right_name:{
            type:String
        },
        training:{
            type:Boolean,
            default:null
        },
    },
    data(){
        return{
            eq_formation_isOk_res:true,
        }
    },
    methods:{
        send_right_change(e,value,name){
            var rightUrl = (right_name,user_id) => `/user/update_right/${right_name}/${user_id}`;
            axios.post(rightUrl(name,value),{
                user_value:e.target.checked,
                //id of user who change the right
                user_id:this.$userId.id
            })
            .then(response =>{console.log(response.data)})
            //If the controller sends errors we put it in the errors object 
            .catch(error => this.errors=error.response.data.errors) ;
        },
        formationEqOk(user_id){
            var getUrlFormationOk = (id) => ` /user/get/formationEqOk/${id}`;
            axios.get(getUrlFormationOk(user_id))
            .then (response=> {
                this.eq_formation_isOk_res=response.data;
            }) 
            .catch(error =>{this.$refs.successAlert.showAlert(error.response.data.errors.user);}) ;
            return false;
        }

    },
   


}
</script>

<style lang="scss">
.row_right{
    .row_right_tab{
        border: solid 1px lightgrey;
    }
    .col_right_tab{
        border-right: solid 1px lightgrey;
        border-left: solid 1px lightgrey;
    }
    .col_right_tab_title{
        border-left: solid 1px lightgrey;
    }
    .check_formed{
        
        text-align: center;
        .right_checkbox{
            margin: auto;
            display: block;
            
        }
        .formed{
            display: block;
            
        }
    }

}

</style>
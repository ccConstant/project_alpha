<template>
    <div class="row row_right">
        <div class="w-100 row_right_tab"></div>
        <b-col class="col_right_tab_title" >{{right_title}}</b-col>
        <b-col class="col_right_tab" v-for="(user) in users" :key="key_letter+user.id">
            <input type="checkbox" :id="right_name+user.id" :class="[right_name, 'right_checkbox']" :name="right_name" :value="user.id" @click="send_right_change($event,user.id,right_name)">
        </b-col>
    </div>

</template>

<script>
export default {
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
        }
    },
    methods:{
        send_right_change(e,value,name){
            console.log(e.target.checked)
            console.log(value)
            console.log(name)
            console.log("/user/update_right/"+name+'/'+value)
            
            var rightUrl = (right_name,user_id) => `/user/update_right/${right_name}/${user_id}`;
            axios.post(rightUrl(name,value),{
                user_value:e.target.checked
            })
            .then(response =>{console.log(response.data)})
            //If the controller sends errors we put it in the errors object 
            .catch(error => this.errors=error.response.data.errors) ;
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
    .right_checkbox{
        display: block;
        margin: auto;
    }
}

</style>
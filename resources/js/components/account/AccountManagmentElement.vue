<template>
    <div class="row">
        <div class="w-100"></div>
        <b-col>{{right_title}}</b-col>
        <b-col v-for="(user) in users" :key="key_letter+user.id">
            <input type="checkbox" :id="right_name+user.id" :class="right_name" :name="right_name" :value="user.id" @click="send_right_change($event,user.id,right_name)">
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
        }

    },

}
</script>

<style>

</style>
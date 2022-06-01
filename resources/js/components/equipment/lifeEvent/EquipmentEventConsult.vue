<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <UpdateState :consultMod="true"/>
            <ReferenceACurMtnOp :importedCurMtnOp="eq_curMtnOp" consultMod/>
        </div>

  </div>
</template>

<script>
import UpdateState from './UpdateState.vue'
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'

export default {
    components:{
        UpdateState,
        ReferenceACurMtnOp

    },
    data(){
        return{
            state_id:this.$route.params.state_id,
            eq_curMtnOp:null,
            loaded:false
        }
    },
    created(){
        var consultUrlCurMtnOp = (id) => `/state/curMtnOp/send/${id}`;
        axios.get(consultUrlCurMtnOp(this.state_id))
            .then (response=>{
                console.log(response.data)
                this.eq_curMtnOp=response.data
                this.loaded=true
            })
            .catch(error => console.log(error)) ;

    }

}
</script>

<style>

</style>
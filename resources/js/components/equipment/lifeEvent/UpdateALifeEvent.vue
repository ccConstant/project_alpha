<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <ReferenceAPrvMtnOpRlz  v-if="eq_prvMtnOpRlz.length>0"  :importedPrvMtnOpRlz="eq_prvMtnOpRlz" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>
            <ReferenceACurMtnOp v-if="eq_curMtnOp.length>0" :importedCurMtnOp="eq_curMtnOp" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>

            
        </div>
    </div>
</template>

<script>
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'

export default {
    components:{
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz
    },
    data(){
        return{
            state_id:this.$route.params.state_id,
            eq_curMtnOp:null,
            eq_prvMtnOpRlz:null,
            loaded:false,
            eq_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id)
        }
    },
    created(){
        var consultUrlPrvMtnOpRlz = (id) => `/state/prvMtnOpRlz/send/${id}`;
        axios.get(consultUrlPrvMtnOpRlz(this.state_id))
            .then (response=>{
                this.eq_prvMtnOpRlz=response.data
            })
            .catch(error => console.log(error)) ;

        var consultUrlCurMtnOp = (id) => `/state/curMtnOp/send/${id}`;
        axios.get(consultUrlCurMtnOp(this.state_id))
            .then (response=>{
                this.eq_curMtnOp=response.data
                this.loaded=true
            })
            .catch(error => console.log(error)) ;


    }


}
</script>

<style>

</style>
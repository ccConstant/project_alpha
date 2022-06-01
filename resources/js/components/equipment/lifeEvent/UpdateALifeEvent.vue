<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <ReferenceACurMtnOp  :importedCurMtnOp="eq_curvMtnOp" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>
            <ReferenceAPrvMtnOpRlz  :importedPrvMtnOpRlz="eq_prvMtnOpRlz" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>
            
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
            eq_curvMtnOp:null,
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
                var consultUrlPrvMtnOp =(id) => `/prvMtnOp/send/${id}`;
                axios.get(consultUrlPrvMtnOp(response.data[0].prvMtnOp_id))
                .then (response=>{
                    console.log(response.data);
                    this.eq_prvMtnOpRlz[0].push(response.data);
                    
                })
                .catch(error => console.log(error)) ;
                
            })
            .catch(error => console.log(error)) ;

        var consultUrlCurMtnOp = (id) => `/state/curMtnOp/send/${id}`;
        axios.get(consultUrlCurMtnOp(this.state_id))
            .then (response=>{
                this.eq_curvMtnOp=response.data
                this.loaded=true
            })
            .catch(error => console.log(error)) ;


    }


}
</script>

<style>

</style>
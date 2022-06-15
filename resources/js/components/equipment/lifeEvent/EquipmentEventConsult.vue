<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <UpdateState :consultMod="true"/>
            <div v-if="eq_prvMtnOpRlz.length>0">
                <div>
                    <li class="list-group-item" v-for="(list,index) in states " :key="index" >
                        Start date : {{list.state_startDate}}
                        End date : {{list.state_endDate}}
                        State : {{list.state_name}}
                        <router-link :to="{name:'url_life_event_all_consult',params:{id: eq_id,state_id:list.id} }">Consult</router-link>
                    </li>
                </div>
            </div>
            <ReferenceAPrvMtnOpRlz v-if="eq_prvMtnOpRlz.length>0" :importedPrvMtnOpRlz="eq_prvMtnOpRlz" consultMod/>
            <ReferenceACurMtnOp v-if="eq_curMtnOp.length>0" :importedCurMtnOp="eq_curMtnOp" consultMod/>

        </div>

  </div>
</template>

<script>
import UpdateState from './UpdateState.vue'
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'

export default {
    components:{
        UpdateState,
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz

    },
    data(){
        return{
            state_id:this.$route.params.state_id,
            eq_curMtnOp:null,
            eq_prvMtnOpRlz:null,
            loaded:false
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
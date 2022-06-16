<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Preventive maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAPrvMtnOpRlz  v-if="eq_prvMtnOpRlz.length>0"  :importedPrvMtnOpRlz="eq_prvMtnOpRlz" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Curative maintenance operation record
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <ReferenceACurMtnOp v-if="eq_curMtnOp.length>0" :importedCurMtnOp="eq_curMtnOp" modifMod :eq_id="this.eq_id" :state_id="this.state_id"/>
                        </div>
                    </div>
                </div>
            </div>
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
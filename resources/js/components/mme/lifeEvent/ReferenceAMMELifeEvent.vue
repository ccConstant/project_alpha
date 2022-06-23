<template>
    <div>
        <ReferenceAMMECurMtnOp v-if="operation_type=='curative'" @addSucces="addSucces()" :mme_id="this.mme_id" :state_id="this.state_id"/>
        <ReferenceAVerifRlz v-if="operation_type=='verif'" @addSucces="addSucces()" :mme_id="this.mme_id" :state_id="this.state_id"/>
        <div v-if="isInConsultMod==true">
            <button type="button" @click="referenceAnother()">Reference another operation</button>
        </div>
    </div>
</template>

<script>
import ReferenceAMMECurMtnOp from './ReferenceAMMECurMtnOp.vue'
import ReferenceAVerifRlz from './ReferenceAVerifRlz.vue'

export default {
    components: {
        ReferenceAMMECurMtnOp,
        ReferenceAVerifRlz,
    },
    data(){
        return{
        //Id de l'mme qui vien detre crée
            mme_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id),
            isInConsultMod:false,
            operation_type:this.$route.query.type
        }
    },
    methods:{
        addSucces(){
            this.isInConsultMod = true;
        },
        referenceAnother(){
            window.location.reload();
        }
    },
    created(){
    }
}
</script>

<style>

</style>
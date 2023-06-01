<!--File name : ReferenceALifeEvent.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component used to reference a life event, it use ReferenceACurMtnOp and ReferenceAPrvMtnOpRlz-->

<template>
    <div>
        <ReferenceACurMtnOp
            v-if="operation_type=='curative'"
            @addSucces="addSucces()"
            :eq_id="this.eq_id"
            :state_id="this.state_id"
            :number="this.number"
            :id="this.id"
        />
        <ReferenceAPrvMtnOpRlz
            v-if="operation_type=='preventive'"
            @addSucces="addSucces()"
            :eq_id="this.eq_id"
            :state_id="this.state_id"
            :number="this.number"
            :id="this.id"
        />
        <div v-if="isInConsultMod==true">
            <button type="button" @click="referenceAnother()">Reference another operation</button>
        </div>
    </div>
</template>

<script>
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'

export default {
    components: {
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz,

    },
    data(){
        return{
            eq_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id),
            selected_reference:'',
            isInConsultMod:false,
            operation_type:this.$route.query.type,
            number: this.$route.query.number,
            id: this.$route.query.id,
        }
    },
    methods:{
        addSucces(){
            this.isInConsultMod = true;
        },
        referenceAnother(){
            window.location.reload();
        }
    }
}
</script>

<style lang="scss">
    .date-selector{
        width: 44px;
        margin-top:8px
    }
</style>

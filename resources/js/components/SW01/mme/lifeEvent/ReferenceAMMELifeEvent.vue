<!--File name : ReferenceAMMELifeEvent.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a life event in the current MME, it use the ReferenceAMMEVerifRlz and ReferenceAMMECurMtnOp to reference each one respectively-->

<template>
    <div>
        <ReferenceAMMECurMtnOp
            v-if="operation_type=='curative'"
            :id="this.id"
            :mme_id="this.mme_id"
            :number="this.number"
            :state_id="this.state_id"
            @addSucces="addSucces()"
        />
        <ReferenceAVerifRlz
            v-if="operation_type=='verif'"
            :id="this.id"
            :mme_id="this.mme_id"
            :number="this.number"
            :state_id="this.state_id"
            @addSucces="addSucces()"
        />
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
    data() {
        return {
            mme_id: parseInt(this.$route.params.id),
            state_id: parseInt(this.$route.params.state_id),
            isInConsultMod: false,
            operation_type: this.$route.query.type,
            number: Number(this.$route.query.number),
            id: Number(this.$route.query.id),
        }
    },
    methods: {
        addSucces() {
            this.isInConsultMod = true;
        },
        referenceAnother() {
            window.location.reload();
        }
    }
}
</script>

<style>

</style>

<template>
    <div>
        <RadioGroupForm label="What do you want to reference ?:" :options="referenceOption" :isDisabled="!!isInConsultMod" v-model="selected_reference"/> 
        <ReferenceACurMtnOp v-if="selected_reference==false" @addSucces="addSucces()" :eq_id="this.eq_id" :state_id="this.state_id"/>
        <ReferenceAPrvMtnOpRlz v-if="selected_reference==true" @addSucces="addSucces()" :eq_id="this.eq_id" :state_id="this.state_id"/>

        <div v-if="isInConsultMod==true">
            <button type="button" @click="referenceAnother()">Reference another operation</button>

        </div>
    </div>
</template>

<script>
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'

export default {
    components: {
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz,
        RadioGroupForm
        
    },
    data(){
        return{
        //Id de l'equipement qui vien detre crée
            eq_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id),
            selected_reference:'',
            referenceOption :[
                {id: 'Preventive Maintenance Operation', value:true},
                {id : 'Curative Maintenance Operation', value:false}
            ],
            isInConsultMod:false
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
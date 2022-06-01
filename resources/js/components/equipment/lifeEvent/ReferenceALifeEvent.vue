<template>
    <div>
        <InputSelectForm selectClassName="form-select w-50" label="What do you want to reference ?:" :options="referenceOption" :isDisabled="!!isInConsultMod" v-model="selected_reference"/>
        <!--<RadioGroupForm label="What do you want to reference ?:" :options="referenceOption" :isDisabled="!!isInConsultMod" v-model="selected_reference"/> -->
        <ReferenceACurMtnOp v-if="selected_reference=='Curative Maintenance Operation'" @addSucces="addSucces()" :eq_id="this.eq_id" :state_id="this.state_id"/>
        <ReferenceAPrvMtnOpRlz v-if="selected_reference=='Preventive Maintenance Operation'" @addSucces="addSucces()" :eq_id="this.eq_id" :state_id="this.state_id"/>

        <div v-if="isInConsultMod==true">
            <button type="button" @click="referenceAnother()">Reference another operation</button>

        </div>
    </div>
</template>

<script>
import ReferenceACurMtnOp from './ReferenceACurMtnOp.vue'
import ReferenceAPrvMtnOpRlz from './ReferenceAPrvMtnOpRlz.vue'
import RadioGroupForm from '../../input/RadioGroupForm.vue'
import InputSelectForm from '../../input/InputSelectForm.vue'

export default {
    components: {
        ReferenceACurMtnOp,
        ReferenceAPrvMtnOpRlz,
        RadioGroupForm,
        InputSelectForm
        
    },
    data(){
        return{
        //Id de l'equipement qui vien detre crée
            eq_id:parseInt(this.$route.params.id),
            state_id:parseInt(this.$route.params.state_id),
            selected_reference:'',
            referenceOption :[
                {value:'Preventive Maintenance Operation'},
                {value:'Curative Maintenance Operation'}
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
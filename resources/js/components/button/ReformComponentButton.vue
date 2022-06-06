<template>
    <div>
        <div v-if="reformDate
        
        
        ==null">
            <b-button class="reformButton" v-if="this.reformMod==true" variant="danger" @click="$bvModal.show(`modal-reform_component-${_uid}`)" >Reform</b-button>
            <b-modal :id="`modal-reform_component-${_uid}`"  @ok="reformConfirmation">
                <p class="my-4">Are you sure you want to reform
                </p>
            </b-modal>
            <div v-if="hasError(this.Errors)" class="error_reformButton">
                <p>{{this.Errors[0]}}</p>
            </div>
        </div>
        <div v-else>
            <p>Refrom by {{reformBy}} at {{reformDate}}</p>
        </div>

    </div>
    
</template>

<script>
export default {
    props:{
        reformMod:{
            type:Boolean
        },
        Errors:{
            type:Array,
            default: () => ([])
        },
        reformDate:{
            type: String
        },
        reformBy:{
            type: String
        }
    },
    methods:{
        reformConfirmation(){
            this.$emit('reformOk',"")
        },
        hasError(errors){
            return(errors.length>0);
        }
    },
    data(){
        return{
            reform_date:moment(this.reformDate).format('D MMM YYYY') 
        }
    }

}
</script>

<style lang="scss">
    .reformButton{
        margin-top: 10px;
    }
    .error_reformButton{
        p{
            color:red
        }
    }
</style>
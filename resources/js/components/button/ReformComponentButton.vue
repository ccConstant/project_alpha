<template>
    <div>
        <div v-if="reformDate==null">
            <div v-if="this.$userId.user_makeReformRight==true">
                <b-button class="reformButton" v-if="this.reformMod==true" variant="danger" @click="$bvModal.show(`modal-reform_component-${_uid}`)" >Reform</b-button>
            </div>
            <div v-else>
                <b-button class="reformButton" v-if="this.reformMod==true" variant="danger" disabled >Reform</b-button>
                <p class="text-danger">
                  You don't have the right to Reform  
                </p>
            </div>

            <div v-if="endDate==''">
                <b-modal :id="`modal-reform_component-${_uid}`"  @ok="reformConfirmation" :ok-disabled="true">
                    <p class="my-4">Are you sure you want to reform</p>
                        <div class="input-group">
                            <InputTextForm inputClassName="form-control" label="Reform date :" :isDisabled="true" v-model="endDate" isRequired :info_text="info" />
                            <InputDateForm inputClassName="form-control date-selector" name="selected_endDate" v-model="selected_endDate"/>
                        </div>
                </b-modal>
            </div>
            <div v-else>
                <b-modal :id="`modal-reform_component-${_uid}`"  @ok="reformConfirmation" >
                    <p class="my-4">Are you sure you want to reform</p>
                        <div class="input-group">
                            <InputTextForm inputClassName="form-control" label="Reform date :" :isDisabled="true" v-model="endDate" isRequired />
                            <InputDateForm inputClassName="form-control date-selector" name="selected_endDate" v-model="selected_endDate"/>
                        </div>
                </b-modal>

            </div>

            <div v-if="hasError(this.Errors)" class="error_reformButton">
                <p>{{this.Errors[0]}}</p>
            </div>
        </div>
        <div v-else>
            <p>Reform by {{reformBy}} at {{reformDate}}</p>
        </div>

    </div>
    
</template>

<script>
import InputTextForm from '../input/InputTextForm.vue'
import InputDateForm from '../input/InputDateForm.vue'

import moment from 'moment'
export default {
    components : {
        InputDateForm,
        InputTextForm
    },
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
        },
        info:{
            type:String
        }
    },
    data(){
        return{
            selected_endDate:'',
            endDate:'',
        }
    },
    updated(){
        if(this.selected_endDate!==null){
            this.endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
    },
    methods:{
        reformConfirmation(){
            this.$emit('reformOk',this.selected_endDate)
        },
        hasError(errors){
            return(errors.length>0);
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
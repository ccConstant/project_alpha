<!--File name : InputNumberForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of an input type number called in the different forms-->

<template>
    <div>
        <b-form-group
            :class="[inputClassName, hasError(this.Errors)?'is-invalid':'']"
            :name="name"
            type="number"
            :disabled="isDisabled"
            :invalid-feedback="invalidFeedBack"
            :state="state">
            <label slot="label" :for="name">
                {{label}}
                <InputInfo :info="returnedText_info" v-if="returnedText_info!=null"/>
            </label>
            <b-form-input
                type="number"
                v-model="data"
                :state="state"
                v-on:input="updateValue(Number(data))">
            </b-form-input>
        </b-form-group>
        <!--If this field has an error this div appear with the error described inside -->
        <div v-if="hasError(this.Errors)" class="invalid-feedback">
            {{this.Errors[0]}}
        </div>
    </div>
</template>

<script>
import InputInfo from '../InputInfo.vue'
export default {
    /*--------Declaration of the others Components:--------*/
    components : {
        InputInfo
    },
    /*--------Declaration of the differents props:--------
        name: Name of this input
        label : Label of this input who will appear on it
        isRequired : If this props is present user must write something in the field
        value : Value of this input
        info_text: Help text who will be managing in the InputInfo Component
        stepOfInput: The interval between legal numbers, for example step for 12,5 is 0,1 the user can't type 12,56
        isDisabled: : If this props is present the field is shaded and user can't write anything inside
        inputClassName: Class or Classes of the Input
        Errors: An array of errors caught when user has wanted to post the form
    ---------------------------------------------------*/
    props : {
        name :{
            type : String,
        },
        label : {
            type : String,
        },
        isRequired:{
            type : Boolean,
            default :false
        },
        value:{
            type : Number,
            default : null
        },
        info_text:{
            type:String,
            default : null
        },
        stepOfInput:{
            type :Number,
            default:1

        },
        isDisabled:{
            type : Boolean,
            default :false
        },
        inputClassName:{
            type:String
        },
        Errors:{
            type:Array,
            default: () => ([])
        }

    },
    /*--------Declaration of the differents returned data:--------
    returnedText_info: Help text who will be sent to the Component here we initialize
        it with the value of info_text got in an other components
    -----------------------------------------------------------*/
    data(){
        return{
            returnedText_info:this.info_text,
            data: this.value
        }
    },
    /*--------Declaration of the differents methods:--------
    updateValue: Emit to the parent component the value of the input
    hasError: True of the errors array has at least 1 error else False
    ---------------------------------------------------*/
    methods: {
      updateValue: function (value) {
        this.$emit('input', value)
      },
      hasError(errors){
          return(errors.length>0);
      }
    },
    computed: {
        InputInfo() {
            return InputInfo
        },
        state() {
            if (this.isRequired) {
                return this.data === null
            }
            return true;
        },
        invalidFeedBack() {
            if (this.isRequired && this.data === null) {
                return 'This field is required';
            }
        }
    }
}
</script>
<style lang="scss">
    input{
        /* Firefox */
        -moz-appearance: textfield;

        /* Chrome */
        &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin:0;
        }

        /* Opéra*/
        &::-o-inner-spin-button {
            -o-appearance: none;
            margin: 0;
        }
    }
</style>

<!--File name : InputNumberForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of an input type number called in the different forms-->

<template>
    <div>
        <div
            class="form-group row"
        >
            <label for="input" class="col-sm-2 col-form-label">
                {{label}}
                <InputInfo :info="returnedText_info" v-if="returnedText_info!=null"/>
            </label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input
                        type="number"
                        class="form-control is-valid"
                        id="input"
                        v-model="data"
                        v-on:input="updateValue(data)"
                    >
                </div>
            </div>
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
                return this.data !== null
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

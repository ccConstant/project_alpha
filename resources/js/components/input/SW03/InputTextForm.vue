<!--File name : InputTextForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of an input type text called in the different forms-->


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
                        type="text"
                        :class="'form-control' + (state() ? '' : ' is-invalid')"
                        id="input"
                        :placeholder="placeholer"
                        v-model="data"
                        v-on:input="updateValue(data)"
                        :disabled="isDisabled"
                    >
                    <div v-if="!this.state()" class="invalid-feedback">
                        {{this.invalidFeedBack()}}
                    </div>
                    <div v-else class="valid-feedback">
                        {{this.invalidFeedBack()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputInfo from '../InputInfo.vue'
import {data} from "autoprefixer";
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
        inputClassName: Class or Classes of the Input
        isDisabled: : If this props is present, the field is shaded and user can't write anything inside
        Errors: An array of errors caught when user has wanted to post the form
    ---------------------------------------------------*/
    props : {
        name :{
            type : String,
            default : "Nom non renseigné"
        },
        label : {
            type : String,
            default : "Nom non renseigné"
        },
        isRequired:{
            type : Boolean,
            default :false
        },
        value:{
            type :String,
            default : ''
        },
        placeholer:{
            type:String
        },
        info_text:{
            type:String,
            default : null
        },
        inputClassName:{
            type:[String,Array]
        },
        divClassName:{
            type:String
        },
        isDisabled:{
            type : Boolean,
            default :false
        },
        Errors:{
            type:Array,
            default: () => ([])
        },
        min:{
            type:Number
        },
        max:{
            type:Number
        }

    },
    /*--------Declaration of the differents returned data:--------
        returnedText_info: Help text who will be sent to the Component here we initialize
        it with the value of info_text got in an other component
    -----------------------------------------------------------*/
    data(){
        return{
            data: this.value,
            returnedText_info:this.info_text,
            errors: this.Errors
        }
    },
    /*--------Declaration of the differents methods:--------
        updateValue: Emit to the parent component the value of the input
        hasError: True of the errors array has at least 1 error else False
    ---------------------------------------------------*/
    methods: {
        updateValue: function (value) {
            this.$emit('input', value);
            this.errors = [];
        },
        hasError(errors){
            return(errors.length>0);
        },
        state() {
            if (this.hasError(this.Errors)) {
                return false;
            }
            if (this.isRequired) {
                if (this.data === null) {
                    return false;
                }
                return this.data.length >= this.min && this.data.length <= this.max;
            }
            if (this.data !== null) {
                return this.data.length <= this.max;
            }
            return true;
        },
        invalidFeedBack() {
            if (this.hasError(this.Errors)) {
                return this.Errors[0];
            }
            if (this.isRequired) {
                if (this.data === null) {
                    return 'This field is required';
                }
                if (this.data.length < this.min && this.data.length !== 0) {
                    return 'You must enter at least ' + this.min + ' characters';
                } else if (this.data.length > this.max) {
                    return 'You must enter a maximum of ' + this.max + ' characters';
                } else {
                    return 'This field is required';
                }
            } else {
                if (this.data !== null && this.data.length > this.max) {
                    return 'You must enter a maximum of ' + this.max + ' characters';
                }
            }
        }
    },
    computed: {
        InputInfo() {
            return InputInfo
        },
    }
}
</script>

<style lang="scss">
</style>

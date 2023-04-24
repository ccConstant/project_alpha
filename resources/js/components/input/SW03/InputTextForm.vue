<!--File name : InputTextForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component of an input type text called in the different forms-->

<template>
    <div :class="divClassName">
        <!--Initializing of the number type input with his props initialized in the parent component-->
        <b-form-group
            :class="[inputClassName, hasError(this.Errors)?'is-invalid':'']"
            :name="name"
            type="text"
            :disabled="isDisabled"
            :invalid-feedback="invalidFeedBack"
            :state="state"
            :placeholder="placeholer"
        >
            <label slot="label" :for="name">
                {{label}}
                <InputInfo :info="returnedText_info" v-if="returnedText_info!=null"/>
            </label>
            <b-form-input
                v-model="data"
                :state="state"
                v-on:input="updateValue(data)"
                trim
            ></b-form-input>
        </b-form-group>
        <!--If this field has an error this div appear with the error described inside -->
        <div v-if="hasError(this.Errors)" class="invalid-feedback">
            {{this.Errors[0]}}
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
            returnedText_info:this.info_text
        }
    },
    /*--------Declaration of the differents methods:--------
        updateValue: Emit to the parent component the value of the input
        hasError: True of the errors array has at least 1 error else False
    ---------------------------------------------------*/
    methods: {
        updateValue: function (value) {
            console.log('value =' + value)
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
            return this.data.length >= this.min && this.data.length <= this.max;
        },
        invalidFeedBack() {
            if (this.isRequired) {
                if (this.data.length < this.min && this.data.length !== 0) {
                    return 'You must enter at least ' + this.min + ' characters';
                } else if (this.data.length > this.max) {
                    return 'You must enter a maximum of ' + this.max + ' characters';
                } else {
                    return 'This field is required';
                }
            } else if (this.data.length > this.max) {
                return 'You must enter a maximum of ' + this.max + ' characters';
            }
        }
    }
}
</script>

<style lang="scss">
</style>

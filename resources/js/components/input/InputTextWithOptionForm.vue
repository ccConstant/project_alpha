<!--File name : InputTextWithOptionForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of an input type text called in the different forms-->

<template>
    <div class="inputAndLabel">
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" :for="name">
                {{label}}
                <InputInfo :info="returnedText_info" v-if="returnedText_info!=null "/>
            </label>
            <!--InputInfo component is called here, we send to him the help test initialized in a parent component if he is not equal to null-->
            <div class="col-sm-10">
                <input :class="[inputClassName, hasError(this.Errors)?'is-invalid':'']" list="browsers"
                             :name="name" :required="!!isRequired" :disabled="!!isDisabled"
                             :value="value" v-on:input="updateValue($event.target.value)">
                <!--Options of the input, the for loop here is used to initialize them with an array of the differents value-->
                <datalist id="browsers">
                    <option v-for="(option,index) in options " :key="index"  :value= option[inputName]> </option>
                </datalist>
                <!--If this field has an error this div appear with the error described inside -->
                <div v-if="hasError(this.Errors)" class="invalid-feedback">
                    {{this.Errors[0]}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import InputInfo from './InputInfo.vue'
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
        options: Array of option values
        inputClassName: Class or Classes of the Input
        isDisabled: : If this props is present the field is shaded and user can't write anything inside
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
            type :String
        },
        info_text:{
            type:String,
            default : null
        },
        options:{
            type : Array
        },
        inputClassName:{
            type:String
        },
        isDisabled:{
            type : Boolean,
            default :false
        },
        Errors:{
            type:Array,
            default: () => ([])
        }
    },
    /*--------Declaration of the differents returned data:--------
        returnedText_info: Help text who will be sent to the Component here we initialize
        it with the value of info_text got in an other component
    ---------------------------------------------------*/
    data(){
        return{
            returnedText_info:this.info_text,
            inputName:this.name
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
        },
    }

}
</script>

<style>

</style>

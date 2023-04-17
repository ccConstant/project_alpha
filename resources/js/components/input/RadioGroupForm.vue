<!--File name : RadioGroupForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 09 May 2022-->
<!--Vue Component of a radio group called in the different forms-->

<template>
    <div>
        <!--Label of the radio group-->
        <label class="form-label" for="radio-button-group">
            {{label}}
        </label>
        <InputInfo :info="returnedText_info" v-if="returnedText_info!=null "/>
        <div class="radio-button-group" :class="[hasError(this.Errors)?'is-invalid':'']">
            <!--Label of options, the for loop here is used to initialize them with an array of the differents value-->
            <label v-for="(option,index) in options " :key="index">
                <!--Initializing of the radio type input with his props initialized in the parent component-->
                <input type="radio" :label="label" name="radio-input" :value="option.value" :id="option.id"
                 :required="!!isRequired" :disabled="!!isDisabled" @change="emitAndClear(option.value)" />
                {{ option.text }}
            </label>
        </div>
        <div v-if="hasError(this.Errors)" class="invalid-feedback">
            {{this.Errors[0]}}
        </div>
    </div>
</template>

<script>
import InputInfo from './InputInfo.vue'
export default {
    components : {
        InputInfo
    },
       /*--------Declaration of the differents props:--------
        options: Array of option values
        label : Label of this select who will appear on it
        isRequired : If this props is present, user must select an option
        checkedOption: Value of the checked option initialized with the database or by the user
        isDisabled: : If this props is present the field is shaded and user can't write nothing inside
    ---------------------------------------------------*/
    props: {
        options: {
            required: true,
            type: Array
        },
        label: {
            type:String
        },
        isRequired:{
            type : Boolean,
            default :false
        },
        checkedOption:{
            type:Boolean
        },
        isDisabled:{
            type : Boolean,
            default :false
        },
        Errors:{
            type:Array,
            default: () => ([])
        },
        info_text:{
            type:String,
            default:null
        }
    },
    data(){
        return{
            returnedText_info:this.info_text,
        }
    },
    mounted(){
        //Here we put the checked attribute in the option corresponding with the prop checkedOption
        const radio = document.getElementsByName('radio-input');
        for (var i = 0; i < radio.length; i++) {
            if (`${radio[i].id}`==`${this.options[0].id}`&& `${radio[i].value}` == `${this.checkedOption}`) {
                radio[i].setAttribute("checked", "checked");
            }
        }
    },
    methods: {
        hasError(errors){
            return(errors.length>0);
        },
        emitAndClear(value){
            this.$emit('input',value);
            this.$emit('clearRadioError','');
        }
    }

}
</script>

<style>
    label{
        padding-right: 5px;
    }

</style>

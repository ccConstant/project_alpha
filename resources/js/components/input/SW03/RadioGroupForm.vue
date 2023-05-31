 <!--File name : RadioGroupForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 30 May 2023-->
<!--Vue Component of a radio group called in the different forms-->

<template>
    <div>
        <b-form-radio-group
            :state="state"
            :disabled="isDisabled"
            :required="isRequired"
            :invalid-feedback="invalidFeedBack"
            v-model="data"
        >
            <label>
                {{ label }}
                <InputInfo :info="returnedText_info" v-if="returnedText_info!=null"/>
            </label>
            <b-form-radio
                v-for="(option, key) in options"
                :key="key"
                v-model="data"
                :value="option.value"
                :disabled="isDisabled"
            >
            {{ option.text }}
            </b-form-radio>
        </b-form-radio-group>
    </div>
</template>

<script>
import InputInfo from '../InputInfo.vue'
import {data} from "autoprefixer";
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
            type:Boolean,
            default:null
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
        },
        value: {
            type: Boolean,
            default: null
        }
    },
    data(){
        return{
            returnedText_info:this.info_text,
            data: this.checkedOption,
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
    },
    computed: {
        InputInfo() {
            return InputInfo
        },
        state() {
            this.$emit('input',this.data);
            return this.data === null;
        },
        invalidFeedBack() {
            if (this.isRequired) {
                return 'This field is required';
            }
        }
    },
    created() {
    }

}
</script>

<style>
    label{
        padding-right: 5px;
    }

</style>

<!--File name : InputTextForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 09 May 2022-->
<!--Vue Component of an input type text called in the different forms-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
    InputInfo:
        info
-------------------------------------------------------------->
<template>
    <div :class="divClassName">
        <!--Label of the input-->
        <label class="form-label" :for="name">
            {{label}}
        </label>
        <!--Inputinfo component is called here, we send to him the help test initialized in a parent component if he is not equal to null-->
        <InputInfo :info="returnedText_info" v-if="returnedText_info!=null "/>
        <!--Initializing of the number type input with his props initialized in the parent compenant-->
        <input :class="[inputClassName, hasError(this.Errors)?'is-invalid':'']"  
         :name="name" type="text" :required="!!isRequired" :disabled="!!isDisabled" 
          :value="value" :placeholder="placeholer" v-on:input="updateValue($event.target.value)" >
        <!--If this field has an error this div appear with the error described inside -->   
        <div v-if="hasError(this.Errors)" class="invalid-feedback">
            {{this.Errors[0]}}
        </div>
    </div>
  
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputInfo from './InputInfo.vue'
export default {
    /*--------Declartion of the others Components:--------*/
    components : {
        InputInfo
    },
    /*--------Declartion of the differents props:--------
        name: Name of this input
        label : Label of this input who will appear on it
        isRequired : If this props is present user must write something in the field 
        value : Value of this input  
        info_text: Help text who will be managing in the InputInfo Component
        inputClassName: Class or Classes of the Input
        isDisabled: : If this props is present the field is shaded and user can't write nothing inside
        Errors: An array of errors catched when user has wanted to post the form
    ---------------------------------------------------*/
    props : {
        name :{
            type : String,
            default : "nom non renseigné"
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
        },
        placeholer:{
            type:String
        },
        info_text:{
            type:String,
            default : null
        },
        inputClassName:{
            type:String
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
        }

    },
    /*--------Declartion of the differents returned data:--------
        returnedText_info: Help text who will be send to the InputInfo Component here we initialize
        it with the value of info_text getted in an other componoents
    -----------------------------------------------------------*/
    data(){
        return{
            returnedText_info:this.info_text
        }
    },
    /*--------Declartion of the differents methods:--------
        updateValue: Emit to the parent component the value of the input
        hasError : True of the errors array has at least 1 error else False
    ---------------------------------------------------*/
    methods: {
        updateValue: function (value) {
            this.$emit('input', value)
        },
        hasError(errors){
            return(errors.length>0);
        }
    }
}
</script>

<style lang="scss">
 

</style>

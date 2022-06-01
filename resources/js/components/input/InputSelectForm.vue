<!--File name : InputSelectForm.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 09 May 2022-->
<!--Vue Component of a scrolling menu (select with option) called in the different forms-->

<!----------Props of other component who can be called:--------
    See details in related Vue Component
    InputInfo:
        info
-------------------------------------------------------------->

<template>
    <div>
        <!--Label of the input-->
        <label class="form-label" :for="name">
            {{label}}
        </label>
        <!--Inputinfo component is called here, we send to him the help test initialized in a parent component if he is not equal to null-->
        <InputInfo :info="returnedText_info" v-if="returnedText_info!=null "/>
        <!--Initializing of the select with his props initialized in the parent compenant-->     
        <select @change="clearError" :class="[selectClassName, hasError(this.Errors)?'is-invalid':'']" :name="name" :disabled="!!isDisabled"
         :required="!!isRequired" v-on:input="updateValue($event.target.value)">
            <option value="" selected>---Select---</option>
            <!--Options of the select, the for loop here is used to initialize them with an array of the differents value-->
            <option v-for="(type,index) in options " :key="index"  :value="type.value">
                {{type.value}}
            </option>
        </select>
        <div v-if="hasError(this.Errors)" class="invalid-feedback">
            {{this.Errors[0]}}
        </div>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import InputInfo from './InputInfo.vue'

export default {
    /*--------Declartion of the child Components:--------*/
    components : {
        InputInfo
    },
    /*--------Declartion of the differents props:--------
        name: Name of this select
        label : Label of this select who will appear on it
        isRequired : If this props is present user must select an option
        value : value of the selected option
        options: Array of option values
        info_text: Help text who will be managing in the InputInfo Component
        selectClassName: Class or Classes of the select
        selectedOption: Value of the selected option initialized with the data base or by the user 
        isDisabled: : If this props is present the field is shaded and user can't write nothing inside 
        selectedDivName: class name of the div who contains the scrolling menu
    ---------------------------------------------------*/
    props:{
        name :{
            type : String,
            default : "nom non renseigné"
        },
        label:{
            type :String,
            default:"nom non renseigné"
        },
        isRequired:{
            type : Boolean,
            default :false
        },
        value:{
            type :String
        },
        options:{
            type:Array
        },
        info_text:{
            type:String,
            default : null
        },
        selectClassName:{
            type:String,
            default:"form-select"
        },
        selctedOption:{
            type:String
        },
        isDisabled:{
            type : Boolean,
            default :false
        },
        selectedDivName:{
            type:String,
            default:undefined
        },
        Errors:{
            type:Array,
            default: () => ([])
        }
        
    },
    /*--------Declartion of the differents returned data:--------
    returnedText_info: Help text who will be send to the InputInfo Component here we initialize
        it with the value of info_text getted in an other componoents
    ---------------------------------------------------*/
    data(){
        return{
            returnedText_info:this.info_text,
        }
    },
    /*All function inside the updated option is called each time the value is changed*/
    updated(){
        //Here we put the selected attribute in the option corresponding with the prop selctedOption
        if(this.selectedDivName!=undefined){
            var selectedDiv = document.getElementsByClassName(this.selectedDivName)
            var option =selectedDiv[0].getElementsByTagName('option');
            for (var i = 0; i < option.length; i++) {
                if(option[i].value == this.selctedOption) {
                    option[i].setAttribute("selected", "selected");
                }
            }
        }else{
            var option = document.getElementsByTagName('option');
            for (var i = 0; i < option.length; i++) {
                if(option[i].value == this.selctedOption) {
                    option[i].setAttribute("selected", "selected");
                }
            }
        }
        
    },
    mounted(){
        var option = document.getElementsByTagName('option');
        for (var i = 0; i < option.length; i++) {
            if(option[i].value == this.selctedOption) {
                option[i].setAttribute("selected", "selected");
            }
        }
    },
    /*--------Declartion of the differents methods:--------
    updateValue: Emit to the parent component the value of the input
    ---------------------------------------------------*/
    methods: {
        updateValue: function (value) {
            this.$emit('input', value)
        },
        hasError(errors){
            return(errors.length>0);
        },
        clearError(){
            this.$emit('clearSelectError',this.name)
        },
        
         
    }
}
</script>

<style lang="scss">


</style>

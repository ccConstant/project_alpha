<!--File name : ValidationButton.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the validation button-->

<template>
    <div>
        <div v-if="validationMethod=='technical'">
            <button @click="Validate" type="button" class="btn btn-primary technical_validate_button">Technical Validate </button>
        </div>
        <div v-else-if="validationMethod=='quality'">
            <button @click="Validate" type="button" class="btn btn-primary quality_validate_button" >Quality Validate</button>
        </div>
        <div v-if="hasError(Errors)===true" class="error_eq_lifesheet_validation">
            <div v-for="(error, index) in this.Errors" :key="index">
                <p>{{error.validation}}</p>
            </div>
        </div>
    </div>


</template>

<script>
export default {
    props:{
        validationMethod:{
            type:String
        },
        Errors:{
            type:Array,
        },
        eq_id:{
            type:String
        }
    },
    methods:{
        hasError(errors){
            console.log((errors.length>0))
            return(errors.length>0);

        },
        Validate(){
            this.$emit("ValidatePressed",'')
        }
    }

}
</script>

<style lang="scss">
    .error_eq_lifesheet_validation{
        p{
            color:red;
            text-align: center;
        }
    }
</style>

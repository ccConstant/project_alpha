<!--File name : EnumManagement.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component to show the different categories of enumerates-->

<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div class="enumManagment" v-if="loaded==true">
            <h1>ENUM MANAGEMENT</h1>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Equipment Type
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_eq_type' :enumList="enum_eq_type" title="Equipment Type" url="/equipment/enum/type/" info_text=" info of enum type"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Equipment Mass Unit
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_eq_massUnit' :enumList="enum_eq_massUnit" title="Equipment Mass Unit" url="/equipment/enum/massUnit/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Equipment Dimension Type
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_dim_type' :enumList="enum_dim_type" title="Equipment Dimension Type" url="/dimension/enum/type/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Equipment Dimension Name
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_dim_name' :enumList="enum_dim_name" title="Equipment Dimension Name" url="/dimension/enum/name/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Equipment Dimension Unit
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_dim_unit'  :enumList="enum_dim_unit" title="Equipment Dimension Unit" url="/dimension/enum/unit/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Equipment Power Type
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_pow_type'  :enumList="enum_pow_type" title="Equipment Power Type" url="/power/enum/type/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        Equipment Risk For
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_riskfor' :enumList="enum_risk_for" title="Equipment Risk For" url="/risk/enum/riskfor/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        MME Required Skill
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_requiredSkill' :enumList="enum_requiredSkill" title="MME Required Skill" url="/verification/enum/requiredSkill/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            MME Verification acceptance authority
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_verifAcceptanceAuthority' :enumList="enum_verifAcceptanceAuthority" title="MME Verification acceptance authority" url="/verification/enum/verifAcceptanceAuthority/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                            MME metrological level
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_metrologicalLevel' :enumList="enum_metrologicalLevel" title="MME metrological level" url="/usage/enum/metrologicalLevel/" />
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEleven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                            MME precaution type
                        </button>
                    </h2>
                    <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven">
                        <div class="accordion-body">
                            <EnumElement error_name='enum_prctn_type' :enumList="enum_prctn_type" title="MME precaution type" url="/precaution/enum/type/" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EnumElement from './EnumElement.vue'
export default {
    components:{
        EnumElement
    },
    data(){
        return{
            enum_eq_type:[],
            enum_eq_massUnit:[],
            enum_dim_type : [],
            enum_dim_name : [],
            enum_dim_unit : [],
            enum_pow_type : [],
            enum_risk_for : [],
            enum_requiredSkill:[],
            enum_verifAcceptanceAuthority:[],
            enum_metrologicalLevel:[],
            loaded:false

        }
    },
    created(){
        if( this.$userId.user_addEnumRight!=true
        &&  this.$userId.user_deleteEnumRight!=true
        &&  this.$userId.user_updateEnumRight!=true ){
                this.$router.replace({ name: "home" })
        }
        /*Ask for the controller the different equipment type option */
        axios.get('/equipment/enum/type')
            .then (response=> this.enum_eq_type=response.data)
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different equipment mass unit option */
        axios.get('/equipment/enum/massUnit')
            .then (response=> this.enum_eq_massUnit=response.data)
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different types of the dimension  */
        axios.get('/dimension/enum/type')
            .then (response=> this.enum_dim_type=response.data)
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different names of the dimension  */
        axios.get('/dimension/enum/name')
            .then (response=> this.enum_dim_name=response.data)
            .catch(error => console.log(error)) ;
         /*Ask for the controller the different unites of the dimension  */
        axios.get('/dimension/enum/unit')
            .then (response=> this.enum_dim_unit=response.data)
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different types of the power  */
        axios.get('/power/enum/type')
            .then (response=> this.enum_pow_type=response.data)
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different risk  */
        axios.get('/risk/enum/riskfor')
            .then (response=>{
                this.enum_risk_for=response.data
            })
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different required skills for verification  */
        axios.get('/verification/enum/requiredSkill')
            .then (response=>{
                console.log("required skill")
                console.log(response.data)
                this.enum_requiredSkill=response.data
            })
            .catch(error => console.log(error)) ;
        axios.get('/verification/enum/verifAcceptanceAuthority')
            .then (response=>{
                console.log(response.data)
                this.enum_verifAcceptanceAuthority=response.data;
            } )
            .catch(error => console.log(error)) ;
        axios.get('/usage/enum/metrologicalLevel')
            .then (response=>{
                this.enum_metrologicalLevel=response.data;
            } )
            .catch(error => console.log(error)) ;
        /*Ask for the controller the different types of precautions  */
        axios.get('/precaution/enum/type')
            .then (response=> {
                this.enum_prctn_type=response.data;
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>

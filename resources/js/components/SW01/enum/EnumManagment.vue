<!--File name : EnumManagement.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component to show the different categories of enumerates-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="enumManagment">
            <h1>ENUM MANAGEMENT</h1>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 id="headingOne" class="accordion-header">
                        <button aria-controls="collapseOne" aria-expanded="true" class="accordion-button"
                                data-bs-target="#collapseOne" data-bs-toggle="collapse" type="button">
                            Equipment Type
                        </button>
                    </h2>
                    <div id="collapseOne" aria-labelledby="headingOne" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_eq_type" error_name='enum_eq_type'
                                         info_text=" info of enum type"
                                         title="Equipment Type" url="/equipment/enum/type/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTwo" class="accordion-header">
                        <button aria-controls="collapseTwo" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTwo" data-bs-toggle="collapse" type="button">
                            Equipment Mass Unit
                        </button>
                    </h2>
                    <div id="collapseTwo" aria-labelledby="headingTwo" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_eq_massUnit" error_name='enum_eq_massUnit'
                                         title="Equipment Mass Unit" url="/equipment/enum/massUnit/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingThree" class="accordion-header">
                        <button aria-controls="collapseThree" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseThree" data-bs-toggle="collapse" type="button">
                            Equipment Dimension Type
                        </button>
                    </h2>
                    <div id="collapseThree" aria-labelledby="headingThree" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_dim_type" error_name='enum_dim_type'
                                         title="Equipment Dimension Type" url="/dimension/enum/type/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFour" class="accordion-header">
                        <button aria-controls="collapseFour" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFour" data-bs-toggle="collapse" type="button">
                            Equipment Dimension Name
                        </button>
                    </h2>
                    <div id="collapseFour" aria-labelledby="headingFour" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_dim_name" error_name='enum_dim_name'
                                         title="Equipment Dimension Name" url="/dimension/enum/name/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingFive" class="accordion-header">
                        <button aria-controls="collapseFive" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseFive" data-bs-toggle="collapse" type="button">
                            Equipment Dimension Unit
                        </button>
                    </h2>
                    <div id="collapseFive" aria-labelledby="headingFive" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_dim_unit" error_name='enum_dim_unit'
                                         title="Equipment Dimension Unit" url="/dimension/enum/unit/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSix" class="accordion-header">
                        <button aria-controls="collapseSix" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSix" data-bs-toggle="collapse" type="button">
                            Equipment Power Type
                        </button>
                    </h2>
                    <div id="collapseSix" aria-labelledby="headingSix" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_pow_type" error_name='enum_pow_type'
                                         title="Equipment Power Type" url="/power/enum/type/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingSeven" class="accordion-header">
                        <button aria-controls="collapseSeven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseSeven" data-bs-toggle="collapse" type="button">
                            Equipment Risk For
                        </button>
                    </h2>
                    <div id="collapseSeven" aria-labelledby="headingSeven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_risk_for" error_name='enum_riskfor' title="Equipment Risk For"
                                         url="/risk/enum/riskfor/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingEight" class="accordion-header">
                        <button aria-controls="collapseEight" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEight" data-bs-toggle="collapse" type="button">
                            MME Required Skill
                        </button>
                    </h2>
                    <div id="collapseEight" aria-labelledby="headingEight" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_requiredSkill" error_name='enum_requiredSkill'
                                         title="MME Required Skill" url="/verification/enum/requiredSkill/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingNine" class="accordion-header">
                        <button aria-controls="collapseNine" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseNine" data-bs-toggle="collapse" type="button">
                            MME Verification acceptance authority
                        </button>
                    </h2>
                    <div id="collapseNine" aria-labelledby="headingNine" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_verifAcceptanceAuthority"
                                         error_name='enum_verifAcceptanceAuthority'
                                         title="MME Verification acceptance authority"
                                         url="/verification/enum/verifAcceptanceAuthority/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingTen" class="accordion-header">
                        <button aria-controls="collapseTen" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseTen" data-bs-toggle="collapse" type="button">
                            MME metrological level
                        </button>
                    </h2>
                    <div id="collapseTen" aria-labelledby="headingTen" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_metrologicalLevel" error_name='enum_metrologicalLevel'
                                         title="MME metrological level" url="/usage/enum/metrologicalLevel/"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 id="headingEleven" class="accordion-header">
                        <button aria-controls="collapseEleven" aria-expanded="false" class="accordion-button collapsed"
                                data-bs-target="#collapseEleven" data-bs-toggle="collapse" type="button">
                            MME precaution type
                        </button>
                    </h2>
                    <div id="collapseEleven" aria-labelledby="headingEleven" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <EnumElement :enumList="enum_prctn_type" error_name='enum_prctn_type'
                                         title="MME precaution type" url="/precaution/enum/type/"/>
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
    components: {
        EnumElement
    },
    data() {
        return {
            enum_eq_type: [],
            enum_eq_massUnit: [],
            enum_dim_type: [],
            enum_dim_name: [],
            enum_dim_unit: [],
            enum_pow_type: [],
            enum_risk_for: [],
            enum_requiredSkill: [],
            enum_verifAcceptanceAuthority: [],
            enum_metrologicalLevel: [],
            loaded: false

        }
    },
    created() {
        if (this.$userId.user_addEnumRight != true
            && this.$userId.user_deleteEnumRight != true
            && this.$userId.user_updateEnumRight != true) {
            this.$router.replace({name: "home"})
        }
        /*Ask for the controller the different equipment type option */
        axios.get('/equipment/enum/type')
            .then(response => {
                this.enum_eq_type = response.data;
                /*Ask for the controller the different equipment mass unit option */
                axios.get('/equipment/enum/massUnit')
                    .then(response => {
                        this.enum_eq_massUnit = response.data;
                        /*Ask for the controller the different types of the dimension  */
                        axios.get('/dimension/enum/type')
                            .then(response => {
                                this.enum_dim_type = response.data;
                                /*Ask for the controller the different names of the dimension  */
                                axios.get('/dimension/enum/name')
                                    .then(response => {
                                        this.enum_dim_name = response.data;
                                        /*Ask for the controller the different unites of the dimension  */
                                        axios.get('/dimension/enum/unit')
                                            .then(response => {
                                                this.enum_dim_unit = response.data;
                                                /*Ask for the controller the different types of the power  */
                                                axios.get('/power/enum/type')
                                                    .then(response => {
                                                        this.enum_pow_type = response.data;
                                                        /*Ask for the controller the different risk  */
                                                        axios.get('/risk/enum/riskfor')
                                                            .then(response => {
                                                                this.enum_risk_for = response.data;
                                                                /*Ask for the controller the different required skills for verification  */
                                                                axios.get('/verification/enum/requiredSkill')
                                                                    .then(response => {
                                                                        this.enum_requiredSkill = response.data;
                                                                        axios.get('/verification/enum/verifAcceptanceAuthority')
                                                                            .then(response => {
                                                                                this.enum_verifAcceptanceAuthority = response.data;
                                                                                axios.get('/usage/enum/metrologicalLevel')
                                                                                    .then(response => {
                                                                                        this.enum_metrologicalLevel = response.data;
                                                                                        /*Ask for the controller the different types of precautions  */
                                                                                        axios.get('/precaution/enum/type')
                                                                                            .then(response => {
                                                                                                this.enum_prctn_type = response.data;
                                                                                                this.loaded = true;
                                                                                            }).catch(error => {
                                                                                        });
                                                                                    }).catch(error => {
                                                                                });
                                                                            }).catch(error => {
                                                                        });
                                                                    }).catch(error => {
                                                                });
                                                            }).catch(error => {
                                                        });
                                                    }).catch(error => {
                                                });
                                            }).catch(error => {
                                        });
                                    }).catch(error => {
                                });
                            }).catch(error => {
                        });
                    }).catch(error => {
                });
            }).catch(error => {
        });
    }
}
</script>

<style>

</style>

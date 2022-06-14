<template>
    <div class="infoManagment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Equipment ID card infos
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <InfoElement title="internal Reference" :info_content="infos_idCard[0].info_value" :info_id="infos_idCard[0].id"/>
                            <InfoElement title="external Reference" :info_content="infos_idCard[1].info_value" :info_id="infos_idCard[1].id"/>
                            <InfoElement title="name" :info_content="infos_idCard[2].info_value" :info_id="infos_idCard[2].id"/>
                            <InfoElement title="type" :info_content="infos_idCard[3].info_value" :info_id="infos_idCard[3].id"/>
                            <InfoElement title="serial number" :info_content="infos_idCard[4].info_value" :info_id="infos_idCard[4].id"/>
                            <InfoElement title="constructor" :info_content="infos_idCard[5].info_value" :info_id="infos_idCard[5].id"/>
                            <InfoElement title="mass" :info_content="infos_idCard[6].info_value" :info_id="infos_idCard[6].id"/>
                            <InfoElement title="mass unit" :info_content="infos_idCard[7].info_value" :info_id="infos_idCard[7].id"/>
                            <InfoElement title="mobility" :info_content="infos_idCard[8].info_value" :info_id="infos_idCard[8].id"/>
                            <InfoElement title="remarks" :info_content="infos_idCard[9].info_value" :info_id="infos_idCard[9].id"/>
                            <InfoElement title="set" :info_content="infos_idCard[10].info_value" :info_id="infos_idCard[10].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Equipment dimension infos
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body">
                            <InfoElement title="type" :info_content="infos_dimension[0].info_value" :info_id="infos_dimension[0].id"/>
                            <InfoElement title="name" :info_content="infos_dimension[1].info_value" :info_id="infos_dimension[1].id"/>
                            <InfoElement title="unit" :info_content="infos_dimension[2].info_value" :info_id="infos_dimension[2].id"/>
                            <InfoElement title="value" :info_content="infos_dimension[3].info_value" :info_id="infos_dimension[3].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Equipment Power infos
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                        <div class="accordion-body">
                            <InfoElement title="type" :info_content="infos_power[0].info_value" :info_id="infos_power[0].id"/>
                            <InfoElement title="name" :info_content="infos_power[1].info_value" :info_id="infos_power[1].id"/>
                            <InfoElement title="value" :info_content="infos_power[2].info_value" :info_id="infos_power[2].id"/>
                            <InfoElement title="unit" :info_content="infos_power[3].info_value" :info_id="infos_power[3].id"/>
                            <InfoElement title="consumptionValue" :info_content="infos_power[4].info_value" :info_id="infos_power[4].id"/>
                            <InfoElement title="consumptionUnit" :info_content="infos_power[5].info_value" :info_id="infos_power[5].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Equipment Special process infos
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                        <div class="accordion-body">
                            <InfoElement title="exist" :info_content="infos_spProc[0].info_value" :info_id="infos_spProc[0].id"/>
                            <InfoElement title="remarks" :info_content="infos_spProc[1].info_value" :info_id="infos_spProc[1].id"/>
                            <InfoElement title="name" :info_content="infos_spProc[2].info_value" :info_id="infos_spProc[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Equipment Usage infos
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                        <div class="accordion-body">
                            <InfoElement title="type" :info_content="infos_usage[0].info_value" :info_id="infos_usage[0].id"/>
                            <InfoElement title="precaution" :info_content="infos_usage[1].info_value" :info_id="infos_usage[1].id"/>
                            <InfoElement title="reform date" :info_content="infos_usage[2].info_value" :info_id="infos_usage[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Equipment File infos
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                        <div class="accordion-body">
                            <InfoElement title="name" :info_content="infos_file[0].info_value" :info_id="infos_file[0].id"/>
                            <InfoElement title="location" :info_content="infos_file[1].info_value" :info_id="infos_file[1].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        Equipment preventive maintenance operation infos
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                        <div class="accordion-body">
                            <InfoElement title="description" :info_content="infos_prvMtnOp[0].info_value" :info_id="infos_prvMtnOp[0].id"/>
                            <InfoElement title="periodicty" :info_content="infos_prvMtnOp[1].info_value" :info_id="infos_prvMtnOp[1].id"/>
                            <InfoElement title="symbolPeriodicity" :info_content="infos_prvMtnOp[2].info_value" :info_id="infos_prvMtnOp[2].id"/>
                            <InfoElement title="number" :info_content="infos_prvMtnOp[3].info_value" :info_id="infos_prvMtnOp[3].id"/>
                            <InfoElement title="protocol" :info_content="infos_prvMtnOp[4].info_value" :info_id="infos_prvMtnOp[4].id"/>
                            <InfoElement title="reformDate" :info_content="infos_prvMtnOp[5].info_value" :info_id="infos_prvMtnOp[5].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        Equipment risk infos
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                        <div class="accordion-body">
                            <InfoElement title="origin" :info_content="infos_risk[0].info_value" :info_id="infos_risk[0].id"/>
                            <InfoElement title="remarks" :info_content="infos_risk[1].info_value" :info_id="infos_risk[1].id"/>
                            <InfoElement title="way of control" :info_content="infos_risk[2].info_value" :info_id="infos_risk[2].id"/>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                        Equipment state infos
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                        <div class="accordion-body">
                            <InfoElement title="name" :info_content="infos_state[0].info_value" :info_id="infos_state[0].id"/>
                            <InfoElement title="remarks" :info_content="infos_state[1].info_value" :info_id="infos_state[1].id"/>
                            <InfoElement title="start date" :info_content="infos_state[2].info_value" :info_id="infos_state[2].id"/>
                            <InfoElement title="is ok" :info_content="infos_state[3].info_value" :info_id="infos_state[3].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                        Equipment Preventive maintenance operation realized infos
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                        <div class="accordion-body">
                            <InfoElement title="report number" :info_content="infos_prvMtnOpRlz[0].info_value" :info_id="infos_prvMtnOpRlz[0].id"/>
                            <InfoElement title="start date" :info_content="infos_prvMtnOpRlz[1].info_value" :info_id="infos_prvMtnOpRlz[1].id"/>
                            <InfoElement title="end date" :info_content="infos_prvMtnOpRlz[2].info_value" :info_id="infos_prvMtnOpRlz[2].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEleven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                        Equipment Curative Maintenace operation infos
                        </button>
                    </h2>
                    <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven">
                        <div class="accordion-body">
                            <InfoElement title="number" :info_content="infos_curMtnOp[0].info_value" :info_id="infos_curMtnOp[0].id"/>
                            <InfoElement title="report number" :info_content="infos_curMtnOp[1].info_value" :info_id="infos_curMtnOp[1].id"/>
                            <InfoElement title="description" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>
                            <InfoElement title="start date" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>
                            <InfoElement title="end date" :info_content="infos_curMtnOp[2].info_value" :info_id="infos_curMtnOp[2].id"/>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwelve">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                            User infos
                        </button>
                    </h2>
                    <div id="collapseTwelve" class="accordion-collapse collapse" aria-labelledby="headingTwelve">
                        <div class="accordion-body">
                            <InfoElement title="first name" :info_content="infos_person[0].info_value" :info_id="infos_person[0].id"/>
                            <InfoElement title="last name" :info_content="infos_person[1].info_value" :info_id="infos_person[1].id"/>
                            <InfoElement title="initial" :info_content="infos_person[2].info_value" :info_id="infos_person[2].id"/>
                            <InfoElement title="signature path" :info_content="infos_person[3].info_value" :info_id="infos_person[3].id"/>
                            <InfoElement title="user name" :info_content="infos_person[4].info_value" :info_id="infos_person[4].id"/>
                            <InfoElement title="password" :info_content="infos_person[5].info_value" :info_id="infos_person[5].id"/>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InfoElement from './InfoElement.vue'
export default {
    components:{
        InfoElement
    },
    data(){
        return{
            infos:[],
            infos_idCard:[],
            infos_dimension:[],
            infos_power:[],
            infos_spProc:[],
            infos_usage:[],
            infos_file:[],
            infos_prvMtnOp:[],
            infos_risk:[],
            infos_state:[],
            infos_prvMtnOpRlz:[],
            infos_curMtnOp:[],
            infos_enum:[],
            infos_person:[],
            loaded:false
        }
    },
    created(){
            if(this.$userId.user_updateInformationRight!=true){
                this.$router.replace({ name: "home" })
            }
            axios.get('/info/send/eqIdCard')
            .then (response=> {
                this.infos_idCard=response.data;
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/dimension')
            .then (response=> {
                this.infos_dimension=response.data;
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/power')
            .then (response=> {
                this.infos_power=response.data;
                
            }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/specialProcess')
            .then (response=> {
                this.infos_spProc=response.data;
            }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/usage')
            .then (response=> {
                this.infos_usage=response.data;
                }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/file')
            .then (response=> {
                this.infos_file=response.data;
            }) 
            .catch(error => console.log(error)) ;


            axios.get('/info/send/preventiveMaintenanceOperation')
            .then (response=> {
                this.infos_prvMtnOp=response.data;
            }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/risk')
            .then (response=> {
                this.infos_risk=response.data;
            }) 
            .catch(error => console.log(error)) ;

            axios.get('/info/send/state')
            .then (response=> {
                this.infos_state=response.data;
                }) 
            .catch(error => console.log(error)) ;

           axios.get('/info/send/preventiveMaintenanceOperationRealized')
            .then (response=> {
                this.infos_prvMtnOpRlz=response.data;
                }) 
            .catch(error => console.log(error)) ;

           axios.get('/info/send/curativeMaintenanceOperation')
            .then (response=> {
                this.infos_curMtnOp=response.data;
                }) 
            .catch(error => console.log(error)) ;

           axios.get('/info/send/person')
            .then (response=> {
                console.log(response.data)
                this.infos_person=response.data;
                this.loaded=true;
                }) 
            .catch(error => console.log(error)) ;
    }
}
</script>

<style>

</style>
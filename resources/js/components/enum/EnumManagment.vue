<template>
    <div>
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div class="enumManagment" v-if="loaded==true">
            <h1>ENUM MANAGMENT</h1>
            <EnumElement error_name='enum_eq_type' :enumList="enum_eq_type" title="Equipment Type" url="/equipment/enum/type/" info_text="testttt"/>
            <EnumElement error_name='enum_eq_massUnit' :enumList="enum_eq_massUnit" title="Equipment Mass Unit" url="/equipment/enum/massUnit/" />
            <EnumElement error_name='enum_dim_type' :enumList="enum_dim_type" title="Equipment Dimension Type" url="/dimension/enum/type/" />
            <EnumElement error_name='enum_dim_name' :enumList="enum_dim_name" title="Equipment Dimension Name" url="/dimension/enum/name/" />
            <EnumElement error_name='enum_dim_unit'  :enumList="enum_dim_unit" title="Equipment Dimension Unit" url="/dimension/enum/unit/" />
            <EnumElement error_name='enum_pow_type'  :enumList="enum_pow_type" title="Equipment Power Type" url="/power/enum/type/" />
            <EnumElement error_name='enum_risk_for'  :enumList="enum_risk_for" title="Equipment Risk For" url="/risk/enum/riskfor/" />
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
            loaded:false

        }
    },
    created(){
        /*Ask for the controller different equipment type option */
        axios.get('/equipment/enum/type')
            .then (response=> this.enum_eq_type=response.data) 
            .catch(error => console.log(error)) ;
        /*Ask for the controller different equipment mass unit option */
        axios.get('/equipment/enum/massUnit')
            .then (response=> this.enum_eq_massUnit=response.data) 
            .catch(error => console.log(error)) ; 
        /*Ask for the controller different types of the dimension  */
        axios.get('/dimension/enum/type')
            .then (response=> this.enum_dim_type=response.data) 
            .catch(error => console.log(error)) ;
        /*Ask for the controller different names of the dimension  */
        axios.get('/dimension/enum/name')
            .then (response=> this.enum_dim_name=response.data) 
            .catch(error => console.log(error)) ;
         /*Ask for the controller different unites of the dimension  */
        axios.get('/dimension/enum/unit')
            .then (response=> this.enum_dim_unit=response.data) 
            .catch(error => console.log(error)) ;
        /*Ask for the controller different types of the power  */
        axios.get('/power/enum/type')
            .then (response=> this.enum_pow_type=response.data) 
            .catch(error => console.log(error)) ;
        axios.get('/risk/enum/riskfor')
            .then (response=>{
                this.enum_risk_for=response.data
                this.loaded=true
            }) 
            .catch(error => console.log(error)) ;
    }

}
</script>

<style>

</style>
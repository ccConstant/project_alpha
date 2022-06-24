<template>
    <div class="MMEVerifRlz" >
        <h2 class="titleForm">MME verification</h2>
        <span class="hr"></span>
        <!--Adding to the vue MMEVerifRlzForm by going through the components array with the v-for-->
        <!--ref="ask_verifRlz_data" is used to call the child elements in this component-->
        <!--The emitted deleteVerifRlz is catched here and call the function getContent -->
        <MMEVerifRlzForm ref="ask_verifRlz_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :number="component.number" :isPassed="component.isPassed"
            :reportNumber="component.reportNumber" :startDate="component.startDate" :endDate="component.endDate"
            :divClass="component.className" :id="component.id" :state_id="data_state_id" :verif_id_prop="component.verif_id"
            :verif_expectedResult_prop="component.verif_expectedResult" :verif_nonComplianceLimit_prop="component.verif_nonComplianceLimit"
            :verif_description_prop="component.verif_description" :verif_number_prop="component.verif_number" :verif_protocol_prop="component.verif_protocol"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :mme_id="data_mme_id"
            @deleteVerifRlz="getContent(key)" @addSucces="addSucces()" />
    
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import MMEVerifRlzForm from './MMEVerifRlzForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
export default {
/*--------Declartion of the others Components:--------*/
    components:{
        MMEVerifRlzForm,
        SaveButtonForm,
    },
    props:{
        consultMod:{
            type:Boolean,
            default:false
        },
        modifMod:{
            type:Boolean,
            default:false
        },
        importedVerifRlz:{
            type:Array,
            default:null
        },
        mme_id:{
            type:Number
        },
        state_id:{
            type:Number
        }
    },
    data() {
        return {
            components: [],
            uniqueKey:0,
            verifsRlz:this.importedVerifRlz,
            count:0,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            data_mme_id:this.mme_id,
            data_state_id:this.state_id
        }
    },
    methods:{
        addComponent() {
            this.components.push({
                comp:'MMEVerifRlzForm',
                key : this.uniqueKey++,
            });
        },
        addImportedComponent(verifRlz_number,verifRlz_reportNumber,verifRlz_startDate,
        verifRlz_endDate,verifRlz_isPassed,verifRlz_validate,verifRlz_className,id,
        verif_id,verif_expectedResult,verif_nonComplianceLimit,verif_description,verif_number,verif_protocol) {
            this.components.push({
                comp:'MMEVerifRlzForm',
                key : this.uniqueKey++,
                number:verifRlz_number,
                reportNumber :verifRlz_reportNumber,
                startDate:verifRlz_startDate,
                endDate:verifRlz_endDate,
                isPassed:verifRlz_isPassed,
                className:verifRlz_className,
                validate:verifRlz_validate,
                id:id,
                verif_id:verif_id,
                verrif_expectedResult:verif_expectedResult,
                verif_nonComplianceLimit:verif_nonComplianceLimit,
                verif_description:verif_description,
                verif_number:verif_number,
                verif_protocol:verif_protocol
            });
        },
        //Suppresion of a preventive maintenance operation component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        importVerifRlz(){
            for (const verifRlz of this.verifsRlz) {
                var className="importedVerifRlz"+verifRlz.id
                this.addImportedComponent(verifRlz.verifRlz_number,verifRlz.verifRlz_reportNumber,verifRlz.verifRlz_startDate,
                    verifRlz.verifRlz_endDate,verifRlz.verifRlz_isPassed,verifRlz.verifRlz_validate,className,verifRlz.id,
                    verifRlz.verif_id,verifRlz.verif_expectedResult,verifRlz.verif_nonComplianceLimit,
                    verifRlz.verif_description,verifRlz.verif_number,verifRlz.verif_protocol);
            }
            this.verifsRlz=null
        },
        //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_verifRlz_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the preventive maintenance operation doesn't have an id
                    if(component.verifRlz_id==null ){
                        //addMmeVerifRlz is used
                        component.addMmeVerifRlz(savedAs);
                    }else
                    //Else if the preventive maintenance operation have an id and addSucces is equal to true 
                    if(component.verifRlz_id!=null || component.addSucces==true){
                        //updateMmeVerifRlz is used
                        component.updateMmeVerifRlz(savedAs);
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addMmeVerifRlz(savedAs);
                }
                

            }
        },
        addSucces(){
            this.$emit('addSucces','')
        }
    },
    /*All function inside the created option is called after the component has been mounted.*/
    mounted(){
        //If the user is in consultation or modification mode preventive maintenance operation will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importVerifRlz();
        }else{
            this.addComponent();
        }
    },
    created(){
        if(this.$userId.user_makeMmeOpValidationRight!=true){
            this.$router.push({ name: "home"});
        }
    },
}
</script>

<style>

</style>
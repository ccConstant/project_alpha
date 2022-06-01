<template>
    <div class="equipmentprvMtnOpRlz" >
        <h2 class="titleForm">Equipment Preventive Maintenance Operation</h2>
        <span class="hr"></span>
        <!--Adding to the vue EquipmentPrvMtnOpRlzForm by going through the components array with the v-for-->
        <!--ref="ask_prvMtnOpRlz_data" is used to call the child elements in this component-->
        <!--The emitted deletePrvMtnOpRlz is catched here and call the function getContent -->
        <EquipmentPrvMtnOpRlzForm ref="ask_prvMtnOpRlz_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :number="component.number"
            :reportNumber="component.reportNumber" :startDate="component.startDate" :endDate="component.endDate"
            :divClass="component.className" :id="component.id" :state_id="data_state_id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            @deletePrvMtnOpRlz="getContent(key)" @addSucces="addSucces()" />
    
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
    </div>
</template>

<script>
/*Importation of the others Components who will be used here*/
import EquipmentPrvMtnOpRlzForm from './EquipmentPrvMtnOpRlzForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
export default {
/*--------Declartion of the others Components:--------*/
    components:{
        EquipmentPrvMtnOpRlzForm,
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
        importedPrvMtnOpRlz:{
            type:Array,
            default:null
        },
        eq_id:{
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
            prvMtnOpsRlz:this.importedPrvMtnOpRlz,
            count:0,
            isInConsultMod:this.consultMod,
            isInModifMod:this.modifMod,
            data_eq_id:this.eq_id,
            data_state_id:this.state_id
        }
    },
    methods:{
   
        addComponent() {
            this.components.push({
                comp:'EquipmentPrvMtnOpRlzForm',
                key : this.uniqueKey++,
            });
        },

        addImportedComponent(prvMtnOpRlz_number,prvMtnOpRlz_reportNumber,prvMtnOpRlz_startDate,prvMtnOpRlz_endDate,prvMtnOpRlz_validate,prvMtnOpRlz_className,id) {
            this.components.push({
                comp:'EquipmentPrvMtnOpRlzForm',
                key : this.uniqueKey++,
                number:prvMtnOpRlz_number,
                reportNumber :prvMtnOpRlz_reportNumber,
                startDate:prvMtnOpRlz_startDate,
                endDate:prvMtnOpRlz_endDate,
                className:prvMtnOpRlz_className,
                validate:prvMtnOpRlz_validate,
                id:id
            });
        },
        //Suppresion of a preventive maintenance operation component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported preventive maintenance operation
        importPrvMtnOpRlz(){
            for (const prvMtnOpRlz of this.prvMtnOpsRlz) {
                var className="importedPrvMtnOpRlz"+prvMtnOpRlz.id
                this.addImportedComponent(prvMtnOpRlz.prvMtnOpRlz_number,prvMtnOpRlz.prvMtnOpRlz_reportNumber,prvMtnOpRlz.prvMtnOpRlz_startDate,
                    prvMtnOpRlz.prvMtnOpRlz_endDate,prvMtnOpRlz.prvMtnOpRlz_validate,className,prvMtnOpRlz.id);
            }
            this.prvMtnOpsRlz=null
        },
                //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_prvMtnOpRlz_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the preventive maintenance operation doesn't have an id
                    if(component.prvMtnOpRlz_id==null ){
                        //AddequipmentPrvMtnOpRlz is used
                        component.addEquipmentPrvMtnOpRlz(savedAs);
                    }else
                    //Else if the preventive maintenance operation have an id and addSucces is equal to true 
                    if(component.prvMtnOpRlz_id!=null || component.addSucces==true){
                        //updateEquipmentPrvMtnOpRlz is used
                        component.updateEquipmentPrvMtnOpRlz(savedAs);
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addEquipmentPrvMtnOpRlz(savedAs);
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
            this.importPrvMtnOpRlz();
        }else{
            this.addComponent();
        }
    }

}
</script>

<style>

</style>
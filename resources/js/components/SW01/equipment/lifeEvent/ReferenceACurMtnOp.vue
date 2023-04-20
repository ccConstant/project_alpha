<!--File name : ReferenceACurMtnOp.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a curative maintenance operation-->

<template>
      <div class="equipmentCurMtnOp" >
        <h2 class="titleForm">Equipment Curative Maintenance Operation</h2>
        <span class="hr"></span>
        <!--Adding to the vue EquipmentCurMtnOpForm by going through the components array with the v-for-->
        <!--ref="ask_curMtnOp_data" is used to call the child elements in this component-->
        <!--The emitted deleteCurMtnOp is caught here and call the function getContent -->
        <EquipmentCurMtnOpForm ref="ask_curMtnOp_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :number="component.number" :description="component.description"
            :reportNumber="component.reportNumber" :startDate="component.startDate" :endDate="component.endDate"
            :divClass="component.className" :id="component.id" :state_id="data_state_id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"
            @deleteCurMtnOp="getContent(key)" @addSucces="addSucces()" />
        <SaveButtonForm :in_life_sheet="false" saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentCurMtnOpForm from './EquipmentCurMtnOpForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
export default {
    /*--------Declaration of the others Components:--------*/
    components:{
        EquipmentCurMtnOpForm,
        SaveButtonForm,
    },
    /*--------Declaration of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedCurMtnOp: All curative maintenance operations imported from the database
        eq_id: ID of the equipment in which the curative maintenance operation will be added
    ---------------------------------------------------*/
    props:{
            consultMod:{
                type:Boolean,
                default:false
            },
            modifMod:{
                type:Boolean,
                default:false
            },
            importedCurMtnOp:{
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
    /*--------Declaration of the differents returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    curMtnOps: Array of all imported curative maintenance operation
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
      return {
        components: [],
        uniqueKey:0,
        curMtnOps:this.importedCurMtnOp,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_eq_id:this.eq_id,
        data_state_id:this.state_id
      };
    },
    methods:{
        //Function for adding a new (empty) curative maintenance operation form
        addComponent() {
            this.components.push({
                comp:'EquipmentCurMtnOpForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding an imported curative maintenance operation form with his data
        addImportedComponent(curMtnOp_number,curMtnOp_reportNumber,curMtnOp_description,curMtnOp_startDate,curMtnOp_endDate,curMtnOp_validate,curMtnOp_className,id) {
            this.components.push({
                comp:'EquipmentCurMtnOpForm',
                key : this.uniqueKey++,
                number:curMtnOp_number,
                reportNumber :curMtnOp_reportNumber,
                description:curMtnOp_description,
                startDate:curMtnOp_startDate,
                endDate:curMtnOp_endDate,
                className:curMtnOp_className,
                validate:curMtnOp_validate,
                id:id
            });
        },
        //Suppression of a curative maintenance operation component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported curative maintenance operation
        importCurMtnOp(){
            for (const curMtnOp of this.curMtnOps) {
                const className = "importedCurMtnOp" + curMtnOp.id;
                this.addImportedComponent(curMtnOp.curMtnOp_number,curMtnOp.curMtnOp_reportNumber,curMtnOp.curMtnOp_description,curMtnOp.curMtnOp_startDate,
                    curMtnOp.curMtnOp_endDate,curMtnOp.curMtnOp_validate,className,curMtnOp.id);
            }
            this.curMtnOps=null
        },
        //Function for saving all the data in one time
        saveAll(savedAs){
            for(const component of this.$refs.ask_curMtnOp_data){
                //If the user is in modification mode
                if(this.modifMod==true){
                    //If the curative maintenance operation doesn't have an id
                    if(component.curMtnOp_id==null ){
                        //AddequipmentCurMtnOp is used
                        component.addEquipmentCurMtnOp(savedAs);
                    }else
                    //Else if the curative maintenance operation has an id and addSucces, is equal to true
                    if(component.curMtnOp_id!=null || component.addSucces==true){
                        //updateEquipmentCurMtnOp is used
                        component.updateEquipmentCurMtnOp(savedAs);
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addEquipmentCurMtnOp(savedAs);
                }


            }
        },
        addSucces(){
            this.$emit('addSucces','')
        }
    },
    mounted(){
        //If the user is in consultation or modification mode, the imported curative maintenance operation will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importCurMtnOp();
        }else{
            // else an empty one is added to the vue
            this.addComponent();
        }
    },
    created(){
        if(this.$userId.user_makeEqOpValidationRight!=true){
            this.$router.push({ name: "home"});
        }
    },

}
</script>

<style>

</style>

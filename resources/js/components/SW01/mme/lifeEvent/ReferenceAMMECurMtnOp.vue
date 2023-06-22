<!--File name : ReferenceAMMECurMtnOp.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a curative maintenance operation in the MME-->

<template>
      <div class="MmeCurMtnOp" >
        <h2 class="titleForm">MME Curative Maintenance Operation</h2>
        <span class="hr"></span>
        <!--Adding to the vue MmeCurMtnOpForm by going through the components array with the v-for-->
        <!--ref="ask_curMtnOp_data" is used to call the child elements in this component-->
        <!--The emitted deleteCurMtnOp is caught here and call the function getContent -->
        <MmeCurMtnOpForm ref="ask_curMtnOp_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :number="component.number" :description="component.description"
            :reportNumber="component.reportNumber" :startDate="component.startDate" :endDate="component.endDate"
            :divClass="component.className" :id="component.id" :state_id="data_state_id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :mme_id="data_mme_id"
            @deleteCurMtnOp="getContent(key)" @addSucces="addSucces()" />
        <SaveButtonForm :in_life_sheet="false" saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
    </div>
</template>
<script>
/*Importation of the other Components who will be used here*/
import MmeCurMtnOpForm from './MmeCurMtnOpForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
export default {
    /*--------Declaration of the others Components:--------*/
    components:{
        MmeCurMtnOpForm,
        SaveButtonForm,
    },
	/*--------Declaration of the differents props:--------
	consultMod: If this props is present the form is in consult mode we disable all the field
	modifMod: If this props is present the form is in modification mode we disable the save button and show update button
	importedCurMtnOp: All curative maintenance operation imported from the database
	mme_id: Id of the mme in which the curative maintenance operation will be added
	state_id: The ID of the current state of the affected MME
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
		mme_id:{
			type:Number
		},
		state_id:{
			type:Number
		},
        number:{
            type:Number
        }
        
    },
    /*--------Declaration of the differents returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    curMtnOps: Array of all imported curative maintenance operation
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_mme_id: data of the mme_id prop
    -----------------------------------------------------------*/
    data() {
      return {
        components: [],
        uniqueKey:0,
        curMtnOps:this.importedCurMtnOp,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_mme_id:this.mme_id,
        data_state_id:this.state_id
      };
    },
    methods:{
        //Function for adding a new empty curative maintenance operation form
        addComponent() {
            this.components.push({
                comp:'MmeCurMtnOpForm',
                key : this.uniqueKey++,
            });
        },
        //Function for adding an imported curative maintenance operation form with his data
        addImportedComponent(curMtnOp_number,curMtnOp_reportNumber,curMtnOp_description,curMtnOp_startDate,curMtnOp_endDate,curMtnOp_validate,curMtnOp_className,id) {
            this.components.push({
                comp:'MmeCurMtnOpForm',
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
                const className="importedCurMtnOp"+curMtnOp.id
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
                        component.addMmeCurMtnOp(savedAs);
                    }else
                    //Else if the curative maintenance operation have an id and addSucces is equal to true
                    if(component.curMtnOp_id!=null || component.addSucces==true){
                        component.updateMmeCurMtnOp(savedAs);
                    }
                }else{
                    //Else If the user is not in modification mode
                    component.addMmeCurMtnOp(savedAs);
                }
            }
        },
        addSucces(){
            this.$emit('addSucces','')
        },
    },
    mounted(){
        //If the user is in consultation or modification mode, curative maintenance operation will be added to the vue automatically
        if(this.consultMod || this.modifMod ){
            this.importCurMtnOp();
        }else{
		// Else an empty one will be added to the vue
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

<template>
    <div class="MmePrctn">
        <div v-if="this.components.length>0">
            <h3 class="titleForm">Precaution</h3>
        </div>
        
        <!--Adding to the vue MMEPrecautionForm by going through the components array with the v-for-->
        <!--ref="ask_prctn_data" is used to call the child elements in this component-->
        <!--The emitted deletePrctn is catched here and call the function getContent -->
        <MMEPrecautionForm ref="ask_prctn_data" v-for="(component, key) in components" :key="component.key"
            :is="component.comp" :type="component.type" :description="component.description"
            :divClass="component.className" :id="component.id" :usg_id="data_usg_id"
            :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod" :mme_id="data_mme_id"
            @deletePrctn="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another precaution button appear -->
            <button v-on:click="addComponent">Add Precaution</button>
            <!--If prctns array is not empty and if the user is not in modifacion mode -->
            <div v-if="this.prctns!==null">
                <!--The importation button appear-->
                <button v-if="!modifMod " v-on:click="importPrctn">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>
  
</template>

<script>
/*Importation of the others Components who will be used here*/
import MMEPrecautionForm from './MMEPrecautionForm.vue'
import SaveButtonForm from '../../button/SaveButtonForm.vue'
import ImportationAlert from '../../alert/ImportationAlert.vue'
export default {
    /*--------Declartion of the others Components:--------*/
    components:{
        MMEPrecautionForm,
        SaveButtonForm,
        ImportationAlert

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
        importedPrctn:{
            type:Array,
            default:null
        },
        mme_id:{
            type:Number
        },
        usg_id:{
            type:Number
        },
        import_id:{
            type:Number,
            default:null
        }
    },
    data() {
      return {
        components: [],
        uniqueKey:0,
        prctns:this.importedPrctn,
        count:0,
        isInConsultMod:this.consultMod,
        isInModifMod:this.modifMod,
        data_mme_id:this.mme_id,
        data_usg_id:this.usg_id,
      };
    },

}
</script>

<style>

</style>
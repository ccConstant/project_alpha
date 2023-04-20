<!--File name :ReferenceARisk.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a risk in the equipment-->

<template>
    <div>
        <div v-if="this.riskForEq==true">
            <div class="equipmentRisk">
                <h2 class="titleForm">Equipment Risk</h2>
                <!--Adding to the vue EquipmentRiskForm by going through the components array with the v-for-->
                <!--ref="ask_risk_data" is used to call the child elements in this component-->
                <!--The emitted deleteRisk is caught here and call the function getContent -->
                <EquipmentRiskForm ref="ask_risk_data" v-for="(component, key) in components" :key="component.key"
                                   :is="component.comp" :for="component.for" :remarks="component.remarks"
                                   :riskForEq="true"
                                   :wayOfControl="component.wayOfControl" :divClass="component.className"
                                   :id="component.id"
                                   :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod"
                                   :eq_id="data_eq_id"
                                   @deleteRisk="getContent(key)"/>
                <!--If the user is not in consultation mode -->
                <div v-if="!this.consultMod">
                    <!--Add another risk button appear -->
                    <button v-on:click="addComponent">Add Risk</button>
                    <!--If risks array is not empty and if the user is not in modification mode -->
                    <div v-if="this.risks!==null">
                        <!--The importation button appear -->
                        <button v-if="!modifMod " v-on:click="importRisk">import</button>
                    </div>
                </div>
                <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                                :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
                <ImportationAlert ref="importAlert"/>
            </div>
        </div>

        <div v-else>
            <div class="prvMtnOpRisk">
                <div v-if="this.components.length>0">
                    <h3 class="titleForm">Preventive maintenance operation Risk</h3>
                </div>

                <!--Adding to the vue EquipmentRiskForm by going through the components array with the v-for-->
                <!--ref="ask_risk_data" is used to call the child elements in this component-->
                <!--The emitted deleteRisk is caught here and call the function getContent -->
                <EquipmentRiskForm ref="ask_risk_data" v-for="(component, key) in components" :key="component.key"
                                   :is="component.comp" :for="component.for" :remarks="component.remarks"
                                   :riskForEq="false"
                                   :wayOfControl="component.wayOfControl" :divClass="component.className"
                                   :id="component.id" :prvMtnOp_id="data_prvMtnOp_id"
                                   :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod"
                                   :eq_id="data_eq_id"
                                   @deleteRisk="getContent(key)"/>
                <!--If the user is not in consultation mode -->
                <div v-if="!this.consultMod">
                    <!--Add another risk button appear -->
                    <button v-on:click="addComponent">Add Risk</button>
                    <!--If risks array is not empty and if the user is not in modification mode -->
                    <div v-if="this.risks!==null">
                        <!--The importation button appear-->
                        <button v-if="!modifMod " v-on:click="importRisk">import</button>
                    </div>
                </div>
                <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                                :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
                <ImportationAlert ref="importAlert"/>
            </div>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentRiskForm from './EquipmentRiskForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentRiskForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        importedRisk: All risk imported from the database
        prvMtnop_id: ID of the preventive maintenance operation in which the risk will be added
        eq_id: ID of the equipment in which the risk will be added
        import_id: ID of the equipment with which risks will be imported
        riskForEq : If this props is present the risk is for the equipment
    ---------------------------------------------------*/
    props: {
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        importedRisk: {
            type: Array,
            default: null
        },
        eq_id: {
            type: Number
        },
        prvMtnOp_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        riskForEq: {
            type: Boolean
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        risks: Array of all imported risks
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_eq_id: data of the eq_id prop
        data_riskForEq: data of the riskForEq prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            risks: this.importedRisk,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_eq_id: this.eq_id,
            data_prvMtnOp_id: this.prvMtnOp_id,
            data_riskForEq: this.riskForeq
        };
    },
    methods: {
        /*Function for adding a new empty risk form*/
        addComponent() {
            this.components.push({
                comp: 'EquipmentRiskForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported risk form with his data*/
        addImportedComponent(risk_for, risk_remarks, risk_wayOfControl, risk_validate, risk_className, id) {
            this.components.push({
                comp: 'EquipmentRiskForm',
                key: this.uniqueKey++,
                for: risk_for,
                remarks: risk_remarks,
                wayOfControl: risk_wayOfControl,
                className: risk_className,
                validate: risk_validate,
                id: id
            });
        },
        /*Suppression of a risk component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported risk*/
        importRisk() {
            if (this.risks.length == 0 && !this.isInModifMod && this.riskForEq == true) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const risk of this.risks) {
                    const className = "importedRisk" + risk.id;
                    this.addImportedComponent(risk.risk_for, risk.risk_remarks, risk.risk_wayOfControl, risk.risk_validate, className, risk.id);
                }
            }
            this.risks = null
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_risk_data) {
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    /*If the risk doesn't have an id*/
                    if (component.risk_id == null) {
                        /*AddequipmentRisk is used*/
                        component.addEquipmentRisk(savedAs);
                    } else
                        /*Else if the risk has an id and if addSucces is equal to true*/
                    if (component.risk_id != null || component.addSucces == true) {
                        /*updateEquipmentRisk is used*/
                        if (component.risk_validate !== "validated") {
                            component.updateEquipmentRisk(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentRisk(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the risk corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/equipment/risk/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => this.risks = response.data)
                .catch(error => console.log(error));
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, risk will be added to the vue automatically*/
        if (this.risks !== null) {
            if (this.riskForEq == false) {
                console.log(this.importedRisk);
            }
            this.importRisk();
        }
    }
}
</script>

<style lang="scss">
</style>

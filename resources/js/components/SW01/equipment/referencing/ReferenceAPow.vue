<!--File name :ReferenceAPow.vue-->
<!--Creation date : 17 May 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference a power in the equipment-->

<template>
    <div class="equipmentPow">
        <h2 class="titleForm">Equipment Power source(s)</h2>
        <!--Adding to the vue EquipmentPowForm by going through the components array with the v-for-->
        <!--ref="ask_pow_data" is used to call the child elements in this component-->
        <!--The emitted deleteDim is caught here and call the function getContent -->
        <EquipmentPowForm :is="component.comp" v-for="(component, key) in components" :id="component.id"
                          :key="component.key" ref="ask_pow_data" :consultMod="isInConsultMod"
                          :consumptionUnit="component.consumptionUnit" :consumptionValue="component.consumptionValue" :divClass="component.className"
                          :eq_id="data_eq_id" :modifMod="isInModifMod"
                          :name="component.powName"
                          :type="component.type" :unit="component.unit" :validate="component.validate"
                          :value="component.value"
                          @deletePow="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If dimensions array is not empty and if the user is not in modification mode -->
            <div v-if="this.powers!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod" v-on:click="importPow">import</button>
            </div>
        </div>
        <SaveButtonForm v-if="components.length>2" :consultMod="this.consultMod" :modifMod="this.modifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
        <ImportationAlert ref="importAlert"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentPowForm from './EquipmentPowForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentPowForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable save button and show update button
        importedPow: All powers imported from the database
        eq_id: ID of the equipment in which the power will be added
        import_id: ID of the equipment with which power will be imported
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
        importedPow: {
            type: Array,
            default: null
        },
        eq_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        dimensions: Array of all imported dimensions
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modification mode prop
        data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [{}],
            uniqueKey: 0,
            powers: this.importedPow,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_eq_id: this.eq_id
        };
    },
    methods: {
        /*Function for adding a new empty power form*/
        addComponent() {
            this.components.push({
                comp: 'EquipmentPowForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported power form with his data*/
        addImportedComponent(pow_type, pow_name, pow_value, pow_unit, pow_consumptionValue, pow_consumptionUnit, pow_validate, pow_className, id) {
            this.components.push({
                comp: 'EquipmentPowForm',
                key: this.uniqueKey++,
                type: pow_type,
                powName: pow_name,
                value: pow_value.toString(),
                unit: pow_unit,
                consumptionValue: pow_consumptionValue,
                consumptionUnit: pow_consumptionUnit,
                className: pow_className,
                validate: pow_validate,
                id: id
            });
        },
        /*Suppression of a power component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported power*/
        importPow() {
            if (this.powers.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const power of this.powers) {
                    const className = "importedPow" + power.id;
                    this.addImportedComponent(power.pow_type, power.pow_name, power.pow_value, power.pow_unit,
                        power.pow_consumptionValue, power.pow_consumptionUnit, power.pow_validate, className, power.id);
                }
            }
            this.powers = null;

        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_pow_data) {
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    /*If the dimension doesn't have, an id*/
                    if (component.pow_id == null) {
                        /*AddequipmentPow is used*/
                        component.addEquipmentPow(savedAs);
                    } else
                        /*Else if the dimension has an id and addSucces is equal to true*/
                    if (component.pow_id != null || component.addSucces == true) {
                        /*updateEquipmentpow is used*/
                        if (component.pow_validate !== "validated") {
                            component.updateEquipmentPow(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentPow(savedAs);
                }
            }
        }
    },
    created() {
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the dimension corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/power/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.powers = response.data;
                }).catch(error => {
            });
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode powers will be added to the vue automatically*/
        if (this.consultMod || this.modifMod) {
            this.importPow();
        }
    }
}
</script>

<style>

</style>

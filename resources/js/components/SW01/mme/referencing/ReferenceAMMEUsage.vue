<!--File name : ReferenceAMMEUsage.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference a usage in a MME-->

<template>
    <div class="MMEUsage">
        <h2 class="titleForm">MME Usage</h2>
        <MMEUsageForm :is="component.comp" v-for="(component, key) in components" :id="component.id"
                      :key="component.key" ref="ask_usage_data" :application="component.application"
                      :consultMod="isInConsultMod"
                      :divClass="component.className" :import_id="component.import" :measurementType="component.measurementType"
                      :metrologicalLevel="component.metrologicalLevel"
                      :mme_id="data_mme_id" :modifMod="isInModifMod" :precision="component.precision"
                      :reformBy="component.reformBy"
                      :reformDate="component.reformDate" :reformMod="isInReformMod"
                      :validate="component.validate"
                      @deleteUsage="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another preventive maintenance operation button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If preventive maintenance operation array is not empty and if the user is not in modification mode -->
            <div v-if="this.usages!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importUsage">import</button>
            </div>
        </div>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
        <ImportationAlert ref="importAlert"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import MMEUsageForm from './MMEUsageForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        MMEUsageForm,
        SaveButtonForm,
        ImportationAlert
    },
    props: {
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        importedUsage: {
            type: Array,
            default: null
        },
        mme_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        reformMod: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            components: [],
            uniqueKey: 0,
            usages: this.importedUsage,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            isInReformMod: this.reformMod,
            data_mme_id: this.mme_id
        };
    },
    methods: {
        //Function for adding a new usage form
        addComponent() {
            this.components.push({
                comp: 'MMEUsageForm',
                key: this.uniqueKey++,
            });
        },
        //Function for adding an imported usage form with his data
        addImportedComponent(usg_measurementType, usg_precision, usg_metrologicalLevel,
                             usg_application, usg_validate, usg_className, id, usg_reformDate, usg_reformBy) {
            this.components.push({
                comp: 'MMEUsageForm',
                key: this.uniqueKey++,
                measurementType: usg_measurementType,
                precision: usg_precision,
                metrologicalLevel: usg_metrologicalLevel,
                application: usg_application,
                className: usg_className,
                validate: usg_validate,
                import: this.import_id,
                id: id,
                reformDate: usg_reformDate,
                reformBy: usg_reformBy
            });
        },
        //Suppression of a usage component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported usage from the database
        importUsage() {
            if (this.usages.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const usage of this.usages) {
                    var className = "importedUsage" + usage.id
                    this.addImportedComponent(usage.usg_measurementType, usage.usg_precision, usage.usg_metrologicalLevel,
                        usage.usg_application, usage.usg_validate, className, usage.id, usage.usg_reformDate, usage.usg_reformBy);
                }
            }
            this.usages = null
        },
        //Function for saving all the data in one time
        saveAll(savedAs) {
            for (const component of this.$refs.ask_usage_data) {
                //If the user is in modification mode
                if (this.modifMod == true) {
                    //If the usage doesn't have, an id
                    if (component.usg_id == null) {
                        component.addMmeUsage(savedAs);
                    } else
                        //Else if the usage has an id and addSucces is equal to true
                    if (component.usg_id != null || component.addSucces == true) {
                        if (component.usg_validate !== "validated") {
                            component.updateMmeUsage(savedAs);
                        }
                    }
                } else {
                    //Else If the user is not in modification mode
                    component.addMmeUsage(savedAs);
                }
            }
        }
    },
    created() {
        //If the user chooses imported usage
        if (this.import_id !== null) {
            //Make a get request to ask the controller the usage corresponding to the id of the equipment with which data will be imported
            const consultUrl = (id) => `/mme_usage/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => this.usages = response.data)
                .catch(error => {
                });
        }
    },
    mounted() {
        //If the user is in consultation or modification mode, the usage will be added to the vue automatically
        if (this.consultMod || this.modifMod) {
            this.importUsage();
        }
    }
}
</script>

<style>

</style>

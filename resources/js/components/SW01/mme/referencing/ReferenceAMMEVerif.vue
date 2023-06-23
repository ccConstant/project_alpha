<!--File name : ReferenceAMMEVerif.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to reference a verification in a MME-->

<template>
    <div class="mmeVerif">
        <h2 class="titleForm">MME Verification</h2>
        <!--Adding to the vue MMEVerificationForm by going through the components array with the v-for-->
        <!--ref="ask_verif_data" is used to call the child elements in this component-->
        <!--The emitted deleteVerif is caught here and call the function getContent -->
        <MMEVerificationForm
            :is="component.comp"
            v-for="(component, key) in components"
            :id="component.id"
            :key="component.key"
            ref="ask_verif_data"
            :consultMod="isInConsultMod"
            :description="component.description"
            :divClass="component.className"
            :expectedResult="component.expectedResult"
            :mesureRange="component.mesureRange"
            :mesureUncert="component.mesureUncert"
            :mme_id="data_mme_id"
            :modifMod="isInModifMod"
            :name="component.name"
            :nonComplianceLimit="component.nonComplianceLimit"
            :number="component.number"
            :periodicity="component.periodicity"
            :preventiveOperation="component.preventiveOperation"
            :protocol="component.protocol"
            :puttingIntoService="component.puttingIntoService"
            :reformBy="component.reformBy"
            :reformDate="component.reformDate"
            :reformMod="isInReformMod"
            :requiredSkill="component.requiredSkill"
            :symbolPeriodicity="component.symbolPeriodicity"
            :validate="component.validate"
            :verifAcceptanceAuthority="component.verifAcceptanceAuthority"
            @deleteVerif="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another verification button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If verifications array is not null and if the user is not in modification mode -->
            <div v-if="this.verifs!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importVerif">import</button>
            </div>
        </div>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
        <ImportationAlert ref="importAlert"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import MMEVerificationForm from './MMEVerificationForm.vue'

export default {
    components: {
        MMEVerificationForm,
        SaveButtonForm,
        ImportationAlert
    },
    /*--------Declaration of the differents props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode, we disable the save button and show update button
        importedVerif: All the verification imported from the database
        mme_id: ID of the mme in which the verification will be added
        import_id: ID of the mme with which verification will be imported
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
        importedVerif: {
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
    /*--------Declaration of the differents returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    verifs: Array of all imported verifications
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_mme_id: data of the mme_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            verifs: this.importedVerif,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            isInReformMod: this.reformMod,
            data_mme_id: this.mme_id
        };
    },
    methods: {
        //Function for adding a new verification form
        addComponent() {
            this.components.push({
                comp: 'MMEVerificationForm',
                key: this.uniqueKey++,
            });
        },
        //Function for adding an imported verification form with his data
        addImportedComponent(verif_number, verif_name, verif_nonComplianceLimit, verif_expectedResult, verif_description, verif_periodicity,
                             verif_symbolPeriodicity, verif_requiredSkill, verif_verifAcceptanceAuthority, verif_protocol,
                             verif_className, verif_validate, id, verif_reformDate, verif_reformBy, verif_puttingIntoService, verif_preventiveOperation, verif_mesureUncert, verif_mesureRange) {
            this.components.push({
                comp: 'MMEVerificationForm',
                key: this.uniqueKey++,
                number: verif_number,
                name: verif_name,
                nonComplianceLimit: verif_nonComplianceLimit,
                expectedResult: verif_expectedResult,
                description: verif_description,
                periodicity: verif_periodicity,
                symbolPeriodicity: verif_symbolPeriodicity,
                requiredSkill: verif_requiredSkill,
                verifAcceptanceAuthority: verif_verifAcceptanceAuthority,
                protocol: verif_protocol,
                className: verif_className,
                validate: verif_validate,
                id: id,
                reformDate: verif_reformDate,
                reformBy: verif_reformBy,
                puttingIntoService: verif_puttingIntoService,
                preventiveOperation: verif_preventiveOperation,
                mesureUncert: verif_mesureUncert,
                mesureRange: verif_mesureRange,
            });
        },
        //Suppression of a verification component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported verifications
        importVerif() {
            if (this.verifs.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const verif of this.verifs) {
                    var className = "importedVerif" + verif.id
                    this.addImportedComponent(verif.verif_number, verif.verif_name, verif.verif_nonComplianceLimit, verif.verif_expectedResult, verif.verif_description, verif.verif_periodicity,
                        verif.verif_symbolPeriodicity, verif.verif_requiredSkill, verif.verif_verifAcceptanceAuthority, verif.verif_protocol,
                        verif.verif_className, verif.verif_validate, verif.id, verif.verif_reformDate, verif.verif_reformBy, verif.verif_puttingIntoService, verif.verif_preventiveOperation, verif.verif_mesureUncert, verif.verif_mesureRange);
                }
            }
            this.verifs = null
        },
        //Function for saving all the data in one time
        saveAll(savedAs) {
            for (const component of this.$refs.ask_verif_data) {
                //If the user is in modification mode
                if (this.modifMod == true) {
                    //If the verification doesn't have, an id
                    if (component.verif_id == null) {
                        //AddequipmentVerif is used
                        component.addMmeVerif(savedAs);
                    } else
                        //Else if the verification has an id and addSucces, is equal to true
                    if (component.verif_id != null || component.addSucces == true) {
                        //updateMmeVerif is used
                        if (component.verif_validate !== "validated") {
                            component.updateMmeVerif(savedAs);
                        }
                    }
                } else {
                    //Else If the user is not in modification mode
                    component.addEquipmentVerif(savedAs); // FIXME: addEquipmentVerif is not defined
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        //If the user chooses importation equipment
        if (this.import_id !== null) {
            //Make a get request to ask the controller the verification corresponding to the id of the equipment with which data will be imported
            const consultUrl = (id) => `/verifs/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => this.verifs = response.data).catch(error => {
            });
        }
    },
    mounted() {
        //If the user is in consultation or modification mode, verifications will be added to the vue automatically
        if (this.consultMod || this.modifMod) {
            this.importVerif();
        }
    }
}
</script>

<style>

</style>

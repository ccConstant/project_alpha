<!--File name :ReferenceAMmeList.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a MME list in the equipment-->

<template>
    <div class="equipmentMme">
        <h2 class="titleForm">Equipment Mme(s)</h2>
        <!--Adding to the vue EquipmentMmeForm by going through the components array with the v-for-->
        <!--ref="ask_mme_data" is used to call the child elements in this component-->
        <!--The emitted deleteMme is caught here and call the function getContent -->
        <EquipmentMmeForm v-for="(component, key) in components" :id="component.id" :key="component.key"
                          ref="ask_mme_data"
                          :consultMod="isInConsultMod" :divClass="component.className"
                          :eq_id="data_eq_id" :internalRef="component.internalRef" :modifMod="isInModifMod"
                          :validate="component.validate"
                          @deleteMme="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If dimensions array is not empty and if the user is not in modification mode -->
        </div>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
    </div>


</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentMmeForm from './EquipmentMmeListForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentMmeForm,
        SaveButtonForm,
    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present, the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        eq_id: ID of the equipment in which the dimension will be added
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
        eq_id: {
            type: Number
        },
        importedMme: {
            type: Array,
            default: null
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
        isInModifMod: data of the modifMod prop
        data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_eq_id: this.eq_id,
            all_dim_validate: [],
            mmes: this.importedMme,

        };
    },
    methods: {
        /*Function for adding a new empty mme form*/
        addComponent() {
            this.components.push({
                comp: 'EquipmentMmeForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported mme form with his data*/
        addImportedComponent(mme_internalReference) {
            console.log("hello12");
            this.components.push({
                comp: 'EquipmentMmeForm',
                key: this.uniqueKey++,
                internalRef: mme_internalReference,
            });
        },
        /*Function for adding to the vue the imported mmes*/
        importMme() {
            if (this.mmes.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const mme of this.mmes) {
                    this.addImportedComponent(mme.mme_internalReference);
                }
                this.mmes = null
            }
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },

        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_mme_data) {
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    /*If the mme doesn't have an id*/
                    if (component.mme_id == null) {
                        /*AddequipmentDim is used*/
                        component.addEquipmentMme(savedAs);
                    } else
                        /*Else if the mme have an id and addSucces is equal to true*/
                    if (component.mme_id != null || component.addSucces == true) {
                        /*updateEquipmentMme is used*/
                        if (component.mme_validate !== "validated") {
                            component.updateEquipmentMme(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentMme(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the file corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/mme/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.mmes = response.data
                }).catch(error => {
            });
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        if (this.consultMod || this.modifMod) {
            this.importMme();
        }
    }
}
</script>

<style>

</style>

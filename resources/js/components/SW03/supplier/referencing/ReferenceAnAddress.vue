<!--File name :ReferenceAStorageCondition.vue-->
<!--Creation date : 27 Apr 2023 -->
<!--Update date : 27 Apr 2023-->
<!--Vue Component used to reference a storage condition in the article-->

<template>
    <div class="address">
        <h2 class="titleForm">Supplier's Address</h2>
        <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
        <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
        <!--ref="ask_dim_data" is used to call the child elements in this component-->
        <!--The emitted deleteDim is caught here and call the function getContent -->
        <AddressIDForm
            ref="askAddress"
            v-for="(component, key) in components"
            :key="component.key"
            :is="component.comp"
            :name="component.name"
            :street="component.street"
            :town="component.town"
            :country="component.country"
            :principal="component.principal"
            :validated="component.validate"
            :id="component.id"
            :consultMod="isInConsultMod"
            :modifMod="isInModifMod"
            :supplier_id="supplr_id"
            :divClass="component.className"
            @deleteAddress="getContent(key)"
        />
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add</button>
        </div>
<!--        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                        :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>-->
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'
import AddressIDForm from "./AddressIDForm.vue"
import ImportationAlert from "../../../alert/ImportationAlert.vue";


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        AddressIDForm,
        SaveButtonForm,
        InputInfo
    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedDim: All dimensions imported from the database
        eq_id: ID of the equipment in which the dimension will be added
    ---------------------------------------------------*/
    props: {
        supplier_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        importedAddress: {
            type: Array,
            default: null
        },
        divClass: {
            type: String
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_art_id: data of the art id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            title_info: null,
            address: this.importedAddress,
            supplr_id: this.supplier_id
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'AddressIDForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(addressName, addressStreet, addressTown, addressCountry, addressPrincipal, addressValidate, addressId, className) {
            this.components.push({
                comp: 'AddressIDForm',
                key: this.uniqueKey++,
                name: addressName,
                street: addressStreet,
                town: addressTown,
                country: addressCountry,
                principal: addressPrincipal,
                validate: addressValidate,
                id: addressId,
                className: className
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        importAddress() {
            if (this.address === null) {
                ImportationAlert.showAlert();
            } else {
                this.address.forEach(adr => {
                    this.addImportedComponent(
                        adr.supplrAdr_name,
                        adr.supplrAdr_street,
                        adr.supplrAdr_town,
                        adr.supplrAdr_country,
                        adr.supplrAdr_principal,
                        adr.supplrAdr_validate,
                        adr.id,
                        'importedAddress'+adr.id
                    );
                });
                this.address = null;
            }
        },

        /*Function for saving all the data in one time*/
        /*saveAll(savedAs) {
            for (const component of this.$refs.ask_dim_data) {
                /*If the user is in modification mode
                if (this.modifMod == true) {
                    /*If the dimension doesn't have, an id
                    if (component.dim_id == null) {
                        /*AddequipmentDim is used
                        component.addEquipmentDim(savedAs);
                    } else
                        /*Else if the dimension has an id and addSucces is equal to true
                    if (component.dim_id != null || component.addSucces == true) {
                        /*updateEquipmentDim is used
                        if (component.dim_validate !== "validated") {
                            component.updateEquipmentDim(savedAs);
                        }

                    }
                } else {
                    /*Else If the user is not in modification mode
                    component.addEquipmentDim(savedAs);
                }
            }
        }*/
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        if (this.import_id !== null) {
            axios.get('/supplier/address/send/'+ this.import_id)
                .then(response => {
                    this.importedAddress = response.data;
                    this.loaded = true;
                })
                .catch(error => {
                });
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        if (this.isInConsultMod || this.isInModifMod) {
            this.importAddress();
        }
    }
}
</script>

<style>

.info_title {
    position: relative;
}

.title {
    display: inline-block;
}

</style>

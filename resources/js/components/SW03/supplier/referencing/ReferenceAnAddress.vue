<!--File name : SupplierIDForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div class="supplierAddress" v-if="loaded==true">
        <h2 class="titleForm">Supplier's Address</h2>
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
            :is-in-consult-mod="isInConsultMod"
            :is-in-edit-mod="isInModifMod"
            :supplier_id="supplr_id"
            @deleteAddress="removeAddress(key)"
        />
        <div v-if="!this.isInConsultMod">
            <button v-on:click="addAddress" class="btn btn-primary">Add an address</button>
            <div v-if="this.address !== null">
                <button v-if="!isInModifMod" v-on:click="importAddress" class="btn btn-primary">Import address</button>
            </div>
        </div>
        <SaveButtonForm
            ref="saveButton"
            v-if="components.length > 1"
            @add="saveAllAddress"
            @update="saveAllAddress"
            :consultMod="this.isInConsultMod"
            :modifMod="this.isInModifMod"
        />
    </div>
</template>

<script>
import AddressIDForm from "./AddressIDForm.vue";
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import SaveButtonForm from "../../../button/SaveButtonForm.vue";

export default {
    components: {
        SaveButtonForm,
        AddressIDForm,
        ImportationAlert
    },
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
        }
    },
    data() {
        return {
            components: [],
            address: this.importedAddress,
            count: 0,
            uniqueKey: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            supplr_id: this.supplier_id,
            loaded: false
        }
    },
    methods: {
        addAddress() {
            this.components.push({
                key: this.uniqueKey++,
                comp: 'SupplierAddressForm'
            });
        },
        addImportedAddress(addressName, addressStreet, addressTown, addressCountry, addressPrincipal, addressValidate, addressId) {
            this.components.push({
                key: this.uniqueKey++,
                comp: 'SupplierAddressForm',
                name: addressName,
                street: addressStreet,
                town: addressTown,
                country: addressCountry,
                principal: addressPrincipal,
                validate: addressValidate,
                id: addressId
            });
        },
        removeAddress(key) {
            return [] === this.components.slice(key, 1);
        },
        importAddress() {
            if (this.importedAddress === null) {
                ImportationAlert.showAlert();
            } else {
                this.importedAddress.forEach(address => {
                    this.addImportedAddress(address.name, address.street, address.town, address.country, address.principal, address.validate, address.id);
                });
                this.importedAddress = null;
            }
        },
        saveAllAddress(savedAs) {
            for (const component of this.$refs.askAddress) {
                if (this.isInEditMode) {
                    if (component.adr_id === null) {
                        component.addSupplierAdr(savedAs);
                    } else {
                        if (component.validate !== 'validated') {
                            component.updateSupplierAdr(savedAs);
                        }
                    }
                } else {
                    component.addSupplierAdr(savedAs);
                }
            }
        }
    },
    created() {
        if (this.import_id !== null) {
            axios.get('/supplier/address/send/'+ this.import_id)
                .then(response => {
                    this.importedAddress = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        }
        this.loaded = true;
    },
    mounted() {
        if (this.isInConsultMode || this.isInEditMode) {
            this.importAddress();
        }
    }
}
</script>

<style>

</style>

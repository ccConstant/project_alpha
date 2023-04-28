<!--File name : SupplierIDForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div class="supplierAddress">
        <h2 class="titleForm">Supplier's Address</h2>
        <AddressIDForm
            ref="askAddress"
            v-for="(comp, key) in component"
            :key="comp.key"
            :is="comp.comp"
            :name="comp.name"
            :street="comp.street"
            :town="comp.town"
            :country="comp.country"
            :principal="comp.principal"
            :validated="comp.validate"
            :id="comp.id"
            :consultMod="isInConsultMod"
            :modifMod="isInModifMod"
            :supplier_id="supplr_id"
            :divClass="comp.className"
            @deleteAddress="removeAddress(key)"
        />
        <div v-if="!this.consultMod">
            <button v-on:click="addAddress">Add</button>
            <div v-if="this.importedAddress!==null">
                <button v-if="!isInModifMod " v-on:click="importAddress">import</button>
            </div>
        </div>
        <SaveButtonForm
            saveAll
            v-if="component.length>1"
            @add="saveAllAddress"
            @update="saveAllAddress"
            :consultMod="this.isInConsultMod"
            :modifMod="this.isInModifMod"
        />
        <ImportationAlert ref="importAlert"/>
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
        },
        divClass: {
            type: String
        }
    },
    data() {
        return {
            component: [],
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
            this.component.push({
                key: this.uniqueKey++,
                comp: 'AddressIDForm'
            });
        },
        addImportedAddress(addressName, addressStreet, addressTown, addressCountry, addressPrincipal, addressValidate, addressId, className) {
            this.component.push({
                key: this.uniqueKey++,
                comp: 'AddressIDForm',
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
        removeAddress(key) {
            this.component.slice(key, 1);
        },
        importAddress() {
            if (this.address === null) {
                ImportationAlert.showAlert();
            } else {
                this.address.forEach(adr => {
                    this.addImportedAddress(adr.supplrAdr_name, adr.supplrAdr_street, adr.supplrAdr_town, adr.supplrAdr_country, adr.supplrAdr_principal, adr.supplrAdr_validate, adr.id, 'importedAddress'+adr.id);
                });
                this.address = null;
            }
        },
        saveAllAddress(savedAs) {
            for (const component of this.$refs.askAddress) {
                if (this.isInModifMod) {
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
        if (this.isInConsultMod || this.isInModifMod) {
            this.importAddress();
        }
    }
}
</script>

<style>

</style>

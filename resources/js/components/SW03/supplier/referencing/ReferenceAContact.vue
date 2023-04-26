<!--File name : SupplierIDForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div class="supplierContact" v-if="loaded==true">
        <h2 class="titleForm">Supplier's Contact(s)</h2>
        <ContactIDForm
            ref="askContact"
            v-for="(component, key) in components"
            :key="component.key"
            :is="component.comp"
            :name="component.name"
            :functiun="component.function"
            :email="component.email"
            :phoneNumber="component.phoneNumber"
            :principal="component.principal"
            :validated="component.validate"
            :id="component.id"
            :is-in-consult-mod="isInConsultMod"
            :is-in-edit-mod="isInModifMod"
            :supplier_id="supplr_id"
            @deleteContact="removeContact(key)"
        />
        <SaveButtonForm
            ref="saveButton"
            v-if="components.length >= 1"
            @add="saveAllContact"
            @update="saveAllContact"
            :consultMod="this.isInConsultMod"
            :modifMod="this.isInModifMod"
        />
    </div>
</template>

<script>
import ContactIDForm from "./ContactIDForm.vue";
import ImportationAlert from "../../../alert/ImportationAlert.vue";
import SaveButtonForm from "../../../button/SaveButtonForm.vue";
export default {
    components: {
        SaveButtonForm,
        ContactIDForm
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
        importedContact: {
            type: Array,
            default: null
        }
    },
    data() {
        return {
            supplr_id: this.supplier_id,
            components: [],
            count: 0,
            uniqueKey: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            import_id: this.import_id,
            importedContact: this.importedContact,
            loaded: false
        };
    },
    methods: {
        addContact() {
            this.components.push({
                comp: "ContactIDForm",
                key: this.uniqueKey++,
            });
        },
        addImportedContact(contactName, contactFunction, contactEmail, contactPhoneNumber, contactPrincipal, contactId, contactValidate) {
            this.components.push({
                comp: "ContactIDForm",
                key: this.uniqueKey++,
                name: contactName,
                function: contactFunction,
                email: contactEmail,
                phoneNumber: contactPhoneNumber,
                principal: contactPrincipal,
                id: contactId,
                validate: contactValidate
            });
        },
        removeContact(key) {
            this.components.splice(key, 1);
        },
        importContact() {
            if (this.importedContact === null) {
                ImportationAlert.showAlert();
            } else {
                this.importedContact.forEach(contact => {
                    this.addImportedContact(contact.name, contact.function, contact.email, contact.phoneNumber, contact.principal, contact.id, contact.validate);
                });
                this.importedContact = null;
            }
        },
        saveAllContact(savedAs) {
            for (const component of this.$refs.askContact) {
                if (this.isInModifMod) {
                    if (component.contact_id === null) {
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
            axios.get('/supplier/contact/send/' + this.import_id)
                .then(response => {
                    this.importedContact = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        }
        this.loaded = true;
    },
    mounted() {
        if (this.isInConsultMod || this.isInModifMod) {
            this.importContact();
        }
    }
}
</script>

<style scoped>

</style>

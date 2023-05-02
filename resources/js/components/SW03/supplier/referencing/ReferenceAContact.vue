<!--File name : SupplierIDForm.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the supplier who call all the input component and send the data to the controllers-->

<template>
    <div :class="divClass">
        <div v-if="loaded === false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded === true">
            <h2 class="titleForm">Supplier's Contact(s)</h2>
            <ContactIDForm
                ref="askContact"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :name="component.name"
                :function="component.function"
                :email="component.email"
                :phoneNumber="component.phoneNumber"
                :principal="component.principal"
                :validated="component.validate"
                :id="component.id"
                :consult-mod="isInConsultMod"
                :modif-mod="isInModifMod"
                :supplier_id="supplr_id"
                :divClass="component.className"
                @deleteContact="removeContact(key)"
            />
            <div v-if="!this.consultMod">
                <button v-on:click="addContact">Add</button>
                <div v-if="this.importedContact!==null">
                    <button v-if="!modifMod " v-on:click="importContact">import</button>
                </div>
            </div>
            <SaveButtonForm
                saveAll
                v-if="components.length > 1"
                @add="saveAllContact"
                @update="saveAllContact"
                :consultMod="this.isInConsultMod"
                :modifMod="this.isInModifMod"
            />
            <ImportationAlert ref="importAlert"/>
        </div>
    </div>
</template>

<script>
import ContactIDForm from "./ContactIDForm.vue";
import ImportationAlert from "../../../alert/ImportationAlert.vue";
import SaveButtonForm from "../../../button/SaveButtonForm.vue";
export default {
    components: {
        SaveButtonForm,
        ContactIDForm,
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
        importedContact: {
            type: Array,
            default: null
        },
        divClass: {
            type: String,
            default: "supplierContact"
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
            import_ID: this.import_id,
            contacts: this.importedContact,
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
        addImportedContact(contactName, contactFunction, contactEmail, contactPhoneNumber, contactPrincipal, contactId, contactValidate, className) {
            console.log(contactName);
            this.components.push({
                comp: "ContactIDForm",
                key: this.uniqueKey++,
                name: contactName,
                function: contactFunction,
                email: contactEmail,
                phoneNumber: contactPhoneNumber,
                principal: contactPrincipal,
                id: contactId,
                validate: contactValidate,
                className: className
            });
        },
        removeContact(key) {
            this.components.splice(key, 1);
        },
        importContact() {
            if (this.contacts === null) {
                ImportationAlert.showAlert();
            } else {
                this.contacts.forEach(contact => {
                    this.addImportedContact(
                        contact.supplrContact_name,
                        contact.supplrContact_function,
                        contact.supplrContact_email,
                        contact.supplrContact_phoneNumber,
                        contact.supplrContact_principal,
                        contact.id,
                        contact.supplrContact_validate,
                        "importedContact"+contact.id
                    );
                });
                this.contacts = null;
            }
        },
        saveAllContact(savedAs) {
            for (const component of this.$refs.askContact) {
                if (this.isInModifMod) {
                    if (component.contact_id === null) {
                        component.addSupplierContact(savedAs);
                    } else {
                        if (component.validate !== 'validated') {
                            component.updateSupplierContact(savedAs);
                        }
                    }
                } else {
                    component.addSupplierContact(savedAs);
                }
            }
        }
    },
    created() {
        if (this.import_ID !== null) {
            axios.get('/supplier/contact/send/' + this.import_ID)
                .then(response => {
                    this.importedContact = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        }
        this.loaded = true;
        console.log(this.contacts);
        console.log(this.components.length)
    },
    mounted() {
        if (this.isInConsultMod || this.isInModifMod) {
            console.log("import");
            this.importContact();
        }
        console.log(this.components.length)
    }
}
</script>

<style>

</style>

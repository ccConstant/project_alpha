<template>
    <div class="supplierContact" v-if="loaded==true">
        <InputTextForm
            label="Name"
            :isRequired="true"
            v-model="supplrContact_name"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrContact_name"
            name="name"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Function"
            :isRequired="true"
            v-model="supplrContact_function"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrContact_function"
            name="function"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Phone Numbers"
            :isRequired="true"
            v-model="supplrContact_phoneNumber"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrContact_phoneNumber"
            name="phoneNumbers"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Email"
            :isRequired="true"
            v-model="supplrContact_email"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrContact_email"
            name="email"
            :min="min"
            :max="max"
        />
        <RadioGroupForm
            :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
            :isRequired="true"
            v-model="supplrContact_principal"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrContact_principal"
            label="Principal"
            :checked-option="supplrContact_principal"
        />
        <SaveButtonForm
            ref="saveButton"
            v-if="this.addSuccess===false"
            @add="addSupplierAdr"
            @update="updateSupplierAdr"
            :consultMod="this.consultMod"
            :modifMod="this.modifMod"
            :savedAs="validate"
        />
    </div>
</template>

<script>
import InputTextForm from "../../../input/SW03/InputTextForm.vue";
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";
import SaveButtonForm from "../../../button/SaveButtonForm.vue";

export default {
    name: "ReferenceAContact",
    components: {
        SaveButtonForm,
        RadioGroupForm,
        InputTextForm
    },
    props: {
        name: {
            type: String,
            default: null
        },
        contactFunction: {
            type: String,
            default: null
        },
        phoneNumbers: {
            type: String,
            default: null
        },
        email: {
            type: String,
            default: null
        },
        principal: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: "Nom non renseigné"
        },
        isRequired: {
            type: Boolean,
            default: false
        },
        value: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String
        },
        info_text: {
            type: String,
            default: null
        },
        inputClassName: {
            type: [String, Array]
        },
        divClassName: {
            type: String
        },
        isDisabled: {
            type: Boolean,
            default: false
        },
        min: {
            type: Number,
            default: 3
        },
        max: {
            type: Number,
            default: 255
        },
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
        id: {
            type: Number,
            default: null
        },
        validated: {
            type: String
        }
    },
    data() {
        return {
            addSuccess: false,
            Errors: [],
            validate: this.validated,
            supplrContact_name: this.name,
            supplrContact_function: this.contactFunction,
            supplrContact_phoneNumber: this.phoneNumbers,
            supplrContact_email: this.email,
            supplrContact_principal: this.principal,
            supplr_id: this.supplier_id,
            contact_id: this.id,
            loaded: false,
        }
    },
    created() {
        this.loaded = true;
    },
    methods: {
        addSupplierAdr(savedAs) {
            console.log('add');
            console.log(savedAs);
            console.log(this.supplrContact_name);
            console.log(this.supplrContact_function);
            console.log(this.supplrContact_phoneNumber);
            console.log(this.supplrContact_email);
            console.log(this.supplrContact_principal);
            console.log(this.supplr_id);
            axios.post('/supplier/contact/verif', {
                supplrContact_validate: savedAs,
                supplrContact_name: this.supplrContact_name,
                supplrContact_function: this.supplrContact_function,
                supplrContact_phoneNumber: this.supplrContact_phoneNumber,
                supplrContact_email: this.supplrContact_email,
                supplrContact_principal: this.supplrContact_principal,
                supplr_id: this.supplier_id,
            }).then(response => {
                this.errors = [];
                axios.post('/supplier/contact/add', {
                    supplrContact_validate: savedAs,
                    supplrContact_name: this.supplrContact_name,
                    supplrContact_function: this.supplrContact_function,
                    supplrContact_phoneNumber: this.supplrContact_phoneNumber,
                    supplrContact_email: this.supplrContact_email,
                    supplrContact_principal: this.supplrContact_principal,
                    supplr_id: this.supplier_id,
                }).catch(error => {
                    console.log('error add');
                    this.errors = error.response.data.errors;
                }).then(response => {
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
                console.log('error verif');
                this.errors = error.response.data.errors;
            });
        },
        updateSupplierAdr(savedAs, reason, lifeSheetExist) {

        },
    },
}
</script>

<style scoped>

</style>

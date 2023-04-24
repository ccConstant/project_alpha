<template>
    <div>
        <InputTextForm
            label="Name"
            :isRequired="true"
            :value="name"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            name="name"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Function"
            :isRequired="true"
            :value="contactFunction"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            name="function"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Phone Numbers"
            :isRequired="true"
            :value="phoneNumbers"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            name="phoneNumbers"
            :min="min"
            :max="max"
        />
        <InputTextForm
            label="Email"
            :isRequired="true"
            :value="email"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            name="email"
            :min="min"
            :max="max"
        />
        <RadioGroupForm
            :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
            :isRequired="true"
            :value="principal"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            label="Principal"
            :checked-option="true"
        />
        <SaveButtonForm
            ref="saveButton"
            v-if="this.addSuccess===false"
            @add="addSupplierAdr"
            @update="updateSupplierAdr"
            :consultMod="this.isInConsultMod"
            :modifMod="this.isInEditMod"
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
            type: String
        },
        contactFunction: {
            type: String
        },
        phoneNumbers: {
            type: String
        },
        email: {
            type: String
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
            type: Number,
            default: null
        },
        import_id: {
            type: Number,
            default: null
        },
        isInConsultMod: {
            type: Boolean,
            default: false
        },
        isInEditMod: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            addSuccess: false,
            Errors: [],
            validate: ''
        }
    },
    methods: {
        addSupplierAdr(savedAs) {

        },
        updateSupplierAdr(savedAs, reason, lifeSheetExist) {

        },

    }
}
</script>

<style scoped>

</style>

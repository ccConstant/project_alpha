<template>
    <div class="supplierAdr">
        <InputTextForm
            name="street"
            label="Street"
            :isRequired="true"
            :value="street"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
        />
        <InputTextForm
            name="town"
            label="Town"
            :isRequired="true"
            :value="town"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
        />
        <InputTextForm
            name="country"
            label="Country"
            :isRequired="true"
            :value="country"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
        />
        <InputTextForm
            name="Name"
            label="Name"
            :isRequired="true"
            :value="name"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
        />
        <RadioGroupForm
            :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
            :isRequired="true"
            :value="principal"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors"
            name="principal"
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
import InputInfo from "../../../input/InputInfo.vue";

export default {
    components: {
        InputInfo,
        SaveButtonForm,
        RadioGroupForm,
        InputTextForm
    },
    props: {
        street: {
            type: String,
            default: ""
        },
        town: {
            type: String,
            default: ""
        },
        country: {
            type: String,
            default: ""
        },
        ID: {
            type: Number,
            default: null
        },
        name: {
            type: String,
            default: ""
        },
        principal: {
            type: Boolean,
            default: false
        },
        isDisabled: {
            type: Boolean,
            default: false
        },
        isInConsultMod: {
            type: Boolean,
            default: false
        },
        isInEditMod: {
            type: Boolean,
            default: false
        },
        supplier_id: {
            type: Number,
            default: null
        },
        import_id: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            addSuccess: false,
            updateSuccess: false,
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

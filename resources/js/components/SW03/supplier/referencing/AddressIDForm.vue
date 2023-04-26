<template>
    <div class="supplierAdr" v-if="loaded==true">
        <InputTextForm
            name="street"
            label="Street"
            :isRequired="true"
            v-model="supplrAdr_street"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrAdr_street"
            :min="min"
            :max="max"
        />
        <InputTextForm
            name="town"
            label="Town"
            :isRequired="true"
            v-model="supplrAdr_town"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrAdr_town"
            :min="min"
            :max="max"
        />
        <InputTextForm
            name="country"
            label="Country"
            :isRequired="true"
            v-model="supplrAdr_country"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrAdr_country"
            :min="min"
            :max="max"
        />
        <InputTextForm
            name="Name"
            label="Name"
            :isRequired="true"
            v-model="supplrAdr_name"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrAdr_name"
            :min="min"
            :max="max"
        />
        <RadioGroupForm
            :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
            :isRequired="true"
            v-model="supplrAdr_principal"
            :info_text="null"
            :inputClassName="null"
            :isDisabled="isDisabled"
            :Errors="Errors.supplrAdr_principal"
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
            default: null
        },
        town: {
            type: String,
            default: null
        },
        country: {
            type: String,
            default: null
        },
        name: {
            type: String,
            default: null
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
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        min: {
            type: Number,
            default: 3
        },
        max: {
            type: Number,
            default: 255
        },
        id: {
            type: Number,
            default: null
        },
        validated: {
            type: String
        },
    },
    data() {
        return {
            addSuccess: false,
            updateSuccess: false,
            Errors: [],
            validate: this.validated,
            supplrAdr_name: this.name,
            supplrAdr_street: this.street,
            supplrAdr_town: this.town,
            supplrAdr_country: this.country,
            supplrAdr_principal: this.principal,
            adr_id: this.id,
            loaded: false,
        }
    },
    methods: {
        addSupplierAdr(savedAs) {
            console.log('add');
            console.log(savedAs);
            console.log(this.supplrAdr_street);
            console.log(this.supplrAdr_town);
            console.log(this.supplrAdr_country);
            console.log(this.supplrAdr_name);
            console.log(this.supplrAdr_principal);
            console.log('coucou' + this.supplier_id);
            axios.post('/supplier/adr/verif', {
                supplrAdr_validate: savedAs,
                supplrAdr_street: this.supplrAdr_street,
                supplrAdr_town: this.supplrAdr_town,
                supplrAdr_country: this.supplrAdr_country,
                supplrAdr_name: this.supplrAdr_name,
                supplrAdr_principal: this.supplrAdr_principal,
                supplr_id: this.supplier_id,
            }).then(response => {
                console.log('response verif');
                this.Errors = [];
                axios.post('/supplier/adr/add', {
                    supplrAdr_validate: savedAs,
                    supplrAdr_street: this.supplrAdr_street,
                    supplrAdr_town: this.supplrAdr_town,
                    supplrAdr_country: this.supplrAdr_country,
                    supplrAdr_name: this.supplrAdr_name,
                    supplrAdr_principal: this.supplrAdr_principal,
                    supplr_id: this.supplier_id,
                }).catch(error => {
                    console.log('error add');
                    console.log(error);
                    this.Errors = error.response.data.errors;
                }).then(response => {
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
                console.log('error verif');
                this.Errors = error.response.data.errors;
            });
        },
        updateSupplierAdr(savedAs, reason, lifeSheetExist) {

        },

    },
    created() {
        this.loaded = true;
    }

}
</script>

<style scoped>

</style>

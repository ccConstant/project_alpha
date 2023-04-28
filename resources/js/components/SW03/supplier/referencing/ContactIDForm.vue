<template>
    <div :class="divClass">
        <div v-if="loaded==true">
            <form>
                <InputTextForm
                    label="Name"
                    isRequired
                    v-model="supplrContact_name"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :Errors="Errors.supplrContact_name"
                    name="name"
                    :min="2"
                    :max="255"
                />
                <InputTextForm
                    label="Function"
                    v-model="supplrContact_function"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :Errors="Errors.supplrContact_function"
                    name="function"
                    :min="2"
                    :max="255"
                />
                <InputTextForm
                    label="Phone Numbers"
                    v-model="supplrContact_phoneNumber"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :Errors="Errors.supplrContact_phoneNumber"
                    name="phoneNumbers"
                    :min="10"
                    :max="30"
                />
                <InputTextForm
                    label="Email"
                    v-model="supplrContact_email"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :Errors="Errors.supplrContact_email"
                    name="email"
                    :min="2"
                    :max="255"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    :isRequired="true"
                    v-model="supplrContact_principal"
                    :info_text="null"
                    :inputClassName="null"
                    :isDisabled="isInConsultMod"
                    :Errors="Errors.supplrContact_principal"
                    label="Principal"
                    :checked-option="supplrContact_principal"
                />
                <SaveButtonForm
                    ref="saveButton"
                    v-if="this.addSuccess===false"
                    @add="addSupplierContact"
                    @update="updateSupplierContact"
                    :consultMod="this.consultMod"
                    :modifMod="this.modifMod"
                    :savedAs="validate"
                />
                <div v-if="this.addSuccess==false ">
                    <!--If this file doesn't have a id the addEquipmentFile is called function else the updateEquipmentFile function is called -->
                    <div v-if="this.contact_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addSupplierContact" @update="updateSupplierContact"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addSupplierContact" @update="updateSupplierContact"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.contact_id!==null">
                        <SaveButtonForm @add="addSupplierContact" @update="updateSupplierContact"
                                        :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                                        :savedAs="validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <!--            <DeleteComponentButton :validationMode="validate" :consultMod="this.isInConsultMod"
                                                       @deleteOk="deleteContact"/>-->
                </div>
            </form>
        </div>
        <div v-else>
            <b-spinner variant="primary"></b-spinner>
        </div>
    </div>
</template>

<script>
import InputTextForm from "../../../input/SW03/InputTextForm.vue";
import RadioGroupForm from "../../../input/SW03/RadioGroupForm.vue";
import SaveButtonForm from "../../../button/SaveButtonForm.vue";
import DeleteComponentButton from "../../../button/DeleteComponentButton.vue";

export default {
    components: {
        DeleteComponentButton,
        SaveButtonForm,
        RadioGroupForm,
        InputTextForm
    },
    props: {
        name: {
            type: String,
            default: null
        },
        function: {
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
        },
        divClass: {
            type: String
        }
    },
    data() {
        return {
            addSuccess: false,
            Errors: [],
            validate: this.validated,
            supplrContact_name: this.name,
            supplrContact_function: this.function,
            supplrContact_phoneNumber: this.phoneNumbers,
            supplrContact_email: this.email,
            supplrContact_principal: this.principal,
            supplr_id: this.supplier_id,
            contact_id: this.id,
            loaded: false,
            isInConsultMod: this.consultMod,
        }
    },
    created() {
        this.loaded = true;
        console.log(this.supplrContact_name);
    },
    methods: {
        addSupplierContact(savedAs) {
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
                    this.errors = error.response.data.errors;
                }).then(response => {
                    this.$snotify.success('Supplier\'s contact is correctly added in the database as ' + savedAs);
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
                this.errors = error.response.data.errors;
            });
        },
        updateSupplierContact(savedAs, reason, lifeSheetExist) {

        },
    },
}
</script>

<style>

</style>

<template>
    <div :class="divClass">
        <div v-if="loaded==true">
            <form>
                <InputTextForm
                    name="street"
                    label="Street"
                    v-model="supplrAdr_street"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_street"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="town"
                    label="Town"
                    v-model="supplrAdr_town"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_town"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="country"
                    label="Country"
                    v-model="supplrAdr_country"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_country"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <InputTextForm
                    name="Name"
                    label="Name"
                    isRequired
                    v-model="supplrAdr_name"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_name"
                    :min="2"
                    :max="255"
                    :isDisabled="isInConsultMod"
                />
                <RadioGroupForm
                    :options="[{value: true, text: 'Yes'}, {value: false, text: 'No'}]"
                    isRequired
                    v-model="supplrAdr_principal"
                    :info_text="null"
                    :inputClassName="null"
                    :Errors="Errors.supplrAdr_principal"
                    name="principal"
                    label="Principal"
                    :checked-option="true"
                    :isDisabled="isInConsultMod"
                />
                <SaveButtonForm
                    ref="saveButton"
                    v-if="this.addSuccess===false"
                    @add="addSupplierAdr"
                    @update="updateSupplierAdr"
                    :consultMod="this.isInConsultMod"
                    :modifMod="this.modifMod"
                    :savedAs="validate"
                />
                <div v-if="this.addSuccess==false ">
                    <!--If this file doesn't have a id the addEquipmentFile is called function else the updateEquipmentFile function is called -->
                    <div v-if="this.adr_id==null ">
                        <div v-if="modifMod==true">
                            <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"
                                            :AddinUpdate="true"/>
                        </div>
                        <div v-else>
                            <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                            :consultMod="this.isInConsultMod" :savedAs="validate"/>
                        </div>
                    </div>
                    <div v-else-if="this.adr_id!==null">
                        <SaveButtonForm @add="addSupplierAdr" @update="updateSupplierAdr"
                                        :consultMod="this.isInConsultMod" :modifMod="this.modifMod"
                                        :savedAs="validate"/>
                    </div>
                    <!-- If the user is not in the consultation mode, the delete button appear -->
                    <!--            <DeleteComponentButton :validationMode="validate" :consultMod="this.isInConsultMod"
                                                       @deleteOk="deleteContact"/>-->
                </div>
            </form>
            <SuccessAlert ref="successAlert"/>
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
import InputInfo from "../../../input/InputInfo.vue";
import SuccessAlert from "../../../alert/SuccesAlert.vue";

export default {
    components: {
        SuccessAlert,
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
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
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
        validate: {
            type: String
        },
        divClass: {
            type: String
        }
    },
    data() {
        return {
            addSuccess: false,
            updateSuccess: false,
            Errors: [],
            supplrAdr_name: this.name,
            supplrAdr_street: this.street,
            supplrAdr_town: this.town,
            supplrAdr_country: this.country,
            supplrAdr_principal: this.principal,
            adr_id: this.id,
            loaded: false,
            isInConsultMod: this.consultMod,
        }
    },
    methods: {
        addSupplierAdr(savedAs) {
            axios.post('/supplier/adr/verif', {
                supplrAdr_validate: savedAs,
                supplrAdr_street: this.supplrAdr_street,
                supplrAdr_town: this.supplrAdr_town,
                supplrAdr_country: this.supplrAdr_country,
                supplrAdr_name: this.supplrAdr_name,
                supplrAdr_principal: this.supplrAdr_principal,
                supplr_id: this.supplier_id,
            }).then(response => {
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
                    this.Errors = error.response.data.errors;
                }).then(response => {
                    this.$snotify.success('Supplier\'s address is correctly added in the database as ' + savedAs);
                    this.addSuccess = true;
                    this.isInConsultMod = true;
                });
            }).catch(error => {
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

<style>

</style>

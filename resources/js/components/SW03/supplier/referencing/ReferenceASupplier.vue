<!--File name :ReferenceASupplier.vue-->
<!--Creation date : 21 Apr 2023-->
<!--Update date : 21 Apr 2023-->
<!--Vue Component used to reference a new supplier-->

<template>
    <div>
        <SupplierIDForm @SupplierID="put_supplier_id" @importFromSupplierID="put_import_id"/>
        <div class="accordion" v-if="supplier_id != null">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Supplier's Address
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <ReferenceAnAddress :supplier_id="this.supplier_id" :import_id="this.importation_id"/>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        Supplier's Contact
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                    <div class="accordion-body">
                        <ReferenceAContact :supplier_id="this.supplier_id" :import_id="this.importation_id"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SupplierIDForm from "./SupplierIDForm.vue";
import ReferenceAnAddress from "./AddressIDForm.vue";
import ReferenceAContact from "./ContactIDForm.vue";


export default {
    components: {
        ReferenceAContact,
        SupplierIDForm,
        ReferenceAnAddress
    },
    data() {
        return {
            supplier_id: null,
            importation_id: null,
            loaded: false
        }
    },
    methods: {
        put_supplier_id(value) {
            console.log(value);
            this.supplier_id = value;
            console.log(this.supplier_id);
        },
        put_import_id(value) {
            this.importation_id = value;
        }
    },
    created() {
        this.loaded = true;
    }
}
</script>

<style>
</style>


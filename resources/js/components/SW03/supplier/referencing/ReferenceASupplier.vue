<!--File name :ReferenceASupplier.vue-->
<!--Creation date : 21 Apr 2023-->
<!--Update date : 21 Apr 2023-->
<!--Vue Component used to reference a new supplier-->

<template>
    <div>
        <SupplierIDForm @SupplierID="put_supplier_id" @importFromSupplierID="put_import_id"/>
        <div class="accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Supplier's Address
                    </button>
                </h2>
                <!--<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <ReferenceAnAddress :supplier_id="this.supplier_id" :import_id="this.importation_id"/>
                    </div>
                </div>-->
            </div>
            <!--<div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Supplier's Contact
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <ReferenceAContact :supplier_id="this.supplier_id" :import_id="this.importation_id"/>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
</template>

<script>
import SupplierIDForm from "./SupplierIDForm.vue";
import ReferenceAnAddress from "./ReferenceAnAddress.vue";
import ReferenceADim from "../../../SW01/equipment/referencing/ReferenceADim.vue";
import ReferenceAContact from "./ReferenceAContact.vue";


export default {
    components: {
        ReferenceAContact,
        ReferenceADim,
        SupplierIDForm,
        ReferenceAnAddress
    },
    data() {
        return {
            supplier_id: null,
            importation_id: null
        }
    },
    methods: {
        put_supplier_id(value) {
            this.supplier_id = value;
        },
        put_import_id(value) {
            this.importation_id = value;
        }
    }
}
</script>

<style>
</style>


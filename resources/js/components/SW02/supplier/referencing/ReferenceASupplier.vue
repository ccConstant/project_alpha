<!--File name :ReferenceASupplier.vue-->
<!--Creation date : 21 Apr 2023-->
<!--Update date : 21 Apr 2023-->
<!--Vue Component used to reference a new supplier-->

<template>
    <div>
        <SupplierIDForm @ArticleID="put_supplier_id" @importFromArticleID="put_import_id"/>
    </div>
</template>

<script>
import SupplierIDForm from "./SupplierIDForm.vue";


export default {
    components: {
        SupplierIDForm: SupplierIDForm,
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


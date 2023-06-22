<!--File name :ImportationModal.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to show a modal, this one is used to show the list of possible importations-->

<template>
    <div>
        <b-button v-b-modal.modal-eq_add_import @click="importFromDB">Import From</b-button>
        <b-modal id="modal-eq_add_import" hide-footer title="Importation">
            <p class="my-4">Import from set : {{ set }}</p>
            <div>
                <div v-if="imported_eq_ref.length>0">
                    <input v-model="searchTerm" type="text">
                    <div v-for="option in filterByTerm" :key="option.id">
                        <input :id="option.eq_internalReference" v-model="radio_value" :value="option" name="radio-input"
                               type="radio"/>
                        {{ option.eq_internalReference }}
                    </div>
                </div>
                <div v-else>
                    <p>Nothing to import From</p>
                </div>
            </div>
            <b-button block class="mt-3" @click="$bvModal.hide('modal-eq_add_import')">Close</b-button>
            <b-button block class="mt-3" @click="chooseEquipment">Choose</b-button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        set: {
            type: String
        }
    },
    data() {
        return {
            imported_eq_ref: [],
            searchTerm: "",
            radio_value: ''
        }
    },
    computed: {
        filterByTerm() {
            return this.imported_eq_ref.filter(option => {
                return option.eq_internalReference.toLowerCase().startsWith(this.searchTerm.toLowerCase());
            });
        }
    },
    methods: {
        chooseEquipment() {
            if (this.radio_value != '') {
                this.$emit('choosedEq', this.radio_value)
            }
            this.$bvModal.hide('modal-eq_add_import')
        },
        importFromDB() {
            const importUrl = (set) => `/equipments/same_set/${set}`;
            axios.get(importUrl(this.set))
                .then(response => {
                    this.imported_eq_ref = response.data;
                }).catch(error => {
            });
        }
    }
}
</script>

<style lang="scss">
.modal-backdrop {
    opacity: 0.8;
}
</style>

<!--File name : EquipmentReform.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component of the reform of equipment-->

<template>
    <div>
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true" class="equipment_consultation">
            <EquipmentIDForm :construct="eq_idCard.eq_constructor"
                             :externalReference="eq_idCard.eq_externalReference"
                             :internalReference="eq_idCard.eq_internalReference" :mass="eq_idCard.eq_mass"
                             :massUnit="eq_idCard.eq_massUnit"
                             :mobility="eq_idCard.eq_mobility" :name="eq_idCard.eq_name"
                             :remarks="eq_idCard.eq_remarks"
                             :serialNumber="eq_idCard.eq_serialNumber" :set="eq_idCard.eq_set" :type="eq_idCard.eq_type"
                             :validate="eq_idCard.eq_validate"
                             consultMod/>
            <ReferenceAUsage v-if="eq_usg.length>0" :importedUsg="eq_usg" :reformMod="true" consultMod/>
            <ReferenceAPrvMtnOp v-if="eq_prvMtnOp.length>0" :importedPrvMtnOp="eq_prvMtnOp" :reformMod="true"
                                consultMod/>
        </div>
    </div>
</template>

<script>
import EquipmentIDForm from '../referencing/EquipmentIDForm.vue'
import ReferenceAUsage from '../referencing/ReferenceAUsage.vue'
import ReferenceAPrvMtnOp from '../referencing/ReferenceAPrvMtnOp.vue'
import ValidationButton from '../../../button/ValidationButton.vue'


export default {
    components: {
        EquipmentIDForm,
        ReferenceAUsage,
        ReferenceAPrvMtnOp,
        ValidationButton
    },
    data() {
        return {
            eq_id: this.$route.params.id.toString(),
            eq_idCard: null,
            eq_usg: null,
            eq_prvMtnOp: null,
            loaded: false,
            errors: {}
        }
    },

    created() {
        if (this.$userId.user_makeReformRight != true) {
            this.$router.replace({name: "home"})
        }
        const consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then(response => {
                this.eq_idCard = response.data;
                const consultUrlUsg = (id) => `/usage/send/${id}`;
                axios.get(consultUrlUsg(this.eq_id))
                    .then(response => {
                        this.eq_usg = response.data;
                        const consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
                        axios.get(consultUrlPrvMtnOp(this.eq_id))
                            .then(response => {
                                this.eq_prvMtnOp = response.data
                                this.loaded = true;
                            }).catch(error => {
                        });
                    }).catch(error => {
                });
            }).catch(error => {
        });
    }
}


</script>

<style lang="scss">
</style>

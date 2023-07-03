<!--File name : ReferenceAVerifRlz.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference a verification linked to the current MME-->

<template>
    <div class="MMEVerifRlz">
        <h2 class="titleForm">MME verification</h2>
        <span class="hr"></span>
        <!--Adding to the vue MMEVerifRlzForm by going through the components array with the v-for-->
        <!--ref="ask_verifRlz_data" is used to call the child elements in this component-->
        <!--The emitted deleteVerifRlz is caught here and call the function getContent -->
        <MMEVerifRlzForm :is="component.comp" v-for="(component, key) in components" :id="component.id"
                         :key="component.key" ref="ask_verifRlz_data" :comment="component.verifRlz_comment"
                         :consultMod="isInConsultMod" :divClass="component.className"
                         :endDate="component.endDate"
                         :isPassed="component.isPassed"
                         :mme_id="data_mme_id" :modifMod="isInModifMod" :number="component.number"
                         :reportNumber="component.reportNumber"
                         :startDate="component.startDate"
                         :state_id="data_state_id"
                         :validate="component.validate"
                         :verif_description_prop="component.verif_description" :verif_expectedResult_prop="component.verif_expectedResult"
                         :verif_id_prop="component.verif_id" :verif_nonComplianceLimit_prop="component.verif_nonComplianceLimit" :verif_number_prop="component.verif_number"
                         :verif_protocol_prop="component.verif_protocol"
                         @addSucces="addSucces()" @deleteVerifRlz="getContent(key)"/>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
    </div>
</template>

<script>
import MMEVerifRlzForm from './MMEVerifRlzForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        MMEVerifRlzForm,
        SaveButtonForm,
    },
    props: {
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        importedVerifRlz: {
            type: Array,
            default: null
        },
        mme_id: {
            type: Number
        },
        state_id: {
            type: Number
        },
        id: {
            type: Number
        },
        number: {
            type: Number
        }
    },
    data() {
        return {
            components: [],
            uniqueKey: 0,
            verifsRlz: this.importedVerifRlz,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_mme_id: this.mme_id,
            data_state_id: this.state_id
        }
    },
    methods: {
        addComponent() {
            this.components.push({
                comp: 'MMEVerifRlzForm',
                key: this.uniqueKey++,
                verif_number: this.number,
                verif_id: this.id,
                id: this.id,
            });
        },
        addImportedComponent(verifRlz_number, verifRlz_reportNumber, verifRlz_startDate,
                             verifRlz_endDate, verifRlz_isPassed, verifRlz_validate, verifRlz_className, id,
                             verif_id, verif_expectedResult, verif_nonComplianceLimit, verif_description, verif_number, verif_protocol, verif_comment) {
            this.components.push({
                comp: 'MMEVerifRlzForm',
                key: this.uniqueKey++,
                number: verifRlz_number,
                reportNumber: verifRlz_reportNumber,
                startDate: verifRlz_startDate,
                endDate: verifRlz_endDate,
                isPassed: verifRlz_isPassed,
                className: verifRlz_className,
                validate: verifRlz_validate,
                id: id,
                verif_id: verif_id,
                verrif_expectedResult: verif_expectedResult,
                verif_nonComplianceLimit: verif_nonComplianceLimit,
                verif_description: verif_description,
                verif_number: verif_number,
                verif_protocol: verif_protocol,
                verif_comment: verif_comment
            });
        },
        //Suppression of the verification component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        importVerifRlz() {
            for (const verifRlz of this.verifsRlz) {
                const className = "importedVerifRlz" + verifRlz.id
                this.addImportedComponent(verifRlz.verifRlz_number, verifRlz.verifRlz_reportNumber, verifRlz.verifRlz_startDate,
                    verifRlz.verifRlz_endDate, verifRlz.verifRlz_isPassed, verifRlz.verifRlz_validate, className, verifRlz.id,
                    verifRlz.verif_id, verifRlz.verif_expectedResult, verifRlz.verif_nonComplianceLimit,
                    verifRlz.verif_description, verifRlz.verif_number, verifRlz.verif_protocol, verifRlz.verifRlz_comment);
            }
            this.verifsRlz = null
        },
        //Function for saving all the data in one time
        saveAll(savedAs) {
            for (const component of this.$refs.ask_verifRlz_data) {
                //If the user is in modification mode
                if (this.modifMod == true) {
                    //If the verification doesn't have id
                    if (component.verifRlz_id == null) {
                        component.addMmeVerifRlz(savedAs);
                    } else
                        //Else if the verification have an id and addSucces is equal to true
                    if (component.verifRlz_id != null || component.addSucces == true) {
                        component.updateMmeVerifRlz(savedAs);
                    }
                } else {
                    //Else If the user is not in modification mode
                    component.addMmeVerifRlz(savedAs);
                }
            }
        },
        addSucces() {
            this.$emit('addSucces', '')
        }
    },
    mounted() {
        //If the user is in consultation or modification mode the verification will be added to the vue automatically
        if (this.consultMod || this.modifMod) {
            this.importVerifRlz();
        } else {
            this.addComponent();
        }
    },
    created() {
        if (this.$userId.user_makeMmeOpValidationRight != true) {
            this.$router.push({name: "home"});
        }
    },
}
</script>

<style>

</style>

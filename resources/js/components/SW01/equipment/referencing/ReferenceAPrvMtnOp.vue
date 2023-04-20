<!--File name :ReferenceAPrvMtnOp.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a preventive maintenance operation in the equipment-->

<template>
    <div class="equipmentPrvMtnOp">
        <h2 class="titleForm">Equipment Preventive Maintenance Operation</h2>
        <!--Adding to the vue EquipmentPrvMtnOpForm by going through the components array with the v-for-->
        <!--ref="ask_prvMtnOp_data" is used to call the child elements in this component-->
        <!--The emitted deletePrvMtnOp is caught here and call the function getContent -->
        <EquipmentPrvMtnOpForm ref="ask_prvMtnOp_data" v-for="(component, key) in components" :key="component.key"
                               :is="component.comp" :number="component.number" :description="component.description"
                               :periodicity="component.periodicity" :symbolPeriodicity="component.symbolPeriodicity"
                               :reformMod="isInReformMod"
                               :protocol="component.protocol" :divClass="component.className" :id="component.id"
                               :import_id="component.import"
                               :validate="component.validate" :consultMod="isInConsultMod" :modifMod="isInModifMod"
                               :eq_id="data_eq_id"
                               :reformDate="component.reformDate" :reformBy="component.reformBy"
                               :puttingIntoService="component.puttingIntoService"
                               :preventiveOperation="component.preventiveOperation"
                               @deletePrvMtnOp="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another preventive maintenance operation button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If preventive maintenance operation array is not empty and if the user is not in modification mode -->
            <div v-if="this.prvMtnOps!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importPrvMtnOp">import</button>
            </div>
        </div>
        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                        :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        <ImportationAlert ref="importAlert"/>
    </div>

</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentPrvMtnOpForm from './EquipmentPrvMtnOpForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentPrvMtnOpForm,
        SaveButtonForm,
        ImportationAlert


    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode we disable save button and show update button
        importedPrvMtnOp: All preventive maintenance operation imported from the database
        eq_id: ID of the equipment in which the preventive maintenance operation will be added
        import_id: ID of the equipment with which preventive maintenance operation
    ---------------------------------------------------*/
    props: {
        consultMod: {
            type: Boolean,
            default: false
        },
        modifMod: {
            type: Boolean,
            default: false
        },
        importedPrvMtnOp: {
            type: Array,
            default: null
        },
        eq_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        reformMod: {
            type: Boolean,
            default: false
        }
    },
    /*--------Declaration of the different returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    prvMtnOps: Array of all imported preventive maintenance operation
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            prvMtnOps: this.importedPrvMtnOp,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            isInReformMod: this.reformMod,
            data_eq_id: this.eq_id
        };
    },
    methods: {
        /*Function for adding a new empty preventive maintenance operation form */
        addComponent() {
            this.components.push({
                comp: 'EquipmentPrvMtnOpForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported preventive maintenance operation form with his data*/
        addImportedComponent(prvMtnOp_number, prvMtnOp_description, prvMtnOp_periodicity, prvMtnOp_symbolPeriodicity, prvMtnOp_protocol,
                             prvMtnOp_className, prvMtnOp_validate, id, prvMtnOp_reformDate, prvMtnOp_reformBy, prvMtnOp_puttingIntoService, prvMtnOp_preventiveOperation) {
            this.components.push({
                comp: 'EquipmentPrvMtnOpForm',
                key: this.uniqueKey++,
                number: prvMtnOp_number,
                description: prvMtnOp_description,
                periodicity: prvMtnOp_periodicity,
                symbolPeriodicity: prvMtnOp_symbolPeriodicity,
                protocol: prvMtnOp_protocol,
                className: prvMtnOp_className,
                validate: prvMtnOp_validate,
                import: this.import_id,
                id: id,
                reformDate: prvMtnOp_reformDate,
                reformBy: prvMtnOp_reformBy,
                puttingIntoService: prvMtnOp_puttingIntoService,
                preventiveOperation: prvMtnOp_preventiveOperation,
            });
            console.log(this.components)
        },
        /*Suppression of a preventive maintenance operation component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported preventive maintenance operation*/
        importPrvMtnOp() {
            if (this.prvMtnOps.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const prvMtnOp of this.prvMtnOps) {
                    const className = "importedPrvMtnOp" + prvMtnOp.id;
                    this.addImportedComponent(prvMtnOp.prvMtnOp_number, prvMtnOp.prvMtnOp_description, prvMtnOp.prvMtnOp_periodicity, prvMtnOp.prvMtnOp_symbolPeriodicity,
                        prvMtnOp.prvMtnOp_protocol, className, prvMtnOp.prvMtnOp_validate, prvMtnOp.id, prvMtnOp.prvMtnOp_reformDate, prvMtnOp.prvMtnOp_reformBy, prvMtnOp.prvMtnOp_puttingIntoService, prvMtnOp.prvMtnOp_preventiveOperation);
                }
            }
            this.prvMtnOps = null
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_prvMtnOp_data) {
                console.log("coucou37")
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    console.log("coucou38")
                    /*If the preventive maintenance operation doesn't have an id*/
                    if (component.prvMtnOp_id == null) {
                        /*AddequipmentPrvMtnOp is used*/
                        component.addEquipmentPrvMtnOp(savedAs);
                    } else
                        /*Else if the preventive maintenance operation has an id and if addSucces is equal to true */
                    if (component.prvMtnOp_id != null || component.addSucces == true) {
                        console.log("coucou39")
                        /*updateEquipmentPrvMtnOp is used*/
                        if (component.prvMtnOp_validate !== "validated") {
                            console.log("coucou40")
                            component.updateEquipmentPrvMtnOp(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentPrvMtnOp(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        console.log("hello1")
        console.log(this.importedPrvMtnOp)
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            console.log("not null")
            /*Make a get request to ask the controller the preventive maintenance operation corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/prvMtnOps/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.prvMtnOps = response.data
                })
                .catch(error => console.log(error));
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        console.log("hello2")
        /*If the user is in consultation or modification mode, preventive maintenance operation will be added to the vue automatically*/
        if (this.consultMod || this.modifMod) {
            console.log("hello34")
            this.importPrvMtnOp();
        }
    }
}
</script>

<style>

</style>

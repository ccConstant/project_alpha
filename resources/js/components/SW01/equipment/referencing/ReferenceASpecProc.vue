<!--File name :ReferenceASpecProc.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a special process in the equipment-->

<template>
    <div class="equipmentSpProc">
        <h2 class="titleForm">Equipment Special Process</h2>
        <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
        <!--Adding to the vue EquipmentSpecProcForm by going through the components array with the v-for-->
        <!--ref="ask_spProc_data" is used to call the child elements in this component-->
        <!--The emitted deleteSpPRoc is caught here and call the function getContent -->
        <EquipmentSpecProcForm ref="ask_spProc_data" v-for="(component) in components" :key="component.key"
                               :is="component.comp" :divClass="component.className" :id="component.id"
                               :exist="component.exist"
                               :remarksOrPrecaution="component.remarksOrPrecaution" :name="component.name"
                               :validate="component.validate"
                               :consultMod="isInConsultMod" :modifMod="isInModifMod" :eq_id="data_eq_id"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--If special process array is not empty and if the user is not in modification mode -->
            <div v-if="this.specProc!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importSpProc">import</button>
            </div>
        </div>
        <ImportationAlert ref="importAlert"/>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentSpecProcForm from './EquipmentSpecProcForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import InputInfo from '../../../input/InputInfo.vue'

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentSpecProcForm,
        ImportationAlert,
        InputInfo
    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present the form is in modification mode, we disable save button and show update button
        importedSpProc: All special process imported from the database
        eq_id: ID of the equipment in which the special process will be added
        import_id: ID of the equipment with which special process will be imported
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
        importedSpProc: {
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
    },
    /*--------Declaration of the different returned data:--------
    components: Array in which will be added the data of a component
    uniqueKey: A unique key assigned to a component
    specProc: Array of imported special process
    isInConsultedMod: data of the consultMod prop
    isInModifMod: data of the modifMod prop
    data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            specProc: this.importedSpProc,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_eq_id: this.eq_id,
            title_info: null,
        };
    },
    methods: {
        /*Function for adding an imported special process form with his data*/
        addImportedComponent(spProc_remarksOrPrecaution, spProc_name, spProc_exist, spProc_validate, spProc_className, id) {
            this.components.splice(0, 1);
            this.components.push({
                comp: 'EquipmentSpecProcForm',
                key: 1,
                remarksOrPrecaution: spProc_remarksOrPrecaution,
                name: spProc_name,
                exist: spProc_exist,
                className: spProc_className,
                validate: spProc_validate,
                id: id
            });
        },
        /*Function for adding to the vue the imported special process*/
        importSpProc() {
            if (this.specProc.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else if (this.specProc.length > 0) {
                const className = "importedSpProc" + this.specProc[0].id;
                this.addImportedComponent(this.specProc[0].spProc_remarksOrPrecaution, this.specProc[0].spProc_name, this.specProc[0].spProc_exist, this.specProc[0].spProc_validate, className, this.specProc[0].id);
            }
            this.specProc = null
        }
    },
    created() {
        /*If the user imports equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the special process corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/spProc/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => this.specProc = response.data)
                .catch(error => console.log(error));
        }
        axios.get('/info/send/specialProcess')
            .then(response => {
                this.title_info = response.data[3];
            })
            .catch(error => console.log(error));
    },
    mounted() {
        if (!this.consultMod) {
            this.components.push({
                comp: 'EquipmentSpecProcForm',
                key: this.uniqueKey++,
            });
        }
        /*If the user is in consultation or modification mode, a special process will be added to the vue automatically*/
        if (this.consultMod || this.modifMod) {
            this.importSpProc();
        }
        /*Function for adding a new empty special process form */
    }
}
</script>

<style>
    .info_title {
        position: relative;
    }
    .title {
        display: inline-block;
    }
</style>

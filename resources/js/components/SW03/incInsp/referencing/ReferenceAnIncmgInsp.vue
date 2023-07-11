<!--File name :ReferenceAFile.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to reference a file in the equipment-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="incmgInsp">
            <h2 v-if="components.length>0" class="titleForm">Incoming Inspection</h2>
            <!--Adding to the vue IncmgInspIDForm by going through the components array with the v-for-->
            <!--ref="ask_incmgInsp_data" is used to call the child elements in this component-->
            <!--The emitted deleteFile is caught here and call the function getContent -->
            <IncmgInspIDForm
                ref="ask_incmgInsp_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :remarks="component.remarks"
                :part-material-certif="component.partMaterialCertif"
                :raw-material-certif="component.rawMaterialCertif"
                :divClass="component.className"
                :id="component.id"
                :validate="component.validate"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :article_id="data_article_id"
                :article_type="data_article_type"
                @deleteFile="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another file button appear -->
                <button v-on:click="addComponent">Add Incoming Inspection</button>
                <!--If file array is not empty and if the user is not in modification mode -->
                <!--            <div v-if="this.incmgInsp!==null">
                                &lt;!&ndash;The importation button appear &ndash;&gt;
                                <button v-if="!modifMod " v-on:click="importIncmgInsp">import</button>
                            </div>-->
            </div>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import IncmgInspIDForm from "./IncmgInspIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        IncmgInspIDForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedArticle: All article imported from the database
        article_id: ID of the equipment in which the file will be added
        import_id: ID of the equipment with which article will be imported
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
        importedIncgmInsp: {
            type: Array,
            default: null
        },
        article_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        },
        articleType: {
            type: String
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        article: Array of all imported article
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_article_id: data of the article_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            incmgInsp: this.importedIncgmInsp,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_article_id: this.article_id,
            data_article_type: this.articleType === null ? 'raw' : this.articleType.toLowerCase(),
            loaded: false
        };
    },
    methods: {
        /*Function for adding a new empty file form*/
        addComponent() {
            this.components.push({
                comp: 'IncmgInspIDForm',
                key: this.uniqueKey++,
                id: null,
            });
        },
        /*Function for adding an imported file form with his data*/
        addImportedComponent(incmgInsp_remarks, incmgInsp_partMaterialCertif, incmgInsp_rawMaterialCertif, validate, id, className) {
            this.components.push({
                comp: 'IncmgInspIDForm',
                key: this.uniqueKey++,
                remarks: incmgInsp_remarks,
                partMaterialCertif: incmgInsp_partMaterialCertif,
                rawMaterialCertif: incmgInsp_rawMaterialCertif,
                validate: validate,
                id: id,
                className: className
            });
        },
        /*Suppression of a file component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported article*/
        importIncmgInsp() {
            if (this.incmgInsp.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const ii of this.incmgInsp) {
                    const className = "importedArticle" + ii.id;
                    this.addImportedComponent(
                        ii.incmgInsp_remarks,
                        ii.incmgInsp_partMaterialComplianceCertificate,
                        ii.incmgInsp_rawMaterialCertificate,
                        ii.incmgInsp_validate,
                        ii.id,
                        className);
                }
                this.incmgInsp = null
            }
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_incmgInsp_data) {
                /*If the user is in modification mode*/
                if (this.modifMod === true) {
                    /*If the file doesn't have, an id*/
                    if (component.id == null) {
                        /*AddequipmentFile is used*/
                        component.addIncmgInsp(savedAs);
                    } else
                        /*Else if the file has an id and addSucces is equal to true*/
                    if (component.id != null || component.addSucces == true) {
                        /*updateEquipmentFile is used*/
                        if (component.incmgInsp_validate !== "validated") {
                            component.updateIncmgInsp(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addIncmgInsp(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the file corresponding to the id of the equipment with which data will be imported*/
            if (this.data_article_type === 'raw') {
                axios.get('/incmgInsp/send/raw/' + this.data_article_id)
                    .then(response => {
                        this.incmgInsp = response.data;
                        this.importIncmgInsp();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            } else if (this.data_article_type === 'cons') {
                axios.get('/incmgInsp/send/cons/' + this.data_article_id)
                    .then(response => {
                        this.incmgInsp = response.data;
                        this.importIncmgInsp();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            } else if (this.data_article_type === 'comp') {
                axios.get('/incmgInsp/send/comp/' + this.data_article_id)
                    .then(response => {
                        this.incmgInsp = response.data;
                        this.importIncmgInsp();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            }
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importIncmgInsp();
        }*/
    }


}
</script>

<style>

</style>

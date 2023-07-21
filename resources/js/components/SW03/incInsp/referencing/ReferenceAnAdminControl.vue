<!--File name : ReferenceAnAdminControl.vue-->
<!--Creation date : 10 Jul 2022-->
<!--Update date : 10 Jul 2023-->
<!--Vue Component used to reference a administrative control in as incoming inspection-->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="adminControl">
            <h2 class="titleForm" v-if="components.length > 0">Additional Administrative Control</h2>
            <!--Adding to the vue adminControlIDForm by going through the components array with the v-for-->
            <!--ref="ask_adminControl_data" is used to call the child elements in this component-->
            <!--The emitted deleteFile is caught here and call the function getContent -->
            <AdminControlIDForm
                ref="ask_adminControl_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :id="component.id"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :reference="component.reference"
                :adminControlName="component.adminControlName"
                :materialCertiSpec="component.materialCertiSpec"
                :fds="component.fds"
                :incmgInsp_id="incmgInsp_id"
                :articleID="data_article_id"
                :articleType="data_article_type"
                :articleSubFam_id="articleSubFam_id"
                @deleteAdminControl="getContent(key)"
            />
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another file button appear -->
                <button v-on:click="addComponent('')">Add</button>
                <!--If file array is not empty and if the user is not in modification mode -->
            </div>
<!--            <SaveButtonForm v-if="components.length>1" :consultMod="this.isInModifMod" :modifMod="this.isInModifMod"
                        saveAll
                        @add="saveAll" @update="saveAll"/>-->
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import AdminControlIDForm from "./AdminControlIDForm.vue";

export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        AdminControlIDForm,
        SaveButtonForm,
        ImportationAlert

    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedAdminControl: All article imported from the database
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
        importedAdminControl: {
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
        },
        incmgInsp_id: {
            type: Number,
            default: null
        },
        checkedTest: {
            type: Array,
            default: null
        },
        articleSubFam_id: {
            type: Number,
            default: null
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
            adminControl: this.importedAdminControl,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_article_id: this.article_id,
            data_article_type: this.articleType,
            data_incmingInsp_id: this.incmgInsp_id,
            loaded: false,
            data_sub_fam_id: this.articleSubFam_id

        };
    },
    methods: {
        /*Function for adding a new empty file form*/
        addComponent(name) {
            this.components.push({
                comp: 'AdminControlIDForm',
                key: this.uniqueKey++,
                id: null,
                adminControlName: name
            });
        },
        /*Function for adding an imported file form with his data*/
        addImportedComponent(adminControl_name, adminControl_reference, adminControl_materialCertifSpe, incmgInsp_id, adminControl_FDS, id, className) {
            this.components.push({
                comp: 'AdminControlIDForm',
                key: this.uniqueKey++,
                adminControlName: adminControl_name,
                reference: adminControl_reference,
                materialCertiSpec: adminControl_materialCertifSpe,
                incmgInsp_id: incmgInsp_id,
                fds: adminControl_FDS,
                id: id,
                className: className
            });
        },
        /*Suppression of a file component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported article*/
        importAdminControl() {
            if (this.adminControl.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dc of this.adminControl) {
                    const className = "importedAdminControl" + dc.id;
                    this.addImportedComponent(
                        dc.adminControl_name,
                        dc.adminControl_reference,
                        dc.adminControl_materialCertifSpe,
                        dc.incmgInsp_id,
                        dc.adminControl_FDS,
                        dc.id,
                        className
                    );
                }
                this.adminControl = null
            }
        },
        /*Function for saving all the data in one time*/
        /*saveAll(savedAs) {
            for (const component of this.$refs.ask_adminControl_data) {
                /!*If the user is in modification mode*!/
                if (this.modifMod == true) {
                    /!*If the file doesn't have, an id*!/
                    if (component.id == null) {
                        /!*AddequipmentFile is used*!/
                        component.addEquipmentFile(savedAs);
                    } else
                        /!*Else if the file has an id and addSucces is equal to true*!/
                    if (component.id != null || component.addSucces == true) {
                        /!*updateEquipmentFile is used*!/
                        /!*if (component !== "validated") {
                            component.updateEquipmentFile(savedAs);
                        }*!/ // FIXME ?
                    }
                } else {
                    /!*Else If the user is not in modification mode*!/
                    component.addEquipmentFile(savedAs);
                }
            }
        }*/
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
         if (this.checkedTest!=null && this.checkedTest.includes('adminControl')) {
            this.addComponent("Test required to ensure performance of the medical device");
        }
        if (this.import_id !== null && this.modifMod) {
            /*If the user chooses importation doc control*/
            var consultUrl="";
            /*Make a get request to ask the controller the doc control corresponding to the id of the incoming inspection with which data will be imported*/
            if (this.data_article_id != null && this.data_article_id == this.import_id) {
                consultUrl = (type,id) => `/incmgInsp/adminControl/sendFromFamily/${type}/${id}`;
            }else{
                if (this.incmgInsp_id != null && this.incmgInsp_id == this.import_id){
                    consultUrl = (id) => `/incmgInsp/adminControl/sendFromIncmgInsp/${id}`;
                }else{
                    if (this.data_sub_fam_id != null && this.data_sub_fam_id == this.import_id){
                        consultUrl = (type,id) => `/incmgInsp/adminControl/sendFromSubFamily/${type}/${id}`; 
                    }    
                }

            }
            if (this.data_article_id != null && this.data_article_id == this.import_id || this.data_sub_fam_id!=null && this.data_sub_fam_id == this.import_id ){
                axios.get(consultUrl(this.articleType,this.import_id))
                    .then(response => {
                    this.adminControl = response.data;
                        this.importAdminControl();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            }else{
                axios.get(consultUrl(this.import_id))
                    .then(response => {
                        this.adminControl = response.data;
                        this.importAdminControl();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            } 
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the mounted option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importAdminControl();
        }*/
    }
}
</script>

<style>

</style>

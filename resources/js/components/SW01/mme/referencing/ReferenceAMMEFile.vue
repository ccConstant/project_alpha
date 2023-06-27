<!--File name : ReferenceAMMEFile.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference a file from another MME-->

<template>
    <div class="mmeFile">
        <h2 class="titleForm">MME File</h2>
        <!--Adding to the vue MMEFileForm by going through the components array with the v-for-->
        <!--ref="ask_file_data" is used to call the child elements in this component-->
        <!--The emitted deleteFile is caught here and call the function getContent -->
        <MMEFileForm :is="component.comp" v-for="(component, key) in components" :id="component.id"
                     :key="component.key" ref="ask_file_data" :consultMod="isInConsultMod"
                     :divClass="component.className" :location="component.location"
                     :mme_id="data_mme_id" :modifMod="isInModifMod" :name="component.fileName"
                     :validate="component.validate"
                     @deleteFile="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another file button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If file array is not empty and if the user is not in modification mode -->
            <div v-if="this.files!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importFile">import</button>
            </div>
        </div>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod" saveAll
                        @add="saveAll" @update="saveAll"/>
        <ImportationAlert ref="importAlert"/>
    </div>

</template>


<script>
/*Importation of the other Components who will be used here*/
import MMEFileForm from './MMEFileForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'

export default {
    components: {
        MMEFileForm,
        SaveButtonForm,
        ImportationAlert
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
        importedFile: {
            type: Array,
            default: null
        },
        mme_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            components: [],
            uniqueKey: 0,
            files: this.importedFile,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_mme_id: this.mme_id
        };
    },
    methods: {
        //Function for adding a new empty file form
        addComponent() {
            this.components.push({
                comp: 'MMEFileForm',
                key: this.uniqueKey++,
            });
        },
        //Function for adding an imported file form with his data
        addImportedComponent(file_name, file_location, file_validate, file_className, id) {
            this.components.push({
                comp: 'MMEFileForm',
                key: this.uniqueKey++,
                fileName: file_name,
                location: file_location,
                className: file_className,
                validate: file_validate,
                id: id
            });
        },
        //Suppression of a file component from the vue
        getContent(key) {
            this.components.splice(key, 1);
        },
        //Function for adding to the vue the imported files
        importFile() {
            if (this.files.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const file of this.files) {
                    var className = "importedFile" + file.id
                    this.addImportedComponent(file.file_name, file.file_location, file.file_validate, className, file.id);
                }
                this.files = null
            }
        },
        //Function for saving all the data in one time
        saveAll(savedAs) {
            for (const component of this.$refs.ask_file_data) {
                //If the user is in modification mode
                if (this.modifMod == true) {
                    //If the file doesn't have, an id
                    if (component.file_id == null) {
                        //addMmeFile is used
                        component.addMmeFile(savedAs);
                    } else
                        //Else if the file has an id and addSucces is equal to true
                    if (component.file_id != null || component.addSucces == true) {
                        //updateMmeFile is used
                        if (component.file_validate !== "validated") {
                            component.updateMmeFile(savedAs);
                        }
                    }
                } else {
                    //Else If the user is not in modification mode
                    component.addMmeFile(savedAs);
                }
            }
        }
    },
    created() {
        //If the user chooses an imported mme
        if (this.import_id !== null) {
            //Make a get request to ask the controller the file corresponding to the id of the mme with which data will be imported
            const consultUrl = (id) => `/file/send/mme/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.files = response.data
                }).catch(error => {
            });
        }
    },
    mounted() {
        //If the user is in consultation or modification mode, the file will be added to the vue automatically
        if (this.consultMod || this.modifMod) {
            this.importFile();
        }
    }
}
</script>

<style>

</style>

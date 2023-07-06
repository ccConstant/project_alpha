<!--File name :ReferenceASubFamilyForm.vue-->
<!--Creation date : 3 Jul 2023-->
<!--Update date : 3 Jul 2023-->
<!--Vue Component used to reference a sub family -->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="articleSubFamily">
            <h2 v-if="components.length>0" class="titleForm">Article Sub Family </h2>
            <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
            <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
            <!--ref="ask_dim_data" is used to call the child elements in this component-->
            <!--The emitted deleteDim is caught here and call the function getContent -->
            <ArticleSubFamily
                ref="ask_familyMember_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :reference="component.reference"
                :designation="component.designation"
                :drawingPath="component.drawingPath"
                :version="component.version"
                :purchasedBy="component.purchasedBy"
                :divClass="component.className"
                :id="component.id"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :art_type="data_art_type.toUpperCase()"
                :art_id="data_art_id"
                :art_ref="data_art_ref"
                :validate="component.validate"
                @deleteStorageCondition="getContent(key)"/>
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another dimension button appear -->
                <button v-on:click="addComponent">Add</button>
            </div>
            <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                            :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        </div>
    </div>



</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticleSubFamilyForm from './ArticleSubFamilyForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ArticleSubFamilyForm,
        SaveButtonForm,
        InputInfo


    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedDim: All dimensions imported from the database
        eq_id: ID of the equipment in which the dimension will be added
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
        artFam_id: {
            type: Number
        },
        artFam_type:{
            type: String
        },
        import_id: {
            type: Number,
            default: null
        },
        artFam_ref:{
            type: String
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_art_id: data of the art id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_art_id: this.artFam_id,
            data_art_ref: this.artFam_ref,
            all_familyMember_validate: [],
            title_info: null,
            data_art_type: this.artFam_type.toLowerCase(),
            loaded: false,
            subFamilies:[],
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'ArticleSubFamilyForm',
                key: this.uniqueKey++,
                id: null,
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(artFam_ref, artFam_type, artFam_id, subFam_reference, subFam_designation, subFam_drawingPath, subFam_version, subFam_purchasedBy, className, id, subFam_validate) {
            this.components.push({
                comp: 'ArticleSubFamilyForm',
                art_ref: artFam_ref,
                key: this.uniqueKey++,
                reference: subFam_reference,
                designation: subFam_designation,
                drawingPath: subFam_drawingPath,
                validate: subFam_validate,
                version:subFam_version,
                art_type: artFam_type,
                art_id: artFam_id,
                purchasedBy:subFam_purchasedBy,
                className: className,
                id: id
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        importSubFamilies() {
            if (this.subFamilies.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.subFamilies) {
                    const className = "importedSubFamily" + dt.id;
                    this.addImportedComponent(
                        this.data_art_ref,
                        this.data_art_type,
                        this.data_art_id,
                        dt.reference,
                        dt.designation,
                        dt.drawingPath,
                        dt.version,
                        dt.purchasedBy,
                        className,
                        dt.id,
                        dt.validate,
                    );
                }
            }
        },

        /*Function for saving all the data in one time*/
        /*saveAll(savedAs) {
            for (const component of this.$refs.ask_dim_data) {
                /*If the user is in modification mode
                if (this.modifMod == true) {
                    /*If the dimension doesn't have, an id
                    if (component.dim_id == null) {
                        /*AddequipmentDim is used
                        component.addEquipmentDim(savedAs);
                    } else
                        /*Else if the dimension has an id and addSucces is equal to true
                    if (component.dim_id != null || component.addSucces == true) {
                        /*updateEquipmentDim is used
                        if (component.dim_validate !== "validated") {
                            component.updateEquipmentDim(savedAs);
                        }

                    }
                } else {
                    /*Else If the user is not in modification mode
                    component.addEquipmentDim(savedAs);
                }
            }
        }*/
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            axios.get('/'+this.data_art_type+'/subFam/send/'+this.import_id)
                .then(response => {
                    this.subFamilies = response.data;
                    this.importSubFamilies();
                    this.loaded = true;
                })
                .catch(error => {
                });
        } else {
            console.log("coucou")
            this.loaded = true;
        }
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

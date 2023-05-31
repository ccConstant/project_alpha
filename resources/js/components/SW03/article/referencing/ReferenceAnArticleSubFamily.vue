<!--File name :ReferenceAnArticleSubFamily.vue-->
<!--Creation date : 31 May 2023 -->
<!--Update date : 31 May 2023-->
<!--Vue Component used to reference a sub family  -->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="ArticleSubFamily">
            <h2 v-if="components.length>0" class="titleForm">Article Sub Families </h2>
            <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
            <div v-for="(component, key) in components">
                <ArticleSubFamily
                    ref="ask_familyMember_data"
                    :key="component.key"
                    :is="component.comp"
                    :dimension="component.dimension"
                    :sameValues="component.sameValues"
                    :designation="component.designation"
                    :divClass="component.className"
                    :id="component.id"
                    :consultMod="isInConsultMod"
                    :modifMod="component.id !== null"
                    :art_type="data_art_type.toUpperCase()"
                    :art_id="data_art_id"
                    :genRef="data_genRef"
                    :genDesign="data_genDesign"
                    :varCharac="data_varCharac"
                    :varCharacDesign="data_varCharacDesign"
                    @deleteStorageCondition="getContent(key)"
                />
                <!--Affichage du formulaire pour les membres si la sous famille à été créée-->
                <ReferenceAnArticleFamilyMember
                />

            </div>

            <div v-if="!this.consultMod">
                <button v-on:click="addComponent">Add</button>
            </div>
            <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                            :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        </div>
    </div>
</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticleSubFamily from './ArticleSubFamilyForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'
import ReferenceAnArticleFamilyMember from "./ReferenceAnArticleFamilyMember.vue";


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ReferenceAnArticleFamilyMember,
        ArticleSubFamily,
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
        artType:{
            type: String
        },
        import_id: {
            type: Number,
            default: null
        },
        genRef: {
            type: String
        },
        genDesign: {
            type: String
        },
        varCharac: {
            type: String
        },
        varCharacDesign: {
            type: String
        },
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
            all_familyMember_validate: [],
            title_info: null,
            data_art_type: this.artType.toLowerCase(),
            data_genRef: this.genRef,
            data_genDesign: this.genDesign,
            data_varCharac: this.varCharac,
            data_varCharacDesign: this.varCharacDesign,
            loaded: false,
            familyMember: [],
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'ArticleSubFamily',
                key: this.uniqueKey++,
                id: null,
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(familyMember_dimension, familyMember_sameValues, familyMember_design,familyMember_className, id) {
            this.components.push({
                comp: 'ArticleSubFamily',
                key: this.uniqueKey++,
                dimension: familyMember_dimension,
                sameValues: familyMember_sameValues,
                designation: familyMember_design,
                className: familyMember_className,
                id: id
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        importFamilyMembers() {
            if (this.familyMember.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const dt of this.familyMember) {
                    const className = "importedPurSpe" + dt.id;
                    this.addImportedComponent(
                        dt.dimension,
                        Boolean(dt.sameValues),
                        dt.designation,
                        className,
                        dt.id
                    );
                }
                this.criticality = null
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
        /*If the user chooses importation doc control*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the doc control to corresponding to the id of the incoming inspection with which data will be imported*/
            if (this.data_art_type === 'comp') {
                axios.get('/comp/mb/send/'+this.import_id)
                    .then(response => {
                        this.familyMember = response.data;
                        this.importFamilyMembers();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            } else if (this.data_art_type === 'cons') {
                axios.get('/cons/mb/send/'+this.import_id)
                    .then(response => {
                        this.familyMember = response.data;
                        this.importFamilyMembers();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            } else if (this.data_art_type === 'raw') {
                axios.get('/raw/mb/send/'+this.import_id)
                    .then(response => {
                        this.familyMember = response.data;
                        this.importFamilyMembers();
                        this.loaded = true;
                    })
                    .catch(error => {
                    });
            }
        } else {
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

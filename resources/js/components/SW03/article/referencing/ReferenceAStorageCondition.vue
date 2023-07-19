<!--File name :ReferenceAStorageCondition.vue-->
<!--Creation date : 27 Apr 2023 -->
<!--Update date : 27 Apr 2023-->
<!--Vue Component used to reference a storage condition in the article-->

<template>
    <div class="articleStorageCondition">
        <h2 v-if="sto_conds.length > 0" class="titleForm">Article Storage Condition(s) </h2>
        <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
        <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
        <!--ref="ask_dim_data" is used to call the child elements in this component-->
        <!--The emitted deleteDim is caught here and call the function getContent -->
        <ArticleStorageConditionForm
            ref="ask_storageCondition_data"
            v-for="(component, key) in components"
            :key="component.key"
            :is="component.comp"
            :value="component.value"
            :divClass="component.className"
            :id="component.id"
            :consultMod="isInConsultMod"
            :modifMod="component.id !== null && isInModifMod"
            :art_type="data_art_type"
            :art_id="data_art_id"
            :artSubFam_id="data_artSubFam_id"
            @deleteStorageCondition="getContent(key)"
            :validate="component.validate"
        />
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add Storage Conditions</button>
        </div>
<!--        <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                        :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>-->
    </div>


</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticleStorageConditionForm from './ArticleStorageConditionForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ArticleStorageConditionForm,
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
            type: Number,
            default: null
        },
        artType:{
            type: String
        },
        import_id: {
            type: Number,
            default: null
        },
        articleSubFam_id: {
            type: Number
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
            all_storageCondition_validate: [],
            title_info: null,
            data_art_type: this.artType,
            sto_conds: [],
            loaded: false,
            data_artSubFam_id: this.articleSubFam_id
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'ArticleStorageConditionForm',
                key: this.uniqueKey++,
                id: null
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(storageCondition_value, storageCondition_className, id, validate) {
            this.components.push({
                comp: 'ArticleStorageConditionForm',
                key: this.uniqueKey++,
                value: storageCondition_value,
                className: storageCondition_className,
                id: id,
                validate: validate
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
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

        importStoConds() {
            if (this.sto_conds.length === 0 && !this.isInModifMod) {
                this.loaded = true;
            } else {
                for (const st of this.sto_conds) {
                    this.addImportedComponent(
                        st.value,
                        'importedStorageCondition'+st.id,
                        st.id,
                        st.validate
                    );
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        console.log("Reference a storage condition created")
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the file corresponding to the id of the equipment with which data will be imported*/
            if (this.data_article_id !== null) {
                axios.get('/artFam/enum/storageCondition/sendUsageByType/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        this.sto_conds = response.data;
                        this.importStoConds();
                        this.loaded = true;
                    }).catch(error => {
                });
            } else {
                axios.get('/artSubFam/enum/storageCondition/sendUsageByType/' + this.data_art_type + '/' + this.import_id)
                    .then(response => {
                        this.sto_conds = response.data;
                        this.importStoConds();
                        this.loaded = true;
                    }).catch(error => {
                });
            }
            /*axios.get('/artFam/enum/storageCondition/sendUsageByType/' + this.data_art_type + '/' + this.import_id)
                .then(response => {
                    this.sto_conds = response.data;
                    console.log(this.sto_conds);
                    this.importStoConds();
                    this.loaded = true;
                })
                .catch(error => {
                });*/
        } else {
            this.loaded = true;
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        /*if (this.consultMod || this.modifMod) {
            this.importDim();
        }*/
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

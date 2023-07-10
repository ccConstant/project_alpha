<!--File name :ReferenceAnArticleFamilyMember.vue-->
<!--Creation date : 2 May 2023 -->
<!--Update date : 2 May 2023-->
<!--Vue Component used to reference a family member -->

<template>
    <div>
        <div v-if="loaded===false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else class="articleFamilyMember">
            <h2 v-if="components.length>0" class="titleForm">Article Family Member(s) </h2>
            <InputInfo class="info_title" :info="title_info.info_value" v-if="title_info!=null "/>
            <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
            <!--ref="ask_dim_data" is used to call the child elements in this component-->
            <!--The emitted deleteDim is caught here and call the function getContent -->
            <ArticleFamilyMemberForm
                ref="ask_familyMember_data"
                v-for="(component, key) in components"
                :key="component.key"
                :is="component.comp"
                :reference="component.reference"
                :designation="component.designation"
                :divClass="component.className"
                :id="component.id"
                :validate="component.validate"
                :consultMod="isInConsultMod"
                :modifMod="component.id !== null"
                :art_type="data_art_type.toUpperCase()"
                :art_id="data_art_id"
                :artSubFam_id="artSubFam_id"
                :artFam_ref="data_artFam_ref"
                :artSubFam_ref="data_artSubFam_ref"
                @deleteStorageCondition="getContent(key)"/>
            <!--If the user is not in consultation mode -->
            <div v-if="!this.consultMod">
                <!--Add another dimension button appear -->
                <button v-on:click="addComponent">Add Article Family Member</button>
            </div>
            <SaveButtonForm saveAll v-if="components.length>1" @add="saveAll" @update="saveAll"
                            :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"/>
        </div>
    </div>



</template>

<script>
/*Importation of the other Components who will be used here*/
import ArticleFamilyMemberForm from './ArticleFamilyMemberForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import InputInfo from '../../../input/InputInfo.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        ArticleFamilyMemberForm,
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
        artSubFam_id: {
            type: Number
        },
        artFam_ref: {
            type: String
        },
        artSubFam_ref: {
            type: String
        },
        importedMembers: {
            type: Array,
            default: null
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
            data_artFam_ref: this.artFam_ref,
            data_artSubFam_ref: this.artSubFam_ref,
            loaded: false,
            familyMember: this.importedMembers
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'ArticleFamilyMemberForm',
                key: this.uniqueKey++,
                id: null,
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(familyMember_reference, familyMember_designation,familyMember_className, id, familyMember_validate) {
            this.components.push({
                comp: 'ArticleFamilyMemberForm',
                key: this.uniqueKey++,
                reference: familyMember_reference,
                designation: familyMember_designation,
                className: familyMember_className,
                id: id,
                validate: familyMember_validate
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
                    const className = "importedFamilyMembers" + dt.id;
                    this.addImportedComponent(
                        dt.reference,
                        dt.designation,
                        className,
                        dt.id,
                        dt.validate
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

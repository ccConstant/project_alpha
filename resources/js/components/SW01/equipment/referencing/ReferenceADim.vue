<!--File name :ReferenceADim.vue-->
<!--Creation date : 10 May 2022-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component used to reference a dimension in the equipment-->

<template>
    <div class="equipmentDim">
        <h2 class="titleForm">Equipment Characteristic(s)</h2>
        <InputInfo v-if="title_info!=null " :info="title_info.info_value" class="info_title"/>
        <!--Adding to the vue EquipmentDimForm by going through the components array with the v-for-->
        <!--ref="ask_dim_data" is used to call the child elements in this component-->
        <!--The emitted deleteDim is caught here and call the function getContent -->
        <EquipmentDimForm :is="component.comp" v-for="(component, key) in components" :id="component.id"
                          :key="component.key" ref="ask_dim_data" :consultMod="isInConsultMod"
                          :divClass="component.className" :eq_id="data_eq_id" :modifMod="isInModifMod"
                          :name="component.dimName"
                          :type="component.type" :unit="component.unit" :validate="component.validate"
                          :value="component.value"
                          @deleteDim="getContent(key)"/>
        <!--If the user is not in consultation mode -->
        <div v-if="!this.consultMod">
            <!--Add another dimension button appear -->
            <button v-on:click="addComponent">Add</button>
            <!--If dimensions array is not empty and if the user is not in modification mode -->
            <div v-if="this.dimensions!==null">
                <!--The importation button appear -->
                <button v-if="!modifMod " v-on:click="importDim">import</button>
            </div>
        </div>
        <SaveButtonForm v-if="components.length>1" :consultMod="this.isInConsultMod" :modifMod="this.isInModifMod"
                        saveAll
                        @add="saveAll" @update="saveAll"/>
        <ImportationAlert ref="importAlert"/>
    </div>


</template>

<script>
/*Importation of the other Components who will be used here*/
import EquipmentDimForm from './EquipmentDimForm.vue'
import SaveButtonForm from '../../../button/SaveButtonForm.vue'
import ImportationAlert from '../../../alert/ImportationAlert.vue'
import InputInfo from '../../../input/InputInfo.vue'


export default {
    /*--------Declaration of the others Components:--------*/
    components: {
        EquipmentDimForm,
        SaveButtonForm,
        ImportationAlert,
        InputInfo


    },
    /*--------Declaration of the different props:--------
        consultMod: If this props is present the form is in consult mode we disable all the field
        modifMod: If this props is present, the form is in modification mode we disable the save button and show update button
        importedDim: All dimensions imported from the database
        eq_id: ID of the equipment in which the dimension will be added
        import_id: ID of the equipment with which dimensions will be imported
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
        importedDim: {
            type: Array,
            default: null
        },
        eq_id: {
            type: Number
        },
        import_id: {
            type: Number,
            default: null
        }
    },
    /*--------Declaration of the different returned data:--------
        components: Array in which will be added the data of a component
        uniqueKey: A unique key assigned to a component
        dimensions: Array of all imported dimensions
        isInConsultedMod: data of the consultMod prop
        isInModifMod: data of the modifMod prop
        data_eq_id: data of the eq_id prop
    -----------------------------------------------------------*/
    data() {
        return {
            components: [],
            uniqueKey: 0,
            dimensions: this.importedDim,
            count: 0,
            isInConsultMod: this.consultMod,
            isInModifMod: this.modifMod,
            data_eq_id: this.eq_id,
            all_dim_validate: [],
            title_info: null,
        };
    },
    methods: {
        /*Function for adding a new empty dimension form*/
        addComponent() {
            this.components.push({
                comp: 'EquipmentDimForm',
                key: this.uniqueKey++,
            });
        },
        /*Function for adding an imported dimension form with his data*/
        addImportedComponent(dim_type, dim_name, dim_value, dim_unit, dim_validate, dim_className, id) {
            this.components.push({
                comp: 'EquipmentDimForm',
                key: this.uniqueKey++,
                type: dim_type,
                dimName: dim_name,
                value: dim_value.toString(),
                unit: dim_unit,
                className: dim_className,
                validate: dim_validate,
                id: id
            });
        },
        /*Suppression of a dimension component from the vue*/
        getContent(key) {
            this.components.splice(key, 1);
        },
        /*Function for adding to the vue the imported dimensions*/
        importDim() {
            if (this.dimensions.length == 0 && !this.isInModifMod) {
                this.$refs.importAlert.showAlert();
            } else {
                for (const dimension of this.dimensions) {
                    const className = "importedDim" + dimension.id;
                    this.addImportedComponent(dimension.dim_type, dimension.dim_name, dimension.dim_value, dimension.dim_unit, dimension.dim_validate, className, dimension.id);
                }
            }


            this.dimensions = null
        },
        /*Function for saving all the data in one time*/
        saveAll(savedAs) {
            for (const component of this.$refs.ask_dim_data) {
                /*If the user is in modification mode*/
                if (this.modifMod == true) {
                    /*If the dimension doesn't have, an id*/
                    if (component.dim_id == null) {
                        /*AddequipmentDim is used*/
                        component.addEquipmentDim(savedAs);
                    } else
                        /*Else if the dimension has an id and addSucces is equal to true*/
                    if (component.dim_id != null || component.addSucces == true) {
                        /*updateEquipmentDim is used*/
                        if (component.dim_validate !== "validated") {
                            component.updateEquipmentDim(savedAs);
                        }
                    }
                } else {
                    /*Else If the user is not in modification mode*/
                    component.addEquipmentDim(savedAs);
                }
            }
        }
    },
    /*All functions inside the created option are called after the component has been created.*/
    created() {
        /*If the user chooses importation equipment*/
        if (this.import_id !== null) {
            /*Make a get request to ask the controller the dimension corresponding to the id of the equipment with which data will be imported*/
            const consultUrl = (id) => `/dimension/send/${id}`;
            axios.get(consultUrl(this.import_id))
                .then(response => {
                    this.dimensions = response.data;
                    axios.get('/info/send/dimension')
                        .then(response => {
                            this.title_info = response.data[4];
                        }).catch(error => {
                    });
                }).catch(error => {
            });
        }
    },
    /*All functions inside the created option are called after the component has been mounted.*/
    mounted() {
        /*If the user is in consultation or modification mode, dimensions will be added to the vue automatically*/
        if (this.consultMod || this.modifMod) {
            this.importDim();
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

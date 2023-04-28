<!--File name : ReferenceAnArticleFamily.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div>
        <ArticleFamilyForm @CompFamID="put_compFamily_id"/>
        <div v-if="this.compFam_id!=null">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Article Storage Condition
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <ReferenceAStorageCondition :artType="'COMP'" :artFam_id="this.compFam_id" :import_id="this.compFam_id"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ArticleFamilyForm from "./ArticleFamilyForm";
import ReferenceAStorageCondition from "./ReferenceAStorageCondition";



export default {
    components: {
        ArticleFamilyForm,
        ReferenceAStorageCondition,
    },
    data() {
        return {
            /*ID of the equipment created*/
            compFam_id: null,
        }
    },
    methods: {
        put_compFamily_id(value) {
            this.compFam_id = value;
        },
    }
}
</script>

<style>
</style>


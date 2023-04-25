<!--File name : ReferenceAnArticleFamily.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the Id card of the component who call all the input component and send the data to the controllers-->


<template>
    <div>
        <ArticleFamilyForm @CompFamID="put_compFamily_id"/>
        <div v-if="this.compFam_id!=null">
        </div>
    </div>
</template>

<script>
import ArticleFamilyForm from "./ArticleFamilyForm";



export default {
    components: {
        ArticleFamilyForm,
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


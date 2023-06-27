<!--File name : SuccesAlert.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of a success message-->

<template>
    <div>
        <vue-snotify></vue-snotify>
    </div>

</template>

<script>
export default {
    methods: {
        showAlert(message) {
            this.$snotify.success(message);
        }
    }



}
</script>

<style>

</style>

<!--File name : ErrorAlert.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of a error message-->

<template>
    <div>
        <vue-snotify></vue-snotify>
    </div>
</template>

<script>
  export default {
    methods: {
      showAlert(error_data) {
        this.$snotify.error(error_data);
      }
    }
  }
</script>

<style lang="scss">

</style>

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
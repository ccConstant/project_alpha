<template>
  <div>
    <b-alert
      :show="dismissCountDown"
      dismissible
      variant="success"
      @dismissed="dismissCountDown=0"
      @dismiss-count-down="countDownChanged"
    >
      <p>Success </p>

    </b-alert>

  </div>
</template>

<script>
  export default {
    data() {
      return {
        dismissSecs: 3,
        dismissCountDown: 0,
        showDismissibleAlert: false,
      }
    },
    methods: {
      countDownChanged(dismissCountDown) {
        this.dismissCountDown = dismissCountDown
      },
      showAlert() {
        this.dismissCountDown = this.dismissSecs
      }
    }
  }
</script>

<style lang="scss">

</style>
<template>
  <div>
    <b-alert
      :show="dismissCountDown"
      dismissible
      variant="danger"
      @dismissed="dismissCountDown=0"
      @dismiss-count-down="countDownChanged"
    >
      <p>{{this.errors[0]}} </p>

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
        errors:[]
      }
    },
    methods: {
      countDownChanged(dismissCountDown) {
        this.dismissCountDown = dismissCountDown
      },
      showAlert(error_data) {
        this.errors=error_data;
        this.dismissCountDown = this.dismissSecs
      }
    }
  }
</script>

<style lang="scss">

</style>
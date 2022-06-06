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
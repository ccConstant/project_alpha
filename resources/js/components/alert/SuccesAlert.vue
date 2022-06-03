<template>
<div>
    <vue-snotify></vue-snotify>
  
</div>

</template>

<script>
export default {
    methods: {
        showAlert() {
            this.$snotify.success('Ajouter avec succes');
        }
    }



}
</script>

<style>

</style>
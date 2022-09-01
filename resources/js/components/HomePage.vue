<template>
    <div>
	    <h1>Welcome to ALPHA</h1>
		<h1 v-if="this.$userId!==''">{{this.$userId.user_firstName}} {{this.$userId.user_lastName}}</h1>
		<br><br>  <br>  <br>  <br>  <br>  <br>
        <br><br><br><br><br><br><br><br><br><br><br>
        <br><br><br><br>
        <br>
        <p> Software Version : 1.0.0  (last update 12 Jul 2022)</p>
	</div>
</template>

<script>
export default {
	created(){
		console.log(this.$userId)
	}
}
</script>

<style>

</style>
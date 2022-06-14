<template>
    <div>
	    <h1>Welcome to ALPHA</h1>
		<h1 v-if="this.$userId!==''">{{this.$userId.user_firstName}} {{this.$userId.user_lastName}}</h1>
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
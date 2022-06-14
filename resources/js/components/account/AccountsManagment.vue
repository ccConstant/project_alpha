<template>
	<div>
		coco
	</div>
</template>

<script>
export default {
	created(){
		if(this.$userId.user_menuUserAcessRight!=true){
            this.$router.replace({ name: "home" })
        }
	}

}
</script>

<style>

</style>
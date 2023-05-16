<!--File name : HomePage.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component of the main page of the website-->

<template>
    <div>
		<br><br><br><br><br><br>
	    <h1>Welcome to ALPHA</h1>
		<h1 v-if="this.$userId!==''">{{this.$userId.user_firstName}} {{this.$userId.user_lastName}}</h1>
		<div class="div_image"><img src="/images/Homepage Alpha Software.png" class="best_wishes_navbar" alt="Best Wishes 2023"></div>
		<br><br>  <br>  <br>  <br>  <br>  <br>
        <br><br><br><br><br><br><br><br><br>
        <br>
        <br>
        <p> SW01 Version : 0.0.0  (last update Apr 2023)</p>
		<p> SW03 Version : 0.0.0  (last update Apr 2023)</p>
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
h1{
	text-align:center;
	font-family : 'Times New Roman', Times, serif;
	color : #20dad3;
	font-size:50px;
}
.div_image{
	text-align:center;
}

.best_wishes_navbar{
	width: 50%;
	height: 50%;
	margin-top : 80px;
	top: 0;
	left: 0;
	z-index: -1;
}

</style>

<!--File name : HomePageSW03.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the main page of the website-->

<template>
    <div class="main">
        <br><br>
        <h1 v-if="this.$userId!==''">Welcome {{this.$userId.user_firstName}},</h1>
    </div>
</template>

<script>
export default {
}
</script>

<style scoped>
.main{
    background-image: url("/images/Homepage_Alpha_Software_SW03.JPG");
    background-repeat: no-repeat;
    background-size: cover;
    height: 85vh;
    z-index: 1;
}
h1{
    text-align:left;
    font-family : 'Times New Roman', Times, serif;
    color : #000000;
    font-size:50px;
    margin-bottom: 5px;
    margin-left: 10px;
}
p{
    vertical-align: bottom;
}
</style>

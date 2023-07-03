<!--File name : Navbar.vue-->
<!--Creation date : 21 Apr 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the navigation bar, up of the website-->

<template>
	<div class="main">
		<b-navbar type="dark" variant="dark">
			<b-navbar-nav>
				<b-navbar-brand href="/"><img src="/images/logo.jpg" class="logo_navbar" alt="Alpha Logo"></b-navbar-brand>
				<!-- Navbar dropdowns -->
                <!-- For the SW01 drop-down menu -->
				<b-navbar-brand href="/SW01">SW01</b-navbar-brand>
				<b-navbar-brand href="/SW03">SW03</b-navbar-brand>
<!--                &lt;!&ndash; For the MME drop-down menu &ndash;&gt;
				<b-nav-item-dropdown text="MME" right>
					<b-dropdown-item href="/mme/add">Add a new MME</b-dropdown-item>
					<b-dropdown-item href="/mme/list">List of all MME</b-dropdown-item>
					<b-dropdown-item href="/mme/life_event">Life Event</b-dropdown-item>
					<b-dropdown-item href="/mme/annual/planning">Annual planning</b-dropdown-item>
					<b-dropdown-item href="/mme/monthly/planning">Monthly planning</b-dropdown-item>
				</b-nav-item-dropdown>
                &lt;!&ndash; For the all simple menu &ndash;&gt;
                <b-nav-item @click="enum_acces">Enum Management</b-nav-item>
                <b-nav-item  @click="info_acces">Info Management</b-nav-item>
                <b-nav-item @click="account_managment_acces">Accounts Management</b-nav-item>-->
                <!-- For the User drop-down menu -->
                <b-nav-item-dropdown text="User" right>
					<b-dropdown-item v-if="this.$userId==''" href="/sign_up">Sign up</b-dropdown-item>
					<b-dropdown-item v-if="this.$userId==''" href="/sign_in">Sign in</b-dropdown-item>
					<b-dropdown-item v-if="this.$userId!=''" href="#" @click="disconnect()" >Disconnect</b-dropdown-item>
					<b-dropdown-item v-if="this.$userId!=''" href="/my_account" >My account</b-dropdown-item>
				</b-nav-item-dropdown>
			</b-navbar-nav>
		</b-navbar>
		<ErrorAlert ref="errorAlert"/>
	</div>
</template>

<script>
import ErrorAlert from '../alert/ErrorAlert.vue'

export default {
	components:{
		ErrorAlert
	},
	created(){
	},
	methods:{
		disconnect(){
			axios.post('/logout',{
			})
			.then(response =>{window.location.href = "/"})
			.catch(error => this.errors=error.response.data.errors) ;
		},
	},
}
</script>

<style scoped>
	.main{
        width: 100%;
        max-height: 5vh;
        margin-bottom: 10px;
    }
	.logo_navbar{
		width: 80px;
		height: 40px;
	}
</style>


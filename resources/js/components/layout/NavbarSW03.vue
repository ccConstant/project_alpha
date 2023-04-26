<!--File name : NavbarSW01.vue-->
<!--Creation date : 25 Apr 2023-->
<!--Update date : 25 Apr 2023-->
<!--Vue Component of the navigation bar, up of the website-->

<template>
	<div>
		<b-navbar type="dark" variant="dark">
			<b-navbar-nav>
				<b-navbar-brand href="/"><img src="/images/logo.png" class="logo_navbar" alt="Alpha Logo"></b-navbar-brand>
<!--				<b-navbar-brand href="/">Home</b-navbar-brand>-->
				<!-- Navbar dropdowns -->
                <!-- For the Equipment drop-down menu -->
				<b-nav-item-dropdown text="Article" right>
					<b-dropdown-item href="/article/add">Add a new article</b-dropdown-item>
					<b-dropdown-item href="/article/list">List of all article</b-dropdown-item>
				</b-nav-item-dropdown>
                <!-- For the MME drop-down menu -->
				<b-nav-item-dropdown text="Supplier" right>
					<b-dropdown-item href="/supplier/add">Add a new supplier</b-dropdown-item>
					<b-dropdown-item href="/supplier/list">List of all supplier</b-dropdown-item>
				</b-nav-item-dropdown>
                <!-- For the all simple menu -->
                <b-nav-item @click="enum_acces">Enum Management</b-nav-item>
                <b-nav-item  @click="info_acces">Info Management</b-nav-item>
                <b-nav-item @click="account_managment_acces">Accounts Management</b-nav-item>
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
        console.log(this.$userId)
	},
	methods:{
		disconnect(){
			axios.post('/logout',{
			})
			.then(response =>{window.location.href = "/"})
			.catch(error => this.errors=error.response.data.errors) ;
		},
		enum_acces(){
			if(this.$userId.user_addEnumRight==true ||
			this.$userId.user_deleteEnumRight==true ||
			this.$userId.user_updateEnumRight==true){
				this.$router.push({ name: "url_enum" }).catch(() => {})
			}else{
				this.$refs.errorAlert.showAlert("You don't have the right");
			}
		},
		account_managment_acces(){
			if(this.$userId.user_menuUserAcessRight==true){
				this.$router.push({ name: "url_accounts" }).catch(() => {})
			}else{
				this.$refs.errorAlert.showAlert("You don't have the right");
			}
		},
		info_acces(){
			if(this.$userId.user_updateInformationRight==true){
				this.$router.push({ name: "url_infos" }).catch(() => {})
			}else{
				this.$refs.errorAlert.showAlert("You don't have the right");
			}
		}
	},
}
</script>

<style>
	/*.navbar{
		width:1042px;
		height:60px;
		position:fixed;
	}*/

	.logo_navbar{
		width: 80px;
		height: 40px;
	}


</style>


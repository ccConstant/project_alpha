
<template>
	<div>
		<b-navbar type="dark" variant="dark">
			<b-navbar-nav>
				<b-navbar-brand href="/"><img src="/images/logo.png" class="logo_navbar" alt="Alpha Logo"></b-navbar-brand>
				<!-- Navbar dropdowns -->
				<b-nav-item-dropdown text="Equipment" right>
					<b-dropdown-item href="/equipment/add">Add a new equipment</b-dropdown-item>
					<b-dropdown-item href="/equipment/list">List of all equipment</b-dropdown-item>
					<b-dropdown-item href="/equipment/life_event">Life Event</b-dropdown-item>
					<b-dropdown-item href="/equipment/maintenance/calendar">Maintenance</b-dropdown-item>
				</b-nav-item-dropdown>

				<b-nav-item-dropdown text="MME" right>
					<b-dropdown-item href="/mme/add">Add a new MME</b-dropdown-item>
					<b-dropdown-item href="/mme/list">List of all MME</b-dropdown-item>
					<b-dropdown-item href="/mme/life_event">Life Event</b-dropdown-item>
					<b-dropdown-item href="/mme/maintenance/calendar">Maintenance</b-dropdown-item>
					
				</b-nav-item-dropdown>

					<b-nav-item @click="enum_acces">Enum Managment</b-nav-item>
					<b-nav-item  @click="info_acces">Info Managment</b-nav-item>
					<b-nav-item @click="account_managment_acces">Accounts Managment</b-nav-item>
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
			//If the dimension is added succesfuly
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


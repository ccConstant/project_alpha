<template>
	<div class="account_managment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
			<b-container class="user_table_container" fluid="xl">
				<b-row>
					<b-col>Right</b-col>
					<b-col v-for="(user, index) in pageOfItems" :key="index">
						{{user.user_lastName}} {{user.user_firstName}}
					</b-col>

					<AccountManagmentElement right_title="Make equipment opération validation" key_letter="A" :users="pageOfItems" right_name="user_makeEqOpValidationRight"/>
					<AccountManagmentElement right_title="Person trained to general principles of equipment managment" key_letter="B" :users="pageOfItems" right_name="user_personTrainedToGeneralPrinciplesOfEqManagementRight"/>
					<AccountManagmentElement right_title="Person trained to general principles of MME managment" key_letter="C" :users="pageOfItems" right_name="user_personTrainedToGeneralPrinciplesOfMMEManagementRight"/>
					<AccountManagmentElement right_title="Reset user password" key_letter="D" :users="pageOfItems" right_name="user_resetUserPasswordRight"/>
					<AccountManagmentElement right_title="Update data validated but not signed" key_letter="E" :users="pageOfItems" right_name="user_updateDataValidatedButNotSignedRight" />
					<AccountManagmentElement right_title="Update descriptive LifeSheet of signed data" key_letter="F" :users="pageOfItems" right_name="user_updateDescriptiveLifeSheetDataSignedRight"/>
					<AccountManagmentElement right_title="Validate other data" key_letter="G" :users="pageOfItems" right_name="user_validateOtherDataRight"/>
					<AccountManagmentElement right_title="Update data in draft or to be validated" key_letter="H" :users="pageOfItems" right_name="user_updateDataInDraftRight"/>
					<AccountManagmentElement right_title="Validate descriptive LifeSheet data right" key_letter="I" :users="pageOfItems" right_name="user_validateDescriptiveLifeSheetDataRight"/>
					<AccountManagmentElement right_title="Delete not validated data of an MME or equipment" key_letter="J" :users="pageOfItems" right_name="user_deleteDataNotValidatedLinkedToEqOrMmeRight"/>
					<AccountManagmentElement right_title="Delete validated data of an MME or equipment" key_letter="K" :users="pageOfItems" right_name="user_deleteDataValidatedLinkedToEqOrMmeRight"/>
					<AccountManagmentElement right_title="Delete an MME or equipment" key_letter="L" :users="pageOfItems" right_name="user_deleteEqOrMmeRight"/>
					<AccountManagmentElement right_title="Update an enumeration" key_letter="M" :users="pageOfItems" right_name="user_updateEnumRight" />
					<AccountManagmentElement right_title="Delete an enumeration" key_letter="N" :users="pageOfItems" right_name="user_deleteEnumRight"/>
					<AccountManagmentElement right_title="Add an enumeration" key_letter="O" :users="pageOfItems" right_name="user_addEnumRight"/>
					<AccountManagmentElement right_title="Make a quality Validation" key_letter="P" :users="pageOfItems" right_name="user_makeQualityValidationRight"/>
					<AccountManagmentElement right_title="Make a technical Validation" key_letter="Q" :users="pageOfItems" right_name="user_makeTechnicalValidationRight"/>
					<AccountManagmentElement right_title="Acces to user managment" key_letter="R" :users="pageOfItems" right_name="user_menuUserAcessRight"/>
					<AccountManagmentElement right_title="Update information" key_letter="S" :users="pageOfItems" right_name="user_updateInformationRight"/>
					<AccountManagmentElement right_title="Reform Data" key_letter="T" :users="pageOfItems" right_name="user_deleteDataSignedLinkedToEqOrEcmeRight"/>

				</b-row>
			</b-container>
			<jw-pagination class="eq_list_pagination" :pageSize=5 :items="users" @changePage="onChangePage"></jw-pagination>
		</div>
	</div>
</template>

<script>
import AccountManagmentElement from './AccountManagmentElement.vue'
export default {
	components:{
		AccountManagmentElement
	},
	data(){
		return{
			loaded:false,
			users:[],
			pageOfItems: [],
		}
	},
	created(){
		if(this.$userId.user_menuUserAcessRight!=true){
            this.$router.replace({ name: "home" })
        };
		axios.get('/users/send')
        .then (response=> {
            this.users=response.data;
            this.loaded=true;
			console.log(response.data)
		}) 
        .catch(error => console.log(error)) ;
		
	},

	methods:{
		onChangePage(pageOfItems) {
			// update page of items
			this.pageOfItems = pageOfItems;

		}
	},

	updated(){
		for(const user of this.users){
			console.log(user)
			if(user.user_makeEqOpValidationRight==true){
				document.getElementById('user_makeEqOpValidationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_personTrainedToGeneralPrinciplesOfEqManagementRight==true){
				document.getElementById('user_personTrainedToGeneralPrinciplesOfEqManagementRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_personTrainedToGeneralPrinciplesOfMMEManagementRight==true){
				document.getElementById('user_personTrainedToGeneralPrinciplesOfMMEManagementRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_resetUserPasswordRight==true){
				document.getElementById('user_resetUserPasswordRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_updateDataValidatedButNotSignedRight==true){
				document.getElementById('user_updateDataValidatedButNotSignedRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_updateDescriptiveLifeSheetDataSignedRight==true){
				document.getElementById('user_updateDescriptiveLifeSheetDataSignedRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_validateOtherDataRight==true){
				document.getElementById('user_validateOtherDataRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_updateDataInDraftRight==true){
				document.getElementById('user_updateDataInDraftRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_validateDescriptiveLifeSheetDataRight==true){
				document.getElementById('user_validateDescriptiveLifeSheetDataRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteDataNotValidatedLinkedToEqOrMmeRight==true){
				document.getElementById('user_deleteDataNotValidatedLinkedToEqOrMmeRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteDataValidatedLinkedToEqOrMmeRight==true){
				document.getElementById('user_deleteDataValidatedLinkedToEqOrMmeRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteEqOrMmeRight==true){
				document.getElementById('user_deleteEqOrMmeRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_updateEnumRight==true){
				document.getElementById('user_updateEnumRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteEnumRight==true){
				document.getElementById('user_deleteEnumRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_addEnumRight==true){
				document.getElementById('user_addEnumRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_makeQualityValidationRight==true){
				document.getElementById('user_makeQualityValidationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_makeTechnicalValidationRight==true){
				document.getElementById('user_makeTechnicalValidationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_menuUserAcessRight==true){
				document.getElementById('user_menuUserAcessRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_updateInformationRight==true){
				document.getElementById('user_updateInformationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteDataSignedLinkedToEqOrEcmeRight==true){
				document.getElementById('user_deleteDataSignedLinkedToEqOrEcmeRight'+user.id).setAttribute("checked", true)
			}


		}
	}

}
</script>

<style lang="scss">
	.user_table_container{
		min-width: 950px;
	}
	.toto{
		display: flexbox;
	}
</style>
<template>
	<div class="account_managment">
        <div v-if="loaded==false" >
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
        	<ErrorAlert ref="errorAlert"/>
        	<SuccesAlert ref="succesAlert"/>
			
			<b-container class="user_table_container" fluid="xl">
				<b-row>
					<b-col class="right_title">Right</b-col>
					<b-col class="right_name" v-for="(user, index) in pageOfItems" :key="index">
						<a href="#" @click="openUserUpdateModal(user.user_pseudo,user.user_firstName,user.user_lastName,user.user_initials,user.id,user.user_formationEqDate,user.user_formationMmeDate)">{{user.user_lastName}} {{user.user_firstName}}</a>
					</b-col>

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> User Management </b-col>  </b-row>
					

					<AccountManagmentElement right_title="Acces to user managment" key_letter="R" :users="pageOfItems" right_name="user_menuUserAcessRight"/>
					<AccountManagmentElement right_title="Reset user password" key_letter="D" :users="pageOfItems" right_name="user_resetUserPasswordRight"/>

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Data recording </b-col>  </b-row>

					<AccountManagmentElement right_title="Update data in draft or to be validated" key_letter="H" :users="pageOfItems" right_name="user_updateDataInDraftRight"/>
					<AccountManagmentElement right_title="Validate descriptive LifeSheet data " key_letter="I" :users="pageOfItems" right_name="user_validateDescriptiveLifeSheetDataRight"/>
					<AccountManagmentElement right_title="Update data validated but not signed" key_letter="E" :users="pageOfItems" right_name="user_updateDataValidatedButNotSignedRight" />
					<AccountManagmentElement right_title="Update descriptive LifeSheet of signed data" key_letter="F" :users="pageOfItems" right_name="user_updateDescriptiveLifeSheetDataSignedRight"/>
					<AccountManagmentElement right_title="Validate other data" key_letter="G" :users="pageOfItems" right_name="user_validateOtherDataRight"/>
					
					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Data Validation </b-col>  </b-row>

					<AccountManagmentElement right_title="Make a quality Validation" key_letter="P" :users="pageOfItems" right_name="user_makeQualityValidationRight"/>
					<AccountManagmentElement right_title="Make a technical Validation" key_letter="Q" :users="pageOfItems" right_name="user_makeTechnicalValidationRight"/>

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Enum Management </b-col>  </b-row>

					<AccountManagmentElement right_title="Update an enumeration" key_letter="M" :users="pageOfItems" right_name="user_updateEnumRight" />
					<AccountManagmentElement right_title="Delete an enumeration" key_letter="N" :users="pageOfItems" right_name="user_deleteEnumRight"/>
					<AccountManagmentElement right_title="Add an enumeration" key_letter="O" :users="pageOfItems" right_name="user_addEnumRight"/>

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Data suppression management </b-col>  </b-row>

					<AccountManagmentElement right_title="Delete not validated data of a MME or equipment" key_letter="J" :users="pageOfItems" right_name="user_deleteDataNotValidatedLinkedToEqOrMmeRight"/>
					<AccountManagmentElement right_title="Delete validated data of a MME or equipment" key_letter="K" :users="pageOfItems" right_name="user_deleteDataValidatedLinkedToEqOrMmeRight"/>
					<AccountManagmentElement right_title="Delete a MME or equipment" key_letter="L" :users="pageOfItems" right_name="user_deleteEqOrMmeRight"/>
					<AccountManagmentElement right_title="Delete signed data" key_letter="U" :users="pageOfItems" right_name="user_deleteDataSignedLinkedToEqOrMmeRight"/>
					<AccountManagmentElement right_title="Reform data" key_letter="T" :users="pageOfItems" right_name="user_makeReformRight"/>

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Dictionnary management </b-col>  </b-row>

					<AccountManagmentElement right_title="Update information" key_letter="S" :users="pageOfItems" right_name="user_updateInformationRight"/>
					

					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Formation management </b-col>  </b-row>

					<AccountManagmentElement right_title="Person trained to general principles of equipment managment" :training="true" training_type="equipment" right_date='user_formationEqDate' key_letter="B" :users="pageOfItems" right_name="user_personTrainedToGeneralPrinciplesOfEqManagementRight"/>
					<AccountManagmentElement right_title="Person trained to general principles of MME managment"  :training="true" training_type="mme" right_date='user_formationMmeDate' key_letter="C" :users="pageOfItems" right_name="user_personTrainedToGeneralPrinciplesOfMMEManagementRight"/>
	
					<b-row><div class="w-100 row_right_tab"></div>
					<b-col class="right_title"> Life Event Management </b-col>  </b-row>
					
					<AccountManagmentElement right_title="state management" key_letter="V" :users="pageOfItems" right_name="user_declareNewStateRight"/>
					<AccountManagmentElement right_title="Approve an equipment maintenance record" key_letter="W" :users="pageOfItems" right_name="user_makeEqRespValidationRight"/>
					<AccountManagmentElement right_title="Make equipment operation validation" key_letter="A"  :users="pageOfItems" right_name="user_makeEqOpValidationRight"/>
					<AccountManagmentElement right_title="Approve an mme verification or maintenance record" key_letter="X" :users="pageOfItems" right_name="user_makeMmeRespValidationRight"/>
					<AccountManagmentElement right_title="Make mme verification validation" key_letter="Y" :users="pageOfItems" right_name="user_makeMmeOpValidationRight"/>

					<div class="w-100 row_right_tab"></div>
				</b-row>
			</b-container>
			<b-modal :id="`modal-updateUser-${_uid}`" @hidden="resetModal" @ok="handleOkUpdate" title="User info">
				<div>
					<form @keydown="clearError">
						<InputTextForm  inputClassName="form-control" :Errors="errors.user_userName" name="user_userName" label="Username :" v-model="modal_userName" :isDisabled="true"/>
						<InputTextForm  inputClassName="form-control" :Errors="errors.user_firstName" name="user_firstName" label="First :" v-model="modal_firstName" :isDisabled="true"/>
						<InputTextForm  inputClassName="form-control" :Errors="errors.user_lastName" name="user_lastName" label="Last :" v-model="modal_lastName" :isDisabled="true"/>
						<InputTextForm  inputClassName="form-control" :Errors="errors.user_initials" name="user_initials" label="Initial :" v-model="modal_initials"/>
						<div class="input-group">
							<InputTextForm inputClassName="form-control" :Errors="errors.user_endDate" name="user_endDate" label="End date :" :isDisabled="true"  isRequired v-model="user_endDate"/>
							<InputDateForm inputClassName="form-control  date-selector"  name="selected_endDate"  isRequired v-model="selected_endDate"/>
						</div>
						<div class="input-group">
							<InputTextForm inputClassName="form-control" :Errors="errors.user_formationEqDate" name="user_formationEqDate" label="Eq Formation Date :" :isDisabled="true"  isRequired v-model="user_formationEqDate"/>
							<InputDateForm inputClassName="form-control  date-selector"  name="selected_formationEqDate"  isRequired v-model="selected_formationEqDate"/>
						</div>
						<div class="input-group">
							<InputTextForm inputClassName="form-control" :Errors="errors.user_formationMmeDate" name="user_formationMmeDate" label="MME Formation Date :" :isDisabled="true"  isRequired v-model="user_formationMmeDate"/>
							<InputDateForm inputClassName="form-control  date-selector"  name="selected_formationMmeDate"  isRequired v-model="selected_formationMmeDate"/>
						</div>
						<InputPasswordForm  inputClassName="form-control" :Errors="errors.user_password" name="user_password" label="Change the current password :" v-model="modal_password"/>
						<InputPasswordForm  inputClassName="form-control" :Errors="errors.user_confirmation_password" name="user_confirmation_password" label="Confirm the password :" v-model="modal_confirmation_password"/>
					</form>
                    
				</div>
			</b-modal>

			<jw-pagination class="eq_list_pagination" :pageSize=5 :items="users" @changePage="onChangePage"></jw-pagination>
		</div>
	</div>
</template>

<script>
import moment from 'moment'
import InputTextForm from '../input/InputTextForm.vue'
import InputDateForm from '../input/InputDateForm.vue'
import InputPasswordForm from '../input/InputPasswordForm.vue'
import AccountManagmentElement from './AccountManagmentElement.vue'
import ErrorAlert from '../alert/ErrorAlert.vue'
import SuccesAlert from '../alert/SuccesAlert.vue'
export default {
	components:{
		AccountManagmentElement,
		InputPasswordForm,
		InputTextForm,
		InputDateForm,
		ErrorAlert,
		SuccesAlert
	},
	data(){
		return{
			loaded:false,
			users:[],
			pageOfItems: [],
			compId:this._uid,
			modal_id:'',
			modal_userName:'',
			modal_firstName:'',
			modal_lastName:'',
			modal_initials:'',
			modal_password:'',
			selected_endDate:null,
			user_endDate:'',
			user_formationEqDate:'',
			selected_formationEqDate:null,
			user_formationMmeDate:'',
			selected_formationMmeDate:null,
			modal_confirmation_password:'',
			errors:[],									

		}
	},
	created(){
		if(this.$userId.user_menuUserAcessRight!=true){
            this.$router.replace({ name: "home" })
        };
		axios.get('/users/send')
        .then (response=> {
            this.users=response.data;
			for (var i=0;i<this.users.length;i++) {
                if(this.users[i].user_formationEqDate!=null){
                    this.users[i].user_formationEqDate=moment(this.users[i].user_formationEqDate).format('D MMM YYYY'); 
                }
				if(this.users[i].user_formationMmeDate!=null){
                    this.users[i].user_formationMmeDate=moment(this.users[i].user_formationMmeDate).format('D MMM YYYY'); 
                }
            }
            this.loaded=true;
			console.log(response.data)
		}) 
        .catch(error => console.log(error)) ;
	},

	methods:{
		onChangePage(pageOfItems) {
			// update page of items
			this.pageOfItems = pageOfItems;

		},
		openUserUpdateModal(user,first,last,initials,id,formation_eq_date,formation_mme_date){
			this.modal_userName=user;
			this.modal_firstName=first;
			this.modal_lastName=last;
			this.modal_initials=initials;
			this.modal_id=id
			this.user_formationEqDate=formation_eq_date;
			this.user_formationMmeDate=formation_mme_date;
			this.$bvModal.show(`modal-updateUser-${this.compId}`)
		},
		handleOkUpdate(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
			if(this.modal_password!='' && this.$userId.user_resetUserPasswordRight==false){
				this.$refs.errorAlert.showAlert("You don't have the right to change an other user password");
				return;
			}	
			var postUrlUpdate = (id) => `/user/update/infos/${id}`;
			axios.post(postUrlUpdate(this.modal_id),{
					user_initials:this.modal_initials,
                    user_password:this.modal_password,
					user_confirmation_password:this.modal_confirmation_password,
					user_resetUserPasswordRight:this.$userId.user_resetUserPasswordRight,
					user_endDate:this.selected_endDate,
					user_formationEqDate:this.selected_formationEqDate,
					user_formationMmeDate:this.selected_formationMmeDate,

					//id of user who change the info
					user_id:this.$userId.id
			})
			.then(response =>{           
				// Hide the modal manually
				console.log("updated")
				this.$bvModal.hide(bvModalEvent.target.id);
				this.resetModal()
				this.$refs.succesAlert.showAlert("Data updated succesfully");
			})
			.catch(error =>{
				this.errors=error.response.data.errors;
			});
        },
		resetModal(){
			this.modal_userName='';
			this.modal_firstName='';
			this.modal_lastName='';
			this.modal_initials='';
			this.modal_password='';
			this.modal_confirmation_password='';
			this.selected_endDate=null,
			this.user_endDate='',
			this.modal_id='';
			this.errors={};
		},
		/*Clear all the error of the targeted field*/
        clearError(event){
            delete this.errors[event.target.name];
        },

	},

	updated(){
        if(this.selected_endDate!==null){
            this.user_endDate=moment(this.selected_endDate).format('D MMM YYYY'); 
        }
		if(this.selected_formationEqDate!==null){
            this.user_formationEqDate=moment(this.selected_formationEqDate).format('D MMM YYYY'); 
        }
		if(this.selected_formationMmeDate!==null){
            this.user_formationMmeDate=moment(this.selected_formationMmeDate).format('D MMM YYYY'); 
        }
		for(const user of this.pageOfItems){
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
			if(user.user_makeReformRight==true){
				document.getElementById('user_makeReformRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_deleteDataSignedLinkedToEqOrMmeRight==true){
				document.getElementById('user_deleteDataSignedLinkedToEqOrMmeRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_declareNewStateRight==true){
				document.getElementById('user_declareNewStateRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_makeEqRespValidationRight==true){
				document.getElementById('user_makeEqRespValidationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_makeMmeRespValidationRight==true){
				document.getElementById('user_makeMmeRespValidationRight'+user.id).setAttribute("checked", true)
			}
			if(user.user_makeMmeOpValidationRight==true){
				document.getElementById('user_makeMmeOpValidationRight'+user.id).setAttribute("checked", true)
			}
		}
	}

}
</script>

<style lang="scss">
	.user_table_container{
		min-width: 950px;
		margin-top: 50px;

		.right_name{
			border-top: solid 1px lightgray;
			border-left: solid 1px lightgray;
			
			margin-left:-24px ;
		}
		.right_title{
			border-top: solid 1px lightgray;
			border-left: solid 1px lightgray;
			
		}
		.row_right_tab{
			border: solid 1px lightgray;
		
		}
	}

</style>
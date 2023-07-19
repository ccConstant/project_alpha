<!--File name : AccountManagement.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the account management part-->

<template>
    <div class="account_managment">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <ErrorAlert ref="errorAlert"/>
            <SuccesAlert ref="succesAlert"/>

            <b-container class="user_table_container" fluid="xl">
                <b-row>
                    <b-col class="right_title">Right</b-col>
                    <b-col v-for="(user, index) in pageOfItems" :key="index" class="right_name">
                        <a href="#"
                           @click="openUserUpdateModal(user.user_pseudo,user.user_firstName,user.user_lastName,user.user_initials,user.id,user.user_formationEqDate,user.user_formationMmeDate)">{{ user.user_lastName }}
                            {{ user.user_firstName }}</a>
                    </b-col>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> User Management</b-col>
                    </b-row>


                    <AccountManagmentElement :users="pageOfItems" key_letter="R" right_name="user_menuUserAcessRight"
                                             right_title="Acces to user managment"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="D" right_name="user_resetUserPasswordRight"
                                             right_title="Reset user password"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Data recording</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems" key_letter="H"
                                             right_name="user_updateDataInDraftRight" right_title="Update data in draft or to be validated"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="I"
                                             right_name="user_validateDescriptiveLifeSheetDataRight"
                                             right_title="Validate descriptive LifeSheet data "/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="E"
                                             right_name="user_updateDataValidatedButNotSignedRight"
                                             right_title="Update data validated but not signed"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="F"
                                             right_name="user_updateDescriptiveLifeSheetDataSignedRight"
                                             right_title="Update descriptive LifeSheet of signed data"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="G" right_name="user_validateOtherDataRight"
                                             right_title="Validate other data"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Data Validation</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems" key_letter="P" right_name="user_makeQualityValidationRight"
                                             right_title="Make a quality Validation"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="Q"
                                             right_name="user_makeTechnicalValidationRight" right_title="Make a technical Validation"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Enum Management</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems" key_letter="M" right_name="user_updateEnumRight"
                                             right_title="Update an enumeration"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="N" right_name="user_deleteEnumRight"
                                             right_title="Delete an enumeration"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="O" right_name="user_addEnumRight"
                                             right_title="Add an enumeration"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Data suppression management</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems"
                                             key_letter="J" right_name="user_deleteDataNotValidatedLinkedToEqOrMmeRight"
                                             right_title="Delete not validated data of a MME or equipment"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="K"
                                             right_name="user_deleteDataValidatedLinkedToEqOrMmeRight"
                                             right_title="Delete validated data of a MME or equipment"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="L" right_name="user_deleteEqOrMmeRight"
                                             right_title="Delete a MME or equipment"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="U" right_name="user_deleteDataSignedLinkedToEqOrMmeRight"
                                             right_title="Delete signed data"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="T" right_name="user_makeReformRight"
                                             right_title="Reform data"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Dictionnary management</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems" key_letter="S" right_name="user_updateInformationRight"
                                             right_title="Update information"/>


                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Formation management</b-col>
                    </b-row>

                    <AccountManagmentElement :training="true"
                                             :users="pageOfItems" key_letter="B"
                                             right_date='user_formationEqDate' right_name="user_personTrainedToGeneralPrinciplesOfEqManagementRight" right_title="Person trained to general principles of equipment managment"
                                             training_type="equipment"/>
                    <AccountManagmentElement :training="true"
                                             :users="pageOfItems" key_letter="C" right_date='user_formationMmeDate'
                                             right_name="user_personTrainedToGeneralPrinciplesOfMMEManagementRight" right_title="Person trained to general principles of MME managment"
                                             training_type="mme"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Life Event Management</b-col>
                    </b-row>

                    <AccountManagmentElement :users="pageOfItems" key_letter="V" right_name="user_declareNewStateRight"
                                             right_title="state management"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="W"
                                             right_name="user_makeEqRespValidationRight" right_title="Approve an equipment maintenance record"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="A"
                                             right_name="user_makeEqOpValidationRight" right_title="Make equipment operation validation"/>
                    <AccountManagmentElement :users="pageOfItems"
                                             key_letter="X" right_name="user_makeMmeRespValidationRight"
                                             right_title="Approve an mme verification or maintenance record"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="Y"
                                             right_name="user_makeMmeOpValidationRight" right_title="Make mme verification validation"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Article Data Management</b-col>
                    </b-row>
                    <AccountManagmentElement :users="pageOfItems" key_letter="Z" right_name="user_SW03_addArticle"
                                             right_title="Add an article"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AA" right_name="user_SW03_updateArticle"
                                             right_title="Update an article"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AB" right_name="user_SW03_updateArticleSigned"
                                             right_title="Update a signed article"/>

                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Supplier Data Management</b-col>
                    </b-row>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AC" right_name="user_SW03_addSupplier"
                                             right_title="Add a supplier"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AD" right_name="user_SW03_updateSupplier"
                                             right_title="Update a supplier"/>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AE" right_name="user_SW03_updateSupplierSigned"
                                             right_title="Update a signed supplier"/>
                    <b-row>
                        <div class="w-100 row_right_tab"></div>
                        <b-col class="right_section"> Validation SW03</b-col>
                    </b-row>
                    <AccountManagmentElement :users="pageOfItems" key_letter="AF" right_name="user_SW03_technicalValidate"
                                             right_title="Technical Right"/>
                </b-row>
                <div class="w-100 row_right_tab"></div>
            </b-container>
            <b-modal :id="`modal-updateUser-${_uid}`" title="User info" @hidden="resetModal" @ok="handleOkUpdate">
                <div>
                    <form @keydown="clearError">
                        <InputTextForm v-model="modal_userName" :Errors="errors.user_userName" :isDisabled="true"
                                       inputClassName="form-control" label="Username :" name="user_userName"/>
                        <InputTextForm v-model="modal_firstName" :Errors="errors.user_firstName"
                                       :isDisabled="true" inputClassName="form-control" label="First :"
                                       name="user_firstName"/>
                        <InputTextForm v-model="modal_lastName" :Errors="errors.user_lastName" :isDisabled="true"
                                       inputClassName="form-control" label="Last :" name="user_lastName"/>
                        <InputTextForm v-model="modal_initials" :Errors="errors.user_initials" inputClassName="form-control"
                                       label="Initial :" name="user_initials"/>
                        <div class="input-group">
                            <InputTextForm v-model="user_endDate" :Errors="errors.user_endDate"
                                           :isDisabled="true" inputClassName="form-control" isRequired label="End date :"
                                           name="user_endDate"/>
                            <InputDateForm v-model="selected_endDate" inputClassName="form-control  date-selector"
                                           isRequired name="selected_endDate"/>
                        </div>
                        <div class="input-group">
                            <InputTextForm v-model="user_formationEqDate" :Errors="errors.user_formationEqDate"
                                           :isDisabled="true" inputClassName="form-control" isRequired
                                           label="Eq Formation Date :" name="user_formationEqDate"/>
                            <InputDateForm v-model="selected_formationEqDate" inputClassName="form-control  date-selector"
                                           isRequired name="selected_formationEqDate"/>
                        </div>
                        <div class="input-group">
                            <InputTextForm v-model="user_formationMmeDate" :Errors="errors.user_formationMmeDate"
                                           :isDisabled="true" inputClassName="form-control" isRequired
                                           label="MME Formation Date :" name="user_formationMmeDate"/>
                            <InputDateForm v-model="selected_formationMmeDate" inputClassName="form-control  date-selector"
                                           isRequired name="selected_formationMmeDate"/>
                        </div>
                        <InputPasswordForm v-model="modal_password" :Errors="errors.user_password"
                                           inputClassName="form-control" label="Change the current password :"
                                           name="user_password"/>
                        <InputPasswordForm v-model="modal_confirmation_password" :Errors="errors.user_confirmation_password"
                                           inputClassName="form-control" label="Confirm the password :"
                                           name="user_confirmation_password"/>
                    </form>

                </div>
            </b-modal>

            <jw-pagination :items="users" :pageSize=5 class="eq_list_pagination"
                           @changePage="onChangePage"></jw-pagination>
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
    components: {
        AccountManagmentElement,
        InputPasswordForm,
        InputTextForm,
        InputDateForm,
        ErrorAlert,
        SuccesAlert
    },
    data() {
        return {
            loaded: false,
            users: [],
            pageOfItems: [],
            compId: this._uid,
            modal_id: '',
            modal_userName: '',
            modal_firstName: '',
            modal_lastName: '',
            modal_initials: '',
            modal_password: '',
            selected_endDate: null,
            user_endDate: '',
            user_formationEqDate: '',
            selected_formationEqDate: null,
            user_formationMmeDate: '',
            selected_formationMmeDate: null,
            modal_confirmation_password: '',
            errors: [],

        }
    },
    created() {
        if (this.$userId.user_menuUserAcessRight != true) {
            this.$router.replace({name: "home"})
        }

        axios.get('/users/send')
            .then(response => {
                this.users = response.data;
                for (var i = 0; i < this.users.length; i++) {
                    if (this.users[i].user_formationEqDate != null) {
                        this.users[i].user_formationEqDate = moment(this.users[i].user_formationEqDate).format('D MMM YYYY');
                    }
                    if (this.users[i].user_formationMmeDate != null) {
                        this.users[i].user_formationMmeDate = moment(this.users[i].user_formationMmeDate).format('D MMM YYYY');
                    }
                }
                this.loaded = true;
            }).catch(error => {
        });
    },

    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;

        },
        openUserUpdateModal(user, first, last, initials, id, formation_eq_date, formation_mme_date) {
            this.modal_userName = user;
            this.modal_firstName = first;
            this.modal_lastName = last;
            this.modal_initials = initials;
            this.modal_id = id
            this.user_formationEqDate = formation_eq_date;
            this.user_formationMmeDate = formation_mme_date;
            this.$bvModal.show(`modal-updateUser-${this.compId}`)
        },
        handleOkUpdate(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            if (this.modal_password != '' && this.$userId.user_resetUserPasswordRight == false) {
                this.$refs.errorAlert.showAlert("You don't have the right to change an other user password");
                return;
            }
            var postUrlUpdate = (id) => `/user/update/infos/${id}`;
            axios.post(postUrlUpdate(this.modal_id), {
                user_initials: this.modal_initials,
                user_password: this.modal_password,
                user_confirmation_password: this.modal_confirmation_password,
                user_resetUserPasswordRight: this.$userId.user_resetUserPasswordRight,
                user_endDate: this.selected_endDate,
                user_formationEqDate: this.selected_formationEqDate,
                user_formationMmeDate: this.selected_formationMmeDate,

                //id of user who change the info
                user_id: this.$userId.id
            })
                .then(response => {
                    // Hide the modal manually
                    this.$bvModal.hide(bvModalEvent.target.id);
                    this.resetModal()
                    this.$refs.succesAlert.showAlert("Data updated succesfully");
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
        },
        resetModal() {
            this.modal_userName = '';
            this.modal_firstName = '';
            this.modal_lastName = '';
            this.modal_initials = '';
            this.modal_password = '';
            this.modal_confirmation_password = '';
            this.selected_endDate = null,
                this.user_endDate = '',
                this.modal_id = '';
            this.errors = {};
        },
        /*Clear all the error of the targeted field*/
        clearError(event) {
            delete this.errors[event.target.name];
        },

    },

    updated() {
        if (this.selected_endDate !== null) {
            this.user_endDate = moment(this.selected_endDate).format('D MMM YYYY');
        }
        if (this.selected_formationEqDate !== null) {
            this.user_formationEqDate = moment(this.selected_formationEqDate).format('D MMM YYYY');
        }
        if (this.selected_formationMmeDate !== null) {
            this.user_formationMmeDate = moment(this.selected_formationMmeDate).format('D MMM YYYY');
        }
        for (const user of this.pageOfItems) {
            if (user.user_makeEqOpValidationRight == true) {
                document.getElementById('user_makeEqOpValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_personTrainedToGeneralPrinciplesOfEqManagementRight == true) {
                document.getElementById('user_personTrainedToGeneralPrinciplesOfEqManagementRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_personTrainedToGeneralPrinciplesOfMMEManagementRight == true) {
                document.getElementById('user_personTrainedToGeneralPrinciplesOfMMEManagementRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_resetUserPasswordRight == true) {
                document.getElementById('user_resetUserPasswordRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_updateDataValidatedButNotSignedRight == true) {
                document.getElementById('user_updateDataValidatedButNotSignedRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_updateDescriptiveLifeSheetDataSignedRight == true) {
                document.getElementById('user_updateDescriptiveLifeSheetDataSignedRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_validateOtherDataRight == true) {
                document.getElementById('user_validateOtherDataRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_updateDataInDraftRight == true) {
                document.getElementById('user_updateDataInDraftRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_validateDescriptiveLifeSheetDataRight == true) {
                document.getElementById('user_validateDescriptiveLifeSheetDataRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteDataNotValidatedLinkedToEqOrMmeRight == true) {
                document.getElementById('user_deleteDataNotValidatedLinkedToEqOrMmeRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteDataValidatedLinkedToEqOrMmeRight == true) {
                document.getElementById('user_deleteDataValidatedLinkedToEqOrMmeRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteEqOrMmeRight == true) {
                document.getElementById('user_deleteEqOrMmeRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_updateEnumRight == true) {
                document.getElementById('user_updateEnumRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteEnumRight == true) {
                document.getElementById('user_deleteEnumRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_addEnumRight == true) {
                document.getElementById('user_addEnumRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeQualityValidationRight == true) {
                document.getElementById('user_makeQualityValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeTechnicalValidationRight == true) {
                document.getElementById('user_makeTechnicalValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_menuUserAcessRight == true) {
                document.getElementById('user_menuUserAcessRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_updateInformationRight == true) {
                document.getElementById('user_updateInformationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteDataSignedLinkedToEqOrEcmeRight == true) {
                document.getElementById('user_deleteDataSignedLinkedToEqOrEcmeRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeReformRight == true) {
                document.getElementById('user_makeReformRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_deleteDataSignedLinkedToEqOrMmeRight == true) {
                document.getElementById('user_deleteDataSignedLinkedToEqOrMmeRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_declareNewStateRight == true) {
                document.getElementById('user_declareNewStateRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeEqRespValidationRight == true) {
                document.getElementById('user_makeEqRespValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeMmeRespValidationRight == true) {
                document.getElementById('user_makeMmeRespValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_makeMmeOpValidationRight == true) {
                document.getElementById('user_makeMmeOpValidationRight' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_addArticle == true) {
                document.getElementById('user_SW03_addArticle' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_updateArticle == true) {
                document.getElementById('user_SW03_updateArticle' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_updateArticleSigned == true) {
                document.getElementById('user_SW03_updateArticleSigned' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_addSupplier == true) {
                document.getElementById('user_SW03_addSupplier' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_updateSupplier == true) {
                document.getElementById('user_SW03_updateSupplier' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_updateSupplierSigned == true) {
                document.getElementById('user_SW03_updateSupplierSigned' + user.id).setAttribute("checked", true)
            }
            if (user.user_SW03_technicalValidate == true) {
                document.getElementById('user_SW03_technicalValidate' + user.id).setAttribute("checked", true)
            }
        }
    }

}
</script>

<style lang="scss">
.user_table_container {
    min-width: 950px;
    margin-top: 50px;

    .right_name {
        border-top: solid 1px lightgray;
        border-left: solid 1px lightgray;
        margin-left: -24px;
    }

    .right_title {
        border-top: solid 1px lightgray;
        border-left: solid 1px lightgray;
    }

    .right_section {
        border-top: solid 1px lightgray;
        border-left: solid 1px lightgray;
        background: deepskyblue;
    }

    .row_right_tab {
        border: solid 1px lightgray;

    }
}


</style>

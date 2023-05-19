<!--File name : EnumElement.vue-->
<!--Creation date : 26 Apr 2023-->
<!--Update date : 27 Apr 2023-->
<!--Vue Component to show and update a list of elements-->

<template>
    <div v-if="enums">
        <h2 class="enumTitle">{{ this.title }}</h2>
        <InputInfo v-if="returnedText_info!=null " :info="returnedText_info"/>
        <ErrorAlert ref="errorAlert"/>
        <ul>
            <li v-for="(element,index) in enums" :key="index" class="list-group-item">
                <div class="enum_name">
                    {{ element.value }}
                </div>
                <div v-if="update_enum_right==true" class="enum_update">
                    <a v-b-modal="`modal-updateEnum-${_uid}`" href=# @click="sendEnumInfo(element)">Update</a>
                </div>
                <div v-else class="enum_update">
                    <a @click="showRightAlert">Update</a>
                </div>
                <div v-if="element.caduc !== undefined">
                    <div v-if="element.caduc === 0">
                        <div v-if="delete_enum_right==true" class="enum_delete">
                            <a href=# @click="DisableEnum(element)">Disable</a>
                        </div>
                        <div v-else class="enum_delete">
                            <a @click="showRightAlert">Disable</a>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="delete_enum_right==true" class="enum_delete">
                            <a v-b-modal="`modal-enableEnum-${_uid}`" href=# @click="sendEnumInfo(element)">Enable</a>
                        </div>
                        <div v-else class="enum_delete">
                            <a @click="showRightAlert">Enable</a>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div v-if="delete_enum_right==true" class="enum_delete">
                        <a v-b-modal="`modal-deleteEnum-${_uid}`" href=# @click="sendEnumInfo(element)">Delete</a>
                    </div>
                    <div v-else class="enum_delete">
                        <a @click="showRightAlert">Delete</a>
                    </div>
                </div>
            </li>
        </ul>
        <div>
            <div v-if="add_enum_right==true">
                <b-button variant="primary" @click="$bvModal.show(`modal-addEnum-${_uid}`)">Add a new enum</b-button>
            </div>
            <div v-else>
                <b-button disabled variant="primary">Add a new enum</b-button>
                <p class="enum_add_right_red"> You dont have the right to add a new enum.</p>
            </div>
            <b-modal :id="`modal-addEnum-${_uid}`" ref="modal" :title="`Submit Your ${title} Enum`" @hidden="resetModal"
                     @ok="handleOkAdd" @show="resetModal">
                <form ref="form" @submit.stop.prevent="handleSubmitAdd">
                    <b-form-group :state="enumState" invalid-feedback="Enum is required" label="Enum"
                                  label-for="enum-input">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
            <b-modal :id="`modal-updateEnum-${_uid}`" ref="modal" :title="`Update Your ${title} Enum`"
                     @hidden="resetModal" @ok="handleOkUpdate" @show="resetModal">
                <form ref="form" @submit.stop.prevent="handleSubmitVerifUpdate">
                    <b-form-group :state="enumState" invalid-feedback="Enum is required" label="Enum"
                                  label-for="enum-input">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
            <b-modal :id="`modal-disableEnum-${_uid}`" ref="modal" :title="`Disable Your ${title} Enum`"
                     @hidden="resetModal" @ok="handleOkDisable" @show="resetModal">
                <form ref="form" @submit.stop.prevent="handleSubmitVerifDisable">
                    <b-form-group :state="enumState" invalid-feedback="Enum is required" label="Enum"
                                  label-for="enum-input">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
            <b-modal :id="`modal-enableEnum-${_uid}`" @ok="enableConfirmation">
                <p class="my-4">Are you sure you want to enable {{ returnedEnum }} from {{ this.title }} enum ?</p>
            </b-modal>
            <b-modal :id="`modal-deleteEnum-${_uid}`" @ok="deleteConfirmation">
                <p class="my-4">Are you sure you want to delete {{ returnedEnum }} from {{ this.title }} enum ?</p>
            </b-modal>
            <b-modal :id="`modal-enum-update-approved-${_uid}`" @ok="handleSubmitUpdate()">
                <div v-if="article_concerned !== null">
                    <div v-if="article_concerned.length !== 0" class="my-4">
                        <p>Are you sure you want to update this Enum? The following not signed articles will be
                            concerned : </p>
                        <p v-for="(element,index) in this.article_concerned" :key="index">
                            Reference : {{ element.artFam_ref }}, Designation : {{ element.artFam_design }}
                        </p>
                    </div>
                </div>
                <div v-if="approved_articles !== null">
                    <div v-if="approved_articles.length !== 0" class="my-4">
                        <p>You will have to re validate the following articles : </p>
                        <p v-for="(element,index) in this.article_concerned" :key="index">
                            Reference : {{ element.artFam_ref }}, Designation : {{ element.artFam_design }}
                        </p>
                        <InputTextForm v-model="reason" :Errors="errors.history_reason" inputClassName="form-control"
                                       label="Reason" name="reason"/>
                    </div>
                </div>
            </b-modal>
            <b-modal :id="`modal-enum-disable-approved-${_uid}`" @ok="handleSubmitDisable()">
                <div v-if="article_concerned !== null">
                    <div v-if="article_concerned.length !== 0" class="my-4">
                        <p>Are you sure you want to update this Enum? The following not signed articles will be
                            concerned : </p>
                        <p v-for="(element,index) in this.article_concerned" :key="index">
                            Reference : {{ element.artFam_ref }}, Designation : {{ element.artFam_design }}
                        </p>
                    </div>
                </div>
                <div v-if="approved_articles !== null">
                    <div v-if="approved_articles.length !== 0" class="my-4">
                        <p>You will have to re validate the following articles : </p>
                        <p v-for="(element,index) in this.article_concerned" :key="index">
                            Reference : {{ element.artFam_ref }}, Designation : {{ element.artFam_design }}
                        </p>
                        <InputTextForm v-model="reason" :Errors="errors.history_reason" inputClassName="form-control"
                                       label="Reason" name="reason"/>
                    </div>
                </div>
            </b-modal>
        </div>

    </div>

</template>

<script>
import ErrorAlert from '../../alert/ErrorAlert.vue'
import InputInfo from '../../input/InputInfo.vue'
import InputTextForm from '../../input/InputTextForm.vue'

export default {
    components: {
        ErrorAlert,
        InputInfo,
        InputTextForm
    },
    props: {
        enumList: {
            type: Array
        },
        title: {
            type: String
        },
        url: {
            type: String
        },
        error_name: {
            type: String
        },
        enum_value: {
            type: String
        },
        info_text: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            returnedEnum: '',
            enumState: null,
            compId: this._uid,
            sendedEnumValue: '',
            sendedEnumId: null,
            enums: this.enumList,
            dismissSecs: 5,
            dismissCountDown: 0,
            showDismissibleAlert: false,
            returnedText_info: this.info_text,
            delete_enum_right: this.$userId.user_deleteEnumRight,
            add_enum_right: this.$userId.user_addEnumRight,
            update_enum_right: this.$userId.user_updateEnumRight,
            approved_articles: null,
            approved_mmes: null,
            article_concerned: null,
            mmes_concerned: null,
            enumId: '',
            reason: '',
            errors: {},
        }
    },
    methods: {
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            this.enumState = valid
            return valid
        },
        resetModal() {
            this.enumState = null

        },
        handleOkAdd(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            this.handleSubmitAdd()
        },
        handleSubmitAdd() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            const postUrlAdd = (url) => `${url}add`;
            axios.post(postUrlAdd(this.url), {
                value: this.returnedEnum,
            })
                .then(response => window.location.reload())
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                });
            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide(`modal-addEnum-${this.compId}`)
            })
        },
        handleOkUpdate(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            this.handleSubmitVerifUpdate()
        },
        handleOkDisable(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            this.handleSubmitVerifDisable()
        },
        handleSubmitVerifUpdate() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            const postUrlAdd = (url, id) => `${url}verif/${id}`;
            axios.post(postUrlAdd(this.url, this.sendedEnumId), {
                value: this.returnedEnum,
            })
                .then(response => {
                    this.sendedEnumId = response.data
                    const postUrlAnalyze = (url, id) => `${url}sendUsage/${id}`;
                    axios.get(postUrlAnalyze(this.url, this.sendedEnumId))
                        .then(response => {
                            if (response.data.signed_articles.length !== 0) {
                                this.approved_articles = response.data.signed_articles;
                            }
                            if (response.data.articles.length !== 0){
                                this.article_concerned = response.data.articles;
                            }
                            this.enumId = response.data.id;
                            this.$bvModal.show(`modal-enum-update-approved-${this.compId}`);
                        })
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                });

            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide(`modal-updateEnum-${this.compId}`);
                this.sendedEnumId = null;
            })
        },
        handleSubmitVerifDisable() {
            const postUrlAdd = (url, id) => `${url}verif/${id}`;
            axios.post(postUrlAdd(this.url, this.sendedEnumId), {
                value: this.returnedEnum,
            })
                .then(response => {
                    this.sendedEnumId = response.data
                    const postUrlAnalyze = (url, id) => `${url}sendUsage/${id}`;
                    axios.get(postUrlAnalyze(this.url, this.sendedEnumId))
                        .then(response => {
                            if (response.data.signed_articles.length !== 0) {
                                this.approved_articles = response.data.signed_articles;
                            }
                            if (response.data.articles.length !== 0) {
                                this.article_concerned = response.data.articles;
                            }
                            this.enumId = response.data.id;
                            this.$bvModal.show(`modal-enum-disable-approved-${this.compId}`);
                        })
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                });
        },
        handleSubmitUpdate() {
            const postUrlUpdate = (url, id) => `${url}update/${id}`;
            axios.post(postUrlUpdate(this.url, this.sendedEnumId), {
                value: this.returnedEnum,
                /*validated_eq: this.approved_articles,
                validated_mme: this.approved_mmes,
                history_reasonUpdate: this.reason,*/
            })
                .then(response => {
                    window.location.reload();
                    this.enumId = null;
                    this.approved_articles = null;
                    this.approved_mmes = null;
                    this.article_concerned = null;
                    this.mmes_concerned = null;
                    this.reason = "";
                })
                .catch(error => {
                });
        },
        handleSubmitDisable() {
            const postUrlDisable = (url, id) => `${url}disable/${id}`;
            axios.post(postUrlDisable(this.url, this.sendedEnumId))
                .then(response => {
                    this.sendedEnumId = null;
                    window.location.reload();
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                });
        },
        enableConfirmation() {
            const postUrlAdd = (url, id) => `${url}enable/${id}`;
            axios.post(postUrlAdd(this.url, this.sendedEnumId), {})
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name])
                });
            this.$nextTick(() => {
                this.sendedEnumId = null;
            })
        },
        deleteConfirmation() {
            const postUrlAdd = (url, id) => `${url}delete/${id}`;
            axios.post(postUrlAdd(this.url, this.sendedEnumId), {})
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name])
                });
            this.$nextTick(() => {
                this.sendedEnumId = null;
            })
        },
        sendEnumInfo(element) {
            this.returnedEnum = element.value;
            this.sendedEnumId = element.id;
        },
        DisableEnum(element) {
            this.sendEnumInfo(element);
            this.handleSubmitVerifDisable();
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showAlert() {
            this.dismissCountDown = this.dismissSecs
        },
        showRightAlert() {
            this.$refs.errorAlert.showAlert("You don't have the right")
        }
    },
    created() {
    }
}
</script>

<style lang="scss">
.enumTitle {
    display: inline-block;
}

.list-group-item {
    .enum_name {
        display: inline-block;
    }

    .enum_update {
        display: inline-block;
    }

    .enum_delete {
        display: inline-block;
    }
}

.input-reason {
    width: 300px;
}

.enum_add_right_red {
    color: red;
}
</style>

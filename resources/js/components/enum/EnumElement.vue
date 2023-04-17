<!--File name : EnumElement.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component to show and update a list of elements-->

<template>
    <div v-if="enums">
        <h2 class="enumTitle">{{this.title}}</h2>
        <InputInfo :info="returnedText_info" v-if="returnedText_info!=null "/>
            <ErrorAlert ref="errorAlert"/>
        <ul>
            <li class="list-group-item" v-for="(element,index) in enums" :key="index" >
                <div class="enum_name">
                    {{element.value}}
                </div>
                <div class="enum_update" v-if="update_enum_right==true">
                     <a  href=# v-b-modal="`modal-updateEnum-${_uid}`" @click="sendEnumInfo(element)">Update</a>
                </div>
                <div class="enum_update" v-else>
                    <a  @click="showRightAlert" >Update</a>
                </div>
                <div class="enum_delete" v-if="delete_enum_right==true">
                    <a  href=# v-b-modal="`modal-deleteEnum-${_uid}`" @click="sendEnumInfo(element)">Delete</a>
                </div>
                <div class="enum_delete" v-else>
                    <a @click="showRightAlert" >Delete</a>
                </div>
            </li>
        </ul>
        <div>
            <div v-if="add_enum_right==true">
                <b-button  @click="$bvModal.show(`modal-addEnum-${_uid}`)" variant="primary">Add a new enum</b-button>
            </div>
            <div v-else>
                <b-button disabled variant="primary">Add a new enum</b-button>
                <p class="enum_add_right_red"> You dont have the right to add a new enum.</p>
            </div>
            <b-modal :id="`modal-addEnum-${_uid}`"  ref="modal" :title="`Submit Your ${title} Enum`" @show="resetModal" @hidden="resetModal" @ok="handleOkAdd">
                <form ref="form" @submit.stop.prevent="handleSubmitAdd">
                    <b-form-group label="Enum" label-for="enum-input" invalid-feedback="Enum is required" :state="enumState">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required ></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
            <b-modal :id="`modal-updateEnum-${_uid}`"  ref="modal" :title="`Update Your ${title} Enum`" @show="resetModal" @hidden="resetModal" @ok="handleOkUpdate">
                <form ref="form" @submit.stop.prevent="handleSubmitVerifUpdate">
                    <b-form-group label="Enum" label-for="enum-input" invalid-feedback="Enum is required" :state="enumState">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
        <b-modal :id="`modal-deleteEnum-${_uid}`"  @ok="deleteConfirmation">
            <p class="my-4">Are you sure you want to delete {{returnedEnum}} from {{this.title}} enum ?</p>
        </b-modal>
        <b-modal :id="`modal-enum-update-approved-${_uid}`" @ok="handleSubmitUpdate()"  >
            <div v-if="equipments_concerned!=null && approved_equipments!=null">
                <div v-if="equipments_concerned.length!=0" class="my-4">
                <p>Are you sure you want to update this Enum? The following not signed equipments will be concerned : </p>
                <p v-for="(element,index) in this.equipments_concerned" :key="index">
                    Internal Reference : {{element.internalReference}}, Name : {{element.name}}
                </p>
                </div>
                <div v-if="approved_equipments.length!=0" class="my-4">
                <p>You will have to re validate the following equipments : </p>
                <p v-for="(element2,index2) in this.approved_equipments" :key="index2">
                    Internal Reference : {{element2.internalReference}}, Name : {{element2.name}}
                </p>
                 <InputTextForm  inputClassName="form-control" :Errors="errors.history_reason" name="reason" label="Reason" v-model="reason" />
                </div>
            </div>
            <div v-else-if="mmes_concerned!=null && approved_mmes!=null">
                 <div v-if="mmes_concerned.length!=0" class="my-4">
                <p>Are you sure you want to update this Enum? The following not signed mmes will be concerned :  </p>
                <p v-for="(element,index) in this.mmes_concerned" :key="index">
                    Internal Reference : {{element.internalReference}}, Name : {{element.name}}
                </p>
                </div>
                <div v-if="approved_mmes.length!=0" class="my-4">
                <p class="my-4"> You will have to re validate the following mmes :   </p>
                <p v-for="(element2,index2) in this.approved_mmes" :key="index2">
                    Internal Reference : {{element2.internalReference}}, Name : {{element2.name}}
                </p>
                <InputTextForm  inputClassName="form-control" :Errors="errors.history_reason" name="reason" label="Reason" v-model="reason" />
                </div>
            </div>
        </b-modal>


        </div>

    </div>

</template>

<script>
import ErrorAlert from '../alert/ErrorAlert.vue'
import InputInfo from '../input/InputInfo.vue'
import InputTextForm from '../input/InputTextForm.vue'
export default {
    components:{
        ErrorAlert,
        InputInfo,
        InputTextForm
    },
    props:{
        enumList:{
            type:Array
        },
        title:{
            type:String
        },
        url:{
            type:String
        },
        error_name:{
            type:String
        },
        enum_value:{
            type:String
        },
        info_text:{
            type:String,
            default : null
        }
    },
    data(){
        return{
            returnedEnum:'',
            enumState:null,
            compId:this._uid,
            sendedEnumValue:'',
            sendedEnumId:null,
            enums:this.enumList,
            dismissSecs: 5,
            dismissCountDown: 0,
            showDismissibleAlert: false,
            returnedText_info:this.info_text,
            delete_enum_right:this.$userId.user_deleteEnumRight,
            add_enum_right:this.$userId.user_addEnumRight,
            update_enum_right:this.$userId.user_updateEnumRight,
            approved_equipments:null,
            approved_mmes:null,
            equipments_concerned:null,
            mmes_concerned:null,
            enumId:'',
            reason:'',
            errors:{},
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
            axios.post(postUrlAdd(this.url),{
                    value:this.returnedEnum,
                })
                .then(response =>{ window.location.reload();})
                .catch(error =>{
                    console.log(error.response.data.errors);
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                }) ;
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
        handleSubmitVerifUpdate() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            const postUrlAdd = (url,id) => `${url}verif/${id}`;
            axios.post(postUrlAdd(this.url,this.sendedEnumId),{
                    value:this.returnedEnum,
                })
                .then(response =>{
                    console.log("verif ok")
                    this.sendedEnumId=response.data
                    const postUrlAnalyze = (url,id) => `${url}analyze/${id}`;
                    axios.post(postUrlAnalyze(this.url,this.sendedEnumId),{
                            value:this.returnedEnum,
                    })
                    .then(response =>{
                        console.log(response.data);
                        if (response.data.validated_eq!=null){
                            console.log("cas1")
                            this.approved_equipments=response.data.validated_eq ;
                        }
                        if (response.data.equipments!=null)
                         console.log("cas2")
                            this.equipments_concerned=response.data.equipments;
                        if (response.data.validated_mme!=null){
                             console.log("cas3")
                            this.approved_mmes=response.data.validated_mme;
                        }
                        if (response.data.mmes!=null){
                             console.log("cas4")
                            this.mmes_concerned=response.data.mmes;
                        }
                        this.enumId=response.data.id;
                        this.$bvModal.show(`modal-enum-update-approved-${this.compId}`);
                        console.log(response.data)
                    })
                })
                .catch(error =>{
                    this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name]);
                });

            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide(`modal-updateEnum-${this.compId}`);
                this.sendedEnumId=null;
            })
        },

        handleSubmitUpdate(){
            const postUrlUpdate = (url, id) => `${url}update/${id}`;
            axios.post(postUrlUpdate(this.url,this.enumId),{
                    value:this.returnedEnum,
                    validated_eq:this.approved_equipments,
                    validated_mme:this.approved_mmes,
                    history_reasonUpdate:this.reason,
            })
                .then(response =>{
                    console.log(response.data)
                    window.location.reload();
                    this.enumId=null;
                    this.approved_equipments=null;
                    this.approved_mmes=null;
                    this.equipments_concerned=null;
                    this.mmes_concerned=null;
                    this.reason="";
                })
        },
        deleteConfirmation(){
            const postUrlAdd = (url, id) => `${url}delete/${id}`;
            axios.post(postUrlAdd(this.url,this.sendedEnumId),{
            })
            .then(response =>{ window.location.reload();})
            .catch(error =>{this.$refs.errorAlert.showAlert(error.response.data.errors[this.error_name])}) ;
            this.$nextTick(() => {
                this.sendedEnumId=null;
            })
        },
        sendEnumInfo(element){
            this.returnedEnum=element.value;
            this.sendedEnumId=element.id;
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showAlert() {
            this.dismissCountDown = this.dismissSecs
        },
        showRightAlert(){
            this.$refs.errorAlert.showAlert("You don't have the right")
        }
    }
}
</script>

<style lang="scss">
    .enumTitle{
        display: inline-block;
    }
    .list-group-item{
        .enum_name{
            display: inline-block;
        }
        .enum_update{
            display: inline-block;
        }
        .enum_delete{
            display: inline-block;
        }
    }
    .input-reason{
        width: 300px;
    }
    .enum_add_right_red{
        color: red;
    }
</style>

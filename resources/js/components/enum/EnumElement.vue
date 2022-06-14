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
                <form ref="form" @submit.stop.prevent="handleSubmitUpdate">
                    <b-form-group label="Enum" label-for="enum-input" invalid-feedback="Enum is required" :state="enumState">
                        <b-form-input id="enum-input" v-model="returnedEnum" :state="enumState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>

        <b-modal :id="`modal-deleteEnum-${_uid}`"  @ok="deleteConfirmation">
            <p class="my-4">Are you sure you want to delete {{returnedEnum}} from {{this.title}} enum ?</p>
        </b-modal>


        </div>

    </div>
    
</template>

<script>
import ErrorAlert from '../alert/ErrorAlert.vue'
import InputInfo from '../input/InputInfo.vue'
export default {
    components:{
        ErrorAlert,
        InputInfo
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
            update_enum_right:this.$userId.user_updateEnumRight 


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
            var postUrlAdd = (url) => `${url}add`;
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
            this.handleSubmitUpdate()
        },
        handleSubmitUpdate() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            var postUrlAdd = (url,id) => `${url}update/${id}`;
            axios.post(postUrlAdd(this.url,this.sendedEnumId),{
                    value:this.returnedEnum,
                })
                .then(response =>{ 
                    window.location.reload();
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
        deleteConfirmation(){
            var postUrlAdd = (url,id) => `${url}delete/${id}`;
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
    .enum_add_right_red{
        color: red;
    }
</style>
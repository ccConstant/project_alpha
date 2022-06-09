<template>
    <div class="info_element">
        <ul>
            <li class="list-group-item" >
                {{infos[Object.keys(infos)[0]]}}
                <a href=# v-b-modal="`modal-updateInfo-${_uid}`" @click="sendInfo(infos)">Update</a>
            </li>
        </ul>
        <div>
            <b-modal :id="`modal-updateInfo-${_uid}`"  ref="modal" :title="`Update Your ${title} Info`" @show="resetModal" @hidden="resetModal" @ok="handleOkUpdate">
                <form ref="form" @submit.stop.prevent="handleSubmitUpdate">
                    <b-form-group label="Info" label-for="info-input" invalid-feedback="Info is required" :state="infoState">
                        <b-form-input id="info-input" v-model="returnedInfo" :state="infoState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
        </div>
  </div>
</template>

<script>
export default {
    props:{
        title:{
            type:String
        },
        info_prop:{
            type:Object
        }
    },
    data(){
        return{
            infos:this.info_prop,
            infoState:null,
            returnedInfo:''
        }
    },
    methods:{
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            this.infoState = valid
            return valid
        },
        resetModal() {
            this.infoState = null

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
            var postUrlAdd = (url,id) => `info/update/${id}`;
            axios.post(postUrlAdd(this.url,this.sendedInfoId),{
                    value:this.returnedInfo
                })
                .then(response =>{ 
                    this.infos[`${Object.keys(this.infos)[0]}`]=this.returnedInfo
                })
                .catch(error =>{});
            // Hide the modal manually
            
            this.$nextTick(() => {
                this.$bvModal.hide(`modal-updateInfo-${this.compId}`);
                this.sendedInfoId=null;
            })
        },
        sendInfo(element){
            this.returnedInfo=element.value;
            this.sendedInfoId=element.id;
        },
    }

}
</script>

<style>

</style>
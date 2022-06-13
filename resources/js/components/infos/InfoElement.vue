<template>
    <div class="info_element">
            <li class="list-group-item" >
                {{info_content_data}}
                <a href=# v-b-modal="`modal-updateInfo-${_uid}`">Update</a>
            </li>
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
        info_id:{
            type:Number
        },
        info_content:{
            type:String
        }
    },
    data(){
        return{
            info_content_data:this.info_content,
            infoState:null,
            returnedInfo:this.info_content
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
            console.log(this.returnedInfo)
             console.log(this.info_id)
            var postUrlAdd = (id) => `info/update/${id}`;
            axios.post(postUrlAdd(this.info_id),{
                    info_value:this.returnedInfo
                })
                .then(response =>{ 
                    console.log(response.data)
                    this.info_content_data=this.returnedInfo
                })
                .catch(error =>{});
            // Hide the modal manually
            
            this.$nextTick(() => {
                this.$bvModal.hide(`modal-updateInfo-${this.compId}`);
                this.sendedInfoId=null;
            })
        },
    }

}
</script>

<style>

</style>
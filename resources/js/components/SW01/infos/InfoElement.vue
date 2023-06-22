<!--File name : InfosElement.vue-->
<!--Creation date : 12 Jul 2022-->
<!--Update date : 25 May 2023-->
<!--Vue Component representing an info called in the different forms-->


<template>
    <div class="info_element">
        <li class="list-group-item">
            <b>{{ title }} : </b>{{ info_content_data }}
            <a v-b-modal="`modal-updateInfo-${_uid}`" href=#>Update</a>
        </li>
        <div>
            <b-modal :id="`modal-updateInfo-${_uid}`" ref="modal" :title="`Update Your ${title} Info`"
                     @hidden="resetModal" @ok="handleOkUpdate" @show="resetModal">
                <form ref="form" @submit.stop.prevent="handleSubmitUpdate">
                    <b-form-group :state="infoState" invalid-feedback="Info is required" label="Info"
                                  label-for="info-input">
                        <b-form-input id="info-input" v-model="returnedInfo" :state="infoState" required></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String
        },
        info_id: {
            type: Number
        },
        info_content: {
            type: String
        }
    },
    data() {
        return {
            info_content_data: this.info_content,
            infoState: null,
            returnedInfo: this.info_content,
            compId: this._uid
        }
    },
    methods: {
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
            const postUrlAdd = (id) => `/info/update/${id}`;
            axios.post(postUrlAdd(this.info_id), {
                info_value: this.returnedInfo
            }).then(response => {
                this.info_content_data = this.returnedInfo
                this.$bvModal.hide(`modal-updateInfo-${this.compId}`);
            }).catch(error => {
            });
        },
    }
}
</script>

<style>

</style>




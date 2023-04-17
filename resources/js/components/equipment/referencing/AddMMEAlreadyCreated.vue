<!--File name : AddMMEAlreadyCreated.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to link a MME already created to the equipment-->

<template>
    <div>
        <b-button v-b-modal.modal-mme_add_already_created>Add MME Already Created</b-button>
        <b-modal id="modal-mme_add_already_created" title="Add MME already created" hide-footer>
            <div>
                <div v-if="mme_not_linked.length>0">
                    <div v-for="option in mme_not_linked" :key="option.id">
                        <input type="radio" name="radio-input" :value="option" :id="option" v-model="radio_value"/>
                        {{ option.internalReference }}
                    </div>
                </div>
                <div v-else>
                    <p>Nothing to select</p>
                </div>
            </div>
            <b-button class="mt-3" block @click="$bvModal.hide('modal-mme_add_already_created')">Close</b-button>
            <b-button class="mt-3" block @click="chooseMME">Choose</b-button>
        </b-modal>

        <b-modal :id="`modal-approved${_uid}`" @ok="dataAppear()">
            <p class="my-4">Are you sure you want to add this mme to this approved data? You will have to sign them
                again </p>
            <input v-model="reason" placeholder="Reason for the update"/>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        eq_id: {
            type: Number,
        },
    },
    data() {
        return {
            mme_not_linked: [],
            radio_value: '',
            reason: '',
            lifesheet_created: this.$route.query.signed,
        }
    },
    created() {
        console.log(this.lifesheet_created)
        /*Ask the controller for a list of the not linked mme*/
        axios.get('/mme/mmes_not_linked')
            .then(response => {
                this.mme_not_linked = response.data;
            })
            .catch(error => console.log(error));
    },
    methods: {
        /*Function to import the mme selected*/
        chooseMME() {
            if (this.radio_value != '') {
                console.log(this.radio_value.internalReference)
                console.log(this.eq_id)
                const urlLinkMmeToEq = (id) => `/mme/link_to_eq/${id}`;
                axios.post(urlLinkMmeToEq(this.eq_id), {
                    'mme_internalReference': this.radio_value.internalReference,
                });
                if (this.lifesheet_created == true) {
                    this.$bvModal.show(`modal-approved${this._uid}`)
                } else {
                    this.$emit('choosedMME', this.radio_value, this.reason, this.lifesheet_created)
                }
            }
            this.$bvModal.hide('modal-mme_add_already_created')
        },
        dataAppear() {
            this.$emit('choosedMME', this.radio_value, this.reason, this.lifesheet_created)
        }
    }
}
</script>

<style lang="scss">
.modal-backdrop {
    opacity: 0.8;
}
</style>

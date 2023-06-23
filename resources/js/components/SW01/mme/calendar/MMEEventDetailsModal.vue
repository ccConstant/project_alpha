<!--File name : MMEEventDetailsModal.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to generate a modal to show detail about an event linked to a MME-->

<template>
    <div>
        <b-modal id="modal-event_details" hide-footer title="Details" @hidden="resetModal">
            <ErrorAlert ref="errorAlert"/>
            <div>
                <div v-for="option in verif" :key="option.id">
                    <div>
                        <b-card>
                            <h3>{{ option.mme_internalReference }}</h3>
                            <p class="card-text">
                                Operation number : {{ option.verif_number }}<br>
                                Expected result : {{ option.verif_expectedResult }}<br>
                                Non compliance limit : {{ option.verif_nonComplianceLimit }}<br>
                                Description : {{ option.verif_description }}<br>
                                Protocol : {{ option.verif_protocol }}<br>
                                Operation date :
                                {{
                                    new Date(option.verif_nextDate.slice(6), option.verif_nextDate.slice(3, 5), option.verif_nextDate.slice(0, 2)).getDate()
                                }}
                                {{
                                    new Date(option.verif_nextDate.slice(6), option.verif_nextDate.slice(3, 5) - 1, option.verif_nextDate.slice(0, 2)).toDateString().slice(4, 7)
                                }}
                                {{
                                    new Date(option.verif_nextDate.slice(6), option.verif_nextDate.slice(3, 5), option.verif_nextDate.slice(0, 2)).getFullYear()
                                }}
                            </p>
                            <div v-if="makeMmeOpValidationRight==true">
                                <b-button variant="primary"
                                          @click="redirect_to_preventive(option.mme_id,option.state_id, option.verif_number, option.id)">
                                    Record it
                                </b-button>
                            </div>
                            <div v-else>
                                <b-button disabled variant="primary">Record it</b-button>
                            </div>
                        </b-card>
                    </div>
                </div>
            </div>
            <b-button block class="mt-3" @click="closeAndClear()">Close</b-button>
        </b-modal>
    </div>
</template>

<script>
import ErrorAlert from '../../../alert/ErrorAlert.vue'

export default {
    components: {
        ErrorAlert
    },
    props: {
        verif: {
            type: Array
        }
    },
    data() {
        return {
            makeMmeOpValidationRight: this.$userId.user_makeMmeOpValidationRight,
        }
    },
    methods: {
        closeAndClear() {
            this.$bvModal.hide('modal-event_details')
        },
        resetModal() {
            this.$emit('modalClosed', '')
        },
        redirect_to_preventive(mme_id, state_id, number, id) {
            if (this.$userId.user_makeMmeOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            }
            const consultUrl = (state_id) => `/mme_state/verif/beforeReferenceVerif/${state_id}`;
            axios.post(consultUrl(state_id), {
                mme_id: mme_id
            }).then(response => {
                this.$router.push({
                    name: "url_mme_life_event_reference",
                    params: {id: mme_id, state_id: state_id},
                    query: {type: "verif", number: number, id: id}
                });
            }).catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
            });
        }
    }
}
</script>

<style lang="scss">
.modal-backdrop {
    opacity: 0.8;
}


</style>

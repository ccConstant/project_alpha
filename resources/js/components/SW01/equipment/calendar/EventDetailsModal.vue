<!--File name : EventDetailsModal.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 27 Jun 2023-->
<!--Vue Component of the detailed events-->

<template>
    <div>
        <b-modal id="modal-event_details" hide-footer title="Details" @hidden="resetModal">
            <ErrorAlert ref="errorAlert"/>
            <div>
                <div v-for="option in prvMtnOp" :key="option.id">
                    <div>
                        <b-card>
                            <h3>{{ option.eq_internalReference }}</h3>
                            <div class="card-text">
                                <p>
                                    Operation number: {{ option.prvMtnOp_number }}
                                </p>
                                <p>
                                    Description : {{ option.prvMtnOp_description }}
                                </p>
                                <p>
                                    Protocol : {{ option.prvMtnOp_protocol }}
                                </p>
                                <p>
                                    Last Operation Comment : {{ option.prvMtnOp_lastComment }}
                                </p>
                                <p>
                                    Operation date :
                                    {{
                                        new Date(option.prvMtnOp_nextDate.slice(6), option.prvMtnOp_nextDate.slice(3, 5), option.prvMtnOp_nextDate.slice(0, 2)).getDate()
                                    }}
                                    {{
                                        new Date(option.prvMtnOp_nextDate.slice(6), option.prvMtnOp_nextDate.slice(3, 5) - 1, option.prvMtnOp_nextDate.slice(0, 2)).toDateString().slice(4, 7)
                                    }}
                                    {{
                                        new Date(option.prvMtnOp_nextDate.slice(6), option.prvMtnOp_nextDate.slice(3, 5), option.prvMtnOp_nextDate.slice(0, 2)).getFullYear()
                                    }}
                                </p>
                                <p>
                                    Periodicity : {{ option.prvMtnOp_periodicity }}
                                    {{ option.prvMtnOp_symbolPeriodicity }}
                                </p>
                            </div>
                            <div v-if="makeEqOpValidationRight==true">
                                <b-button variant="primary"
                                          @click="redirect_to_preventive(option.eq_id, option.state_id, option.prvMtnOp_number, option.id)">
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
        ErrorAlert,
    },
    props: {
        prvMtnOp: {
            type: Array
        }

    },
    data() {
        return {
            makeEqOpValidationRight: this.$userId.user_makeEqOpValidationRight,
        }
    },
    methods: {
        closeAndClear() {
            this.$bvModal.hide('modal-event_details')
        },
        resetModal() {
            this.$emit('modalClosed', '')
        },
        redirect_to_preventive(eq_id, state_id, number, id) {
            if (this.$userId.user_makeEqOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");

            }
            const consultUrl = (state_id) => `/state/verif/beforeReferencePrvOp/${state_id}`;
            axios.post(consultUrl(state_id), {
                eq_id: eq_id
            })
                .then(response => {
                    this.$router.replace({
                        name: "url_life_event_reference",
                        params: {id: eq_id, state_id: state_id},
                        query: {type: "preventive", number: number, id: id}
                    });
                })
                //If the controller sends errors we put it in the errors object
                .catch(error => {
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

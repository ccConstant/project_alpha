<!--File name : VerifChooseModal.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 13 Apr 2023-->
<!--Vue Component used to choose a verification to realize with a modal-->

<template>
    <div>
        <div v-if="verifs.length>0">
            <b-button v-b-modal.modal-1>Choose a verification to realize</b-button>
        </div>
        <div v-else>
            <b-button v-b-modal.modal-1 disabled>Please inform or validate verification before realizing one</b-button>
        </div>
        <b-modal id="modal-1" hide-footer title="Choose a verification to realize">
            <div>
                <b-button v-b-toggle.collapse-chooseOpe icon="chevron-bar-up" variant="primary">Show details</b-button>
                <div v-for="option in verifs_data" :key="option.id">
                    <input :id="option.id" v-model="radio_value" :value="option" name="radio-input" type="radio"/>
                    {{ option.verif_number }}
                    <div>
                        <b-collapse id="collapse-chooseOpe" class="mt-2">
                            <b-card>
                                <p class="card-text">
                                    Expected Result :{{ option.verif_expectedResult }}<br>
                                    Non Compliance Limit :{{ option.verif_nonComplianceLimit }}<br>
                                    Description : {{ option.verif_description }}<br>
                                    Protocol : {{ option.verif_protocol }}<br>
                                    Next Date : {{ option.verif_nextDate }}
                                </p>
                            </b-card>
                        </b-collapse>
                    </div>
                </div>
            </div>
            <b-button block class="mt-3" @click="$bvModal.hide('modal-1')">Close</b-button>
            <b-button block class="mt-3" @click="chooseMme">Choose</b-button>
        </b-modal>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        verifs: {
            type: Array
        }
    },
    data() {
        return {
            radio_value: '',
            verifs_data: this.verifs
        }
    },
    methods: {
        chooseMme() {
            if (this.radio_value != '') {
                this.$emit('choosedOpe', this.radio_value)
            }
            this.$bvModal.hide('modal-1')
        }
    },
    created() {
        for (let i = 0; i < this.verifs_data.length; i++) {
            this.verifs_data[i].verif_nextDate = moment(this.verifs_data[i].verif_nextDate).format('D MMM YYYY ');
        }
    }
}
</script>

<style lang="scss">
.modal-backdrop {
    opacity: 0.8;
}
</style>

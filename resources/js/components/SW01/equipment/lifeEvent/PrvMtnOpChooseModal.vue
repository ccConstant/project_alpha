<!--File name : PrvMtnOpChooseModal.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component to make the modal to choose the preventive maintenance operation to realize-->

<template>
    <div>
        <div v-if="prvMtnOps.length>0">
            <b-button v-b-modal.modal-1>Choose a preventive maintenance operation to realize</b-button>
        </div>
        <div v-else>
            <b-button v-b-modal.modal-1 disabled>Please inform or validate preventive maintenance operation before
                realizing one
            </b-button>
        </div>
        <b-modal id="modal-1" hide-footer title="Choose a preventive maintenance operation to realize">
            <div>
                <b-button v-b-toggle.collapse-chooseOpe icon="chevron-bar-up" variant="primary">Show details</b-button>
                <div v-for="option in prvMtnOps_data" :key="option.id">
                    <input :id="option.id" v-model="radio_value" :value="option" name="radio-input" type="radio"/>
                    {{ option.prvMtnOp_number }}
                    <div>
                        <b-collapse id="collapse-chooseOpe" class="mt-2">
                            <b-card>
                                <p class="card-text">
                                    Description : {{ option.prvMtnOp_description }}<br>
                                    Protocol : {{ option.prvMtnOp_protocol }}<br>
                                    Next Date : {{ option.prvMtnOp_nextDate }}
                                </p>
                            </b-card>
                        </b-collapse>
                    </div>
                </div>
            </div>
            <b-button block class="mt-3" @click="$bvModal.hide('modal-1')">Close</b-button>
            <b-button block class="mt-3" @click="chooseEquipment">Choose</b-button>
        </b-modal>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        prvMtnOps: {
            type: Array
        },
        number: {
            type: String
        }
    },
    data() {
        return {
            radio_value: this.number,
            prvMtnOps_data: this.prvMtnOps
        }
    },
    methods: {
        chooseEquipment() {
            if (this.radio_value != '') {
                this.$emit('choosedOpe', this.radio_value);
            }
            this.$bvModal.hide('modal-1')
        }
    },
    created() {
        for (let i = 0; i < this.prvMtnOps_data.length; i++) {
            this.prvMtnOps_data[i].prvMtnOp_nextDate = moment(this.prvMtnOps_data[i].prvMtnOp_nextDate).format('D MMM YYYY ');
        }
    }
}
</script>

<style lang="scss">
.modal-backdrop {
    opacity: 0.8;
}
</style>

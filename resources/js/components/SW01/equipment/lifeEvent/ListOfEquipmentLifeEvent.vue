<!--File name : ListOfEquipmentLifeEvent.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component to show the list of the equipment in the life event menu-->

<template>
    <div class="listOfEquipmentLifeEvent">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <h1>Equipment Life Record</h1>
            <input v-model="searchTerm" class="form-control w-50 search_bar"
                   placeholder="Search an equipment by his Alpha Reference" type="text">
            <ErrorAlert ref="errorAlert"/>
            <ul>
                <div v-for="(list,index) in pageOfItems " :key="index" class="one_element_list">
                    <li :class="'element'+index%2" class="list-group-item">
                        <div class="eq_list_internalReference_state">
                            <b>{{ list.eq_internalReference }}</b>
                        </div>
                        <div class="eq_list_current_state">
                            Current state : {{ list.eq_state }}
                        </div>
                        <div class="eq_list_option_state">
                            <a href="#" @click="verifBeforeUpdateState(list.id,list.state_id)">Update the state</a>
                            <a href="#" @click="verifBeforeAddState(list.id,list.state_id)">Change the state</a>
                            <router-link
                                :to="{name:'url_life_event_all',params:{id: list.id},query:{internalReference:list.eq_internalReference} }">
                                All Event
                            </router-link>
                            <a href="#" @click="verifBeforeAddOpe(list.id,list.state_id)">Record a curative maintenance
                                operation</a>
                            <a href="#" @click="verifBeforeUpdateOp(list.id,list.state_id)">Update maintenance
                                record</a>
                        </div>
                    </li>
                </div>
            </ul>
            <jw-pagination :items="filterByTerm" :pageSize=10 @changePage="onChangePage"></jw-pagination>
        </div>
    </div>
</template>

<script>

import ErrorAlert from '../../../alert/ErrorAlert.vue'

export default {
    components: {
        ErrorAlert,
    },
    data() {
        return {
            equipments: [],
            searchTerm: "",
            loaded: false,
            currentState: '',
            pageOfItems: [],
            user_makeEqOpValidationRight: this.$userId.user_makeEqOpValidationRight
        }
    },
    methods: {
        verifBeforeAddState(eq_id, state_id) {
            if (this.$userId.user_declareNewStateRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                const consultUrl = (id) => `/state/verif/beforeChangingState/${id}`;
                axios.post(consultUrl(state_id), {}).then(response => {
                    this.$router.replace({
                        name: "url_life_event_change_state",
                        params: {id: eq_id},
                        query: {currentState: state_id}
                    });
                }).catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors.state_verif);
                });
            }

        },
        verifBeforeAddOpe(eq_id_to_send, state_id) {
            if (this.$userId.user_makeEqOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
                return;
            }
            const consultUrl = (state_id) => `/state/verif/beforeReferenceCurOp/${state_id}`;
            axios.post(consultUrl(state_id), {
                eq_id: eq_id_to_send
            }).then(response => {
                this.$router.push({
                    name: "url_life_event_reference",
                    params: {id: eq_id_to_send, state_id: state_id},
                    query: {type: "curative"}
                });
            }).catch(error => {
                this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
            });
        },
        onChangePage(pageOfItems) {
            // update page of items
            this.pageOfItems = pageOfItems;
        },
        verifBeforeUpdateOp(eq_id_to_send, state_id) {
            if (this.$userId.user_makeEqOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                this.$router.push({name: "url_life_event_update", params: {id: eq_id_to_send, state_id: state_id}})
            }
        },
        verifBeforeUpdateState(eq_id_to_send, state_id) {
            if (this.$userId.user_declareNewStateRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                this.$router.push({
                    name: "url_life_event_update_state",
                    params: {id: eq_id_to_send, state_id: state_id}
                })
            }
        },
    },
    computed: {
        filterByTerm() {
            return this.equipments.filter(option => {
                return option.eq_internalReference.toLowerCase().includes(this.searchTerm);
            });
        }
    },
    created() {
        axios.get('/equipment/equipments')
            .then(response => {
                this.equipments = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    },
}
</script>

<style lang="scss">
.listOfEquipmentLifeEvent {
    .element0 {
        background-color: #ccc;
    }

    h1 {
        text-align: center;
    }

    .search_bar {
        margin-left: 30px;
        margin-bottom: 20px;
    }
}

.eq_list_internalReference_state {
    display: inline-block;
}

.eq_list_current_state {
    display: block;
    margin-left: 200px;
    margin-top: -20px;
}

.eq_list_option_state {
    display: block;
    margin-left: 460px;
    margin-top: -22.5px;
}

</style>

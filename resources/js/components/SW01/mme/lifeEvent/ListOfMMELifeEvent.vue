<!--File name : ListOfMMELifeEvent.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to show a list of the differents life event linked to any of the MME-->

<template>
    <div class="listOfMMELifeEvent">
        <div v-if="loaded==false">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-if="loaded==true">
            <h1>MME Life Record</h1>
            <input v-model="searchTerm" class="form-control w-50 search_bar"
                   placeholder="Search an MME by his Alpha Reference" type="text">
            <ErrorAlert ref="errorAlert"/>
            <ul>
                <div v-for="(list,index) in pageOfItems " :key="index" class="one_element_list">
                    <li :class="'element'+index%2" class="list-group-item">
                        <div class="mme_list_internalReference_state">
                            <b>{{ list.mme_internalReference }}</b>
                        </div>
                        <div class="mme_list_current_state">
                            Current state : {{ list.mme_state }}
                        </div>
                        <div class="mme_list_option_state">
                            <a href="#" @click="verifBeforeUpdateState(list.id,list.state_id)">Update the state</a>
                            <a href="#" @click="verifBeforeAddState(list.id,list.state_id)">Change the state</a>
                            <router-link
                                :to="{name:'url_mme_life_event_all',params:{id: list.id},query:{internalReference:list.mme_internalReference} }">
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
            mmes: [],
            searchTerm: "",
            loaded: false,
            currentState: '',
            pageOfItems: [],
            user_makeMmeOpValidationRight: this.$userId.user_makeMmeOpValidationRight
        }
    },
    methods: {
        verifBeforeAddState(mme_id, state_id) {
            if (this.$userId.user_declareNewStateRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                const consultUrl = (id) => `/mme_state/verif/beforeChangingState/${id}`;
                axios.post(consultUrl(state_id), {})
                    .then(response => {
                        this.$router.replace({
                            name: "url_mme_life_event_change_state",
                            params: {id: mme_id},
                            query: {currentState: state_id}
                        })
                        ;
                    }).catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors.state_verif);
                });
            }
        },
        verifBeforeAddOpe(mme_id_to_send, state_id) {
            if (this.$userId.user_makeMmeOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
                return;
            }
            const consultUrl = (state_id) => `/mme_state/verif/beforeReferenceCurOp/${state_id}`;
            axios.post(consultUrl(state_id), {
                mme_id: mme_id_to_send
            })
                .then(response => {
                    this.$router.push({
                        name: "url_mme_life_event_reference",
                        params: {id: mme_id_to_send, state_id: state_id},
                        query: {type: "curative"}
                    })
                    ;
                })
                //If the controller sends errors we put it in the errors object
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
                });
        },
        onChangePage(pageOfItems) {
            // update page of items
            this.pageOfItems = pageOfItems;
        },
        verifBeforeUpdateState(mme_id_to_send, state_id) {
            if (this.$userId.user_declareNewStateRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");

            } else {
                this.$router.push({
                    name: "url_mme_life_event_update_state",
                    params: {id: mme_id_to_send, state_id: state_id}
                })
            }
        },
        verifBeforeUpdateOp(mme_id_to_send, state_id) {
            if (this.$userId.user_makeMmeOpValidationRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                this.$router.push({name: "url_mme_life_event_update", params: {id: mme_id_to_send, state_id: state_id}})
            }
        },
    },
    computed: {
        filterByTerm() {
            return this.mmes.filter(option => {
                return option.mme_internalReference.toLowerCase().includes(this.searchTerm);
            });
        }
    },
    created() {
        axios.get('/mme/mmes')
            .then(response => {
                this.mmes = response.data;
                this.loaded = true;
            }).catch(error => {
        });
    },
}
</script>

<style lang="scss">
.listOfMMELifeEvent {
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

.mme_list_internalReference_state {
    display: inline-block;
}

.mme_list_current_state {
    display: block;
    margin-left: 200px;
    margin-top: -20px;
}

.mme_list_option_state {
    display: block;
    margin-left: 460px;
    margin-top: -22.5px;
}
</style>

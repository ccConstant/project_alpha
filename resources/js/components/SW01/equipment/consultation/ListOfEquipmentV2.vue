<!--File name : ListOfEquipmentV2.vue-->
<!--Creation date : 25 May 2023-->
<!--Update date : 25 May 2023-->
<!--Vue Component of the list of equipment menu-->

<template>
    <div>
        <div v-if="loaded !== true">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="header">
                <table>
                    <tr>
                        <td colspan="4">
                            <h1 class="title">Equipment's List</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100vh;"></td>
                        <td>
                            <button class="btn btn-primary" @click="newEquipment">Add new equipment</button>
                        </td>
                        <td>
                            <button class="btn btn-warning" @click="changeState">Change the state</button>
                        </td>
                        <td>
                            <button class="btn btn-info" @click="updateMaintenance">Record and validate maintenance event</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input v-model="searchTermInternRef" class="form-control search_bar align-self-center"
                                   placeholder="Search an EQ by is Intern Ref" type="text">
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="updateEquipment">Update lifesheet descriptive part</button>
                        </td>
                        <td>
                            <button class="btn btn-warning" @click="updateState">Update actual state informations</button>
                        </td>
                        <td>
                            <button class="btn btn-info" @click="addCurativeMaintenance">Add curative operation</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input v-model="searchTermName" class="form-control search_bar align-self-center"
                                   placeholder="Search an EQ by is name" type="text">
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="consultEquipment">Consult lifesheet descriptive part</button>
                        </td>
                        <td>
                        </td>
                        <td>
                            <button class="btn btn-info" @click="consultEvents">Consult lifesheet event part</button>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-primary" @click="reviewEquipment">Validate lifesheet descriptive part</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="list">
                <b-form>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">EQ Intern Ref</th>
                            <th scope="col">EQ Name</th>
                            <th scope="col">Actual State</th>
                            <th scope="col">
                                Technical Reviewed ?
                                <select v-model="searchTermTechReview" class="form-control search_bar align-self-center"
                                        type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                            <th scope="col">
                                Quality Reviewed ?
                                <select v-model="searchTermQualityReview"
                                        class="form-control search_bar align-self-center" type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                            <th scope="col">
                                Maintenance requested ?
                                <select v-model="searchTermRealized" class="form-control search_bar align-self-center"
                                        type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                            <th scope="col">
                                Maintenance need approving ?
                                <select v-model="searchTermApproved" class="form-control search_bar align-self-center"
                                        type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="elem in pageOfItems">
                            <td>
                                <b-checkbox :checked="checked.includes(elem.eq_internalReference)" class="checkbox"
                                            variant="primary"
                                            v-on:change="updateChecked(elem.eq_internalReference)"></b-checkbox>
                            </td>
                            <td>{{ elem.eq_internalReference }}</td>
                            <td>{{ elem.eq_name }}</td>
                            <td>{{ elem.eq_state }}</td>
                            <td>{{ elem.alreadyValidatedTechnical ? 'Yes' : 'No' }}</td>
                            <td>{{ elem.alreadyValidatedQuality ? 'Yes' : 'No' }}</td>
                            <td>{{ elem.needToBeRealized ? 'Yes' : 'No' }}</td>
                            <td>{{ elem.needToBeApprove ? 'Yes' : 'No' }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <jw-pagination :items="filterByTerm" :pageSize=25 class="eq_list_pagination"
                                   @changePage="onChangePage"></jw-pagination>
                </b-form>
                <ErrorAlert ref="errorAlert"></ErrorAlert>
            </div>
        </div>
    </div>

</template>

<script>
import ErrorAlert from "../../../alert/ErrorAlert.vue";

export default {
    components: {
        ErrorAlert
    },
    data() {
        return {
            equipments: [],
            loaded: false,
            pageOfItems: [],
            checked: [],
            searchTermInternRef: "",
            searchTermName: "",
            searchTermTechReview: -1,
            searchTermQualityReview: -1,
            searchTermRealized: -1,
            searchTermApproved: -1
        }
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
        updateChecked(ref) {
            if (this.checked.includes(ref)) {
                this.checked.splice(this.checked.indexOf(ref), 1);
                return false;
            }
            this.checked.push(ref);
            return true;
        },
        newEquipment() {
            window.location.href = "/equipment/add";
        },
        updateEquipment() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        window.location.href = "/equipment/list/update/" + element.id;
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to update");
            }
        },
        updateState() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        this.verifBeforeUpdateState(element.id, element.state_id)
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to update");
            }
        },
        changeState() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        this.verifBeforeAddState(element.id, element.state_id)
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to update");
            }
        },
        addCurativeMaintenance() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        this.verifBeforeAddOpe(element.id, element.state_id)
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to update");
            }
        },
        updateMaintenance() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        this.verifBeforeUpdateOp(element.id, element.state_id)
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to update");
            }
        },
        verifBeforeAddState(eq_id, state_id) {
            if (this.$userId.user_declareNewStateRight != true) {
                this.$refs.errorAlert.showAlert("You don't have the right");
            } else {
                const consultUrl = (id) => `/state/verif/beforeChangingState/${id}`;
                axios.post(consultUrl(state_id), {})
                    .then(response => {
                        this.$router.replace({
                            name: "url_life_event_change_state",
                            params: {id: eq_id},
                            query: {currentState: state_id}
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data.errors)
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
            })
                .then(response => {
                    this.$router.push({
                        name: "url_life_event_reference",
                        params: {id: eq_id_to_send, state_id: state_id},
                        query: {type: "curative"}
                    });
                })
                .catch(error => {
                    this.$refs.errorAlert.showAlert(error.response.data.errors.verif_reference);
                });
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
        consultEquipment() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        window.location.href = "/equipment/lifesheet_pdf/" + element.id;
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to consult");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to consult");
            }
        },
        consultEvents() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        this.$router.push({
                            name: "url_life_event_all",
                            params: {id: element.id},
                            query: {internalReference: element.eq_internalReference}
                        });
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to consult");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to consult");
            }
        },
        reviewEquipment() {
            if (this.checked.length === 1) {
                this.equipments.forEach(element => {
                    if (element.eq_internalReference === this.checked[0]) {
                        if (element.alreadyValidatedTechnical) {
                            if (element.alreadyValidatedQuality) {
                                this.$refs.errorAlert.showAlert("This equipment is already validated");
                            } else {
                                this.$router.replace({
                                    name: "url_eq_consult",
                                    params: {id: element.id},
                                    query: {method: "quality"}
                                });
                            }
                        } else {
                            this.$router.replace({
                                name: "url_eq_consult",
                                params: {id: element.id},
                                query: {method: "technical"}
                            });
                        }
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an equipment to consult");
                else
                    this.$refs.errorAlert.showAlert("Please select only one equipment to consult");
            }
        },
        resetActiveFilter() {
            this.searchTermActive = -1;
        },
        resetValidateFilter() {
            this.searchTermValidate = -1;
        },
    },
    mounted() {
    },
    computed: {
        filterByTerm() {
            let res = [];
            if (this.searchTermInternRef !== "" && this.searchTermName === "") {
                res = this.equipments.filter(option => {
                    return option.eq_internalReference.toLowerCase().includes(this.searchTermInternRef.toLowerCase());
                });
            } else if (this.searchTermInternRef === "" && this.searchTermName !== "") {
                res = this.equipments.filter(option => {
                    return option.eq_name.toLowerCase().includes(this.searchTermName.toLowerCase());
                });
            } else {
                res = this.equipments.filter(option => {
                    return option.eq_internalReference.toLowerCase().includes(this.searchTermInternRef.toLowerCase()) && option.eq_name.toLowerCase().includes(this.searchTermName.toLowerCase());
                });
            }
            if (this.searchTermTechReview !== -1) {
                res = res.filter(option => {
                    if (this.searchTermTechReview === 0)
                        return option.alreadyValidatedTechnical === false;
                    else if (this.searchTermTechReview === 1)
                        return option.alreadyValidatedTechnical;
                });
            }
            if (this.searchTermQualityReview !== -1) {
                res = res.filter(option => {
                    if (this.searchTermQualityReview === 0)
                        return option.alreadyValidatedQuality === false;
                    else if (this.searchTermQualityReview === 1)
                        return option.alreadyValidatedQuality;
                });
            }
            return res;
        }
    },
    created() {
        axios.get('/equipment/equipments')
            .then(response => {
                console.log(response.data)
                this.equipments = response.data;
                this.loaded = true;
            })
            .catch(error => console.log(error.response.data));
    }
}
</script>

<style scoped>
.title {
    margin-left: 10vh;
}
</style>

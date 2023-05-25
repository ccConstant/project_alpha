<template>
    <div>
        <div v-if="loaded !== true">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <div v-else>
            <div class="header">
                <table>
                    <tr>
                        <td rowspan="2">
                            <h1>Supplier's List</h1>
                        </td>
                        <td colspan="2">
                            <input placeholder="Search a supplier by his name" v-model="searchTermName" class="form-control search_bar align-self-center" type="text">
                        </td>
                        <td rowspan="2">
                            <button class="btn btn-primary" @click="newSupplier">Add a new supplier</button>
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="consultSupplier">Consult selected supplier</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input placeholder="Search a supplier by his Form ID" v-model="searchTermFormID" class="form-control search_bar align-self-center" type="text">
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="updateSupplier">Update selected supplier</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="list">
                <b-form >
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Name</th>
                                <th scope="col">Form ID</th>
                                <th scope="col">
                                    Active ?
                                    <select v-model="searchTermActive" class="form-control search_bar align-self-center" type="text">
                                        <option :value="1">Yes</option>
                                        <option :value="0">No</option>
                                        <option :value="-1">All</option>
                                    </select>
                                </th>
                                <th scope="col">
                                    Critical ?
                                    <select v-model="searchTermCritical" class="form-control search_bar align-self-center" type="text">
                                        <option :value="1">Yes</option>
                                        <option :value="0">No</option>
                                        <option :value="-1">All</option>
                                    </select>
                                </th>
                                <th scope="col">
                                    Real ?
                                    <select v-model="searchTermReal" class="form-control search_bar align-self-center" type="text">
                                        <option :value="1">Yes</option>
                                        <option :value="0">No</option>
                                        <option :value="-1">All</option>
                                    </select>
                                </th>
                                <th scope="col">
                                    Validate ?
                                    <select v-model="searchTermValidate" class="form-control search_bar align-self-center" type="text">
                                        <option :value="1">Yes</option>
                                        <option :value="0">No</option>
                                        <option :value="-1">All</option>
                                    </select>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="elem in pageOfItems">
                                <td><b-checkbox class="checkbox" :checked="checked.includes(elem.ref)" variant="primary" v-on:change="updateChecked(elem.formID)"></b-checkbox></td>
                                <td>{{elem.name}}</td>
                                <td>{{elem.formID}}</td>
                                <td>{{elem.active === 1 ? "Yes": "No"}}</td>
                                <td>{{elem.critical === 1 ? "Yes": "No"}}</td>
                                <td>{{elem.real === 1 ? "Yes": "No"}}</td>
                                <td>{{elem.signatureDate === null ? "No": elem.signatureDate}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <jw-pagination class="eq_list_pagination" :pageSize=25 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>
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
            supplierList: [],
            loaded: false,
            searchTermName: "",
            searchTermFormID: "",
            pageOfItems: [],
            checked: [],
            searchTermActive: -1,
            searchTermCritical: -1,
            searchTermReal: -1,
            searchTermValidate: -1,
        }
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
        updateChecked(formID) {
            if (this.checked.includes(formID)) {
                this.checked.splice(this.checked.indexOf(formID), 1);
                return false;
            }
            this.checked.push(formID);
            return true;
        },
        newSupplier() {
            window.location.href = "/supplier/add";
        },
        updateSupplier() {
            if (this.checked.length === 1) {
                this.supplierList.forEach(element => {
                    if (element.formID === this.checked[0]) {
                        window.location.href = "/supplier/list/update/" + element.id;
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an article to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one article to update");
            }
        },
        consultSupplier() {
            if (this.checked.length === 1) {
                this.supplierList.forEach(element => {
                    if (element.formID === this.checked[0]) {
                        window.location.href = "/supplier/list/consult/" + element.id;
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an article to consult");
                else
                    this.$refs.errorAlert.showAlert("Please select only one article to consult");
            }
        },
        resetActiveFilter() {
            this.searchTermActive = -1;
        },
        resetCriticalFilter() {
            this.searchTermCritical = -1;
        },
        resetRealFilter() {
            this.searchTermReal = -1;
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
            if (this.searchTermName !== "" && this.searchTermFormID === "") {
                res = this.supplierList.filter(option => {
                    return option.name.toLowerCase().includes(this.searchTermName.toLowerCase());
                });
            } else if (this.searchTermFormID !== "" && this.searchTermName === "") {
                res = this.supplierList.filter(option => {
                    return option.formID.toLowerCase().includes(this.searchTermFormID.toLowerCase());
                });
            } else {
                res = this.supplierList.filter(option => {
                    return option.name.toLowerCase().includes(this.searchTermName) && option.formID.toLowerCase().includes(this.searchTermFormID);
                });
            }
            if (this.searchTermActive !== -1) {
                res = res.filter(option => {
                    return option.active === this.searchTermActive;
                });
            }
            if (this.searchTermCritical !== -1) {
                res = res.filter(option => {
                    return option.critical === this.searchTermCritical;
                });
            }
            if (this.searchTermReal !== -1) {
                res = res.filter(option => {
                    return option.real === this.searchTermReal;
                });
            }
            if (this.searchTermValidate !== -1) {
                res = res.filter(option => {
                    if (this.searchTermValidate === 0)
                        return option.signatureDate === null;
                    else if (this.searchTermValidate === 1)
                        return option.signatureDate !== null;
                });
            }
            return res;
        }
    },
    created() {
        axios.get('/supplier/send').then(response => {
            for (let i = 0; i < response.data.length; i++) {
                this.supplierList.push({
                    name: response.data[i].supplr_name,
                    formID: response.data[i].supplr_formID,
                    active: response.data[i].supplr_active,
                    critical: response.data[i].supplr_critical,
                    real: response.data[i].supplr_real,
                    signatureDate: response.data[i].supplr_signatureDate,
                    id: response.data[i].id
                });
            }
            console.log(this.supplierList);
            this.loaded = true;
        }).catch(error => {
        });
    }
}
</script>

<style>
</style>

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
                            <h1>Article's List</h1>
                        </td>
                        <td colspan="2">
                            <input placeholder="Search an article by his Alpha reference" v-model="searchTermRef" class="form-control search_bar align-self-center" type="text">
                        </td>
                        <td rowspan="2">
                            <button class="btn btn-primary" @click="newArticle">Add a new article</button>
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="consultArticle">Consult selected article</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input placeholder="Search an article by his Designation" v-model="searchTermDesign" class="form-control search_bar align-self-center" type="text">
                        </td>
                        <td>
                            <button class="btn btn-primary" @click="updateArticle">Update selected article</button>
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
                                <th scope="col">Reference Alpha</th>
                                <th scope="col">Reference Supplier</th>
                                <th scope="col">Designation</th>
                                <th scope="col">Version</th>
                                <th scope="col">
                                    Active ?
                                    <select v-model="searchTermActive" class="form-control search_bar align-self-center" type="text">
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
                                <td><b-checkbox class="checkbox" :checked="checked.includes(elem.ref)" variant="primary" v-on:change="updateChecked(elem.ref)"></b-checkbox></td>
                                <td>{{elem.ref}}</td>
                                <td>{{elem.refSupplier}}</td>
                                <td>{{elem.design}}</td>
                                <td>{{elem.version}}</td>
                                <!--                            <td><b-checkbox disabled :checked="elem.active === 1"></b-checkbox></td>-->
                                <td>{{elem.active === 1 ? "Yes": "No"}}</td>
                                <!--                            <td><b-checkbox disabled :checked="elem.signatureDate === null"></b-checkbox></td>-->
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
            compList: [],
            consList: [],
            rawList: [],
            globalList: [],
            loaded: false,
            searchTermRef: "",
            searchTermDesign: "",
            pageOfItems: [],
            checked: [],
            searchTermActive: -1,
            searchTermValidate: -1,
        }
    },
    methods: {
        createGlobalList() {
            for (const i in this.compList) {
                let obj = {
                    ref: this.compList[i].compFam_ref,
                    refSupplier: this.compList[i].refSupplier, //FIXME: variable inexistante
                    design: this.compList[i].compFam_design,
                    version: this.compList[i].compFam_version,
                    active: this.compList[i].compFam_active,
                    signatureDate: this.compList[i].compFam_signatureDate,
                    type: "comp"
                };
                this.globalList.push(obj);
            }
            for (const i in this.consList) {
                let obj = {
                    ref: this.consList[i].consFam_ref,
                    refSupplier: this.consList[i].refSupplier, //FIXME: variable inexistante
                    design: this.consList[i].consFam_design,
                    version: this.consList[i].consFam_version,
                    active: this.consList[i].consFam_active,
                    signatureDate: this.consList[i].consFam_signatureDate,
                    type: "cons"
                };
                this.globalList.push(obj);
            }
            for (const i in this.rawList) {
                let obj = {
                    ref: this.rawList[i].rawFam_ref,
                    refSupplier: this.rawList[i].refSupplier, //FIXME: variable inexistante
                    design: this.rawList[i].rawFam_design,
                    version: "",
                    active: this.rawList[i].rawFam_active,
                    signatureDate: this.rawList[i].rawFam_signatureDate,
                    type: "raw"
                };
                this.globalList.push(obj);
            }
        },
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
        newArticle() {
            if (!this.$userId.user_SW03_addArticle) {
                this.$refs.errorAlert.showAlert("You don't have the permission to add an article");
            } else {
                window.location.href = "/article/add";
            }
        },
        checkRights(art) {
            if (art.compFam_signatureDate !== null) {
                if (!this.$userId.user_SW03_updateArticleSigned) {
                    this.$refs.errorAlert.showAlert("You don't have the permission to update a signed article");
                    return false;
                }
                return true;
            }
            if (!this.$userId.user_SW03_updateArticle) {
                this.$refs.errorAlert.showAlert("You don't have the permission to update an article");
                return false;
            }
            return true;
        },
        updateArticle() {
            if (this.checked.length === 1) {
                this.compList.forEach(element => {
                    if (element.compFam_ref === this.checked[0]) {
                        let res = this.checkRights(element);
                        if (res) {
                            this.$router.push({name: 'article_url_update', params: {id: element.id, type: 'comp', ref:element.compFam_ref}});
                        }
                    }
                });
                this.consList.forEach(element => {
                    if (element.consFam_ref === this.checked[0]) {
                        let res = this.checkRights(element);
                        if (res) {
                            this.$router.push({name: 'article_url_update', params: {id: element.id, type: 'cons', ref:element.consFam_ref}});
                        }
                    }
                });
                this.rawList.forEach(element => {
                    if (element.rawFam_ref === this.checked[0]) {
                        let res = this.checkRights(element);
                        if (res) {
                            this.$router.push({name: 'article_url_update', params: {id: element.id, type: 'raw', ref:element.rawFam_ref}});
                        }
                    }
                });
            } else {
                if (this.checked.length === 0)
                    this.$refs.errorAlert.showAlert("Please select an article to update");
                else
                    this.$refs.errorAlert.showAlert("Please select only one article to update");
            }
        },
        consultArticle() {
            if (this.checked.length === 1) {
                this.compList.forEach(element => {
                    if (element.compFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/comp/" + element.id;
                    }
                });
                this.consList.forEach(element => {
                    if (element.consFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/cons/" + element.id;
                    }
                });
                this.rawList.forEach(element => {
                    if (element.rawFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/raw/" + element.id;
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
        resetValidateFilter() {
            this.searchTermValidate = -1;
        },
    },
    mounted() {
    },
    computed: {
        filterByTerm() {
            let res = [];
            if (this.searchTermRef !== "" && this.searchTermDesign === "") {
                res = this.globalList.filter(option => {
                    return option.ref.toLowerCase().includes(this.searchTermRef.toLowerCase());
                });
            } else if (this.searchTermDesign !== "" && this.searchTermRef === "") {
                res = this.globalList.filter(option => {
                    return option.design.toLowerCase().includes(this.searchTermDesign.toLowerCase());
                });
            } else {
                res = this.globalList.filter(option => {
                    return option.ref.toLowerCase().includes(this.searchTermRef) && option.design.toLowerCase().includes(this.searchTermDesign);
                });
            }
            if (this.searchTermActive !== -1) {
                res = res.filter(option => {
                    return option.active === this.searchTermActive;
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
        axios.get('/comp/family/send').then(response => {
            this.compList = response.data;
            axios.get('/cons/family/send').then(response => {
                this.consList = response.data;
                axios.get('/raw/family/send').then(response => {
                    this.rawList = response.data;
                    this.createGlobalList();
                    this.loaded = true;
                }).catch(error => {});
            }).catch(error => {});
        }).catch(error => {});
    }
}
</script>

<style>
</style>

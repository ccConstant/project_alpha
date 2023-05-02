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
                    <table>
                        <tr>
                            <th></th>
                            <th>Reference Alpha</th>
                            <th>Reference Supplier</th>
                            <th>Designation</th>
                            <th>Version</th>
                            <th>
                                Active ?
                                <select v-model="searchTermActive" class="form-control search_bar align-self-center" type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                            <th>
                                Validate ?
                                <select v-model="searchTermValidate" class="form-control search_bar align-self-center" type="text">
                                    <option :value="1">Yes</option>
                                    <option :value="0">No</option>
                                    <option :value="-1">All</option>
                                </select>
                            </th>
                        </tr>
                        <tr v-for="elem in pageOfItems">
                            <td><b-checkbox class="checkbox" :checked="checked.includes(elem.ref)" variant="primary" v-on:change="updateChecked(elem.ref)"></b-checkbox></td>
                            <td>{{elem.ref}}</td>
                            <td>{{elem.refSupplier}}</td>
                            <td>{{elem.design}}</td>
                            <td>{{elem.version}}</td>
<!--                            <td><b-checkbox disabled :checked="elem.active === 1"></b-checkbox></td>-->
                            <td>{{elem.active === 1 ? "Yes": "No"}}</td>
<!--                            <td><b-checkbox disabled :checked="elem.signatureDate === null"></b-checkbox></td>-->
                            <td>{{elem.signatureDate === undefined ? "No": elem.signatureDate}}</td>
                        </tr>
                    </table>
                    <jw-pagination class="eq_list_pagination" :pageSize=25 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>
                </b-form>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    components: {
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
                console.log(this.checked);
                return false;
            }
            this.checked.push(ref);
            console.log(this.checked);
            return true;
        },
        newArticle() {
            window.location.href = "/article/add";
        },
        updateArticle() {
            if (this.checked.length === 1) {
                this.compList.forEach(element => {
                    if (element.compFam_ref === this.checked[0]) {
                        window.location.href = "/article/update/" + element.id;
                    }
                });
                this.consList.forEach(element => {
                    if (element.consFam_ref === this.checked[0]) {
                        window.location.href = "/article/update/" + element.id;
                    }
                });
                this.rawList.forEach(element => {
                    if (element.rawFam_ref === this.checked[0]) {
                        window.location.href = "/article/update/" + element.id;
                    }
                });
            } else {
                alert("Please select only one article to update");
            }
        },
        consultArticle() {
            if (this.checked.length === 1) {
                this.compList.forEach(element => {
                    if (element.compFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/" + element.id;
                    }
                });
                this.consList.forEach(element => {
                    if (element.consFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/" + element.id;
                    }
                });
                this.rawList.forEach(element => {
                    if (element.rawFam_ref === this.checked[0]) {
                        window.location.href = "/article/consult/" + element.id;
                    }
                });
            } else {
                alert("Please select only one article to consult");
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
                    return option.ref.toLowerCase().includes(this.searchTermRef);
                });
            } else if (this.searchTermDesign !== "" && this.searchTermRef === "") {
                res = this.globalList.filter(option => {
                    return option.design.toLowerCase().includes(this.searchTermDesign);
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
                    if (this.searchTermValidate === "0")
                        return option.signatureDate === undefined;
                    else if (this.searchTermValidate === "1")
                        return option.signatureDate !== undefined;
                });
            }
            return res;
        }
    },
    created() {
        axios.get('/comp/family/send').then(response => {
            this.compList = response.data;
        }).catch(error => {
            console.log(error.response.data);
        });
        axios.get('/cons/family/send').then(response => {
            this.consList = response.data;
        }).catch(error => {
            console.log(error.response.data);
        });
        axios.get('/raw/family/send').then(response => {
            this.rawList = response.data;
            this.createGlobalList();
            this.loaded = true;
        }).catch(error => {
            console.log(error.response.data);
        });
    }
}
</script>

<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th {
        text-align: center;
    }
    td {
        text-align: center;
        width: auto;
        height: auto;
        margin: auto;
    }
    .list td {
        border: 1px solid black;
    }
    b-checkbox {
        margin: 0;
    }
</style>

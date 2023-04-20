<!--File name : ListOfMME.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 12 Apr 2023-->
<!--Vue Component used to show a list of the different MME in the menu-->

<template>
	<div class="listOfMme">
		<div v-if="loaded==false" >
			<b-spinner variant="primary"></b-spinner>
		</div>
		<div v-if="loaded==true" >
		<ErrorAlert ref="errorAlert"/>
		<h1>MME List</h1>
			<input placeholder="Search an mme by his Alpha Reference" v-model="searchTerm" class="form-control w-50 search_bar" type="text">
		<ul>
			<div class="one_element_list" v-for="(list,index) in pageOfItems " :key="index">
			<li class="list-group-item" :class="'element'+index%2">
				<div class="mme_list_internalReference">
					<b>{{list.mme_internalReference}}</b>
				</div>
				<div class="mme_list_option">
					<router-link :to="{name:'url_mme_consult',params:{id: list.id} }">Consult</router-link>
					<a href="#" @click="warningUpdate(list.id,list.alreadyValidatedTechnical,list.alreadyValidatedQuality)">Update</a>
					<a v-if="list.alreadyValidatedTechnical===false" href="#" @click="technicalValidation(list.id)">Technical validation</a>
					<a v-if="list.alreadyValidatedQuality===false" href="#" @click="qualityValidation(list.id)">Quality validation</a>
					<a v-if="list.alreadyValidatedTechnical===true && list.alreadyValidatedQuality===true">Statut : Approved</a>
					<a @click="reformMME(list.id)" href="#">Reform</a>
					<router-link  :to="{name:'mme_url_lifesheet_pdf',params:{id: list.id} }">Generate PDF</router-link>
				</div>
			</li>
			</div>
		</ul>
		<jw-pagination class="mme_list_pagination" :pageSize=10 :items="filterByTerm" @changePage="onChangePage"></jw-pagination>
		</div>
		<b-modal :id="`modal-updateWarning-${_uid}`"  @ok="warningUpdate(mme_id,technical,quality,true)">
			<p class="my-4">Your mme has a validated life sheet, If you update your mme you will have to
			revalidate </p>
		</b-modal>

	</div>
</template>

<script>
import ErrorAlert from '../../../alert/ErrorAlert.vue'
export default {
	components:{
        ErrorAlert,
	},
    data(){
		return{
			mmes:[],
			searchTerm: "",
			loaded:false,
			mme_id:null,
			technical:null,
			quality:null,
			pageOfItems: []
		}
	},
    computed: {
        filterByTerm() {
            return this.mmes.filter(option => {
                return option.mme_internalReference.toLowerCase().includes(this.searchTerm);
            });
        }
    },
    created(){
		axios.get('/mme/mmes')
			.then (response=>{
				console.log(response.data)
				this.mmes=response.data;
				this.loaded=true;
			})
			.catch(error => console.log(error));
	},
	methods:{
		technicalValidation(id){
			if( this.$userId.user_makeTechnicalValidationRight!=true){
				this.$refs.errorAlert.showAlert("You don't have the right");
			}else{
				this.$router.replace({ name: "url_mme_consult", params: {id:id}, query: {method:"technical" } })
			}

		},
		qualityValidation(id){
			if( this.$userId.user_makeQualityValidationRight!=true){
				this.$refs.errorAlert.showAlert("You don't have the right");
			}else{
				this.$router.replace({ name: "url_mme_consult", params: {id:id}, query: {method:"quality" } })
			}
		},
		reformMME(id){
			if( this.$userId.user_makeReformRight!=true){
				this.$refs.errorAlert.showAlert("You don't have the right");
			}else{
				this.$router.replace({ name: "url_mme_reform", params: {id:id}})
			}

		},
		warningUpdate(id,technical,quality,redirect){
			this.mme_id=id;
			this.technical=technical;
			this.quality=quality;
			if(technical==true && quality ==true){
				this.$bvModal.show(`modal-updateWarning-${this._uid}`)
			}else{
				this.$router.replace({ name: "url_mme_update", params: {id}})
			}
			if(redirect==true){
				if(this.$userId.user_updateDescriptiveLifeSheetDataSignedRight==false && this.$userId.user_deleteDataSignedLinkedToEqOrMmeRight==false ){
					this.$refs.errorAlert.showAlert("You don't have the right");
				}else{
					this.$router.replace({ name: "url_mme_update", params: {id}})
				}

			}
		},
		onChangePage(pageOfItems) {
				console.log(pageOfItems)
				// update page of items
				this.pageOfItems = pageOfItems;
		}

	}
}
</script>

</script>

	<style lang="scss">
	.listOfMme{
		.element0{
			background-color: #ccc;
		}
		h1{
			text-align:center;
		}
		.search_bar{
			margin-left:30px;
			margin-bottom: 20px;
		}
		.mme_list_internalReference{
			display: inline-block;
		}
		.mme_list_option{
			display: block;
			margin-left: 200px;
			margin-top: -20px;
			a{
				margin-right:50px;
			}
		}
	}

	</style>

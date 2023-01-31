<template>
    <div v-if="loaded==false" >
        <div> No data loaded</div>
    </div>
    <div v-else> Coucou</div>

</template>

<script>
export default {

	data(){
		return{
			loaded:false,
		}
	},
  	created(){
        //we need to load the reason of the version upgrade only
		axios.get('/equipment/equipments')
			.then (response=>{
				console.log(response.data)
				this.equipments=response.data;
				this.loaded=true;
			})
			.catch(error => console.log(error));
	},
	methods:{
	
	}




}
</script>

<style lang="scss">

</style>

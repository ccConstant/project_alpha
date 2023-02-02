<template>
    <div v-if="loaded==true" >
		<div id="page">
			<p>'</p>
			<div class="eq_top_infos">
				<div class=" equipement_pdf_logo ">
					<img src="/images/logo.png" alt="Alpha logo" class="logo" >
				</div>

				<div class="equipement_pdf_titre">
					<h2 id="equipement_fiche_de_vie_titre">EQUIPMENT LIFE SHEET DESCRIPTIVE PART</h2>
				</div>

				<div class="equipement_pdf_index">
					<h5>Version : {{eq_idCard.eq_internalReference}}_LS-D_V{{eq_idCard.eq_version}} </h5>
				</div>
				<div class="equipment_revued_by">
					<p >Technical Review <b class="text-primary">{{ eq_idCard.eq_technicalVerifier_firstName}} {{eq_idCard.eq_technicalVerifier_lastName}} </b></p>
				</div>

				<div class="equipment_approuved_by">
					<p>Quality Review <b class="text-primary">{{ eq_idCard.eq_qualityVerifier_firstName}} {{eq_idCard.eq_qualityVerifier_lastName}} </b></p>
				</div>

				<div class="eq_internalReference_pdf">
					<p>Equipment unique ID :</p>
					<h5 class="text-primary">{{eq_idCard.eq_internalReference}}</h5>
				</div>
			</div>	


			<div class="eq_history_pdf">
				<div class="title_history_pdf">
					History Version
				</div>
				<div class="history_table">
					<b-row>
						<b-col cols="1" class="history_table_versionFrom">
							Version before update
						</b-col>
						<b-col cols="1" class="history_table_reason">
							Reason for the update of version
						</b-col>
						<b-col cols="1" class="history_table_versionTo">
							Version after update
						</b-col>
						<b-col cols="4"  class="history_table_Date">
							Date of the update
						</b-col>                  
					</b-row>
					<div v-for="(history,index) in eq_history " :key="index">
						<b-row>
							<b-col cols="1" class="history_table_versionFrom">
								<p class="text-primary"> {{history.history_numVersion}} </p>
							</b-col>
							<b-col cols="1" class="history_table_reason">
								<p class="text-primary">{{history.history_reasonUpdate}}</p>
							</b-col>
							<b-col cols="4" class="history_table_versionTo">
								<p class="text-primary">{{history.history_numVersion+1}}</p>
							</b-col>
							<b-col cols="4"  class="history_table_Date">
								<p class="text-primary"> {{history.history_date}}</p>
							</b-col>                 
						</b-row>
					</div>
				</div>-->
			</div>


			<div class="eq_historyRecordTemplateRefPdf">
			<div class="eqHistory_table_recordTemplateRefPdf">
					<div class="eqHistory_index_recordTemplateRefPdf">
					Record Template Ref :  REC-IWE01
				</div>
				<div class="eqHistory_confidential_recordTemplateRefPdf">
					This document contains CONFIDENTIAL information
				</div>
			</div>
			</div>
		</div>
		<button class="btn btn-primary" @click="generateReport()" >Generate PDF</button>
	</div>  

</template>

<script>
export default {

	data(){
		return{
			loaded:false,
			eq_idCard:null,
			eq_id:this.$route.params.id,
			eq_history:null,
		}
	},
  	created(){
        //we need to load the reason of the version upgrade only
		var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response => {
                this.eq_idCard=response.data;
                console.log(this.eq_idCard)
            })
            .catch(error => console.log(error));

		var consultUrl= (id) => `/history/send/equipment/${id}`;
		axios.get(consultUrl(this.eq_id))
			.then (response => {
				this.eq_history=response.data;
				this.loaded=true;
			})
			.catch(error => console.log(error));
	},

	methods:{
	
		generateReport () {
            let page = document.getElementById('page');
            html2PDF(page, {
                jsPDF: {
                    unit: 'px',
                    format: 'a4',
                    width : 100
                },
                html2canvas: {
                    imageTimeout: 15000,
                    logging: true,
                    useCORS: false,
                },
                  imageType: 'image/jpeg',
                imageQuality: 1,
                margin: {
                    top: 40,
                    right: 10,
                    bottom: 40,
                    left: 10,
                },
                output: this.eq_idCard.eq_internalReference+'_LS-D'+'_V'+this.eq_idCard.eq_version+'.pdf', 
            });
        },
	}




}
</script>

<style lang="scss">
        #page{
            width:1329px;
            font-size : 10px ;
            .text-primary{
                font-size : 20px ;
            }
            .eq_top_infos{
                position: absolute;
                margin-top: 0px;
                margin-left:50px;

                h5{
                        margin-top :auto;
                        width: auto;
                        font-size:25px;
                        text-align:center;
                        font-weight: bold;
                    }

                .equipement_pdf_logo{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 200px;
                    height: 170px;
                    margin-left:100px ;
                    margin-top: 00px;
                    
                    .logo{
                        margin-top:30px;
                    }
                    
                }
                .equipement_pdf_titre{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 00px;
                    left:300px;
                    height: 87px;
                    text-align:center;
                   
                    
                    
                }
                .equipement_fiche_de_vie_titre{
                    text-align: center;
                    position: relative;
                }
                .equipement_pdf_index{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left: 942px;
                    top: 0px;
                    height: 86px;
                    width: 200px;
                    
                }
                .equipment_revued_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;

                    left :300px;
                    top: 86px;
                    height: 84px;
                    width: 400px;

                }
                .equipment_approuved_by{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;


                    left :700px;
                    top: 86px;
                    height: 84px;
                    width: 242px;
                }
                .eq_internalReference_pdf{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    left :942px;
                    top: 86px;
                    width: 200px;
                    height: 84px;
                }

            }
            
        .eq_history_pdf{
            position: relative;
            margin-top:10px ;
            width : 1112px;
			margin-top : 250px;
            
            .title_history_pdf{
                width: 400px;
                font-size : 20px;
                font-weight: bold;
                margin-left:150px;
            }
            .history_table{
                margin-left:163px;
                width:1042px;
                .history_table_versionFrom{
                    border: solid 1px black;
                    text-align: center;
                    width:150px;
                }
                    .history_table_reason{
                    border: solid 1px black;
                    text-align: center;
                    width:540px;
                }
                .history_table_versionTo{
                    border: solid 1px black;
                    text-align: center;
                    width:150px;
                }
                .history_table_Date{
                    border: solid 1px black;
                    text-align: center;
                    width:200px;
                }                    
            }
        }

        .eq_historyRecordTemplateRefPdf{
            position: relative;
            left:150px;
            margin-top:10px ;
            width: 1042px;
            

            .eqHistory_table_recordTemplateRefPdf{
                .eqHistory_confidential_recordTemplateRefPdf{
                    border: solid 1px black;
                    background-color: lightgrey;
                    text-align: center;
                    height: auto;
                    font-size: 20px;
                    font-style : italic;
                }
                .eqHistory_index_recordTemplateRefPdf{
                    background-color: lightgrey;
                    border: solid 1px black;
                    text-align: center;
                    height: auto;
                    font-size: 20px;
                }
            }
        }
    }

        
        

    

</style>

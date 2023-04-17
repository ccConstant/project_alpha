<!--File name : ListOfEquipmentPDF.vue-->
<!--Creation date : 10 Jan 2023-->
<!--Update date : 11 Apr 2023-->
<!--Vue Component to generate a pdf version of the equipment list-->

<template>
    <div v-if="loaded==true">
        <div id="page">
            <p>'</p>
            <div class="list_eq_top_infos">
                <div class="list_eq_pdf_logo ">
                  <img src="/images/logo.png" alt="Alpha logo" class="logo" >
                </div>

                <div class="list_eq_pdf_titre">
                    <h2 id="list_eq_fiche_de_vie_titre">LIST OF EQUIPMENT USED BY ALPHA </h2>
                </div>

                <div class="list_eq_pdf_index">
                    <h5> </h5>
                </div>


            </div>
            <div class="eq_infos_pdf">
                <div class="eq_table">
                    <b-row>
                        <b-col cols="1" class="eq_table_internalReference">
                            Internal Reference
                        </b-col>
                        <b-col cols="1" class="eq_table_externalReference">
                            External Reference
                        </b-col>
                        <b-col cols="1" class="eq_table_name">
                            Name
                        </b-col >
                        <b-col cols="4" class="eq_table_state">
                            Actual State
                        </b-col>
                    </b-row>
                    <div v-for="(eq,index) in list_eq " :key="index">
                        <b-row>
                            <b-col cols="1" class="eq_table_internalReference">
                               <p class="text-primary"> {{eq.eq_internalReference}} </p>
                            </b-col>
                            <b-col cols="1" class="eq_table_externalReference">
                                <p class="text-primary">{{eq.eq_externalReference}}</p>
                            </b-col>
                            <b-col cols="4" class="eq_table_name">
                                <p class="text-primary">{{eq.eq_name}}</p>
                            </b-col>
                            <b-col cols="4"  class="eq_table_state">
                               <p class="text-primary"> {{eq.eq_state}}</p>
                            </b-col>
                        </b-row>


                    </div>
                </div>
            </div>
        </div>
         <button class="btn btn-primary" @click="generateReport" >Generate PDF</button>
    </div>
</template>

<script>
import html2PDF from 'jspdf-html2canvas';
export default {
    data(){
        return{
            eq_id:this.$route.params.id,
            list_eq:null,
            loaded:false,
        }
    },

    components: {

    },
    methods: {

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
                output: 'listOfAllEquipmentUsedByAlph.pdf',
            });
        },


    },
    created(){
        axios.get('/equipment/equipments')
			.then (response=>{
				console.log(response.data)
				this.list_eq=response.data;
				this.loaded=true;
			})
			.catch(error => console.log(error));
	},
}
</script>

<style lang="scss">
        #page{
            width:1329px;
            font-size : 10px ;
            .text-primary{
                font-size : 20px ;
            }
            .list_eq_top_infos{
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

                .list_eq_pdf_logo{
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
                .list_eq_pdf_titre{
                    border: solid 0.5px black;
                    margin: auto;
                    position: absolute;
                    width: 642px;
                    top: 00px;
                    left:300px;
                    height: 87px;
                    text-align:center;



                }
                .list_eq_fiche_de_vie_titre{
                    text-align: center;
                    position: relative;
                }
                .list_eq_pdf_index{
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


        .eq_infos_pdf{
            position: relative;
            margin-top:300px ;
            width : 1112px;

            .title_eq_pdf{
                width: 400px;
                font-size : 20px;
                font-weight: bold;
                margin-left:150px;
            }
            .eq_table{
                margin-left:163px;
                width:1042px;
                .eq_table_internalReference{
                    border: solid 1px black;
                    text-align: center;
                    width:300px;
                    height:auto;
                }
                .eq_table_externalReference{
                    border: solid 1px black;
                    text-align: center;
                    width:300px;
                    height:auto;
                }
                .eq_table_name{
                    border: solid 1px black;
                    text-align: center;
                    width:200px;
                    height:auto;
                }
                .eq_table_state{
                    border: solid 1px black;
                    text-align: center;
                    width:242px;
                    height:auto;
                }
            }
        }
    }






</style>

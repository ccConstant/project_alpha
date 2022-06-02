<template>
    <div>
        <div ref="content">
            <div class=" equipement_pdf_logo ">
            LOGO<br>
            LOGO<br>
            LOGO<br>
            LOGO<br>
            LOGO<br>
            </div>
            <div class="equipement_pdf_titre">
            <h2 id="equipement_fiche_de_vie_titre">FICHE DE VIE</h2>
            <h2>DESCRIPTION DE L'EQUIPEMENT</h2>
            </div>
            <div class="equipement_pdf_version">
            <h2>ENR QA-25</h2>
            </div>

        </div>
        
            <button @click="generateReport" class="btn btn-primary">Generate</button>
    </div>  
</template>

<script>
import jsPDF from 'jspdf'
export default { 
    data(){
        return{
            eq_id:this.$route.params.id,
            eq_idCard:null,
            eq_dimensions:null,
            eq_powers:null,
            eq_spProc:null,
            eq_usg:null,
            eq_file:null,
            eq_prvMtnOp:null,
            eq_risk:null,
            loaded:false,
        }
    },

    components: {
        
    },
    methods: { 
        generateReport () {
            
        }
    },
    created(){
        var consultUrl = (id) => `/equipment/${id}`;
        axios.get(consultUrl(this.eq_id))
            .then (response => this.eq_idCard=response.data)
            .catch(error => console.log(error));


        var consultUrlDim = (id) => `/dimension/send/${id}`;
        axios.get(consultUrlDim(this.eq_id))
            .then (response=> this.eq_dimensions=response.data)
            .catch(error => console.log(error)) ;
        
        var consultUrlPow = (id) => `/power/send/${id}`;
        axios.get(consultUrlPow(this.eq_id))
            .then (response=> this.eq_powers=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlSpProc = (id) => `/spProc/send/${id}`;
        axios.get(consultUrlSpProc(this.eq_id))
            .then (response=>{
                if(response.data==""){
                    this.eq_spProc=[];
                }else{
                    this.eq_spProc=response.data;
                }
            })
            .catch(error => console.log(error)) ;
        
        var consultUrlUsg = (id) => `/usage/send/${id}`;
        axios.get(consultUrlUsg(this.eq_id))
            .then (response=>this.eq_usg=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlFile = (id) => `/file/send/${id}`;
        axios.get(consultUrlFile(this.eq_id))
            .then (response=>this.eq_file=response.data)
            .catch(error => console.log(error)) ;

        var consultUrlPrvMtnOp = (id) => `/prvMtnOps/send/${id}`;
        axios.get(consultUrlPrvMtnOp(this.eq_id))
            .then (response=>this.eq_prvMtnOp=response.data)
            .catch(error => console.log(error)) ;
        
        var consultUrlRisk = (id) => `/equipment/risk/send/${id}`;
        axios.get(consultUrlRisk(this.eq_id))
            .then (response=>{
                this.eq_risk=response.data
                this.loaded=true;
            })
            .catch(error => console.log(error)) ;
    }
}
</script>

<style lang="scss">
        .equipement_pdf_logo{
            border: solid 1px black;
            margin: auto;
            position: absolute;
            width: 100px;
            left: 100px;
            
        }
        .equipement_pdf_titre{
            border: solid 1px black;
            margin: auto;
            position: absolute;
            width: 500px;
            left: 200px;
            
        }
        .equipement_fiche_de_vie_titre{
            text-align: center;
        }
        .equipement_pdf_version{
            border: solid 1px black;
            margin: auto;;
            width: 200px;

            
        }

    

</style>
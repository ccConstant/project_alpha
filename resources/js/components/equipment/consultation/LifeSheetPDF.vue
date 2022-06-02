<template>
   <div>
     <VueHtml2pdf
        :show-layout="false"
        :float-layout="true"
        :enable-download="true"
        :preview-modal="true"
        :paginate-elements-by-height="1400"
        filename="myPDF"
        :pdf-quality="2"
        :manual-pagination="false"
        pdf-format="a4"
        pdf-orientation="landscape"
        pdf-content-width="800px"
        ref="html2Pdf"
    >
        <section slot="pdf-content">
            <div>
                <h2>FICHE DE VIE</h2>
            </div>
        </section>
    </VueHtml2pdf>
    <button @click="generateReport" class="btn btn-primary">Generate</button>
   </div>
</template>

<script>
import VueHtml2pdf from 'vue-html2pdf'
 
export default { 
    components: {
        VueHtml2pdf
    },
    methods: { 
        generateReport () {
            this.$refs.html2Pdf.generatePdf()
        }
    },
}
</script>
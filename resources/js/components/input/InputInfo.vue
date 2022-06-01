<!--File name : InputInfo.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 05 May 2022-->
<!--Displays a clickable information button that show a help text to facilitate user input-->

<template>
    <div class="infoInput">
        <img src="../../../assets/info_logo.png" alt="info_logo">

        <div class="textInfo">
            <p hidden>{{info}}</p>
        </div>
        
    </div>
    
</template>

<script>
    
export default {
    methods: {
        showInfo(){
            console.log("Afficher info");
            $('.myPopover').popover();
        }
        

    },
    props:['info']

}
</script>

<style lang="scss">
    .infoInput {
        display:inline-block;
    }
    img{
        padding-left: 5px;
        width : 15px;
        height : 10px;
        transition:1ms ;
    }
    img:hover{
        -ms-transform: scale(1.5); 
        -webkit-transform: scale(1.5); 
        transform: scale(1.5);
    }
    .textInfo p{
        font-size: 12px;
    }

    

</style>

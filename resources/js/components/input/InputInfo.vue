<!--File name : InputInfo.vue-->
<!--Creation date : 27 Apr 2022-->
<!--Update date : 05 May 2022-->
<!--Displays a clickable information button that show a help text to facilitate user input-->

<template>
    <div class="infoInput">
        <b-icon class="info_icon" :id="`popover-target-${_uid}`" icon="info-circle-fill" variant="primary"></b-icon>
        <b-popover :target="`popover-target-${_uid}`" triggers="hover" placement="right">
            {{info}}
        </b-popover>
        
    </div>
    
</template>

<script>
    
export default {
    props:['info']

}
</script>

<style lang="scss">
    .infoInput {
        display:inline-block;
    }
    .info_icon{
        padding-left: 5px;
        width : 20px;
        height : 15px;
        transition:1ms ;
    }
    .info_icon:hover{
        -ms-transform: scale(1.5); 
        -webkit-transform: scale(1.5); 
        transform: scale(1.5);
    }
    .textInfo p{
        font-size: 12px;
    }
    

    

</style>

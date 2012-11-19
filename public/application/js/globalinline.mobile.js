

$(function(){
    
    //disable transition jquery mobile on anything else than iphone
    if((!navigator.userAgent.match(/iPhone/i)) || (!navigator.userAgent.match(/iPod/i))) {
        $.extend(  $.mobile , {
            defaultPageTransition:"none",
            defaultDialogTransition:"none"
        });
    }
    
/*
================================================================================
front access page : slide show
================================================================================
*/  
    
    if ($('#camera_wrap_1').length > 0){
        $('#camera_wrap_1').camera({
            thumbnails: false
        });
    }

}); 

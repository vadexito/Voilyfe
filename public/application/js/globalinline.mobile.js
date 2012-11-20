

$(function(){
    
/*
================================================================================
iphone specificity
================================================================================
*/  

    //disable transition jquery mobile on anything else than iphone
    if((!navigator.userAgent.match(/iPhone/i)) || (!navigator.userAgent.match(/iPod/i))) {
        $.extend(  $.mobile , {
            defaultPageTransition:"none",
            defaultDialogTransition:"none"
        });
    }
}); 

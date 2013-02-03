$(function(){
    

/*
================================================================================
front access page : slide show
================================================================================
*/  
    
    if ($('#camera_wrap_1').length > 0){
        $('#camera_wrap_1').camera({
            height: 'auto',
            pagination: false,
            hover: false,
            opacityOnGrid: false,
            imagePath: '/images/images-camera/slides/'
        });
    }

}); 

    


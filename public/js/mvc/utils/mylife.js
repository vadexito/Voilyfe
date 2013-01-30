function mylife (){
    
    this.showPopupMap = function(pageId,popupId,mapOptions,googleMapFunc){
        
        if ($('#'+popupId).length === 0){
            
            $('#'+pageId).append(new PopupView({id:popupId,attributes:{
                'data-role'          : 'popup',
                'data-overlay-theme' : 'a',
                'data-theme'         : 'a',
                'data-corners'       : 'false'
            },deleteButton: 'true'}).render());
            
            $('#'+popupId).append('<div class="google_map" id="'+mapOptions.id+'"></div>');
            
            googleMapFunc(mapOptions);
            
            $('#'+popupId).trigger('create').popup();
        }
        $('#'+popupId).popup('open'); 
    };
};





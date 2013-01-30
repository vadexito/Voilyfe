window.GpsButtonView = Backbone.View.extend({
    
    tagName     : 'img',
    className   : 'icon-gps',
    attributes: {
        'src' : '/images/icons/other/icon-gps-saved.jpg'        
    },
    
    initialize: function(){ 
    },  
        
    events:{
        'click' : 'showPopupMap'
    },
    
    render: function(){
        
        return this.$el;
    },
   
    showPopupMap: function(e){
        
        var page = $(e.currentTarget).parents().filter('div[data-role="page"]').first().attr('id');
        var idPopup = 'popupMap-'+page;        
        var mapOptions = {
            id : 'map',
            latitude: this.options.latitude,
            longitude: this.options.longitude,
            markerTitle: this.options.markerTitle
        };
        
        new mylife().showPopupMap(page,idPopup, mapOptions, this.createMap);
    },
    
    createMap: function(options){
            
        var eventPosition = new google.maps.LatLng(options.latitude,options.longitude);
        
        //create map
        var map = new google.maps.Map($('#'+options.id).get(0), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: eventPosition,
            zoom: 15
        });

        // add marker
        new google.maps.Marker({
            position: eventPosition,
            map: map,
            title: options.markerTitle ? options.markerTitle : ''
        });
    }
    
});


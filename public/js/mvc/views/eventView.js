window.EventView = Backbone.View.extend({
    
    initialize: function(){
        
        this.template = _.template( $("#event-details-template").html());
        this.templatePopup = _.template( $("#event-deletePopup-template").html());
    },
    
    tagName: 'div',
    attributes: {
        'id'  : 'event-single'
    },
    
    events:{
        'click img.icon-gps'        : 'showMap'
    },
    
    
    showMap: function(e){
        
        var event = mainPage.options.lastEventsCollection.get(
            $(e.currentTarget).attr('data-eventid')
        );
        
        var page = $(e.currentTarget).parents().filter('div[data-role="page"]').first().attr('id');
        
        var idPopup = 'popupMap-'+page;        
        
        if ($('#'+idPopup).length === 0){
            
            $('#'+page).append(new PopupView({model:event,id:idPopup}).render());
            $('#'+idPopup).append('<div id="map"></div>');
            this.createMap('map',event);
            $('#'+idPopup).trigger('create').popup();
        }
        $('#'+idPopup).popup('open'); 
    },

    createMap: function(id,event){
        var eventPosition = new google.maps.LatLng(
                event.get('latitude'),
                event.get('longitude'));

        //create map
        var map = new google.maps.Map($('#'+id).get(0), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: eventPosition,
            zoom: 15
        });

        // add marker
        new google.maps.Marker({
            position: eventPosition,
            map: map,
            title: event.get('address') ? event.get('address') : event.get('category')
        });
    },
    
    render: function(){
        
        this.$el.html( this.template(this.model.toJSON()) );
            
         
        this.popup = this.templatePopup({
            eventId     : this.model.get('id'),
            categoryId  : this.model.get('categoryId')
        });
        return this.el;
    }
    
});


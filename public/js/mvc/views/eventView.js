window.EventView = Backbone.View.extend({
    
    tagName: 'div',    
    attributes: {
        'id'  : 'event-single'
    },
    template: _.template( $("#event-details-template").html() ),
    
    initialize: function(){
        
        this.templatePopup = _.template( $("#event-deletePopup-template").html());
    },
    
    render: function(){
        
        this.$el.html( this.template(this.model.toJSON()) );
        
        //if there are GPS coordinates
        if (this.$el.find('img.icon-gps').attr('data-eventid')){
            
            var event = mainPage.options.lastEventsCollection.get(
                this.$el.find('img.icon-gps').attr('data-eventid')
            );    

            new GpsButtonView({
                el : this.$el.find('img.icon-gps'),
                latitude    :event.get('latitude'),
                longitude   :event.get('longitude'),
                markerTitle :event.get('address')
            });

            this.popup = this.templatePopup({
                eventId     : this.model.get('id'),
                categoryId  : this.model.get('categoryId')
            });
            
        }
        
        return this.el;
    }
    
});


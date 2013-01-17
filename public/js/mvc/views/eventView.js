window.EventView = Backbone.View.extend({
    
    initialize: function(){
        
        this.template = _.template( $("#event-details-template").html());
        this.options.popup = _.template( $("#event-deletePopup-template").html());
    },
    
    tagName: 'div',
    attributes: {
        'id'  : 'event-single'
    },

    
    
    render: function(){
        
        this.$el.html( this.template({
            eventId                 : this.model.get('id'),
            date                    : this.model.generateEventLineProperties().date,
            userImage               : '<img alt="user-image" src="'+this.model.get('imgSrc')+'">',
            specificProperties      :this.model.generateEventLineProperties().specific.join(', '),
            commonProperties        :this.model.generateEventLineProperties().common.join(', ')
        }));
        
        this.options.popup({'eventId': this.model.get('id')})
        return this.el;
    }
    
});


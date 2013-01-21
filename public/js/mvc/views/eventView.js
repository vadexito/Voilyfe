window.EventView = Backbone.View.extend({
    
    initialize: function(){
        
        this.template = _.template( $("#event-details-template").html());
        this.templatePopup = _.template( $("#event-deletePopup-template").html());
    },
    
    tagName: 'div',
    attributes: {
        'id'  : 'event-single'
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


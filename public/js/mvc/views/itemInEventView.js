window.ItemInEventView = Backbone.View.extend({
    
    model:ItemInEvent,
    
    tag: 'span',
    
    initialize: function(){
        this.template = _.template( $("#event-property-template").html());
    },
        
    render: function(){
        
               
        this.$el.html(this.template(this.model.toJSON()));
        return this.el;
    }
    
    
    
});


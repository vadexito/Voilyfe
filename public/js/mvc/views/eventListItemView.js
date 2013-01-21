window.EventListItemView = Backbone.View.extend({
    
    tagName:'li',
    className :'event-line',
    
    initialize: function(){
        
        this.template = _.template( $("#event-line-template").html());
       
        
    },
    
    
    
    render: function(){
       
        this.$el.html( this.template (this.model.toJSON()));
        
        return this.el;
       
    }
    
});


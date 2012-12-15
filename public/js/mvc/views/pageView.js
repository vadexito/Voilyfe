window.PageView = Backbone.View.extend({
    
    tagName: 'div',
    attributes: {
        'data-role' : 'page',
        'id' : 'event-list-page'
    },
    
    model:Page,
    
    initialize: function(){
        
        this.template = _.template( $("#last-events-page-template").html());
        
    },
    
    render: function(){
        
       this.$el.html(this.template(this.model.toJSON()));
       return this.$el;
    }
    
});


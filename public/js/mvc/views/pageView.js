window.PageView = Backbone.View.extend({
    
    tagName: 'div',
    attributes: {
        'data-role' : 'page',
        'id' : 'event-list-page'
    },
    
    model:Page,
    
    initialize: function(){
        
        if (this.options.template == 'first-level'){
            this.template = _.template( $("#indexpage-first-level-template").html());
        } else {
            this.template = _.template( $("#indexpage-second-level-template").html());
        }
        
        
    },
    
    render: function(){
        
       this.$el.html(this.template(this.model.toJSON()));
       return this.$el;
    }
    
});


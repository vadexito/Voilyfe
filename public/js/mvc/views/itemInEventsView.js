window.ItemInEventsView = Backbone.View.extend({
    
    tag: 'span',
    
    initialize: function(){
        
    },
        
    render: function(){
        
        var render = [];
        _.each(this.model.models,function(itemModel){
            
            var item = new ItemInEventView({model:itemModel});
            render.push($(item.render()).html());            
        });        
        
        this.$el.html(render.join(', '));
        
        return this.el;
    }    
        
        
    
});


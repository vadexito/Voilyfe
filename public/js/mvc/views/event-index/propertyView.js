window.PropertyView = Backbone.View.extend({
    
    tagName : 'div',
    
    initialize: function(){
        
        
    },
    
    render : function(){
        
        this.$el.val('coucou')
        
        return this.$el.html();
    }
    
});


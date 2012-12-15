window.EventListView = Backbone.View.extend({
    
    initialize: function(){
        
    },
    
    tagName: 'ul',
    attributes: {
        'id'  : 'event-list',
        'data-role'  : 'listview'
    },

    
    
    render: function(){
        
        _.each(this.model.models,function(event){
            
        this.$el.append(new EventListItemView({model:event}).render()); 
        },this);
        
        return this.el;
    }
    
});


window.EventListView = Backbone.View.extend({
    
    initialize: function(){
        
    },
    
    tagName: 'ul',
    
    attributes: {
        'id'            : 'event-list',
        'data-role'     : 'listview'
    },

    
    
    render: function(){
        
        _.each(this.model.models,function(event){
            
            this.appendNewEvent(event);
        },this);
        
        return this.el;
    },
    
    appendNewEvent: function(event){
        this.$el.append(new EventListItemView({model:event,active:this.options.active}).render()); 
    }
    
});


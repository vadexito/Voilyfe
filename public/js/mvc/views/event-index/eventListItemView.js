window.EventListItemView = Backbone.View.extend({
    
    tagName:'li',
    className :'event-line',
    attributes:{
        'data-icon' : "false"
    },
    initialize: function(){
        
        this.template = _.template( $("#event-line-template").html());
        
    },
    
    render: function(){       
       
        this.$el.html( this.template (this.model.toJSON()));
        this.$el.find('a').attr('data-active',this.options.active);
        return this.el;
       
    }
    
});


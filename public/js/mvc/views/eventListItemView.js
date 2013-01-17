window.EventListItemView = Backbone.View.extend({
    
    tagName:'li',
    className :'event-line',
    
    initialize: function(){
        
        this.template = _.template( $("#event-line-template").html());
       
        
    },
    
    
    
    render: function(){
       
        this.$el.html( this.template ({
                href:this.model.get('href'),
                eventId:this.model.get('id'),
                imgSrc:this.model.get('imgSrc'),
                aside:this.model.get('aside'),
                title: this.model.generateEventLineProperties().date,
                specificProperties:this.model.generateEventLineProperties().specific.join(', '),
                commonProperties:this.model.generateEventLineProperties().common.join(', ')
        }));
        
        return this.el;
       
    }
    
});


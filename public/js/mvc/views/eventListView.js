window.EventListView = Backbone.View.extend({
    
    initialize: function(){
        
        this.template = _.template( $("#event-line-template").html());
        
    },
    
    el: '#event-list',
    
    render: function(){
        this.$el.html( this.template ({
            href :"test",
            imgSrc :"test",
            mainTitle :"test",
            commonProperties : 'j',
            specificProperties : 'j'
        }) );
        
        return this.el;
    }
    
    
    
});


window.ItemInEventView = Backbone.View.extend({
    
    model:ItemInEvent,
    
    tag: 'span',
    
    initialize: function(){
        this.template = _.template( $("#event-property-template").html());
    },
        
    render: function(){
        
        this.$el.html(this.template(this.model.toJSON()));     
//        this.$el.html( '<img src="'
//            +this.model.get('srcIcon')
//            +'" style="width:15px;height:15px;"/> '
//            + this.model.get('value'));
        
        return this.el;
    }   
    
});


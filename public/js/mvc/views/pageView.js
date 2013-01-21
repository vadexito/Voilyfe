window.PageView = Backbone.View.extend({
    
    tagName: 'div',
    attributes: {
        'data-role' : 'page',
        'id' : 'event-list-page'
    },
    
    model:Page,
    
    initialize: function(){
        
        this.template = _.template( $("#indexpage-"+this.options.template+"-template").html());
    },
    
    render: function(){
        
       this.$el.html(this.template(this.model.toJSON()))               
            .find('#' + this.options.active).addClass('ui-btn-active ui-state-persist'); 
       return this.$el;
    }
    
});


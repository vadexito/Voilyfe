window.PopupView = Backbone.View.extend({
    
    tag: 'div',
    
    
    initialize: function(){
        if (this.options.deleteButton === 'true'){
            this.$el.append('<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class=" close-popup ui-btn-right">Close</a>');
        }
        
        
    }, 
    
    render: function(){
        
        return this.el;
    }
    
    
    
    
});


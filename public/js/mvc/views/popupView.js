window.PopupView = Backbone.View.extend({
    
    tag: 'div',
    attributes:{
       'data-role'          : 'popup',
       'data-overlay-theme' : 'a',
       'data-theme'         : 'a',
       'data-corners'       : 'false'
       
    },
    
    initialize: function(){
        
        this.$el.append('<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class=" close-popup ui-btn-right">Close</a>');
        
    }, 
    
    events:{
        'click a.close-popup': 'closePopup'
    },
    
    render: function(){
        
        return this.el;
    },
    closePopup: function(){
        this.remove();
    }
    
    
    
});


window.CalendarView = Backbone.View.extend({
    
    
    
    initialize: function(){
        
        this.addLinkToCalEvent();
    },
    
    addLinkToCalEvent: function (){
        
        $('div.ui-datebox-griddate').filter(function(){
            return !($(this).hasClass('ui-datebox-griddate-disable') ||
                $(this).hasClass('ui-datebox-griddate-empty'));
            }).each(function(){
                $(this).html('<a class="event-date ui-link" data-form="ui-body-b" data-theme="c" href="#">' 
                    + $(this).html() + '</a>')
        });
    },
        
        
    events:{
        'datebox' : 'showEvent'
    },
    
    showEvent: function(e, passed) { 

        if ( passed.method === 'postrefresh') {
            this.addLinkToCalEvent();
        }
    }
    
});


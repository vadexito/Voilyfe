window.CalendarView = Backbone.View.extend({
    
    initialize: function(){
        
        this.addLinkToCalEvent();
    },
    
    addLinkToCalEvent: function (){
        
        $('div.ui-datebox-griddate').filter(function(){
            return !($(this).hasClass('ui-datebox-griddate-disable') ||
                $(this).hasClass('ui-datebox-griddate-empty'));
            }).each(function(){
                $(this).html('<a class="calendar-event-date ui-link" data-form="ui-body-b" data-theme="c" href="#">' 
                    + $(this).html() + '</a>');
                $(this).removeClass('ui-btn-up-d').addClass('ui-btn-down-d');
        });
        
        $('div.ui-datebox-griddate').filter(function(){
            return ($(this).hasClass('ui-datebox-griddate-disable') );
            }).removeClass('ui-datebox-griddate-disable').each(function(){
                $(this).html('<a class="calendar-noevent-date" data-form="ui-body-b" data-theme="c">' 
                    + $(this).html() + '</a>');
        });
        
    },
        
        
    events:{
        'datebox'                       : 'showEvent',
        'click a.calendar-noevent-date' : 'addEvent'
    },
    
    addEvent: function(){
        mainPage.changePage('newEvent',{date:''});
    },
    
    showEvent: function(e, passed) { 

        if ( passed.method === 'postrefresh') {
            this.addLinkToCalEvent();
        }
    }
    
});


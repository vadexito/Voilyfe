window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        
    },
    
    events : {
        'click a.winner-list-line' : 'viewEvents'
    },
    
    viewEvents : function(e){
        
        var eventList = new EventListView();
        eventList.render().listview('refresh');
        $.mobile.changePage('#event-list-page');
        
    }
    
    
});


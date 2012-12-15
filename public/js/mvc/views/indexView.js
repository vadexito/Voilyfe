window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        this.initEventsLast();
    },
    
    collection : new Events,
    
    initEventsLast: function(){
        
        this.collection.add(lastEvents);
    },
    
    events : {
        'click a.winner-list-line' : 'viewEvents'
    },
    
    viewEvents : function(e){
        
        var events = new Events();
        var value = $(e.currentTarget).find('h3').html();
        
        
        _.each($.parseJSON($(e.currentTarget).attr('data-events')),function(id){
            events.add(this.collection.get(id));
        },this);
        
        var eventList = new EventListView({model:events});
        
        var modelPage = new Page({
            title:value
        });
        var page = new PageView({model: modelPage});
        page.attributes.id = $(e.currentTarget).attr('data-item')+'-'+value+"-page";
        
        page.render().find('div[data-role="content"]').append(eventList.render());
        this.$el.append(page.el);
        
        $.mobile.changePage('#'+page.$el.attr('id'));
    }
    
    
});


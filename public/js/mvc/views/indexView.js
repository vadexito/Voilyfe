window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        this.initEventsLast();       
        
        $(document).on('beforepagechange',function(){ 
            
            if (this.options.currentPage){                
                this.options.currentPage.close();
                this.options.currentPage =null;
            }            
        });
        
    },
    
    //collection : new Pages,
    
    initEventsLast: function(){
        
        //lastEvents is defined in the html page as a global variable
        this.options.lastEventsCollection = new Events(lastEvents);
    },
    
    events : {
        'click a.winner-list-line'  : 'viewListEvents',
        'click a.event-line-link'   : 'showEvent'
    },
    
    showEvent: function(e){
        
       e.preventDefault();
       var model = this.options.lastEventsCollection.get($(e.currentTarget).attr('data-eventid'));
       var event = new EventView({model:model});
       this.addPage({
            id: 'event-' + model.get('id'),
            title: '',
            content: event.render(),
            popup: event.popup
        });
    },
    viewListEvents : function(e){
        
        //Create collection for subgroup of events corresponding to tag
        var tagEvents = new Events();
        var tagValue = $(e.currentTarget).find('h3').html();
        
        _.each($.parseJSON($(e.currentTarget).attr('data-events')),function(id){
            tagEvents.add(this.options.lastEventsCollection.get(id));            
        },this);
        
        this.addPage({
            id: $(e.currentTarget).attr('data-item')+'-'+tagValue+"-page",
            title: tagValue,
            content: (new EventListView({model:tagEvents})).render()
        });
    },
    
    addPage: function(options,NoChangePage){
        
        var page = new PageView({
            model: new Page({title:options.title}),
            id:options.id
        });
        
        // add content in the page
        if (options.content){
            page.render().find('div[data-role="content"]').append(options.content);
            $('body').append(page.el);
        }
        
        //possibly add popup (inside the page to be jquery mobile compatible)
        if (options.popup){
            
            page.$el.append(options.popup);
        }
        
        if (!NoChangePage){
          $.mobile.changePage('#'+options.id);  
        }
        this.options.currentPage = page;       
    }
    
});


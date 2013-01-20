window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        this.initEventsLast();
        
        if ($('#calendar-widget').length > 0){       
            this.calendar = new CalendarView({el:$('#calendar-widget')});
        } 
    },
    
    initEventsLast: function(){
        
        //lastEvents is defined in the html page as a global variable
        this.options.lastEventsCollection = new Events(lastEvents);
    },
    
    events : {
        'click a.winner-list-line'  : 'viewListEvents',
        'click a.event-line-link'   : 'showEvent',
        'datebox' : 'showEventofDate'
    },
    
    showEventofDate:function(e, passed){
        if ( passed.method === 'set') {
            
            var theDate = this.calendar.$el.data('datebox').theDate;
            var EventsAtDate = new Events();

            this.options.lastEventsCollection.each( function(event){
                
                if ((new Date(event.get('W3CDate')) - theDate) === 0){
                    EventsAtDate.add(event);       
                }            
            });
       
            this.openPage({
                id: "new-page-for-daily-events-"+theDate.getFullYear()+'-'+ (theDate.getMonth()+1) +'-'+theDate.getDate(),
                title: "",
                content: new EventListView({model:EventsAtDate}).render()
            });
        }
        
    },
    
    showEvent: function(e){
        
       e.preventDefault();
       var model = this.options.lastEventsCollection.get($(e.currentTarget).attr('data-eventid'));
       var event = new EventView({model:model});
       this.openPage({
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
        
        this.openPage({
            id: $(e.currentTarget).attr('data-item')+'-'+tagValue.replace(/ /g,'')+"-page",
            title: tagValue,
            content: (new EventListView({model:tagEvents})).render()
        });
    },
    
    openPage: function(options,NoChangePage){    
        
        var pageNotFound = true;
        
        //if the page already exists
        $('div[data-role="page"]').each(function(index,page){                
            if (options.id == $(page).attr('id')){
                
                if (!NoChangePage){
                    
                    $.mobile.changePage('#'+options.id);
                    pageNotFound = false;
                }
            }
        });
        
        if (pageNotFound){
            //if the page doesn't exists we create it
            var page = new PageView({
                model   : new Page({title:options.title}),
                id      :options.id
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
        }  
    }
    
});


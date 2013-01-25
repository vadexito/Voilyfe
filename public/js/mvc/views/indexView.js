window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        this.initEventsLast();
        this.initGraphs();
        this.initCalendar();
        this.initLastEventsPage();        
        
        
        mainPage = this;
    },
    
    events : {
        'click a.event-line-link'   : 'showEvent',
        'datebox'                   : 'showEventofDate',
        'click .user-image'         : 'showImage'
    },
    
    showEvent: function(e){
        
       e.preventDefault();
       var model = this.options.lastEventsCollection.get($(e.currentTarget).attr('data-eventid'));
       var event = new EventView({model:model});
       this.openPage({
            id          : 'event-' + model.get('id'),
            title       : '',
            content     : event.render(),
            popup       : event.popup,
            active      : $(e.currentTarget).attr('data-active'),
            template    :'event-details'
        });        
    },
    
    initCalendar: function(){
        if ($('#calendar-widget').length > 0){       
            this.calendar = new CalendarView({el:$('#calendar-widget').parent()});
        } 
    },
    
    initLastEventsPage: function(){
        var id = 'last-events-page';
        var page = this.openPage({
            id          : '',
            content     : (new EventListView({model:this.options.lastEventsCollection,active:'last-events'})).render(),
            template    : 'first-level',
            active      : 'last-events'
        },true,true);
        
        $('#' + id).html(page.$el.html());
        if ($.mobile.activePage.attr('id') == id){
            $('#' + id).trigger("pagecreate");        
        }        
    },
    
    initGraphs: function(){ 
        
        _.each($('.google-chart'),function(graph){
            new GraphView({el: graph});
        });
    },
    
    initEventsLast: function(){
        
        this.options.lastEventsCollection = new Events(lastEvents);        
        
    },
    
    
    
    showEventofDate:function(e, passed){
        if ( passed.method === 'set') {
            
            var theDate = this.calendar.$el.find('input').data('datebox').theDate;
            var EventsAtDate = new Events();

            this.options.lastEventsCollection.each( function(event){
                
                if ((new Date(event.get('W3CDate')) - theDate) === 0){
                    EventsAtDate.add(event);       
                }            
            });
       
            this.openPage({
                id      : "new-page-for-daily-events-"+theDate.getFullYear()+'-'+ (theDate.getMonth()+1) +'-'+theDate.getDate(),
                title   : "",
                content : new EventListView({model:EventsAtDate,active:'calendar'}).render(),
                active  : 'calendar',
                template: 'second-level'
            });
        }
        
    },
    
    
    
    showImage: function(e){
        
        this.openPage({
            id      : $(e.currentTarget).attr('src')
                .toLowerCase().replace(/\/(.*)\//g, '').replace(/\_/,'').replace(/\./,''),
            title   : '',
            content : $(e.currentTarget).clone().removeClass('user-image').addClass('image-full-page'),
            template:'event-details'
        });
        
    },
    
    openPage: function(options,NoChangePage,NoAddDOM){    
        
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
        
        //if the page doesn't exist, we create it
        if (pageNotFound){
            
            var page = new PageView({
                model   : new Page({title:options.title}),
                id      : options.id,
                template: options.template,
                active  : options.active
            });
            
            // add content in the page
            if (options.content){
                page.render().find('div[data-role="content"]').append(options.content);
                if (!NoAddDOM){
                   $('body').append(page.el); 
                }                
            }

            //possibly add popup (inside the page to be jquery mobile compatible)
            if (options.popup){            
                page.$el.append(options.popup);
            }

            if (!NoChangePage){
                
                $.mobile.changePage('#'+options.id);
            }
        }
        
        return page;
    },
    
    
    changePage: function(namePage,options){
        
        var id;
        
        if (namePage == 'newEvent'){
            
            window.location = $('a[data-icon="plus"]').first().attr('href');            
        }
        
        $.mobile.changePage('#'+id);       
        
    }
    
});


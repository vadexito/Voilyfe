window.IndexView = Backbone.View.extend({
    
    initialize: function(){
        
        this.initEventsLast();
        this.initGoogleCharts();
        this.initCalendar();
        this.initLastEventsPage();        
        
        mainPage = this;
    },
    
    events : {
        'click a.winner-list-line'  : 'viewListEvents',
        'click a.event-line-link'   : 'showEvent',
        'datebox'                   : 'showEventofDate'
    },
    
    initCalendar: function(){
        if ($('#calendar-widget').length > 0){       
            this.calendar = new CalendarView({el:$('#calendar-widget').parent()});
        } 
    },
    
    initLastEventsPage: function(){
        
        var page = this.openPage({
            id: '',
            content: (new EventListView({model:this.options.lastEventsCollection})).render(),
            template:'first-level'
        },true,true);
        
        $('#last-events-page').html(page.$el.html()).trigger("pagecreate");        
    },
    
    initGoogleCharts: function(){
        
        $('.google-chart').each(function(){
        
            var input = $.parseJSON($(this).attr('data-visual'));
            var self = this;
            function drawVisualization(){
                var data = google.visualization.arrayToDataTable(input.values);

                var options = {
                    'title':input.options.title,
                    'width':input.options.width,
                    'height':input.options.height,
                    'hAxis': {'title': input.options.hAxisTitle},
                    'vAxis': {'title': input.options.vAxisTitle},
                    'legend': input.options.legend
                };
                new google.visualization.ColumnChart(self).draw(data,options);
            }

            google.load('visualization', '1.0', {'callback':drawVisualization,'packages':['corechart']}); 
        });
    },
    
    initEventsLast: function(){
        
        //lastEvents is defined in the html page as a global variable
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
                id      :options.id,
                template:options.template
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
            console.log($('a[data-icon="plus"]').length);
            window.location = $('a[data-icon="plus"]').first().attr('href');            
        }
        
        $.mobile.changePage('#'+id);       
        
    }
    
});


window.GraphView = Backbone.View.extend({
    
    initialize: function(){
        
        this.page = this.$el.parents().filter('div[data-role="page"]').first();
        
        if ($.mobile.activePage.attr('id') == this.page.attr('id')){            
            this.initGoogleChart();        
        }
        
        this.page.on('pageshow',{graph:this},this.updatePage);
    },
    
    events : {
        'click a.winner-list-line'  : 'viewListEvents'
    },
    
    initGoogleChart: function(){
        
        this.drawGoogleChart();
    },
    
    
    updatePage: function(event){
      
      event.data.graph.drawGoogleChart();
    },    
    
    drawGoogleChart: function(){        
        
        var self = this;
        var input = $.parseJSON(this.$el.attr('data-visual'));
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
            new google.visualization.ColumnChart(self.el).draw(data,options);
        }

        google.load('visualization', '1.0', {'callback':drawVisualization,'packages':['corechart']});           
        
    },
    
    
    viewListEvents : function(e){
        
        //Create collection for subgroup of events corresponding to tag
        var tagEvents = new Events();
        var tagValue = $(e.currentTarget).find('h3').html();
        
        _.each($.parseJSON($(e.currentTarget).attr('data-events')),function(id){
            tagEvents.add(mainPage.options.lastEventsCollection.get(id));            
        },mainPage);
        
        mainPage.openPage({
            id      : $(e.currentTarget).attr('data-item')+'-'+tagValue.replace(/ /g,'')+"-page",
            title   : tagValue,
            content : (new EventListView({model:tagEvents,active:'graphs'})).render(),
            active  :'graphs',
            template:'second-level'
        });
    },
    
    
    
    
    render: function(){
        
       this.$el.html(this.template(this.model.toJSON()))               
            .find('#' + this.options.active).addClass('ui-btn-active ui-state-persist'); 
       return this.$el;
    }
    
});


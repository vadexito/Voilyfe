window.GraphView = Backbone.View.extend({
    
    initialize: function(){
        
        this.page = this.$el.parents().filter('div[data-role="page"]').first();
        
        if (typeof google != 'undefined'){
            this.initGoogleChart(); 
        }
        
        this.page.on('pageshow',{graph:this},this.updatePage);
    },
    
    events : {
        
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
    
    
    
    
    
    
    
    render: function(){
        
       this.$el.html(this.template(this.model.toJSON()))               
            .find('#' + this.options.active).addClass('ui-btn-active ui-state-persist'); 
       return this.$el;
    }
    
});


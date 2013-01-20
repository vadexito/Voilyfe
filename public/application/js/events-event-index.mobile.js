/*
================================================================================
load google package for graphs / maps
================================================================================
*/

$(function(){
    

/*
================================================================================
SUBPAGE GRAPHS : draw graphs from google chart
================================================================================
*/

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
    
    

    
/*
================================================================================
SUBPAGE GRAPHS : ranking events link and generation of event page (one event)
================================================================================
*/   
    new IndexView({el:$('body')});
	
}); 				
/*
================================================================================
load google package for graphs / maps
================================================================================
*/

$(function(){
    
/*
================================================================================
get category from server
================================================================================
*/
    var categoryId= $('#dataToClientSide').attr('data-categoryId');
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
SUBPAGE CALENDAR : draw calendar
================================================================================
*/    

    if ($('#calendar-widget').length > 0){
        
        function addLinkToCalEvent(){
            $('div.ui-datebox-griddate').filter(function(){
                return !($(this).hasClass('ui-datebox-griddate-disable') ||
                    $(this).hasClass('ui-datebox-griddate-empty'));
                }).each(function(){
                    $(this).html('<a class="event-date ui-link" data-form="ui-body-b" data-theme="c" href="#">' 
                        + $(this).html() + '</a>')
            });
        }
        
        addLinkToCalEvent();
        
        
        
        $('#calendar-widget').bind('datebox', function (e, passed) { 
            
            if ( passed.method === 'set') {
                var theDate = $(this).data('datebox').theDate;
                var month = theDate.getMonth()+1;
                
                window.location = '/events/event/index/containerId/'
                    +categoryId
                    +'/date/'+theDate.getFullYear()+'-'+month+'-'+theDate.getDate()
                
            }

            if ( passed.method === 'postrefresh') {
                addLinkToCalEvent();
            }
        });

        
    }
    
    new IndexView({el:$('body')});
	
}); 				
google.load('visualization', '1.0', {'packages':['corechart']});

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
    var categoryId;
    
    //get category from url for ajax calls if categoryId sent through url
    var pattern1 = /.*container\/(\d+).*/;
    var pattern2 = /.*event\/showall.*/;
    if (window.location.pathname.match(pattern1)){
        categoryId = window.location.pathname.replace(pattern1,"$1");
    } else if (window.location.pathname.match(pattern2)){
        categoryId = 'all';
    } else {
        if ($('#categoryId').length > 0){
            categoryId = $('#categoryId').attr('value');
        }
    }
     
    
    
/*
================================================================================
SUBPAGE GRAPHS : draw graphs from google chart
================================================================================
*/

    if ($('#chart_div').length >0){
        $.get('/events/ajax/widgetchart/containerId/'
                        +categoryId
                        +'/parameter/frequency/format/json',success,'json');
        function success(input){
            
            function drawVisualization() {
                
                var data = google.visualization.arrayToDataTable(input.dataChart.values);
                var options = {
                    'title':input.dataChart.options.title,
                    'width':input.dataChart.options.width,
                    'height':input.dataChart.options.height,
                    'hAxis': {'title': input.dataChart.options.hAxisTitle},
                    'vAxis': {'title': input.dataChart.options.vAxisTitle},
                    'legend': input.dataChart.options.legend
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
                chart.draw(data, options);

            }
            
            google.load('visualization', '1.0', {'callback':drawVisualization,'packages':['corechart']}); 
            
        }
    }
    
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
                var day = theDate.getDate();
                var month = theDate.getMonth()+1;
                var year = theDate.getFullYear();

                window.location = '/events/event/index/containerId/'
                    +categoryId
                    +'/day/'+day
                    +'/month/'+month
                    +'/year/'+year;
            }

            if ( passed.method === 'postrefresh') {
                addLinkToCalEvent()
            }
        });

        
    }
}); 

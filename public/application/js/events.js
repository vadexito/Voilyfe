$(function() {
    
/*
================================================================================
event page : menus
================================================================================
*/ 
    
    //upper menu----------------------------------------------------------------
    $('nav#upper-menu ul.nav > li > a').attr('data-toggle','dropdown');
    $('nav#upper-menu ul.nav > li > ul').addClass('dropdown-menu');
    $('nav#upper-menu ul.nav > li').addClass('dropdown');
    //--------------------------------------------------------------------------

    
     //side menu----------------------------------------------------------------
    $('#side-menu  ul.nav').each(function(){
        $(this).addClass('nav-list bs-docs-sidenav dropdown-submenu');
        var nbLi = $(this).children().length;
        $(this).children().slice(1,nbLi).filter('li').addClass('dropdown-submenu');
        $(this).children().children().filter('a').slice(1,nbLi).prepend('<i class="icon-chevron-right"></i> ');
        $(this).children().children().filter('ul').addClass('dropdown-menu').attr('role','menu');
    });
    $('#side-menu  ul.nav > li > ul > li').attr('tabindex','-1');
    $('#menu-event + ul > li > a').attr('tabindex','-1');
    //--------------------------------------------------------------------------
    
    //form button submit centered : add a div
    $('form#add_event').prepend('<div class="form-excep-submit"></div>');
    $('.form-excep-submit').prepend($('#add_event').children().not('.form-excep-submit').not('input[type="submit"]'));
    //--------------------------------------------------------------------------
    
    //add twitter bootstrap class for multickeckbox in form
    $('input[type="checkbox"]').parent().addClass('checkbox');
    $('form br').remove();
    //--------------------------------------------------------------------------
    
    //add new item_row value button
    $(':hidden[name^="add_"]').each(function(){
        href = $(this).attr('value');
        title = $(this).attr('title');
        $(this).replaceWith('<a title="'+title+'" href="'+href+'" class="add-event-button btn btn-primary"><i class="icon-plus icon-white"></i></a>');
    });
    $('label+select').prev().each(function(){
        $(this).append($(this).nextAll('a').first());
    });
    $('label[class="checkbox"]').prev().not('label[class="checkbox"]').each(function(){
        $(this).append($(this).nextAll('a').first());
    });
    
    //--------------------------------------------------------------------------
    
   
    //get category from url for ajax calls if categoryId sent through url
    var pattern = /.*container\/(\d+).*/;
    if (window.location.pathname.match(pattern)){
        var categoryId = window.location.pathname.replace(pattern,"$1");
    }
    var pattern = /.*event\/showall.*/;
    if (window.location.pathname.match(pattern)){
        var categoryId = 'all';
    }
    //--------------------------------------------------------------------------
    
    //validation of data input form
    var categoryId = $('#categoryId').attr('value');

    function doValidation(id){
        url = '/events/ajax/validateform/containerId/'+categoryId+'/format/json';
        var data = {};
        $('input').each(function(){
            data[$(this).attr('name')] = $(this).val();
        });
        $.post(url,data,function(resp){
            $("#"+id).parent().find('.help-inline').remove();

            if (resp.messages[id]){
                $("#"+id).parent().parent().attr('class','control-group error');
            }else{
                $("#"+id).parent().parent().attr('class','control-group success');
            }
            $("#"+id).parent().append(getValidationHtml(resp.messages[id]));
        },"json");
    }

    function getValidationHtml(formErrors){
        var o='';

        //validated value
        if (!formErrors){
            o += "<span class='help-inline'>perfect !</span>";
        }

        //case of error
        for (errorKey in formErrors){
            o += "<span class='help-inline'>" + formErrors[errorKey] + "</span>";
        }
        o += '</ul>';
        return o;
    }


    //validation form
    $('input,textarea').blur(function(){
        var formElementId = $(this).parent().prevAll().filter('label').attr('for');
        doValidation(formElementId);
    });
    
/*
================================================================================
eventpage : calendar
================================================================================
*/ 
 
    
    //add date picker to event form
    $('.datepickerAddEvent').datepicker({
        dateFormat:'yy-mm-dd',
        onClose: function(dateText,inst){
            var formElementId = $(this).parent().prevAll().filter('label').attr('for');
            doValidation(formElementId);
        }
    });
    //--------------------------------------------------------------------------
    
    /**
     * prepare event calendar
     */
    $.get('/events/ajax/eventcalendar/containerId/'+categoryId+'/format/json',ajaxCalendarOk,'json');
    function ajaxCalendarOk (data){
        $("#calendarBiMonth").datepicker({
            beforeShowDay: prepareCalendar,
            numberOfMonths: [2, 1],
            onSelect: function(dateText, inst) {showEvent(dateText);}
        });
        
        function prepareCalendar(date,eventDates){
            var show = false;
            var cssClass = '';
            var message = '';

            var eventDates = data.eventDates;

            $.each(eventDates,function(index,value){
                var eventDate = new Date(value.date.year,value.date.month-1,value.date.day);

                if (isSameDate(date,eventDate)){
                    show = true;
                    cssClass = 'date-event';
                    message = value.message
                }
            });

            return [show,cssClass,message];
        }
        
    }
    
    function isSameDate(date1,date2){
        return (date1.getDate() === date2.getDate() &&
                date1.getMonth() === date2.getMonth() &&
                date1.getYear() === date2.getYear());
    }     
        
    function showEvent(dateText){
        alert(dateText);
    }
    
    
/*
================================================================================
event page : show graphs
================================================================================
*/ 
 
    
    
    //show graph corresponding to the category if a category is in the url
    
    (function (){
        var pathAjaxBegin = '/events/ajax/widgetchart/containerId/'+categoryId
                +'/parameter/';
        if (categoryId && ($('#chart_div').length >0)){
            $.get(pathAjaxBegin+'frequency/format/json',drawVisualization,'json');
            
            $('.btn_chart').click(function(){
                $.get(pathAjaxBegin+$(this).attr('value')+'/format/json',drawVisualization,'json'); 
            });
            
            function drawVisualization(input) {
                //define data input
                var data = google.visualization.arrayToDataTable(input.dataChart.values);
                var options = input.dataChart.options;

                // Create and draw the visualization.
                new google.visualization.ColumnChart(document.getElementById('chart_div')).
                    draw(data,
                        {title:options.title,
                            width:options.width,
                            height:options.height,
                            hAxis: {title: options.hAxisTitle},
                            vAxis: {title: options.vAxisTitle}
                });
            }
        }
    })();
        
/*
================================================================================
gps functionality
================================================================================
*/ 
    
    
    //location with GPS function
    
    Backbone.emulateHTTP = true;
    Backbone.emulateJSON = true;
    
    var MapModel = Backbone.Model.extend({
        initialize: function(){
            this.on('all',function(e){console.log(this.get('name')+" event:"+e)})
        },
        urlRoot : '/events/rest',
        defaults:{
            name:'hkjhkj'
        },
        url : function(){
            var base = this.urlRoot || (this.collection && this.collection.url) || "/";
            if (this.isNew()) return base;
            return base + "?id=" + encodeURIComponent(this.id);
        }
    });
    
    var mmap = new MapModel({id:1});
    mmap.fetch().success(function(data){
        console.log(data.response.key);
    });
   
    
    var Router = Backbone.Router.extend({
        routes: {
         
            "*action" : "func",
            "foo/:bar" : 'paramtest'
        },
        func: function(action){
            console.log(action);
        },
        paramtest: function(p){
            console.log(p);
        }
    });
    
    new Router();
    Backbone.history.start();
    
    var Map = Backbone.View.extend({
        initialize : function(){
            var map = new google.maps.Map(this.el, {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: 17
            });
        },
        el:$('#map')
    });
    
    //var googlemap = new Map({MapModel:model});
    
    
    (function (){
        if ($('#map').length>0){
            var map = new google.maps.Map(document.getElementById('map'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: 17
            });
            
            $('#location-latitude').hide();
            $('#location-longitude').hide(); 
            $('#map').hide();


            //define map
            

            //define autocomplete google maps element
            var input = document.getElementById('location-address');
            var options = {
                types: ['establishment']
            };

            autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.bindTo('bounds', map);

            //add listener for place changing in the autocomplete
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                if (place){

                    var address='';
                    if (place.address_components) {
                        address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }
                    createMarker(place.geometry.location,place.name +': ' + address);
                    addToMap(place);
                    updateForm(place.geometry.location);
                }
            });


            function initLocation(){
                if (navigator.geolocation){
                    $('#location-address').click(function(){$('#map').show();
                        if ($(this).attr('value')== ''){
                            navigator.geolocation.getCurrentPosition(function(position){
                                var currentPos = new google.maps.LatLng(
                                    position.coords.latitude,
                                    position.coords.longitude
                                );
                                map.setCenter(currentPos);
                                createMarker(currentPos,'currentPosition')
                                autocomplete.bounds = new google.maps.LatLngBounds(currentPos);
                            });
                        }
                    });

                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            function createMarker(location,title){
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                title: title,
                draggable: true
            });

            google.maps.event.addListener(marker,'dragend',function(){
                updateForm(marker.getPosition());
            });
        } 

            function addToMap(place){
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15); 
                }
            }

            function updateForm(location){
                if (location){
                    var longitude = location.lng();
                    var latitude = location.lat();
                    $('#location-latitude').attr('value',latitude);
                    $('#location-longitude').attr('value',longitude);  
                } 
            }

            initLocation();
        }
    })();
    
    var Person = Backbone.Model.extend({
       initialize: function(){
           console.log('hello world');
           this.bind('change:name',function(){
               console.log(this.get('name'));
           });
           this.bind('error',function(model,error){
               console.log(error);
           })
       },
       defaults:{
           name:'bob',
           height:'1,76m'
       },
       validate: function(attributes){
         if (attributes.name ==   'barbara'){
             return 'you are david ?';
         }
       }
    });
    
    var View = Backbone.View.extend({
        initialize:function(){
            console.log(this.options.blankOption);
        }
    });
    
    //var view = new View({el});
    
    
    
    var person = new Person({name:'david',height:'1,76 m'});
    person.set("name",'barbara');
    
    
    
});  
      
google.load('visualization', '1', {'packages': ['corechart']});
    


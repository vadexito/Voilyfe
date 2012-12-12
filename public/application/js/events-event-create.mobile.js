//google.load('visualization', '1.0', {'packages':['corechart']});


$(function(){
    
/*
================================================================================
define tags input (with and without subform)
================================================================================
*/ 
    $('input.form-element-tags').each(function(){
        
        new InputView({
            el: this,
            collection: new Tags,
            model:new Input({
                'tagsContainer': $('#add_event'),
                'propertyName':$(this).attr('data-property-name'),
                'multitag':$(this).attr('data-multitag'),
                'itemName':$(this).attr('data-item-name'),
                'itemId':$(this).attr('data-containerId'),
                'itemGroupForm': $('#'+$(this).attr('data-property-name')+'_itemGroup_form_page'),
                'formElementName': $(this).attr('id'),
                'autocomplete':$.parseJSON($(this).attr('data-autocomplete')),
                'populate':$.parseJSON($(this).attr('data-populate'))
            })
        });
    });
    
/*
================================================================================
add a plus button for an input field
================================================================================
*/ 
    
    
    $('#form_element_item_location').find('div.ui-block-b').append(
        $('<img class="button-option-plus" src="/images/icons/other/icon-plus.png" alt="plus" class="ui-li-icon">')
    );
        
        
    $('.button-option-plus').click(function(){
        console.log('ok');
    });
/*
================================================================================
event add : give focus to element on changepage
================================================================================
*/ 
   
    $('div[data-role=page]').first().find('input').not('input[type=hidden]').first().focus();
   //for the arrival on the page
    $( document ).on('pagechange',function(e,data){
        data.toPage.find('input').not('input[type=hidden]').first().focus();
    }); 
    
    
/*
================================================================================
date element
================================================================================
*/
    $('.button_date').each(function(){
        
        $(this).on('click',function(e){
            var $popup = $($(e.currentTarget).attr('href'));
            $popup.find('input').focus();
        });
        
    });
    
    $('.popupDate').on('change',function(e){
        
            var $input = $(this).find('input');
            var date = $input.attr('value');
            $('#date').attr('value',date);
            
            $.ajax({
                url: '/events/ajax/datelocale/dateW3C/'+date+'/format/json',
                success: function(dateString){
                    $('.button_date').text(dateString.date);
                },
                dataType: 'json'
            });
        });
});



$(document).bind('pageinit',function(e){
    
    
   
/*
================================================================================
header button for saving new event and input hidden to submit post
================================================================================
*/
    
    //input for submit post
    if ($('input[type=submit]').length > 0){
        $('#add_event').append('<input type="hidden" value="'
            + $('input[type=submit]').attr('value')
            +'" name="' + $('input[type=submit]').attr('name')
            + '"/>');
        $('input[type=submit]').remove();
    }
        
    
    
    //button save implying submit
    $('.menu_save').on('click',function(e){
        
        $('#add_event').trigger('submit');
    });
    
    //remove input element where tags are entered (but not stored) and 
    // button date
    $('#add_event').on('submit',function(){
        
        $('input[type=date].popupinput').remove();
        $('input.form-element-tags').remove();
        $('input.form-template').remove();
        
    });
    

/*
================================================================================
geolocation
================================================================================
*/  
    
    //no finding action if no geolocation
//    if ($('#btn_find_current_event').length > 0 && $('#map').length > 0){
//        
//        if (!navigator.geolocation){
//            $('#btn_find_current_event').hide();
//        } else {
//
//
//            // from the beginning locate possible event location
//            navigator.geolocation.getCurrentPosition(
//                showResultsFromResearch,
//                errorlocalization,
//                {maximumAge:5000, timeout:2000}
//            );
//
//            var map;
//
//            function showResultsFromResearch(position){
//                var latitude = position.coords.latitude;
//                var longitude = position.coords.longitude;
//                var currentPos = new google.maps.LatLng(latitude,longitude);
//
//                var request = {
//                    location: currentPos,
//                    radius: '200',
//                    types: ['restaurant']
//                };
//
//                map = new google.maps.Map(document.getElementById('map'), {
//                    mapTypeId: google.maps.MapTypeId.ROADMAP,
//                    center: currentPos,
//                    zoom: 15
//                    });
//
//                service = new google.maps.places.PlacesService(map);
//                service.search(request, findLocationRequest);
//
//                function findLocationRequest(results,status){
//
//                    //show on the map
//                    if (status == google.maps.places.PlacesServiceStatus.OK) {
//                        for (var i = 0; i < results.length; i++) {
//                        createMarker(results[i]);
//                        }
//                    }
//
//                    results.sort(sortResults);
//                    //show in a list
//                    var nb=0;
//                    var MaxResult = 10;
//
//                    $('#find_current_event ul').children().last().hide();
//                    var $list = $('#find_current_event ul');
//
//                    function renderNewLine(placeResult){
//                        var $lastItem = $list.find('a').last();
//                        var type = placeResult.types[0];
//
//                        $lastItem.find('h3').text(placeResult.name);
//                        $lastItem.find('span').text(Math.round(distanceToCurrentPos(placeResult))+' m');
//
//                        $.get('/events/ajax/googlelocations/type/'+type+'/format/json',ajaxGoogleLink,'json');
//
//                        function ajaxGoogleLink(data){
//                            $lastItem.attr('href',data.locationData.url_new_event);
//                            $lastItem.find('img').attr('src',data.locationData.iconPath);
//                            $lastItem.find('p').text(data.locationData.category);
//                        }
//
//                    }
//
//                    for (var i in results){
//                        if (nb < MaxResult){
//                            var $newLine = $('#find_current_event ul').children().last().clone().show();
//                            $list.append($newLine);
//                            renderNewLine(results[i]);
//                            nb++;
//                        }
//                    }
//                    $('#find_current_event ul li').first().append(': '+nb);
//                }
//
//                function sortResults(placeResultA,placeResultB){
//                    return (distanceToCurrentPos(placeResultA) - distanceToCurrentPos(placeResultB));
//                }
//
//                function createMarker(placeResult){
//                    new google.maps.Marker({
//                        position: placeResult.geometry.location,
//                        map: map,
//                        title: placeResult.types[0] +':'+placeResult.name
//                    });
//                }
//
//                function distanceToCurrentPos(placeResult){
//                    return google.maps.geometry.spherical.computeDistanceBetween(
//                        currentPos,
//                        placeResult.geometry.location)
//                    ;
//                }
//            }  
//
//            function errorlocalization(error){
//                switch(error.code) {
//                    case error.TIMEOUT:
//                        $('#find_current_event').text('Timeout - GPS unable to localize');
//                    break;
//                    case error.PERMISSION_DENIED:
//                        $('#find_current_event').text('You must authorize geololization to use this function');
//                    break;
//                    default:
//                        $('#find_current_event').text('GPS function must be activated');
//                }
//            }
//        }
//    }
//    
//    
//    
    
}); 

//google.load('visualization', '1.0', {'packages':['corechart']});


$(function(){
    
    new SaveEventPageBodyView({
        
        el:$('body')  
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

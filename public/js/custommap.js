jQuery(document).ready(function(){
    jQuery("#location_search_button").click(function(event){
        event.preventDefault();
      // ga('send', 'pageview', '/contractors/distributorlocatorresults');
        // ga('send', 'event', 'Distributor', 'Search', 'Found');
          
        jQuery("#dajax_loader").show();

        if(jQuery("#zipcode") || jQuery("#city") && jQuery("#state"))
        {
            codeAddress();
        } else {
            locateUser();
        }
    });
});

var mapDiv;
function locateUser(){
    jQuery('html, body').animate({
        scrollTop: jQuery("#map-canvas").offset().top
    }, 2000);
    jQuery("#locations-table tbody").empty();
    mapDiv = document.getElementById('map-canvas');
    mapDiv.innerHTML = 'Trying to find your location...';
    if(navigator.geolocation)
        navigator.geolocation.getCurrentPosition(handleGetCurrentPosition, handleGetCurrentPositionError);
}

function codeAddress() {
    var address = "";
    if(jQuery("#zipcode").val() !== "")
    {
        address = jQuery("#zipcode").val();
    }
    if(jQuery("#city").val() !== "" && jQuery("#state").val() !== "")
    {
        address = jQuery("#city").val() + ", " + jQuery("#state").val();
    }
    if(address.length === 0)
    {
        locateUser();
    } else {
        var retailSearch = "zipcode=" + jQuery("#zipcode").val() + "&city=" + jQuery("#city").val() + "&state=" + jQuery("#state").val();
        jQuery.ajax({
                    url: ajax_params.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: {
                       action: "distributor_get_latlng",
                        data: retailSearch
                    },
                    success:function(data) {
                     if (data.Result === "Success")
                      {
                        
                        jQuery('html, body').animate({
                            scrollTop: jQuery("#map-canvas").offset().top
                        }, 2000);
                        jQuery("#locations-table tbody").empty();
                        mapDiv = document.getElementById('map-canvas');
                        var location = {coords: {latitude: data.lat, longitude: data.lng}};
                        //var location = {coords: {latitude: 41.919807, longitude: -88.304977}};
                        handleGetCurrentPosition(location);
                     }
                  }
              });
//        jQuery.post( "/scripts/ajax.php?action=loadGeoPosition", retailSearch, function( data )
//        {
//            if(data.Result === "Success")
//            {
//                jQuery('html, body').animate({
//                    scrollTop: jQuery("#map-canvas").offset().top
//                }, 2000);
//                jQuery("#locations-table tbody").empty();
//                mapDiv = document.getElementById('map-canvas');
//                var location = {coords: {latitude: data.lat, longitude: data.lng}};
//                handleGetCurrentPosition(location);
//            }
//        }, "json");
    }
  }

function handleGetCurrentPosition(location){
    
    var position = new google.maps.LatLng(location.coords.latitude, location.coords.longitude);
    
    var locationSearch = "radius=" + jQuery("#radius").val() + "&lat=" + location.coords.latitude + "&lng=" + location.coords.longitude + "&search_type=" + jQuery("#search_type").val();
   //alert(locationSearch);
   //return false;
    // Get the search result locations
   // jQuery.post( "/scripts/ajax.php?action=loadLocationsMap", locationSearch, function( data ) {
            // create a map object
     jQuery.ajax({
              url: ajax_params.ajax_url,
              type: 'post',
              dataType: 'json',
              data: {
                     action: "distributor_result_data",
                     data: locationSearch
                    },
                    success:function(data) { 
                       //console.log(data);
                      // return false;
                    //}   
               //  alert(data);
                // return false;
             jQuery("#dajax_loader").hide();
            var map = new google.maps.Map(mapDiv, {
                zoom: 9,
                center: position,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var bounds = new google.maps.LatLngBounds();
            
            // Add user location marker
            var latlng = new google.maps.LatLng(location.coords.latitude,location.coords.longitude);
            var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: 'Your Location'
                });
            bounds.extend(latlng);
           
            // for each of the results create a new marker
            jQuery.each(data, function(index, element) {
                var infowindow = new google.maps.InfoWindow({content: "<strong>" + element.pin.name + "<br />" + element.pin.address + "<br />" + element.pin.city + ", " + element.pin.state + " " + element.pin.zip + "</strong>"});
                var latlng = new google.maps.LatLng(element.pin.lat,element.pin.lng);
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: element.pin.name,
                    icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter_withshadow&chld=' + element.pin.mapLabel + '|78BC31|002066',
                    animation: google.maps.Animation.DROP
                });
                bounds.extend(latlng);
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });
                var locationtablestring = "<tr><td>"+element.pin.mapLabel+"</td><td>";
                if(element.pin.website.length > 0)
                {
                    locationtablestring += "<a href='"+ element.pin.website +"' target='_blank'>" + element.pin.name + " <span class='glyphicon glyphicon-share'></span></a>";
                } else {
                    locationtablestring += element.pin.name;
                }
                locationtablestring += "</td><td>" + element.pin.address + "<br />" + element.pin.city + ", " + element.pin.state + " " + element.pin.zip + "</td><td>" + element.pin.phone + "</td><td>" + element.pin.distance +"</td></tr>";
                jQuery("#locations-table tbody").append(locationtablestring);
            });
            map.fitBounds(bounds);
  //    }, "json");
      },
         error: function(errorThrown){
                      //  console.log(errorThrown);
                        alert("error");
                    }
        });   
    jQuery("#locations-table").css('visibility','visible').hide().fadeIn('fast');
    
    new google.maps.Geocoder().geocode({location: position}, handleGeocoderGetLocations);
}


function handleGeocoderGetLocations( addresses, status ){
        if (status != google.maps.GeocoderStatus.OK)
            return maybe_log( 'Response from Google Failed' );
            
        var city = extractNameFromGoogleGeocoderResults('locality', addresses);
        var country = extractNameFromGoogleGeocoderResults('country', addresses);
        
        var mapOverlay = document.getElementById('user-location');
        mapOverlay.innerHTML = 'Distributors near <strong>' + addresses[0].formatted_address + '</strong>';
        mapOverlay.style.visibility = 'visible';
    }

function extractNameFromGoogleGeocoderResults(type, results){
    for( var i = 0, l = results.length; i < l; i ++)
        for(var j = 0, l2 = results[i].types.length; j < l2; j++ )
            if( results[i].types[j] == type )
                 return results[i].address_components[0].long_name;
    return ''
}
    
function handleGetCurrentPositionError(){
    mapDiv.innerHTML = 'Sorry, but we could not find your current location. Please search using your zipcode or city/state';
}
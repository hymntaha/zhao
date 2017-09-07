
function initialize() {

  var scrollwheelEnabled = false;

  var mapOptions = {
    center: new google.maps.LatLng(38.883333, -87.016667),
    zoom: 1,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    scrollwheel: scrollwheelEnabled
  };

  var geocoder = new google.maps.Geocoder();    

  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
  var input = document.getElementById('location');

  var autocomplete = new google.maps.places.Autocomplete(input);

  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();
  var marker = new google.maps.Marker({
    map: map,
    draggable: true
  });

  google.maps.event.addListener(marker, "drag", function(event) {
    geocoder.geocode({ 'latLng': marker.getPosition() }, function(places, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        update(infowindow, places[0].name, places[0].formatted_address,
          places[0].geometry.location.lat(), places[0].geometry.location.lng());
      }
    });
  });


  google.maps.event.addListener(marker, "dragend", function(event) {
    var point = marker.getPosition();
    map.panTo(point);
  });

  google.maps.event.addListener(autocomplete, 'place_changed', function() {

    infowindow.close();

    var place = autocomplete.getPlace();
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);
    }

    var image = new google.maps.MarkerImage(
      place.icon,
      new google.maps.Size(71, 71),
      new google.maps.Point(0, 0),
      new google.maps.Point(17, 34),
      new google.maps.Size(35, 35)
    );

    marker.setIcon(image);
    marker.setPosition(place.geometry.location);

    update(infowindow, place.name, place.formatted_address, 
      place.geometry.location.lat(), place.geometry.location.lng());

    infowindow.open(map, marker);

  });

  google.maps.event.addListener(map, 'click', function(event) {
    scrollwheelEnabled = (scrollwheelEnabled ? false : true);
    map.setOptions({scrollwheel: scrollwheelEnabled});
  });


  if ($('#location_latitude').val() != '') {

    var name = $('#location_name').val();
    var formatted = $('#location_formatted').val();
    var latitude = $('#location_latitude').val();
    var longitude = $('#location_longitude').val();

    var place = new google.maps.LatLng(latitude, longitude);

    map.panTo(place);
    map.setZoom(17);
    marker.setPosition(place);
    
    infowindow.open(map, marker);

    if (name == '') {
      infowindow.setContent(formatted);
    } else {
      infowindow.setContent('<div><strong>' + name + '</strong></div>' + formatted);
    }

  }


}

function update(infowindow, name, formatted_address, latitude, longitude) {

  $('#location_name').val(name);
  $('#location_formatted').val(formatted_address);
  $('#location_latitude').val(latitude);
  $('#location_longitude').val(longitude);
  if (name == undefined) {
    infowindow.setContent(formatted_address);
  } else {
    infowindow.setContent('<div><strong>' + name + '</strong></div>' + formatted_address);
  }

}

google.maps.event.addDomListener(window, 'load', initialize);


<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 11,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  
    var auto = true;
    if(auto) {  
      if(navigator.geolocation) {
        browserSupportFlag = true;
        navigator.geolocation.getCurrentPosition(function(position) {
          initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
          contentString = "Location found using W3C standard";
          map.setCenter(initialLocation);
          map.setZoom(16);
          //infowindow.setContent(contentString);
          //infowindow.setPosition(initialLocation);
          //infowindow.open(map);
        }, function() {
          handleNoGeolocation(browserSupportFlag);
        });
      } else if (google.gears) {
        // Try Google Gears Geolocation
        browserSupportFlag = true;
        var geo = google.gears.factory.create('beta.geolocation');
        geo.getCurrentPosition(function(position) {
          initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
          contentString = "Location found using Google Gears";
          map.setCenter(initialLocation);
          map.setZoom(16);
          infowindow.setContent(contentString);
          infowindow.setPosition(initialLocation);
          infowindow.open(map);
        }, function() {
          handleNoGeolocation(browserSupportFlag);
        });
      } else {
        // Browser doesn't support Geolocation
        browserSupportFlag = false;
        handleNoGeolocation(browserSupportFlag);
      }
    } else {
      geocoder.geocode({'address': "Milano"}, function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
        } else {
          //map.setCenter(new google.maps.LatLng(47, 6));
        }
      });
    }
  }

  function getLatLng(results, status, latlng) {
      if (status == google.maps.GeocoderStatus.OK)
        latlng = results[0].geometry.location;
      else
        latlng = new google.maps.LatLng(-34.397, 150.644);
      return latlng;
  }
  function codeAddress() {
    var address = document.getElementById("address").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
  function centerMap() {
    map.setCenter(codeAddress());
  }

</script>
<body onload="initialize()">
 <div id="map_canvas" style="width: 100%; height: 480px;"></div>
  <div>
    <input id="address" type="textbox" value="Milano">
    <input type="button" value="Encode" onclick="codeAddress()">
  </div>
</body>
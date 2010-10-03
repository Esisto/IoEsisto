<?php

class MapManager {
	static function showSimpleMap($place, $width = 200, $height = 200) {
		$latlng = explode(", ", $place);
		?>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript">
		  var geocoder;
		  var map;
		  function initialize() {
			geocoder = new google.maps.Geocoder();
			var myOptions = {
			  zoom: 11,
			  latlng: new google.maps.LatLng(<?php echo $latlng[0]; ?>, <?php echo $latlng[1]; ?>);
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			
		
		  }
		</script>
		
		<div id="map_canvas" style="width: <?php echo $width; ?>px; height: <?php echo $width; ?>px;"></div>
		<div>
			<input id="address" type="text" value="Milano" />
		</div>
		<?php
	}
	
	static function printInfoInElement($place, $elementid, $types = null) {
		$latlng = explode(", ", $place);
		?>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript">
			var geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(<?php echo $latlng[0]; ?>, <?php echo $latlng[1]; ?>);
			geocoder.geocode({'latLng': latlng}, function(results, status) {
				if(status == google.maps.GeocoderStatus.OK) {
					var i = 0;
					while(results[i]) {
						//document.getElementById("<?php echo $elementid; ?>").innerHTML = results[2].types;
						if(results[i].types == "locality,political") {
							document.getElementById("<?php echo $elementid; ?>").innerHTML = results[i].formatted_address + " - ";
							break;
						}
						i++;
					}
				} else {
					alert("Geocoder failed due to: " + status);
				}
			});
		</script>
		<?php
	}
}
?>

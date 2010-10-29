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
	
	static function showPostMap($place, $input_id, $class) {
		if($place != "") {
			$latlng = explode(", ", $place);
		} else {
			require_once 'settings.php';
			$latlng = array(DEF_LAT, DEF_LNG);
		} 
		?>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<div id="map_canvas" class="<?php echo $class; ?>"></div>
		<script type="text/javascript">
			var geocoder;
			var map;
			//function initialize() {
				geocoder = new google.maps.Geocoder();
				var myOptions = {
					zoom: 11,
					latlng: new google.maps.LatLng(<?php echo $latlng[0]; ?>, <?php echo $latlng[1]; ?>),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				map.setCenter(myOptions.latlng);
				var marker = new google.maps.Marker({
		            map: map, 
		            position: myOptions.latlng
		        });
			//}

			function savePosition(input_element, label_element) {
				ll = map.getCenter();

				input_element.value = ll.lat() + ", " + ll.lng();
				input_element.type = "text";
				label_element.innerHTML = "Posizione salvata:";
				label_element.setAttribute("class", "");
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
		</script>
		<div>
			<input id="address" type="text" value="" />
			<input type="button" value="Cerca" onclick="codeAddress()" />
		</div>
		<?php
	}
	
	static function setCenterToMap($center, $map_id) {
		if(trim($center) == "") return;
		$latlng = explode(", ", $center);
		?>
		<script type="text/javascript">
			var geocoder;
			var map;
			geocoder = new google.maps.Geocoder();
			var myOptions = {
				zoom: 13,
				latlng: new google.maps.LatLng(<?php echo $latlng[0]; ?>, <?php echo $latlng[1]; ?>),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			map = new google.maps.Map(document.getElementById("<?php echo $map_id; ?>"), myOptions);
			map.setCenter(myOptions.latlng);
	        var marker = new google.maps.Marker({
	            map: map, 
	            position: myOptions.latlng
	        });
		</script>
		<?php
	}
}
?>

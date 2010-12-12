function printNameOfPlace(lat, lng, element) {
	var geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(lat, lng);
	geocoder.geocode({'latLng': latlng}, function(results, status) {
		if(status == google.maps.GeocoderStatus.OK) {
			firstParagraph = element.nextElementSibling;
			firstParagraph.insertBefore(element, firstParagraph.firstChild);
			var i = 0;
			while(results[i]) {
				//element.innerHTML = element.innerHTML + results[i].types;
				if(results[i].types == "locality,political") {
					element.innerHTML = /*element.innerHTML + */results[i].formatted_address + " - ";
					break;
				}
				i++;
			}
			if(element.innerHTML == "")
				element.innerHTML = /*element.innerHTML + */results[i-1].formatted_address + " - ";
		}
	});

}
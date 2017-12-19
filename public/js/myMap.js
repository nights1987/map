var map;
var infowindow;
var placesService;
var placeId;

function initMap()
{
	let defaultLatLng = { lat: 13.804670, lng: 100.537090 };

	map = new google.maps.Map(document.getElementById('map'),
	{
		center: defaultLatLng,
		zoom: 17
	});

	infowindow = new google.maps.InfoWindow();
	placesService = new google.maps.places.PlacesService(map);

	// This event listener calls addMarker() when the map is clicked.
	map.addListener('click', function(event)
	{
		placeId = event.placeId;
		addMarker(event.latLng);
	});

	// Add a marker at the center of the map.
	addMarker(defaultLatLng);
}

// Adds a marker to the map.
function addMarker(location)
{
	let marker = new google.maps.Marker(
	{
		position: location,
		map: map,
		draggable: false,
		animation: google.maps.Animation.DROP,
		title: 'Click for more detail'
	});

	// Close previouse infowindow.
	infowindow.close();

	// This event listener calls showInfo() when the marker is clicked.
	marker.addListener('click', function(event)
	{
		showInfo(event.latLng, marker);
	});
}

function showInfo(location, marker)
{
	if (placeId != null)
	{
		this.placesService.getDetails({placeId: placeId}, function(place, status)
		{
			infowindow.setContent(createInfoWindowContent(location, map.getZoom(), place));
			infowindow.open(map, marker);
		});
	}
	else
	{
		infowindow.setContent(createInfoWindowContent(location, map.getZoom()));
		infowindow.open(map, marker);
	}
}

function createInfoWindowContent(location, zoom, place = null)
{
	let id = "<strong>Place Id:</strong> ";
	let placeName = "<strong>Place Name:</strong> ";
	let placeAddress = "<strong>Address:</strong> ";

	if (place != null)
	{
		id += place.place_id;
		placeName += place.name;
		placeAddress += place.formatted_address;
	}
	else
	{
		id += "-";
		placeName += "-";
		placeAddress += "-";
	}

	let placeDetail = [
		id,
		placeName,
		placeAddress,
		"<strong>Latitude and Longitude</strong> " + location,
		"<strong>Zoom Level:</strong> " + zoom
		// ,"",
		// "<a href='mailto:ksrimulchai@gmail.com' target='_top'>Send Email</a>"
	]
	.join("<br/>");

	let to = "Tonywilk@scg.com";
	let cc = "ksrimulchai@gmail.com";
	let subject = escape("GIT URL of Code");
	let body = escape("GIT URL of Code: https://github.com/nights1987/map.git");

	placeDetail += "<br/><br/><a href='mailto:"+to
																		+ "&cc=" + cc
																		+ "&subject=" + subject
																		+ "&body=" + body
																		+ "' target='_top'>Send Email</a>";
	
	return placeDetail;
}

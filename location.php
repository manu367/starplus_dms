<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Location</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBEbWSe_aKRgsnbFS24G_KtlN_XZ0luwLQ&sensor=false&libraries=places">
</script>
</head>

<body>
<input type="text" id="location" style="width:400px"/>
<input type="text" placeholder="Latitude" id="lat"/>
<input type="text" placeholder="Longitude" id="long"/>
<script type="text/javascript">
$(document).ready(function(){
	var autocomplete;
	var id = 'location';
	autocomplete = new google.maps.places.Autocomplete((document.getElementById(id)),{
		types:['geocode'],
	})
	google.maps.event.addListener(autocomplete,'place_changed',function(){
		var place = autocomplete.getPlace();
		jQuery("#lat").val(place.geometry.location.lat());
		jQuery("#long").val(place.geometry.location.lng());
	})
});
</script>
</body>
</html>

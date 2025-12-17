<?php
require_once("../config/config.php");
$engid = base64_decode($_REQUEST['id']);
$traveldate = base64_decode($_REQUEST['travel_date']);
$totdist = base64_decode($_REQUEST['total_distance']);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
	<style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #map {
	height: 100%;
  }
  /* Optional: Makes the sample page fill the window. */
  /*html, body {
	height: 100%;
	margin: 0;
	padding: 0;
  }*/
</style>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-child"></i> Distance Covered</h2>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">User Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">User Id</label></td>
                <td width="30%"><?php echo str_replace("~",",",getAdminDetails($engid,"name,username",$link1));?></td>
                <td width="20%"><label class="control-label">Travel Date</label></td>
                <td width="30%"><?php echo $traveldate;?></td>
              </tr>
              <tr>
                <td><label class="control-label">Total Air Distance</label></td>
                <td><?=$totdist?> KM</td>
                <td colspan="2">&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading">Location On Map</div>
      	<div class="panel-body" id="map" style="height:300px;"></div>
      </div>
    </div>   
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Distance Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="15%">From Location</th>
                <th style="text-align:center" width="10%">From Time</th>
                <th style="text-align:center" width="10%">To Location</th>
                <th style="text-align:center" width="10%">To Time</th>
               <!-- <th style="text-align:center" width="10%">Air Distance Covered</th>-->
                </tr>
            </thead>
            <tbody>
            <?php
			$arr_from = array();
			$arr_from_time = array();
			$arr_to = array();
			$arr_to_time = array();
			$arr_distance = array();
			$center_loc = "";
			$muli_loc = "";
			$draw_loc = "";
			$i = 1;
			$sql = "SELECT latitude,longitude,entry_date,update_date, address,travel_km FROM user_track where userid='".$engid."' and entry_date='".$traveldate."' AND latitude!='' order by id";
			$res = mysqli_query($link1,$sql);
			while($row = mysqli_fetch_assoc($res)){
				$expl = explode(" ",$row["update_date"]);
				////// get first location of the day
				if($center_loc==""){
					$center_loc = $row["latitude"].", ".$row["longitude"];
				}
				////// get all location , user is walking
				if($muli_loc==""){
					$cordinate .="'".$row["latitude"].", ".$row["longitude"]."'";
					$muli_loc .= "{".
								    "position: new google.maps.LatLng(".$row["latitude"].", ".$row["longitude"]."),".
									"type: 'library',".
									"usercode: '".$row["address"]." at ".$expl[0]." ".$expl[1]."'".
	  							  "}";
				}else{
					$cordinate .="/'".$row["latitude"].", ".$row["longitude"]."'";
					$muli_loc .= ",{".
								    "position: new google.maps.LatLng(".$row["latitude"].", ".$row["longitude"]."),".
									"type: 'library',".
									"usercode: '".$row["address"]." at ".$expl[0]." ".$expl[1]."'".
	  							  "}";
				}
				///// draw all location
				if($draw_loc==""){
					$draw_loc .= "{lat: ".$row["latitude"].", lng: ".$row["longitude"]."}";
				}else{
					$draw_loc .= ",{lat: ".$row["latitude"].", lng: ".$row["longitude"]."}";
				}
				if($i%2 == 1){
					$arr_from[] = "latitude: ".$row["latitude"].", longitude: ".$row["longitude"]."<br/>".$row["address"];
					$arr_from_time[] = $expl[0]." ".$expl[1];
					$arr_distance[] = $row["travel_km"];
				}
				if($i%2 == 0){
					$arr_to[] = "latitude: ".$row["latitude"].", longitude: ".$row["longitude"]."<br/>".$row["address"];
					$arr_to_time[] = $expl[0]." ".$expl[1];
					$arr_distance[] = $row["travel_km"];
				}
				$i++;
			}
			for($j=0; $j<count($arr_from); $j++){
			?>
              <tr>
                <td><?=$arr_from[$j]?></td>
                <td align="center"><?=$arr_from_time[$j]?></td>
                <td><?=$arr_to[$j]?></td>
                <td align="center"><?=$arr_to_time[$j]?></td>
               <?php /*?> <td align="right"><?=$arr_distance[$j]?></td><?php */?>
                </tr>
            <?php
			}
			?>
            <tr>
                 <td colspan="5" align="center">
                 <a href="https://www.google.com/maps/dir/<?=$cordinate?>/@<?=$center_loc?>,13z" target="_blank" class="btn <?=$btncolor?>">Live Google Map</a>
                 <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='track_total_distance.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
<script>
  var map;
  function initMap() {
	map = new google.maps.Map(
		document.getElementById('map'),
		{center: new google.maps.LatLng(<?=$center_loc?>), zoom: 10});

	var iconBase =
		'https://developers.google.com/maps/documentation/javascript/examples/full/images/';

	var icons = {
	  parking: {
		icon: iconBase + 'parking_lot_maps.png'
	  },
	  library: {
		icon: iconBase + 'library_maps.png'
	  },
	  info: {
		icon: iconBase + 'info-i_maps.png'
	  }
	};

	var features = [
          <?=$muli_loc?>
        ];
	var infoWindow = new google.maps.InfoWindow();
	// Create markers.
	for (var i = 0; i < features.length; i++) {
				var contentString = features[i];
				
		var marker = new google.maps.Marker({
			position: features[i].position,
			icon: icons[features[i].type].icon,
			map: map
		});
		//////
		(function (marker, contentString) {
			google.maps.event.addListener(marker, "click", function (e) {
				//Wrap the content inside an HTML DIV in order to set height and width of InfoWindow.
				infoWindow.setContent("<div style = 'width:200px;min-height:40px'>" + contentString.usercode + "</div>");
				infoWindow.open(map, marker);
			});
		})(marker, contentString);
	};
	// Define a symbol using a predefined path (an arrow)
        // supplied by the Google Maps JavaScript API.
        var lineSymbol = {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };
	// Create the polyline and add the symbol via the 'icons' property.
        var line = new google.maps.Polyline({
          path: [<?=$draw_loc?>],
          icons: [{
            icon: lineSymbol,
            offset: '100%'
          }],
          map: map
        });
  }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC63n5t34jDq2mSufoBt_tmliq6mMJkCy4&callback=initMap">
</script>
</html>
<?php
require_once("../config/config.php");
$engid = base64_decode($_REQUEST['id']);
$traveldate = base64_decode($_REQUEST['travel_date']);
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
      <h2 align="center"><i class="fa fa-child"></i> Distance Covered (Using Google API)</h2>
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
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Distance Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="25%">From Location</th>
                <th style="text-align:center" width="25%">To Location</th>
                <th style="text-align:center" width="10%">Distance</th>
                <th style="text-align:center" width="10%">Distance<br/>(in meter)</th>
                <th style="text-align:center" width="15%">From Latitude/Longitude</th>
                <th style="text-align:center" width="15%">To Latitude/Longitude</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i = 1;
			$center_loc = "";
			$cordinate = "";
			$total_distance = 0;
			$sql = "SELECT * FROM google_api_response WHERE userid='".$engid."' AND entry_date='".$traveldate."' ORDER BY id";
			$res = mysqli_query($link1,$sql);
			while($row = mysqli_fetch_assoc($res)){
				////// get first location of the day
				if($center_loc==""){
					$center_loc = $row["latitude"].", ".$row["longitude"];
				}
				////// get all location , user is walking
				if($cordinate==""){
					$cordinate .="'".$row["latitude"].", ".$row["longitude"]."'";
				}else{
					$cordinate .="/'".$row["latitude"].", ".$row["longitude"]."'";
				}
			?>
              <tr>
                <td><?=$row['respdata1']?></td>
                <td><?=$row['respdata2']?></td>
                <td align="center"><?=$row['respdata3']?></td>
                <td align="right"><?=$row['distance']?></td>
                <td><?=$row['latitude']." , ".$row['longitude']?></td>
                <td><?=$row['latitude2']." , ".$row['longitude2']?></td>
              </tr>
              <?php 
			  	$total_distance += $row['distance'];
			  	$i++;
			}
			?>
            <tr>
                <td colspan="3" align="right"><strong>Total Distance</strong></td>
                <td align="right"><?=$total_distance?> meters</td>
                <td colspan="2" align="right">&nbsp;</td>
                </tr>
            <tr>
                 <td colspan="6" align="center">
                 <a href="https://www.google.com/maps/dir/<?=$cordinate?>/@<?=$center_loc?>,13z" target="_blank" class="btn <?=$btncolor?>">Live Google Map</a>
                 <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='track_total_distance.php?fdate=<?=$traveldate?>&tdate=<?=$traveldate?>&isp_name=<?=$engid?><?=$pagenav?>'"></td>
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
</html>
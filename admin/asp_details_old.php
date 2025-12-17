<?php
require_once("../config/config.php");
///// get access location ///
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$accessState=getAccessState($_SESSION['userid'],$link1);
@extract($_POST);
////// filters value/////
## selected state
## selected state
if($accessLocation!=""){
	$loc_code="and asc_code in  (".$accessLocation.") ";
}else{
	$loc_code="and asc_code in ($accessLocation)";
}
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="state in ($accessState)";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="status='".$locationstatus."'";
}else{
	$loc_status="1";
}
//////End filters value/////
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        <h2 align="center"><i class="fa fa-bank"></i> Locations Master</h2>
	    <?php if($_REQUEST['msg']){?>
	    <br>
        <h4 align="center" style="color:#FF0000">
          <?=$_REQUEST['msg']?>
        </h4>
	    <?php }?>
        <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">State</label>
        	<select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                <option value=''>--Please Select-</option>
                <?php
				$circlequery="select distinct(state) from asc_master where state in ($accessState)  order by state";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
                <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>>
                  <?=ucwords($circlearr['state'])?>
                  </option>
                <?php 
				}
                ?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">City</label>
        	<select  name='locationcity' id="locationcity" class='form-control'  onChange="document.form1.submit();">
                <option value=''>--Please Select-</option>
                <?php
				$model_query="SELECT distinct city FROM asc_master where state='$_REQUEST[locationstate]' order by city";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
                <option value="<?=$br['city']?>"<?php if($_REQUEST['locationcity']==$br['city']){echo 'selected';}?>>
                  <?=$br['city']?>
                  </option>
                <?php
                }
				?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Type</label>
        	<select  name="locationtype" id="locationtype" class='form-control' onChange="document.form1.submit();">
                <option value=''>All</option>
                <?php
				$type_query="SELECT locationname,locationtype FROM location_type where status='A' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
        	<select name="locationstatus" id="locationstatus" class="form-control"  onChange="document.form1.submit();">
                <option value=""<?php if($_REQUEST['locationstatus']==''){ echo "selected";}?>>All</option>
                <option value="active"<?php if($_REQUEST['locationstatus']=='active'){ echo "selected";}?>>Active</option>
                <option value="deactive"<?php if($_REQUEST['locationstatus']=='deactive'){ echo "selected";}?>>Deactive</option>
              </select>
          
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
        	<?php
			//// get excel process id ////
			//$processid=getExlCnclProcessid("Product",$link1);
			////// check this user have right to export the excel report
			//if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
		   ?>
		   <a href="excelexport.php?rname=<?=base64_encode("locationmaster")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_GET['locationstate'])?>&loccity=<?=base64_encode($_GET['locationcity'])?>&loctype=<?=base64_encode($_GET['locationtype'])?>&locstatus=<?=base64_encode($_GET['locationstatus'])?>" title="Export location details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export location details in excel"></i></a>
		   <?php
			//}
			?>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
       		<button title="Add New Location" type="button" class="btn <?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='asp_add.php?op=add<?=$pagenav?>'"><span>Add New Location</span></button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
	    <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
        <table  width="100%" id="myTable" class="table-responsive table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th style="text-align:center;" ><a href="#" name="entity_id" title="asc"></a>#</th>
              <th style="text-align:center;" data-hide="phone" data-class="expand"><a href="#" name="name" title="asc"></a>Location Id</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Location Name</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Location Type</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Phone No.</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th style="text-align:center;" data-hide="phone,tablet">View/Edit</th>
              <th style="text-align:center;" data-hide="phone,tablet">Mapping</th>
              <th style="text-align:center;" data-hide="phone,tablet">History</th>
            </tr>
          </thead>
          <tbody>
            <?php
		    $i=1;
			if($_SESSION['user_level']!=''){
			 $query="Select * from asc_master where $loc_state $loc_code  and $loc_city and $loc_type and $loc_status  and user_level >= $_SESSION[user_level] order by sno";
			}
			else {
			 $query="Select * from asc_master where $loc_state   and $loc_city and $loc_type and $loc_status order by sno";
			}
			$result=mysqli_query($link1,$query) or die(mysqli_error($link1));
			while($arr_result=mysqli_fetch_array($result)){
          ?>
            <tr>
              <td style="text-align:center;" ><?=$i?></td>
              <td><?=$arr_result['uid']?></td>
              <td><?=$arr_result['name']?></td>
              <td><?=$arr_result['state']?></td>
              <td><?=getLocationType($arr_result['id_type'],$link1);?></td>
              <td><?=$arr_result['phone']?></td>
              <td><?=$arr_result['status']?></td>
              <td align="center"><a href='asp_edit.php?op=edit&id=<?php echo base64_encode($arr_result['sno']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              <td align="center"><a href='mappWithLocations.php?id=<?php echo base64_encode($arr_result['sno']);?><?=$pagenav?>'  title='Mapp to parent location'><i class="fa fa-map-signs fa-lg" title="Mapp to parent location"></i></a></td>
              <td align="center"><a href='asp_history.php?asccode=<?php echo base64_encode($arr_result['asc_code']);?><?=$pagenav?>'  title='party history'><i class="fa fa-history fa-lg" title="view history"></i></a></td>
            </tr>
            <?php
		  $i++;
		
			} 
          ?>
          </tbody>
        </table>
	    <!--</div>-->
	    <!--close form group-->
      </form>
    </div>
	<!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
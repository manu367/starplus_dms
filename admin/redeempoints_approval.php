<?php
require_once("../config/config.php");
@extract($_POST);
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['location_code'] !=''){
	$filter_str	.= " and location_code = '".$_REQUEST['location_code']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " and status = '".$_REQUEST['status']."'";
}
//////End filters value/////
$accesslocation = getAccessLocation($_SESSION['userid'],$link1);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery-1.10.1.min.js"></script>
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
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active " id="home">
      <h2 align="center"><i class="fa fa-address-card"></i> Redeem Points Approval</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
        <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">From</label>
             <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label">To</label>
            <div class="col-md-5 input-append date">
                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
        </div><!--close form group-->
        <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Location Name</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select  name='location_code' id="location_code" class='form-control selectpicker' data-live-search="true">
                  <option value=''>All</option>
                    <?php
                    $sql = "SELECT asc_code, name FROM asc_master WHERE asc_code IN (".$accesslocation.") AND status='Active' order by name";
                    $res = mysqli_query($link1,$sql);
                    while($row = mysqli_fetch_array($res)){
                    ?>
                  <option value="<?=$row['asc_code']?>"<?php if($_REQUEST['location_code']==$row['asc_code']){echo 'selected';}?>><?=$row['name']." | ".$row['asc_code']?></option>
                    <?php
                    }
                    ?>
               </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
            <div class="col-md-3">
               <select  name='status' id="status" class='form-control'>
                  <option value=''>All</option>
                  <option value="Pending For Approval"<?php if($_REQUEST['status']=="Pending For Approval"){echo 'selected';}?>>Pending For Approval</option>
                  <option value="Approved"<?php if($_REQUEST['status']=="Approved"){echo 'selected';}?>>Approved</option>
                  <option value="Rejected"<?php if($_REQUEST['status']=="Rejected"){echo 'selected';}?>>Rejected</option>
               </select>
            </div>
            <div class="col-md-2">
                <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
          </div>
        </div><!--close form group-->
      </form>
      <form class="form-horizontal" role="form">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th>S.No</th>
              <th>Location Name</th>
              <th>System Ref. No.</th>
              <th>Redeem Point</th>
              <th>Delivery Address</th>
              <th>Entry Date & Time</th>
              <th>Entry By</th>
              <th>Status</th>
              <th>SO No.</th>
              <th>View</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			
			$sql = mysqli_query($link1,"SELECT * FROM reward_redemption_master WHERE ".$filter_str." AND location_code IN (".$accesslocation.") ORDER BY id DESC");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $userdet = explode("~",getAdminDetails($row['entry_by'],"name,oth_empid",$link1));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $row['location_code'];?></td>
              <td><?php echo $row['system_ref_no'];?></td>
              <td><?php echo $row['total_redeem_reward'];?></td>
              <td><?php echo $row['delivery_address'];?></td>
              <td><?php echo $row['entry_date'];?></td>
              <td><?php echo $userdet[0]." (".$row['entry_by'].")";?></td>
              <td <?php if($row['status']=="Pending"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td><?php echo $row['so_no'];?></td>
              <td align="center"><a href='redeempointsApprovalPage.php?id=<?php echo base64_encode($row['system_ref_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
            </tr>
            <?php }?>
          </tbody>
          </table>
      </div>
      </form>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
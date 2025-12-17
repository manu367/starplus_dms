<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
$target_sql = "SELECT * FROM claim_budget WHERE id = '".$docid."'";
$target_res = mysqli_query($link1,$target_sql)or die("er 1".mysqli_error($link1));
$target_row = mysqli_fetch_assoc($target_res);

@extract($_POST);
if($_POST['submit']=="Update"){	
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	// update status in claim budget master table //
	$sql_master = "UPDATE claim_budget SET status = '".$status."', update_date  = '".$datetime."', update_by  = '".$_SESSION['userid']."', update_ip='".$ip."' WHERE id = '".$docid."' ";
	$res_master =  mysqli_query($link1,$sql_master);
	/// check if query is execute or not//
	if(!$res_master){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	$ref_no = $target_row['claim_typeid']."/".$target_row['party_id']."/".$target_row['budget_year']."/".$docid;
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$ref_no,"CLAIM BUDGET","UPDATE",$ip,$link1,$flag);	
	///// check all query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Claim Budget is successfully updated for ref. id. - ".$ref_no;
		///// move to parent page
		header("location:claim_budget_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. ".$err_msg;
		///// move to parent page
		header("location:claim_budget_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
}
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
 
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-suitcase"></i> View Claim Budget </h2><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Budget Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Party Name</label></td>
                <td width="30%"><?php echo $target_row['party_name']; ?></td>
                <td width="20%"><label class="control-label">Party Id</label></td>
                <td width="30%"><?php echo $target_row['party_id']; ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Claim Type</label></td>
                <td><?php echo $target_row['claim_type']; ?></td>
                <td><label class="control-label">Claim Type Id</label></td>
                <td><?php echo $target_row['claim_typeid']; ?></td>
              </tr>
               <tr>
                <td><label class="control-label">Budget Year</label></td>
                <td><?php echo $target_row['budget_year'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php if($target_row["status"]==1){ $sts="Active";}else if($target_row["status"]==2){ $sts="Deactive";}else{$sts=$target_row["status"];} echo $sts; ?></td>
              </tr>
              <tr class="alert-success">
                <td><label class="control-label">Yearly Budget</label></td>
                <td><?php echo $target_row['budget_yearly'];?></td>
                <td><label class="control-label">Monthly Budget</label></td>
                <td><?php echo $target_row['budget_monthly']; ?></td>
              </tr>
              <tr class="alert-success">
                <td><label class="control-label">Manpower</label></td>
                <td><?php echo $target_row['man_power']; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td><label class="control-label">Create By</label></td>
                <td><?php echo getAdminDetails($target_row['entry_by'],"name",$link1)." | ".$target_row['entry_by'];?></td>
                <td><label class="control-label">Create Date</label></td>
                <td><?php echo $target_row['entry_date']; ?></td>
              </tr>
			  <?php if($target_row['update_by'] != ""){ ?>
			  <tr>
                <td><label class="control-label">Update By</label></td>
                <td><?php echo getAdminDetails($target_row['update_by'],"name",$link1)." | ".$target_row['update_by'];?></td>
                <td><label class="control-label">Update Date</label></td>
                <td><?php echo $target_row['update_date']; ?></td>
              </tr>
			  <?php } ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Action</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
			  	<td width="20%"><label class="control-label">Change Status : </label></td>
                <td width="80%">
					<select class="form-control" name="status" id="status">
						<option value="1" <?php if($target_row['status'] == "1"){ echo "selected"; } ?> >Active</option>
						<option value="2" <?php if($target_row['status'] == "2"){ echo "selected"; } ?> >Deactive</option>
					</select>
				</td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</div>
	<br><br>
	<div class="form-group">
	  <div class="col-md-12" style="text-align:center;" > 
	  	  <button class="btn <?=$btncolor?>" type="submit" name="submit" id="submit" value="Update"> Update </button>  
		  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='claim_budget_list.php?<?=$pagenav?>'">
	  </div>  
	</div>
	<br><br>
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
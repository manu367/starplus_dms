<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$today=date("Y-m-d");
$todayt=date("Ymd");
$datetime=date("Y-m-d H:i:s");
$req_uid = base64_decode($_REQUEST['userid']);
/////////////////
@extract($_POST);
///// extract lat long
$lat = $_REQUEST['latitude'];
$long = $_REQUEST['longitude'];
$trackaddrs = $_REQUEST['trackaddress'];
$trackdistc = $_REQUEST['trackdistance'];
////// we hit save button
if($_POST){
 if ($_POST['upd']=='Save'){
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"SELECT COUNT(id) AS no FROM dealer_target where user_id='".$req_uid."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
	$po_no="DTR/".$target_year.$target_month."/".strtoupper($req_uid)."/".$c_nos; 
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	$empid = getAnyDetails($req_uid,"oth_empid","username","admin_users",$link1);
	///// Insert Master Data
	$query1= "INSERT INTO dealer_target SET target_no ='".$po_no."',party_code='".$party_code."',prod_code='".$psubcat."',target_val='".$target_val."',month='".$target_month."',year='".$target_year."',emp_id='".$empid."',user_id='".$req_uid."',task_name='SO',status='Active',remark='".$remark."',entry_by='".$req_uid."',entry_ip='".$ip."',entry_date='".$datetime."',address='".$trackaddrs."',latitude='".$lat."',longitude='".$long."'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
   /*$result = mysqli_query($link1,"INSERT INTO user_track SET userid='".$req_uid."', task_name='Dealer Target', task_action='Create', ref_no='".$po_no."', latitude='".$lat."', longitude='".$long."', address='".$trackaddrs."',travel_km='".$trackdistc."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
	//// check if query is not executed
	if (!$result) {
		 $flag = false;
		 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
	}*/
	////// insert in activity table////
	$flag=dailyActivity($req_uid,$po_no,"Dealer Target","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Dealer Target is successfully created with ref. no.".$po_no;
		$respheadmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$err_msg;
		$respheadmsg = "Failed";
	} 
    mysqli_close($link1);
	$headerline = "Dealer Target";
	///// move to parent page
    //header("location:purchaseOrderList.php?msg=".$msg."".$pagenav);
	header("Location:processpage.php?respmsg=".base64_encode($msg)."&usercode=".$req_uid."&latitude=".$lat."&longitude=".$long."&taskid=".$_REQUEST['taskid']."&trackaddress=".$_REQUEST['trackaddress']."&respheadmsg=".$respheadmsg."&headerline=".$headerline);
    exit;
 }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<style type="text/css">
-ms-user-select: contain; /*IE,Edge*/
-webkit-user-select: text; /*Webkit*/
-moz-user-select: text; /*Mozilla*/
user-select: all; /*Global, select all with one click*/
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("#frm1").validate({
		submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
				//spinner.show();		  
				form.submit();
			} else {
				return false;
			}
		}
	});
});
$(document).ready(function(){
	$("#party_code").select2({
  		ajax: { 
   			url: "getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "customer",
					userid: '<?=$req_uid?>'
    			};
   			},
   			processResults: function (response) {
     			return {
        			results: response
     			};
   			},
   			cache: true

  		}
	});
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h2 align="center"><i class="fa fa-bullseye"></i> Add Dealer Target </h2>
      <br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Party Name <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select name="party_code" id="party_code" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                 <?php
				if(isset($_POST["party_code"])){
				  $loc_name = explode("~",getAnyDetails($_POST["party_code"],"name, city, state","asc_code","asc_master",$link1));
				  echo '<option value="'.$_POST["party_code"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["party_code"].'</option>';
				}
				?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Year <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select id="target_year" name="target_year" class="form-control required" required>
                    <option value="" <?php if($_REQUEST['emp_year']==""){ echo "selected";}?> > -- Please Select -- </option>
                    <?php
                    $currrent_year=date('Y');
                    $next_year=$currrent_year+1;
                    ?>
                    <option value="<?=$currrent_year?>" <?php if($_REQUEST['emp_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
                    <option value="<?=$next_year?>" <?php if($_REQUEST['emp_year']==$next_year)echo "selected";?>><?=$next_year?></option>
                </select> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Month <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select id="target_month" name="target_month" class="form-control required" required>
                    <option value="" <?php if($_REQUEST['emp_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                    <option value="01" <?php if($_REQUEST['emp_month']=='01')echo "selected";?>>JAN</option>
                    <option value="02" <?php if($_REQUEST['emp_month']=='02')echo "selected";?>>FEB</option>
                    <option value="03" <?php if($_REQUEST['emp_month']=='03')echo "selected";?>>MAR</option>
                    <option value="04" <?php if($_REQUEST['emp_month']=='04')echo "selected";?>>APR</option>
                    <option value="05" <?php if($_REQUEST['emp_month']=='05')echo "selected";?>>MAY</option>
                    <option value="06" <?php if($_REQUEST['emp_month']=='06')echo "selected";?>>JUN</option>
                    <option value="07" <?php if($_REQUEST['emp_month']=='07')echo "selected";?>>JUL</option>
                    <option value="08" <?php if($_REQUEST['emp_month']=='08')echo "selected";?>>AUG</option>
                    <option value="09" <?php if($_REQUEST['emp_month']=='09')echo "selected";?>>SEP</option>
                    <option value="10" <?php if($_REQUEST['emp_month']=='10')echo "selected";?>>OCT</option>
                    <option value="11" <?php if($_REQUEST['emp_month']=='11')echo "selected";?>>NOV</option>
                    <option value="12" <?php if($_REQUEST['emp_month']=='12')echo "selected";?>>DEC</option>	 
                </select> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Product Sub-category <span style="color:#F00">*</span></label>
              <div class="col-md-7"> 
                <select name="psubcat"  id= "psubcat" class="form-control required" required>
				   <option value="">--Please Select--</option>
				  <?php
                      $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1' ORDER BY prod_sub_cat");
                      while($row_pcat=mysqli_fetch_array($pcat)){
                      ?>
                      <option value="<?=$row_pcat['prod_sub_cat']?>" <?php if($_REQUEST['psubcat'] == $row_pcat['psubcatid']) { echo "selected" ;} ?>>
                      <?=$row_pcat['prod_sub_cat']?>
                      </option>
                      <?php
                      }
                      ?>
                </select> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Target Value <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <input type="text" class="form-control number required" name="target_val"  id="target_val" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Remark</label>
              <div class="col-md-7">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
              </div>
            </div>
          </div>
          <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This Target" <?php if($_POST["upd"]=="Save"){ echo "disabled";}?>>&nbsp;&nbsp;
          <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='myTarget.php?usercode=<?=$req_uid?>'">
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
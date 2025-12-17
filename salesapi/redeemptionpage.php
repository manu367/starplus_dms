<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$uid = $_REQUEST['usercode'];
$toloctiondet=explode("~",getLocationDetails($_REQUEST['po_from'],"state,id_type,disp_addrs,city",$link1));
$avl_rwd = getAnyDetails($_REQUEST['po_from'],"total_reward","location_code","reward_points_balance",$link1);
$lat = $_REQUEST['latitude'];
$long = $_REQUEST['longitude'];
$trackaddrs = $_REQUEST['trackaddress'];
$trackdistc = $_REQUEST['trackdistance'];
////// we hit save button
if($_POST){
	if($_POST['upd']=='Save'){
		@extract($_POST);
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
		//// Make System generated ref no.//////
		$res1 = mysqli_query($link1,"SELECT MAX(temp_no) AS no FROM reward_redemption_master WHERE location_code='".$partycode."'");
		$row1 = mysqli_fetch_array($res1);
		$nextno = $row1['no']+1;
		$refno = "RR/".date("Ymd")."/".$partycode."/".$nextno;
		$total_reward = array_sum($burn_reward);
		///// Insert Master Data
		$query2 = "INSERT INTO reward_redemption_master SET location_code='".$partycode."', system_ref_no='".$refno."', temp_no='".$nextno."', delivery_address='".$_POST['delivery_address']."', remark='".$_POST['remark']."', status='Pending For Approval', entry_by='".$uid."', entry_date='".$datetime."', entry_ip='".$ip."', total_redeem_reward='".$total_reward."', address='".$trackaddrs."',latitude='".$lat."',longitude='".$long."'";
		$result2 = mysqli_query($link1,$query2);
		//// check if query is not executed
		if (!$result2) {
	     	$flag = false;
         	$err_msg = "Error details1: " . mysqli_error($link1) . ".";
    	}
		///// Insert in item data by picking each data row one by one
		foreach($prod_code as $k=>$val)
		{   
	    	// checking row value of product and qty should not be blank
			if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
				/////////// insert data
		   		$query3 = "INSERT INTO reward_redemption_data SET system_ref_no='".$refno."', partcode='".$val."', qty='".$req_qty[$k]."', redeem_point='".$burn_reward[$k]."'";
		   		$result3 = mysqli_query($link1, $query3);
		   		//// check if query is not executed
		   		if(!$result3) {
	           		$flag = false;
               		$err_msg = "Error details2: " . mysqli_error($link1) . ".";
           		}
				////// insert in reward ledger
				$result5 = mysqli_query($link1,"INSERT INTO reward_points_ledger SET partcode='".$val."', location_code='".$partycode."', transaction_no='".$refno."', transaction_date='".$today."', reward_type ='BURN', cr_reward='0', dr_reward='".$burn_reward[$k]."', update_by='".$uid."', update_on='".$datetime."', update_ip='".$ip."'");
				//// check if query is not executed
				if (!$result5) {
					 $flag = false;
					 $err_msg = "Error details5: " . mysqli_error($link1) . ".";
				}
			}// close if loop of checking row value of product and qty should not be blank
		}/// close for loop
		//// update reward points of loaction
		$result4 = mysqli_query($link1,"UPDATE reward_points_balance SET total_reward = total_reward - '".$total_reward."', lastupdate_by='".$uid."', lastupdate_on='".$datetime."' WHERE location_code='".$partycode."'");
		//// check if query is not executed
		if (!$result4) {
			$flag = false;
			$err_msg = "Error details2.2: " . mysqli_error($link1) . ".";
		}
		//// get department id and subdepartment id of user written on 27 apr 23 by shekhar
		$res_usr = mysqli_query($link1,"SELECT department,subdepartment FROM admin_users WHERE username = '".$uid."'");
		$row_usr = mysqli_fetch_assoc($res_usr);
   		$result = mysqli_query($link1,"INSERT INTO user_track SET userid='".$uid."', task_name='Reward Redeem', task_action='Create', ref_no='".$refno."', latitude='".$_REQUEST['latitude']."', longitude='".$_REQUEST['longitude']."', address='".$trackaddrs."',travel_km='".$trackdistc."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."', department='".$row_usr['department']."', subdepartment='".$row_usr['subdepartment']."'");
		//// check if query is not executed
		if (!$result) {
		 	$flag = false;
		 	$err_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		////// insert in activity table////
		$flag = dailyActivity($uid,$refno,"Reward Redeem","ADD",$ip,$link1,$flag);
		///// check both master and data query are successfully executed
		if ($flag) {
        	mysqli_commit($link1);
        	$msg = "Reward is successfully redeemed with ref. no.".$refno;
			$respheadmsg = "Success";
			$cflag= "success";
    	} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again. ".$err_msg;
			$respheadmsg = "Failed";
			$cflag= "danger";
		} 
    	mysqli_close($link1);
		///// move to parent page
		header("Location:redeemptionpage.php?msg=".base64_encode($msg)."&usercode=".$uid."&latitude=".$_REQUEST['latitude']."&longitude=".$_REQUEST['longitude']."&trackaddress=".$_REQUEST['trackaddress']."&chkmsg=".$respheadmsg."&chkflag=".$cflag);
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
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<style type="text/css">
-ms-user-select: contain; /*IE,Edge*/
-webkit-user-select: text; /*Webkit*/
-moz-user-select: text; /*Mozilla*/
user-select: all; /*Global, select all with one click*/
</style>
<script>
$(document).ready(function(){
    //$("#frm2").validate();
	$("#frm2").validate({
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
	$("#po_from").select2({
  		ajax: { 
   			url: "getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "customer",
					userid: '<?=$uid?>'
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
$(document).ready(function(){
	$(".dynmprod").select2({
  		ajax: { 
   			url: "getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchProd: params.term, // search term
					requestFor: "product",
					rwdpnt:"Y",
					userid: '<?=$uid?>'
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
///// function to get price of product
function getProdInfo(ind){
	var productCode = document.getElementById("prod_code["+ind+"]").value;
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{productinfo:productCode,fromstate:"",idtype:""},
		success:function(data){
			var splitprice=data.split("~");
			var burnqty = document.getElementById("req_qty["+ind+"]").value;
			var avlrwd = document.getElementById("avl_reward").value;
			var burnrwd = splitprice[5];
			var totburnrwd = parseInt(burnqty)*parseInt(burnrwd);
			var balrwd = parseInt(avlrwd)-parseInt(totburnrwd);
	        document.getElementById("burn_reward["+ind+"]").value = totburnrwd;
			document.getElementById("bal_reward["+ind+"]").value = balrwd;
			if(balrwd < 0){
				$("#upd").attr("disabled","disabled");
			}else{
				$("#upd").removeAttr("disabled");
			}
	    }
	});
}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
       			<h2 align="center"><i class="fa fa-exchange"></i> Reward Redeemption</h2>
                <div align="left">
                <button title="Reward Ledger" type="button" class="btn btn-success" style="float:left" onClick="window.location.href='rewardledgerpage.php?usercode=<?=$uid?>'"><i class="fa fa-balance-scale fa-lg"></i>&nbsp;&nbsp;Reward Ledger</button>
                <button title="Redeem Point List" type="button" class="btn btn-info" style="float:right" onClick="window.location.href='redeemedpointslist.php?usercode=<?=$uid?>'"><i class="fa fa-list fa-lg"></i>&nbsp;&nbsp;Redeem Point List</button>
                </div><br/>
                <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <?php if($_REQUEST['msg']){?>
                    <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=base64_decode($_REQUEST['msg'])?>.
                    </div>
                    <?php 
                        unset($_POST);
                     }?>
                    <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                      <div class="form-group">
                        <div class="col-md-10">
                          <label class="col-md-5 control-label">Party Name <span style="color:#F00">*</span></label>
                          <div class="col-md-7">                            
                            <select name="po_from" id="po_from" class="form-control required" onChange="document.frm1.submit();">
                              <option value=''>--Please Select--</option>
                             <?php
                            if(isset($_POST["po_from"])){
                              $loc_name = explode("~",getAnyDetails($_POST["po_from"],"name, city, state","asc_code","asc_master",$link1));
                              echo '<option value="'.$_POST["po_from"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["po_from"].'</option>';
                            }
                            ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-10">
                          <label class="col-md-5 control-label">Available Reward</label>
                          <div class="col-md-7">
                            <input type="text" name="avl_reward" id="avl_reward" class="form-control" value="<?=$avl_rwd?>" readonly/>
                          </div>
                        </div>
                      </div>
                    </form>
                    <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
                      <div class="form-group" id='addr0' style="padding-top: 8px; padding-bottom: 8px; border:ridge; background-color: aliceblue; margin-right: 0px">
                        <div class="col-md-6"><label class="col-md-6 control-label">Product <span class="red_small">*</span></label>
                            <div class="col-md-6">
                                  <select name="prod_code[0]" id="prod_code[0]" class="form-control required dynmprod" onChange="getProdInfo(0);">
                                      <option value=''>--Please Select--</option>
                                  </select>
                                  <span id="prd_desc0"></span>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-5 control-label">Qty <span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control digits required" name="req_qty[0]" id="req_qty[0]" autocomplete="off" required maxlength="4" value="1" onKeyUp="getProdInfo(0);">
                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-5 control-label">Reward</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control digits" name="burn_reward[0]" id="burn_reward[0]" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-6 control-label">Balance</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control digits" name="bal_reward[0]" id="bal_reward[0]" autocomplete="off" readonly value="<?=$avl_rwd?>">
                            </div>
                        </div>	
                    </div>
                      <div class="form-group">
                        <div class="col-md-10">
                          <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
                          <div class="col-md-3">
                            <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:none" required><?=$toloctiondet[2]?></textarea>
                          </div>
                          <label class="col-md-3 control-label">Remark</label>
                          <div class="col-md-3">
                            <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
                          </div>
                        </div>
                      </div>
                      <!--<div class="form-group">
                        <div class="col-md-10">
                          <label class="col-md-3 control-label">Payment Terms</label>
                          <div class="col-md-3">
                            <textarea name="payment_terms" id="payment_terms" class="form-control addressfield" style="resize:none"></textarea>
                          </div>
                          <label class="col-md-3 control-label"></label>
                          <div class="col-md-3"> </div>
                        </div>
                      </div>
            -->          <div class="form-group">
                        <div class="col-md-12" align="center">
                          <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Redeem Reward">
                          <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_from']?>"/>
                        </div>
                      </div>
                    </form>
                </div>
                <!--End form group--> 
            </div>
            <!--End col-sm-9--> 
        </div>
        <!--End row content--> 
    </div>
    <!--End container fluid-->
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>
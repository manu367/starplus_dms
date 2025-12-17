<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM reward_redemption_master WHERE system_ref_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
$se = explode("~",getAdminDetails($po_row['userid'],"name,phone,emailid,band",$link1));
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Update'){
		$decodepono=base64_decode($refno);
	  	if($actiontaken=="Approved"){ $app_point = $approved_points; }else{ $app_point = 0;}
		////// start make auto sale order of this
		//// Make System generated PO no.//////
		$res_po = mysqli_query($link1,"select max(temp_no) as no from purchase_order_master where po_from='".$po_row["location_code"]."'");
		$row_po = mysqli_fetch_array($res_po);
		$c_nos = $row_po['no']+1;
		$po_no = $po_row["location_code"]."SO".$c_nos; 
		$parentcode = "DEHOHR001";
		$se = getAnyDetails($po_row["entry_by"],'name','username','admin_users',$link1);
		$partyinfo = explode("~",getAnyDetails($po_row["location_code"],'id_type,state','asc_code','asc_master',$link1));
		
		if(substr($po_row["location_code"],0,4)=="EADL"){ $saletype = "SECONDARY";}else if(substr($po_row["location_code"],0,4)=="EADS" || substr($po_row["location_code"],0,4)=="EART" || substr($po_row["location_code"],0,4)=="EADD"){ $saletype = "PRIMARY";}else{ $saletype = "TERTIARY";}
		///// Insert Master Data
		$ref_no = $po_row["entry_by"];
		$total_value = 0.00;
		///// Insert in item data by picking each data row one by one
		$res_pdata = mysqli_query($link1,"SELECT * FROM reward_redemption_data WHERE system_ref_no='".$po_row["system_ref_no"]."'");
		while($row_pdata = mysqli_fetch_assoc($res_pdata))
		{   
			// checking row value of product and qty should not be blank
			if($row_pdata['partcode']!='' && $row_pdata['qty']!='' && $row_pdata['qty']!=0) {
				///// get product details
				$proddet = explode("~",getAnyDetails($row_pdata['partcode'],"productcategory,productsubcat,brand","productcode","product_master",$link1));
				$price_expl = explode("~",getProductPrice($row_pdata['partcode'],$partyinfo[0],$partyinfo[1],$link1));
				$price = $price_expl[0];
				$mrp = $price_expl[1];
				$value = $price*$row_pdata['qty'];
				/////////// insert data
				$query2="insert into purchase_order_data set po_no='".$po_no."', prod_code='".$row_pdata['partcode']."',prod_cat='".$proddet[0]."',psc_id='".$proddet[1]."',brand_id='".$proddet[2]."', req_qty='".$row_pdata['qty']."', po_price='".$price."', po_value='".$value."', hold_price='".$price."', mrp='".$mrp."', discount='0', totalval='".$value."',warranty='',expected_deliv_date='', sale_type='".$saletype."'";
			   $result = mysqli_query($link1, $query2);
			   //// check if query is not executed
			   if (!$result) {
				   $flag = false;
				   $err_msg = "Error details2: " . mysqli_error($link1) . ".";
			   }
			   $total_value += $value;
			   ////// hold the PO qty in stock ////
			   $flag = holdStockQty($parentcode,$row_pdata['partcode'],$row_pdata['qty'],$link1,"");
			}// close if loop of checking row value of product and qty should not be blank
		}/// close for loop
		$query1= "INSERT INTO purchase_order_master set po_to='".$parentcode."',po_from='".$po_row["location_code"]."',po_no='".$po_no."',temp_no='".$c_nos."',ref_no='".$po_row["system_ref_no"]."',requested_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',req_type='SO',status='PFA',po_value='".$total_value."',create_by='".$_SESSION['userid']."',ip='".$ip."',sales_person='".$se."',sales_executive='".$po_row["entry_by"]."',payment_status='".$payment_terms."',transport_exp='',discount='".$total_discount."',discount_type='PD',remark='SO Against Reward Redemption',delivery_address='".$po_row["delivery_address"]."',address='".$trackaddrs."',latitude='".$lat."',longitude='".$long."',pjp_id='".$_REQUEST['taskid']."',entry_from='', sale_type='".$saletype."'";
		$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		////// insert in activity table////
		$flag=dailyActivity($_SESSION['userid'],$po_no,"SO","ADD",$ip,$link1,"");
		/////// add script when dealer PO to Distributor then it should be auto approved .requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022
		if(substr($po_row["location_code"],0,4)=="EADL"){
			$actiontaken = "Approved";
			///// update po status ///////////
			$res_upd = mysqli_query($link1,"UPDATE purchase_order_master set status='".$actiontaken."' where po_no='".$po_no."'");
			  //// check if query is not executed
			if (!$res_upd) {
				 $flag = false;
				 $err_msg = "Error details5: " . mysqli_error($link1) . ".";
			}
			////// insert in approval table////
			$flag = approvalActivity($po_no,$today,"PO",$_SESSION['userid'],$actiontaken,$today,$currtime,"AUTO APPROVED",$ip,$link1,"");
			////// insert in activity table////
			$flag = dailyActivity($_SESSION['userid'],$po_no,"PO APPROVAL","APPROVAL",$ip,$link1,"");
		}
		 /////// end add script when dealer PO to Distributor then it should be auto approved .requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022
		 ////// end make auto sale order of this
		
	  	///// update po status ///////////
	  	mysqli_query($link1,"UPDATE reward_redemption_master SET so_no='".$po_no."', status='".$actiontaken."', app_by='".$_SESSION['userid']."', app_date='".$datetime."', app_remark='".$remark."', app_ip='".$ip."', total_approved_reward='".$app_point."' WHERE system_ref_no = '".$decodepono."'")or die("ER1".mysqli_error($link1));
	  	////// insert in approval table////
	 	approvalActivity($decodepono,$po_row["entry_date"],"Redeem Reward",$_SESSION['userid'],$actiontaken,$today,$currtime,$remark,$ip,$link1,"");
     	////// insert in activity table////
	 	dailyActivity($_SESSION['userid'],$decodepono,"REDEEM REWARD APPROVAL",$actiontaken,$ip,$link1,"");
		
		if($actiontaken=="Rejected"){
			$res_data2 = mysqli_query($link1,"SELECT * FROM reward_redemption_data WHERE system_ref_no='".$docid."'");
			while($row_data2 = mysqli_fetch_assoc($res_data2)){
				$result5 = mysqli_query($link1,"INSERT INTO reward_points_ledger SET partcode='".$row_data2['partcode']."', location_code='".$po_row["location_code"]."', transaction_no='".$docid."', transaction_date='".$po_row["entry_date"]."', reward_type ='BURN', cr_reward='".$row_data2['redeem_point']."', dr_reward='0', update_by='".$_SESSION['userid']."', update_on='".$datetime."', update_ip='".$ip."'");
			}
			$result4 = mysqli_query($link1,"UPDATE reward_points_balance SET total_reward = total_reward + '".$po_row["total_redeem_reward"]."', lastupdate_by='".$_SESSION['userid']."', lastupdate_on='".$datetime."' WHERE location_code='".$po_row["location_code"]."'");	
		}
	 	////// return message
	 	$msg="You have successfully taken approval (".$actiontaken.") action for Reward Redemption ".$decodepono;
  	}else{
	 	////// return message
	 	$msg="Something went wrong. Please try again.";
  	}
  	///// move to parent page
  	header("Location:redeempoints_approval.php?msg=".$msg."".$pagenav);
  	exit;
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
 <script type="text/javascript">
$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
	});
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-address-card"></i> Reward Redemption Approval</h2>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Basic Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">System Ref. No.</label></td>
                <td><?php echo $docid;?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?=$po_row["entry_date"]?></td>
              </tr>
              <tr>
                <td><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo $po_row["location_code"];?></td>
                <td width="20%"><label class="control-label">Entry By</label></td>
                <td width="30%"><?php echo $po_row["entry_by"];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Redeem Points</label></td>
                <td width="30%"><?php echo $po_row["total_redeem_reward"];?></td>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><?php echo $po_row["status"];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td width="30%"><?php echo $po_row["delivery_address"];?></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><?php echo $po_row["remark"];?></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Product Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
        	<thead>
            	<th width="10%">S.No.</th>
                <th width="40%">Product</th>
                <th width="25%">Qty</th>
                <th width="25%">Redeem Point</th>
            </thead>
            <tbody>
              <?php
			  $i=1;
			  $res_data = mysqli_query($link1,"SELECT * FROM reward_redemption_data WHERE system_ref_no='".$docid."'");
			  while($row_data = mysqli_fetch_assoc($res_data)){
			  ?>
              <tr>
                <td><?=$i?></td>
                <td><?=getAnyDetails($row_data['partcode'],"productname","productcode","product_master",$link1).", ".$row_data['partcode']?></td>
                <td><?=$row_data['qty']?></td>
                <td><?=$row_data['redeem_point']?></td>
              </tr>
              <?php
			  	$i++;
			  }
			  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  <div class="panel panel-info table-responsive">
      <div class="panel-heading">Approval Action</div>
      <div class="panel-body">
        <?php if($po_row['status']=="Pending For Approval"){ ?>
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="50%"><label class="control-label">Approved Points <span class="red_small">*</span></label></td>
                <td width="50%">
                 <input name="approved_points" id="approved_points" type="text" class="required form-control number" required value="<?php echo $po_row['total_redeem_reward'];?>" style="width:300px;" readonly/>
                </td>
              </tr>
              <tr>
                <td width="50%"><label class="control-label">Action Taken <span class="red_small">*</span></label></td>
                <td width="50%">
                 <select name="actiontaken" id="actiontaken" class="required form-control" required style="width:300px;">
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                </select>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control addressfield" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input name="refno" id="refno" type="hidden" value="<?=base64_encode($docid)?>"/>
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='redeempoints_approval.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
          <?php }else{ }?>
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr>
                <th width="20%">Action Date & Time</th>
                <th width="30%">Action Taken By</th>
                <th width="20%">Action</th>
                <th width="30%">Action Remark</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$res_poapp=mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$po_row['system_ref_no']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                <td><?php echo $row_poapp['action_taken']?></td>
                <td><?php echo $row_poapp['action_remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='redeempoints_approval.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
          <?php }?>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<div id="loader"></div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
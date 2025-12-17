<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM party_collection where id='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Update'){
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
	  	$decodepono=base64_decode($refno);
	  	if($actiontaken=="Approved"){ $app_amt = $approved_amt; }else{ $app_amt = 0.00;}
	  	///// update po status ///////////
	  	$result1 = mysqli_query($link1,"UPDATE party_collection set status='".$actiontaken."', approved_amt='".$app_amt."' where id = '".$decodepono."'");
		//// check if query is not executed
		if (!$result1) {
			 $flag = false;
			 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
	  	////// if approved the entry in payment receipt
	  	if($actiontaken=="Approved"){
	  		///// get counter
	  		$res_po=mysqli_query($link1,"select max(rcvpay_counter) as no, rcvpay_str from document_counter where location_code='".$parentcode."'");
			if(mysqli_num_rows($res_po)){
				$row_po=mysqli_fetch_array($res_po);
				$c_nos=$row_po['no']+1;
				$pad1=str_pad($c_nos,4,"0",STR_PAD_LEFT);  
				$doc_no=$row_po['rcvpay_str'].$pad1; ;
				///// Insert Master Data
		 		$query2= "INSERT INTO payment_receive set doc_no='".$doc_no."',from_location='".$po_row['party_code']."',to_location='".$parentcode."',amount='".$po_row['amount']."', rec_amount='".$app_amt."' ,status='Approve',payment_mode='".$po_row['pay_mode']."', bank_name='', bank_branch='', dd_cheque_no='', dd_cheque_dt='', receipt_no='COLLECTION', transaction_id='".$po_row['transaction_no']."', remark='".$remark."',payment_date='".$po_row['transaction_date']."',entry_dt='".$today."',entry_time='".$currtime."',entry_by='".$_SESSION['userid']."',ip='".$ip."',address='".$po_row['address']."',latitude='".$po_row['latitude']."',longitude='".$po_row['longitude']."',pjp_id='".$po_row['pjp_id']."'";
				$result2 = mysqli_query($link1,$query2);
				//// check if query is not executed
				if (!$result2) {
					 $flag = false;
					 $err_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
				///// update document counter
				$result3=mysqli_query($link1, "update document_counter set rcvpay_counter='".$c_nos."' where location_code='".$parentcode."'");
				//// check if query is not executed
				if (!$result3) {
					 $flag = false;
					 $err_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
				$flag=dailyActivity($_SESSION['userid'],$doc_no,"RP","Receive payment",$ip,$link1,$flag);
				$type = "RP";
				$flag=partyLedger($parentcode,$po_row['party_code'],$doc_no,$po_row['transaction_date'],$today, $currtime, $_SESSION['userid'],$type, $app_amt,'CR',$link1, $flag);
		
				/// insert  into  location_account_ledger table //////////////////////////
	   			/*$result4 = mysqli_query($link1,"update current_cr_status set  cr_abl = cr_abl +'".$app_amt."'  , total_cr_limit = total_cr_limit +'".$app_amt."'   where  parent_code = '".$parentcode."' and  asc_code = '".$po_row['party_code']."' ");
				//// check if query is not executed
				if (!$result4) {
					 $flag = false;
					 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
				}*/
				if(mysqli_num_rows(mysqli_query($link1,"select id from current_cr_status where parent_code='".$parentcode."' and asc_code='".$po_row['party_code']."'"))>0){
					$upd = mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl+'".$app_amt."',total_cr_limit=total_cr_limit+'".$app_amt."', last_updated='$today' where parent_code='".$parentcode."' and asc_code='".$po_row['party_code']."'");
				   ############# check if query is not executed
					if (!$upd) {
						$flag = false;
						$err_msg = "Error details11: " . mysqli_error($link1) . ".";
					}
				}else{
						$upd = mysqli_query($link1,"insert into current_cr_status set cr_abl=cr_abl+'".$app_amt."',total_cr_limit=total_cr_limit+'".$app_amt."', last_updated='$today', parent_code='".$parentcode."' , asc_code='".$po_row['party_code']."'");
					   ############# check if query is not executed
						if (!$upd) {
							$flag = false;
							$err_msg = "Error details11: " . mysqli_error($link1) . ".";
						}
				}
				
				//// insert into approval activities table
				$flag=approvalActivity($doc_no,$po_row['transaction_date'],$type,$_SESSION['userid'],"Approve",$today,$currtime,$remark,$ip,$link1,$flag);	
				////// insert in activity table////
				$flag=dailyActivity($_SESSION['userid'],$doc_no,"Payment Approval","Approve",$ip,$link1,$flag);
			}else{
			 	$flag = false;
			 	$err_msg = "Request could not be processed receipt series not found. Please try again.";
			}
	  	}
	  	////// insert in approval table////
	 	$flag = approvalActivity($decodepono,$po_row["entry_date"],"Collection",$_SESSION['userid'],$actiontaken,$today,$currtime,$remark,$ip,$link1,$flag);
     	////// insert in activity table////
	 	$flag = dailyActivity($_SESSION['userid'],$decodepono,"COLLECTION APPROVAL","APPROVAL",$ip,$link1,$flag);
	 	////// return message
	 	$msg="You have successfully taken approval (".$actiontaken.") action for Collection ".$decodepono;
  	}else{
	 	////// return message
	 	$msg="Something went wrong. Please try again.";
  	}
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Payment Received successfully  with ref. no.".$doc_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
  	///// move to parent page
  	header("Location:collection_approval.php?msg=".$msg."".$pagenav);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
 <style>
/* The Modal (background) */
.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		padding-top: 50px; /* Location of the box */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
		background-color: #fefefe;
		margin: auto;
		padding: 20px;
		border: 1px solid #888;
		width: 50%;
		height: 50%;
		margin-top: 20px;
	}

	/* The Close Button */
	.close {
		color: #aaaaaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
	}
	div.dropdown-menu.open
    {
        max-width:200px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:200px !important;
        overflow-y:auto;
    }
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-address-card"></i> Collection Approval</h2>
      <h4 align="center"><?php echo "Collection-".$docid;?></h4>
      <h4 align="center"><?php echo $po_row["entry_date"];?></h4>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Collection Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Party Name</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['party_code'],"name,city,state,asc_code",$link1));?></td>
                <td width="20%"><label class="control-label">Entry By</label></td>
                <td width="30%"><?php echo str_replace("~",",",getAdminDetails($po_row['user_id'],"name,oth_empid,username",$link1));?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Collected Amount</label></td>
                 <td><?php echo $po_row['amount'];?></td>
                 <td><label class="control-label">Payment Mode</label></td>
                 <td><?php echo $po_row['pay_mode'];?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Approved Amount</label></td>
                 <td><?php echo $po_row['approved_amt'];?></td>
                 <td><label class="control-label">Status</label></td>
                 <td><?php echo $po_row['status'];?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Attachment</label></td>
                 <td><?php if($po_row['doc_name']){?> <a href='<?='../salesapi/collectionimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['doc_name']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download attachment"></i></a> <?php }else{ echo  "NA";}?></td>
                 <td><label class="control-label">&nbsp;</label></td>
                 <td>&nbsp;</td>
               </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  <div class="panel panel-info table-responsive">
      <div class="panel-heading">Approval Action</div>
      <div class="panel-body">
        <?php if($po_row['status']=="Pending"){ ?>
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="50%"><label class="control-label">Approved Amount <span class="red_small">*</span></label></td>
                <td width="50%">
                 <input name="approved_amt" id="approved_amt" type="text" class="required form-control number" required value="<?php echo $po_row['amount'];?>" style="width:300px;"/>
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
                <td width="50%"><label class="control-label">In Favour Of <span class="red_small">*</span></label></td>
                <td width="50%">
                 <select name="parentcode" id="parentcode" required class="form-control selectpicker required" data-live-search="true">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select uid from mapped_master where mapped_code='".$po_row['party_code']."'";
					$res_parent=mysqli_query($link1,$sql_parent);
					if(mysqli_num_rows($res_parent)>0){
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[uid]'"));
                          ?>
                    <option value="<?=$result_parent['uid']?>" <?php if(isset($_REQUEST['parentcode']) && $result_parent['uid']==$_REQUEST['parentcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['uid']?>
                    </option>
                    <?php
					}
					}else{
						if(substr($po_row['party_code'],0,4)=="EADS"){
                    	$party_det2 = mysqli_query($link1,"select asc_code,name , city, state,id_type from asc_master where id_type IN ('HO','BR') AND status='Active' AND asc_code!='".$po_row['party_code']."' ORDER BY name");
						}else{
							$party_det2 = mysqli_query($link1,"select asc_code,name , city, state,id_type from asc_master where id_type IN ('HO','BR','DS') AND status='Active' AND asc_code!='".$po_row['party_code']."' ORDER BY name");
						}
                    while($result_parent=mysqli_fetch_array($party_det2)){
                          ?>
                    <option value="<?=$result_parent['asc_code']?>" <?php if(isset($_REQUEST['parentcode']) && $result_parent['asc_code']==$_REQUEST['parentcode'])echo "selected";?> >
                       <?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['asc_code']?>
                    </option>
                    <?php
					}
                    }?>
                 </select>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input name="refno" id="refno" type="hidden" value="<?=base64_encode($docid)?>"/>
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='collection_approval.php?<?=$pagenav?>'">
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
			$res_poapp=mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$po_row['id']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                <td><?php echo $row_poapp['action_taken']?></td>
                <td><?php echo $row_poapp['action_remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='collection_approval.php?<?=$pagenav?>'"></td>
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
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
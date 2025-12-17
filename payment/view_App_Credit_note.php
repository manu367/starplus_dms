<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
$docid = $_REQUEST['ref_no'];
$po_sql = "SELECT * FROM credit_note WHERE ref_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$bill_det = mysqli_fetch_assoc($po_res);
////// final submit form ////
if($_POST){
	@extract($_POST);
	if(isset($_POST['updButton'])){
		if($_POST['updButton']=='Update'){  
			##transcation parameter #################
		    mysqli_autocommit($link1, false);
            $flag = true;
			$err_msg = "";
			if($actiontaken == 'Approved') {
				$appstatus="Approved";
				################################################## Update credit limit of party
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM current_cr_status WHERE parent_code='".$location_code."' AND asc_code='".$party_name."'"))>0){
					$upd = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl=cr_abl+'".$amt."',total_cr_limit=total_cr_limit+'".$amt."', last_updated='".$today."' WHERE parent_code='".$location_code."' AND asc_code='".$party_name."'");
					############# check if query is not executed
					if (!$upd) {
						$flag = false;
						$err_msg = "Error details11: " . mysqli_error($link1) . ".";
					}
				}else{
					$upd = mysqli_query($link1,"INSERT INTO current_cr_status SET cr_abl=cr_abl+'".$amt."',total_cr_limit=total_cr_limit+'".$amt."', last_updated='".$today."', parent_code='".$location_code."' , asc_code='".$party_name."'");
					############# check if query is not executed
					if (!$upd) {
						$flag = false;
						$err_msg = "Error details12: " . mysqli_error($link1) . ".";
					}
				}
				$result_ledger = mysqli_query($link1,"INSERT INTO party_ledger SET location_code='".$location_code."',cust_id='".$party_name."',doc_no='".$ref_no."',doc_date='".$doc_date."', entry_date='".$today."',entry_time='".$currtime."',entry_by='".$_SESSION['userid']."',doc_type='CR NOTES',amount='".$amt."',cr_dr='CR'");
				//// check if query is not executed
				if (!$result_ledger) {
					 $flag = false;
					 $err_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
			 	#################33 update status in credit note#################################3
				$main = mysqli_query($link1,"UPDATE credit_note SET app_status='".$appstatus."',status='".$appstatus."',app_date='".$today."',app_time='".$now."',app_remark='".$remark."',app_id ='".$_SESSION['userid']."' WHERE ref_no='".$ref_no."'");						    
				############# check if query is not executed
				if (!$main) {
					$flag = false;
					$err_msg = "Error details14: " . mysqli_error($link1) . ".";
				}					
				$msg="CR NOTE ".$ref_no." is ".$appstatus;
				$cflag ="success";
				$cmsg="success";
				
				
				$arr_taxx = array();
				$arr_val = array();
				$gst_type = "";
				$tcs_per = $bill_det["tcs_per"];
				$tcs_amt = $bill_det["tcs_amt"];
				$round_off = $bill_det["round_off"];
				//// run data loop for retriving tax parameters
				$res_data = mysqli_query($link1,"SELECT value,sgst_per,cgst_per,igst_per,sgst_amt,cgst_amt,igst_amt FROM credit_note_data WHERE ref_no='".$ref_no."'");
				while($row_data = mysqli_fetch_assoc($res_data)){
					if($row_data["sgst_per"]!="" && $row_data["sgst_per"]!="0.00"  && $row_data["sgst_per"]!="0"){
						$gstper = $row_data["sgst_per"] + $row_data["cgst_per"];					
						$arr_taxx[$gstper] += $row_data["sgst_amt"] + $row_data["cgst_amt"];
						$arr_val[$gstper] += $row_data["value"];
						$gst_type = "SGST-CGST";
					}else{
						$gstper = $row_data["igst_per"];					
						$arr_taxx[$gstper] += $row_data["igst_amt"];
						$arr_val[$gstper] += $row_data["value"];
						$gst_type = "IGST";
					}
				}
				/////// make account ledger entry for location
				/////// start ledger entry for tally purpose ///// written by shekhar on 17 nov 2022
				///// make ledger array which are need to be process
				$arr_ldg_name = array(
				"igstldgname" => "IGST @",
				"cgstldgname" => "CGST @",
				"sgstldgname" => "SGST @",
				"igstdocldgname" => "Central Sale @",
				"cgstdocldgname" => "GST Sales @",
				"sgstdocldgname" => "GST Sales @",
				"tcsldgname" => "TCS on Sale @",
				"roundoffldgname" => "Rounded Off"
				);
				/////// function parameter sequence
				//// 1. location code on which trasaction is being execute
				//// 2. document no. which is being execute
				//// 3. document date which is being execute
				//// 4. Voucher Type . It means Purchase(1)/Sale(2)/Credit Note(3)/Debit Note(4)/Payment(5)/Receipt(6)
				//// 5. Voucher For . It means Purchase/Sale/Credit Note/Debit Note/Payment/Receipt
				//// 6. Tax Percentage and its tax amount array which are applicable of selected transaction
				//// 7. Each line of item total value array with its tax percentage
				//// 8. TCS % if applicable
				//// 9. TCS Amount if applicable
				//// 10. Round Off value
				//// 11. GST Type either it will IGST or CGST/SGST
				//// 12. All ledger name which are related to current transaction
				//// 13. Account group name
				//// 14. Account head name
				//// 15. DB connection link
				//// 16. transaction flag for commmit/rollback
				$resp = explode("~",storeLedgerTransaction($location_code,$ref_no,$today,"3","Credit Note",$arr_taxx,$arr_val,$tcs_per,$tcs_amt,$round_off,$gst_type,$arr_ldg_name,"GST Sales","GST Sales Account",$link1,$flag));
				$flag = $resp[0];
				$error_msg = $resp[1];
				/////// end ledger entry for tally purpose ///// written by shekhar on 17 nov 2022
				
			} ############ approved condition ends 
		    else if($actiontaken == 'Rejected'){
				$appstatus="Rejected"; 
				$main  = mysqli_query($link1,"UPDATE credit_note SET app_status='".$appstatus."',status='".$appstatus."',app_date='".$today."',app_time='".$now."',app_remark='".$remark."',app_id ='".$_SESSION['userid']."' where ref_no='".$ref_no."'");
						    
				############# check if query is not executed
				if (!$main) {
					$flag = false;
					$err_msg = "Error details15: " . mysqli_error($link1) . ".";
				}
				$msg="CR NOTE ".$ref_no." is ".$appstatus;
				$cflag ="success";
				$cmsg="success";
			}
			else {
			
			}
			
			///// check query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again. ".$err_msg;
			}
			mysqli_close($link1);
			///// move to parent page
			header("location:approve_credit_notes.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			exit;
	    }
	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script src="../js/jquery.validate.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-address-card"></i> View Credit Note Detail</h2><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1 ">Credit Note Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location</label></td>
                <td width="30%"><?php $get_result2=explode("~",getLocationDetails($bill_det['location_id'],"name,city,state",$link1)); echo $get_result2[0].",".$get_result2[1].",".$get_result2[2];?></td>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><?php if($bill_det['app_status']!=''){if($bill_det['status']=='Cancelled')echo $bill_det['status']; else echo $bill_det['app_status']; } else echo $bill_det['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Name</label></td>
                <td><?php $custdet = explode(",",getAnyParty($bill_det['cust_id'],$link1)); echo $custdet[0].",".$custdet[1].",".$custdet[2].",".$custdet[3];?></td>
                <td><label class="control-label">Entry By</label></td>
                <td><?=getAdminDetails($bill_det['create_by'],"name",$link1)."(".$bill_det['create_by'].")";?></td>
              </tr>
               <tr>
                <td><label class="control-label">System Reference No.</label></td>
                <td><?php echo $bill_det['ref_no'];?></td>
                <td><label class="control-label">Amount</label></td>
                <td><?=$bill_det['amount']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Description</label></td>
                <td colspan="3"><?php echo $bill_det['description'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Remark</label></td>
                <td colspan="3"><?php echo $bill_det['remark'];?></td>
              </tr>
              <?php if($bill_det['status']=="Cancelled"){ ?>
               <tr>
                 <td><label class="control-label">Cancellation Reason</label></td>
                 <td align="left"><?=$bill_det[cancel_reason]?></td>
                  <td><label class="control-label">Cancelled By</label></td>
                 <td align="left"><?=$bill_det['cancelled_by']." (".$bill_det['cancel_date'].")";?>&nbsp;</td>
               </tr>
           <?php }?>
               
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Items Information</div>
        <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                <th style="text-align:center" width="4%">#</th>
                <th style="text-align:center" width="19%">Product</th>
                <th style="text-align:center" width="5%">Qty</th>
                <th style="text-align:center" width="5%">Price</th>
                <th style="text-align:center" width="5%">Value</th>
                <th style="text-align:center" width="7%">Discount %</th>
                <th style="text-align:center" width="7%">Discount Amount</th>
                <?php if($get_result2[2] == $custdet[2]){?>
                <th style="text-align:center" width="5%">Sgst Per(%)</th>
                <th style="text-align:center" width="5%">Sgst Amt</th>
                <th style="text-align:center" width="5%">Cgst Per(%)</th>
                <th style="text-align:center" width="6%">Cgst Amt</th>
                <?php } else {?>
                <th style="text-align:center" width="6%">Igst Per(%)</th>
                <th style="text-align:center" width="8%">Igst Amt</th>
                <?php }?>
                <th style="text-align:center" width="13%">Total</th>
              </tr>
            </thead>
            <tbody>
           <?php
            $i=1;
            $podata_sql = "SELECT * FROM credit_note_data where ref_no='".$docid."'";
            $podata_res = mysqli_query($link1,$podata_sql);
            while($podata_row = mysqli_fetch_assoc($podata_res)){
				$data = getProductDetails($podata_row['prod_code'],"productname,model_name,productcode",$link1);
				$d = explode('~', $data);
            ?>
              <tr>
                <td><?=$i?></td>
                <td><?php if($podata_row["prod_cat"]=="C"){ echo $podata_row["combo_name"];}else{ echo $d[0].' | '.$d[1].' | '.$d[2];}?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount_per']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                <?php if($get_result2[2] == $custdet[2]){?>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <?php }else{?>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <?php }?>
                <td style="text-align:right"><?=$podata_row['totalvalue']?></td>
              </tr>
            <?php
            $i++;
            }
            ?>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
   <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Approval Action</div>
      <div class="panel-body">
        <?php if($bill_det['status']=='Pending For Approval'){ ?>
        <table class="table table-bordered" width="100%">
            <tbody>
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
                <td><textarea name="remark" id="remark" required class="required addressfield form-control"  style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <button class='btn<?=$btncolor?>' id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>&nbsp;
                  <input name="ref_no" id="ref_no" type="hidden" value="<?=$bill_det['ref_no']?>"/>
                  <input name="amt" id="amt" type="hidden" value="<?=$bill_det['amount']?>"/>   
                  <input name="party_name" id="party_name" type="hidden" value="<?=$bill_det['cust_id']?>"/>   
                  <input type="hidden" name="location_code" id="location_code" value="<?=$bill_det['location_id']?>">
                 <input type="hidden" name="doc_date" id="doc_date" value="<?=$bill_det['create_date']?>">				  
                  
                 
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location='approve_credit_notes.php?status=<?=$_REQUEST['status']?>&from_date=<?=$_REQUEST['from_date']?>&to_date=<?=$_REQUEST['to_date']?>&location_code=<?=$_REQUEST['location_code']?><?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
          <?php } ?>
		  
		<?php if($bill_det['app_status']!="" && $bill_det['status']!="Cancelled"){?>
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="20%">Approved/Reject By</th>
                <th width="30%">Approved/Reject Status</th>
                <th width="20%">Approved/Reject Date</th>
                <th width="30%">Approved/Reject Remark</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $name=getAdminDetails($bill_det['app_id'],"name",$link1)?></td>
                <td><?=$bill_det['app_status']?></td>
                <td><?php if($bill_det['app_date']!='0000-00-00'){ echo $bill_det['app_date'];}?></td>
                <td><?=$bill_det['app_rmk']?></td>
              </tr>
               <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location='approve_credit_notes.php?status=<?=$_REQUEST['status']?>&from_date=<?=$_REQUEST['from_date']?>&to_date=<?=$_REQUEST['to_date']?>&location_code=<?=$_REQUEST['location_code']?><?=$pagenav?>'"></td>
                </tr>
              
              <?php }?>
             
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
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
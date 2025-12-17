<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
 $po_sql="SELECT * FROM payment_receive where doc_no='".$docid."'   ";
$po_res=mysqli_query($link1,$po_sql);
$p_row=mysqli_fetch_assoc($po_res);

$app="SELECT * FROM approval_activities where ref_no='".$docid."'";
$app_res=mysqli_query($link1,$app);
$ap_row=mysqli_fetch_assoc($app_res);

@extract($_POST);
////// we hit save button

 if (isset($_POST['upd']) && $_POST['upd']=='Save'){
   
	$flag = true;
	
	if($_POST['action']=='Approve' ){
	///// Insert Master Data
     $query1= "update payment_receive set status='".$action."', rec_amount='".$rec_amount."' where doc_no='".$docid."' ";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	///// inert into party ledger
	//// condition if it is case of account adjustment  then entry of CR/DR  in party ledger else entry of cr in party ledger//////////////////////////
	if($adjust_type){
	$type = "RP Account Adjustment";
	$flag=partyLedger($_POST['parentcode'],$_POST['partycode'],$docid,$_REQUEST['pay_date'],$today, $currtime, $_SESSION['userid'],$type, $rec_amount,$adjust_type,$link1, $flag);
	
		////////////// update current_cr_status table /////////////////////////////////////
			if($adjust_type == 'CR') {
			 $adjustment  = " total_cr_limit = total_cr_limit + '".$rec_amount."' ";
			 $adjustcredit =  "cr_abl = cr_abl +  '".$rec_amount."' ";			
			}
			else if($adjust_type == 'DR') {
			 $adjustment  = " total_cr_limit = total_cr_limit -  '".$rec_amount."' ";
			 $adjustcredit =  "cr_abl = cr_abl -  '".$rec_amount."' ";		
			} else {$adjustment = "";}
			
		 	$query2 = "update current_cr_status set $adjustment , $adjustcredit,last_updated = '".$today."' where parent_code = '".$_POST['parentcode']."' and  asc_code = '".$_POST['partycode']."'  ";			
               $result1 = mysqli_query($link1, $query2);
                    //// check if query is not executed
                    if (!$result1) {
                        $flag = false;
                        $err_msg = "Error Code2:";
                    }
	
	}else {
	$type = "RP";
	$flag=partyLedger($_POST['parentcode'],$_POST['partycode'],$docid,$_REQUEST['pay_date'],$today, $currtime, $_SESSION['userid'],$type, $rec_amount,'CR',$link1, $flag);
	
	/// insert  into  location_account_ledger table //////////////////////////
   $result2=mysqli_query($link1,"update current_cr_status set  cr_abl = cr_abl +'".$rec_amount."'  , total_cr_limit = total_cr_limit +'".$rec_amount."'   where  parent_code = '".$_POST['parentcode']."' and  asc_code = '".$_POST['partycode']."' ");
	//// check if query is not executed
    if (!$result2) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	
	
	}
	//// insert into approval activities table
	$flag=approvalActivity($docid,$p_row['entry_dt'],$type,$_SESSION['userid'],$action,$today,$currtime,$remark,$ip,$link1,$flag);
	
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"Reciept Approval",$action,$ip,$link1,$flag);
	}
	//////////   case of Reject ///////////////////////////////////////////////////////////////////////////////
	else if($_POST['action']=='Reject' )  {
	
	///// Insert Master Data
    $query1= "update payment_receive set status='".$action."', rec_amount='".$rec_amount."' where doc_no='".$docid."' ";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	
	//// insert into approval activities table
	$flag=approvalActivity($docid,$p_row['entry_dt'],$type,$_SESSION['userid'],$action,$today,$currtime,$remark,$ip,$link1,$flag);
	
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"Reciept Approval",$action,$ip,$link1,$flag);
	
	} else {}
	
	///// check payment receive query is successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Reciept ".$action." successfully  with ref. no.".$docid;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:payment_approval_list.php?msg=".$msg."".$pagenav);
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

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
    $("#frm1").validate();
});

$(document).ready(function () {
	$('#dd_ch_dt').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>

<script>
/// function to hide details in case of cash
function get_details(val)
{
	if(val==1)
	{
		document.getElementById('b_name').style.display='none';
		document.getElementById('b_branch').style.display='none';
		document.getElementById('d_no').style.display='none';
		document.getElementById('d_dt').style.display='none';
		document.getElementById('t_id').style.display='none';
		document.getElementById('re_no').style.display='none';
		
	}
	
	else if(val==4)
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('d_no').style.display='none';
		document.getElementById('d_dt').style.display='none';
		document.getElementById('t_id').style.display='block';
		document.getElementById('re_no').style.display='block';
	}
	
	else if(val==5)
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('d_no').style.display='none';
		document.getElementById('d_dt').style.display='none';
		document.getElementById('t_id').style.display='none';
		document.getElementById('re_no').style.display='block';
	}
	else
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('d_no').style.display='block';
		document.getElementById('d_dt').style.display='block';
		document.getElementById('t_id').style.display='block';
		document.getElementById('re_no').style.display='block';
	}
}
</script>

</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-rupee"></i> Reciept Details</h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Reciept Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($p_row['from_location'],"name,city,state",$link1));?>
				
				</td>
                <td width="20%"><label class="control-label">To Location</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($p_row['to_location'],"name,city,state",$link1));?>
				
				</td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $p_row['doc_no'];?></td>
                <td><label class="control-label">Amount</label></td>
                <td><?php echo $p_row['amount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Received Amount</label></td>
                <td><?php echo $p_row['rec_amount'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $p_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Payment Mode</label></td>
                <?php $mode=mysqli_query($link1,"select * from payment_mode where id='".$p_row['payment_mode']."'"); $mrow=mysqli_fetch_assoc($mode);?>
                <td><?php echo $mrow['mode'];?></td>
                <td><label class="control-label">Reciept Date</label></td>
                <td><?php echo dt_format($p_row['payment_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Bank Name</label></td>
                <td><?php echo $p_row['bank_name'];?></td>
                <td><label class="control-label">Bank Branch</label></td>
                <td><?php echo $p_row['bank_branch'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">DD/Cheque No</label></td>
                <td><?php echo $p_row['dd_cheque_no'];?></td>
                <td><label class="control-label">DD/Cheque Date</label></td>
                <td><?php echo dt_format($p_row['dd_cheque_dt']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Receipt/Ref No.</label></td>
                <td><?php echo $p_row['receipt_no'];?></td>
                <td><label class="control-label">Transaction Id</label></td>
                <td><?php echo $p_row['transaction_id'];?></td>
              </tr>
			  <tr>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $p_row['remark'];?></td>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($p_row['entry_by'],"name",$link1);?></td>
              </tr>
			  
              <tr>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo dt_format($p_row['entry_dt']);?></td>
                <td><label class="control-label">Entry Time</label></td>
                <td><?php echo $p_row['entry_time'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">IP</label></td>
                <td><?php echo $p_row['ip'];?></td>
                <td><label class="control-label">Approve By</label></td>
                <td><?php echo getAdminDetails($ap_row['action_by'],"name",$link1);?></td>
              </tr>
              
              <tr>
                <td><label class="control-label">Approve Date</label></td>
                <td><?php echo dt_format($ap_row['action_date']);?></td>
                <td><label class="control-label">Approve Time</label></td>
                <td><?php echo $ap_row['action_time'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Approve Remark</label></td>
                <td><?php echo $ap_row['action_remark'];?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
  </div><!--close panel group-->
  <!--<div class="row" align="center">
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='paymentlist.php?<?=$pagenav?>'">
  </div>-->
 </div><!--close col-sm-9-->
</div>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-rupee"></i> Reciept Approval </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Received Amount<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <input type="text" name="rec_amount" id="rec_amount" class="form-control" value="<?php echo $p_row['amount'];?>" onKeyPress="return onlyFloatNum(this.value);" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Action<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="action" id="action" required class="form-control selectpicker required" data-live-search="true">
                 <option value="" selected="selected">Please Select </option>
                   <option data-tokens="Approve" value="Approve">Approve</option>
                     <option data-tokens="Reject" value="Reject">Reject</option>
                      </select>
              </div>
            </div>
          </div>
         
        <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Remark<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <textarea name="remark" id="remark" class="form-control" required></textarea>
              </div>
            </div>
          </div>
        <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Save" title="Approve Reciept">
			  <input type="hidden" name="adjust_type" id="adjust_type" value="<?=$p_row['adjustment_type']?>">
			  <input type="hidden"  id="partycode" name="partycode" value="<?=$p_row['from_location']?>" >
			  <input type="hidden"  id="parentcode" name="parentcode" value="<?=$p_row['to_location']?>" >
			  <input type="hidden"  id="pay_date" name="pay_date" value="<?=$p_row['payment_date']?>" >
               <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='payment_approval_list.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>
         
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>

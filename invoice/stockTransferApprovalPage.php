<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Update'){
		/// transcation parameter /////////////////////	
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
		$decodepono = base64_decode($refno);
	  	$decodepodt = base64_decode($refdate);
	  	$fromloction = base64_decode($fromloc);
	  	$tolocation = base64_decode($toloc);
		
		////// start saving doc attach if any 
		$allowed = array('pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'txt', 'xlsx', 'xls');
		if($_FILES['doc_attachment']['name']){
		  $folder="stn_doc";
		  $file_name = $_FILES['doc_attachment']['name'];
		  $ext1 = pathinfo($file_name, PATHINFO_EXTENSION);
		  if (!in_array($ext1, $allowed)) {
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. ".$ext1." file extension is not allowed in document attachment";
			header("location:stockTRansferApproval.php?msg=".$msg."".$pagenav);
			exit;
		  }else{
			$file_tmp = $_FILES['doc_attachment']['tmp_name'];
			if (!is_dir("../".$folder)) {
					mkdir("../".$folder, 0777, 'R');
			}
			$path1 = "../".$folder."/".time().$file_name;
			$up = move_uploaded_file($file_tmp,$path1);
		  }
		}
		
	  	if($_POST['actiontaken'] == 'Approved'){
	   		///// first check stock is availbale in from LOCATION , IF YES THEN DEDUCT STOCK ////////////////////////////////////////////////////////////////
	   		$data = mysqli_query($link1 , "select prod_code , from_location , qty , price from billing_model_data where challan_no = '".$decodepono."' ");
	   		while($row = mysqli_fetch_array($data)){
	    		$qtycheck = mysqli_query($link1 , "select okqty from stock_status where partcode = '".$row['prod_code']."' and asc_code = '".$row['from_location']."'  and okqty >0");
				if(mysqli_num_rows($qtycheck)>0) {
	    			$check = mysqli_query($link1, " update stock_status set hold_okqty=hold_okqty-'".$row['qty']."', okqty = okqty-'".$row['qty']."' where partcode = '".$row['prod_code']."' and asc_code = '".$row['from_location']."' ");	      
             		//// check if query is not executed
					if (!$check) {
					   $flag = false;
					   $err_msg = "Error Code4:";
					}	   
	   				/////// entry in stock ledegr
	   				$flag=stockLedger($decodepono,$today,$row['prod_code'], $fromloction,$tolocation,$tolocation,"OUT","OK","Stock Transfer",$row['qty'],$row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
				}          
				else {
	      			$msg="Stock is not Available to transfer";
	  				 ///// move to parent page
            		header("Location:stockTRansferApproval.php?msg=".$msg."".$pagenav);
            		exit;
				}
	  			//// update status in master table //////////////////////////////////////////////////////////////
	  			$res = mysqli_query($link1,"UPDATE billing_master set status='Pending' , approved_by='".$_SESSION["userid"]."', approved_rmrk = '".$remark."' , approved_status = '".$actiontaken."',approved_date='".$today."',attachment='".$path1."'  where challan_no ='".$decodepono."'");
				if (!$res) {
					 $flag = false;
					 $err_msg = "Error details: " . mysqli_error($link1) . ".";
				} 
			}
	  		////// insert in approval table////
	  		$query="INSERT INTO approval_activities set ref_no='".$decodepono."',ref_date='".$decodepodt."',req_type='STN',action_by='".$_SESSION['userid']."',action_taken='".$actiontaken."',action_date='".$today."',action_time='".$currtime."',action_remark='".$remark."',action_ip='".$ip."'";
			$result=mysqli_query($link1,$query);
			//// check if query is not executed
			if (!$result) {
				 $flag = false;
				 $err_msg =  "Error details: " . mysqli_error($link1) . ".";
			}
	 		////// return message
	 		$msg="You have successfully taken approval action for STN ".$loccode;
		}
  		else if($_POST['actiontaken'] == 'Rejected') {
  			$data = mysqli_query($link1 , "select prod_code , from_location , qty , price from billing_model_data where challan_no = '".$decodepono."' ");
	   		while($row = mysqli_fetch_array($data)){
 				//////  release stock from hold qty and add stock in okqty of to location /////////////////////////////////
	       		$query="UPDATE stock_status set hold_okqty=hold_okqty-'".$row['qty']."'  where asc_code='".$fromloction."' and partcode='".$row['prod_code']."'";
	       		$result=mysqli_query($link1,$query);
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 $err_msg = "Error details: " . mysqli_error($link1) . ".";
				} 
			}
   			//// update status in master table //////////////////////////////////////////////////////////////
	   		$res = mysqli_query($link1,"UPDATE billing_master set status='Cancelled', cancel_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_rmk='".$remark."', cancel_step='Approval', cancel_ip='".$ip."', approved_status = '".$actiontaken."',attachment='".$path1."' where challan_no ='".$decodepono."'");
			if (!$res) {
				 $flag = false;
				 $err_msg = "Error details: " . mysqli_error($link1) . ".";
			} 
	    	////// insert in approval table////
	   		$query="INSERT INTO approval_activities set ref_no='".$decodepono."',ref_date='".$decodepodt."',req_type='STN',action_by='".$_SESSION['userid']."',action_taken='".$actiontaken."',action_date='".$today."',action_time='".$currtime."',action_remark='".$remark."',action_ip='".$ip."'";
			$result=mysqli_query($link1,$query);
			//// check if query is not executed
			if (!$result) {
				 $flag = false;
				 $err_msg = "Error details: " . mysqli_error($link1) . ".";
			}
  			$msg="You have successfully taken approval action for STN ".$loccode;	   
		}
  		else{
	 		////// return message
	 		$msg="Something went wrong. Please try again.";
		}
    	////// insert in activity table////
	 	$flag = dailyActivity($_SESSION['userid'],$decodepono,"Stock Transfer APPROVAL","APPROVAL",$ip,$link1,$flag);
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			//$msg = "STN is successfully placed with ref. no.".$invno;
		} else {
			mysqli_rollback($link1);
			//$msg = "Request could not be processed. Please try again.";
		} 
		mysqli_close($link1);
  		///// move to parent page
   		header("Location:stockTRansferApproval.php?msg=".$msg."".$pagenav);
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
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-reply"></i> Stock Transfer Approval</h2><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                
                <td width="20%"><label class="control-label">Stock Transfer From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Stock Transfer To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
             
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
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="6%">Req. Qty</th>
                <th style="text-align:center" width="6%">Price</th>
                <th style="text-align:center" width="6%">Value</th>
                <th style="text-align:center" width="4%">Discount</th>
				<th style="text-align:center" width="6%">Value After Discount</th>
				<th style="text-align:center" width="6%">Sgst Per(%)</th>
				<th style="text-align:center" width="6%">Sgst Amt</th>
				<th style="text-align:center" width="6%">Cgst Per(%)</th>
				<th style="text-align:center" width="6%">Cgst Amt</th>
				<th style="text-align:center" width="6%">Igst Per(%)</th>
				<th style="text-align:center" width="6%">Igst Amt</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
           <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2];?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
				<td style="text-align:right"><?=$podata_row['disc_amt']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
				<td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
				<td style="text-align:right"><?=$podata_row['cgst_per']?></td>
				<td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
				<td style="text-align:right"><?=$podata_row['igst_per']?></td>
				<td style="text-align:right"><?=$podata_row['igst_amt']?></td>
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
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['basic_cost'];?></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo $po_row['discount_amt'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo ($po_row['total_cost']);?></td>
               <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
               
            </tbody>
          </table>

      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
  <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Approval Action</div>
      <div class="panel-body">
        <?php if($po_row['status']=="PFA"){ ?>
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
                <td width="50%"><label class="control-label">Attachment</label></td>
                <td width="50%">
                 <input type="file" class="form-control" name="doc_attachment" id="doc_attachment" accept=".xlsx,.xls,image/*,.doc, .docx,.txt,.pdf"/>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['challan_no'])?>"/>
                  <input name="refdate" id="refdate" type="hidden" value="<?=base64_encode($po_row['sale_date'])?>"/>
				  <input name="fromloc" id="fromloc" type="hidden" value="<?=base64_encode($po_row['from_location'])?>"/>
                  <input name="toloc" id="toloc" type="hidden" value="<?=base64_encode($po_row['to_location'])?>"/>
				  
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='stockTRansferApproval.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
          <?php }else{ ?>
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="20%">Action Date & Time</th>
                <th width="30%">Action Taken By</th>
                <th width="20%">Action</th>
                <th width="30%">Action Remark</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$res_poapp=mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$po_row['challan_no']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                <td><?php echo $row_poapp['action_taken']?></td>
                <td><?php echo $row_poapp['action_remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='stockTRansferApproval.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
          <?php }}?>
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
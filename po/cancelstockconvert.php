<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM stockconvert_master where doc_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Cancel'){
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
	$messageIdent = md5($_POST['Submit'] . $docid);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['msgCnlSTC'])?$_SESSION['msgCnlSTC']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['msgCnlSTC'] = $messageIdent;
		////// start transaction
    	mysqli_autocommit($link1, false);
    	$flag = true;
    	$err_msg ="";
		
		$stk_convtype = explode(" to ",$po_row["stock_type"]);
		if($stk_convtype[0] == 'okqty'){
		 $typestock = "OK";
		}else if ($stk_convtype[0] == 'missing'){
			$typestock = "MISSING";
		}else if($stk_convtype[0] == 'damage'){
			$typestock = "DAMAGE";
		}else {
			
		}
		/////
		if($stk_convtype[1] == 'okqty'){
			 $contypestock = "OK";
		}else if ($stk_convtype[1] == 'missing'){
			$contypestock = "MISSING";
		}else if($stk_convtype[1] == 'damage'){
			$contypestock = "DAMAGE";
		}else {
			
		}
    	///// cancel corporate invoice ///////////
   		$query1 = "UPDATE stockconvert_master SET status='Cancelled', cancel_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_rmk='".$remark."', cancel_ip='".$ip."' WHERE doc_no='".$docid."'";
		$result1 = mysqli_query($link1,$query1);
		//// check if query is not executed
		if (!$result1) {
		   $flag = false;
		   $err_msg = "Error Code1:" . mysqli_error($link1) . ".";
		}
		$vpo_sql="SELECT * FROM stockconvert_data WHERE doc_no='".$docid."'";
		$vpo_res=mysqli_query($link1,$vpo_sql);
		while($vpo_row=mysqli_fetch_assoc($vpo_res)){
        	if($vpo_row["prod_cat"]!="C"){
       			//// update stock of from loaction
       			$result2 = mysqli_query($link1, "UPDATE stock_status SET ".$vpo_row["stock_type"]."=".$vpo_row["stock_type"]."+'".$vpo_row['qty']."', updatedate='".$datetime."' WHERE asc_code='".$po_row['location_code']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$vpo_row['prod_code']."'");
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$err_msg = "Error Code2:" . mysqli_error($link1) . ".";
				}  
				$flag = stockLedger($docid,$today,$vpo_row['prod_code'],$po_row['location_code'],$po_row['sub_location'],$po_row['sub_location'],"IN",$typestock,"Cancel Stock Convert",$vpo_row['qty'],$vpo_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				///check converted part stock
				$selstok = mysqli_fetch_array(mysqli_query($link1,"SELECT ".$vpo_row["convertstocktype"]." FROM stock_status WHERE partcode = '".$vpo_row['to_prod_code']."' AND asc_code='".$po_row["location_code"]."' AND sub_location='".$po_row["sub_location"]."'"));
				//// check stock should be available ////
				if ($selstok[0] < $vpo_row['qty']) {
					$flag = false;
					$err_msg = "Error Code0.1: Stock is not available for ".$vpo_row['to_prod_code'];
				} else {
					
				}
	   			$result3 = mysqli_query($link1, "UPDATE stock_status SET ".$vpo_row["convertstocktype"]."=".$vpo_row["convertstocktype"]."-'".$vpo_row['qty']."', updatedate='".$datetime."' WHERE asc_code='".$po_row['location_code']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$vpo_row['to_prod_code']."'");
				//// check if query is not executed
				if (!$result3) {
					$flag = false;
					$err_msg = "Error Code3:" . mysqli_error($link1) . ".";
				}
      			$flag=stockLedger($docid,$today,$vpo_row['to_prod_code'],$po_row['sub_location'],$po_row['location_code'],$po_row['sub_location'],"OUT",$contypestock,"Cancel Stock Convert",$vpo_row['qty'],$vpo_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
       		}
			/////// check if imei is attached then it should also cancelled or reverse to the from location
			if($vpo_row['serial_attach']=="Y"){
				///////////   check if stock is available /////////////////////////////////////////////////// 			
				$billing_data = mysqli_query ($link1 ,"SELECT * FROM billing_imei_data WHERE doc_no = '".$docid."' AND prod_code = '".$vpo_row['to_prod_code']."'");
				if(mysqli_num_rows($billing_data) > 0) {
					while ($row = mysqli_fetch_array($billing_data)){		
						//////   insert deleted imeis into new table///////////////////////////////////////////////////
						$result3 = mysqli_query($link1,"INSERT INTO cancel_imei_data SET from_location = '".$row['from_location']."' , to_location = '".$row['to_location']."' , owner_code = '".$row['owner_code']."' , prod_code = '".$row['prod_code']."' , doc_no = '".$row['doc_no']."' , imei1 = '".$row['imei1']."' , imei2 = '".$row['imei2']."' , flag = '".$row['flag']."'  , stock_type = '".$row['stock_type']."' ");	
						//// check if query is not executed
						if (!$result3) {
							$flag = false;
							$err_msg = "Error Code31:" . mysqli_error($link1) . ".";
						}	
					}
					///////////   delete entry from billing imei data /////////////////////////////////////////////////////////
					$result4 = mysqli_query($link1, "DELETE FROM billing_imei_data WHERE doc_no = '".$docid."' AND prod_code = '".$vpo_row['to_prod_code']."'");
					//// check if query is not executed////////
					if (!$result4) {
						$flag = false;
						$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
					}
				}
			}
    	}/// close for loop  
  		////// insert in activity table////
  		$flag=dailyActivity($_SESSION['userid'],$docid,"Stock Convert","CANCEL",$ip,$link1,$flag);
		///// check  master  query are successfully executed
		if ($flag) {
		  mysqli_commit($link1);
		  $msg = "Stock conversion is cancelled successfully with ref. no." .$docid." Please check stock." ;
		} else {
		  mysqli_rollback($link1);
		  $msg = "Request could not be processed ".$err_msg.". Please try again.";
		} 
  	}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}	
  	mysqli_close($link1);
	///// move to parent page
	header("Location:stockconvert_list.php?msg=".$msg."".$pagenav);
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
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Cancel Stock Convert </h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" onSubmit="return myConfirm();">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Stock Convert From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['location_code'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Document No.</label></td>
                <td width="30%"><?php echo $po_row['doc_no']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr> 
              <tr>
                <td><label class="control-label">Cost Centre(Godown)</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                <td><label class="control-label">&nbsp;</label></td>
                <td>&nbsp;</td>
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
              <tr>
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">From Product Code</th>
			   <th style="text-align:center" width="15%">Stock Type</th>
                <th style="text-align:center" width="15%">Qty</th>
                <th style="text-align:center" width="15%">Convert Into</th>
                <th style="text-align:center" width="15%">Convert Stock Type</th>
                <th style="text-align:center" width="15%">Entry Date & Time</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM stockconvert_data where doc_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode,model_name",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2]."|".$d[3];?></td>
				<td style="text-align:left"><?php if($podata_row['stock_type'] == 'okqty'){ echo "OK";} else if($podata_row['stock_type'] == 'broken') { echo "Damage";} else if($podata_row['stock_type'] == 'missing') { echo "Missing";} else {}?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:left"><?php $todata = getProductDetails($podata_row['to_prod_code'],"productname,productcolor,productcode,model_name",$link1); $tod = explode('~', $todata); echo $tod[0].' | '.$tod[1].' | '.$tod[2]."|".$tod[3];?></td>
                <td style="text-align:left"><?php if($podata_row['convertstocktype'] == 'okqty'){ echo "OK";} else if($podata_row['convertstocktype'] == 'broken') { echo "Damage";} else if($podata_row['convertstocktype'] == 'missing') { echo "Missing";}else {}?></td>
                <td style="text-align:right"><?=$podata_row['entry_time']?></td>
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
      <div class="panel-heading heading1"> Action </div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>

              <tr>
                <td colspan="2" align="center">
                  
                <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
                
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stockconvert_list.php?<?=$pagenav?>'">               
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div>
     </form>
    <!--close panel-->
  </div><!--close panel group-->
  
  <br><br>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
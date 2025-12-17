<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$from=base64_decode($_REQUEST['from']);
$to=base64_decode($_REQUEST['to']);
$saledate=base64_decode($_REQUEST['sdate']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."' and from_location='".$to."' and to_location='".$from."' and type='PURCHASE RETURN'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
@extract($_POST);
if($_POST['save']=='Update'){
	if($_POST['actiontaken']=="Approved"){
		///// update po status ///////////
		mysqli_query($link1,"UPDATE billing_master SET status = 'Pending', approved_status = '".$actiontaken."', approved_by = '".$_SESSION['userid']."', approved_rmrk = '".$remark."', approved_date = '".$today."' WHERE challan_no = '".$docid."'")or die("ER1".mysqli_error($link1));
		////// return message
		$msg="You have successfully taken approval action for challan no. ".$docid; 
	}else if($_POST['actiontaken']=="Rejected"){
		///// update po status ///////////
		mysqli_query($link1,"UPDATE billing_master set status = 'Rejected', approved_status = '".$actiontaken."', approved_by = '".$_SESSION['userid']."', approved_rmrk = '".$remark."', approved_date = '".$today."' where challan_no = '".$docid."'")or die("ER1".mysqli_error($link1));
		
		///// Insert in billing data by picking each data row one by one
		$sql7 = "SELECT * FROM billing_model_data where challan_no='".$docid."' and from_location='".$to."'";
		$res7=mysqli_query($link1,$sql7);
		while($rw = mysqli_fetch_assoc($res7)){   
			
			//// update stock of from loaction
		    $result=mysqli_query($link1, "update stock_status set okqty=okqty+'".$rw['qty']."',updatedate='".$datetime."' where asc_code='".$to."' and partcode='".$rw['prod_code']."' AND sub_location='".$po_row["sub_location"]."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code5:";
           }
		   ///// update stock ledger table
		   $flag=stockLedger($docid,$today,$rw['prod_code'],$po_row["sub_location"],$from,$po_row["sub_location"],"IN","OK","PURCHASE RETURN",$rw['qty'],$rw['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,"");
		}		
		//// update cr bal of child location
  		$result=mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl-'".$gtot."',total_cr_limit=total_cr_limit-'".$gtot."', last_updated='".$datetime."' where parent_code='".$from."' and asc_code='".$to."'");
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error Code8:";
		}

	////// maintain party ledger////
	$flag=partyLedger($from,$to,$docid,$today,$today,$currtime,$_SESSION['userid'],"PURCHASE RETURN",$gtot,"DR",$link1,$flag);
		
	}else{
	
	}
	
	 ////// insert in approval table////
	 approvalActivity($docid,$saledate,"PURCHASE RETURN",$_SESSION['userid'],$actiontaken,$today,$currtime,$remark,$ip,$link1,"");
	 ////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$docid,"PRN APPROVAL","APPROVAL",$ip,$link1,"");
	 
	 ///// move to parent page
	 header("Location:return_approval_list.php?msg=".$msg."".$pagenav);
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
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<form id="frm1" name="frm1" method="post">
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-address-card fa-lg"></i> Return Approval View </h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1 ">Party Information</div>
		  <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php } ?>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Return To:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Purchase Return From:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.:</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Purchase Return Date:</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
			    <tr>
                <td><label class="control-label">Reference Invoice No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Reference Invoice Date</label></td>
                <td><?php echo $po_row['ref_date'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Entry By:</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
             
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 ">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="15%">Return Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount</th>
                <th style="text-align:center" width="6%" >SGST(%)</th>
                <th style="text-align:center" width="6%" >SGST Amt</th>
                <th style="text-align:center" width="6%" >CGST(%)</th>
                <th style="text-align:center" width="6%" >CGST Amt</th>
                <th style="text-align:center" width="6%" >IGST(%)</th>
                <th style="text-align:center" width="6%" >IGST Amt</th>
                <th style="text-align:center" width="15%" >Total</th>
				
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."' and from_location='".$to."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td>
					<?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?>
			    </td>
                <td style="text-align:right">
					<?=$podata_row['qty']?>
				</td>
                <td style="text-align:right">
					<?=$podata_row['price']?>
				</td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right">
					<?=$podata_row['totalvalue']?>
				</td>
                
              </tr>
            <?php
			 $tot_val+=$podata_row['value'];
			 $tot_dics+= $podata_row['discount'];
			$tot_qty+=$podata_row['qty'];
			$i++;
			
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 ">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                 <td><label class="control-label">Total Discount</label></td>
                <td><?php echo $tot_dics;?></td>
                <td width="20%"><label class="control-label">Total Qty:</label></td>
                <td width="30%"><?php echo $tot_qty;?></td>
              </tr>
              <tr>
              <td><label class="control-label">Total Value</label></td>
                <td><?php echo $tot_val;?></td>
                 <td width="20%"><label class="control-label">Delivery Address:</label></td>
                <td width="30%"><?php echo $po_row['deliv_addrs'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Grand Total</label></td>
                <td>
					<?php echo $po_row['total_cost'];?>
					<input type="hidden" name="gtot" id="gtot" value="<?=$po_row['total_cost']?>" />
				</td>
                <td><label class="control-label">Remark:</label></td>
                <td><?php echo $po_row['billing_rmk'];?></td>
              </tr>
			 
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 ">Approval Action</div>
      <div class="panel-body">
		  
			<table class="table table-bordered" width="100%">
				<tbody>
				 <tr>
					<td width="50%"><label class="control-label">Action Taken <span class="red_small">*</span></label></td>
					<td width="50%">
					 <select name="actiontaken" id="actiontaken" class="required form-control" required style="width:300px;">
					  <option value="" <?php if($po_row['approved_status'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
					  <option value="Approved" <?php if($po_row['approved_status'] == "Approved"){ echo "selected"; } ?> >Approved</option>
					  <option value="Rejected" <?php if($po_row['approved_status'] == "Rejected"){ echo "selected"; } ?> >Rejected</option>
					</select>
					</td>
				  </tr>
				  <tr>
					<td><label class="control-label">Remark </label></td>
					<td><textarea name="remark" id="remark" class="form-control" style="width:300px;" ><?php echo $po_row['approved_rmrk']; ?></textarea></td>
				  </tr>
				  <tr>
					<td colspan="2" style="text-align:center;">
						<?php if($po_row['status']=="Pending For Approval"){ ?>
						<input type="submit" class="btn <?=$btncolor?>" name="save" id="save" value="Update" title="" <?php if($_POST['save']=='Update'){?>disabled<?php }?>>&nbsp;
						<?php } ?>
						<input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='return_approval_list.php?<?=$pagenav?>'">
					</td>
				  </tr>
				</tbody>
			  </table>
			
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
  </div><!--close panel group-->
  <br><br>
  
 </div><!--close col-sm-9-->
 </form>
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
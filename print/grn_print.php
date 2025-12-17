<?php
////// Function ID ///////
$fun_id = array("u"=>array(109)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid=base64_decode($_REQUEST['id']);
//// po details from master table
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "' ";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GRN Print</title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#barcodeprint").barcode(
		"<?=$po_row['challan_no']?>", // Value barcode (dependent on the type of barcode)
		"code128" // type (string)
/* Types
codabar
code11 (code 11)
code39 (code 39)
code93 (code 93)
code128 (code 128)
ean8 (ean 8)
ean13 (ean 13)
std25 (standard 2 of 5 - industrial 2 of 5)
int25 (interleaved 2 of 5)
msi
datamatrix (ASCII + extended)
*/
/* Setting
barWidth: 1,
barHeight: 50,
moduleSize: 5,
showHRI: true,
addQuietZone: true,
marginHRI: 5,
bgColor: "#FFFFFF",
color: "#000000",
fontSize: 10,
output: "css",
posX: 0,
posY: 0
*/
	);
});
</script>
</head>

<body>
<!--	<page size="A4" layout="portrait"></page>-->
	<page size="A4">
		<table class="table" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="20%"><img src="../img/inner_logo.png"/></td>
                <td width="30%" align="center"><div id="barcodeprint"></div></td>
                <td width="50%">
                	             
                </td>
              </tr>
            </tbody>
    	</table>
        <div align="center" class="lable"><u><strong>GRN</strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>GRN No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['challan_no']?></td>
                <td width="15%" colspan="2"><strong>GRN Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['entry_date'])?></td>
              </tr>
	     <tr>
                <td width="15%" colspan="2"><strong>VPO No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['ref_no']?></td>
                <td width="15%" colspan="2"><strong>Invoice No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['inv_ref_no']?></td>
              </tr>
				<tr>
                <td width="15%" colspan="2"><strong>Status</strong></td>
                <td width="35%" colspan="2"><?=$po_row['status']?></td>
                <td width="15%" colspan="2"><strong>Invoice Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['po_inv_date'])?></td>
              </tr>
                 
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>From Location</strong></td>
                <td colspan="2"><?php $fromloc = explode("~",getVendorDetails($po_row['from_location'],"name,city,state",$link1)); echo implode(",",$fromloc)." (".$po_row['to_location'].")";?></td>
                <td colspan="2"><strong>To Location </strong></td>
                <td colspan="2"><?php $toloc = explode("~",getLocationDetails($po_row['to_location'],"name,city,state",$link1)); echo implode(",",$toloc)." (".$po_row['from_location'].")";?></td>
				                
              </tr>
              <tr>
			  	<td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['from_location'],"address",$link1);?></td>
              	<td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['to_location'],"addrs",$link1);?></td>
                				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['from_location'],"state",$link1);?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['to_location'],"state",$link1);?></td>
                
              </tr>
              <tr>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['from_location'],"gstin_no",$link1);?></td>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['to_location'],"gstin_no",$link1);?></td>
                
              </tr>
             
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
	    </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="3%">#</td>
              <td width="17%"><strong>Description Of Goods</strong></td>
              <td width="10%"><strong>Product Code</strong></td>
              <td width="10%"><strong>Qty</strong></td>
              <td width="10%"><strong>Price</strong></td>
			  <td width="10%"><strong>Value</strong></td>
               <?php if($toloc[2] == $fromloc[2]) {?>
			  <td width="5%"><strong>SGST %</strong></td>
			  <td width="5%"><strong>SGST Amount</strong></td>
			  <td width="5%"><strong>CGST %</strong></td>
			  <td width="5%"><strong>CGST Amount</strong></td>
              <?php }else{?>
			  <td width="10%"><strong>IGST %</strong></td>
			  <td width="10%"><strong>IGST Amount</strong></td>
              <?php }?>
			  <td width="10%"><strong>Total Amount</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			 $podata_sql="SELECT * FROM billing_model_data where challan_no = '".$po_row['challan_no']."'  and qty >0.00  ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=getProductDetails($podata_row['prod_code'],"productname",$link1);?></td>
                <td><?=$podata_row['prod_code'];?></td>
                <td align="right"><?=$podata_row['qty']?></td>
                <td align="right"><?=currencyFormat($podata_row['price'])?></td>
                <td align="right"><?=currencyFormat($podata_row['value'])?></td>    
                <?php if($toloc[2] == $fromloc[2]) {?>
				<td align="right"><?=$podata_row['sgst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['sgst_amt'])?></td>
				<td align="right"><?=$podata_row['cgst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['cgst_amt'])?></td>
                <?php }else{?>
				<td align="right"><?=$podata_row['igst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['igst_amt'])?></td>
                <?php }?>
				<td align="right"><?=currencyFormat($podata_row['totalvalue'])?></td>       
                </tr>
            <?php
			$total+=$podata_row['qty'];
			$price+=$podata_row['price'];
			$totalval+=$podata_row['totalvalue'];                                                
			$value+=$podata_row['value']; 
                        $totaltax+= ($podata_row['igst_amt'] + $podata_row['sgst_amt'] + $podata_row['cgst_amt']);
			$i++;
			}
			if($toloc[2] == $fromloc[2]) { $colspn=10; }else{ $colspn=8;}
			?>   
            	<tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total Taxable Value</strong></td>
                    <td align="right"><?php echo currencyFormat($value); ?></td>
                </tr>
                   <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Tax Amount</strong></td>
                    <td align="right"><?php echo currencyFormat($totaltax); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Round Off</strong></td>
                    <td align="right"><?php echo currencyFormat($po_row['round_off']); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Gross Total</strong></td>
                    <td align="right"><?php echo currencyFormat($totalval); ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($po_row['total_cost']) . " Only"; ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['receive_remark']?></td>
                </tr>
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Authorize signature)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>              
          </tbody>
   	  </table>
    </page>
</body>
</html>
<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
//// po details from master table
$po_sql = "SELECT * FROM vendor_order_master where po_no='" . $docid . "' ";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Purchase Order Print</title>
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
        <div align="center" class="lable"><u><strong>Purchase Order</strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>PO No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['po_no']?></td>
                <td width="15%" colspan="2"><strong>PO Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['entry_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>PO From </strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['po_from'],"name",$link1)." (".$po_row['po_from'].")";?></td>
				<td colspan="2"><strong>PO To</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['po_to'],"name",$link1)." (".$po_row['po_to'].")";?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['po_from'],"addrs",$link1);?></td>
                <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['po_to'],"address",$link1);?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['po_from'],"state",$link1);?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['po_to'],"state",$link1);?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['po_from'],"gstin_no",$link1);?></td>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=getVendorDetails($po_row['po_to'],"gstin_no",$link1);?></td>
              </tr>
               <tr>
                <td colspan="2"><strong>Payment Terms</strong></td>
                <td colspan="2"><?=$po_row['payment_status'];?></td>
                <td colspan="2"><strong>Delivery Address</strong></td>
                <td colspan="2"><?=$po_row['delivery_address'];?></td>
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
              <td width="10%"><strong>Model Name</strong></td>
              <td width="6%"><strong>Qty</strong></td>
			  <td width="10%"><strong>HSN</strong></td>
              <td width="10%"><strong>Purchase Price</strong></td>
			  <td width="10%"><strong>Value</strong></td>
               <?php if($po_row['total_igst_amt'] == '0.00') {?>
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
			 $podata_sql="SELECT * FROM vendor_order_data where po_no = '".$po_row['po_no']."' ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,model_name,productcode,hsn_code",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." | ".$proddet[2]?></td>
                <td><?=$proddet[1]?></td>
                <td align="right"><?=$podata_row['req_qty']?></td>
				<td><?=$proddet[3]?></td>
                <td><?=currencyFormat($podata_row['po_price'])?></td>
                <td align="right"><?=currencyFormat($podata_row['po_value'])?></td>    
                <?php if($po_row['total_igst_amt'] == '0.00') {?>
				<td><?=$podata_row['sgst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['sgst_amt'])?></td>
				<td><?=$podata_row['cgst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['cgst_amt'])?></td>
                <?php }else{?>
				<td><?=$podata_row['igst_per']?></td>
				<td align="right"><?=currencyFormat($podata_row['igst_amt'])?></td>
                <?php }?>
				<td align="right"><?=currencyFormat($podata_row['totalval'])?></td>       
                </tr>
            <?php
			$total+=$podata_row['req_qty'];
			$price+=$podata_row['po_price'];
			$totalval+=$podata_row['totalval'];                                                
			$value+=$podata_row['po_value'];
			$i++;
			}
			$grand_total=$totalval; 
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "+".$ro;
				}else{
					$roundoff = "-".$checkval;
				}
			}else{
				$roundoff = 0.00;
			}
			if($po_row['total_igst_amt'] == '0.00'){ $colspn=11; }else{ $colspn=9;}
			?>   
            	<tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Sub Total</strong></td>
                    <td align="right"><?php echo currencyFormat($value); ?></td>
                </tr>
                 <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total Tax</strong></td>
                    <td align="right"><?php echo currencyFormat($po_row['total_cgst_amt']+$po_row['total_sgst_amt']+$po_row['total_igst_amt']); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total</strong></td>
                    <td align="right"><?php echo currencyFormat($totalval); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Round Off</strong></td>
                    <td align="right"><?php echo currencyFormat($roundoff); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total Amount</strong></td>
                    <td align="right"><?php echo currencyFormat($totalval+$roundoff); ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($totalval) . " Only"; ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['remark']?></td>
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
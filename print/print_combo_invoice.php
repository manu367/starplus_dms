<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$loc = explode('~', getLocationDetails($po_row['from_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode,pincode", $link1));
if ($po_row['type'] == "RETAIL") {
    $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,category,category,category,customername", $link1));
    if ($to[0] == "") {
        $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
    }
} else {
    $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
}
if($po_row['document_type'] == "Delivery Challan") {
$header = "Delivery Challan";
} else {
$header = "TAX INVOICE";
}
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Print Document</title>
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
</head>
<body>
<!--	<page size="A4" layout="portrait"></page>-->
	<page size="A4">
		<table class="table" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="35%">&nbsp;</td>
                <td width="30%" align="center"><?=$header?></td>
                <td width="35%">&nbsp;</td>
              </tr>
            </tbody>
    	</table>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" rowspan="3"><img src="../img/inner_logo.png"/></td>
                <td width="35%" rowspan="3"><strong><?=$loc[7]?></strong><br/><?=$loc[1]?><br/><strong>GST No.: </strong><span class="lable"><?=$loc[5]?></span><BR><strong>State: </strong><span class="lable"><?=$loc[3]?></span><BR><strong>State Code: </strong><span class="lable"><?=$loc[8]?></span><BR><strong>PAN No.: </strong><span class="lable"><?=$loc[4]?></span></td>
                <td width="25%"><span role="presentation" dir="ltr">Invoice No.</span><div style="position:absolute"><strong><?php echo $po_row['challan_no']; ?></strong></div></td>
                <td width="25%"><span role="presentation" dir="ltr">Dated</span><div style="position:absolute"><strong><?php echo dt_format($po_row['entry_date']); if($po_row['status']=="Cancelled"){ echo "&nbsp;&nbsp;(".$po_row['status'].")";}?></strong></div></td>
              </tr>
              <tr>
                <td><span role="presentation" dir="ltr">Delivery Note</span><div style="position:absolute">&nbsp;</div></td>
                <td><span role="presentation" dir="ltr">Mode/Terms of Payment</span><div style="position:absolute">&nbsp;</div></td>
              </tr>
              <tr>
                <td><span role="presentation" dir="ltr">Supplier's Ref.</span><div style="position:absolute">&nbsp;</div></td>
                <td><span role="presentation" dir="ltr">Other Reference(s)</span><div style="position:absolute">&nbsp;</div></td>
              </tr>
              <tr>
                <td colspan="2" rowspan="3"><span role="presentation" dir="ltr"><strong>Buyer</strong>:</span><br/><strong><?=$to[7]?></strong><br/><span class="lable"><?=$to[1];?></span><BR><strong>GST No.: </strong><span class="lable"><?=$to[5]?></span><BR><strong>State: </strong><span class="lable"><?=$to[3]?></span><BR><strong>State Code: </strong><span class="lable"><?=$to[8]?></span><BR><strong>PAN No.: </strong><span class="lable"><?=$to[4]?></span></td>
                <td><span role="presentation" dir="ltr">Buyer's Order No.</span><div style="position:absolute">&nbsp;</div></td>
                <td><span role="presentation" dir="ltr">Dated</span><div style="position:absolute">&nbsp;</div></td>
              </tr>
              <tr>
                <td><span role="presentation" dir="ltr">Dispatch Document No.</span><div style="position:absolute"><?=$po_row['docket_no']?></div></td>
                <td><span role="presentation" dir="ltr">Delivery Note Date</span><div style="position:absolute"><?=dt_format($po_row['dc_date'])?></div></td>
              </tr>
              <tr>
                <td><span role="presentation" dir="ltr">Dispatched through</span><div style="position:absolute"><?=$po_row['trans_mode']?></div></td>
                <td><span role="presentation" dir="ltr">Destination</span><div style="position:absolute">&nbsp;</div></td>
              </tr>
              <tr>
                <td colspan="2" rowspan="3"><span role="presentation" dir="ltr"><strong>Consignee /Deliver at:</strong></span><br/><strong><?=$to[7]?></strong><br/><span class="lable"><?=$to[1];?></span><BR><strong>GST No.: </strong><span class="lable"><?=$to[5]?></span><BR><strong>State: </strong><span class="lable"><?=$to[3]?></span><BR><strong>State Code: </strong><span class="lable"><?=$to[8]?></span><BR><strong>PAN No.: </strong><span class="lable"><?=$to[4]?></span></td>
                <td colspan="2"><span role="presentation" dir="ltr">Terms of Delivery</span></td>
              </tr>
              <tr>
                <td colspan="2"><span role="presentation" dir="ltr">IRN Number:</span></td>
              </tr>
              <tr>
                <td colspan="2"><span role="presentation" dir="ltr">QR Code:</span></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
           	</tbody>
      </table>
        <table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="3%"><strong>S.No.</strong></td>
              <td width="27%"><strong>Description Of Goods</strong></td>
              <td width="10%"><strong>HSN/SAC</strong></td>
              <td width="7%"><strong>Qty</strong></td>
              <td width="10%"><strong>Price</strong></td>
			  <td width="10%"><strong>Value</strong></td>
               <?php if($loc[3]==$to[3]){ ?>
			  <td width="5%"><strong>SGST %</strong></td>
			  <td width="5%"><strong>SGST Amount</strong></td>
			  <td width="5%"><strong>CGST %</strong></td>
			  <td width="5%"><strong>CGST Amount</strong></td>
              <?php }else{?>
			  <td width="7%"><strong>IGST %</strong></td>
			  <td width="13%"><strong>IGST Amount</strong></td>
              <?php }?>
			  <td width="13%"><strong>Total Amount</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			 $podata_sql="SELECT * FROM billing_model_data where challan_no = '".$po_row['challan_no']."' AND prod_cat='C'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$comboprodlist_res = mysqli_query($link1,"SELECT GROUP_CONCAT(prod_code) AS pc, GROUP_CONCAT(qty) AS qt FROM billing_model_data WHERE combo_code='".$podata_row['combo_code']."' AND  prod_cat!='C'");
				$comboprodlist_row = mysqli_fetch_assoc($comboprodlist_res);
				$expl_prod = explode(",",$comboprodlist_row["pc"]);
				$expl_qty = explode(",",$comboprodlist_row["qt"]);
				$prd_str = "";
				for($k=0; $k<count($expl_prod); $k++){
					$product_name = explode("~", getProductDetails($expl_prod[$k], "productname,productcolor", $link1));
					if($prd_str){
						$prd_str .= ",<br/>".$product_name[0]." = ".round($expl_qty[$k]);
					}else{
						$prd_str = $product_name[0]." = ".round($expl_qty[$k]);
					}
				}
				$combo_hsn = getAnyDetails($podata_row['combo_code'],"bom_hsn","bom_modelcode","combo_master",$link1);
				//if($podata_row["prod_cat"] == "C"){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$podata_row['combo_name']." (".$podata_row['combo_code'].")<br/><i>".$prd_str."</i>";?></td>
                <td align="center"><?=$combo_hsn?></td>
                <td align="right"><?=round($podata_row['qty'])?></td>
                <td align="right"><?=currencyFormat($podata_row['price'])?></td>
                <td align="right"><?=currencyFormat($podata_row['value'])?></td>    
                <?php if($loc[3]==$to[3]){ ?>
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
			//}
			}
			if($loc[3]==$to[3]){ $colspn=10; }else{ $colspn=8;}
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
                <td colspan="8" align="left" style="border-bottom:none" height="50"><strong>Term and Condition</strong><br/>
                 <p>*Disputes,If any Shall be Subjected to Court of Haryana only.<br></p><br/>
                 <strong>Declaration</strong>
                 <p>
We declare that this invoice shows the actual price of the goods
described and that all particulars are true and correct.</p></td>
              </tr>
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Authorised Signatory)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>              
          </tbody>
   	  </table>
      <div align="center">This Invoice is electronically generated.</div>
    </page>
</body>
</html>
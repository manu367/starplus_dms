<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
//// po details from master table
$po_sql = "SELECT * FROM stockconvert_master where doc_no='" . $docid . "' ";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Print</title>
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
        <div align="center" class="lable"><u><strong>Stock Convert</strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>Document No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['doc_no']?></td>
                <td width="15%" colspan="2"><strong>Document Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['requested_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Stock Convert From </strong></td>
                <td colspan="2"><?=getLocationDetails($po_row['location_code'],"name",$link1)." (".$po_row['location_code'].")";?></td>
				<td colspan="2"><strong>Status</strong></td>
                <td colspan="2"><?=$po_row['status']?></td>                
              </tr>
              <tr>
                <td colspan="2"><strong>Convert Type</strong></td>
                <td colspan="2"><?=$po_row['stock_type']?></td>
                <td colspan="2">Cost Centre(Godown)</td>
                <td colspan="2"><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
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
              <td width="12%"><strong>From Product Code</strong></td>
			  <td width="10%"><strong>Stock Type</strong></td>
              <td width="6%"><strong>Qty</strong></td>
			  <td width="12%">Convert Into</td>
			  <td width="12%"><strong>Convert Stock Type</strong></td>
			  <td width="10%"><strong>Entry Date & Time</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			 $podata_sql="SELECT * FROM stockconvert_data where doc_no = '".$po_row['doc_no']."' ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=getProductDetails($podata_row['prod_code'],"productname",$link1)."(".$podata_row['prod_code'].")";?></td>   
				<td style="text-align:left"><?php if($podata_row['stock_type'] == 'okqty'){ echo "OK";} else if($podata_row['stock_type'] == 'broken') { echo "Damage";} else if($podata_row['stock_type'] == 'missing') { echo "Missing";} else {}?></td>             
                <td align="right"><?=$podata_row['qty']?></td>
				<td><?=getProductDetails($podata_row['to_prod_code'],"productname",$link1)."(".$podata_row['prod_code'].")";?></td>
				<td><?php if($podata_row['convertstocktype'] == 'okqty'){ echo "OK";} else if($podata_row['convertstocktype'] == 'broken') { echo "Damage";} else if($podata_row['convertstocktype'] == 'missing') { echo "Missing";}else {}?></td>  
                 <td align="right"><?=$podata_row['entry_time']?></td>   
                </tr>
            <?php
			$total+=$podata_row['okqty'];
			$i++;
			}
			
			?>   
            	<tr>
                	<td colspan="3" align="right"><strong>Total Qty</strong></td>
                    <td align="right"><?php echo currencyFormat($total); ?></td>
                </tr>
                <tr>
                  <td colspan="8"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['remark']?></td>
                </tr>
			 </tbody>
        </table>
   <table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="5" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="5" style="border-top:none" align="right">(Authorize signature)</td>
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
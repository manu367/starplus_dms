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
                <td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
	    </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <tbody>
            	<tr>
                	<td><FONT size=2>
			  <?php
			  $arr_modelws = array();
	          $imeiquery="Select * from billing_imei_data where doc_no='".$docid."' order by prod_code";
			  $imeiresult=mysqli_query($link1,$imeiquery);
			  while($imeirow=mysqli_fetch_array($imeiresult)){
			  	$arr_modelws[$imeirow['prod_code']][] = $imeirow['imei1'];
			  }
				  foreach($arr_modelws as $prodcode => $serialarr){
				  	$product_name = explode("~", getProductDetails($prodcode, "productname,productcolor", $link1));
					echo "<b>Model Name: </b>".$product_name[0]." / ".$prodcode."<br/>";
					echo "<b>Serial No.: </b>".implode(" , ",$serialarr);
					echo "<br/><br/>";
				  }
                 ?></FONT></td>
                </tr>
                <tr>
                  <td><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['remark']?></td>
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
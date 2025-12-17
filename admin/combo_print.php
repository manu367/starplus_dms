<?php
////// Function ID ///////
$fun_id = array("a"=>array(53));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//$arrstatus = getFullStatus("",$link1);
$docid=base64_decode($_REQUEST['id']);
//// BOM details from master table
$req_sql = "SELECT * FROM combo_master where bomid='".$docid."' group by bomid";
$req_res = mysqli_query($link1,$req_sql);
$req_row = mysqli_fetch_assoc($req_res);
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document Print</title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#barcodeprint").barcode(
		"<?=$req_row['bom_modelcode']?>", // Value barcode (dependent on the type of barcode)
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
                <td width="20%"><img src="../img/logo.png"/></td>
                <td width="80%" align="right"><div id="barcodeprint"></div></td>
              </tr>
            </tbody>
    	</table>
        <div align="center" class="lable"><u><strong>BOM Print</strong></u></div>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>BOM Model</strong></td>
                <td width="35%" colspan="2"><?=$req_row['bom_modelname']."  (".$req_row['bom_modelcode'].")"?></td>
                <td colspan="2"><strong>Status</strong></td>
                <td colspan="2"><?php if($req_row["status"] == 1){ echo $sts = "Active";}else{ echo $sts = "Deactive";}?></td>	
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-newspaper-o fa-lg"></i><strong style="font-size:14px">&nbsp;BOM Details</strong></td>
              </tr>
              <tr>
			  	<td colspan="2"><strong>Create By</strong></td>
                <td colspan="2"><?=getAnyDetails($req_row['createby'],"name","username","admin_users",$link1)?></td>
				<td width="15%" colspan="2"><strong>Create Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($req_row['createdate'])?></td>	
              </tr>
              <?php if($req_row['updateby']){?>
              <tr>
                <td colspan="2"><strong>Update By</strong></td>
                <td colspan="2"><?=getAnyDetails($req_row['updateby'],"name","username","admin_users",$link1);?></td>
                <td colspan="2"><strong>Update Date</strong></td>
                <td colspan="2"><?=dt_format($req_row['updatedate']);?></td>
              </tr>
              <?php }?>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-cubes fa-lg"></i><strong style="font-size:14px"> BOM Part Details</strong></td>
              </tr>
			  </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="5%">#</td>
              <td width="20%"><strong>Part Name</strong></td>
              <td style="width:15%; text-align:right;"><strong>BOM Qty</strong></td>
              <td width="15%"><strong> Unit</strong></td>		 
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			$tot_req_qty = 0 ;
			/////////////////////////// fetching data from combo_master table /////////////////////////////////////////////////////////////////////////
			$req_data_sql="SELECT bom_partcode,bom_qty,bom_unit,conversion_factor FROM combo_master where bomid='".$docid."'";
			$req_data_res=mysqli_query($link1,$req_data_sql);
			while($req_data_row=mysqli_fetch_assoc($req_data_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php echo getAnyDetails($req_data_row['bom_partcode'],"productname","productcode","product_master",$link1)." (".$req_data_row['bom_partcode'].")";?></td>
				<td align="right"><?=round($req_data_row['bom_qty'])?></td>
                <td><?=$req_data_row['bom_unit']?></td> 
              </tr>
			  <?php 
				  $tot_req_qty+=$req_data_row['bom_qty'];
			$i++;
			}
			?>  
			<tr>
				<td colspan="2" align="right"><strong style="font-size:12px; text-align:right;">Total</strong></td>
				<td align="right"><strong><?=$tot_req_qty;?></strong></td>
                <td>&nbsp;</td>
            </tr> 
			 </tbody>
        </table>
   <table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Authorized signature)</td>
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
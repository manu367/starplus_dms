<?php 
require_once("../includes/serial_logic_function.php");
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$location=json_decode(base64_decode($_REQUEST['loc']));
$product_cat=base64_decode($_REQUEST['product_cat']);
$product_subcat=base64_decode($_REQUEST['product_subcat']);
$brand=base64_decode($_REQUEST['brand']);
$partcode=base64_decode($_REQUEST['partcode']);
## selected location
/*if($location!=""){
	$imeiloc="a.owner_code='".$location."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$imeiloc="a.owner_code in (".$locstr.")";
}*/
if(is_array($location)){
	$statusstr="";
	$post_statusarr = $location;
	for($i=0; $i<count($post_statusarr); $i++){
		if($statusstr){
			$statusstr .= ",'".$post_statusarr[$i]."'";
		}else{
			$statusstr .= "'".$post_statusarr[$i]."'";
		}
	}
	$imeiloc="a.owner_code in (".$statusstr.")";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$imeiloc="a.owner_code in (".$locstr.")";
}
## selected Product Category
if($product_cat!=""){
	$pcat_s= " b.productcategory='".$product_cat."'";
}else{
	$pcat_s = " 1";
}
## selected Product Sub Category
if($product_subcat!=""){
	$pscat_s = " b.productsubcat='".$product_subcat."'";
}else{
	$pscat_s = " 1";
}
## selected brand
if($brand!=""){
	$brd_s = " b.brand='".$brand."'";
}else{
	$brd_s = " 1";
}
## selected product id
if($partcode!=""){
	$part_code = " a.prod_code='".$partcode."'";
}else{
	$part_code = " 1";
}
//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>City</strong></td>
<td><strong>State</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Model</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product Sub Category</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Serial No.</strong></td>
<td><strong>Stock Type</strong></td>
<td><strong>Qty</strong></td>
<td><strong>Price</strong></td>
<td><strong>Value</strong></td>
<td><strong>Date Of Manufacturing</strong></td>
<td><strong>Posting Date</strong></td>
<td><strong>Material Age</strong></td>
<td><strong>Aging</strong></td>
</tr>
<?php
$i=1;
$sql=mysqli_query($link1,"Select a.owner_code, a.prod_code, a.imei1, a.stock_type, a.import_date  from billing_imei_data a, product_master b WHERE a.prod_code=b.productcode AND ".$imeiloc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code)or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql)){
$qty = 0 ;
	$chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select owner_code,doc_no,prod_code from billing_imei_data where imei1='".$row_loc['imei1']."' order by id desc"));
	if($chek_owner["prod_code"]==$row_loc["prod_code"]){
	$chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
	  if($chek_rcvin['status']==""){
	  	$chek_postatus=mysqli_fetch_assoc(mysqli_query($link1,"select status from vendor_order_master where po_no='".$chek_owner['doc_no']."'"));
		if($chek_postatus['status']==""){
			$chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
			if($chek_rcvin2['status']==""){
				$chek_rcvin3=mysqli_fetch_assoc(mysqli_query($link1,"select status from stockconvert_master where doc_no='".$chek_owner['doc_no']."'"));
				if($chek_rcvin3['status']=="Processed"){
					$checkstatus = "Received";
				}else{
					$checkstatus = $chek_rcvin3['status'];
				}
			}else{
				$checkstatus=$chek_rcvin2['status'];
			}
		}else{
			$checkstatus = $chek_postatus['status'];
		}
		
	  }else{
		 $checkstatus=$chek_rcvin['status'];
	  }
	if($chek_owner['owner_code']==$row_loc['owner_code'] && $checkstatus=="Received"){
	$locdet=explode("~",getLocationDetails($row_loc['owner_code'],"name,city,state,id_type",$link1));
	$proddet=explode("~",getProductDetails($row_loc['prod_code'],"productname,productcolor,model_name,productcategory,productsubcat,brand",$link1));
	$price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master where state='".$locdet[2]."' and location_type='".$locdet[3]."' and product_code='".$row_loc['prod_code']."' and status='active'"));
	if($row_loc["import_date"]!="0000-00-00"){ $ag = daysDifference($today,$row_loc["import_date"]);}else{ $ag = "";}
	
	if($proddet[3]=="1"){$serial_for = "BTR";}else if($proddet[3]=="2"){$serial_for = "ERBTRCHR";}else if($proddet[3]=="3"){$serial_for = "LTHIBTR";}else if($proddet[3]=="4"){$serial_for = "SOL";}else if($proddet[3]=="5"){$serial_for = "SOLELC";}else{$serial_for = "SOLELC";}
	/////// split serial no. to get all info
	$serialinfo = checkSerialNoLogic(strtoupper($row_loc['imei1']),$serial_for);
	//print_r($serialinfo);
	if(is_array($serialinfo)){
		////// if serial no. search for all charged battery
		if($serial_for=="BTR"){ 
			///// get manufacturing date
			$mfd = getMfDate($serialinfo["mf_date"])." ".getMfMonth($serialinfo["mf_month"])." ".getMfYear($serialinfo["mf_year"]);
			$bt_mfd = getMfYear($serialinfo["mf_year"])."-".date("m", strtotime(getMfMonth($serialinfo["mf_month"])))."-".str_pad(getMfDate($serialinfo["mf_date"]),2,0,STR_PAD_LEFT);
			$agemfd = $bt_mfd;
		}
		////// if serial no. search for lithium ion battery
		else if($serial_for=="LTHIBTR"){ 
			///// get manufacturing date
			$mfd = getMfDate($serialinfo["mf_date"])." ".getMfMonth($serialinfo["mf_month"])." ".getMfYear($serialinfo["mf_year"]);
			$bt_mfd = getMfYear($serialinfo["mf_year"])."-".date("m", strtotime(getMfMonth($serialinfo["mf_month"])))."-".str_pad(getMfDate($serialinfo["mf_date"]),2,0,STR_PAD_LEFT);
			$agemfd = $bt_mfd;
		}
		////// if serial no. search for E-Rickshaw battery charger
		else if($serial_for=="ERBTRCHR"){ 
			///// get manufacturing month year
			$mfd = getMfMonth($serialinfo["mf_month"])." ".getMfYear2($serialinfo["mf_year"]);
			$chg_mfd = getMfYear2($serialinfo["mf_year"])."-".date("m", strtotime(getMfMonth($serialinfo["mf_month"])))."-01";
			$agemfd = $chg_mfd;
		}
		else if($serial_for=="SOL"){
			///// get manufacturing month
			$sol_mfm = getMfMonth($serialinfo["mf_month"]);
			///// get manufacturing year
			$sol_mfy = getMfYear($serialinfo["mf_year"]);
			$mfd = $sol_mfy."-".$sol_mfm;
			$agemfd = $sol_mfy."-".date("m", strtotime($sol_mfm))."-01";
		}
		else if($serial_for=="SOLELC"){
			///// get manufacturing month
			$sol_mfm = getMfMonth($serialinfo["mf_month"]);
			///// get manufacturing year
			$sol_mfy = getMfYear2($serialinfo["mf_year"]);
			$mfd = $sol_mfy."-".$sol_mfm;
			$agemfd = $sol_mfy."-".date("m", strtotime($sol_mfm))."-01";
		}else{
			//$mfd = "Not Found";
			///// get manufacturing month
			$sol_mfm = getMfMonth($serialinfo["mf_month"]);
			///// get manufacturing year
			$sol_mfy = getMfYear2($serialinfo["mf_year"]);
			$mfd = $sol_mfy."-".$sol_mfm;
			$agemfd = $sol_mfy."-".date("m", strtotime($sol_mfm))."-01";
		}
	}else{
		$mfd = "Not Found";
	}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['owner_code']?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$locdet[1]?></td>
<td align="left"><?=$locdet[2]?></td>
<td align="left"><?=$row_loc['prod_code']?></td>
<td align="left"><?=$proddet[0]?></td>
<td align="left"><?=$proddet[2]?></td>
<td align="left"><?=getAnyDetails($proddet[3],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[5],"make","id","make_master",$link1);?></td>
<td align="left"><?=$row_loc['imei1']?></td>
<td align="left"><?=$row_loc['stock_type']?></td>
<td align="left"><?php echo $qty = 1; ?></td>
<td align="right"><?=number_format($price["price"],'2','.','')?></td>
<td align="right"><?=number_format($price["price"]*$qty,'2','.','')?></td>
<td align="center"><?=$mfd?></td>
<td align="center"><?=$row_loc['import_date']?></td>
<td align="right"><?=$ag?></td>
<td align="right"><?php echo daysDifference($today,$agemfd);?></td>
</tr>
<?php
$i+=1;
}
	}
}
?>
</table>

<?php
require_once("../config/config.php");
///// filter value
$sel_date = $_REQUEST["fdate"];
$sel_yr_mnth = substr($_REQUEST["fdate"],0,7);
function getLast6Months($sel_date){
	$a = array();
	for ($i = 0; $i < 6; $i++) 
	{
	   $months[] = date("Y-m", strtotime( $sel_date." -$i months"));
	   $mnthname[] = date("Y-M", strtotime( $sel_date." -$i months"));
	}
	$a[] = $months;
	$a[] = $mnthname;
	return $a;
}
$last_6month = getLast6Months($sel_date);
$first_6mnth = current($last_6month[0]);
$last_6mnth = end($last_6month[0]);
/////////////////////////
function getLast12Months($sel_date){
	$a = array();
	for ($i = 0; $i < 12; $i++) 
	{
	   $months[] = date("Y-m", strtotime( $sel_date." -$i months"));
	   $mnthname[] = date("Y-M", strtotime( $sel_date." -$i months"));
	}
	$a[] = $months;
	$a[] = $mnthname;
	return $a;
}
$last_12month = getLast12Months($sel_date);
/*echo "<pre>";
print_r($last_12month);
echo "</pre>";*/
$first_12mnth = current($last_12month[0]);
$last_12mnth = end($last_12month[0]);
/////// filter values
##### select location
if($_REQUEST["locationcode"]){
	$loc_code = "m.from_location = '".$_REQUEST["locationcode"]."'";
	$loc_code1 = "m.po_from = '".$_REQUEST["locationcode"]."'";
	$loc_code2 = "m.to_location = '".$_REQUEST["locationcode"]."'";
	$loc_code3 = "m.po_to = '".$_REQUEST["locationcode"]."'";
	$loc_code4 = "asc_code = '".$_REQUEST["locationcode"]."'";
}
else{
	$loc_code = "1";
	$loc_code1 = "1";
	$loc_code2 = "1";
	$loc_code3 = "1";
	$loc_code4 = "1";
}
///// transaction type filter
if($_REQUEST["transactionType"]){
	if($_REQUEST["transactionType"]=="STN"){
		$trans_type = "type = 'STN'";
		$trans_type2 = "m.type = 'STN'";
	}else{
		$trans_type = "type IN ('CORPORATE','RETAIL')";
		$trans_type2 = "m.type IN ('CORPORATE','RETAIL')";
	}
}else{
	$trans_type = "type IN ('CORPORATE','RETAIL','STN')";
	$trans_type2 = "m.type IN ('CORPORATE','RETAIL','STN')";
}
///// end filter value
//////get billing details
$inv_res = mysqli_query($link1,"SELECT SUM(m.total_cost) as saleamt, SUM(d.qty) as saleqty, SUM(adjusted_amt) as adjamt FROM billing_master m, billing_model_data d WHERE ".$loc_code." AND ".$trans_type." AND m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.sale_date LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$inv_row = mysqli_fetch_assoc($inv_res) or die("ER1".mysqli_error($link1));
//////get purchase details
$pur_res1 = mysqli_query($link1,"SELECT SUM(m.grand_total) as puramt, SUM(d.req_qty) as purqty FROM vendor_order_master m, vendor_order_data d WHERE ".$loc_code1." AND m.po_no = d.po_no AND m.status != 'Cancelled' AND m.entry_date LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$pur_row1 = mysqli_fetch_assoc($pur_res1) or die("ER2".mysqli_error($link1));

$pur_res2 = mysqli_query($link1,"SELECT SUM(m.total_cost) as puramt, SUM(d.qty) as purqty FROM billing_master m, billing_model_data d WHERE ".$loc_code2." AND ".$trans_type." AND m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.receive_date LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$pur_row2 = mysqli_fetch_assoc($pur_res2) or die("ER3".mysqli_error($link1));
////// get PO details
$po_res = mysqli_query($link1,"SELECT SUM(m.po_value) as poamt, SUM(d.req_qty) as poqty FROM purchase_order_master m, purchase_order_data d WHERE ".$loc_code3." AND m.po_no = d.po_no AND m.status != 'Cancelled' AND m.entry_date LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$po_row = mysqli_fetch_assoc($po_res) or die("ER4".mysqli_error($link1));
////// get Payment details
$recamt_res = mysqli_query($link1,"SELECT SUM(m.amount) as recamt FROM payment_receive m WHERE ".$loc_code2." AND m.status != 'Cancelled' AND m.entry_dt LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$recamt_row = mysqli_fetch_assoc($recamt_res) or die("ER5".mysqli_error($link1));
$recamt_res2 = mysqli_query($link1,"SELECT SUM(m.amount) as recamt FROM payment_receive m WHERE ".$loc_code2." AND m.status != 'Cancelled' AND adjustment_type='' AND m.entry_dt LIKE '".$sel_date."'");// AND m.entry_date LIKE '".date("Y-m")."%'");
$recamt_row2 = mysqli_fetch_assoc($recamt_res2) or die("ER6".mysqli_error($link1));

////// get inventory
$tot_qty = 0;
$tot_val = 0.00;
$new_loc = "";
$old_loc = "";
$seldate = date('Y-m-d', strtotime("+1 day", strtotime($sel_date)));
if($sel_date!="" && $sel_date!=$today){
	$invt_res = mysqli_query($link1,"SELECT asc_code,partcode,okqty,broken FROM `stock_status".$seldate."` WHERE ".$loc_code4);
}else{
	$invt_res = mysqli_query($link1,"SELECT asc_code,partcode,okqty,broken FROM stock_status WHERE ".$loc_code4);
}
while($invt_row = mysqli_fetch_assoc($invt_res)){
	$new_loc = $invt_row['asc_code'];
	if($old_loc != $new_loc){
		$locdet=explode("~",getLocationDetails($invt_row['asc_code'],"state,id_type",$link1));
	}
	$price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master WHERE state='".$locdet[0]."' AND location_type='".$locdet[1]."' AND product_code='".$invt_row['partcode']."' AND status='active'"));
	$tot_qty += $invt_row["okqty"] + $invt_row["broken"];
	$tot_val += ($invt_row["okqty"] + $invt_row["broken"]) * $price["price"];
}
$top_seller = array();
$top_sellerdate = array();
$top_model = array();
$top_modelseller = array();
$last_6monthdata = array();
$last_12monthdata = array();
$last_12monthamt = array();
$last_12monthrecbl = array();
////// get top 5 model sale of the month
$top_modres = mysqli_query($link1,"SELECT m.from_location, m.entry_date, m.total_cost, m.adjusted_amt, d.prod_code, d.qty FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND ".$trans_type2." AND m.entry_date <= '".$sel_yr_mnth."-31' AND m.entry_date >= '".$last_12mnth."-01'");
while($top_modrow = mysqli_fetch_assoc($top_modres)){
	if(substr($top_modrow["entry_date"],0,7) == $sel_yr_mnth){
		$top_seller[$top_modrow["from_location"]] +=  $top_modrow["qty"];
		$top_model[$top_modrow["prod_code"]] +=  $top_modrow["qty"];
		$top_modelseller[$top_modrow["prod_code"]][$top_modrow["from_location"]] += $top_modrow["qty"];
		$top_sellerdate[$top_modrow["from_location"]][$top_modrow["entry_date"]] +=  $top_modrow["qty"];
	}
	////// last 6 months data
	if($top_modrow["entry_date"] >= $last_6mnth."-01"){
		if($_REQUEST["locationcode"]){
			if($_REQUEST["locationcode"] == $top_modrow["from_location"]){
				$last_6monthdata[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["qty"];
			}else{
			}
		}else{
			$last_6monthdata[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["qty"];
		}
	}
	//////// last 12 months data
		if($_REQUEST["locationcode"]){
			if($_REQUEST["locationcode"] == $top_modrow["from_location"]){
				$last_12monthdata[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["qty"];
				$last_12monthamt[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["total_cost"];
				$last_12monthrecbl[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["total_cost"]-$top_modrow["adjusted_amt"];
			}else{
			}
		}else{
			$last_12monthdata[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["qty"];
			$last_12monthamt[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["total_cost"];
			$last_12monthrecbl[$top_modrow["from_location"]][substr($top_modrow["entry_date"],0,7)] += $top_modrow["total_cost"]-$top_modrow["adjusted_amt"];
		}

}
//print_r($last_12monthamt);
arsort($top_model);
arsort($top_seller);
///// make graph string of top 5 model of the month
$topModelStr = "";
$topModelDrill = "";
$top_5model = array_slice($top_model, 0, 5);
foreach($top_5model as $model => $val){
	if($topModelStr){
		$topModelStr .= ",{ name: '".getProductDetails($model,"productcode",$link1)."',
						    y: ".$val.",
						    drilldown: '".getProductDetails($model,"productcode",$link1)."'}";
	}else{
		$topModelStr .= " { name: '".getProductDetails($model,"productcode",$link1)."',
						    y: ".$val.",
						    drilldown: '".getProductDetails($model,"productcode",$link1)."'}";
	}
	///////get location wise sale
	$str1 = "";
	foreach($top_modelseller[$model] as $loc => $qty){
		if($str1){
			$str1 .= ",[ 
								  '".$loc."',
								  ".$qty."
								]";
		}else{
			$str1 .= "[ 
								  '".$loc."',
								  ".$qty."
								]";
		}
	}
	/////
	if($topModelDrill){
		$topModelDrill .= ",{
							 name: '".getProductDetails($model,"productcode",$link1)."',
							 id: '".$model."',
							 data: [".$str1."]
							}";
	}else{
		$topModelDrill .= "{
							 name: '".getProductDetails($model,"productcode",$link1)."',
							 id: '".$model."',
							 data: [".$str1."]
							}";
	}
}
///// make graph string of top 5 seller of the month
$topSellerStr = "";
$topSellerDrill = "";
$top_5seller = array_slice($top_seller, 0, 5);
foreach($top_5seller as $loc => $val){
	if($topSellerStr){
		$topSellerStr .= ",{ name: '".getLocationDetails($loc,"asc_code",$link1)."',
						    y: ".$val.",
						    drilldown: '".getLocationDetails($loc,"asc_code",$link1)."'}";
	}else{
		$topSellerStr .= " { name: '".getLocationDetails($loc,"asc_code",$link1)."',
						    y: ".$val.",
						    drilldown: '".getLocationDetails($loc,"asc_code",$link1)."'}";
	}
	///////get location wise sale
	$str1 = "";
	foreach($top_sellerdate[$loc] as $day => $qty){
		if($str1){
			$str1 .= ",[ 
								  '".$day."',
								  ".$qty."
								]";
		}else{
			$str1 .= "[ 
								  '".$day."',
								  ".$qty."
								]";
		}
	}
	/////
	if($topSellerDrill){
		$topSellerDrill .= ",{
							 name: '".getLocationDetails($loc,"asc_code",$link1)."',
							 id: '".$loc."',
							 data: [".$str1."]
							}";
	}else{
		$topSellerDrill .= "{
							 name: '".getLocationDetails($loc,"asc_code",$link1)."',
							 id: '".$loc."',
							 data: [".$str1."]
							}";
	}
}
////// make last 6 month string for sale trend location wise
$last_6monthstr = "'".implode("','",$last_6month[1])."'";
$last_6monthdatastr = "";
$arr_key = array_keys($last_6monthdata);
for($j=0; $j<count($arr_key); $j++){
	$innerdata = "";
	for($k=0; $k<count($last_6month[0]); $k++){
		if($last_6monthdata[$arr_key[$j]][$last_6month[0][$k]]){ $salval = $last_6monthdata[$arr_key[$j]][$last_6month[0][$k]];}else{ $salval=0;}
		if($innerdata){
			$innerdata .= ",".$salval;
		}else{
			$innerdata .= "".$salval;
		}
	}
	if($last_6monthdatastr){
		$last_6monthdatastr .= ",{
								name: '".getLocationDetails($arr_key[$j],"asc_code",$link1)."',
								data: [".$innerdata."]
							 }";
	}else{
		$last_6monthdatastr .= "{
								name: '".getLocationDetails($arr_key[$j],"asc_code",$link1)."',
								data: [".$innerdata."]
							 }";
	}	
}
////// make last 12 month string for sale trend location wise
$last_12monthstr = "'".implode("','",$last_12month[1])."'";
/*echo "<pre>";
print_r($last_12month[0]);
echo "</pre>";*/
$last_12monthdatastr = "";
$arr_12key = array_keys($last_12monthdata);

for($l=0; $l<count($last_12month[0]); $l++){
	$inner12data = 0;
	for($m=0; $m<count($arr_12key); $m++){
		if($last_12monthdata[$arr_12key[$m]][$last_12month[0][$l]]){ $sal12val = $last_12monthdata[$arr_12key[$m]][$last_12month[0][$l]];}else{ $sal12val=0;}
			$inner12data += $sal12val;
	}
	if($last_12monthdatastr){
		$last_12monthdatastr .= ",".$inner12data."";
	}else{
		$last_12monthdatastr .= "".$inner12data."";
	}	
}
////// make last 12 month string for sale amount location wise
$last_12monthamtstr = "";
$arr_12keyamt = array_keys($last_12monthamt);
//print_r($arr_12keyamt);
for($l=0; $l<count($last_12month[0]); $l++){
	$inner12amt = 0;
	for($m=0; $m<count($arr_12keyamt); $m++){
		if($last_12monthamt[$arr_12keyamt[$m]][$last_12month[0][$l]]){ $sal12amt = $last_12monthamt[$arr_12keyamt[$m]][$last_12month[0][$l]];}else{ $sal12amt=0;}
			$inner12amt += $sal12amt;
	}
	if($last_12monthamtstr){
		$last_12monthamtstr .= ",".$inner12amt."";
	}else{
		$last_12monthamtstr .= "".$inner12amt."";
	}	
}
////// make last 12 month string for receible amount location wise
$last_12monthrecblstr = "";
$arr_12keyrecbl = array_keys($last_12monthrecbl);
//print_r($arr_12keyamt);
for($l=0; $l<count($last_12month[0]); $l++){
	$inner12recbl = 0;
	for($m=0; $m<count($arr_12keyrecbl); $m++){
		if($last_12monthrecbl[$arr_12keyrecbl[$m]][$last_12month[0][$l]]){ $sal12recbl = $last_12monthrecbl[$arr_12keyrecbl[$m]][$last_12month[0][$l]];}else{ $sal12recbl=0;}
			$inner12recbl += $sal12recbl;
	}
	if($last_12monthrecblstr){
		$last_12monthrecblstr .= ",".$inner12recbl."";
	}else{
		$last_12monthrecblstr .= "".$inner12recbl."";
	}	
}

/*echo "<pre>";
print_r($last_12monthamt);
echo "</pre>";
echo $last_12monthamtstr;*/
//echo "<pre>".$topModelDrill."</pre>";
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link href="../css/bootstrap.min-dash.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
	$('#myTable1').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('#myTable2').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('.selectpicker').selectpicker({
		width : "300px"
	});
});	
$(document).ready(function () {
	$('#fdate').datepicker({
		startDate: "2023-01-07",
		endDate: "<?=$today?>",
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
 </script>
 <style type="text/css">
 .col-md-3{
	 padding-left:5px;
	 padding-right:5px;
 }
td {
    border: 1px solid black;
    border-radius: 5px;
    -moz-border-radius: 5px;
    padding: 5px;
}
 /* ===== DASHBOARD CARD POLISH ===== */
 .card {
     border-radius: 18px !important;
     box-shadow: 0 12px 25px rgba(0,0,0,0.15);
     transition: all 0.25s ease;
     overflow: hidden;
 }

 .card:hover {
     transform: translateY(-6px);
     box-shadow: 0 18px 40px rgba(0,0,0,0.25);
 }

 .card-body h3 {
     font-weight: 600;
     margin: 0;
 }

 .card-body h4 {
     margin-top: 6px;
     opacity: 0.9;
 }

 .card i {
     opacity: 0.85;
 }

 /* Consistent height + alignment */
 .card-body {
     display: flex;
     justify-content: space-between;
     align-items: center;
 }

 /* Chart containers polish */
 #top_model,
 #top_seller,
 #sale_trend,
 #po_status_pie,
 #inv_status_pie,
 #outstanding,
 #container {
     background: #fff;
     margin-top: 15px;
     padding: 10px;
     box-shadow: 0 10px 30px rgba(0,0,0,0.15);
 }

 </style>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <br/>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
      	<div class="col-md-5" align="right">
        	<input type="text" class="form-control" name="fdate"  id="fdate" style="width:160px;" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>" required>
        </div>
        <div class="col-md-3" align="right">
        	<select name="transactionType" id="transactionType" class="form-control" style="width:200px;">
            	<option value="">Invoice & STN</option>
                <option value="INVOICE"<?php if($_REQUEST["transactionType"]=="INVOICE"){ echo "selected";}?>>Invoice</option>
                <option value="STN"<?php if($_REQUEST["transactionType"]=="STN"){ echo "selected";}?>>STN</option>
            </select>
        </div>
        <div class="col-md-3" align="right">
        	<select name="locationcode" id="locationcode" class="form-control selectpicker" style="width:200px;" data-live-search="true">
               <option value="" selected="selected">Select Location</option>
                <?php 
                $sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
                $res_chl=mysqli_query($link1,$sql_chl);
                while($result_chl=mysqli_fetch_array($res_chl)){
                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                      ?>
                <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                   <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                </option>
                <?php
                }
                ?>
          </select>
        </div>
        <div class="col-md-1" align="right">
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
        </div>
      </div>
	  </form>

      <div class="form-group" id="page-wrap" style="margin-left:10px;">
      <div class="row">
      	<div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-success">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> <?=$inv_row["saleamt"]?></h3>
                        </div>
                        <div>
                        	<h3 class="card-title"><?=round($inv_row["saleqty"])?> Pcs</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-tags fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Sale</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-warning">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> <?=$pur_row1["puramt"]?></h3>
                        </div>
                        <div>
                        	<h3 class="card-title"><?=round($pur_row1["purqty"])?> Pcs</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-shopping-cart fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Purchase</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-info">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> <?=$po_row["poamt"]?></h3>
                        </div>
                        <div>
                        	<h3 class="card-title"><?=round($po_row["poqty"])?> Pcs</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-shopping-bag fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">PO</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-secondary">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> <?=$recamt_row["recamt"]?></h3>
                        </div>
                        <div>
                        	<h3 class="card-title">&nbsp;</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-suitcase fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Collection</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-primary">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><?=$tot_qty?> Pcs</h3>
                        </div>
                        <div>
                        	<h3 class="card-title"><i class="fa fa-rupee"></i> <?=$tot_val?></h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-cubes fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Inventory</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-danger">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> <?=$inv_row["saleamt"]-$inv_row["adjamt"]-$recamt_row2["recamt"]?></h3>
                        </div>
                        <div>
                        	<h3 class="card-title">&nbsp;</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-balance-scale fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Outstanding</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--        <div class="col-md-3 py-1">
            <div class="card h-100 text-white bg-success">
                <div class="card-body">
                	<div style="float:left;display:inline-block">
                    	<div>
                        	<h3><i class="fa fa-rupee"></i> 0.00</h3>
                        </div>
                        <div>
                        	<h3 class="card-title">&nbsp;</h3>
                        </div>
                    </div>
                    <div style="float:right;display:inline-block;">
                        <div style="color:#666" align="right">
                        	<h3><i class='fa fa-rupee fa-2x'></i></h3>
                        </div>
                        <div align="right">
                        	<h4 class="card-title">Profit on sale</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
       </div>
       <div class="row">
          <div class="col-md-6" id="top_model" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
          <div class="col-md-6" id="top_seller" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        </div>
        <div class="row">
          <div class="col-md-12" id="sale_trend" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        </div>
        <br/>
        <div class="row">
        	<div class="col-md-6">
            	<table id="myTable1" width="100%" border="0" cellpadding="2"  cellspacing="0">
    				<thead>	
                    	<tr align="center" class="<?=$tableheadcolor?>">
                    	  <th height="20" colspan="2" style="text-align:center">Current Month PO Status</th>
                   	  </tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						<th width="70%" height="20"><strong>PO Status</strong></th>
      						<th width="30%" style="text-align:right"><strong>Count</strong></th>
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get PO status count
						$postatus_str = "";
						$arr_postatus = array();
						$pocnt_res = mysqli_query($link1,"SELECT m.status, COUNT(m.status) as cnt FROM purchase_order_master m WHERE ".$loc_code3." AND m.entry_date LIKE '".$sel_yr_mnth."%' GROUP BY m.status");
						while($pocnt_row = mysqli_fetch_assoc($pocnt_res)){
							$arr_postatus[$pocnt_row["status"]] = $pocnt_row["cnt"];
						?>
                    	<tr>
    				    	<td height="20"><?=$pocnt_row["status"]?></td>
    				  		<td align="right"><?=$pocnt_row["cnt"]?></td>
  				  		</tr>
                        <?php 
						}
						/////// 
						$totpo = array_sum($arr_postatus);
						foreach($arr_postatus as $statuss => $cont){
							if($postatus_str){
								$postatus_str .= ",{ name: '".$statuss."', y: ".round($cont*100/$totpo)." }";
							}else{
								$postatus_str .= "{ name: '".$statuss."', y: ".round($cont*100/$totpo)." }";
							}
						}
						?>
                  	</tbody>
  				</table>
            </div>
            <div class="col-md-6">
           	  <table id="myTable2" width="100%" border="0" cellpadding="2"  cellspacing="0">
    				<thead>
                    	<tr align="center" class="<?=$tableheadcolor?>">
                    	  <th height="20" colspan="2" style="text-align:center">Current Month Invoice Status</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						<th width="70%" height="20"><strong>Invoice Status</strong></th>
      						<th width="30%" style="text-align:right"><strong>Count</strong></th>
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get invoice status count
						$invstatus_str = "";
						$arr_invstatus = array();
						$invcnt_res = mysqli_query($link1,"SELECT m.status, COUNT(m.status) as cnt FROM billing_master m WHERE ".$loc_code." AND ".$trans_type2." AND m.entry_date LIKE '".$sel_yr_mnth."%' GROUP BY m.status");
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$arr_invstatus[$invcnt_row["status"]] = $invcnt_row["cnt"];
						?>
                    	<tr>
    				    	<td height="20"><?=$invcnt_row["status"]?></td>
    				  		<td align="right"><?=$invcnt_row["cnt"]?></td>
  				  		</tr>
                        <?php 
						}
						/////// 
						$totinv = array_sum($arr_invstatus);
						foreach($arr_invstatus as $statuss => $cont){
							if($invstatus_str){
								$invstatus_str .= ",{ name: '".$statuss."', y: ".round($cont*100/$totinv)." }";
							}else{
								$invstatus_str .= "{ name: '".$statuss."', y: ".round($cont*100/$totinv)." }";
							}
						}
						?>
                  	</tbody>
  				</table>
            </div>
        </div>
        <div class="row">
          <div class="col-md-6" id="po_status_pie" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
          <div class="col-md-6" id="inv_status_pie" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        </div>
        <div class="row">
          <div class="col-md-12" id="outstanding" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        </div>
        <div class="row">
          <div class="col-md-12" id="container" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        </div>
            <button class="btn btn-primary" id="plain">Plain</button>
            <button class="btn btn-primary" id="inverted">Inverted</button>
            <button class="btn btn-primary" id="polar">Polar</button>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
$(function () { 
 // Create the chart for top 5 model sale of the month
Highcharts.chart('top_model', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Top 5 Model Sale Of The Month'
    },
    /*subtitle: {
        text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },*/
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total Qty'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },
	credits: {
		enabled: false
	},
    series: [
        {
            name: "Model",
            colorByPoint: true,
            data: [<?=$topModelStr?>]
        }
    ],
    drilldown: {
        series: [<?=$topModelDrill?>]
    }
});
 // Create the chart for top 5 seller of the month
Highcharts.chart('top_seller', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Top 5 Seller Of The Month'
    },
    /*subtitle: {
        text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },*/
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total Qty'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },
	credits: {
		enabled: false
	},
    series: [
        {
            name: "Seller",
            colorByPoint: true,
            data: [<?=$topSellerStr?>]
        }
    ],
    drilldown: {
        series: [<?=$topSellerDrill?>]
    }
});
///// create the chart for last 6 month sale trend of each location
Highcharts.chart('sale_trend', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Last 6 month sales trend of location'
    },
    /*subtitle: {
        text: 'Source: WorldClimate.com'
    },*/
    xAxis: {
        categories: [<?=$last_6monthstr?>]
    },
    yAxis: {
        title: {
            text: 'Qty (Pcs)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
	credits: {
		enabled: false
	},
    series: [<?=$last_6monthdatastr?>]
});
////// create chart for purchse/payble & invoice/receivable
Highcharts.chart('outstanding', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Invoice/Receivable'
    },
    /*subtitle: {
        text: 'Source: WorldClimate.com'
    },*/
    xAxis: {
        categories: [<?=$last_12monthstr?>],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Amount (in rupee)'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table cellpadding="0" cellspacing="0">',
        pointFormat: '<tr><td style="color:{series.color};">{series.name}: </td>' +
            '<td align="right"><b><i class="fa fa-rupee"></i> {point.y:.2f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
	credits: {
		enabled: false
	},
    series: [/*{
        name: 'Purchase',
        data: [499.9, 711.5, 706.4, 529.2, 444.0, 776.0, 835.6, 948.5, 716.4, 694.1, 595.6, 554.4]

    }, {
        name: 'Payable',
        data: [833.6, 783.8, 981.5, 930.4, 406.0, 784.5, 505.0, 604.3, 491.2, 983.5, 506.6, 692.3]

    },*/ {
        name: 'Invoice',
        data: [<?=$last_12monthamtstr?>]

    }, {
        name: 'Receivable',
        data: [<?=$last_12monthrecblstr?>]

    }]
});
///////////////
var chart = Highcharts.chart('container', {

    title: {
        text: 'Last 12 months sale data'
    },

    /*subtitle: {
        text: 'Plain'
    },
*/
    xAxis: {
        categories: [<?=$last_12monthstr?>]
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Qty (in Pcs)'
        }
    },
	credits: {
		enabled: false
	},
    series: [{
		name: "Sale Qty",
        type: 'column',
        colorByPoint: true,
        data: [<?=$last_12monthdatastr?>],
        showInLegend: false
    }]

});


$('#plain').click(function () {
    chart.update({
        chart: {
            inverted: false,
            polar: false
        },
        subtitle: {
            text: 'Plain'
        }
    });
});

$('#inverted').click(function () {
    chart.update({
        chart: {
            inverted: true,
            polar: false
        },
        subtitle: {
            text: 'Inverted'
        }
    });
});

$('#polar').click(function () {
    chart.update({
        chart: {
            inverted: false,
            polar: true
        },
        subtitle: {
            text: 'Polar'
        }
    });
});
///// create chart for PO status
Highcharts.setOptions({
    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    })
});

///// create chart for PO status
Highcharts.chart('po_status_pie', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'PO Status Of Current Month'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                connectorColor: 'silver'
            }
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Status',
        data: [<?=$postatus_str?>]
    }]
});
///// create chart for invoice status
Highcharts.chart('inv_status_pie', {
	colors: ['#f05b7f','#7cb5ec','#ffbc75','#aafffa','#e4d354','#44a9a8'],
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Invoice Status Of Current Month'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                connectorColor: 'silver'
            }
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Status',
        data: [<?=$invstatus_str?>]
    }]
});

});
 </script>
<script src="../high/highcharts.js" type="text/javascript"></script>
<script src="../high/highcharts-more.js"></script>
<script src="../high/js/modules/data.js"></script>
<script src="../high/js/modules/drilldown.js"></script>
<script src="../high/js/highcharts-3d.js"></script>
<script src="../high/js/modules/exporting.js"></script>
</body>
</html>
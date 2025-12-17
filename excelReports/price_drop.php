<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$location=base64_decode($_REQUEST['loc']);
$part_code=base64_decode($_REQUEST['partcode']);
## selected location
if($location!=""){
	$loc = "asc_code='".$location."'";
}else{
	$locstr = getAccessLocation($_SESSION['userid'],$link1);
	$loc = "asc_code in (".$locstr.")";
}
////
if($part_code!=""){
	$part = "partcode='".$location."'";
}else{
	$part = "1";
}
///// here we are calculating price drop of each stock of selected location
/// make location state and location type array
$arr_final = array();

$arr_loc = array();
$res_loc =  mysqli_query($link1,"SELECT asc_code, id_type, state FROM  asc_master WHERE status = 'active'");
while($row_loc = mysqli_fetch_assoc($res_loc)){
	$arr_loc[$row_loc["asc_code"]]["idType"] = $row_loc["id_type"];
	$arr_loc[$row_loc["asc_code"]]["state"] = $row_loc["state"];
}
/// so first of all we will fetch inventory details of selected location
$invt_res = mysqli_query($link1,"SELECT asc_code, partcode, okqty FROM stock_status WHERE ".$loc." AND ".$part);
while($invt_row = mysqli_fetch_assoc($invt_res)){
	////// get current price of selected product
	$cp_res = mysqli_query($link1,"SELECT price FROM price_master WHERE state='".$arr_loc[$invt_row["asc_code"]]["state"]."' AND location_type='".$arr_loc[$invt_row["asc_code"]]["idType"]."' AND product_code='".$invt_row["partcode"]."' AND status='active'");
	$cp_row = mysqli_fetch_assoc($cp_res);
	$arr_challan = array();
	/////check each incoming stock in selected location order by ascending
	$in_res = mysqli_query($link1,"SELECT reference_no, type_of_transfer FROM stock_ledger WHERE partcode='".$invt_row["partcode"]."' AND owner_code='".$invt_row["asc_code"]."' AND stock_transfer='IN' AND stock_type='OK' AND type_of_transfer IN ('Local Purchase','Opening Stock','Vendor Purchase') GROUP BY reference_no");
	while($in_row = mysqli_fetch_assoc($in_res)){
		////// check each challan current status should not be cancelled
		if($in_row["type_of_transfer"]=="Local Purchase" || $in_row["type_of_transfer"]=="Vendor Purchase"){
			$check_lp = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM vendor_order_master WHERE po_no='".$in_row["reference_no"]."' AND status='Cancelled'"));
			if($check_lp == 0){
				$arr_challan[] = $in_row["reference_no"];		
			}else{
				///// challan is to be skip
			}
		}
		else if($in_row["type_of_transfer"]=="Opening Stock"){
			$check_op = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM opening_stock_master WHERE doc_no='".$in_row["reference_no"]."' AND status='Cancelled'"));
			if($check_op == 0){
				$arr_challan[] = $in_row["reference_no"];		
			}else{
				///// challan is to be skip
			}
		}
		else{
			///// nothing to do
		}
		
	}
	/*echo $invt_row["partcode"]."<pre>";
	print_r($arr_challan);
	echo "</pre>";*/
	///// check all processed challan in ledger fifo bases
	$a = array();
	$b = array();
	
	$ok_qty = $invt_row["okqty"];
	$make_challan_str = implode("','",$arr_challan);
	//echo "SELECT reference_no, qty, rate FROM stock_ledger WHERE partcode='".$invt_row["partcode"]."' AND owner_code='".$invt_row["asc_code"]."' AND stock_transfer='IN' AND stock_type='OK' AND reference_no IN ('".$make_challan_str."') ORDER BY create_date,create_time DESC<br/>";
	$lgr_res = mysqli_query($link1,"SELECT reference_no, qty, rate,create_date FROM stock_ledger WHERE partcode='".$invt_row["partcode"]."' AND owner_code='".$invt_row["asc_code"]."' AND stock_transfer='IN' AND stock_type='OK' AND reference_no IN ('".$make_challan_str."') ORDER BY create_date,create_time DESC");
	while($lgr_row = mysqli_fetch_assoc($lgr_res)){
		//echo $ok_qty ."<=". $lgr_row["qty"]."<br/>";
		//// check if available qty is less than or equal to challan qty
		if($ok_qty <= $lgr_row["qty"]){
			$b["chQty"] = $ok_qty;
			$b["chPrice"] = $lgr_row["rate"];
			$b["chNo"] = $lgr_row["reference_no"];
			$b["chDate"] = $lgr_row["create_date"];
			array_push($a,$b);
			break;
		}else{
			$b["chQty"] = $lgr_row["qty"];
			$b["chPrice"] = $lgr_row["rate"];
			$b["chNo"] = $lgr_row["reference_no"];
			$b["chDate"] = $lgr_row["create_date"];
			array_push($a,$b);
			$ok_qty = $ok_qty - $lgr_row["qty"];
		}
	}
	$arr_final[$invt_row["asc_code"]][$invt_row["partcode"]][0] = $invt_row["okqty"];
	$arr_final[$invt_row["asc_code"]][$invt_row["partcode"]][1] = $cp_row["price"];
	$arr_final[$invt_row["asc_code"]][$invt_row["partcode"]][2] = $a;
}
/*echo "<pre>";
print_r($arr_final);
echo "</pre>";*/
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
	<td>Location</td>
    <td>Partcode</td>
    <td>Current OK Stock</td>
    <td>Current Price</td>
    <td>Challan Redeem OK Qty</td>
    <td>Challan Price</td>
    <td>Challan No.</td>
    <td>Challan Date</td>
</tr>
<?php
  $optimise_array = array();
  foreach($arr_final as $loc => $arr_part){
	  foreach($arr_part as $partcode => $arr_mnl){
		  foreach($arr_mnl as $key_mnl => $val){
			  for($i=0; $i<count($arr_mnl[2]); $i++){
				  foreach($arr_mnl[2][$i] as $key => $valu){
					  //echo $arr_mnl[1]." < ".$arr_mnl[2][$i]["chPrice"]."<br/>";
					  if($arr_mnl[1] < $arr_mnl[2][$i]["chPrice"]){
						$optimise_array[] = $loc."~".$partcode."~".$arr_mnl[0]."~".$arr_mnl[1]."~".$arr_mnl[2][$i]["chQty"]."~".$arr_mnl[2][$i]["chPrice"]."~".$arr_mnl[2][$i]["chNo"]."~".$arr_mnl[2][$i]["chDate"];
					  }
				  }
			  }
		  }
	  }
  }
  $new_array = array_unique($optimise_array);
  foreach($new_array as $final_val){
	  $expld = explode("~",$final_val);
  ?>
  <tr>
    <td><?=$expld[0]?></td>
    <td><?=$expld[1]?></td>
    <td><?=$expld[2]?></td>
    <td><?=$expld[3]?></td>
    <td><?=$expld[4]?></td>
    <td><?=$expld[5]?></td>
    <td><?=$expld[6]?></td>
    <td><?=$expld[7]?></td>
  </tr>
  <?php
  }
  ?>
</table>
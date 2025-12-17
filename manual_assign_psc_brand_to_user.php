<?php
//require_once("config/dbconnect.php");
$s = 0;
$today = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
$res_asp = mysqli_query($link1,"SELECT username FROM admin_users WHERE `status` LIKE 'Active'");
while($row = mysqli_fetch_assoc($res_asp)){
	//add script for psc and brand auto assigned for all users.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
		//// pick all active psc
		$res_psc = mysqli_query($link1,"SELECT psubcatid, prod_sub_cat, productid, product_category FROM product_sub_category WHERE status ='1'");
		while($row_psc = mysqli_fetch_assoc($res_psc)){
			///// check if already exist
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM mapped_productcat WHERE userid='".$row["username"]."' AND prod_subcatid='".$row_psc["psubcatid"]."'"))>0){
				$res10 = mysqli_query($link1,"UPDATE mapped_productcat SET status='Y' WHERE userid='".$row["username"]."' AND prod_subcatid='".$row_psc["psubcatid"]."'");
			}else{
				$res10 = mysqli_query($link1,"INSERT INTO mapped_productcat SET userid='".$row["username"]."',product_cat='".$row_psc["product_category"]."',product_subcat='".$row_psc["prod_sub_cat"]."',prod_subcatid='".$row_psc["psubcatid"]."',status='Y'");
			}
			//// check if query is not executed
			if (!$res10) {
				$flag = false;
				$err_msg = "Error Code10:".mysqli_error($link1);
			}
		}
		//// pick all active brand
		$res_brand = mysqli_query($link1,"SELECT id, make FROM make_master WHERE status ='1'");
		while($row_brand = mysqli_fetch_assoc($res_brand)){
			///// check if already exist
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM mapped_brand WHERE userid='".$row["username"]."' AND brand='".$row_brand["make"]."'"))>0){
				$res11 = mysqli_query($link1,"UPDATE mapped_brand SET status='Y' WHERE userid='".$row["username"]."' AND brand='".$row_brand["make"]."'");
			}else{
				$res11 = mysqli_query($link1,"INSERT INTO mapped_brand SET userid='".$row["username"]."',brand='".$row_brand["make"]."',status='Y'");
			}
			//// check if query is not executed
			if (!$res11) {
				$flag = false;
				$err_msg = "Error Code11:".mysqli_error($link1);
			}
		}
	//end add script for psc and brand auto assigned for all users.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	$s++;
}
echo $s."  ".$err_msg;
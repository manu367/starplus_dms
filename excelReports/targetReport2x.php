<?php
print("\n");
print("\n");

	$user_id = base64_decode($_REQUEST['user_id']);
	$selyear = base64_decode($_REQUEST['selyear']);
	$selmonth = base64_decode($_REQUEST['selmonth']);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>
        <th>Employee Id</th>
        <th>User Id</th>
        <th>Designation</th>
        <th>Department</th>
        <th>Sub-Department</th>
        <th>Year</th>
        <th>Month</th>
        <th>Product Sub Category</th>
        <th>Task Name</th>
        <th>Remark</th>
        <th>Target Value</th>
        <th>Achivement (Self)</th>
        <th>Achivement (Team)</th>
        <th>Achivement %</th>
    </tr>
<?php
	function getAchivement($link1, $id, $task_name, $m, $year,$psc){

		$month = date("m", strtotime($m."-".$year));					
		$resp = 0;
		if($task_name == "Dealer Visit"){
			//// calculate from old dealer vist
			$row_oldcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS olddealer FROM dealer_visit WHERE userid='".$id."' AND MONTH(visit_date) = '".$month."' AND YEAR(visit_date) = '".$year."' AND dealer_type='Old'"));
			//// calculate from new dealer vist
			$row_newcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS newdealer FROM dealer_visit WHERE userid='".$id."' AND MONTH(visit_date) = '".$month."' AND YEAR(visit_date) = '".$year."' AND dealer_type='New'"));
			
			//echo "Old Visit -> ".$row_oldcnt["olddealer"];
			//echo "<br>";
			//echo "New Visit -> ".$row_newcnt["newdealer"];
			//$ach = $row_oldcnt["olddealer"] + $row_newcnt["newdealer"];
			$resp = $row_oldcnt["olddealer"]."~".$row_newcnt["newdealer"];
		}
		else if($task_name == "Feedback"){
			/// get feedback count
			$row_fb =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS feedback FROM query_master WHERE entry_by='".$id."' AND MONTH(entry_date) = '".$month."' AND YEAR(entry_date) = '".$year."'"));
			//if($row_fb["feedback"]){ echo $ach = $row_fb["feedback"];}else{echo $ach = 0;}
			$resp = $row_fb["feedback"];
		}
		else if($task_name == "Sale Order"){
			/// get sale order count
			$row_so =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(req_qty) AS socnt FROM purchase_order_data WHERE po_no IN (SELECT po_no FROM purchase_order_master WHERE  create_by='".$id."' AND MONTH(entry_date) = '".$month."' AND YEAR(entry_date) = '".$year."' AND status IN ('Approved','Processed')) AND prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$psc."'))"));
			//if($row_so["socnt"]){ echo $ach = $row_so["socnt"];}else{echo $ach = 0;}
			$resp = $row_so["socnt"];
		}
		else if($task_name == "Collection"){
			/// get collection count
			$row_col =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(amount) AS collection FROM party_collection WHERE user_id='".$id."' AND MONTH(entry_date) = '".$month."' AND YEAR(entry_date) = '".$year."'"));
			//if($row_col["collection"]){ echo $ach = $row_col["collection"];}else{echo $ach = 0;}
			$resp = $row_col["collection"];	
		}
		else{
			
		}
		return $resp;
	}

	function getDlAchive($link1, $id, $task, $m, $y ,$subcat){
		$resp = 0;
		$childs = getHierarchy($id, $link1);
		foreach($childs as $child){
			
			
			foreach($child as $key => $subchilds){
				if($subchilds){
					$achived = getDlAchive($link1, $key, $task, $m, $y ,$subcat);
					$ge_ach = getAchivement($link1, $key, $task, $m, $y, $subcat);
					$resp += $achived+$ge_ach;
				}
				else{
					$ge_ach = getAchivement($link1, $key, $task, $m, $y, $subcat);
					$resp +=  ($ge_ach)?(int)$ge_ach:0;
				}									
			}
		}
		return $resp;								
	}
	function printTask($link1, $id, $m, $y){
		$achivement = 0;
		$month = date("m", strtotime($m."-".$y));
		$sql = "SELECT * FROM sf_target_data WHERE user_id = '".$id."' AND month='".$month."' AND year='".$y."'";
		$invcnt_res = mysqli_query($link1, $sql);
		if($invcnt_res){
			if(mysqli_num_rows($invcnt_res) > 0){
				$count = 1;
				while($row = mysqli_fetch_assoc($invcnt_res)){
				
					$userinfo = getAnyDetails($id,'name','username','admin_users',$link1);
					$achivement = getAchivement($link1, $id, $row['task_name'], $m, $y,$row["prod_code"]);
					
					$ac_arr = explode("~", $achivement);
					if(count($ac_arr) > 1){
						$achivement = (int)$ac_arr[0] + (int)$ac_arr[1];
					}
					
					$dl_achivement = getDlAchive($link1, $id, $row['task_name'], $m, $y,$row["prod_code"]);
					
					$total_ac = (int)$achivement + (int)$dl_achivement;
					$target = $row['target_val'];
					
					$per = round(($total_ac * 100) / $target);
					
					if($per > 75){
						$cls = "success";
						$txt = "";
					}else if($per > 50 && $per <= 75){
						$cls = "info";
						$txt = "";
					}else if($per > 25 && $per <= 50){
						$cls = "warning";
						$txt = "";
					}else{
						$cls = "danger";
						$txt = "";
					}
					
					$user_info = explode("~", getAnyDetails($id,"designationid,department,subdepartment","username","admin_users",$link1));
					
					$desig = getAnyDetails($user_info[0],"designame","designationid","hrms_designation_master",$link1);
					$depart = getAnyDetails($user_info[1],"dname","departmentid","hrms_department_master",$link1);
					$subdepart = getAnyDetails($user_info[2],"subdept","subdeptid","hrms_subdepartment_master",$link1);
		
					echo '<tr><td>'.$count.'</td><td>'.$userinfo.'</td><td>'.$row['emp_id'].'</td><td>'.$id.'</td><td>'.$desig.'</td><td>'.$depart.'</td><td>'.$subdepart.'</td><td>'.$row['year'].'</td><td>'.$row['month'].'</td><td>'.$row['prod_code'].'</td><td>'.$row['task_name'].'</td><td>'.$row['remark'].'</td><td>'.$row['target_val'].'</td><td>'.(($achivement)?(int)$achivement:0).'</td><td>'.$total_ac.'</td><td>'.$per.'%</td></tr>';
					$count++;
				}
			}
			else{
				//echo "<br>NO task!<br>";
			}
		}
		else{
			//echo "failed<br>";
		}				
	}							
	function printRow($link1, $arr, $m, $y){
		foreach($arr as $key => $value){
			if(strlen($key) > 5){
				printTask($link1, $key, $m, $y);
				printRow($link1, $value, $m, $y);
			}
			else{
				printRow($link1, $value, $m, $y);
			}							
		}
	}
	$arr = getHierarchy($user_id, $link1);
	$arr = [[ $user_id => $arr  ]];
	printRow($link1, $arr, $selmonth, $selyear);
?>
</table>
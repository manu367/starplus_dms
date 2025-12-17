<?php
print("\n");
print("\n");
////// filters value/////
$user_id = base64_decode($_REQUEST['user_id']);
$selyear = base64_decode($_REQUEST['selyear']);
$selmonth = base64_decode($_REQUEST['selmonth']);
//$department = base64_decode($_REQUEST['department']);
//$subdepartment = base64_decode($_REQUEST['subdepartment']);

///////filter value
/*if($department){
	$deptqry = " AND b.department ='".$department."'";
}else{
	$deptqry = "";
}
if($subdepartment){
	$subdeptqry = " AND b.subdepartment ='".$subdepartment."'";
}else{
	$subdeptqry = "";
}*/
//////End filters value/////
$deptqry = "";
$subdeptqry = "";
if($_SESSION["userid"]=="admin"){
$sqldata = "SELECT a.*, b.name, b.oth_empid, b.designationid, b.department, b.subdepartment FROM sf_target_master a, admin_users b WHERE 1=1 AND a.user_id=b.username ".$subdeptqry." ".$deptqry."";
}else{
	$child = getHierarchyStr($_SESSION["userid"], $link1, "");
$sqldata = "SELECT a.*, b.name, b.oth_empid, b.designationid, b.department, b.subdepartment FROM sf_target_master a, admin_users b WHERE 1=1 AND a.user_id=b.username AND (a.user_id IN ('".str_replace(",","','",$child)."') OR a.user_id='".$_SESSION["userid"]."') ".$subdeptqry." ".$deptqry."";
}
if ($user_id != '') {
    $sqldata.=" and a.user_id = '" . $user_id . "'";
}
if($selyear !=''){
	$sqldata	.= " AND a.year = '".$selyear."'";
}
if($selmonth !=''){
	$mnth = date("m", strtotime($selmonth."-".$selyear));
	$sqldata	.= " AND a.month = '".$mnth."'";
}
$sql = mysqli_query($link1, $sqldata);
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
        <th>Achivement</th>
        <th>Achivement %</th>
        <th>Create On</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$desig = getAnyDetails($row["designationid"],"designame","designationid","hrms_designation_master",$link1);
		$depart = getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1);
		$subdepart = getAnyDetails($row["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1);
		
		$res_data = mysqli_query($link1,"SELECT * FROM sf_target_data WHERE target_no='".$row["target_no"]."'");
		while($row_data = mysqli_fetch_assoc($res_data)){
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$row['name']; ?></td>
            <td><?=$row['oth_empid']; ?></td>
            <td><?=$row['user_id']?></td>
            <td><?=$desig?></td>
            <td><?=$depart?></td>
            <td><?=$subdepart?></td>
            <td><?=$row_data['year'];?></td>
            <td><?=$row_data['month'];?></td>
            <td><?=$row_data['prod_code'];?></td>
            <td><?=$row_data['task_name'];?></td> 
            <td><?=$row_data['remark']; ?></td>
            <td><?=$row_data['target_val']; ?></td>
            <td><?php
				$ach = 0;
				if($row_data['task_name']=="Dealer Visit"){
					//// calculate from old dealer vist
					$row_oldcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS olddealer FROM dealer_visit WHERE userid='".$row_data["user_id"]."' AND MONTH(visit_date) = '".$row_data["month"]."' AND YEAR(visit_date) = '".$row_data["year"]."' AND dealer_type='Old'"));
					//// calculate from new dealer vist
					$row_newcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS newdealer FROM dealer_visit WHERE userid='".$row_data["user_id"]."' AND MONTH(visit_date) = '".$row_data["month"]."' AND YEAR(visit_date) = '".$row_data["year"]."' AND dealer_type='New'"));
					echo "Old Visit -> ".$row_oldcnt["olddealer"];
					echo "<br>";
					echo "New Visit -> ".$row_newcnt["newdealer"];
					$ach = $row_oldcnt["olddealer"]+$row_newcnt["newdealer"];
				}
				else if($row_data['task_name']=="Feedback"){
					/// get feedback count
					$row_fb =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS feedback FROM query_master WHERE entry_by='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."'"));
					if($row_fb["feedback"]){ echo $ach = $row_fb["feedback"];}else{echo $ach = 0;}
					
				}
				else if($row_data['task_name']=="Sale Order"){
					/// get sale order count
					$row_so =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(req_qty) AS socnt FROM purchase_order_data WHERE po_no IN (SELECT po_no FROM purchase_order_master WHERE  create_by='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."' AND status IN ('Approved','Processed')) AND prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$row_data["prod_code"]."'))"));
					if($row_so["socnt"]){ echo $ach = $row_so["socnt"];}else{echo $ach = 0;}
				}
				else if($row_data['task_name']=="Collection"){
					/// get collection count
					$row_col =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(amount) AS collection FROM party_collection WHERE user_id='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."'"));
					if($row_col["collection"]){ echo $ach = $row_col["collection"];}else{echo $ach = 0;}	
				}
				else{
					echo $ach = 0;
				}
				?>    </td>
            <td><?php echo  round(($ach/$row_data['target_val'])*100)."%";?></td>
            <td><?=$row['create_date']; ?></td>
        </tr>
        <?php
        	$i+=1;
		}
    }
    ?>
</table>

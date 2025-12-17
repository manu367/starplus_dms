<?php
print("\n");
print("\n");
////// filters value/////
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$state = base64_decode($_REQUEST['state']);
$city = base64_decode($_REQUEST['city']);
$prod_cat = base64_decode($_REQUEST['prod_cat']);
$prod_subcat = base64_decode($_REQUEST['prod_subcat']);
$prod_brand = base64_decode($_REQUEST['prod_brand']);
$prod_code = base64_decode($_REQUEST['prod_code']);
$location_code = base64_decode($_REQUEST['location_code']);
////// filters value/////
$filter_str = "";
if($from_date !=''){
	$filter_str	.= " AND DATE(entry_date) >= '".$from_date."'";
}
if($to_date !=''){
	$filter_str	.= " AND DATE(entry_date) <= '".$to_date."'";
}
if($prod_cat !=''){
	$filter_str	.= " AND prod_catid = '".$prod_cat."'";
}
if($prod_subcat !=''){
	$filter_str	.= " AND prod_subcatid = '".$prod_subcat."'";
}
if($prod_brand !=''){
	$filter_str	.= " AND brand_id ='".$prod_brand."'";
}
if($prod_code !=''){
	$filter_str .= " AND prod_code ='".$prod_code."'";
}
if($state !=''){
	$filter_str .= " AND state ='".$state."'";
}
if($city !=''){
	$filter_str .= " AND city ='".$city."'";
}
if($location_code!=""){
	$location = "location_code='".$location_code."'";
}else{
	$acc_loc = getAccessLocation($_SESSION['userid'],$link1);
	$location = "location_code IN (".$acc_loc.")";
}
//////End filters value/////

$sql = "SELECT * FROM sale_registration WHERE 1 ".$filter_str." AND ".$location;
$res = mysqli_query($link1, $sql);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Serial No.</th>
        <th>Location Code</th>
        <th>Location Name</th>
        <th>Location City</th>
        <th>Location State</th>
        <th>Product Name</th>
        <th>Product Code</th>
        <th>Model Name</th>
        <th>Product Cat</th>
        <th>Product Sub-Cat</th>
        <th>Brand</th>
        <th>Invoice No.</th>
        <th>Invoice Date</th>
        <th>Customer Name</th>
        <th>Contact No.</th>
        <th>State</th>
        <th>City</th>
        <th>Pincode</th>
        <th>Address</th>
        <th>Status</th>
        <th>Entry By</th>
        <th>Entry Date</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
		$prodcat = explode("~",getAnyDetails($row["prod_subcatid"],"prod_sub_cat,product_category","psubcatid","product_sub_category",$link1));
		$locinfo = explode("~",getAnyDetails($row["location_code"],"name,city,state","asc_code","asc_master",$link1));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$row['serial_no'];?></td>
            <td><?=$row['location_code'];?></td>
            <td><?=$locinfo[0];?></td>
            <td><?=$locinfo[1];?></td>
            <td><?=$locinfo[2];?></td>
            <td><?=$row['prod_name'];?></td>
            <td><?=$row['prod_code'];?></td>
            <td><?=$row['model_name'];?></td>
            <td><?=$prodcat[1]?></td>
            <td><?=$prodcat[0]?></td>
            <td><?=getAnyDetails($row["brand_id"],"make","id","make_master",$link1)?></td>
            <td><?=$row['invoice_no'];?></td>
            <td><?=$row['invoice_date'];?></td>
            <td><?=$row['customer_name'];?></td>
            <td><?=$row['contact_no'];?></td>
            <td><?=$row['state']; ?></td>
            <td><?=$row['city']?></td>
            <td><?=$row['pincode'];?></td>
            <td><?=$row['address']; ?></td>
            <td><?=$row['status']; ?></td> 
            <td><?=getAnyDetails($row["entry_by"],"name","username","admin_users",$link1)?></td>
            <td><?=$row['entry_date']; ?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>

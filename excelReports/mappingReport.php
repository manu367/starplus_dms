<?php
print("\n");
print("\n");


///////  extract value//////////////////////////////////
$location = base64_decode($_REQUEST['user_id']);
$partyname = base64_decode($_REQUEST['partyname']);



if($location!= ''){
	$st = " uid  = '".$location."' ";							
		}else {							
	$st = "1";}	
	
if($partyname != ''){
$mappedcode = "mapped_code = '".$partyname."' ";
}else {
$mappedcode ="1";
}
	

////////////   fetching data from party ledger table//////////////////////////////////////////////////////////////////		
    $sql = mysqli_query($link1, "Select distinct(mapped_code) , uid  ,status from mapped_master where $st and $mappedcode  and  status = 'Y' order by mapped_code");
                           
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="35"><strong>S.No.</strong></td>
       <td><strong>Party  Name</th>
	   <td><strong>Party  State</th>
	   <td><strong>Party  City</th>
	   <td><strong>Party  Contact No.</th>
	   <td><strong>Party  Email Id</th>
		<td><strong>Mapped Location Name</th>
		<td><strong>Mapped Location State</th>
        <td><strong>Mapped Location City</th>
  		 <td><strong>Mapped Location Contact No.</strong></td>
		<td><strong>Mapped Location Landline No.</strong></td>
		<td><strong>Mapped Location Email Id</strong></td>
		<td><strong>Mapped Location  Communication Address</strong></td>
		<td><strong>Mapped Location  Dispatch/Delivery Address</strong></td>
		<td><strong>Mapped Location  Landmark</strong></td>
		<td><strong>Mapped Location Pincode</strong></td>
		<td><strong>Mapped Location TIN</strong></td>
		<td><strong>Mapped Location PAN No.</strong></td>
		<td><strong>Mapped Location CST No.</strong></td>
		<td><strong>Mapped Location GST No.</strong></td>
		<td><strong>Status</strong></td>
    </tr>
    <?php
	$i=1;
    while ($row = mysqli_fetch_assoc($sql)) {	
 $row_loc	= mysqli_fetch_array(mysqli_query($link1,"select * from asc_master where uid = '".$row['uid']."' "));
        ?>
        <tr>
           <td><?=$i?></td>   
			<td><?= getLocationDetails($row['mapped_code'],"name" ,$link1) ."(" . $row['mapped_code'].")" ; ?></td> 
			<td><?= getLocationDetails($row['mapped_code'],"state" ,$link1); ?></td>
			<td><?= getLocationDetails($row['mapped_code'],"city" ,$link1); ?></td>
			<td><?= getLocationDetails($row['mapped_code'],"phone" ,$link1); ?></td>
			<td><?= getLocationDetails($row['mapped_code'],"email" ,$link1); ?></td>       
            <td><?=getLocationDetails($row['uid'],"name" ,$link1) ."(".$row['uid'].")";  ;?></td>
			<td><?=$row_loc['state']?></td>
			<td><?=$row_loc['city']?></td>
			<td><?=$row_loc['phone']?></td>
			<td><?=$row_loc['landline']?></td>
			<td><?=$row_loc['email']?></td>
			<td><?=cleanData($row_loc['addrs'])?></td>
			<td><?=cleanData($row_loc['disp_addrs'])?></td>
			<td><?=$row_loc['landmark']?></td>	
			<td><?=$row_loc['picode']?></td>
			<td><?=$row_loc['vat_no']?></td>
			<td><?=$row_loc['pan_no']?></td>
			<td><?=$row_loc['cst_no']?></td>
			<td><?=$row_loc['gstin_no']?></td>								
			<td><?php if($row['status'] == 'Y') {echo "Active";} else { echo "Deactive";}?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>

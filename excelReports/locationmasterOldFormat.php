<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$locationstate=base64_decode($_REQUEST[locstate]);
$locationcity=base64_decode($_REQUEST[loccity]);
$locationtype=base64_decode($_REQUEST[loctype]);
$locationstatus=base64_decode($_REQUEST[locstatus]);
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="status='".$locationstatus."'";
}else{
	$loc_status="1";
}
//////End filters value/////
$sql_loc=mysqli_query($link1,"Select * from asc_master where $loc_state and $loc_city and $loc_type and $loc_status order by state, name asc")or die("er1".mysqli_error($link1));
////////////////// table header////
echo "S.No."."\t";
echo "Circle"."\t";
echo "State"."\t";
echo "City"."\t";
echo "Location Id"."\t";
echo "Location Name"."\t";
echo "Location Type"."\t";
echo "Mapped Group"."\t";
echo "Contact Person"."\t";
echo "Contact No."."\t";
echo "Landline No."."\t";
echo "Email Id"."\t";
echo "Communication Address"."\t";
echo "Dispatch/Delivery Address"."\t";
echo "Landmark"."\t";
echo "Pincode"."\t";
echo "TIN"."\t";
echo "PAN No."."\t";
echo "CST No."."\t";
echo "Service Tax No."."\t";
echo "Mapped Parent id"."\t";
echo "Status"."\t";
echo "Login Status"."\t";
echo "Remark"."\t";
print("\n"); 

$i=1;
while($row_loc = mysqli_fetch_array($sql_loc)){
echo $i."\t";
echo $row_loc['circle']."\t";
echo $row_loc['state']."\t";
echo $row_loc['city']."\t";
echo $row_loc['asc_code']."\t";
echo $row_loc['name']."\t";
echo $row_loc['id_type']."\t";
if($row_loc[group_id]!=''){  echo getGroupName($row_loc[group_id],$link1)."\t";}else{ echo "\t";}
echo $row_loc['contact_person']."\t";
echo $row_loc['phone']."\t";
echo $row_loc['landline']."\t";
echo $row_loc['email']."\t";
echo cleanData($row_loc['addrs'])."\t";
echo cleanData($row_loc['disp_addrs'])."\t";
echo $row_loc['landmark']."\t";
echo $row_loc['picode']."\t";
echo $row_loc['vat_no']."\t";
echo $row_loc['pan_no']."\t";
echo $row_loc['cst_no']."\t";
echo $row_loc['st_no']."\t";
echo getParentLocation($row_loc['asc_code'],$link1)."\t";
echo $row_loc['status']."\t";
echo $row_loc['login_status']."\t";
echo cleanData($row_loc['remark'])."\t";
print "\n";
$i+=1;		
}
?>
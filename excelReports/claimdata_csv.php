<?php
$fun_id = array("u"=>array(135)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

function cleanDataN($x)
{
	$str = preg_replace('/[\'"]+/', ' ', trim($x));
	return $str;
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=claimdata".date("His")."_".time().".csv");
header("Pragma: no-cache");
header("Expires: 0");

$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$party_code=base64_decode($_REQUEST['pcode']);
$status = base64_decode($_REQUEST['status']);
$claimtype = base64_decode($_REQUEST['claimtype']);

$accessloc = getAccessLocation($_SESSION['userid'],$link1);
####### filter value
if($party_code=='' )
{  
	$party = " party_id IN (".$accessloc.")";
}
else
{
	$party = " party_id='".$party_code."'";
}
if($from_date=='' || $to_date=='')
{
	$sql_date=" entry_date>='".date("Y-m-01")."' AND entry_date<='".$today."'";
}
else
{
	$sql_date=" entry_date>='".$from_date."' AND entry_date<='".$to_date."'";
}
if($status){
	$sts = " status='".$status."'";
}else{
	$sts = " 1";
}
if($claimtype){
	$clmtyp = " claim_type='".$claimtype."'";
}else{
	$clmtyp = " 1";
}
//////End filters value/////
echo '"'.'S.No'.'",';
echo '"'.'Party Id'.'",';
echo '"'.'Party Name'.'",';
echo '"'.'Claim Type'.'",';
echo '"'.'Claim No.'.'",';
echo '"'.'Claim Subject'.'",';
echo '"'.'Claim Description'.'",';
echo '"'.'Claim Date'.'",';
echo '"'.'Nos'.'",';
echo '"'.'Claim Amount'.'",';
echo '"'.'Status'.'",';
echo '"'.'Create Date'.'",';
echo '"'.'Create Time'.'",';
echo '"'.'Create By'.'",';
echo '"'.'Edit Date'.'",';
echo '"'.'Edit Time'.'",';
echo '"'.'Edit By'.'"';
print "\n";
$i=1;
////// calculate challan no.
$arr_bm = array();
$arr_party = array();
$arr_claimtyp = array();
$arr_status = array();
$arr_create = array();
$arr_edit = array();

$res_bm = mysqli_query($link1,"SELECT * FROM claim_master WHERE ".$party." AND ".$sql_date." AND ".$sts." AND ".$clmtyp);
while($row_bm = mysqli_fetch_assoc($res_bm)){
	$arr_bm[] = $row_bm['claim_no'];
	$arr_party[$row_bm['claim_no']] = $row_bm['party_id'];
	$arr_claimtyp[$row_bm['claim_no']] = $row_bm['claim_type'];
	$arr_status[$row_bm['claim_no']] = $row_bm['status'];
	$arr_create[$row_bm['claim_no']] = $row_bm['entry_by']."~".$row_bm['entry_date']."~".$row_bm['entry_time'];
	$arr_edit[$row_bm['claim_no']] = $row_bm['update_by']."~".$row_bm['update_date']."~".$row_bm['update_time'];
}
mysqli_free_result($res_bm);

$arr_location = array();
$arr_users = array();
/////// get data
$sql = mysqli_query($link1,"SELECT * FROM claim_data WHERE claim_no IN ('".implode("','",$arr_bm)."')")or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql)){	
	$entry_det = explode("~",$arr_create[$row_loc['claim_no']]);
	$edit_det = explode("~",$arr_edit[$row_loc['claim_no']]);
	
	$party_id = $arr_party[$row_loc['claim_no']];
	
	$partyname = "";
	$entryname = "";
	$editname = "";
	/////// get locationuser name
	if (array_key_exists($party_id, $arr_location)) {
		$partyname = explode("~",$arr_location[$party_id]);
	} else {
		$f_loc = getLocationDetails($party_id,"name,city,state",$link1);
		$partyname = explode("~",$f_loc);
		$arr_location[$party_id] = $f_loc;
	}
	/////// get create by name
	if (array_key_exists($entry_det[0], $arr_users)) {
		$entryname = $arr_users[$entry_det[0]];
	} else {
		$entryname = getAdminDetails($entry_det[0],"name",$link1);
		$arr_users[$entry_det[0]] = $entryname;
	}
	/////// get edit by name
	if (array_key_exists($edit_det[0], $arr_users)) {
		$editname = $arr_users[$edit_det[0]];
	} else {
		$editname = getAdminDetails($edit_det[0],"name",$link1);
		$arr_users[$edit_det[0]] = $editname;
	}
	
	echo '"'.$i.'"'.",";
	
	echo '"'.cleanDataN($party_id).'"'.',';
	echo '"'.cleanDataN(implode(",",$partyname)).'"'.',';
	
	echo '"'.cleanDataN($arr_claimtyp[$row_loc['claim_no']]).'"'.',';
	echo '"'.cleanDataN($row_loc['claim_no']).'"'.',';
	echo '"'.cleanDataN($row_loc['claim_subject']).'"'.',';
	echo '"'.cleanDataN($row_loc['claim_desc']).'"'.',';
	echo '"'.cleanDataN($row_loc['claim_date']).'"'.',';
	echo '"'.cleanDataN($row_loc['qty']).'"'.',';
	echo '"'.cleanDataN($row_loc['amount']).'"'.',';
	echo '"'.cleanDataN($arr_status[$row_loc['claim_no']]).'"'.',';
	echo '"'.cleanDataN($entry_det[1]).'"'.',';
	echo '"'.cleanDataN($entry_det[2]).'"'.',';
	echo '"'.cleanDataN($entryname).'"'.',';
	echo '"'.cleanDataN($edit_det[1]).'"'.',';
	echo '"'.cleanDataN($edit_det[2]).'"'.',';
	echo '"'.cleanDataN($editname).'"'.',';

print "\n";
$i+=1;		
}
mysqli_free_result($sql);
?>
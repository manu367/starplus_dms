<?php 
require_once("../config/config.php");
$msg='';
if($_REQUEST['str']=='del_lead')
{
	$del_lead=mysqli_query($link1,"update sf_lead_master set status='17', update_by='".$_SESSION['userid']."' where reference='".$_REQUEST['id']."'")or die(mysqli_error($link1));
	if($del_lead){
		dailyActivity($_SESSION['userid'],$_REQUEST['id'],"LEAD","CANCELLED",$ip,$link1,"");
		$msgg="Lead is cancelled successfully with reference <font style='size:4;'>".$_REQUEST['id']."</font>";
		header("Location:lead_list.php?msg=".$msgg."&sts=success&page=lead&status=".$_REQUEST['status']."".$pagenav);
		}
	else{}
}
?>
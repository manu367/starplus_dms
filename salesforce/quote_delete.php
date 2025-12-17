<?php 
require_once("../config/config.php");
$msg='';
if($_REQUEST['str']=='del_quote')
{
	$del_quote=mysqli_query($link1,"update sf_quote_master set status='17', update_by='".$_SESSION['userid']."' where quote_no='".$_REQUEST['id']."'")or die(mysqli_error($link1));
	if($del_quote){
		dailyActivity($_SESSION['userid'],$_REQUEST['id'],"QUOTE","CANCELLED",$ip,$link1,"");
		$msgg="Qupte is cancelled successfully with quote no.  <font style='size:4;'>".$_REQUEST['id']."</font>";
		header("Location:quote_list.php?msg=".$msgg."&sts=success&page=quote&status=".$_REQUEST['status']."".$pagenav);
		}
	else{}
}
?>
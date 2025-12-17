<?php 
require_once("../config/config.php");
$msg='';

////// Cancel Payment receive
if($_REQUEST['str']=='del_payment')
{
	
	
	
 $flag=true;
    $result=mysqli_query($link1,"update payment_receive set status='Cancelled' where doc_no='".$_REQUEST['id']."'  ");
 //// check if query is not executed
    if (!$result) {
      $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
 }
 
 //// insert into party ledger
 	$pay=mysqli_query($link1, "select amount, from_location, to_location from payment_receive where doc_no='".$_REQUEST['id']."' ");
	$prow1=mysqli_fetch_assoc($pay);
$flag=partyLedger($prow1['to_location'],$prow1['from_location'],$_REQUEST['id'],$today,$today, $currtime, $_SESSION['userid'],'RP Cancel', $prow1['amount'],'dr',$link1, $flag);
 
 ////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$_REQUEST['id'],"RP","Cancelled",$ip,$link1,$flag);
	
///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Payment Cancelled successfully  with ref. no.".$_REQUEST['id'];
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
  header("location:paymentlist.php?msg=".$msg."".$pagenav);
  exit;
}
?>
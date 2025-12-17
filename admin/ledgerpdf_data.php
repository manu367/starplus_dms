<?php
require_once("../config/dbconnect.php");
require_once("../includes/common_function.php");
 ///////  extract value//////////////////////////////////
$location = base64_decode($_REQUEST['user_id']);
$partyname = base64_decode($_REQUEST['partyname']);
$party = $_REQUEST['partynamedetails'];
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

if ($fromdate != '' or $todate != '') {
      $datefilter =" and entry_date BETWEEN '" . $fromdate . "' and '" . $todate . "'";
              }
else {
		$datefilter =" and entry_date BETWEEN '" . $today . "' and '" . $today . "'";
		} 
		
if($location != ''){
		$locationcode = "location_code='" .$location . "' " ;
	}else { $locationcode = "1";}
	
if($partyname){	
$variable = $partyname;}
else {
$variable = "1";
}

////////////   fetching data from party ledger table//////////////////////////////////////////////////////////////////		
$sql = mysqli_query($link1, "Select location_code, cust_id,doc_no , doc_date ,doc_type  ,amount ,cr_dr   from party_ledger where $locationcode and  $variable $datefilter and doc_no NOT IN (SELECT doc_no FROM party_ledger where doc_type = 'CANCEL CORPORATE INVOICE' ) order by doc_date ");


############ Number convert into words ##############
/*function number_to_words($number)
{

if ($number > 999999999)
{
throw new Exception("Number is out of range");
}
$Tn = floor($number / 100000000000);  trillion () 
$number -= $Tn * 100000000000;

$Bn = floor($number / 1000000000);  billion () 
$number -= $Bn * 1000000000;

$Cn = floor($number / 10000000);  Crore () 
$number -= $Cn * 10000000;

$Gn = floor($number / 1000000);  Millions (giga) 
$number -= $Gn * 1000000;

$ln = floor($number / 100000);  Lakh () 
$number -= $ln * 100000;

$kn = floor($number / 1000);  Thousands (kilo) 
$number -= $kn * 1000;
$Hn = floor($number / 100);  Hundreds (hecto) 
$number -= $Hn * 100;
$Dn = floor($number / 10);  Tens (deca) 
$n = $number % 10;  Ones 
$cn = round(($number-floor($number))*100);  Cents 
$result = "";

if ($Tn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($Tn) . " Trillion"; }

if ($Bn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($Bn) . " Billion"; }

if ($Cn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($Cn) . " Crore"; }

if ($Gn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($Gn) . " Million"; }

if ($ln)
{ $result .= (empty($result) ? "" : " ") . number_to_words($ln) . " Lakh"; }

if ($kn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($kn) . " Thousand"; }

if ($Hn)
{ $result .= (empty($result) ? "" : " ") . number_to_words($Hn) . " Hundred"; }

$ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
"Nineteen");
$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
"Seventy", "Eigthy", "Ninety");

if ($Dn || $n)
{
if (!empty($result))
{ $result .= " ";
}

if ($Dn < 2)
{ $result .= $ones[$Dn * 10 + $n];
}
else
{ $result .= $tens[$Dn];
if ($n)
{ $result .= "-" . $ones[$n];
}
}
}

if ($cn)
{
if (!empty($result))
{ $result .= ' and ';
}
$title = $cn==1 ? 'paisa': 'paise';
$result .= strtolower(number_to_words($cn)).' '.$title;
}

if (empty($result))
{ $result = "zero"; }

return $result;
}*/

#####################################################
?>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<!-- saved from url=(0067)http://122.160.166.107/jainaasso/invtemp2.asp?billid=36969&copies=1 -->
<HTML>
<HEAD>
<TITLE></TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<STYLE>
P.page {
	PAGE-BREAK-AFTER: always
}
BODY {
	FONT-SIZE: 1px;
	FONT-FAMILY: 'ARIAL'
}
TABLE {
	BORDER-RIGHT: medium none;
	BORDER-LEFT-COLOR: black;
	BORDER-TOP-COLOR: black;
	BORDER-BOTTOM: medium none;
	border-collapse: collapse;
}
TABLE.l {
	BORDER-TOP: medium none
}
TABLE.t {
	BORDER-LEFT: medium none
}
TABLE.none {
	BORDER-RIGHT: medium none;
	BORDER-TOP: medium none;
	BORDER-LEFT: medium none;
	BORDER-BOTTOM: medium none
}
.r
{
 border-collapse: collapse;	
}
TD.none {
	BORDER-RIGHT: medium none;
	BORDER-TOP: medium none;
	BORDER-LEFT: medium none;
	BORDER-BOTTOM: medium none
}
TD {
	BORDER-TOP: medium none;
	FONT-SIZE: 12pt;
	BORDER-BOTTOM-COLOR: black;
	BORDER-LEFT: medium none;
	BORDER-RIGHT-COLOR: black
}
TD.r {
	BORDER-BOTTOM: medium none
}
TD.b {
	BORDER-RIGHT: medium none
}
TD.l {
	BORDER-RIGHT: medium none
}
TD.bl {
	BORDER-RIGHT: medium none;
	BORDER-BOTTOM: thin outset
}
.color
{
	color:red;
}
@media Print {
.scrbtn {
	DISPLAY: none
}
/*  TABLE { page-break-after:auto }
  TR    { page-break-inside:avoid; page-break-after:auto }
  TD    { page-break-inside:avoid; page-break-after:auto }
  TBODY { display:table-header-group }
  tfoot { display:table-footer-group }*/
}
.style6 {
	font-family: "Times New Roman", Times, serif;
}
.style9 {
	font-size: 10pt;
	font-weight: bold;
}
div{

    /* Rotate div */
    -ms-transform: rotate(-7deg); /* IE 9 */
    -webkit-transform: rotate(-7deg); /* Chrome, Safari, Opera */
    transform: rotate(-7deg);
}
</STYLE>

</HEAD>
<BODY bottomMargin=0 leftMargin=0  onload=vbscript:window.print();>

<TABLE width="100%" height="40px" align="center" cellPadding=0 cellSpacing="1" style="border-style: solid;
    border-width: 1px 1px 0px 1px; text-align:justify;">
<tr>
<td align="right" style="padding-left:30px;"><strong>Party Ledger</strong></td>
<td></td>
</tr>
<tr> 
<td align="left" style="border-right:0px ! important;">
<img src="../img/inner_logo.png" title="logo" width="150" height="40" border="0" /><br>
</td>
<td align="right">
Statement Period <?php echo  dt_format($fromdate) . " to " . dt_format($todate) ;?> <br>
Party Name : <?php 
$name =explode("~",getLocationDetails($party,"name,state" ,$link1));
								 $vendor = explode("~",getVendorDetails($party,"name,state",$link1));
				if($name[0] != ''){ echo wordwrap($name[0], 25, "<br>", 1);} else if ($vendor[0] != ''){echo wordwrap($vendor[0], 25, "<br>", 1);} else {}
  ?><br>
Party Location : <?php if($name[1] != ''){ echo wordwrap($name[1], 10, "<br>", 1);} else if ($vendor[1] != ''){echo  
wordwrap($vendor[1], 10, "<br>", 1);

} else {}  ?>

</td>

</tr>
</TABLE>
<TABLE width="80%" align="center" cellPadding=0 cellSpacing="1" border ="1">
<tr>
                      <td align="center" width="40%"><strong>Document No.</strong></td>
                      <td align="center" width="40%"><strong>Document Type</strong></td>   
					  <td align="center" width="20%"><strong>Transaction Date</strong></td>                   
                      <td align="center" width="15%"><strong>Amount CR</strong></td>
					  <td align="center" width="15%"><strong>Amount DR</strong></td>
					  <td align="center" width="15%"><strong>Payment Remark</strong></td>
                    </tr>
                 
                     <?php
	$i=1;
    while ($row = mysqli_fetch_assoc($sql)) {
							//////  calculation for cr / dr /////////////////////////////////////////////////////////////////
							if ($row['cr_dr'] == "CR" || $row['cr_dr'] == "cr") { 
							$cr_amt = $row["amount"];  $dr_amt = "0" ;}
							else { $dr_amt = $row["amount"];  $cr_amt = "0";  }			
              $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from asc_master where uid='" .$row['location_code'] . "'"));
			  /////////////  fetch  remark and payment date from payment receive table///////////////////////////////////////////////
				$payment_details = mysqli_fetch_assoc(mysqli_query($link1,"select remark , payment_date from payment_receive where doc_no = '".$row['doc_no']."'  order by payment_date,doc_no  "));
				////  fetch approval remark from approval tabe //////////////////////////////////////////////////////
		//	$approval = mysqli_fetch_assoc(mysqli_query($link1,"select action_remark from approval_activities where ref_no = '".$row['doc_no']."' "));
        ?>
        <tr>	
            <td align="left">&nbsp;<?= $row['doc_no']; ?></td>
			<td align="left">&nbsp;<?php if ($row['doc_type'] == 'VPO') { echo "Purchase";} elseif($row['doc_type'] == 'RP') { echo "Payment Received"; }else {echo $row['doc_type']; }?></td>
			 <td align="center"><?php echo dt_format($row['doc_date']); ?></td>
			 <td align="center"><?= $cr_amt;?></td>
			<td align="center"><?= $dr_amt;?></td>   
			 <td align="center"><?= $payment_details['remark'];?></td>         	
        </tr>
        <?php
        $i+=1;
		$totcr+=$cr_amt;
		$totdr+=$dr_amt;
    }
    ?>


<tr>
<td colspan= "3" align="right"><strong>Total:&nbsp;</strong></td>
<td  align="center"><?php echo number_format($totcr,'2','.','');?></td>
<td align="center"><?php echo number_format($totdr,'2','.','');?></td>
<td></td>
</tr>
<tr>
<td colspan= "3" align="right"><strong>Balance:&nbsp;</strong></td>
<td align="center"><?php $balance = $totcr - $totdr;
 echo number_format($balance,'2','.','');?></td>
<td ></td>
<td ></td>
</tr>
<tr>
 <td style="vertical-align:center; padding-left:30px;" height="50" colspan="6"><?php  echo "This is System generated document, no signature is required."?></td>
                                      </tr>

</TABLE>

</BODY>
</HTML>


<?php
////// Function ID ///////
$fun_id = array("u"=>array(133,134,135,136,137)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid = base64_decode($_REQUEST['id']);
$sql_m = "SELECT * FROM claim_master WHERE claim_no = '".$docid."'";
$res_m = mysqli_query($link1, $sql_m);
$row = mysqli_fetch_assoc($res_m);
///// main location
$main_location = explode("~",getLocationDetails($row['party_id'],"name,city,state,addrs,pincode,email,phone,gstin_no",$link1));
$header = "PARTY CLAIM";
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Print Document</title>
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
</head>
<body>
<!--	<page size="A4" layout="portrait"></page>-->
	<page size="A4">
		<table class="table" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="35%">&nbsp;</td>
                <td width="30%" align="center"><?=$header?></td>
                <td width="35%">&nbsp;</td>
              </tr>
            </tbody>
    	</table>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" rowspan="4"><img src="../img/inner_logo.png"/></td>
                <td width="35%" rowspan="4">
                    <strong>Party Name:</strong>&nbsp;&nbsp;<?php echo $main_location[0]." , ".$main_location[1]." , ".$main_location[2];?><br/>
                    <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/>
                    <strong>State:</strong>&nbsp;&nbsp;<?=$main_location[2]?><br/>
                    <strong>Pincode:</strong>&nbsp;&nbsp;<?=$main_location[4]?><br/>
                    <strong>Email:</strong>&nbsp;&nbsp;<?=$main_location[5]?><br/>
                    <strong>Contact No.:</strong>&nbsp;&nbsp;<?=$main_location[6]?><br/>
                    <strong>GSTIN:</strong>&nbsp;&nbsp;<?=$main_location[7]?><br/></td>
                <td width="25%" height="50"><span role="presentation" dir="ltr">Document No.</span><div style="position:absolute"><strong><?=$row['claim_no']?></strong></div></td>
                <td width="25%" height="50"><span role="presentation" dir="ltr">Dated</span><div style="position:absolute"><strong><?=dt_format($row['entry_date']);?></strong></div></td>
              </tr>
              <tr>
                <td width="25%" height="50"><span role="presentation" dir="ltr">Claim Type</span><div style="position:absolute"><strong><?=$row['claim_type']?></strong></div></td>
                <td width="25%" height="50"><span role="presentation" dir="ltr">Total Amount</span><div style="position:absolute"><strong><?=$row['total_amount'];?></strong></div></td>
              </tr>
              <tr>
                <td height="50"><span role="presentation" dir="ltr">Entry By</span><div style="position:absolute"><?php echo getAnyDetails($row['entry_by'], "name","username","admin_users", $link1);?></div></td>
                <td height="50"><span role="presentation" dir="ltr">Status</span><div style="position:absolute"><?php echo $row['status'];?></div></td>
              </tr>
              <tr>
                <td colspan="2" height="50"><span role="presentation" dir="ltr">Remark</span><div style="position:absolute"><?php echo $row['remark'];?></div></td>
              </tr>
              
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> CLAIM DETAIL</strong></td>
              </tr>
           	</tbody>
      </table>
<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="25%">Subject</th>
                <th style="text-align:center" width="25%">Description</th>
                <th style="text-align:center" width="15%">Date</th>
                <th style="text-align:center" width="10%">Nos.</th>
                <th style="text-align:center" width="20%">Amount</th>
          	</tr>
		  </thead>
          <tbody>
           <?php
            $i=1;
            $sum_qty=0;			
            $sum_subtotal = 0;
            $invdata_sql="SELECT * FROM claim_data WHERE claim_no='".$row["claim_no"]."'";
            $invdata_res=mysqli_query($link1,$invdata_sql);
            while($invdata_row=mysqli_fetch_assoc($invdata_res)){                          
				?>
				  <tr>
					<td><?=$i?></td>
					<td><?=$invdata_row['claim_subject']?></td>
					<td><?=$invdata_row['claim_desc']?></td>
					<td align="center"><?=dt_format($invdata_row['claim_date'])?></td>
					<td align="right"><?=$invdata_row['qty']?></td>
				    <td align="right"><?=$invdata_row['amount']?></td>
			    </tr>
				<?php
					$sum_qty+=$invdata_row['qty'];
					$sum_subtotal+=$invdata_row['amount'];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="3"><strong>Total</strong></td>
					<td style="text-align:right">&nbsp;</td>
                    <td style="text-align:right"><strong><?=$sum_qty?></strong></td>
					<td style="text-align:right"><strong><?=$sum_subtotal?></strong></td>
				</tr>
                <tr>
                  <td colspan="6"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($sum_subtotal) . " Only"; ?></td>
                </tr>
			 </tbody>
        </table>
   <table class="table" border="1">
           <tbody>         
              <tr>
                <td colspan="8" align="left" style="border-bottom:none" height="50"><strong>Term and Condition</strong><br/>
                 <p>*Disputes,If any Shall be Subjected to Court of Uttar Pradesh only.<br></p><br/>
                 <strong>Declaration</strong>
                 <p>
We declare that all particulars are true and correct.</p></td>
              </tr>
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Authorised Signatory)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>              
          </tbody>
   	  </table>
      <div align="center">This print is electronically generated.</div>
    </page>
</body>
</html>
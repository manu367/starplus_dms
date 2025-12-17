<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
$sql_m = "SELECT * FROM stock_movement_master WHERE doc_no = '".$docid."'";
$res_m = mysqli_query($link1, $sql_m);
$row = mysqli_fetch_assoc($res_m);
///// main location
$main_location = explode("~",getLocationDetails($row['main_location'],"name,city,state,addrs,pincode,email,phone,gstin_no",$link1));
/// move from party
$billfrom=getLocationDetails($row['from_location'],"name,city,state",$link1);
$explodevalf=explode("~",$billfrom);
if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($row['from_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
/// move to party
$billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
$explodeval=explode("~",$billto);
if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getAnyDetails($row['to_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
$header = "Stock Movement";
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
                <td width="15%" rowspan="3"><img src="../img/inner_logo.png"/></td>
                <td width="35%" rowspan="3">
                    <strong>Main Location:</strong>&nbsp;&nbsp;<?php echo $main_location[0]." , ".$main_location[1]." , ".$main_location[2];?><br/>
                    <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/>
                    <strong>State:</strong>&nbsp;&nbsp;<?=$main_location[2]?><br/>
                    <strong>Pincode:</strong>&nbsp;&nbsp;<?=$main_location[4]?><br/>
                    <strong>Email:</strong>&nbsp;&nbsp;<?=$main_location[5]?><br/>
                    <strong>Contact No.:</strong>&nbsp;&nbsp;<?=$main_location[6]?><br/>
                    <strong>GSTIN:</strong>&nbsp;&nbsp;<?=$main_location[7]?><br/></td>
                <td width="25%"><span role="presentation" dir="ltr">Document No.</span><div style="position:absolute"><strong><?=$row['doc_no']?></strong></div></td>
                <td width="25%"><span role="presentation" dir="ltr">Dated</span><div style="position:absolute"><strong><?=dt_format($row['entry_date']);?></strong></div></td>
              </tr>
              <tr>
                <td><span role="presentation" dir="ltr">Entry By</span><div style="position:absolute"><?php echo getAnyDetails($row['entry_by'], "name","username","admin_users", $link1);?></div></td>
                <td><span role="presentation" dir="ltr">Status</span><div style="position:absolute"><?php echo $row['status'];?></div></td>
              </tr>
              <tr>
                <td colspan="2"><span role="presentation" dir="ltr">Remark</span><div style="position:absolute"><?php echo $row['entry_remark'];?></div></td>
              </tr>
              <tr>
                <td colspan="2" rowspan="2"><span role="presentation" dir="ltr"><strong>Move From:</strong>&nbsp;&nbsp;<?php echo str_replace("~",",",$fromparty);?><br/>
            <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/><br/><br/><br/><br/></td>
                <td><span role="presentation" dir="ltr">Approval By</span><div style="position:absolute"><?php echo getAnyDetails($row['app_by'], "name","username","admin_users", $link1);?></div></td>
                <td><span role="presentation" dir="ltr">Approval Date</span><div style="position:absolute"><?php if($row['app_date']!="0000-00-00 00:00:00" && $row['app_date']!=""){echo dt_format($row['app_date']);}?></div></td>
              </tr>
              <tr>
                <td colspan="2"><span role="presentation" dir="ltr">Approval Remark</span><div style="position:absolute"><?php echo $row['app_remark'];?><br/></div>                  <span role="presentation" dir="ltr">&nbsp;</span></td>
              </tr>
              
              <tr>
                <td colspan="2"><span role="presentation" dir="ltr"><strong>Move To:</strong>&nbsp;&nbsp;<?php echo str_replace("~",",",$toparty);?><br/>
            <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/><br/><br/><br/><br/></td>
                <td colspan="2"><span role="presentation" dir="ltr"><strong class="text-danger">Move Type:</strong>&nbsp;&nbsp;<?php echo getStockTypeName($row['move_stocktype']);?></span><span role="presentation" dir="ltr">&nbsp;</span><span role="presentation" dir="ltr">&nbsp;</span></td>
              </tr>
              
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
           	</tbody>
      </table>
<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="45%">Product</th>
                <th style="text-align:center" width="15%">Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="20%">Value</th>
            </tr>
		  </thead>
          <tbody>
           <?php
            $i=1;
            $sum_qty=0;			
            $sum_subtotal = 0;
            $invdata_sql="SELECT * FROM stock_movement_data WHERE doc_no='".$row["doc_no"]."'";
            $invdata_res=mysqli_query($link1,$invdata_sql);
            while($invdata_row=mysqli_fetch_assoc($invdata_res)){                          
                $partdet = explode("~",getAnyDetails($invdata_row["partcode"],"productname,model_name","productcode","product_master",$link1));
				?>
				  <tr>
					<td><?=$i?></td>
					<td><?=$partdet[0].", ".$partdet[1].' ('.$invdata_row["partcode"].')';?></td>
					<td style="text-align:right"><?=$invdata_row['qty']?></td>
					<td style="text-align:right"><?=$invdata_row['price']?></td>
					<td style="text-align:right"><?=$invdata_row['value']?></td>
				</tr>
				<?php
					$sum_qty+=$invdata_row['qty'];
					$sum_subtotal+=$invdata_row['value'];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="2"><strong>Total</strong></td>
					<td style="text-align:right"><strong><?=$sum_qty?></strong></td>
					<td style="text-align:right">&nbsp;</td>
					<td style="text-align:right"><strong><?=$sum_subtotal?></strong></td>
				</tr>
                <tr>
                  <td colspan="4"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($sum_subtotal) . " Only"; ?></td>
                </tr>
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>         
              <tr>
                <td colspan="8" align="left" style="border-bottom:none" height="50"><strong>Term and Condition</strong><br/>
                 <p>*Disputes,If any Shall be Subjected to Court of Haryana only.<br></p><br/>
                 <strong>Declaration</strong>
                 <p>
We declare that this invoice shows the actual price of the goods
described and that all particulars are true and correct.</p></td>
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
      <div align="center">This Invoice is electronically generated.</div>
    </page>
</body>
</html>
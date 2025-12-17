<?php 
//$docid = $invno;
//// mail script
////// get location details
$billing_details =  mysqli_fetch_array(mysqli_query($link1,"select * from billing_master where challan_no='".$getid."' "));

$toemail = "sulakshna@candoursoft.com";
$subject = "Dispatch Invoice";
$msgg.="<html><head><title>Automatic Email</title></head><body style='margin:0; padding:10px 0 0 0;' bgcolor='#F8F8F8'>
			<table align='center' border='1' cellpadding='0' cellspacing='0' width='800' style='border-collapse: collapse; border-spacing: 0px 0px; box-shadow: 1px 0 1px 1px #B8B8B8;' bgcolor='#FFFFFF' class='lable'><tr>
					<td align='left' width='250' style='padding: 5px 5px 5px 5px; border-bottom:none; border-right:none;'><img src='http://test.cansale.in/cansaledms/img/inner_logo.png'/></td>
				</tr>";
	
			   $msgg.= "<tr><td colspan='3' align='center' style='border-top:none;'><u><strong>".Invoice."</strong></u></td></tr><tr>
					<td colspan='3' align='center' style='border-bottom:none; border-right:none; border-top:none; border-left:none;'><table width='100%' border='1' cellspacing='0' cellpadding='5' class='lable' style='border-collapse:collapse'>
					  <tr>
						<td width='20%' style='border-top:none; border-left:none;'><strong>".Invoice No."</strong></td>
						<td width='30%' style='border-top:none;'>".$billing_details['challan_no']."</td>
						<td width='20%' style='border-top:none;'><strong>".Invoice Date."</strong></td>
						<td width='30%' style='border-top:none; border-right:none;'>".$billing_details['sale_date']."</td>
					  </tr>
					  <tr>
						<td style='border-left:none;'><strong>".Invoice from."</strong></td>
						<td>".str_replace("~",",",getLocationDetails($billing_details['from_location'],"name,city,state",$link1))."</td>
						<td><strong>".Invoice To."</strong></td>
						<td style='border-right:none;'>".str_replace("~",",",getLocationDetails($billing_details['to_location'],"name,city,state",$link1))."</td>
					  </tr>
					  <tr>
					  <td style='border-left:none;'><strong>".Delivery Address."</strong></td>
						<td >".$billing_details['deliv_addrs']."</td>
						<td ><strong>".Dispatch Address."</strong></td>
						<td style='border-right:none;'>".$billing_details['disp_addrs']."</td>				
					  </tr>
					  <tr>
						<td style='border-left:none;border-bottom:none;'><strong>".Docket No."</strong></td>
						<td style='border-bottom:none;'>".$docketno."</td>
						<td style='border-bottom:none;'><strong>".Courier Name."</strong></td>
						<td style='border-right:none;border-bottom:none;'>".$couriername."</td>
					  </tr>
					   <tr>
						<td style='border-left:none;border-bottom:none;'><strong>".Dispatch Date."</strong></td>
						<td style='border-bottom:none;'>".$dispatchdate."</td>
						<td style='border-bottom:none;'><strong>".Dispatch Remark."</strong></td>
						<td style='border-right:none;border-bottom:none;'>".$remark."</td>
					  </tr>
					</table>
					</td>
				</tr>
				<tr>
					<td colspan='3' style='border-bottom:none; border-right:none; border-top:none; border-left:none;'><table width='100%' border='1' cellspacing='0' cellpadding='5' class='lable' style='border-collapse:collapse'>
					  <tr>
						<td colspan='10' style='border-left:none;border-right:none;'><strong style='font-size:14px'>".PRODUCT DETAIL."</strong></td>
					  </tr>
					  <tr>
						<td width='3%' style='border-left:none;'><strong>#</strong></td>
						<td width='17%'><strong>".Product."</strong></td>
						<td width='10%'><strong>".Bill Qty."</strong></td>
						<td width='10%'><strong>".Price."</strong></td>
						<td width='10%'><strong>".Value."</strong></td>
						<td width='10%'><strong>".Discount/Unit."</strong></td>
						<td width='10%'><strong>".Tax(%)."</strong></td>
						<td width='10%'><strong>".Tax."</strong></td>
						<td width='10%'><strong>".Total."</strong></td>
					  </tr>";
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$getid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
			$msgg .="<tr>
						 <td style='border-left:none;'>".$i."</td>
				<td>".$proddet[0]." (".$proddet[1].")"."</td>
				<td>".$podata_row['qty']."</td>
				<td>".currencyFormat($podata_row['price'])."</td>
				<td>".currencyFormat($podata_row['value'])."</td>    
				<td>".currencyFormat($podata_row['discount']."</td>
				<td>".$invdata_row['tax_name']."(".$invdata_row['tax_per'].")"."</td>
				<td>".currencyFormat($podata_row['tax_amt'])."</td>
				<td style='border-right:none;'>".currencyFormat($podata_row['totalvalue'])."</td> 
					  </tr>";
			$value+=$podata_row['totalvalue'];                                                

			$i++;
			}
			$msgg.="<tr>
						<td colspan='9' align='right' style='border-left:none;'><strong>".Sub Total."</strong></td>
						<td style='border-right:none;'>".currencyFormat($value)."</td>
					  </tr>
					  <tr>
						<td colspan='9' align='right' style='border-left:none;'><strong>".Grand Total."</strong></td>
						<td style='border-right:none;'>".currencyFormat($value)."</td>
					  </tr>
					  <tr>
						<td colspan='10' align='left' style='border-left:none; border-right:none;'><strong>".Amount in Words." </strong>&nbsp;&nbsp;&nbsp;&nbsp;".number_to_words($value)." EURO Only</td>
					  </tr>
					  <tr>
						<td colspan='10' align='left' style='border-left:none; border-right:none;'><strong>".Remark.": </strong>&nbsp;&nbsp;&nbsp;&nbsp;".$billing_details['billing_rmk']."</td>
					  </tr>   
					</table></td>
				</tr>
			</table>
			<div align='center' style='width:800'><span style='color:#FF0000'>** Note: This is an Auto Generated Email. **</span></div>
</body>
</html>";
$msgg.="<br /><br /><br />With Regards,<br />cansaledms<br/>";
$msgg.="<br /><img src='http://test.cansale.in/cansaledms/img/inner_logo.png'/>";


// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: EMAIL ALERT <info@candoursoft.com>' . "\r\n";
$headers .= 'Cc: sulakshna.bhardwaj@gmail.com' . "\r\n";
//$headers .= 'Bcc: ravi@candoursoft.com' . "\r\n";
$output = mail($toemail,$subject,$msgg,$headers);
?>

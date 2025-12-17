<?php
////// Function ID ///////
$fun_id = array("u"=>array(121)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$docid = base64_decode($_REQUEST['id']);
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$loc = explode('~', getLocationDetails($po_row['from_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode,pincode", $link1));

if ($po_row['type'] == "RETAIL") {
    $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,category,category,category,customername", $link1));
    if ($to[0] == "") {
        $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
    }
} else {
    $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
}
if($po_row['document_type'] == "Receipt Note") {
	if($loc[3]!=$to[3]){
		$header = "TAX INVOICE";
	}else{
		$header = "Receipt Note";
	}
} else {
	$header = "TAX INVOICE";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
    <HEAD>
        <TITLE>Document Printing</TITLE>
        <META http-equiv=Content-Type content="text/html; charset=UTF-8">
        <link href="../css/font-awesome.min.css" rel="stylesheet">
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
                BORDER-BOTTOM: medium none
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
            TD.none {
                BORDER-RIGHT: medium none;
                BORDER-TOP: medium none;
                BORDER-LEFT: medium none;
                BORDER-BOTTOM: medium none
            }
            TD {
                BORDER-TOP: medium none;
                FONT-SIZE: 8pt;
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
            @media Print {
                .scrbtn {
                    DISPLAY: none
                }
            }
            .style6 {
                font-family: "Courier New", Courier, monospace
            }
            .style8 {
                font-family: "Courier New", Courier, monospace;
                font-weight: bold;
            }
            .style9 {
                font-size: 10pt;
                font-weight: bold;
            }
        </STYLE>
    </HEAD>
<BODY bottomMargin=0 leftMargin=40 topMargin=0 onload=vbscript:window.print()>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  	<TABLE width=800 align="center" cellPadding=0 cellSpacing=0>
    	<TBODY>
        	<TR>
           		<TD vAlign=top>
                	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
                    	<TBODY>
                        	<TR>
                            	<TD>
                                    <span class="style9">
                                    <img src="../img/inner_logo.png" style="float:left;margin-top:5px; ">
                                    <div style="padding-left:450px;"><?php echo $header;?></div> </span>
                               	</TD>
                           	</TR>
                 		</TBODY>
                	</TABLE>
                    <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
						<TBODY>               
               				<TR vAlign=top>
                            	<TD colspan="2" >&nbsp;</td>
                                <TD colspan="3"><FONT size=2><B>Seller</B></TD>             
                           	</TR>
                            <TR vAlign=top>
								<TD colspan="2" style="padding-right:5px;">
                                	<FONT size=2><B>Receipt Note.:</B>&nbsp;&nbsp;<B><?= $po_row['challan_no'] ?></B></FONT><br>
                                    <FONT size=2><B>Receipt Note Date:</B>&nbsp;<B><?= dt_format($po_row['entry_date']) ?></B></FONT><br/>
									<FONT size=2><B>Status:</B>&nbsp;<B><?=$po_row['status']?></B></FONT>
                               	</TD>
								<TD colspan="2" style="padding-left:5px;">
                                	<FONT size=2><B>Name & Address :</B><B><span class="lable"><br><?= $loc[7]?></span></B><br><span class="lable"><?= $loc[1]?></span></FONT><BR>
                                    <FONT size=2><span class="lable"><?= $loc[3]?></span><BR>
                                    <strong>GST No.:</strong><span class="lable"><?= $loc[5] ?></span><BR> 
                                    <strong>State.:</strong><span class="lable"><?= $loc[3] ?></span><BR>
                                    <strong>State Code:</strong><span class="lable"><?= $loc[8] ?></span><BR>
                                    <strong>PAN No.:</strong><span class="lable"><?= $loc[4] ?></span></FONT>                                    
                               	</TD>
                                <TD width="29%" colspan="2" vAlign=Top>
                                	<FONT size=2><B>Transporter Name & Address</B>    &nbsp;&nbsp; :<br><?=$po_row['logistic_person'];?><BR></FONT><br>
                                    <strong>Docket No:</strong><span class="lable"><?=$po_row['docket_no']?> 
                                            </span><BR>
											 <strong>Docket Date:</strong><span class="lable"> <?=$po_row['dc_date']?> 
                                            </span><BR>
											<strong>E-Ways No.:</strong><span class="lable">  <?=$po_row['ewayno']?> 
                                            </span>
											
											
								</TD>
                        	</TR>
							<TR vAlign=top>
                                  <td colspan="2" ><FONT size=2><B>Details of Receiver (Bill To)</B></td>
                                  <TD colspan="2"><FONT size=2><B>Details of Consignee (Ship To)</B></TD> 
								      <TD colspan="2"><FONT size=2><B>Details</B></TD>             
                           	</TR>
                            <TR vAlign=top>
                          <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Name & Address :</B><span class="lable">  <strong><?= $to[7] ?></strong>
                                            </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?=$to[1];?>
                                            </span><BR>
                                            <span class="lable">
<?=$to[3];?>
                                            </span><BR>
                                            <strong>GST No.:</strong><span class="lable">  <?= $to[5] ?>
                                            </span><BR>
											 <strong>State.:</strong><span class="lable">  <?= $to[3] ?>
                                            </span><BR>
											 <strong>State Code:</strong><span class="lable">  <?= $to[8] ?>
                                            </span><BR>
											<strong>PAN No.:</strong><span class="lable">
                                       <?= $to[4] ?>
                                        </span>                                        </FONT></TD>
                                    <TD colspan="2">
                                        <FONT size=2><B>Name & Address :</B><span class="lable">  <strong><?= $to[7] ?></strong>
                                      </span></FONT><BR>
                                        <FONT size=2><span class="lable">
                                            <?= $to[1] ?>
                                            </span><BR>
                                            <span class="lable">
                                             <?= $to[3] ?>
                                            </span><BR>
                                            <strong>GST No.:</strong><span class="lable">  <?= $to[5] ?>
                                            </span> <BR>
											 <strong>State.:</strong><span class="lable">  <?= $to[3] ?>
                                            </span><BR>
											 <strong>State Code:</strong><span class="lable">  <?= $to[8] ?>
                                            </span><BR>
											<strong>PAN No.:</strong><span class="lable">
                                       <?= $to[4] ?>
                                            </span>                                        </FONT>                                  
							</TD>
											<TD valign="top">
											<B>Transportation Mode : <?=$po_row['trans_mode']?></B><br>                               
                                        <strong>Vehicle No./CourierName:</strong><span class="lable">
                                             <?=$po_row['logistic_person']?>  
                                            </span><BR>
											 <strong>LR No./Docket No.:</strong><span class="lable"> <?=$po_row['docket_no']?>
                                            </span><BR>
											<strong>Docket Date:</strong><span class="lable"><?=$po_row['dc_date']?>  
                                            </span>																				
											</TD>
                                </TR>
                        
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TBODY>
                                <TR vAlign=top>
                                    <TD>
                                    <table cellspacing=0 cellpadding=3 width="100%" border=1>
                                            <tr style="FONT-WEIGHT: bold" valign=top>
                                                <td width="7%"  align=center ><font size=2><strong>S.No.</strong></font></td>
												<td width="7%"  align=center ><font size=2><strong>Item Code</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Description Of Goods/ Services</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>HSN/SAC</strong></font></td>
                                                <td width="6%" align=center><font size=2><strong>Qty</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>UOM</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>Rate/Unit</strong></font></td>
                                                <td width="14%"  align=center><font size=2><strong>Discount</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Taxable Value</strong></font></td>
												  <?php if($loc[3]==$to[3]){ ?>
                                                <td width="8%"  align=center><font size=2><strong>SGST/UGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>SGST/UGST Amount</strong></font></td>
                                                <td width="8%"  align=center><font size=2><strong>CGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>CGST Amount</strong></font></td>
												     <?php }else{?>
                                                <td width="8%"  align=center><font size=2><strong>IGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>IGST Amount</strong></font></td>
												 <?php }?>
                                                <td width="13%"  align=center><font size=2><strong>Total Amount (Inc Tax)</strong></font></td>
                                                <td width="13%"  align=center><font size=2><strong>Scheme</strong></font></td>
                                                <td width="13%"  align=center><font size=2><strong>Scheme Code</strong></font></td>
                                            </tr>
                                            <?php
//-------------Getting invoice details from billing data---------------------//
                                            $rs = mysqli_query($link1, "select * from billing_model_data where challan_no='$docid'");
                                            $i = 1;
                                            $counter+=1;
                                            $hight = 350 - $counter * 14;
                                            $total = 0;
                                            $discount = 0;
                                            $value = 0;
                                            $tot_tax = 0;
                                            $grand_total = 0;
                                            while ($row = mysqli_fetch_array($rs)) {
                                                $product_name = explode("~", getProductDetails($row['prod_code'], "productname,model_name", $link1));
                                                $hsn_code = explode("~", getHsnCode($row['prod_code'], $link1));                                             
                                                $val = $row['qty']*$row['price'];
                                                $taxable = $val-($row['discount']);
                                                ?>
                                                <tr>
                                                    <td align=center><span class="style6"><strong><?= $i ?></strong></span></td>
													<td align=center><span class="style6"><strong><?=$row['prod_code']?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?php if($row["prod_cat"]=="C"){ echo $row["combo_name"];}else{ echo $product_name[0]." (".$product_name[1].")";}?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $hsn_code[0]; ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['qty']; ?></strong></span></td>
                                                    <td align=right>EA</td>
                                                    <td align=right><span class="style6"><strong><?=$row['price'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?=$row['discount']?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= currencyFormat($taxable) ?></strong></span></td>
													  <?php if($loc[3]==$to[3]){ ?>
                                                    <td align=right><span class="style6"><strong><?= $row['sgst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['sgst_amt'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['cgst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['cgst_amt'] ?></strong></span></td>
													  <?php }else{?>
                                                    <td align=right><span class="style6"><strong><?= $row['igst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['igst_amt'] ?></strong></span></td>
													 <?php }?>
                                                    <td align=right><span class="style6"><strong><?= $row['totalvalue'] ?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?php if($row['scheme_name']!=""){ echo $row['scheme_name']; }else{ echo ""; } ?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?php if($row['scheme_code']!=""){ echo $row['scheme_code']; }else{ echo ""; } ?></strong></span></td>
                                                </tr>
                                                <?php
                                                $total+=$row['qty'];
                                                $price+=$row['price'];
                                                $value+=$row['totalvalue'];                                                
                                                $discount = $row['discount'];
												$totaltaxable+=$taxable; 
												$totcgst+=$row['cgst_amt'];
												$totsgst+=$row['sgst_amt'];
												$totigst+=$row['igst_amt'];

                                                $i++;
                                            }
                                            $grand_total+=$after_discount + $tot_tax;
                                            ?>
                                            <tr height="<?= $hight ?>px">
                                                <td>&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
												 <?php if($loc[3]==$to[3]){ ?>
                                                <td align="center">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
												  <?php }else{?>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
												 <?php }?>
												  <td align="right">&nbsp;</td>
                                                  <td align="left">&nbsp;</td>
                                                  <td align="left">&nbsp;</td>
                                            </tr>
                                           <!-- <tr>
                                                <td height="20" colspan="14" style="border-bottom:none"><div align="right"><B>Sub Total</B></div></td>
                                                <td style="border-bottom:none" align="right"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($value); ?></td>
                                            </tr>
                                            <tr>
                                                <td height="20" colspan="14"><div align="right"><strong><span class="style6">&nbsp;</span></strong><strong><span class="style6"></span>&nbsp;</strong><B>Round Off</B></div></td>

                                                <td  style="border-bottom:none" align="right"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($po_row['round_off']); ?></td>
                                            </tr>-->
											<?php	if($loc[3]==$to[3]){ $colspn=4;
											
											 }else{ $colspn=4; } 
											
											?>
                                            <tr>
                                                <td height="30" colspan="<?=$colspn?>"  align="right"><B>Total</B></td>
                                                <td align="right" ><?php echo currencyFormat($total); ?></td>
												<td align="right" ></td>
												<td align="right" ></td>
                                                
                                                <td align="right" ></td>

												<td align="right"  ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($totaltaxable); ?></td>
                                                <td align="right" ></td>
												<?php if($loc[3]==$to[3]){ ?>
												<td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($totsgst); ?></td>
												<td align="right" ></td>																								
												<td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($totcgst); ?></td>
												
												 <?php }else{
												 ?>
										
												<td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($totigst); ?></td>
												 <?php }?>
                                                <?php	if($loc[3]==$to[3]){ $colspn=1; }else{ $colspn=2;} ?>
												<td align="right" colspan="<?=$colspn?>" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($value); ?></td>
												<td align="right" ></td>
                                            </tr>
											<?php	if($loc[3]==$to[3]){ $colspn1=7; }else{ $colspn1=6;} ?>	
											<tr>
											<?php	if($loc[3]==$to[3]){ $colspn2=7; }else{ $colspn2=3;} ?>	
                                     <td colspan="<?=$colspn2?>"><B>Total Weight</B></td>
							   
                                                <td align="left"  colspan="<?=$colspn1?>"><B>Total Quantity:</B><?php echo currencyFormat($total); ?></td>
                                                
												<td align="center" colspan="<?=$colspn1?>"><B>Total Amount(INR):<?php echo currencyFormat($value); ?></B></td>
										
								
												
                                            </tr>
                                            
                                            <tr>
											<?php	if($loc[3]==$to[3]){ $colspn2=7; }else{ $colspn2=3;} ?>	
                               <td colspan="<?=$colspn2?>"><B>TCS </B><?php echo $po_row["tcs_per"]." %    ".$po_row["tcs_amt"];?></td>
							   
                                                <td align="left"  colspan="<?=$colspn1?>"><B>Round Off:  </B>  <?php echo $po_row["round_off"]; ?></td>
                                                
												<td align="center" colspan="<?=$colspn1?>"><B>Grand Total(INR):<?php echo $po_row["tcs_amt"]+$po_row["round_off"]+$value; ?></B></td>
										
								
												
                                            </tr>
                                        </table>
                                    </TD>
                                </TR>
                            </TBODY>
                        </TABLE>
				
                        <TABLE height=50 cellSpacing=0 cellPadding=2 width="100%" border=1>
                            <TBODY>							
                                <TR>
								<td>                                 
                              <B>Amount in Words <i class="fa fa-inr" aria-hidden="true"></i> </B><?php echo number_to_words($value) . " Only"; ?><br/>                          
								<B>Total Tax in Words <i class="fa fa-inr" aria-hidden="true"></i> </B><?php echo number_to_words($totcgst+$totsgst+$totigst) . " Only"; ?><br/>
                                        &nbsp;
									<br>
									<br>
									<p>*Disputes,If any Shall be Subjected to Court of <?=$loc[3]?> only.<br>
									*Goods once sold Shall not be taken Back.   <br>            
									*Certified that the particular above are true and correct.</p>   
					
				<div style="float:left;">
									 <strong>Registered Office:</strong><?=$loc[1]?><BR><?=$loc[3]?>-<?=$loc[9]?>. </strong><br><strong>CIN No.: <?=$loc[6]?>,PAN No.: <?=$loc[4]?>,GSTIN: <?=$loc[5]?>  &nbsp;<br>
									 <strong>Sender Remark:<?php echo $po_row['billing_rmk']; ?></strong>  &nbsp;
								  </div> &nbsp;  <div style="float:right;"><strong >For <?=strtoupper($loc[7])?></strong>
									  <br>
									  <br>
									  <br>
									
									  <strong >Pre Authorized Signatory</strong><br>
									    <BR>
									  <BR>
									  <BR>
									   <strong>Receiver Remark</strong> <br>
									   <strong>Received Date & Time</strong><br>
									   <strong>Signature</strong><br>
									   <strong>Remark</strong>
									    
									 </div>		
									 
								     
			
								       
                         </td>
                         </TR>
						 <tr>
                                <td style="vertical-align:center; padding-left:25px;" height="50"><strong><?php  echo "This Invoice is electronically generated and no signature is required."?></strong></td>
                                            </tr>
       				 </TBODY>
                        </TABLE>
                    </TD>
                </TR>
                </TBODY>
        </TABLE>
    </BODY>
</HTML>


<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);

$po_sql = "SELECT * FROM billing_master where challan_no='".$docid."'";

$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

$loc = explode('~', getLocationDetails($po_row['from_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode,pincode", $link1));
if ($po_row['type'] == "Customer Retail Return") {
    $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,emailid,pincode,category,customername", $link1));
    if ($to[0] == "") {
        $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,emailid,pincode,category,customername", $link1));
    }
} else {
    $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,emailid,pincode,category,customername", $link1));
}
if($po_row['document_type'] == "Delivery Challan") {
$header = "Delivery Challan";
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
									
                                            <div style="padding-left:450px;"><?php echo $header;?></div> </span></TD>
                                </TR>
                            </TBODY>
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
                            <TBODY>               
               
                                <TR vAlign=top>
                                  <td colspan="2" >&nbsp;</td>
                                  <TD colspan="3"><FONT size=2><B>Seller</B></TD>             
                                </TR>
                                <TR vAlign=top>
								<td colspan="2" style="padding-right:5px;"><FONT size=2><B>Invoice No.</B>
                                            &nbsp;&nbsp;<B><?= $po_row['challan_no'] ?></B></FONT><br>
                                        <FONT size=2><B>Invoice Date</B>
                                  &nbsp;<B><?= dt_format($po_row['entry_date']) ?></B></FONT><br>
								   <FONT size=2><B>Status :</B>
                                  &nbsp;<B><?=$po_row['status']?></B></FONT>
</td>
								  
                                    <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Name & Address :</B><B><span class="lable">
                                                <br><?= $loc[7] ?>
                                                </span></B><br><span class="lable">
<?= $loc[1] ?>
                                            </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?= $loc[3] ?>
                                            </span><BR>

                                            <strong>GST No.:</strong><span class="lable">
                                                <?= $loc[5] ?>
                                            </span><BR>
											 <strong>State.:</strong><span class="lable">
                                                <?= $loc[3] ?>
                                            </span><BR>
											<strong>State Code:</strong><span class="lable">
                                                <?= $loc[8] ?>
                                            </span><BR>
											
											<strong>PAN No.:</strong><span class="lable">
<?= $loc[4] ?>
                                            </span></FONT>                                    </TD>
                                    <TD width="29%" colspan="2" vAlign=Top ><FONT size=2><B>Transporter Name & Address</B>
                                            &nbsp;&nbsp; :<br><?=$po_row['logistic_person'];?><BR></FONT><br>
                                        <strong>Docket No:</strong><span class="lable">
                                              <?=$po_row['docket_no']?> 
                                            </span><BR>
											 <strong>Docket Date:</strong><span class="lable">
                                               <?=$po_row['dc_date']?> 
                                            </span><BR>
											<strong>E-Ways No.:</strong><span class="lable">
                                                <?=$po_row['ewayno']?> 
                                            </span>
											
											
								  </TD>
                                </TR>
                                <TR vAlign=top>
							  <TR vAlign=top>
                                  <td colspan="2" ><FONT size=2><B>Details of Receiver (Bill To)</B></td>
                                  <TD colspan="2"><FONT size=2><B>Details of Consignee (Ship To)</B></TD> 
								      <TD colspan="2"><FONT size=2><B>Details</B></TD>             
                              </TR>
                          <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Name & Address :</B><span class="lable">
                                                <strong><?= $to[7] ?></strong>
                                            </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?=$to[1];?>
                                            </span><BR>
                                            <span class="lable">
<?=$to[3];?>
                                            </span><BR>
                                            <strong>Contact No.:</strong><span class="lable">
                                                <?= $to[0] ?>
                                            </span><BR>
											 <strong>State.:</strong><span class="lable">
                                                <?= $to[3] ?>
                                            </span><BR>
											<strong>Email Id :</strong><span class="lable">
<?= $to[4] ?>
                                        </span>                                        </FONT></TD>
                                    <TD colspan="2">
                                        <FONT size=2><B>Name & Address :</B><span class="lable">
                                                <strong><?= $to[7] ?></strong>
                                      </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?= $to[1] ?>
                                            </span><BR>
                                            <span class="lable">
<?= $to[3] ?>
                                            </span><BR>
                                            <strong>Contact No.:</strong><span class="lable">
                                                <?= $to[0] ?>
                                            </span> <BR>
											 <strong>State.:</strong><span class="lable">
                                                <?= $to[3] ?>
                                            </span><BR>
											<strong>Email Id:</strong><span class="lable">
<?= $to[4] ?>
                                            </span>                                        </FONT>                                  
							</TD>
											<TD valign="top">
											<B>Transportation Mode : <?=$po_row['trans_mode']?></B><br>                               
                                        <strong>Vehicle No./CourierName:</strong><span class="lable">
                                             <?=$po_row['logistic_person']?>  
                                            </span><BR>
											 <strong>LR No./Docket No.:</strong><span class="lable">
                                               <?=$po_row['docket_no']?>
                                            </span><BR>
											<strong>Docket Date:</strong><span class="lable">
                                              <?=$po_row['dc_date']?>  
                                            </span>
											
											
											
											</TD>
                                </TR>
                        
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TBODY>
                                <TR vAlign=top>
                                    <TD><table cellspacing=0 cellpadding=3 width="100%" border=1>
                                            <tr style="FONT-WEIGHT: bold" valign=top>
                                                <td width="7%"  align=center ><font size=2><strong>S.No.</strong></font></td>
												<td width="7%"  align=center ><font size=2><strong>Item Code</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Description Of Goods/ Services</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>HSN/SAC</strong></font></td>
                                                <td width="6%" align=center><font size=2><strong>Qty</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>UOM</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>Rate/Unit</strong></font></td>
                                                <td width="14%"  align=center><font size=2><strong>Discount</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Rate/Unit (Inc. Tax)</strong></font></td>
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
                                                $product_name = explode("~", getProductDetails($row['prod_code'], "productname,productcolor", $link1));
                                                $hsn_code = explode("~", getHsnCode($row['prod_code'], $link1));                                             
                                                $val = $row['qty']*$row['price'];
                                                $taxable = $val-$row['discount'];
                                                ?>
                                                <tr>
                                                    <td align=center><span class="style6"><strong><?= $i ?></strong></span></td>
													<td align=center><span class="style6"><strong><?=$row['prod_code']?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?= $product_name[0].' | '.$product_name[1] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $hsn_code[0]; ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['qty']; ?></strong></span></td>
                                                    <td align=right>EA</td>
                                                    <td align=right><span class="style6"><strong><?=$row['price'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?=$row['discount'] ?></strong></span></td>
                                                    <?php ####### Caluclate TAX AMT for per UNit
													$taxableunit= ($row['price']- $row['discount']);
													
													if($loc[3]==$to[3]){
													$tax_per=($row['sgst_per']+ $row['cgst_per']);	
													}
													else{
													$tax_per=($row['igst_per']);	
													}
													
													$tax_amt= (($taxableunit*$tax_per)/100);
													
													$include_tax=($taxableunit+ $tax_amt);
													 ?>
                                                    <td align=right><span class="style6"><strong><?=currencyFormat($include_tax)?></strong></span></td>
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
                                                  
                                            </tr>
                                          
											<?php	if($loc[3]==$to[3]){ $colspn=4;
											
											 }else{ $colspn=4; } 
											
											?>
                                            <tr>
                                                <td height="30" colspan="<?=$colspn?>"  align="right"><B>Total</B></td>
                                                <td align="right" ><?php echo currencyFormat($total); ?></td>
												<td align="right" ></td>
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
												
                                            </tr>
											<?php	if($loc[3]==$to[3]){ $colspn1=5;  $colspannew = 10; }else{ $colspn1=5; $colspannew = 8;} ?>	
											<tr>
												
                               
							   
                                                <td align="right"  colspan="<?=$colspn1?>"><B>Total Quantity:</B><?php echo currencyFormat($total); ?></td>
                                                
												<td align="right" colspan="<?=$colspannew?>"><B>Total Amount(INR):<?php echo currencyFormat($value);?></B></td>
										
								
												
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
									<p>*Disputes,If any Shall be Subjected to Court of Delhi only.<br>
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
						 <tr>
                                <td style="vertical-align:center; padding-left:25px;" height="50"><strong><?php  echo "This Invoice is electronically generated and no signature is required."?></strong></td>
                                            </tr>
                                </TR>
        </TBODY>
                        </TABLE>
                    </TD>
                </TR>
        </TABLE>
    </BODY>
</HTML>


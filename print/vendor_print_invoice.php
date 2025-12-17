<?php
////// Function ID ///////
$fun_id = array("u"=>array(44)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid = base64_decode($_REQUEST[id]);
$po_sql = "SELECT * FROM vendor_order_master where po_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$loc = explode('~', getVendorDetails($po_row['po_from'], "phone,address,city,state,name,gstin_no", $link1));
if($loc[0]==""){
	$loc = explode('~', getLocationDetails($po_row['po_from'], "phone,addrs,city,state,name,gstin_no", $link1));	
}

if ($po_row['type'] == "RETAIL") {
    $to = explode('~', getCustomerDetails($po_row['po_to'], "contactno,address,city,state,category,category,category,customername", $link1));
    if ($to[0] == "") {
        $to = explode('~', getLocationDetails($po_row['po_to'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
    }
} else {
    $to = explode('~', getLocationDetails($po_row['po_to'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name,statecode", $link1));
}

$header = "Delivery Challan";

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
                                  <TD colspan="4" ><FONT size=2><B>Seller</B></TD>             
                                </TR>
                                <TR vAlign=top>
								<td colspan="2" style="padding-right:5px;"><FONT size=2><B>Invoice No.</B>
                                            &nbsp;&nbsp;<B><?= $po_row['po_no'] ?></B></FONT><br>
                                        <FONT size=2><B>Invoice Date</B>
                                  &nbsp;<B><?= dt_format($po_row['entry_date']) ?></B></FONT><br>
                                        <FONT size=2><B>Invoice Status</B>
                                  &nbsp;<B><?= $po_row['status'] ?></B></FONT></td>
								  
                                    <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Name & Address :</B><B><span class="lable">
                                                <br><?= $loc[4] ?>
                                                </span></B><br><span class="lable"><?= $loc[1] ?>
                                            </span></FONT><BR>
                                        <FONT size=2><strong>Phone No.: </strong><span class="lable"><?= $loc[0] ?>
                                            </span><BR>

                                            <?php /*?><strong>GST No.:</strong><span class="lable">
                                                <?= $loc[5] ?>
                                            </span><BR><?php */?>
                                            <strong>City:</strong><span class="lable">
                                                <?= $loc[2] ?>
                                            </span><BR>
											 <strong>State.:</strong><span class="lable">
                                                <?= $loc[3] ?>
                                            </span><BR>										
                                    </TD>
                                </TR>
                                <TR vAlign=top>
							  <TR vAlign=top>
                                  <td colspan="2" ><FONT size=2><B>Details of Receiver (Bill To)</B></td>
                                  <TD colspan="2"><FONT size=2><B>Details of Consignee (Ship To)</B></TD>            
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
                                            <?php /*?><strong>GST No.:</strong><span class="lable">
                                                <?= $to[5] ?>
                                            </span><BR><?php */?>
											 <strong>State.:</strong><span class="lable">
                                                <?= $to[3] ?>
                                            </span><BR>
											 <strong>State Code:</strong><span class="lable">
                                                <?= $to[8] ?>
                                            </span><BR>
											<strong>PAN No.:</strong><span class="lable">
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
                                            <?php /*?><strong>GST No.:</strong><span class="lable">
                                                <?= $to[5] ?>
                                            </span> <BR><?php */?>
											 <strong>State.:</strong><span class="lable">
                                                <?= $to[3] ?>
                                            </span><BR>
											 <strong>State Code:</strong><span class="lable">
                                                <?= $to[8] ?>
                                            </span><BR>
											<strong>PAN No.:</strong><span class="lable">
<?= $to[4] ?>
                                            </span>                                        </FONT>                                  
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
                                                <td width="7%"  align=center ><font size=2><strong>HSN</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Description Of Goods/ Services</strong></font></td>
                                                <td width="6%" align=center><font size=2><strong>Qty</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>UOM</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>Rate/Unit</strong></font></td>
                                                <td width="13%"  align=center><font size=2><strong>Total Amount</strong></font></td>
                                                <td width="13%"  align=center><font size=2><strong>Scheme</strong></font></td>
                                                <td width="13%"  align=center><font size=2><strong>Scheme Code</strong></font></td>
                                            </tr>
                                            <?php
//-------------Getting invoice details from billing data---------------------//
                                            $rs = mysqli_query($link1, "select * from vendor_order_data where po_no='$docid'");
                                            $i = 1;
                                            $counter+=1;
                                            $hight = 350 - $counter * 14;
                                            $total = 0;
                                            $value = 0;
                                            $grand_total = 0;
                                            while ($row = mysqli_fetch_array($rs)) {
                                                $product_name = explode("~", getProductDetails($row['prod_code'], "productname,productcolor,hsn_code", $link1));                                                $val = $row['req_qty']*$row['po_price'];
                                                ?>
                                                <tr>
                                                    <td align=center><span class="style6"><strong><?= $i ?></strong></span></td>
													<td align=center><span class="style6"><strong><?=$row['prod_code']?></strong></span></td>
                                                    <td align=center><span class="style6"><strong><?=$product_name[2]?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?= $product_name[0].' | '.$product_name[1] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['req_qty']; ?></strong></span></td>
                                                    <td align=right><?= $row['uom']; ?></td>
                                                    <td align=right><span class="style6"><strong><?=$row['po_price'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $val ?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?php if($row['scheme_name']!=""){ echo $row['scheme_name']; }else{ echo ""; } ?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?php if($row['scheme_code']!=""){ echo $row['scheme_code']; }else{ echo ""; } ?></strong></span></td>
                                                </tr>
                                                <?php
                                                $total+=$row['req_qty'];
                                                $price+=$row['po_price'];
                                                $value+=$val;                                                
                                               
                                                $i++;
                                            }
                                            $grand_total+=$value;
                                            ?>
                                            <tr height="<?= $hight ?>px">
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="left">&nbsp;</td>
                                            </tr>
                                          											
                                            <tr>
                                                <td height="30" colspan="4"  align="right"><B>Total</B></td>
                                                <td align="right" ><?php echo currencyFormat($total); ?></td>												
												<td align="right"  >&nbsp;</td>
                                                <td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($price); ?></td>
                                                <td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($value); ?></td>
                                                <td align="right" colspan="2" >&nbsp;</td>
                                            </tr>
											<?php	if($loc[3]==$to[3]){ $colspn1=3; }else{ $colspn1=3;} ?>	
											<tr>
											<?php	if($loc[3]==$to[3]){ $colspn2=4; }else{ $colspn2=4;} ?>	
                               					<td colspan="<?=$colspn2?>"><B>Total Weight</B></td>
                                                <td align="left"  colspan="<?=$colspn1?>"><B>Total Quantity:</B><?php echo currencyFormat($total); ?></td>
												<td align="center" colspan="<?=$colspn1?>"><B>Total Amount(INR):<?php echo currencyFormat($value); ?></B></td>
										
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
								
                                        &nbsp;
									<br>
									<br>
									<p>*Disputes,If any Shall be Subjected to Court of Haryana only.<br>
									*Goods once sold Shall not be taken Back.   <br>            
									*Certified that the particular above are true and correct.</p>   
					
				<div style="float:left;">
									 <strong>Corporate Office:</strong>Eastman Auto & Power Limited,<BR> 572,Udyog Vihar,Phase - V<BR>
									 Gurgaon - 122016. INDIA
									 <BR>
									 <strong>Registered Office:</strong>Eastman Auto & Power Limited,<BR> 572,Udyog Vihar,Phase - V<BR>
									 Gurgaon - 122016. INDIA </strong><br><strong><br>
									   
									 <strong>www.eaplworld.com</strong> 
									 <br>
									 <strong>Sender Remark:<?php echo $po_row['billing_rmk']; ?></strong>  &nbsp;
								  </div> &nbsp;  <div style="float:right;"><strong >For Eastman Auto & Power Limited</strong>
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


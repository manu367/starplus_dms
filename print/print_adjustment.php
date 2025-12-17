<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST[id]);
$po_sql = "SELECT * FROM  payment_receive where doc_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$loc = explode('~', getLocationDetails($po_row['from_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name", $link1));
echo $loc[7];
if ($po_row['type'] == "RETAIL") {
    $to = explode('~', getCustomerDetails($po_row['to_location'], "contactno,address,city,state,category,category,category,customername", $link1));
    if ($to[0] == "") {
        $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name", $link1));
    }
} else {
    $to = explode('~', getLocationDetails($po_row['to_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name", $link1));
}

if($po_row['adjustment_type'] == 'CR'){
$adjust_type = "CREDIT NOTE";
}else {
$adjust_type = "DEBIT NOTE";
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
        <TABLE width=800  align="center" cellPadding=0 cellSpacing=0>
            <TBODY>
                <TR>
                    <TD vAlign=top>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
                            <TBODY>
                                <TR>
                                    <TD >
                                        <span class="style9" >
                                            <img src="../img/inner_logo.png" style="float:right;margin-top:5px; ">								
                                            <div style="padding-right:400px; padding-left:10px"><?php echo $adjust_type;?></div> </span></TD>
                                </TR>
								<tr><td>
								<br><br/>
								<div align="right"><FONT size=2><strong>Anee Bullion & Industries Pvt. Ltd, </strong><br>
											<strong> H-36 Ground Floor Sector - 63 Noida</strong><br>
											<strong>UP 201301</strong></FONT>			
											
											<br><br/>
											</div>
											</td>	
											</tr>
                            </TBODY>
							<br><br/>
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=5 width="100%" border=1>
                            <TBODY>               
  				          <TR vAlign=top>
							  <TR vAlign=top>
                                  <td  colspan="2"><FONT size=2><B><?php echo $adjust_type;?> To :</B></td>
                                  <TD colspan="3"><FONT size=2><B><?php echo $adjust_type;?> Detail :</B></TD> 
						       
                              </TR>
                          <TD colspan="2" style="padding-left:3px;"><FONT size=2><B>Name & Address :</B><span class="lable">
                                            <?= wordwrap($loc[7],20,"<br>\n",TRUE)."(".$po_row['from_location'].")";
											
											?>
                                            </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?=$loc[1];?>
                                            </span><BR>
                                          </TD>
                                    <TD  colspan="3" >
                                        <FONT size=2><B>Date :</B><span class="lable">
                                                <?= dt_format($po_row['payment_date']);  ?>
                                      </span></FONT><BR><br/>
                                            <strong><?php echo $adjust_type;?> No:</strong><span class="lable">
                                                <?= $po_row['doc_no']?>
                                            </span> <BR><br/>
							
				                                                          
							</TD>
							
                                </TR>
                        
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TBODY>
                                <TR vAlign=top>
                                    <TD><table cellspacing=0 cellpadding=3 width="100%" border=1>
                                            <tr style="FONT-WEIGHT: bold" valign=top>
											<td width="22%"  align=center><font size=2><strong>Description</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Adjustment Type</strong></font></td>										
                                                <td width="13%"  align=center><font size=2><strong>Total Amount</strong></font></td>
                                            </tr>
                                            <?php
                                                ?>
                                                <tr>
												<td align=center><span class="style6"><strong><?=$po_row['remark']?></strong></span></td>
													<td align=center><span class="style6"><strong><?=$po_row['adjustment_type']?></strong></span></td>
													
                                                    <td align=right><span class="style6"><strong><?= $po_row['amount'] ?></strong></span></td>
                                                    
                                                </tr>
                                            <tr>
                                                <td height="30" colspan="2"  align="right"><B>Total Credit/ Debit :</B></td>
												<td align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($po_row['amount']); ?></td>
                                            </tr>
											<tr>
                                           <td colspan = "3" style="vertical-align:center; padding-left:25px;" height="50"><strong><?php  echo "This is system generated note, no signature is required."?></strong></td>
                                            </tr>
							
											
                                        </table>
                                    </TD>
                                </TR>
                            </TBODY>
                        </TABLE>
				
                      
                    </TD>
                </TR>
        </TABLE>
    </BODY>
</HTML>


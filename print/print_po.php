<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM purchase_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
 $loc=explode('~',getLocationDetails($po_row['po_from'],"phone,addrs,city,state,pan_no,cst_no,st_no,name,pincode",$link1));
$to=explode('~',getLocationDetails($po_row['po_to'],"phone,addrs,city,state,pan_no,cst_no,st_no,name,pincode",$link1));
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Document Printing</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
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
                <div style="margin-left:300px;">&nbsp;&nbsp;&nbsp;<?php  echo $loc[7]  ;?><br/>&nbsp;&nbsp;&nbsp;<?php  echo $loc[1]  ;?><br/>&nbsp;&nbsp;&nbsp;<?php echo "PURCHASE ORDER"
;?></div> </span></TD>
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
            <TR vAlign=top>
               <TD  colspan="2" style="padding-left:5px;">
			  <FONT size=2><B>Ship From(Consignor) :</B><br/><br/><B><span class="lable">
                <?=$loc[7]?>
                </span></B><br><span class="lable">
                <?=$loc[1]?><br>
                </span><span class="lable">
                <?=$loc[3]?>
                </span></FONT><BR>
                <FONT size=2><b>CST N0.:</b><span class="lable">
                <?=$loc[5]?>
                </span><BR>
                </FONT>
			  <FONT size=2><b>ST N0.:</b><span class="lable">
                <?=$loc[6]?>
                </span><BR>
                </FONT>
              </TD>
              <TD width="49%" colspan="2" vAlign=Top><FONT size=2><B>PO No. </B> <B><span class="lable">
                <?=$po_row['po_no']?>
                </span></B></FONT><br/>
			  <FONT size=2><B>PO Date </B>
              &nbsp;&nbsp;<B><?= dt_format($po_row['requested_date'])?></B></FONT></TD>
            </TR>
            <TR vAlign=top>
               <TD colspan="2" style="padding-left:5px;">
			 <FONT size=2><B>Bill To:</B><BR><BR><span class="lable">
                <strong><?=$to[7]?></strong>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$to[1]?>
                </span><BR>
                <span class="lable">
                <?=$to[3]?>
                </span><BR>
                </FONT>
				<FONT size=2><b>CST N0.:</b><span class="lable">
                <?=$to[5]?>
                </span><BR>
                </FONT>
				<FONT size=2><b>ST N0.:</b><span class="lable">
                <?=$to[6]?>
                </span><BR>
                </FONT>
				
			  </TD>
			  <TD colspan="2"><FONT size=2><B>Ship To:</B><BR><BR><span class="lable">
                <strong><?=$to[7]?></strong>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$to[1]?>
                </span><BR>
                <span class="lable">
                <?=$to[3]?>
                </span><BR>
                </FONT>
				<FONT size=2><b>CST N0.:</b><span class="lable">
                <?=$to[5]?>
                </span><BR>
                </FONT>
				<FONT size=2><b>ST N0.:</b><span class="lable">
                <?=$to[6]?>
                </span><BR>
                </FONT>
               </TD>
              
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
          <TBODY>
            <TR vAlign=top>
              <TD><table cellspacing=0 cellpadding=3 width="100%" border=1>
                    <tr style="FONT-WEIGHT: bold" valign=top>
                        <td width="7%" align=center height="25"><font size=2><strong>S.No.</strong></font></td>
                      <td width="23%" align=center><font size=2><strong>Description Of Goods</strong></font></td>
                      <td  width="11%"align=center><font size=2><strong>Qty</strong></font></td>
                      <td width="10%" align=center><font size=2><strong>Price</strong></font></td>
					  <td width="16%" align=center><font size=2><strong>Value</strong></font></td>
					  <td width="15%" align=center><font size=2><strong>Discount/Unit</strong></font></td>
					  <td width="18%" align=center><font size=2><strong>Amount</strong></font></td>
                    </tr>
                    <?php
				//-------------Getting purchase order data ---------------------//

$rs=mysqli_query($link1,"select * from purchase_order_data where po_no='$docid'");
$i=1;
$counter+=1;
$hight=350-$counter*14;	
$total=0;
$value=0;

while($row=mysqli_fetch_array($rs)){
$product_name=explode("~",getProductDetails($row['prod_code'],"productname",$link1));	
?>
                    <tr>
                      <td align=center><span class="style6"><strong><?=$i?></strong></span></td>
                      <td align=left><span class="style6"><strong><?=$product_name[0]?></strong></span></td>
                      <td align=right><span class="style6"><strong><?=$row['req_qty'];?></strong></span></td>
                      <td align=right><span class="style6"><strong><i class="fa fa-inr" aria-hidden="true"></i> <?=$row['po_price']?></strong></span></td>
					   <td align=right><span class="style6"><strong><?=$row['po_value'] ?></strong></span></td>
					   <td align=right><span class="style6"><strong><?=$row['discount'] ?></strong></span></td>
					   <td align=right><span class="style6"><strong><?=$row['totalval'] ?></strong></span></td>
                       </tr>	

                    <?php
$total+=$row['req_qty'];
$value+=$row['po_value'];
$i++;
}
$dis=$po_row['discount'];
$grand_total=$value-$dis;
?>

  <tr height="<?=$hight?>px">
    <td>&nbsp;</td>
    <td  align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
     <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
       <tr>
	   <td height="20" colspan="6" style="border-bottom:none"><div align="right"><B>Sub Total</B></div></td>
	   <td style="border-bottom:none" align="right"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($value) ;?></td>
      </tr>
					<tr>
                    <td height="20" colspan="6" ><div align="right"><strong><span class="style6">&nbsp;</span></strong><strong><span class="style6"></span>&nbsp;</strong><B>Total Discount</B></div></td>
                    
                    <td style="border-bottom:none" align="right" ><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($dis) ;?></td>
                    </tr>
					
					
					<tr>
                    <td height="30" colspan="2" style="border-bottom:none" align="right"><B>Total Qty</B></td>
                    <td align="right" style="border-bottom:none"> <?php echo currencyFormat($total);?></td>
                    <td colspan="3" align="right" style="border-bottom:none"><strong><span class="style6">&nbsp;</span></strong><strong><span class="style6"></span>&nbsp;</strong><B>Total Amount</B></td>
                    <td align="right" style="border-bottom:none"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo currencyFormat($grand_total) ;?></td>
                    </tr>
                </table></TD>
            </TR>
			 </TBODY>
        </TABLE>
        <TABLE height=50 cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
            <TR>
              <TD width="416" rowspan="8" valign="top" style="padding-left:5px;">&nbsp;<FONT size=2><B>Amount in Words <i class="fa fa-inr" aria-hidden="true"></i> </B><?php echo number_to_words($grand_total)." Only";?></FONT><br/><br/>
              &nbsp;<FONT size=2>Remarks  : <?=$po_row['remark']?></FONT>
              </TD>
              <TD width="224" height="23" align="right" style="border-right:none;padding-left:45px;"><strong>Total Amount: </strong></TD>
              <TD width="138" height="23" align="right"><span class="style8"><i class="fa fa-inr" aria-hidden="true"></i> <?=currencyFormat($grand_total);?></span></TD>
            </TR>
            <TR>
              <TD height="50" colspan="3" vAlign=top align="right"><FONT size=2><span class="lable"><strong><?=$loc[7]?></strong></span></FONT>&nbsp;&nbsp;<BR>
                <BR>
                <BR>
				 <BR>
                <BR>
                Authorised Signatory&nbsp;&nbsp;</TD>
            </TR>
          </TBODY>
        </TABLE>
		</TD>
    </TR>
  </TBODY>
</TABLE>
</BODY>
</HTML>

<?php
require_once("../config/config.php");

$so=mysqli_query($link1,"select * from sf_lead_master where lid='".base64_decode($_REQUEST['id'])."'");
$irow=mysqli_fetch_assoc($so);

#####################################################
?>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- saved from url=(0067)http://122.160.166.107/jainaasso/invtemp2.asp?billid=36969&copies=1 -->
<HTML>
<HEAD>
<TITLE>Document Printing</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
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
      <TD vAlign=top><TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
          <TBODY>
            <TR>
          
              <TD colspan="2" align="center" style="border-bottom:none"><span class="style9">
              <img src="../img/inner_logo.png" style="float:left;margin-top:5px; margin-left:5px;">
                <?php //if($row_tax[document_type]=='tax_inv'){ echo "TAX INVOICE";} elseif($row_tax[document_type]=='stn'){ echo "STOCK TRANSFER(STN)";} else{ echo "INVOICE";}
				echo "Lead";//if($row_tax[document_type]=='stn'){ echo "STOCK TRANSFER NOTE";}else{ echo "TAX/RETAIL INVOICE/DELIEVERY CHALLAN";}//$row_tax[tax_header];}
				 ?></span></TD>
                   
            </TR>
            <TR>
              <TD width="47%" height="35" align=left style="border-bottom:none; border-right:none">&nbsp;<FONT size=2><strong>Lead No : <?=$irow['reference'];?></strong></FONT></TD>
              <TD width="53%" align=left style="border-bottom:none"><FONT size=2><strong>Date : <?=dt_format($irow['tdate']);?></strong></FONT></TD>
            </TR>
			  <TR>
              <TD width="47%" height="35" align=left style="border-bottom:none; border-right:none">&nbsp;<FONT size=2><strong>Product Name : <?=$irow['productname'];?></strong></FONT></TD>
              <TD width="53%" align=left style="border-bottom:none"><FONT size=2><strong>Product Code : <?=$irow['productcode'];?></strong></FONT></TD>
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
            <TR vAlign=top>
              <TD width="47%" colspan="2"><FONT size=2><B>Party :</B><span class="lable">
                <strong><?=$irow["partyid"]?></strong>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$irow["party_address"]?>
                </span><BR>
                <span class="lable">
                <?=$irow["party_city"]?>
                </span><BR>
                <span class="lable">
                <?=$irow["party_state"]?>
                </span></FONT></FONT>
              </TD>
              <TD width="106%" colspan="2" vAlign=Top><FONT size=2><B><u>Sales Executive :</u></B><br>
              <?=ucwords(getAdminDetails($irow['sales_executive'],"name",$link1))?></FONT></TD>
            </TR>
            <!--<TR align="center">
              <TD><FONT size=2><B>Reference No.</B></FONT></TD>
              <TD><FONT size=2><B>Dispatch Through</B></FONT></TD>
              <TD><FONT size=2><B>Other Reference(s).</B></FONT></TD>
              <TD><FONT size=2><B>Destination</B></FONT></TD>
            </TR>-->
            <!--<TR >
              <TD style="border-bottom:none">&nbsp;</TD>
              <TD style="border-bottom:none">&nbsp;</TD>
              <TD style="border-bottom:none">&nbsp;</TD>
              <TD style="border-bottom:none">&nbsp;</TD>
            </TR>-->
          </TBODY>
        </TABLE>
        <TABLE height=81 cellSpacing=0 cellPadding=0 width="100%" border=0>
          <TBODY>
            <TR vAlign=top>
              <TD height="79"><table cellspacing=0 cellpadding=3 width="100%" border=1>
                  <tbody>
                    <tr style="FONT-WEIGHT: bold" valign=top>
                      <td width="5%" align=center height="25"><font size=2>Party Name</font></td>
                      <td width="12%" align=center><font size="2">Party Address</font></td>
                      <td width="9%" align=center><font size="2">Status</font></td>
                      <td width="9%" align=center><font size=2>Lead Source</font></td>
                    </tr>
                    <?php

$i=1;
$counter+=1;
$hight=350-$counter*14;	
?>
                    <tr>
                      <td align=center><span class="style6"><strong><?=$irow['partyid'];?></strong></span></td>
                      <td align=left><span class="style6"><strong><?php  echo  ucwords($irow['party_address']);?></strong></span></td>
                      <td align=right><?=get_status($irow['status'],$link1);?></td>
                      <td align=right><span class="style6"><strong><?=get_leadsource($irow['lead_source'],$link1);?></strong></span></td>
                    </tr>
                    <?php

?>
  <tr height="<?=$hight?>px">
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
                </table></TD>
            </TR>
        </TABLE></TD>
    </TR>
  </TBODY>
</TABLE>
</BODY>
</HTML>

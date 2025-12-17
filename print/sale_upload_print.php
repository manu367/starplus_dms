<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
echo $po_sql="SELECT * FROM sale_uploader where doc_no='".$docid."' GROUP BY doc_no,from_location";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$loc=explode('~',getLocationDetails($po_row['from_location'],"phone,addrs,city,state,pan_no,cst_no,st_no,name,gstin_no",$link1));

$to=explode('~',getCustomerDetails($po_row['to_location'],"contactno,address,city,state,category,category,category,customername",$link1));
if($to[0]==""){
	$to=explode('~',getLocationDetails($po_row['to_location'],"phone,addrs,city,state,pan_no,cst_no,st_no,name,gstin_no",$link1));
}

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
                <div style="margin-left:300px;">&nbsp;&nbsp;&nbsp;<?php  echo $loc[7]  ;?><br/>&nbsp;&nbsp;&nbsp;<?php  echo $loc[1]  ;?><br/>&nbsp;&nbsp;&nbsp;<?php echo "INVOICE"
;?></div> </span></TD>
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
            <TR vAlign=top>
              <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Ship From(Consignor) :</B><br/><br/><B><span class="lable">
               <?=$loc[7]?><br> <?=$loc[1]?>
                </span></B><br><span class="lable">
                <?=$loc[0]?>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$loc[2]?>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$loc[3]?>
                </span><BR>
                
                <strong>GST No.:</strong><span class="lable">
                <?=$loc[8]?>
                </span></FONT>
              </TD>
              <TD colspan="2" vAlign=Top><FONT size=2><B>Invoice No.</B>
              &nbsp;&nbsp;<B><?=$po_row['doc_no']?></B></FONT><br>
			  <FONT size=2><B>Invoice Date</B>
              &nbsp;<B><?=dt_format($po_row['doc_date'])?></B></FONT></TD>
            </TR>
            <TR vAlign=top>
              <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Bill To:</B><BR><BR><span class="lable">
                <strong><?=$to[7]?><br><?=$to[6]?></strong>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$to[1]?>
                </span><BR>
                <span class="lable">
                <?=$to[3]?>
                </span><BR>
                <strong>GST No.:</strong><span class="lable">
                <?=$to[8]?>
                </span> 
               </FONT></TD>
              <TD width="47%" colspan="2">
			  <FONT size=2><B>Ship To:</B><BR><BR><span class="lable">
                <strong><?=$to[7]?> <br><?=$to[6]?></strong>
                </span></FONT><BR>
                <FONT size=2><span class="lable">
                <?=$to[1]?>
                </span><BR>
                <span class="lable">
                <?=$to[3]?>
                </span><BR>
                <strong>GST No.:</strong><span class="lable">
                <?=$to[8]?>
                </span>
               </FONT>
			  </TD>
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=5 width="100%" border=1>
          <TBODY>
            <TR vAlign=top>
              <TD style="padding-left:5px;"><strong>Dispatched<?=$imeitag?>Detail:</strong> </TD>
            </TR>
            <TR vAlign=top>
              <TD style="padding-left:5px;"><FONT size=2>
			  <?php
			  $arr_modelws = array();
	          $imeiquery="Select * from sale_uploader where doc_no='".$docid."' order by prod_code";
			  $imeiresult=mysqli_query($link1,$imeiquery);
			  while($imeirow=mysqli_fetch_array($imeiresult)){
			  	$arr_modelws[$imeirow['prod_code']][] = $imeirow['serial_no1'];
			  }
				  foreach($arr_modelws as $prodcode => $serialarr){
				  	$product_name = explode("~", getProductDetails($prodcode, "productname,productcolor", $link1));
					echo "<b>Model Name: </b>".$product_name[0]." / ".$prodcode."<br/>";
					echo "<b>Serial No.: </b>".implode(" , ",$serialarr);
					echo "<br/><br/>";
				  }
                 ?></FONT>
			   </TD>
            </TR>
			 </TBODY>
        </TABLE>
        <TABLE height=50 cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
            <TR>
              <TD width="409" rowspan="7" valign="top" style="padding-left:5px;">
                &nbsp;<FONT size=2>Remarks  : <?=$po_row['entry_rmk']?></FONT>
              </TD>
              <TD width="369" height="50" colspan="3" align="right" vAlign=top><FONT size=2><span class="lable"><strong><?=$loc[7]?></strong></span></FONT>&nbsp;&nbsp;<BR>
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

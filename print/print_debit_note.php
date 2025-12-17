<?php
require_once("../config/config.php");

/// Get Master Table
$pi_no = base64_decode($_REQUEST['ref_no']);


$row_qry=mysqli_query($link1,"select * from debit_note where ref_no='$pi_no'") or die("Error in Selection".mysqli_error($link1));

$row=mysqli_fetch_array($row_qry);

$address=preg_split('/<br[^>]*>/i',$row['comp_add']);
#####################################################
$get_result2=explode("~",getLocationDetails($row['location_id'],"name,city,state,addrs",$link1));
//echo $get_result2[0].",".$get_result2[1].",".$get_result2[2];
?>

<!-- saved from url=(0022)http://internet.e-mail -->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >

<!-- saved from url=(0067)http://122.160.166.107/jainaasso/invtemp2.asp?billid=36969&copies=1 -->

<HTML>

<HEAD>

<TITLE>DEBIT NOTE</TITLE>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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

	BORDER-BOTTOM: medium none;

	border-collapse: collapse;

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

	FONT-SIZE: 12pt;

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

/*  TABLE { page-break-after:auto }

  TR    { page-break-inside:avoid; page-break-after:auto }

  TD    { page-break-inside:avoid; page-break-after:auto }

  TBODY { display:table-header-group }

  tfoot { display:table-footer-group }*/

}

.style6 {

	font-family: "Times New Roman", Times, serif;

}

div{



    /* Rotate div */

    -ms-transform: rotate(-7deg); /* IE 9 */

    -webkit-transform: rotate(-7deg); /* Chrome, Safari, Opera */

    transform: rotate(-7deg);

}

.lable {

	font-size: 12px;

	font-family: Verdana, Arial, Helvetica, sans-serif;

	color: #00000;

}

</STYLE>



</HEAD>

<BODY bottomMargin=0 leftMargin=40 topMargin=0 onload=vbscript:window.print()>

<p>&nbsp;</p>
<p>&nbsp;</p>

<TABLE width=800 align="center" cellPadding=0 cellSpacing=0>

  <TBODY>

            <TR>
              <TD width="400" align=center style="border-bottom:none;border-top:none;border-left:none;border-right:none">&nbsp;</TD>
              <TD width="100" align=left class="style6" style="border-bottom:none;border-top:none;border-left:none;border-right:none"></TD>
              <TD width="300" align=left class="style6" style="border-bottom:none;border-top:none;border-left:none;border-right:none"></TD>
            </TR>
            <TR>
              <TD align=right style="border-bottom:none;border-top:none;border-left:none;border-right:none"><strong class="r">DEBIT NOTE</strong></TD>
              <TD align=right style="border-bottom:none;border-top:none;border-left:none;border-right:none"></TD>
              <TD align=left style="border-bottom:none;border-top:none;border-left:none;border-right:none"><strong><?php echo $get_result2[0]."<br>";
			   echo $get_result2[3];?></strong></TD>
            </TR>

    <TR>

      <TD colspan="3" vAlign=top>

        <TABLE cellSpacing='0' cellPadding='1' width="800" border=1>
<TBODY>
            <TR>
              <TD valign="top" width="175" height="28" style="border-right:none;border-bottom:none;"><strong>M/s </strong></TD>&nbsp;<TD valign="top" width="176" height="28" style="border-right:none;border-bottom:none;"><span><?php $custdet = explode(",",getAnyParty($row['cust_id'],$link1)); echo $custdet[0].",".$custdet[1].",".$custdet[2].",".$custdet[3];?></span></TD>&nbsp;
              
              <TD width="155" height="28" class="r" style="border-right:none;border-bottom:none;"><b>&nbsp;DR No.</b></TD>
              <TD width="276" class="r" style="border-bottom:none;">&nbsp;<?=$row['ref_no'];?></TD>
           </TR>
            <TR>
            <TD height="20" style="border-right:none;border-bottom:none;"><strong> Mobile No. </strong></TD>
            <TD height="20" style="border-right:none;border-bottom:none;"><?=getCustomerDetails($row['cust_id'],"contactno",$link1);?></TD>
              <TD height="20" style="border-right:none;border-bottom:none;"><b>&nbsp;Entry By</b></TD>
              <TD style="border-bottom:none;" class="r">&nbsp;<?=$row['create_by'];?></TD></TR>           
            <TR>
             <TR><TD style="border-right:none;border-bottom:none;" class="r"><strong>Status</strong></TD><TD style="border-right:none;border-bottom:none;" class="r"><?=$row['status'];?></TD>
              <TD height="20" style="border-right:none;border-bottom:none;"><?php if($row['entered_ref_no']){?><b>&nbsp;Invoice No.</b><?php } ?></TD>
              	<TD style="border-bottom:none;" class="r"><?php if($row['entered_ref_no']){?>&nbsp;<?=$row['entered_ref_no']?><?php } ?>&nbsp;</TD>
            </TR>
             <TR>
              <TD height="20" style="border-right:none;border-bottom:none;"><b>Entry Date</b>&nbsp;</TD>
              <TD style="border-right:none;border-bottom:none;"><?=$row['create_date']?>&nbsp;</TD>
              <TD height="20" style="border-right:none;border-bottom:none;"><?php if($row['entered_ref_no']){?><b>&nbsp;Invoice Date</b><?php } ?></TD>
              <TD style="border-bottom:none;" class="r"><?php if($row['entered_ref_no']){ $invdet = explode("~",getAnyDetails($row['entered_ref_no'],"sale_date,total_cost","challan_no","billing_master",$link1));?>&nbsp;<?=$invdet[0]?><?php } ?>&nbsp;</TD>
            </TR>
            <TR>
              <TD height="20" style="border-right:none;border-bottom:none;">&nbsp;</TD>
              <TD style="border-right:none;border-bottom:none;">&nbsp;</TD>
              <TD height="20" style="border-right:none;border-bottom:none;"><?php if($row['entered_ref_no']){?><b>&nbsp;Net Sale Value</b><?php } ?></TD>
              <TD style="border-bottom:none;" class="r"><?php if($row['entered_ref_no']){ ?>&nbsp;<?=$invdet[1]?><?php } ?>&nbsp;</TD>
            </TR>
            
          </TBODY>
          
        </TABLE>

        <TABLE height=81 cellSpacing=0 cellPadding=0 width="800" border=1>
                    <TR style="FONT-WEIGHT: bold" valign="top" class="r">
                      	<TD style="text-align:center" width="4%">#</TD>
                        <TD style="text-align:center" width="19%">Product</TD>
                        <TD style="text-align:center" width="7%">HSN</TD>
                        <TD style="text-align:center" width="5%">Qty</TD>
                        <TD style="text-align:center" width="5%">Price</TD>
                        <TD style="text-align:center" width="5%">Value</TD>
                        
                        <TD style="text-align:center" width="7%">Discount Amount</TD>
                        <?php if($get_result2[2] == $custdet[2]){?>
                        <TD style="text-align:center" width="5%">Sgst Per(%)</TD>
                        <TD style="text-align:center" width="5%">Sgst Amt</TD>
                        <TD style="text-align:center" width="5%">Cgst Per(%)</TD>
                        <TD style="text-align:center" width="6%">Cgst Amt</TD>
                        <?php } else {?>
                        <TD style="text-align:center" width="6%">Igst Per(%)</TD>
                        <TD style="text-align:center" width="8%">Igst Amt</TD>
                        <?php }?>
                        <TD style="text-align:center" width="13%">Total</TD>
          			</TR>
                    <?php
					$i=1;
					$podata_sql = "SELECT * FROM debit_note_data where ref_no='".$pi_no."'";
					$podata_res = mysqli_query($link1,$podata_sql);
					while($podata_row = mysqli_fetch_assoc($podata_res)){
					?>
					  <TR>
						<TD><?=$i?></td>
						<TD><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode,hsn_code",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2];?></td>
                        <TD style="text-align:right"><?=$d[3]?></TD>
						<TD style="text-align:right"><?=$podata_row['req_qty']?></TD>
						<TD style="text-align:right"><?=$podata_row['price']?></TD>
						<TD style="text-align:right"><?=$podata_row['value']?></TD>
						
						<TD style="text-align:right"><?=$podata_row['discount']?></TD>
						<?php if($get_result2[2] == $custdet[2]){?>
						<TD style="text-align:right"><?=$podata_row['sgst_per']?></TD>
						<TD style="text-align:right"><?=$podata_row['sgst_amt']?></TD>
						<TD style="text-align:right"><?=$podata_row['cgst_per']?></TD>
						<TD style="text-align:right"><?=$podata_row['cgst_amt']?></TD>
						<?php }else{?>
						<TD style="text-align:right"><?=$podata_row['igst_per']?></TD>
						<TD style="text-align:right"><?=$podata_row['igst_amt']?></TD>
						<?php }?>
						<TD style="text-align:right"><?=$podata_row['totalvalue']?></TD>
					  </TR>
					<?php
					$i++;
					}
					?>
                    <?php if($get_result2[2] == $custdet[2]){$cols = "11";}else{ $cols = "9";}?>
                  <TR class="r">
                    <TD height="30" style="border-bottom:none" align="right" colspan="<?=$cols?>"><strong>Total&nbsp;</strong></TD>
                    <TD align="right" style="border-bottom:none"><strong><span class="style6">₦ <?php echo $row[amount]?>&nbsp;</span></strong></TD>
                  </TR>
        </TABLE>

        <TABLE height=177 cellSpacing=0 cellPadding='1' width="800" border=1>

          <TBODY>

            <TR>

              <TD colspan="3"><span class="r">&nbsp;&nbsp;<strong>Amount Debit (In Words):&nbsp;&nbsp; </strong></span>₦<span style="font-size:13px" class="style6 lable">

                <?php $x=round($row['amount'],2); echo number_to_words($x)." Only";?>

              </span><strong><span class="style6">&nbsp;</span></strong></TD>

            </TR>

            <?php if($row['status']=="Cancelled"){ ?>

            <TR>

              <TD colspan="3"><span class="r">&nbsp;&nbsp;<strong>Cancellation Reason :</strong></span>&nbsp;<span class="lable"><?=$row['cancel_reason']?></span>&nbsp;&nbsp;&nbsp;<span class="r">&nbsp;&nbsp;<strong>Cancelled By :</strong></span>&nbsp;<span class="lable"><?=getAdminDetails($row['cancelled_by'],"name",$link1)." (".$row['cancel_date'].")";?></span></TD>

            </TR>

            <?php }?>

            <?php 

			$rmk=cleanData($row['remark']);

			if($rmk!=""){ ?>

            <TR>

              <TD colspan="3"><span class="r">&nbsp;&nbsp;<strong>Entered Remark :</strong></span> <span class="lable"><?=$rmk?></span></TD>

            </TR>

            <?php }?>

            <TR>

              <TD height="60" align="left" width="413" valign="top">&nbsp;&nbsp;<span class="r"><strong>Receiver Signature : </strong></span><BR><BR><BR><BR>

			  <span class="lable">&nbsp;&nbsp;&nbsp;<?php ?></span></TD>

              <TD width="377" height="60" colspan="2" align="right" vAlign=top><span class="r"><strong>For <?php echo $address[0];?></strong></span>

                <BR><BR><BR><BR>

                <span class="lable">Authorised Signatory</span>&nbsp;&nbsp;</TD>

            </TR>

          </TBODY>

        </TABLE></TD>

    </TR>

  </TBODY>

</TABLE>

</BODY>

</HTML>
<?php
include("../../includes/connection_close.php");
?>

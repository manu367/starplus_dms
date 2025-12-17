<?php
////// Function ID ///////
$fun_id = array("u"=>array(2)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid = base64_decode($_REQUEST[id]);
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if ($_POST) {
    if ($_POST['Submit'] == 'Cancel') {
        mysqli_autocommit($link1, false);
        $flag = true;
        $err_msg = "";
        $vpo_sql = "SELECT * FROM billing_model_data where challan_no='" . $docid . "'";
        $vpo_res = mysqli_query($link1, $vpo_sql);
        $i = 1;
        while ($vpo_row = mysqli_fetch_assoc($vpo_res)) {
			if($vpo_row["prod_cat"]!="C"){
				//// update stock of from loaction
				$result1 = mysqli_query($link1, "update stock_status set okqty=okqty+'" . $vpo_row['qty'] . "',updatedate='" . $datetime . "' where asc_code='" . $po_row['from_location'] . "' and sub_location='" . $po_row['sub_location'] . "' and partcode='" . $vpo_row['prod_code'] . "'");
				//// check if query is not executed
				if (!$result1) {
					$flag = false;
					$err_msg = "Error Code1:". mysqli_error($link1) . ".";
				}
				///// insert in stock ledger////
				$flag = stockLedger($docid, $today, $vpo_row['prod_code'], $po_row['sub_location'], $po_row['to_location'], $po_row['sub_location'], "IN", "OK", "Cancel Retail Invoice", $vpo_row['qty'], $vpo_row['price'], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
				$i++;
			}
        }/// close for loop
        /////// check if imei is attached then it should also cancelled or reverse to the from location
        if ($po_row['imei_attach'] == "Y") {
            $result2 = mysqli_query($link1, "delete from billing_imei_data where doc_no='" . $docid . "'");
            //// check if query is not executed
            if (!$result2) {
                $flag = false;
                $err_msg = "Error Code2:". mysqli_error($link1) . ".";
            }
        }
        ///// cancel retail invoice ///////////
        $query3 = ("UPDATE billing_master set status='Cancelled',cancel_by='" . $_SESSION['userid'] . "',cancel_date='$today',cancel_rmk='$remark',cancel_step='After " . $po_row['status'] . "',cancel_ip='$ip' where challan_no='" . $docid . "'");
        $result3 = mysqli_query($link1, $query3);
        //// check if query is not executed
        if (!$result3) {
            $flag = false;
            $err_msg = "Error Code3:". mysqli_error($link1) . ".";
        }
        ///// check retail billing was not for customer. we reverse credit balance for location only not for customer
        if (substr($po_row['to_location'], 0, 4) != "CUST") {
            //// update cr bal of child location
            $result4 = mysqli_query($link1, "update current_cr_status set cr_abl=cr_abl+'" . $grand_total . "',total_cr_limit=total_cr_limit+'" . $grand_total . "', last_updated='" . $datetime . "' where parent_code='" . $po_row['from_location'] . "' and asc_code='" . $po_row['to_location'] . "'");
            //// check if query is not executed
            if (!$result4) {
                $flag = false;
                $err_msg = "Error Code4:". mysqli_error($link1) . ".";
            }
        }
        ////// maintain party ledger////
        $flag = partyLedger($po_row['from_location'], $po_row['to_location'], $docid, $today, $today, $currtime, $_SESSION['userid'], "CANCEL RETAIL INVOICE", $grand_total, "CR", $link1, $flag);
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $docid, "RETAIL INVOICE", "CANCEL", $ip, $link1, $flag);
        ///// check  master  query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Invoice is Cancelled successfully with with ref. no." . $docid;
			$cflag = "success";
			$cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Request could not be processed " . $err_msg . ". Please try again.";
			$cflag = "danger";
			$cmsg = "Failed";
        }
        mysqli_close($link1);
        ///// move to parent page
        header("Location:retailbillinglist.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">

        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#myTable').dataTable();
            });
        </script>
        <script type="text/javascript" src="../js/common_js.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-user"></i> Retail Billing  Details</h2><br/>
                    <div class="panel-group">
                        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" onSubmit="return myConfirm();">
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading">Party Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%">
                                        <tbody>
                                            <tr>
                                            	<td width="20%"><label class="control-label">Billing From</label></td>
                                                <td width="30%"><?php echo str_replace("~", ",", getLocationDetails($po_row['from_location'], "name,city,state", $link1)); ?></td>
                                                <td width="20%"><label class="control-label">Billing To</label></td>
                                                <td width="30%"><?php 
				  /// bill to party
				  $billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
				  $explodeval=explode("~",$billto);
				  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($po_row['to_location'],"customername,city,state",$link1);}
				  echo str_replace("~",",",$toparty);?></td>
                                                
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Invoice No.</label></td>
                                                <td><?php echo $po_row['challan_no']; ?></td>
                                                <td><label class="control-label">Billing Date</label></td>
                                                <td><?php echo $po_row['sale_date']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Entry By</label></td>
                                                <td><?php echo getAdminDetails($po_row['entry_by'], "name", $link1); ?></td>
                                                <td><label class="control-label">Status</label></td>
                                                <td><?php echo $po_row['status']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Discount Type</label></td>
                                                <td><?php echo getDiscountType($po_row['discountfor']); ?></td>
                                                <td><label class="control-label">Tax Type</label></td>
                                                <td><?php echo getTaxType($po_row['taxfor']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading">Items Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center" width="5%">#</th>
                                                <th style="text-align:center" width="20%">Product</th>
                                                <th style="text-align:center" width="15%">Bill Qty</th>
                                                <th style="text-align:center" width="15%">Price</th>
                                                <th style="text-align:center" width="15%">Value</th>
                                                <th style="text-align:center" width="15%">Discount</th>
                                                <th style="text-align:center" width="15%">Tax Amount</th>
                                                <th style="text-align:center" width="15%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $podata_sql = "SELECT * FROM billing_model_data where challan_no='" . $docid . "'";
                                            $podata_res = mysqli_query($link1, $podata_sql);
                                            while ($podata_row = mysqli_fetch_assoc($podata_res)) {
                                                $proddet = explode("~", getProductDetails($podata_row['prod_code'], "productname,productcolor", $link1));
                                                ?>
                                                <tr>
                                                    <td><?= $i ?></td>
                                                    <td><?= $proddet[0] . " (" . $proddet[1] . ")" ?></td>
                                                    <td style="text-align:right"><?= $podata_row['qty'] ?></td>
                                                    <td style="text-align:right"><?= $podata_row['price'] ?></td>
                                                    <td style="text-align:right"><?= $podata_row['value'] ?></td>
                                                    <td style="text-align:right"><?= $podata_row['discount'] ?></td>
                                                    <td style="text-align:right"><?= $podata_row['tax_amt'] ?></td>
                                                    <td style="text-align:right"><?= $podata_row['totalvalue'] ?></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading">Amount Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%">
                                        <tbody>
                                            <tr>
                                                <td width="20%"><label class="control-label">Sub Total</label></td>
                                                <td width="30%"><?php echo $po_row['basic_cost']; ?></td>
                                                <td width="20%"><label class="control-label">Total Discount</label></td>
                                                <td width="30%"><?php echo $po_row['discount_amt']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Grand Total</label></td>
                                                <td><?php echo currencyFormat($po_row['total_cost']); ?><input type="hidden" name="grand_total" id="grand_total" value="<?= $po_row['total_cost']; ?>" class="form-control"  readonly/></td>
                                                <td><label class="control-label">Total Tax</label></td>
                                                <td><?php echo currencyFormat($po_row['tax_cost']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Delivery Address</label></td>
                                                <td><?php echo $po_row['deliv_addrs']; ?></td>
                                                <td><label class="control-label">Remark</label></td>
                                                <td><?php echo $po_row['disp_rmk']; ?></td>

                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            </tr>
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading"></div>
                                <div class="panel-body">

                                    <table class="table table-bordered" width="100%">
                                        <tbody>

                                            <tr>
                                                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                                                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ((event.keyCode ? event.keyCode : event.which ? event.which : event.charCode) != 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if ($_POST['Submit'] == 'Cancel') { ?>disabled<?php } ?>>&nbsp;
                                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div><!--close panel body-->
                            </div><!--close panel-->
                        </form>
                    </div><!--close panel group-->
                </div><!--close col-sm-9-->
            </div><!--close row content-->
        </div><!--close container-fluid-->
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>
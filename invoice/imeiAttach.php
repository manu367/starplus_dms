<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
  $billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
  $explodeval=explode("~",$billto);
  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($po_row['to_location'],"customername,city,state",$link1);}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
  //$('#example').dataTable();
  for(var i=0; i<(document.getElementById("noofprod").value);i++){
	 $('#example'+i).dataTable({
        "paging": false,
	    "ordering": false,
		"info": false
     });
  }
});
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
});
$(document).ready(function() {
    $('table.display').DataTable();
} );
</script>
<style>
div.dataTables_wrapper {
        margin-bottom: 3em;
}
</style>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
  <div class="container col-sm-9">
  <h2 align="center"><i class="fa fa-upload"></i> Serial Attach for invoice</h2><br/>
  <ul class="nav nav-tabs">
   <div class="form-buttons" style="float:right">
      <button title="Back" type="button" class="btn btn-primary" style="" onClick="window.location.href='retailbillinglist.php?<?=$pagenav?>'"><span>Back</span></button>
    </div>
    <li class="active"><a data-toggle="tab" href="#home">Customer Details</a></li>
    <li><a data-toggle="tab" href="#menu1">Product Details</a></li>
    <li><a data-toggle="tab" href="#menu2">Amount Details</a></li>
    <li><a data-toggle="tab" href="#menu3">Serial Attach</a></li>
  </ul>
  <div class="tab-content">
    <div id="home" class="tab-pane fade in active"><br/>
      <form id="tab_logic" name="form1" class="form-horizontal">
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Billing To</label>
            <div class="col-md-5">
              <textarea name="billto" id="billto" class="form-control" readonly style="resize:none"><?php echo str_replace("~",",",$toparty); ?></textarea>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Billing From</label>
            <div class="col-md-5">
              <textarea name="billto" id="billto" class="form-control" readonly style="resize:none"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></textarea>
            </div>
          </div>
        </div> 
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Invoice No.</label>
            <div class="col-md-5">
              <input type="text" name="invno" id="invno" class="form-control" value="<?php echo $po_row['challan_no'];?>" readonly/>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Billing Date</label>
            <div class="col-md-5">
              <input type="text" name="bdate" id="bdate" class="form-control" value="<?php echo $po_row['sale_date'];?>" readonly/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Entry By</label>
            <div class="col-md-5">
              <input type="text" name="entryby" id="entryby" class="form-control" value="<?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?>" readonly/>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
            <div class="col-md-5">
              <input type="text" name="status" id="status" class="form-control" value="<?php echo $po_row['status'];?>" readonly/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Discount Type</label>
            <div class="col-md-5">
              <input type="text" name="disctype" id="disctype" class="form-control" value="<?php echo getDiscountType($po_row['discountfor']);?>" readonly/>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Tax Type</label>
            <div class="col-md-5">
              <input type="text" name="taxtype" id="taxtype" class="form-control" value="<?php echo getTaxType($po_row['taxfor']);?>" readonly/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Delivery Address</label>
            <div class="col-md-5">
              <textarea name="billto" id="billto" class="form-control" readonly style="resize:none"><?php echo $po_row['deliv_addrs'];?></textarea>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Remark</label>
            <div class="col-md-5">
              <textarea name="billto" id="billto" class="form-control" readonly style="resize:none"><?php echo $po_row['disp_rmk'];?></textarea>
            </div>
          </div>
        </div>
        <div class="form-group" align="center">
          <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Next</button>
        </div>
      </form>
    </div>
    <div id="menu1" class="tab-pane fade table-responsive"><br/>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Bill Qty</th>
            <th>Price</th>
            <th>Value</th>
            <th>Discount/Unit</th>
            <th>Tax Amount</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
        <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			/// intialize array
			$arr_product=array();
			$arr_productname=array();
			$arr_qty=array();
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
				$arr_product[]=$podata_row['prod_code'];
				$arr_productname[]=$proddet[0]." (".$proddet[1].")";
				$arr_qty[]=$podata_row['qty'];
		?>
          <tr>
            <td><?=$i?></td>
            <td><?=$proddet[0]." (".$proddet[1].")"?></td>
            <td><?=$podata_row['qty']?></td>
            <td><?=$podata_row['price']?></td>
            <td><?=$podata_row['value']?></td>
            <td><?=$podata_row['discount']?></td>
            <td><?=$podata_row['tax_amt']?></td>
            <td><?=$podata_row['totalvalue']?></td>
          </tr>
          <?php
			$i++;
			}
			?>
        </tbody>
      </table>
      <div class="form-group" align="center">
          <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#home'">Previous</button>
          &nbsp;&nbsp;
          <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Next</button>
        </div>
    </div>
    <div id="menu2" class="tab-pane fade"><br/>
      <form id="tab_logic" name="form2" class="form-horizontal">
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Sub Total</label>
            <div class="col-md-5">
              <input type="text" name="subtotal" id="subtotal" class="form-control" value="<?php echo $po_row['basic_cost'];?>" readonly/>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Total Discount</label>
            <div class="col-md-5">
              <input type="text" name="totaldisc" id="totaldisc" class="form-control" value="<?php echo $po_row['discount_amt'];?>" readonly/>
            </div>
          </div>
        </div> 
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Grand Total</label>
            <div class="col-md-5">
              <input type="text" name="grandtotal" id="grandtotal" class="form-control" value="<?php echo currencyFormat($po_row['total_cost']);?>" readonly/>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Total Tax</label>
            <div class="col-md-5">
              <input type="text" name="totaltax" id="totaltax" class="form-control" value="<?php echo currencyFormat($po_row['tax_cost']);?>" readonly/>
            </div>
          </div>
        </div>
        <div class="form-group" align="center">
          <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Previous</button>
          &nbsp;&nbsp;
          <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu3'">Next</button>
        </div>
      </form>
    </div>
    <div id="menu3" class="tab-pane fade"><br/>
      <div class="form-group" align="center">
          <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Previous</button>
      </div>
      <div>
        <?php for($j=0;$j<($i-1);$j++){?>
        <div class="col-md-4 panel panel-default table-responsive" style="height:400px;">
        <div class="panel-heading"><?=$arr_productname[$j]?></div>
        <div class="panel-body">
           <table id="example<?=$j?>" class="display table table-bordered" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial 1</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sno=0;
                $sql=mysqli_query($link1,"Select owner_code,imei1,imei2 from billing_imei_data where owner_code='".$po_row['from_location']."' and prod_code='".$arr_product[$j]."'")or die(mysqli_error($link1));
                while($row=mysqli_fetch_assoc($sql)){
                      $chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select owner_code,doc_no from billing_imei_data where imei1='".$row['imei1']."' order by id desc"));
                      $chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
                      if($chek_rcvin['status']==""){
                      $chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
                          $checkstatus=$chek_rcvin2['status'];
                      }else{
                          $checkstatus=$chek_rcvin1['status'];
                      }
                      if($chek_owner['owner_code']==$row['owner_code'] && $checkstatus=="Received"){
                      $sno=$sno+1;
                ?>
                <tr>
                    <td><?=$sno?></td>
                    <td><?=$row['imei1']?></td>
                </tr>
                <?php } }?>
            </tbody>
        </table>
            <a href="#" class="btn btn-primary">Attach</a>
          </div>
        </div>
        <?php }?>
        <input name="noofprod" id="noofprod" type="hidden" value="<?=$j?>"/>
      </div>
    </div>
  </div>
</div>

  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>

<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
?>
  <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Billing To</label></td>
                <td width="30%">
				  <?php 
				  /// bill to party
				  $billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
				  $explodeval=explode("~",$billto);
				  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($po_row['to_location'],"customername,city,state",$link1);}
				  echo str_replace("~",",",$toparty);?></td>
                <td width="20%"><label class="control-label">Billing From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Billing Date</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php $d = explode('~',$toparty);
                echo $d['2']?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>                
                <th style="text-align:center" width="11%">Discount/Unit</th>
                <th style="text-align:center" width="8%">Value After Discount</th>
                <th style="text-align:center" width="12%">SGST(%)</th>
                <th style="text-align:center" width="12%">SGST Amount</th>
                <th style="text-align:center" width="12%">CGST(%)</th>
                <th style="text-align:center" width="12%">CGST Amount</th>
                <th style="text-align:center" width="12%">IGST(%)</th>
                <th style="text-align:center" width="12%">IGST Amount</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$proddet[1].")"?></td>
                <td style="text-align:right"><?=$podata_row[qty]?></td>
                <td style="text-align:right"><?=$podata_row[price]?></td>
                <td style="text-align:right"><?=$podata_row[discount]?></td>
				<?php  
				$valueafterdiscount =  ($podata_row[qty]*$podata_row[price])-$podata_row[discount];
				?>
                <td style="text-align:right"><?=$valueafterdiscount?></td>
                <td style="text-align:right"><?=$podata_row[sgst_per]?></td>
                <td style="text-align:right"><?=$podata_row[sgst_amt]?></td>
                <td style="text-align:right"><?=$podata_row[cgst_per]?></td>
                <td style="text-align:right"><?=$podata_row[cgst_amt]?></td>
                <td style="text-align:right"><?=$podata_row[igst_per]?></td>
                <td style="text-align:right"><?=$podata_row[igst_amt]?></td>
                <td style="text-align:right"><?=$podata_row[totalvalue]?></td>
              </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['basic_cost'];?></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo $po_row['discount_amt'];?></td>
              </tr>
              <tr>                
                <td><label class="control-label">Round Off</label></td>
                <td><?php echo currencyFormat($po_row['round_off']);?></td>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['total_cost']);?></td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['deliv_addrs'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['disp_rmk'];?></td>
              </tr>
              <?php if($po_row['status']=="Cancelled"){ ?> 
              <tr>
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo getAdminDetails ($po_row['cancel_by'],"name",$link1);?></td>
				 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo dt_format ($po_row['cancel_date']);?></td>
				 </tr>
				<tr>                 
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td ><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
			  <?php }?>
               
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <?php if($po_row['is_adjust']){?>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Payment Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Payment Mode</label></td>
                <td width="30%"><?php echo $po_row['payment_ref'];?></td>
                <td width="20%"><label class="control-label">Receive Amount</label></td>
                <td width="30%"><?php echo currencyFormat($po_row['adjusted_amt']);?></td>
              </tr>
              <tr>                
                <td><label class="control-label">Payment Date</label></td>
                <td><?php echo ($po_row['payment_date']);?></td>
                <td><label class="control-label"></label></td>
                <td>&nbsp;</td>
              </tr>               
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<?php }?>
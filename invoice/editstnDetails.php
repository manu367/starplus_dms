<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
@extract($_POST);
////// we hit save button
if($_POST['upd']=='Save'){
	/// transcation parameter /////////////////////	
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	///// Insert in item data by picking each data row one by one
	foreach($refno as $k=>$val)
	{   	    
		$refid = base64_decode($val);
		// checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!=''){
			/////////// update data
			$query2 = "UPDATE billing_model_data SET qty='" .$req_qty[$k] . "', value='" . $linetotal[$k] . "', discount='" .$rowdiscount[$k] . "', sgst_per='" . $sgst_per[$k]. "', sgst_amt='" .$sgst_amt[$k] . "', cgst_per='" .$cgst_per[$k]. "', cgst_amt='" .$cgst_amt[$k]. "', igst_per='" .$igst_per[$k]. "', igst_amt='" .$igst_amt[$k] . "', totalvalue='" . $total_val[$k] . "', disc_amt = '".$rowdiscount[$k]."', additional_product='EDIT' WHERE challan_no='".$po_row['challan_no']."' AND id='".$refid."'";
			$result2 = mysqli_query($link1, $query2);
			//// check if query is not executed
			if (!$result2) {
				$flag = false;
				$err_msg = "Error Code1:";
			}
			////// hold the PO qty in stock ////
			$totalcgst_amt+=$cgst_amt[$k];
			$totalsgst_amt+=$sgst_amt[$k];
			$totaligst_amt+=$igst_amt[$k];
			//$flag=holdStockQty($partycode,$prod_code[$k],$req_qty[$k],$link1,$flag);
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
    $tax_amount = $totalcgst_amt+$totalsgst_amt+$totaligst_amt;
	/////calculate round off
	$roundoff = 0.00;
	if(strpos($grand_total, ".") !== false){
		$expd_gt = explode(".",$grand_total);
		$checkval = ".".$expd_gt[1];
		if($checkval>=.50){
			$ro = 1-$checkval;
			$roundoff = "+".$ro;
		}else{
			$roundoff = "-".$checkval;
		}
	}else{
		$roundoff = 0.00;
	}
	///// Insert Master Data
    $query1 = "UPDATE billing_master SET 
	basic_cost='" . $sub_total . "',
	discount_amt='" . $total_discount . "',
	tax_cost='" .$tax_amount . "',
	total_sgst_amt='" . $totalsgst_amt . "',
	total_cgst_amt='" . $totalcgst_amt . "',
	total_igst_amt='" . $totaligst_amt . "',
	total_cost='" . $grand_total . "',
	round_off='".$roundoff."' WHERE challan_no='".$po_row['challan_no']."'";	
	$result = mysqli_query($link1, $query1);
	//// check if query is not executed
	if (!$result) {
		$flag = false;
		$err_msg = "Error Code2:";
	} 
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_row['challan_no'],"STN","EDIT",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "STN is successfully edited with ref. no.".$po_row['challan_no'];
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$err_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:stockTRansferApproval.php?msg=".$msg."".$pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
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
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	$("#frm2").validate();
});
/////// calculate line total /////////////
function formatCurrency(num) {
   num = num.toString().replace(/\$|\,/g,'');
   if(isNaN(num))
    num = "0";
    signt = (num == (num = Math.abs (num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
   if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) 
	num = num.substring(0,num.length-(4*i+3))+''+
	num.substring(num.length-(4*i+3));
	return (((signt)?'':'-') + '' + num + '.' + cents);
}
function rowTotal(ind){
	var ent_qty="req_qty["+ind+"]";
  	var ent_rate="price["+ind+"]";
  	var discountField="rowdiscount["+ind+"]";
  	var discountval ="rowdiscount_val["+ind+"]";  
	var sgst_per = "sgst_per[" + ind + "]";
	var sgst_amt = "sgst_amt[" + ind + "]";
	var cgst_per = "cgst_per[" + ind + "]";
	var cgst_amt = "cgst_amt[" + ind + "]";
	var igst_per = "igst_per[" + ind + "]";
	var igst_amt = "igst_amt[" + ind + "]";
	var totalvalField="total_val["+ind+"]";
  	////// check if entered qty is something
  	if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
  	/////  check if entered price is somthing
  	if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  	///// check if discount value is something
  	if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00;}  
   	/////  check if entered sgst per is somthing
	if (document.getElementById(sgst_per).value) {
		var sgst = document.getElementById(sgst_per).value;
	} else {
		var sgst = 0.00;
	}
	/////  check if entered cgst per is somthing
	if (document.getElementById(cgst_per).value) {
		var cgst = document.getElementById(cgst_per).value;
	} else {
		var cgst = 0.00;
	}
	/////  check if entered igst per is somthing
	if (document.getElementById(igst_per).value) {
		var igst = document.getElementById(igst_per).value;
	} else {
		var igst = 0.00;
	}
	/////////////////check value
	var total= parseFloat(qty)*parseFloat(price);
    if(parseFloat(total)>=parseFloat(dicountval)){
     	var totalcost= (parseFloat(total)-parseFloat(dicountval));
     	var var3="linetotal["+ind+"]";
	 	document.getElementById(discountval).value = totalcost;
     	document.getElementById(var3).value=formatCurrency(total);
	 	var doctype =  "<?php echo $po_row["document_type"];?>";
	 	if(doctype == 'INVOICE'){
	  		var sgst_amt1 = totalcost * sgst/100;
      		var cgst_amt1 = totalcost * cgst/100;
      		var igst_amt1 = totalcost * igst/100;
  
			document.getElementById(discountval).value = totalcost;        
			document.getElementById(sgst_amt).value = formatCurrency(sgst_amt1);
			document.getElementById(cgst_amt).value = formatCurrency(cgst_amt1);
			document.getElementById(igst_amt).value = formatCurrency(igst_amt1);   
	  
			var totalsum =  parseFloat(totalcost + sgst_amt1 + cgst_amt1 + igst_amt1);
			document.getElementById(totalvalField).value=formatCurrency(totalsum);
			calculatetotal();
	  	}
	 	else{
     		document.getElementById(totalvalField).value=formatCurrency(totalcost);
     		calculatetotal();
	 	}
	}else{
		alert("Discount is exceeding from price");
		var total= parseFloat(qty)*parseFloat(price);
		var var3="linetotal["+ind+"]";
		document.getElementById(var3).value=formatCurrency(total);
		document.getElementById(discountField).value="0.00";
		document.getElementById(totalvalField).value=formatCurrency(total);
		calculatetotal();
	}
}
////// calculate final value of form /////
function calculatetotal(){
    var rowno = (document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
	var sum_discount=0.00;
	var sumval = 0.00;
	var sumgst = 0.00;
	var sumtotval = 0.00;
    for(var i=1;i<rowno;i++){
		var temp_qty="req_qty["+i+"]";
		var temp_total="linetotal["+i+"]";
		var temp_discount="rowdiscount["+i+"]";
		var subtotal="rowdiscount_val["+i+"]";
		var temp_sgstamt = "sgst_amt[" + i + "]";
		var temp_cgstamt = "cgst_amt[" + i + "]";
		var temp_igstamt = "igst_amt[" + i + "]";
		var totalval = "total_val["+i+"]"
		var discountvar=0.00;
		var totalamtvar=0.00;
		///// check if discount value is something
		if(document.getElementById(temp_discount).value){ discountvar= document.getElementById(temp_discount).value;}else{ discountvar=0.00;}
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}
		if(document.getElementById(totalval).value){ var totsum = document.getElementById(totalval).value;}else{ var totsum=0;}
		if(document.getElementById(subtotal).value){ var totsub = document.getElementById(subtotal).value;}else{ var totsub=0;}
		if(document.getElementById(temp_sgstamt).value){ var totsgst = document.getElementById(temp_sgstamt).value;}else{ var totsgst=0;}
		if(document.getElementById(temp_cgstamt).value){ var totcgst = document.getElementById(temp_cgstamt).value;}else{ var totcgst=0;}
		if(document.getElementById(temp_igstamt).value){ var totigst = document.getElementById(temp_igstamt).value;}else{ var totigst=0;}
		sum_qty += parseFloat(totqty);
		sum_total += parseFloat(totalamtvar);
		sum_discount += parseFloat(discountvar)*parseFloat(totqty);
		sumgst += parseFloat(totsgst)+parseFloat(totcgst)+parseFloat(totigst);
		sumval += parseFloat(totsum);
		sumtotval += parseFloat(totsub)
	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=formatCurrency(sumtotval);
    document.getElementById("total_discount").value=formatCurrency(sum_discount);
	document.getElementById("total_gstamt").value=formatCurrency(sumgst);
	document.getElementById("grand_total").value=formatCurrency(parseFloat(sumval));
}
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   	<div class="col-sm-9">
    	<h2 align="center"><i class="fa fa-reply"></i> Edit STN Details</h2>
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
   		<div class="panel-group">
    		<div class="panel panel-default table-responsive">
        		<div class="panel-heading heading1">Party Information</div>
         		<div class="panel-body">
                  <table class="table table-bordered" width="100%">
                    <tbody>
                      <tr>
                        <td width="20%"><label class="control-label">Stock Transfer To</label></td>
                        <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
                        <td width="20%"><label class="control-label">Stock Transfer From</label></td>
                        <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Document No.</label></td>
                        <td><?php echo $po_row['challan_no'];?></td>
                        <td><label class="control-label">Document Date</label></td>
                        <td><?php echo $po_row['sale_date'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Entry By</label></td>
                        <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                        <td><label class="control-label">Status</label></td>
                        <td><?php echo $po_row['status'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Document Type</label></td>
                        <td><?php echo $po_row['document_type'];?></td>
                        <td><label class="control-label">Cost Center</label></td>
                         <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
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
                          <tr class="btn-primary">
                            <th style="text-align:center" width="5%">#</th>
                            <th style="text-align:center" width="20%">Product</th>
                            <th style="text-align:center" width="6%">Req. Qty</th>
                            <th style="text-align:center" width="6%">Price</th>
                            <th style="text-align:center" width="6%">Value</th>
                            <th style="text-align:center" width="4%">Discount</th>
                            <th style="text-align:center" width="6%">Value After Discount</th>
                            <th style="text-align:center" width="6%">Sgst Per(%)</th>
                            <th style="text-align:center" width="6%">Sgst Amt</th>
                            <th style="text-align:center" width="6%">Cgst Per(%)</th>
                            <th style="text-align:center" width="6%">Cgst Amt</th>
                            <th style="text-align:center" width="6%">Igst Per(%)</th>
                            <th style="text-align:center" width="6%">Igst Amt</th>
                            <th style="text-align:center" width="15%">Total</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=1;
						$j=0;
                        $totqty = 0;
                        $totdiscount = 0;
                        $totalgst = 0;
                        $podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
                        $podata_res=mysqli_query($link1,$podata_sql);
                        while($podata_row=mysqli_fetch_assoc($podata_res)){
                            $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1); 
                            $d = explode('~', $data);
                        ?>
                          <tr>
                            <td><?=$i?></td>
                            <td><?php  echo $d[0].' | '.$d[1].' | '.$d[2];?><input type="hidden" class="form-control required" required name="prod_code[<?=$j?>]" id="prod_code[<?=$i?>]" readonly value="<?=$podata_row['prod_code']?>"><input type="hidden" class="form-control required" required name="refno[<?=$j?>]" id="refno[<?=$i?>]" readonly value="<?=base64_encode($podata_row["id"])?>"></td>
                            <td><input type="text" class="form-control digits required" name="req_qty[<?=$j?>]" id="req_qty[<?=$i?>]" style="width:80px;text-align:right" autocomplete="off" required onKeyUp="rowTotal('<?=$i?>');" value="<?=round($podata_row['qty'])?>"></td>
                            <td><input type="text" class="form-control number required" name="price[<?=$j?>]" id="price[<?=$i?>]" style="width:100px;text-align:right" autocomplete="off" onKeyUp="rowTotal('<?=$i?>');" required value="<?=$podata_row['price']?>" readonly></td>
                            <td><input type="text" class="form-control" name="linetotal[<?=$j?>]" id="linetotal[<?=$i?>]" autocomplete="off" style="width:100px;text-align:right" readonly value="<?=$podata_row['value']?>"></td>
                            <td><input type="text" class="form-control number" name="rowdiscount[<?=$j?>]" id="rowdiscount[<?=$i?>]" autocomplete="off" style="width:100px;text-align:right" onKeyUp="rowTotal('<?=$i?>');" readonly value="<?=$podata_row['discount']?>"></td>
                            <td><input type="text" class="form-control number" name="rowdiscount_val[<?=$j?>]" id="rowdiscount_val[<?=$i?>]" autocomplete="off" style="width:100px;text-align:right" readonly value="<?=$podata_row['value']-$podata_row['discount']?>"></td>
                            <td><input type="text" class="form-control number" name="sgst_per[<?=$j?>]" id="sgst_per[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['sgst_per']?>"></td>
                            <td><input type="text" class="form-control number" name="sgst_amt[<?=$j?>]" id="sgst_amt[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['sgst_amt']?>"></td>
                            <td><input type="text" class="form-control number" name="cgst_per[<?=$j?>]" id="cgst_per[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['cgst_per']?>"></td>
                            <td><input type="text" class="form-control number" name="cgst_amt[<?=$j?>]" id="cgst_amt[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['cgst_amt']?>"></td>
                            <td><input type="text" class="form-control number" name="igst_per[<?=$j?>]" id="igst_per[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['igst_per']?>"></td>
                            <td><input type="text" class="form-control number" name="igst_amt[<?=$j?>]" id="igst_amt[<?=$i?>]" autocomplete="off" readonly style="width:80px;text-align:right" value="<?=$podata_row['igst_amt']?>"></td>
                            <td><input type="text" class="form-control number" name="total_val[<?=$j?>]" id="total_val[<?=$i?>]" autocomplete="off" readonly style="width:100px;text-align:right" value="<?=$podata_row['totalvalue']?>"></td>
                          </tr>
                        <?php
                            $totqty += $podata_row['qty'];
                            $totdiscount += $podata_row['discount'];
                            $totalgst += $podata_row['sgst_amt']+$podata_row['cgst_amt']+$podata_row['igst_amt'];
                            $i++;
							$j++;
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
                            <td width="20%"><label class="control-label">Total Qty</label></td>
                            <td width="30%"><input type="text" name="total_qty" id="total_qty" class="form-control" readonly style="text-align:right" value="<?=$totqty?>"/><input type="hidden" name="rowno" id="rowno" value="<?=$i?>"/></td>
                            <td width="20%"><label class="control-label">Sub Total</label></td>
                            <td width="30%"><input type="text" name="sub_total" id="sub_total" class="form-control" readonly value="<?php echo $po_row['basic_cost'];?>" style="text-align:right"/></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">&nbsp;</label></td>
                            <td width="30%">&nbsp;</td>
                            <td width="20%"><label class="control-label">Total Discount</label></td>
                            <td width="30%"><input type="text" name="total_discount" id="total_discount" class="form-control" value="<?php echo $totdiscount;?>" readonly onKeyUp="check_total_discount();" style="text-align:right"/></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">&nbsp;</label></td>
                            <td width="30%">&nbsp;</td>
                            <td width="20%"><label class="control-label">Total GST</label></td>
                            <td width="30%"><input type="text" name="total_gstamt" id="total_gstamt" class="form-control" value="<?php echo $totalgst;?>" readonly style="text-align:right"/></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">&nbsp;</label></td>
                            <td width="30%">&nbsp;</td>
                            <td width="20%"><label class="control-label">Grand Total</label></td>
                            <td width="30%"><input type="text" name="grand_total" id="grand_total" class="form-control" readonly value="<?=$po_row['total_cost']?>" style="text-align:right"/></td>
                          </tr>
                          <tr>
                            <td colspan="4" align="center"><input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save" <?php if($_POST["upd"]=="Save"){ echo "disabled";}?>>	&nbsp;&nbsp;
                          <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stockTRansferApproval.php?<?=$pagenav?>'"></td>
                            </tr>               
                        </tbody>
                      </table>
      				</div><!--close panel body-->
    		</div><!--close panel-->
  		</div><!--close panel group-->
        </form>
 	</div><!--close col-sm-9-->
	</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
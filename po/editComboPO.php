<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM purchase_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
@extract($_POST);
////// final submit form ////
if($_POST['upd']=='Update'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	//update purchase_order_master ////
	$query1= "UPDATE purchase_order_master set po_value='".$sub_total."',discount='".$total_discount."' where po_no='".$docid."'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
	///// update in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   
	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			
		   $query2="update purchase_order_data set prod_code='".$prod_code[$k]."', req_qty='".$req_qty[$k]."', po_price='".$price[$k]."', po_value='".$linetotal[$k]."',discount='".$rowdiscount[$k]."',  totalval='".$total_val[$k]."',edtflg='Y' where id='".$prod_id[$k]."'";
		   $result1 = mysqli_query($link1, $query2);
		  
		   //// check if query is not executed
		   if (!$result1) {
	           $flag = false;
               $err_msg = "Error details2: " . mysqli_error($link1) . ".";
           }
		   $old_prod = explode("~",$oldprod_code[$k]);
		  ////// hold or release the PO qty in stock ////
		   //$flag=releaseStockQty($po_row['po_to'],$old_prod[0],$old_prod[1],$link1,$flag); 
		   //$flag=holdStockQty($po_row['po_to'],$prod_code[$k],$req_qty[$k],$link1,$flag);
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"CPO","EDIT",$ip,$link1,$flag);
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Purchase Order is Updated successfully  ";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$err_msg;
	} 
    mysqli_close($link1);
	//$msg="product return successfylly";
	///// move to parent page
    header("location:comboPurchaseOrderList.php?msg=".$msg."".$pagenav);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		width: 250
	  });
});
</script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script>
/////////// function to get available stock of ho
  function getAvlStk(indx){
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  var locationCode=$('#po_to').val();
	  var stocktype="okqty";
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{locstk:productCode,loccode:locationCode,stktype:stocktype,indxx:indx},
		success:function(data){
			var getdata=data.split("~");
	        document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
	    }
	  });
  }
/////// calculate line total /////////////
function rowTotal(ind){
  var row_discount="row_discount["+ind+"]";
  var ent_qty="req_qty["+ind+"]";
  var ent_rate="price["+ind+"]";
  var var1="linetotal["+ind+"]";
  var availableQty="avl_stock["+ind+"]";
  var prodCodeField="prod_code["+ind+"]";
  var discountField="rowdiscount["+ind+"]";
  var totalvalField="total_val["+ind+"]";
 
  ////// check if entered qty is something
  if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
  
  /////  check if entered price is somthing
  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  ///// check if discount value is something
  if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00; }
  ////// check entered qty should be available
  if(document.getElementById(availableQty).value){ var available=document.getElementById(availableQty).value;}else{ var available=0.00; }
  
  //if(parseFloat(qty) <= parseFloat(available) ){
	var total= parseFloat(qty)*parseFloat(price);
    if(parseFloat(total)>=parseFloat(dicountval)){
	document.getElementById(var1).value=(total);
	var dis=parseFloat(dicountval);
	//var discount=parseFloat(total)*parseFloat(dis);
     var totalcost= parseFloat(total)-parseFloat(dis);
     //alert(totalcost);
     document.getElementById(totalvalField).value=(totalcost);
     calculatetotal();
	}else{
	  alert("Discount is exceeding from price");
      var total= parseFloat(qty)*parseFloat(price);
	  document.getElementById(var1).value=(total);
	  // document.getElementById(discountField).value="0.00";
	  document.getElementById(discountField).value=document.getElementById(row_discount).value;
	  var disc=document.getElementById(discountField).value;
	  document.getElementById(totalvalField).value=parseFloat(total)-parseFloat(disc);
	  calculatetotal();
	}
 /* }else if(parseFloat(document.getElementById(availableQty).value)=='0.00'){
	  alert("Stock is not Available");  
	  document.getElementById(ent_qty).value="";
	  //document.getElementById(availableQty).value="";
	 // document.getElementById(ent_rate).value="";
	  document.getElementById(hold_rate).value="";
	  document.getElementById(prodCodeField).value="";
	  document.getElementById(prodmrpField).value="";
	  document.getElementById(prodCodeField).focus();
	  
  }
  else{
	  alert("Stock is not Available");
	  document.getElementById(ent_qty).value="";
	  //document.getElementById(availableQty).value="";
	  //document.getElementById(ent_rate).value="";
	  document.getElementById(hold_rate).value="";
	  document.getElementById(prodCodeField).value="";
	  document.getElementById(prodmrpField).value="";
	  document.getElementById(prodCodeField).focus();
  }*/
}
////// calculate final value of form /////
function calculatetotal(){
    var rowno=(document.getElementById("rowno").value);
	
	var sum_qty=0;
	var sum_total=0.00; 
	var sum_discount=0.00;
	var grand_toatal=0.00; 
    for(var i=1;i<rowno;i++){
		
		var temp_qty="req_qty["+i+"]";
		var temp_total="linetotal["+i+"]";
		var temp_discount="rowdiscount["+i+"]";
		var discountvar=0.00;
		var totalamtvar=0.00;
		var total="total_val["+i+"]";
		///// check if discount value is something
		if(document.getElementById(temp_discount).value){ discountvar= document.getElementById(temp_discount).value;}else{ discountvar=0.00;}
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}
		///// check if line qty is something
        if(document.getElementById(total).value){ grand= document.getElementById(total).value;}else{ grand=0.00;}
		sum_qty+=parseFloat(totqty);
		sum_total+=parseFloat(totalamtvar);
		sum_discount+=parseFloat(discountvar);
		grand_toatal+=parseFloat(grand);
		//alert(sum_discount);
	}/// close for loop	
document.getElementById("sub").value=formatCurrency(sum_total);
document.getElementById("total_discount").value=formatCurrency(sum_discount);
document.getElementById("grand").value=formatCurrency(grand_toatal);
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i>&nbsp;&nbsp;Edit Combo Purchase Order </h2><br/>
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
   <div class="panel-group">   
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_from'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Purchase Order No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Purchase Order Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Discount Type</label></td>
                <td><?php if($po_row['discount_type']=='PD') echo 'Productwise Discount';?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Combo</th>
                <th style="text-align:center" width="15%">Req. Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>			 
            <?php
			$i=1;
			$podata_sql="SELECT * FROM purchase_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td class="col-md-2" align="left">
                	<select name="prod_code[<?php echo $i;?>]" id="prod_code[<?php echo $i;?>]" class="form-control selectpicker" required data-live-search="true" onChange="checkDuplicate('<?php echo $i;?>', this.value);" style="width:200px">
                        <option value="">--None--</option>
                        <?php
                        $model_query = "SELECT bom_modelcode,bom_modelname FROM combo_master WHERE status='1' GROUP BY bom_modelcode";
                        $check1 = mysqli_query($link1, $model_query);
                        while ($br = mysqli_fetch_array($check1)) {
                            ?>
                            <option value="<?php echo $br['bom_modelcode'];?>"<?php if($br['bom_modelcode']==$podata_row['prod_code']){ echo "selected";}?>><?= $br['bom_modelname'] . ' | ' . $br['bom_modelcode']; ?></option>
                        <?php } ?>
                    </select>
                    </td>
				<td><input type="text" style="width:100px;" name="req_qty[<?php echo $i;?>]" id="req_qty[<?php echo $i;?>]" onBlur="rowTotal(<?php echo $i;?>);" class="form-control text-right" value="<?php echo $podata_row['req_qty'];?>"/></td>
				<td><input type="text" style="width:100px;" name="price[<?php echo $i;?>]" id="price[<?php echo $i;?>]" onBlur="rowTotal(<?php echo $i;?>);" class="form-control text-right" value="<?php echo $podata_row['po_price'];?>"></td>
				<td><input type="text" style="width:100px;" name="linetotal[<?php echo $i;?>]" id="linetotal[<?php echo $i;?>]" class="form-control text-right" value="<?php echo $podata_row['po_value'];?>" readonly></td>
				<td><input type="text" style="width:100px;" name="rowdiscount[<?php echo $i;?>]" id="rowdiscount[<?php echo $i;?>]" onBlur="rowTotal(<?php echo $i;?>);" class="form-control text-right" value="<?php echo $podata_row['discount'];?>"></td>
			        <td><input type="text" style="width:100px;" name="total_val[<?php echo $i;?>]" id="total_val[<?php echo $i;?>]" class="form-control text-right" value="<?php echo $podata_row['totalval'];?>" readonly></td>
				<input type="hidden" name="avl_stock[<?php echo $i;?>]" id="avl_stock[<?php echo $i;?>]" value="<?php echo getCurrentStock($po_row['po_to'],$podata_row['prod_code'],'okqty',$link1);?>"/>
			        <input type="hidden" style="width:100px;" name="row_discount[<?php echo $i;?>]" id="row_discount[<?php echo $i;?>]" class="form-control text-right" value="<?php echo $podata_row['discount'];?>">
				<input type="hidden" style="width:100px;" class="form-control" name="prod_id[<?php echo $i;?>]" id="prod_id[<?php echo $i;?>]" value="<?php echo($podata_row['id']);?>" readonly>
                <input type="hidden" style="width:100px;" class="form-control" name="oldprod_code[<?php echo $i;?>]" id="oldprod_code[<?php echo $i;?>]" value="<?php echo($podata_row['prod_code']."~".$podata_row['req_qty']);?>" readonly>
              </tr>			   
            <?php
			$i++;
			}
			?>
			<input type="hidden" name="rowno" id="rowno" value="<?php echo $i;?>" /> 
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"> <input type="text" name="sub_total" id="sub" class="form-control "   value="<?php echo $po_row['po_value'];?>"  readonly  /></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><input type="text" name="total_discount" id="total_discount" class="form-control"   value="<?php echo $po_row['discount'];?>"  readonly  /></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><input type="text" name="grnad" id="grand" class="form-control"   value="<?php echo ($po_row['po_value']-$po_row['discount']);?>"  readonly  /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
     <br><br>   
  </div><!--close panel group-->
  <div class="row" align="center">
   <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update">&nbsp;&nbsp;&nbsp;
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='comboPurchaseOrderList.php?<?=$pagenav?>'">
  </div>
  <br><br>
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
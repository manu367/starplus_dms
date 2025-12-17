<?php
////// Function ID ///////
$fun_id = array("u"=>array(25)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$toloctiondet=explode("~",getLocationDetails($_REQUEST['main_location'],"state,id_type,disp_addrs,city",$link1));
$frmloctiondet=explode("~",getLocationDetails($_REQUEST['stock_from'],"state,city",$link1));
@extract($_POST);
////// we hit save button
if ($_POST['upd']=='Save'){
	/// transcation parameter /////////////////////	
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	$main_location = base64_decode($mainlocation);
	$stock_from = base64_decode($stockfrom);
	$stock_to = base64_decode($stockto);
	$stock_movetype = base64_decode($stockmovetype);
	///// pick next system ref no. count
	$query_code="SELECT COUNT(id) as qa FROM stock_movement_master WHERE main_location='".$main_location."'";
	$result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
	$arr_result2=mysqli_fetch_array($result_code);
	$code_id=$arr_result2[0];
    /// make 4 digit padding
	$pad=str_pad(++$code_id,4,"0",STR_PAD_LEFT);
	//// make logic of system ref. no.
	$sysrefno = strtoupper($main_location)."/SM".$pad;
	///// Insert in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   	    
		// checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0){
			/////////// insert data
			$query2 = "INSERT INTO stock_movement_data SET doc_no='".$sysrefno."', main_location='".$main_location."', from_location='".$stock_from."', to_location='".$stock_to."', entry_date='".$datetime."', partcode='".$prod_code[$k]."', qty='".$req_qty[$k]."', price='".$price[$k]."', value='".$linetotal[$k]."', okqty='".$req_qty[$k]."',move_stocktype='".$stock_movetype."'";	
			$result2 = mysqli_query($link1, $query2);
			//// check if query is not executed
			if (!$result2) {
				$flag = false;
				$err_msg = "Error Code1:";
			}
	       	//$flag=holdStockQty($stock_from,$prod_code[$k],$req_qty[$k],$link1,$flag);
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	///// Insert Master Data
    $query1 = "INSERT INTO stock_movement_master SET doc_no='".$sysrefno."', main_location='".$main_location."', from_location='".$stock_from."', to_location='".$stock_to."', total_amt='".$total_amt."', total_qty='".$total_qty."', entry_date='".$datetime."', entry_by='".$_SESSION['userid']."', entry_ip='".$ip."', entry_remark='".$remark."', status='PFA',move_stocktype='".$stock_movetype."'";	
	$result = mysqli_query($link1, $query1);
	//// check if query is not executed
	if (!$result) {
		$flag = false;
		$err_msg = "Error Code1:";
	} 
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$sysrefno,"STOCK MOVEMENT","PFA",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Stock Movement is successfully placed with ref. no.".$sysrefno;
		$cflag = "success";
		$cmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:stock_move_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
    $("#frm2").validate();
});
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({
		trigger : 'hover',
		html : true
	});
});
function rePop(){
	$('[data-toggle="popover"]').popover({
		trigger : 'hover',
		html : true
	});
}
/// Currency Format//////////// 
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
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm="prod_code["+numi.value+"]";
        var qTy="req_qty["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(itm).value!="" && document.getElementById(qTy).value!="" && document.getElementById(qTy).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
			numi.value = num;
     		var r='<tr id="addr'+num+'"><td id="pdtid'+num+'"><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);"><option value="">--None--</option><?php $model_query="select productcode,productname,model_name from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['model_name'].' | '.$br['productcode'];?></option><?php }?></select></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" class="digits form-control" onkeyup="getAvlStk('+num+');"/></td><td><input  name="price['+num+']" id="price['+num+']" type="text" class="required form-control" readonly></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly></td><td><input type="text" class="form-control" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"  autocomplete="off"  readonly ><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td></tr>';
      		$('#itemsTable1').append(r);
	  		makeSelect();
		}
  	});
});
function makeSelect(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
  	});
}
///////////////////////////
////// delete product row///////////
function deleteRow(ind){  
  	//$("#addr"+(indx)).html(''); 
	var id="addr"+ind; 
	var itemid="prod_code"+"["+ind+"]";
	var qtyid="req_qty"+"["+ind+"]";
	var rateid="price"+"["+ind+"]";
	var lineTotal="linetotal["+ind+"]";
	var abl_qtyid="avl_stock"+"["+ind+"]";
	// hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemid).value="";
	document.getElementById(lineTotal).value="0.00";
	document.getElementById(qtyid).value="0.00";
	document.getElementById(rateid).value="0.00";
	document.getElementById(abl_qtyid).value="";
	document.getElementById("upd").disabled = false;
	document.getElementById("error").innerHTML = "";
  	rowTotal(ind);
}
</script>
<script type="text/javascript">
///// function for checking duplicate Product value
function checkDuplicate(fldIndx1, enteredsno) {  
 	document.getElementById("upd").disabled = false;
	if(enteredsno != '') {
		var check2 = "prod_code[" + fldIndx1 + "]";
		var flag = 1;
		for (var i = 0; i <= fldIndx1; i++) {
			var check1 = "prod_code[" + i + "]";
			if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
				if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
					alert("Duplicate Product Selection.");
					document.getElementById(check2).value = '';
					document.getElementById(check2).style.backgroundColor = "#F66";
					flag *= 0;
				}
				else {
					document.getElementById(check2).style.backgroundColor = "#FFFFFF";
					flag *= 1;
					///do nothing
				}
			}
		}//// close for loop
		if (flag == 0) {
			return false;
		} else {
			return true;
		}
		////// call stock available function
	}
}
/////////// function to get available stock of ho
function getAvlStk(indx){
	var productCode=document.getElementById("prod_code["+indx+"]").value;
	//alert(productCode+"----"+indx);
	var mainLocation = $('#main_location').val();
	var subLocation = $('#stock_from').val();
	//var stocktype="okqty";
	var stocktype = $('#move_stocktype').val();
	//alert(stocktype);
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{sm_mainloc:mainLocation, sm_subloc:subLocation, sm_partcode:productCode, stktype:stocktype, indxx:indx},
		success:function(data){
			//alert(data);
			var getdata=data.split("~");
	        document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
			get_price(getdata[1]);
	    }
	});
}
///// function to get price of product
function get_price(ind){
	var productCode2=document.getElementById("prod_code["+ind+"]").value;
	var price_pickstr=document.getElementById("pricepickstr").value;
	var pricestate=price_pickstr.split("~");
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{product:productCode2,locstate:pricestate[0],lctype:pricestate[1]},
		success:function(data){
			var splitprice=data.split("~");
			//alert(data+" ----"+ind);
			if(splitprice[0]){
	        	document.getElementById("price["+ind+"]").value=splitprice[0];
			}else{
				document.getElementById("price["+ind+"]").value="0.00";
			}
			rowTotal(ind);
	    }
	});
}
/////// calculate line total /////////////
function rowTotal(ind){
	var ent_qty="req_qty["+ind+"]";
	var ent_rate="price["+ind+"]";
	var availableQty="avl_stock["+ind+"]";
	var prodCodeField="prod_code["+ind+"]";
  	////// check if entered qty is something
  	if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
  	/////  check if entered price is somthing
  	if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  	////// check entered qty should be available
    if (parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)) {
		if(parseFloat(qty) > "0"){
			if((document.getElementById(availableQty).value)==''){
				document.getElementById("upd").disabled = true;
				document.getElementById("error").innerHTML = "Stock is not Available";
			}
			else {
				document.getElementById("upd").disabled = false;
				document.getElementById("error").innerHTML = "";
			}
			/////////////////////
			var total= parseFloat(qty)*parseFloat(price);
			var var3="linetotal["+ind+"]";
			document.getElementById(var3).value=formatCurrency(total);
			calculatetotal();
		}
		else{
		  document.getElementById(ent_qty).value="";
		  //document.getElementById(ent_rate).value="";
		  //document.getElementById(prodCodeField).value="";
		  document.getElementById(prodCodeField).focus();
		  calculatetotal();
		}
	}else{
		document.getElementById(ent_qty).value="";
		//document.getElementById(ent_rate).value="";
		//document.getElementById(prodCodeField).value="";
		document.getElementById(prodCodeField).focus();
		document.getElementById("upd").disabled = true;
		document.getElementById("error").innerHTML = "Stock is not Available";
		calculatetotal();
	}
}
////// calculate final value of form /////
function calculatetotal(){
    var rowno=(document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
    for(var i=0;i<=rowno;i++){
		var temp_qty="req_qty["+i+"]";
		var temp_total="linetotal["+i+"]";
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ var totalamtvar= document.getElementById(temp_total).value;}else{ var totalamtvar=0.00;}
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ var totqty= document.getElementById(temp_qty).value;}else{ var totqty=0;}
		
		sum_qty+=parseFloat(totqty);
		sum_total+=parseFloat(totalamtvar);	
	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("total_amt").value=formatCurrency(sum_total);
}
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-cubes"></i> Add Stock Movement </h2>
      		<div class="form-group" id="page-wrap" style="margin-left:10px;">
          	<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          		<div class="form-group">
            		<div class="col-md-10"><label class="col-md-5 control-label">Main Location <span style="color:#F00">*</span></label>
              			<div class="col-md-7">
                 			<select name="main_location" id="main_location" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    			<option value="" selected="selected">Please Select </option>
                                <?php                                 
                                $sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('HO','BR')";
                                $res_chl=mysqli_query($link1,$sql_chl);
                                while($result_chl=mysqli_fetch_array($res_chl)){
                                $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                                ?>
                   				<option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['main_location'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
								<?php
                                }
                                ?>
                 			</select>
              			</div>
            		</div>
          		</div>
                <div class="form-group">
            		<div class="col-md-10"><label class="col-md-5 control-label">Stock Move From <span style="color:#F00">*</span></label>
              			<div class="col-md-7">
                 			<select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    			<option value="" selected="selected">Please Select </option>
                                <?php                                 
                                $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['main_location']."'";
                                $smfm_res = mysqli_query($link1,$smfm_sql);
                                while($smfm_row = mysqli_fetch_array($smfm_res)){
                                ?>
                   				<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
								<?php
                                }
                                ?>
								<?php                                 
                                $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['main_location']."' AND status='Active'";
                                $smf_res = mysqli_query($link1,$smf_sql);
                                while($smf_row = mysqli_fetch_array($smf_res)){
                                ?>
                   				<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
								<?php
                                }
                                ?>
                 			</select>
              			</div>
            		</div>
          		</div>
          		<div class="form-group">
            		<div class="col-md-10"><label class="col-md-5 control-label">Stock Move To <span style="color:#F00">*</span></label>
              			<div class="col-md-7">
                 			<select name="stock_to" id="stock_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 				<option value="" selected="selected">Please Select </option>
								<?php
								if($_REQUEST['main_location']!=$_REQUEST['stock_from']){
									$smtm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['main_location']."'";
									$smtm_res = mysqli_query($link1,$smtm_sql);
									while($smtm_row = mysqli_fetch_array($smtm_res)){
									?>
									<option value="<?=$smtm_row['asc_code']?>" <?php if($smtm_row['asc_code']==$_REQUEST['stock_to'])echo "selected";?>><?=$smtm_row['name']." | ".$smtm_row['city']." | ".$smtm_row['state']." | ".$smtm_row['asc_code']?></option>
									<?php
									}
								}
                                ?>
								<?php                                 
                                $smt_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['main_location']."' AND sub_location!='".$_REQUEST['stock_from']."' AND status='Active'";
                                $smt_res = mysqli_query($link1,$smt_sql);
                                while($smt_row = mysqli_fetch_array($smt_res)){
                                ?>
                   				<option value="<?=$smt_row['sub_location']?>" <?php if($smt_row['sub_location']==$_REQUEST['stock_to'])echo "selected";?>><?=$smt_row['sub_location_name']." | ".$smt_row['sub_location']?></option>
								<?php
                                }
                                ?>
                         	</select>
                      	</div>
                    </div>
            	</div>
                <div class="form-group">
            		<div class="col-md-10"><label class="col-md-5 control-label">Stock Move Type <span style="color:#F00">*</span></label>
              			<div class="col-md-7">
                 			<select name="move_stocktype" id="move_stocktype" required class="form-control required" onChange="document.frm1.submit();">
                 				<option value="okqty"<?php if($_REQUEST['move_stocktype']=="okqty"){ echo "selected";}?>>OK</option>
								<option value="broken"<?php if($_REQUEST['move_stocktype']=="broken"){ echo "selected";}?>>DAMAGE</option>
                         	</select>
                      	</div>
                    </div>
            	</div>		  
         	</form>
         	<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          		<div class="form-group">
          			<table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            			<thead>
              				<tr class="<?=$tableheadcolor?>">
                                <th width="40%" style="font-size:13px;">Product</th>                                
                                <th width="10%" style="font-size:13px">Qty</th>
                                <th width="15%" style="font-size:13px">Price</th>
                                <th width="20%" style="font-size:13px">Value</th>
                                <th width="15%" style="font-size:13px">Avl Stock</th>									
							</tr>
            			</thead>
            			<tbody>
              				<tr id='addr0'>
                				<td id="pdtid0">                                	
                                    <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="checkDuplicate(0, this.value);getAvlStk(0);">
                                        <option value="">--None--</option>
                                        <?php                                             
                                        $model_query="select productcode,productname,model_name from product_master where status='active'";
                                        $check1=mysqli_query($link1,$model_query);
                                        while($br = mysqli_fetch_array($check1)){?>
                                        <option value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['model_name'].' | '.$br['productcode'];?></option>
                                        <?php }?>                        
                                    </select>
                				</td>
                                <td><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]" autocomplete="off" required onKeyUp="getAvlStk(0);"></td>
                                <td><input type="text" class="form-control" name="price[0]" id="price[0]" autocomplete="off" readonly required></td>
                                <td><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly></td>
                                <td><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" value="0" readonly></td>	
              				</tr>
						</tbody>
            			<tfoot id='productfooter' style="z-index:-9999;">
                        	<tr class="0">
                            	<td colspan="14" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
                          	</tr>
            			</tfoot>
					</table>
          		</div>
          		<div class="form-group">
            		<div class="col-md-10">
                    	<label class="col-md-3 control-label">Total Amount</label>
              			<div class="col-md-3">
                			<input type="text" name="total_amt" id="total_amt" class="form-control" value="0.00" readonly/>
              			</div>
              			<label class="col-md-3 control-label">Total Qty</label>
              			<div class="col-md-3">
              				<input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              			</div>
            		</div>
          		</div>
          		<div class="form-group">
            		<div class="col-md-10">
              			<label class="col-md-3 control-label">Remark</label>
              			<div class="col-md-3">
                			<textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
              			</div>
					</div>
          		</div>
          		<div class="form-group">
            		<div class="col-md-12" align="center">
                        <input type="hidden" name="mainlocation" id="mainlocation" value="<?=base64_encode($_REQUEST['main_location'])?>"/>
                        <input type="hidden" name="stockfrom" id="stockfrom" value="<?=base64_encode($_REQUEST['stock_from'])?>"/>
                        <input type="hidden" name="stockto" id="stockto" value="<?=base64_encode($_REQUEST['stock_to'])?>"/>
                        <input type="hidden" name="stockmovetype" id="stockmovetype" value="<?=base64_encode($_REQUEST['move_stocktype'])?>"/>
                        <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~".$toloctiondet[1]?>"/>
                        <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save">
			  			<span id="error" name = "error" class="red_small"></span>
						<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='stock_move_list.php?<?=$pagenav?>'">
            		</div>
          		</div>
         	</form>
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
<?php if($_REQUEST['main_location']=='' || $_REQUEST['stock_to']=='' || $_REQUEST['stock_from']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>
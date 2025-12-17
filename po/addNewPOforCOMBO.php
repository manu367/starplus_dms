<?php
////// Function ID ///////
$fun_id = array("u"=>array(126)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$toloctiondet=explode("~",getLocationDetails($_REQUEST['po_from'],"state,id_type,disp_addrs,city",$link1));
$frmloctiondet=explode("~",getLocationDetails($_REQUEST['po_to'],"state,city",$link1));
@extract($_POST);
///// extract lat long
$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
////// we hit save button
if($_POST){
 if ($_POST['upd']=='Save'){
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from purchase_order_master where po_from='".$partycode."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po[no]+1;
	$po_no=$partycode."CPO".$c_nos; 
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	$se = getAnyDetails($sales_executive1,'name','username','admin_users',$link1);
	if(substr($partycode,0,4)=="EADL"){ $saletype = "SECONDARY";}else if(substr($partycode,0,4)=="EADS" || substr($partycode,0,4)=="EART"){ $saletype = "PRIMARY";}else{ $saletype = "TERTIARY";}
	
	///// Insert Master Data
	$query1= "INSERT INTO purchase_order_master set po_to='".$parentcode."',po_from='".$partycode."',po_no='".$po_no."',temp_no='".$c_nos."',ref_no='".$ref_no."',requested_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',req_type='COMBO PO',status='PFA',po_value='".$sub_total."',create_by='".$_SESSION['userid']."',ip='".$ip."',sales_person='".$se."',sales_executive='".$sales_executive1."',payment_status='".$payment_terms."',transport_exp='',discount='".$total_discount."',discount_type='PD',remark='".$remark."',delivery_address='".$delivery_address."',address='',latitude='".$latlong[0]."',longitude='".$latlong[1]."',pjp_id='".$_REQUEST['task_id']."', sale_type='".$saletype."'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	///// Insert in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   
	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			///// get product details
			//$proddet = explode("~",getAnyDetails($val,"productcategory,productsubcat,brand","productcode","product_master",$link1));
			/////////// insert data
		   $query2="insert into purchase_order_data set po_no='".$po_no."', prod_code='".$val."',prod_cat='C',psc_id='".$proddet[1]."',brand_id='".$proddet[2]."', req_qty='".$req_qty[$k]."', po_price='".$price[$k]."', po_value='".$linetotal[$k]."', hold_price='".$holdRate[$k]."', mrp='".$mrp[$k]."', discount='".$rowdiscount[$k]."', totalval='".$total_val[$k]."',warranty='COMBO',expected_deliv_date='".$deliv_date[$k]."', sale_type='".$saletype."'";
		   $result2 = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result2) {
	           $flag = false;
               $err_msg =  "Error details2: " . mysqli_error($link1) . ".";
           }
		   ////// hold the PO qty in stock ////
	       //$flag=holdStockQty($parentcode,$val,$req_qty[$k],$link1,$flag);
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	if($_REQUEST['task_id']){
   	   $result3 = mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['task_id']."'");
		//// check if query is not executed
	   if (!$result3) {
		   $flag = false;
		   $err_msg =  "Error details3: " . mysqli_error($link1) . ".";
	   }
		$_REQUEST['task_id'] = "";
		unset($_REQUEST);
   }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"CPO","ADD",$ip,$link1,$flag);
	
	/////// add script when dealer PO to Distributor then it should be auto approved .requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022
	if(substr($partycode,0,4)=="EADL"){
		$actiontaken = "Approved";
		///// update po status ///////////
		$res_upd = mysqli_query($link1,"UPDATE purchase_order_master set status='".$actiontaken."' where po_no='".$po_no."'");
		  //// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $err_msg = "Error details5: " . mysqli_error($link1) . ".";
		}
		////// insert in approval table////
		$flag = approvalActivity($po_no,$today,"CPO",$_SESSION['userid'],$actiontaken,$today,$currtime,"AUTO APPROVED",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$po_no,"CPO APPROVAL","APPROVAL",$ip,$link1,$flag);
	}
	 /////// end add script when dealer PO to Distributor then it should be auto approved .requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Purchase Order is successfully placed with ref. no.".$po_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$err_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:purchaseOrderList.php?msg=".$msg."".$pagenav);
    exit;
 }
}
///get access product
//$acc_psc = getAccessProduct($_SESSION['userid'],$link1);
///get access brand
//$acc_brd = getAccessBrand($_SESSION['userid'],$link1);
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
</script>
<style type="text/css">
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript">
///// get product specification devlop by shekhar on 26 march 2019
function getSpecification(comboid,indx){
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{comboProduct:comboid,indxx:indx},
		success:function(data){
			var getdata=data.split("~");
	        document.getElementById("prd_desc"+getdata[1]+"").innerHTML = "<a href='#' title='Combo Details' data-toggle='popover' data-trigger='focus' data-content='"+getdata[0]+"'><i class='fa fa-film'></i></a>";
			rePop();
			//getDeliveryDate(getdata[2],getdata[1]);
	    }
	  });
}
////// get delivery date update on 26 march 2019 by shekhar
function getDeliveryDate(prodcat,indx){
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{prodDelivdate:prodcat,fromloc:'<?=$frmloctiondet[1]?>',toloc:'<?=$toloctiondet[3]?>',indxx:indx},
		success:function(data){
			//alert(data);
			var getdata=data.split("~");
	        document.getElementById("deliv_date["+getdata[1]+"]").value = getdata[0];
	    }
	  });
}
/////////// function to get available stock of ho
  function getAvlStk(indx){
	  /*var productCode=document.getElementById("prod_code["+indx+"]").value;
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
	  });*/
  }
///// function to get price of product
function get_price(ind){
	var productCode=document.getElementById("prod_code["+ind+"]").value;
	var price_pickstr=document.getElementById("pricepickstr").value;
	/*var pricestate=price_pickstr.split("~");
	 	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{product:productCode,locstate:pricestate[0],lctype:pricestate[1]},
		success:function(data){
			var splitprice=data.split("~");
	        document.getElementById("price["+ind+"]").value=formatCurrency(splitprice[0]);
			document.getElementById("holdRate["+ind+"]").value=formatCurrency(splitprice[0]);
		    document.getElementById("mrp["+ind+"]").value=formatCurrency(splitprice[1]);
	    }
	  });*/
	  getSpecification(productCode,ind);
}  
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm="prod_code["+numi.value+"]";
        var qTy="req_qty["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(itm).value!="" && document.getElementById(qTy).value!="" && document.getElementById(qTy).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
     	var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:300px"><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);get_price('+num+');"><option value="">--None--</option><?php $model_query="SELECT bom_modelcode,bom_modelname FROM combo_master WHERE status='1' GROUP BY bom_modelcode";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['bom_modelcode'];?>"><?php echo $br['bom_modelname'].' | '.$br['bom_modelcode'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" onKeyUp=rowTotal('+num+'); class="digits form-control"/></td><td><input  name="price['+num+']" id="price['+num+']" type="text" class="required form-control number" onKeyUp="rowTotal('+num+');" style="width:100px;"></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly></td><td><input type="text" class="form-control number" name="rowdiscount['+num+']" id="rowdiscount['+num+']" autocomplete="off" onKeyUp="rowTotal('+num+');" style="width:100px;"></td><td><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td><input name="warranty_days['+num+']" id="warranty_days['+num+']" type="hidden" class="form-control" readonly/><input name="deliv_date['+num+']" id="deliv_date['+num+']" type="text" class="form-control" readonly/></td></tr>';
      $('#itemsTable1').append(r);
	  makeSelect();
	  //$('.selectpicker').selectpicker('refresh');
	  //$(document).ready(function() {
/*		  $('.selectpicker').selectpicker({
			liveSearch: true,
			showSubtext: true
	      });*/
	//});
	  //getDropdown(num);
	  <?php /*?><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required><option value="">--None--</option><?php $model_query="select model from model_master where status='Active'";$check1=mysql_query($model_query);while($br = mysql_fetch_array($check1)){?><option data-tokens="<?php echo $br['model'];?>" value="<?php echo $br['model'];?>"><?php echo $br['model'];?></option><?php }?></select><?php */?>
/*	$("#make_select").change(function() {
    var make_id = $(this).find(":selected").val();
    var request = $.ajax({
        type: 'GET',
        url: '/models/' + make_id + '/',
    });
    request.done(function(data){
        var option_list = [["", "--- Select One ---"]].concat(data);

        $("#model_select").empty();
        for (var i = 0; i < option_list.length; i++) {
            $("#model_select").append(
                $("<option></option>").attr(
                    "value", option_list[i][0]).text(option_list[i][1])
            );
        }
        $('#model_select').selectpicker('refresh');
    });
});*/
	  
	  //document.getElementById('rowno').value=ne; $('.selectpicker').selectpicker();
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
	 var totalid="total_val"+"["+ind+"]";
	 var lineTotal="linetotal["+ind+"]";
	 var mrpid="mrp"+"["+ind+"]";
	 var holdRateid="holdRate"+"["+ind+"]";
	 var discountField="rowdiscount["+ind+"]";
	 var abl_qtyid="avl_stock"+"["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemid).value="";
	document.getElementById(lineTotal).value="0.00";
	document.getElementById(qtyid).value="0.00";
	document.getElementById(rateid).value="0.00";
	document.getElementById(totalid).value="0.00";
	document.getElementById(mrpid).value="0.00";
	document.getElementById(holdRateid).value="0.00";
	document.getElementById(discountField).value="0.00";
	document.getElementById(abl_qtyid).value="0.00";
  rowTotal(ind);
}
/////// calculate line total /////////////
function rowTotal(ind){
  var ent_qty="req_qty["+ind+"]";
  var ent_rate="price["+ind+"]";
  var hold_rate="holdRate["+ind+"]";
  var availableQty="avl_stock["+ind+"]";
  var prodCodeField="prod_code["+ind+"]";
  var prodmrpField="mrp["+ind+"]";
  var discountField="rowdiscount["+ind+"]";
  var totalvalField="total_val["+ind+"]";
  var holdRate=document.getElementById(hold_rate).value;
  ////// check if entered qty is something
  if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
  /////  check if entered price is somthing
  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  ///// check if discount value is something
  if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00; }
  ////// check entered qty should be available
  if(parseFloat(qty) > "0" ){
  	var total= parseFloat(qty)*parseFloat(price);
    if(parseFloat(total)>=parseFloat(dicountval)){
     var totalcost= parseFloat(total)-parseFloat(dicountval);
     var var3="linetotal["+ind+"]";
     document.getElementById(var3).value=formatCurrency(total);
     document.getElementById(totalvalField).value=formatCurrency(totalcost);
     calculatetotal();
	}else{
	  alert("Discount is exceeding from price");
      var total= parseFloat(qty)*parseFloat(price);
      var var3="linetotal["+ind+"]";
	  document.getElementById(var3).value=formatCurrency(total);
	  document.getElementById(discountField).value="0.00";
	  document.getElementById(totalvalField).value=formatCurrency(total);
	  calculatetotal();
	}
  }else if(parseFloat(document.getElementById(availableQty).value)=='0.00'){
	  alert("Stock is not Available");  
	  document.getElementById(ent_qty).value="";
	  //document.getElementById(availableQty).value="";
	  document.getElementById(ent_rate).value="";
	  document.getElementById(hold_rate).value="";
	  document.getElementById(prodCodeField).value="";
	  document.getElementById(prodmrpField).value="";
	  document.getElementById(prodCodeField).focus();
	  
  }
  else{
	  alert("Stock is not Available");
	  document.getElementById(ent_qty).value="";
	  //document.getElementById(availableQty).value="";
	  document.getElementById(ent_rate).value="";
	  document.getElementById(hold_rate).value="";
	  document.getElementById(prodCodeField).value="";
	  document.getElementById(prodmrpField).value="";
	  document.getElementById(prodCodeField).focus();
  }
}
////// calculate final value of form /////
function calculatetotal(){
    var rowno=(document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
	var sum_discount=0.00;
    for(var i=0;i<=rowno;i++){
		var temp_qty="req_qty["+i+"]";
		var temp_total="linetotal["+i+"]";
		var temp_discount="rowdiscount["+i+"]";
		var discountvar=0.00;
		var totalamtvar=0.00;
		///// check if discount value is something
		if(document.getElementById(temp_discount).value){ discountvar= document.getElementById(temp_discount).value;}else{ discountvar=0.00;}
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}
		
		sum_qty+=parseFloat(totqty);
		sum_total+=parseFloat(totalamtvar);
		sum_discount+=parseFloat(discountvar);
	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=formatCurrency(sum_total);
    <?php //if($_REQUEST[discount_type]=="PD"){ ?>	
    document.getElementById("total_discount").value=formatCurrency(sum_discount);
    <?php //} ?>
	document.getElementById("grand_total").value=formatCurrency(parseFloat(sum_total)-parseFloat(sum_discount));
}
///// check total discount is exceeding from total minimum price of all product
function check_total_discount(){
	if(parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)){
	  alert("Discount is exceeding..!!");
	  document.getElementById("total_discount").value="0.00";
	  document.getElementById("grand_total").value=formatCurrency(parseFloat(document.getElementById("sub_total").value));
	}else{
	  document.getElementById("grand_total").value=formatCurrency(parseFloat(document.getElementById("sub_total").value)-parseFloat(document.getElementById("total_discount").value));	
	}
}
</script>
<script type="text/javascript">
            ///// function for checking duplicate Product value
            function checkDuplicate(fldIndx1, enteredsno) {  
			 document.getElementById("upd").disabled = false;
                if (enteredsno != '') {
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
                }
				
            }
		</script>
</head>
<body onKeyPress="return keyPressed(event);">
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-shopping-basket"></i> Add New PO For COMBO</h2>
      			<div class="form-group" id="page-wrap" style="margin-left:10px;">
          			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          				<div class="form-group">
            				<div class="col-md-10"><label class="col-md-5 control-label">Purhase Order From <span style="color:#F00">*</span></label>
              					<div class="col-md-7">
                 					<select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    					<option value="" selected="selected">Please Select </option>
										<?php 
                                        $sql_chl = "SELECT * FROM access_location WHERE uid='".$_SESSION['userid']."' AND status='Y' AND id_type NOT IN ('HO','BR')";
                                        $res_chl=mysqli_query($link1,$sql_chl);
                                        while($result_chl=mysqli_fetch_array($res_chl)){
                                              $party_det=mysqli_fetch_array(mysqli_query($link1,"SELECT name, city, state, id_type FROM asc_master WHERE asc_code='".$result_chl['location_id']."'"));?>
                    					<option value="<?=$result_chl['location_id']?>"<?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                    					<?php
										}
                    					?>
                 					</select>
              					</div>
            				</div>
          				</div>
          				<div class="form-group">
            				<div class="col-md-10"><label class="col-md-5 control-label">Purhase Order To <span style="color:#F00">*</span></label>
              					<div class="col-md-7">
                 					<select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 						<option value="" selected="selected">Please Select </option>
										<?php 
                                        $sql_parent="select uid from mapped_master where mapped_code='".$_REQUEST['po_from']."' AND uid!='NONE'";
                                        $res_parent=mysqli_query($link1,$sql_parent);
                                        if(mysqli_num_rows($res_parent)>0){
                                            while($result_parent=mysqli_fetch_array($res_parent)){
                                              $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[uid]'"));
                                        ?>
                    					<option value="<?=$result_parent['uid']?>"<?php if($result_parent['uid']==$_REQUEST['po_to'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['uid']?></option>
                    					<?php
										}
									}else{
										if($toloctiondet[1]=="DL"){
											$res_pty = mysqli_query($link1,"SELECT asc_code, name, city, state, id_type FROM asc_master WHERE id_type='DS' AND status='Active' AND state='".$toloctiondet[0]."'");
										}else{
											$res_pty = mysqli_query($link1,"SELECT asc_code, name, city, state, id_type FROM asc_master WHERE id_type='HO' AND status='Active' AND state='".$toloctiondet[0]."'");
										}
										while($row_pty = mysqli_fetch_assoc($res_pty)){
										?>
                    					<option value="<?=$row_pty['asc_code']?>" <?php if($row_pty['asc_code']==$_REQUEST['po_to'])echo "selected";?>><?=$row_pty['name']." | ".$row_pty['city']." | ".$row_pty['state']." | ".$row_pty['asc_code']?></option>
										<?php 
                                            }
                                        }
                                        ?>
                 					</select>
              					</div>
            				</div>
          				</div> 
		  				<div class="form-group">
            				<div class="col-md-10"><label class="col-md-5 control-label">Sales Executive <span style="color:#F00">*</span></label>
              					<div class="col-md-7">
                 					<select name="sales_executive" id="sales_executive" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 						<option value="" <?php if($_REQUEST['sales_executive'] == "") { echo "selected"; } ?> >Please Select </option>
										<?php 
                                        $sql_se="select name, username, utype,oth_empid from admin_users where utype in ('2','3','4','5','6','7') and status='Active'";
                                        $res_se=mysqli_query($link1,$sql_se);
                                        while($result_se=mysqli_fetch_array($res_se)){
                                        ?>
                                        <option value="<?=$result_se['username']?>"<?php if($result_se['username']==$_REQUEST['sales_executive']) { echo "selected"; } ?> ><?=$result_se['name']." | ".$result_se['username']." | ".$result_se['oth_empid']?></option>
										<?php
                                        }
                                        ?>
                 					</select>
             		 			</div>
            				</div>
          				</div>
          				<div class="form-group">
          					<div class="col-md-10"><label class="col-md-5 control-label">Available Credit Balance</label>
              					<div class="col-md-3">
                					<input type="text" name="cr_bal" id="cr_bal" class="form-control" value="<?=getCRBAL($_REQUEST['po_from'],$_REQUEST['po_to'],$link1);?>" readonly/>
              					</div>
              					<label class="col-md-1 control-label">
								<!--Discount Type-->
              					</label>
              					<div class="col-md-3">
								<!--<select name="discount_type" id="discount_type" required class="form-control required" onChange="document.frm1.submit();">
                                  <option value="NONE"<?php if($_REQUEST['discount_type']=="NONE")echo "selected";?>>NONE</option>
                                  <option value="PD"<?php if($_REQUEST['discount_type']=="PD")echo "selected";?>>Productwise Discount</option>
                                  <option value="TD"<?php if($_REQUEST['discount_type']=="TD")echo "selected";?>>Total Discount</option>
                                </select>-->
              				</div>
          				</div>
        			</div>
         		</form>
         		<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          			<div class="form-group">
          				<table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            				<thead>
              					<tr class="<?=$tableheadcolor?>" >
                                    <th data-class="expand" class="col-md-3" style="font-size:13px;">Combo</th>
                                    <th class="col-md-1" style="font-size:13px">Qty</th>
                                    <th data-hide="phone"  class="col-md-1" style="font-size:13px">Price</th>
                                    <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>
                                    <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Discount</th>
                                    <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Total</th>
                                    <!--<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Warranty Days</th>-->
                                    <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Delivery Date</th>
                              	</tr>
            				</thead>
            				<tbody>
              					<tr id='addr0'>
                					<td class="col-md-3">
                    					<div id="pdtid0" style="display:inline-block;float:left; width:300px">
                  							<select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk(0);get_price(0);checkDuplicate(0, this.value);">
                    							<option value="">--None--</option>
												<?php  
                                                $model_query = "SELECT bom_modelcode,bom_modelname FROM combo_master WHERE status='1' GROUP BY bom_modelcode";
                                                $check1=mysqli_query($link1,$model_query);
                                                while($br = mysqli_fetch_array($check1)){?>
            									<option value="<?php echo $br['bom_modelcode'];?>"><?php echo $br['bom_modelname'].' | '.$br['bom_modelcode'];?></option>
                    							<?php }?>
                  							</select>
                    					</div>
                    					<div id="prd_desc0" style="display:inline-block;float:right"></div>
                					</td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onKeyUp="rowTotal(0);"></td>
                <td class="col-md-1"><input type="text" class="form-control number" name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" required style="width:100px;"></td>
                <td class="col-md-2"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly></td>
                <td class="col-md-1"><input type="text" class="form-control number" name="rowdiscount[0]" id="rowdiscount[0]" autocomplete="off" onKeyUp="rowTotal(0);" style="width:100px;"></td>
                <td class="col-md-2"><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly>
				  <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                     <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
				</td>
                <!--<td class="col-md-2"></td>-->
                <td class="col-md-2"><input name="warranty_days[0]" id="warranty_days[0]" type="hidden" class="form-control" style="width:100px;" readonly/><input name="deliv_date[0]" id="deliv_date[0]" type="text" class="form-control" style="width:100px;" readonly/></td>
              </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="9" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Sub Total</label>
              <div class="col-md-3">
                <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly/>
              </div>
              <label class="col-md-3 control-label">Total Qty</label>
              <div class="col-md-3">
              <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Total Discount</label>
              <div class="col-md-3">
                <input type="text" name="total_discount" id="total_discount" class="form-control" value="0.00" onKeyUp="check_total_discount();" />
              </div>
              <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
              <div class="col-md-3">
                <textarea name="delivery_address" id="delivery_address" class="form-control required addressfield" style="resize:none" required><?=$toloctiondet[2]?></textarea>
              </div>
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Payment Terms</label>
              <div class="col-md-3">
                <textarea name="payment_terms" id="payment_terms" class="form-control addressfield" style="resize:none"></textarea>
              </div>
              <label class="col-md-3 control-label">Reference No.</label>
              <div class="col-md-3">
                <input type="text" name="ref_no" id="ref_no" class="form-control addressfield" value=""/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This PO">
                <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['po_to']?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_from']?>"/>
				
				<input type="hidden" name="sales_executive1" id="sales_executive1" value="<?=$_REQUEST['sales_executive']?>"/>
<!--                <input type="hidden" name="disc_type" id="disc_type" value="<?=$_REQUEST['discount_type']?>"/>-->
                <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~".$toloctiondet[1]?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='purchaseOrderList.php?<?=$pagenav?>'">
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
<?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>
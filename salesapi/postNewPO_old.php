<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
/////////////////
$toloctiondet=explode("~",getLocationDetails($_REQUEST['po_from'],"state,id_type,disp_addrs,city",$link1));
$frmloctiondet=explode("~",getLocationDetails($_REQUEST['po_to'],"state,city",$link1));
@extract($_POST);
///// extract lat long
//$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
$lat = $_REQUEST['latitude'];
$long = $_REQUEST['longitude'];
$trackaddrs = $_REQUEST['trackaddress'];
$trackdistc = $_REQUEST['trackdistance'];
////// we hit save button
if($_POST){
 if ($_POST['upd']=='Save'){
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from purchase_order_master where po_from='".$partycode."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
	$po_no=$partycode."SO".$c_nos; 
	mysqli_autocommit($link1, false);
	$flag = true;
	$se = getAnyDetails($sales_executive1,'name','username','admin_users',$link1);
	if(substr($partycode,0,4)=="EADL"){ $saletype = "SECONDARY";}else if(substr($partycode,0,4)=="EADS"){ $saletype = "PRIMARY";}else{ $saletype = "TERTIARY";}
	///// Insert Master Data
	$query1= "INSERT INTO purchase_order_master set po_to='".$parentcode."',po_from='".$partycode."',po_no='".$po_no."',temp_no='".$c_nos."',ref_no='".$ref_no."',requested_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',req_type='SO',status='PFA',po_value='".$sub_total."',create_by='".$_REQUEST['usercode']."',ip='".$ip."',sales_person='".$se."',sales_executive='".$sales_executive1."',payment_status='".$payment_terms."',transport_exp='',discount='".$total_discount."',discount_type='PD',remark='".$remark."',delivery_address='".$delivery_address."',address='".$trackaddrs."',latitude='".$lat."',longitude='".$long."',pjp_id='".$_REQUEST['taskid']."',entry_from='APP', sale_type='".$saletype."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
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
			$proddet = explode("~",getAnyDetails($val,"productcategory,productsubcat,brand","productcode","product_master",$link1));
			/////////// insert data
		   	$query2="insert into purchase_order_data set po_no='".$po_no."', prod_code='".$val."',prod_cat='".$proddet[0]."',psc_id='".$proddet[1]."',brand_id='".$proddet[2]."', req_qty='".$req_qty[$k]."', po_price='".$price[$k]."', po_value='".$linetotal[$k]."', hold_price='".$holdRate[$k]."', mrp='".$mrp[$k]."', discount='".$rowdiscount[$k]."', totalval='".$total_val[$k]."',warranty='".$warranty_days[$k]."',expected_deliv_date='".$deliv_date[$k]."', sale_type='".$saletype."'";
		   $result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error details2: " . mysqli_error($link1) . ".";
           }
		   ////// hold the PO qty in stock ////
	       $flag=holdStockQty($parentcode,$val,$req_qty[$k],$link1,$flag);
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	if($_REQUEST['taskid']){
   		$result = mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['taskid']."'");
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error details3: " . mysqli_error($link1) . ".";
		}
		$_REQUEST['taskid'] = "";
   	}
   	$result = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_REQUEST['usercode']."', task_name='Sales Order', task_action='Create', ref_no='".$po_no."', latitude='".$lat."', longitude='".$long."', address='".$trackaddrs."',travel_km='".$trackdistc."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
	//// check if query is not executed
	if (!$result) {
		 $flag = false;
		 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag=dailyActivity($_REQUEST['usercode'],$po_no,"SO","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Sales Order is successfully placed with ref. no.".$po_no;
		$respheadmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$err_msg;
		$respheadmsg = "Failed";
	} 
    mysqli_close($link1);
	$headerline = "Sales Order";
	///// move to parent page
    //header("location:purchaseOrderList.php?msg=".$msg."".$pagenav);
	header("Location:processpage.php?respmsg=".base64_encode($msg)."&usercode=".$_REQUEST['usercode']."&latitude=".$lat."&longitude=".$long."&taskid=".$_REQUEST['taskid']."&respheadmsg=".$respheadmsg."&headerline=".$headerline);
    exit;
 }
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

/*$(document).ready(function(){
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
}*/
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
<script type="text/javascript">
///// get product specification devlop by shekhar on 26 march 2019
function getSpecification(prodid,indx){
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{prodSpecif:prodid,indxx:indx},
		success:function(data){
			var getdata=data.split("~");
	        //document.getElementById("prd_desc"+getdata[1]+"").innerHTML = "<a href='#' title='Product Specification' data-toggle='popover' data-trigger='focus' data-content='"+getdata[0]+"'><i class='fa fa-film'></i></a>";
			//rePop();
			document.getElementById("warranty_days["+getdata[1]+"]").value = getdata[3];
			getDeliveryDate(getdata[2],getdata[1]);
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
			if(getdata[0]){
	        	document.getElementById("deliv_date["+getdata[1]+"]").value = getdata[0];
			}
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
	        //document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
	    }
	  });*/
  }
///// function to get price of product
function get_price(ind){
	var productCode=document.getElementById("prod_code["+ind+"]").value;
	var price_pickstr=document.getElementById("pricepickstr").value;
	var pricestate=price_pickstr.split("~");
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
	  });
	  //getSpecification(productCode,ind);
  }  
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('theValue');
		var itm="prod_code["+numi.value+"]";
        var qTy="req_qty["+numi.value+"]";
		var preno=document.getElementById('theValue').value;
		var num = (document.getElementById("theValue").value -1)+ 2;
		if((document.getElementById(itm).value!="" && document.getElementById(qTy).value!="" && document.getElementById(qTy).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
		r = '<div class="form-group" id="addr'+num+'" style="padding-top: 8px; padding-bottom: 8px; border:ridge; background-color: aliceblue; margin-right: 0px"><div class="col-md-6"><label class="col-md-6 control-label">Product <span class="red_small">*</span></label><div class="col-md-6"><select class="form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);get_price('+num+');"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div></div><div class="col-md-6"><label class="col-md-5 control-label">Qty <span class="red_small">*</span></label><div class="col-md-6"><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" onblur=rowTotal('+num+');myFunction(this.value,'+num+',"req_qty"); class="digits form-control" onkeypress="return onlyNumbers(this.value);"/></div></div><div class="col-md-6"><label class="col-md-6 control-label">Price <span class="red_small">*</span></label><div class="col-md-6"><input  name="price['+num+']" id="price['+num+']" type="text" onkeypress="return onlyFloatNum(this.value)" class="required form-control" onblur="rowTotal('+num+');"></div></div><div class="col-md-6"><label class="col-md-5 control-label">Value</label><div class="col-md-6"><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly><input type="hidden" class="form-control" name="rowdiscount['+num+']" id="rowdiscount['+num+']" onkeypress="return onlyFloatNum(this.value);" autocomplete="off" onblur="rowTotal('+num+');"></div></div><div class="col-md-6"><label class="col-md-6 control-label">Total</label><div class="col-md-6"><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/></div></div><input name="warranty_days['+num+']" id="warranty_days['+num+']" type="hidden" class="form-control" readonly/><input name="deliv_date['+num+']" id="deliv_date['+num+']" type="hidden" class="form-control" readonly/></div>';
		$('#so_items').append(r);
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
	//document.getElementById(abl_qtyid).value="0.00";
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
    if(parseFloat(price)>=parseFloat(dicountval)){
     var total= parseFloat(qty)*parseFloat(price);
     var totalcost= parseFloat(qty)*(parseFloat(price)-parseFloat(dicountval));
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
  }/*else if(parseFloat(document.getElementById(availableQty).value)=='0.00'){
	  alert("Stock is not Available");  
	  document.getElementById(ent_qty).value="";
	  //document.getElementById(availableQty).value="";
	  document.getElementById(ent_rate).value="";
	  document.getElementById(hold_rate).value="";
	  document.getElementById(prodCodeField).value="";
	  document.getElementById(prodmrpField).value="";
	  document.getElementById(prodCodeField).focus();
	  
  }*/
  else{
	  alert("Please enter qty");
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
    var rowno=(document.getElementById("theValue").value);
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
		sum_discount+=parseFloat(discountvar)*parseFloat(totqty);
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
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Add New Sales Order </h2>
      <br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Party Name <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                  <option value="" selected="selected">Please Select </option>
                  <?php 
					$sql_chl="select * from access_location where uid='$_REQUEST[usercode]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                      if($party_det[id_type]!='HO'){
                          ?>
                  <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
                  <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                  </option>
                  <?php
						  }
					}
                    ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Distributor/Branch <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                  <option value="" selected="selected">Please Select </option>
                  <?php 
					$sql_parent="select uid from mapped_master where mapped_code='".$_REQUEST['po_from']."'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[uid]'"));
                          ?>
                  <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['uid']?>" <?php if($result_parent['uid']==$_REQUEST['po_to'])echo "selected";?> >
                  <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['uid']?>
                  </option>
                  <?php
					}
                    ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Sales Executive <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                <select name="sales_executive" id="sales_executive" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                  <option value="" <?php if($_REQUEST['sales_executive'] == "") { echo "selected"; } ?>>Please Select </option>
                  <?php 
					$sql_se="select name, username, utype from admin_users where username='".$_REQUEST["usercode"]."'";
					$res_se=mysqli_query($link1,$sql_se);
					while($result_se=mysqli_fetch_array($res_se)){
                    ?>
                  <option data-tokens="<?=$result_se['name']." | ".$result_se['username']?>" value="<?=$result_se['username']?>" <?php if($result_se['username']==$_REQUEST['usercode']) { echo "selected"; } ?> >
                  <?=$result_se['name']." | ".$result_se['username']?>
                  </option>
                  <?php
					}
                    ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-5 control-label">Available Credit Balance</label>
              <div class="col-md-3">
                <input type="text" name="cr_bal" id="cr_bal" class="form-control" value="<?=getCRBAL($_REQUEST['po_from'],$_REQUEST[po_to],$link1);?>" readonly/>
              </div>
              <label class="col-md-1 control-label">
              <!--                  Discount Type-->
              </label>
              <div class="col-md-3">
                <!--              <select name="discount_type" id="discount_type" required class="form-control required" onChange="document.frm1.submit();">
                  <option value="NONE"<?php if($_REQUEST['discount_type']=="NONE")echo "selected";?>>NONE</option>
                  <option value="PD"<?php if($_REQUEST['discount_type']=="PD")echo "selected";?>>Productwise Discount</option>
                  <option value="TD"<?php if($_REQUEST['discount_type']=="TD")echo "selected";?>>Total Discount</option>
                </select>-->
              </div>
            </div>
          </div>
        </form>
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <div class="form-group" id='addr0' style="padding-top: 8px; padding-bottom: 8px; border:ridge; background-color: aliceblue; margin-right: 0px">
            <div class="col-md-6"><label class="col-md-6 control-label">Product <span class="red_small">*</span></label>
                <div class="col-md-6">
                    <select name="prod_code[0]" id="prod_code[0]" class="form-control" required data-live-search="true" onChange="getAvlStk(0);get_price(0);checkDuplicate(0, this.value);">
                        <option value="">--None--</option>
                        <?php 
				$model_query="select productcode,productname,productcolor from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                        <option value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>
                        <?php }?>
                      </select>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Qty <span class="red_small">*</span></label>
                <div class="col-md-6">
                    <input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value,0,'req_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);">
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Price <span class="red_small">*</span></label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Value</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly>
                    <input type="hidden" class="form-control" name="rowdiscount[0]" id="rowdiscount[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" onBlur="rowTotal(0);">
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Total</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly>
                    <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                    <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
                </div>
            </div>
            <!--<div class="col-md-6"><label class="col-md-5 control-label">Delivery Date</label>
                <div class="col-md-6">-->
                	<input name="warranty_days[0]" id="warranty_days[0]" type="hidden" class="form-control" style="width:100px;" readonly/>
                    <input name="deliv_date[0]" id="deliv_date[0]" type="hidden" class="form-control" readonly/>
                <!--</div>
            </div>-->	
        </div>
        <span id="so_items">
        </span>
        <div class="row">
            <div class="col-md-12">
                <a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
            </div>
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
              <label class="col-md-3 control-label"><!--Total Discount--></label>
              <div class="col-md-3">
                
              </div>
              <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              	<input type="hidden" name="total_discount" id="total_discount" class="form-control" value="0.00" onKeyUp="check_total_discount();" />
                <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
              <div class="col-md-3">
                <textarea name="delivery_address" id="delivery_address" class="form-control required addressfield" style="resize:none" required><?=$toloctiondet[2]?>
</textarea>
              </div>
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
              </div>
            </div>
          </div>
          <!--<div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Payment Terms</label>
              <div class="col-md-3">
                <textarea name="payment_terms" id="payment_terms" class="form-control addressfield" style="resize:none"></textarea>
              </div>
              <label class="col-md-3 control-label"></label>
              <div class="col-md-3"> </div>
            </div>
          </div>
-->          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This PO">
              <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['po_to']?>"/>
              <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_from']?>"/>
              <input type="hidden" name="sales_executive1" id="sales_executive1" value="<?=$_REQUEST['sales_executive']?>"/>
              <!--                <input type="hidden" name="disc_type" id="disc_type" value="<?=$_REQUEST['discount_type']?>"/>-->
              <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~".$toloctiondet[1]?>"/>
              <input name="theValue" type="hidden" id="theValue" value="0" />
              
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>

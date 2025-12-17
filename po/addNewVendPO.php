<?php
require_once("../config/config.php");
@extract($_POST);
////// case 2. if we want to Add new user
if($_POST){
 if ($_POST['upd']=='Save'){
  //////////////////getting tax info ///////////////////////////////////////////////
  $taxinfo = explode('~' ,$tax_type0);
  $taxval = explode('~' ,$tax_total0);
  $taxinfo1 = explode('~' ,$tax_type1);
  $taxval1 = explode('~' ,$tax_total1);
  $taxinfo2 = explode('~' ,$tax_type2);
  $taxval2 = explode('~' ,$tax_total2);
   $taxinfo3 = explode('~' ,$tax_type3);
  $taxval3 = explode('~' ,$tax_total3);
  $taxinfo4 = explode('~' ,$tax_type4);
  $taxval4 = explode('~' ,$tax_total4);
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from vendor_order_master where po_from='".$partycode."' and req_type='VPO'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po[no]+1;
	$po_no=$partycode."VPO".$c_nos; 
	//mysqli_autocommit($link1, false);
	$flag = true;
	
	///// Insert Master Data
	$query1= "INSERT INTO vendor_order_master set po_to='".$parentcode."',po_from='".$partycode."',po_no='".$po_no."',temp_no='".$c_nos."',ref_no='".$ref_no."',requested_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',req_type='VPO',status='Pending',po_value='".$sub_total."',create_by='".$_SESSION['userid']."',ip='".$ip."',taxtype='".$taxinfo[0]."',taxper='".$taxinfo[1]."',taxamount='".$taxval[0]."',
 taxtype1='".$taxinfo1[0]."',tax_per1='".$taxinfo1[1]."',taxamount1='".$taxval1[0]."', taxtype2='".$taxinfo2[0]."',tax_per2='".$taxinfo2[1]."',taxamount2='".$taxval2[0]."', taxtype3='".$taxinfo3[0]."',tax_per3='".$taxinfo3[1]."',taxamount3='".$taxval3[0]."',  taxtype4='".$taxinfo4[0]."',tax_per4='".$taxinfo4[1]."',taxamount4='".$taxval4[0]."',currency_type='".$currency_type."',invoice_no='".$invoiceno."',invoice_date='".$invoicedate."',remark='".$remark."',grand_total='".$grand_total."',delivery_address='".$delivery_address."',payment_status='".$payment_terms."'";
 
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
        echo "Error details: " . mysqli_error($link1) . ".";
 }
	///// Insert in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   

	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			/////////// insert data
	  $query2="insert into vendor_order_data set po_no='".$po_no."', prod_code='".$val."', req_qty='".$req_qty[$k]."', pending_qty = '".$req_qty[$k]."', po_price='".$price[$k]."', po_value='".$linetotal[$k]."', mrp='".$mrp[$k]."', totalval='".$total_val[$k]."',currency_type='".$currency_type."',uom='PCS', deliv_schedule='".$deliv_date[$k]."'";
		$result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
           }
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"VPO","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Vendor Purchase Order is successfully placed with ref. no.".$po_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:vendorPurchaseList.php?msg=".$msg."".$pagenav);
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
 <script src="../js/jquery.js"></script>
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
// When the document is ready
/*$(document).ready(function () {
	$('#invoicedate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});*/
$(document).ready(function () {
	$('#deliv_date0').datepicker({
		format: "yyyy-mm-dd",
		startDate: "<?= $today ?>",
		todayHighlight: true,
		autoclose: true
	});
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
function makeCalDeliv(ind) {
$('#deliv_date' + ind).datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?= $today ?>",
			todayHighlight: true,
			autoclose: true,
		});
}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
///// get product specification devlop by shekhar on 26 march 2019
function getSpecification(prodid,indx){
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{prodSpecif:prodid,indxx:indx},
		success:function(data){
			var getdata=data.split("~");
	        document.getElementById("prd_desc"+getdata[1]+"").innerHTML = "<a href='#' title='Product Specification' data-toggle='popover' data-trigger='focus' data-content='"+getdata[0]+"'><i class='fa fa-film'></i></a>";
			rePop();
			//document.getElementById("warranty_days["+getdata[1]+"]").value = getdata[3];
			//getDeliveryDate(getdata[2],getdata[1]);
	    }
	  });
}
////////////////////////////calculate tax /////////////////////////////////////////////////////////////
 function getTax(val, indx, index) {
 
                                                    var ctax = $("#counttax").val();									
                                                    var sub_amt = $("#sub_total").val();
                                                    if (val != "") {												
                                                        var res = val.split("~");										
                                                        var gd = $("#grand_total").val();
                                                        var discount = parseFloat(sub_amt * res[1] / 100);
														document.getElementById("tax_total" + index).value = parseFloat(discount);												
                                                        var grand = parseFloat(sub_amt) + parseFloat(discount) + parseFloat(gd);
                                                        var grand1 = parseFloat(discount) + parseFloat(gd);										
                                                        document.getElementById("tax_per" + index).value = res[1];
                                                        var total = [];
                                                        for (i = 0; i < ctax; i++) {
                                                            total.push($("#tax_per" + i).val());
                                                        }
                                                        var total1 = 0;
                                                        $.each(total, function(index, val) {
                                                            var tax_per1 = (sub_amt * val / 100);
                                                            total1 = parseFloat(total1) + parseFloat(tax_per1);
                                                            $("#tax_total" + index).val(tax_per1.toFixed(2));
                                                        });
                                                        $("#tottax").val(total1.toFixed(2));
                                                        $("#grand_total").val(parseFloat(Math.round(grand1)).toFixed(2));
                                                    } else {										
                                                        document.getElementById("tax_per" + index).value = "0";
                                                        document.getElementById("tax_total" + index).value = "0";
                                                        var total = [];
                                                        for (i = 0; i < ctax; i++) {
                                                            total.push($("#tax_per" + i).val());
                                                        }
                                                        var total1 = 0;
                                                        $.each(total, function(index, val) {
                                                            var tax_per1 = (sub_amt * val / 100);
                                                            total1 = parseFloat(total1) + parseFloat(tax_per1);
                                                        });
                                                        var total_sum = parseFloat(parseFloat(total1) + parseFloat(sub_amt)).toFixed(2);
                                                        var round_off = parseFloat(parseFloat(Math.round(total_sum)) - parseFloat(total_sum)).toFixed(2);
                                                        $("#round_off").val(round_off);
                                                        $("#grand_total").val(parseFloat(Math.round(total_sum)).toFixed(2));
                                                        $("#tottax").val(total1.toFixed(2));
                                                    }
                                                }
////////////////////////////////////////////End //////////////////////////////////////////////
/////////// function to get available stock of ho
  function getAvlStk(indx){
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  var locationCode=$('#po_from').val();
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
	  getSpecification(productCode,indx);
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
     var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:300px"><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);" required><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname']. " | " . $br['productcode'] . " | " . $br['productcolor'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" onblur=rowTotal('+num+'); class="digits form-control"/></td><td><input  name="price['+num+']" id="price['+num+']" type="text" class="form-control number" onblur="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly></td><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" style="width:130px;" readonly value="100"><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/></div><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td><input type="text" name="deliv_date[' + num + ']" class="form-control" id="deliv_date'+ num +'" value="" readonly="readonly"/></td></tr>';
      $('#itemsTable1').append(r);
	  makeSelect();
	  makeCalDeliv(num);            
		}
  });
});
function makeSelect(){
  $('.selectpicker').selectpicker({
	liveSearch: true,
	showSubtext: true
  });
}

//////////////////////////////////////////////// adding new dropdown for tax ///////////////////////////////////
												taxind = 0;
                                                function addTax() {	
                                                    var rowCount = $('#taxrow tr').length;
                                                   var tax_type = $("#tax_type" + taxind).val();   
											        if (tax_type != "") {
                                                        taxind++;
                                                        if (rowCount < 5) {
														 $("#counttax").val(rowCount);
														 $("#taxrow").append("<tr><td width='239px' style='padding-left:0px;font-size:17px'>Tax Type " + rowCount + "</td><td width='110px'><select name=tax_type" + taxind + " id=tax_type" + taxind + " class='form-control' onChange='getTax(this.value, 1, " + taxind + ");' style='width:120px'><option value=''>Select Tax</option><?php  
$tax=mysqli_query($link1,"select tax_name,tax_per from newtax_master where status ='Active' ");while($row=mysqli_fetch_array($tax)){?><option  value='<?= $row[tax_name] . "~" .$row[tax_per] ?>'><?= $row[tax_name] ?></option><?php }?></select></td><td width='202px' style='padding-left:71px;font-size:17px'>Tax %<td width='113px'><input type='text'  name=tax_per" + taxind + " id= tax_per" + taxind + " class='form-control digits'  style='width:110px;' value='0.00'/></td><td width='295px' style='padding-left:96px;font-size:17px'>Tax Total</td><td width='209px' style='padding-right:134px'><input type='text' name=tax_total" + taxind + " id=tax_total" + taxind + " class='form-control'  style='width:110px;' value='0.00' readonly/></td></tr>");
  }
                                                        else {
                                                            $("#add_tax").hide();
                                                            $("#showtext").text('You have reached maximum number of taxes.');
                                                            //$("#showtext").attr('style','color:red');
                                                        }
                                                    }
                                                }

////////////////////////////////////////////////////////////////////////////
////// delete product row///////////
function deleteRow(ind){  
  //$("#addr"+(indx)).html(''); 
     var id="addr"+ind; 
     var itemid="prod_code"+"["+ind+"]";
	 var qtyid="req_qty"+"["+ind+"]";
	 var rateid="price"+"["+ind+"]";
	 var lineTotal="linetotal["+ind+"]";
	 var mrpid="mrp"+"["+ind+"]";
	 var holdRateid="holdRate"+"["+ind+"]";
	 var abl_qtyid="avl_stock"+"["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemid).value="";
	document.getElementById(lineTotal).value="0.00";
	document.getElementById(qtyid).value="0.00";
	document.getElementById(rateid).value="0.00";
	document.getElementById(mrpid).value="0.00";
	document.getElementById(holdRateid).value="0.00";
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
  var holdRate=document.getElementById(hold_rate).value;
  ////// check if entered qty is something
  if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
  /////  check if entered price is somthing
  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  
     var total= parseFloat(qty)*parseFloat(price);
     var var3="linetotal["+ind+"]";
     document.getElementById(var3).value=(total);
     calculatetotal();
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

		var totalamtvar=0.00;
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		sum_qty+=parseFloat(document.getElementById(temp_qty).value);
		sum_total+=parseFloat(totalamtvar);

	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=(sum_total);
	document.getElementById("grand_total").value=(sum_total);
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
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-ship"></i> Add New Vendor Purchase Order </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order From<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                      if($party_det[id_type]=='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[location_id]?>" <?php if($result_chl[location_id]==$_REQUEST[po_from])echo "selected";?> >
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
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order To<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select * from vendor_master where status='active' and id!=''";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
                          ?>
                    <option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?> >
                       <?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?>
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
              <label class="col-md-3 control-label">Available Credit Balance</label>
              <div class="col-md-3">
                <input type="text" name="cr_bal" id="cr_bal" class="form-control" value="<?=getCRBAL($_REQUEST['po_to'],$_REQUEST['po_from'],$link1);?>" readonly/>
              </div>
              <label class="col-md-3 control-label">Currency Type</label>
              <div class="col-md-3">
              <select name="currency_type" id="currency_type" required class="form-control required" onChange="document.frm1.submit();">
                  <option value="INR"<?php if($_REQUEST[currency_type]=="INR")echo "selected";?>>INR</option>
                  <option value="USD"<?php if($_REQUEST[currency_type]=="USD")echo "selected";?>>USD</option>
                </select>
              </div>
          </div>
        </div>
         </form>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <div class="form-group">
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>
                <th class="col-md-1" style="font-size:13px">Qty</th>
                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Price <?php if($_REQUEST[currency_type]=="USD"){ ?>(<i class="fa fa-usd" aria-hidden="true"></i>)<?php }else{?>(<i class="fa fa-inr" aria-hidden="true"></i>)<?php }?></th>
                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Available Stock<?php /*?><div id="pp"><select name="prodcode" id="prodcode" class="form-control selectpicker" required data-live-search="true">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select model from model_master where status='Active'";
			        $check1=mysql_query($model_query);
			        while($br = mysql_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['model'];?>" value="<?php echo $br['model'];?>"><?php echo $br['model'];?></option>
                    <?php }?>
                  </select></div><?php */?></th>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Delivery Schedule</th>
              </tr>
            </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-3">
                <div id="pdtid0" style="display:inline-block;float:left; width:300px">
                  <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk(0);checkDuplicate(0, this.value);">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select productcode,productname,productcolor from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname']. " | " . $br['productcode'] . " | " . $br['productcolor'];?></option>
                    <?php }?>
                  </select>
                  </div>
                  <div id="prd_desc0" style="display:inline-block;float:right"></div>
                  </td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onBlur="rowTotal(0);"></td>
                <td class="col-md-1"><input type="text" class="form-control number" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" required></td>
                <td class="col-md-2"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly></td>
                <td class="col-md-2"><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:130px;" value="0" readonly>
                                     <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                     <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
                </td>
                <td class="col-md-2"><input type="text" name="deliv_date[0]" class="form-control" id="deliv_date0" value="" readonly style="width:140px"/></td>
              </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="6" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-2 control-label">Total Qty</label>
              <div class="col-md-2">
              <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
              <label class="col-md-3 control-label">Sub Total</label>
              <div class="col-md-3">
                <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
		 <table width="100%" border="0" id="taxrow">
		 <tr>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-2 control-label">Tax Type </label>
              <div class="col-md-2">
                <select name="tax_type0" id="tax_type0" class="form-control" onChange="getTax(this.value, 1, 0);" >
                   <option value="">Select Tax</option>
                  <?php  
				  $tax=mysqli_query($link1,"select tax_name,tax_per from newtax_master where status ='Active' ");
				  while($row=mysqli_fetch_array($tax)){
				  ?>
				  <option  value="<?= $row[tax_name] . "~" .$row[tax_per] ?>"><?= $row[tax_name] ?></option>
				  <?php
				  }
				  ?>
                </select>
              </div>		
             <label class="col-md-2 control-label">Tax %</label>
              <div class="col-md-2">
                <input type="text" name="tax_per0" id="tax_per0" class="form-control number" value="0.00"/>
              </div>
			  <label class="col-md-2 control-label">Tax Total</label>
              <div class="col-md-2">
              <input type="text" name="tax_total0" id="tax_total0" class="form-control" value="0.00" readonly/>
              </div>			
            </div>
          </div>
		  </tr>
		  </table>  
              <tr class="0">
                <td colspan="7" style="font-size:13px;" ><a id="add_tax" style="text-decoration:none" onClick="addTax();"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Tax</a><span id="showtext" style="color:red"></span><input type="hidden" value="1" id="theTaxValue" name="theTaxValue" /></td>
              </tr>
        	  
          <div class="form-group">
            <div class="col-md-10">
			 <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
          <?php /*?><div class="form-group">
            <div class="col-md-10">
                <label class="col-md-3 control-label">Invoice No.</label>
              <div class="col-md-3">
                <input type="text" name="invoiceno" id="invoiceno" class="form-control"/>
              </div>
               <label class="col-md-3 control-label">Invoice Date</label>
               <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="invoicedate"  id="invoicedate" style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
			   </div>
            </div>
          </div><?php */?>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>
              <div class="col-md-3">
                <textarea name="delivery_address" id="delivery_address" class="form-control addressfield required" style="resize:none" required></textarea>
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
              <label class="col-md-3 control-label"></label>
              <div class="col-md-3">
                
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Save" title="Save This PO">
                <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST[po_to]?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST[po_from]?>"/>
                <input type="hidden" name="currency_type" id="currency_type" value="<?=$_REQUEST[currency_type]?>"/>
				<input type='hidden' name='count' id="Count" value="1">
                <input type='hidden' name='counttax' id="counttax" value="1">
                <input type='hidden' name='tottax' id="tottax">
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='vendorPurchaseList.php?<?=$pagenav?>'">
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
<?php if($_REQUEST[po_to]=='' || $_REQUEST[po_from]==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>
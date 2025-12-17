<?php
////// Function ID ///////
$fun_id = array("u"=>array(9)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

@extract($_POST);
////// case 2. if we want to Add new user
if($_POST){
 if ($_POST['upd']=='Save' && $stockin!=""){
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from opening_stock_master where location_code='".$partycode."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po[no]+1;
	$doc_no=$partycode."OPS".$c_nos;
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	///// Insert Master Data
	$query1= "INSERT INTO opening_stock_master set location_code='".$partycode."',sub_location='".$stockin."',doc_no='".$doc_no."',temp_no='".$c_nos."',ref_no='".$refno."',requested_date='".$opendate."',entry_date='".$today."',entry_time='".$currtime."',status='Received',stock_value='".$sub_total."',create_by='".$_SESSION['userid']."',ip='".$ip."',remark='".$remark."'";
	$result1 = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result1) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	///// Insert in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   
	    $totalqty = $ok_qty[$k] + $damage_qty[$k] + $missing_qty[$k];
	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $totalqty!='' && $totalqty!=0) {
			/////////// insert data
		   $query2="insert into opening_stock_data set doc_no='".$doc_no."', prod_code='".$val."', okqty='".$ok_qty[$k]."',damageqty='".$damage_qty[$k]."',missingqty='".$missing_qty[$k]."', price='".$price[$k]."', value='".$linetotal[$k]."', mrp='".$mrp[$k]."',uom='PCS'";
		   $result2 = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result2) {
	           $flag = false;
               $err_msg = "Error details2: " . mysqli_error($link1) . ".";
           }
		   ///// update stock in inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$val."' and asc_code='".$partycode."' and sub_location='".$stockin."'"))>0){
			 ///if product is exist in inventory then update its qty 
			 $result3=mysqli_query($link1,"update stock_status set qty=qty+'".$totalqty."',okqty=okqty+'".$ok_qty[$k]."',broken=broken+'".$damage_qty[$k]."',missing=missing+'".$missing_qty[$k]."',updatedate='".$datetime."' where partcode='".$val."' and asc_code='".$partycode."' and sub_location='".$stockin."'");
		  }
		  else{
			 //// if product is not exist then add in inventory
			 $result3=mysqli_query($link1,"insert into stock_status set asc_code='".$partycode."',sub_location='".$stockin."',partcode='".$val."',qty=qty+'".$totalqty."',okqty='".$ok_qty[$k]."',broken='".$damage_qty[$k]."',missing='".$missing_qty[$k]."',uom='PCS',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result3) {
	           $flag = false;
               $err_msg = "Error details3: " . mysqli_error($link1) . ".";
           }
		   ////// insert in stock ledger////
		   ### CASE 1 if user enter somthing in ok qty
		   if($ok_qty[$k]!="" && $ok_qty[$k]!=0 && $ok_qty[$k]!=0.00){
		      $flag=stockLedger($doc_no,$opendate,$val,$partycode,$stockin,$stockin,"IN","OK","Opening Stock",$ok_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 2 if user enter somthing in damage qty
		   if($damage_qty[$k]!="" && $damage_qty[$k]!=0 && $damage_qty[$k]!=0.00){
		      $flag=stockLedger($doc_no,$opendate,$val,$partycode,$stockin,$stockin,"IN","DAMAGE","Opening Stock",$damage_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($missing_qty[$k]!="" && $missing_qty[$k]!=0 && $missing_qty[$k]!=0.00){
		      $flag=stockLedger($doc_no,$opendate,$val,$partycode,$stockin,$stockin,"IN","MISSING","Opening Stock",$missing_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$doc_no,"OPS","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Opening Stock Challan is successfully entered with ref. no.".$doc_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:openingStockList.php?msg=".$msg."".$pagenav);
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
 <script src="../js/jquery-1.10.1.min.js"></script>
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
$(document).ready(function () {
	$('#opendate').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?//=$today?>",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
/////////// function to get available stock of ho
/*  function getAvlStk(indx){
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  var locationCode=$('#locationcode').val();
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
  }*/
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm="prod_code["+numi.value+"]";
        var okqty="ok_qty["+numi.value+"]";
		var damageqty="damage_qty["+numi.value+"]";
		var missingqty="missing_qty["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(itm).value!="" && ((document.getElementById(okqty).value!="" && document.getElementById(okqty).value!="0") || (document.getElementById(damageqty).value!="" && document.getElementById(damageqty).value!="0") || (document.getElementById(missingqty).value!="" && document.getElementById(missingqty).value!="0") )) || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
     var r='<tr id="addr'+num+'"><td><span id="pdtid'+num+'"><select class="form-control selectpicker" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" onchange="checkDuplicate(' + num + ',this.value);" required><option value="">--None--</option><?php $model_query="select productcode,productname,model_name,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['productcode'];?>"><?=$br['productname']." | ".$br['model_name']." | ".$br['productcode']?></option><?php }?></select></span></td><td><input type="text" class="form-control digits" name="ok_qty['+num+']" id="ok_qty['+num+']"  autocomplete="off" required onBlur=myFunction(this.value,'+num+',"ok_qty");rowTotal('+num+'); onkeypress="return onlyNumbers(this.value);" style="width:100px;" value="0" ></td><td><input type="text" class="form-control digits" name="damage_qty['+num+']" id="damage_qty['+num+']"  autocomplete="off" required onBlur=myFunction(this.value,'+num+',"damage_qty");rowTotal('+num+'); onkeypress="return onlyNumbers(this.value);" style="width:100px;" value="0" ></td><td><input type="text" class="form-control digits" name="missing_qty['+num+']" id="missing_qty['+num+']"  autocomplete="off" required onBlur=myFunction(this.value,'+num+',"missing_qty");rowTotal('+num+'); onkeypress="return onlyNumbers(this.value);" style="width:100px;" value="0" ></td><td><input  name="price['+num+']" id="price['+num+']" type="text" onkeypress="return onlyFloatNum(this.value)" class="form-control" onblur="rowTotal('+num+');"></td><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" style="width:150px;" readonly></div><div style="display:inline-block;float:right"><input type="hidden" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" style="width:130px;" readonly value="100"><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td></tr>';
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
////// delete product row///////////
function deleteRow(ind){  
  //$("#addr"+(indx)).html(''); 
     var id="addr"+ind; 
     var itemid="prod_code"+"["+ind+"]";
	 var okqtyid="ok_qty"+"["+ind+"]";
	 var damageqtyid="damage_qty"+"["+ind+"]";
	 var missqtyid="missing_qty"+"["+ind+"]";
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
	document.getElementById(okqtyid).value="0.00";
	document.getElementById(damageqtyid).value="0.00";
	document.getElementById(missqtyid).value="0.00";
	document.getElementById(rateid).value="0.00";
	document.getElementById(mrpid).value="0.00";
	document.getElementById(holdRateid).value="0.00";
	document.getElementById(abl_qtyid).value="0.00";
  rowTotal(ind);
}
/////// calculate line total /////////////
function rowTotal(ind){
  var entok_qty="ok_qty["+ind+"]";
  var entdamg_qty="damage_qty["+ind+"]";
  var entmiss_qty="missing_qty["+ind+"]";
  var ent_rate="price["+ind+"]";
  var hold_rate="holdRate["+ind+"]";
  var availableQty="avl_stock["+ind+"]";
  var prodCodeField="prod_code["+ind+"]";
  var prodmrpField="mrp["+ind+"]";
  var holdRate=document.getElementById(hold_rate).value;
  ////// check if entered ok qty is something
  if(document.getElementById(entok_qty).value){ var okqty=document.getElementById(entok_qty).value;}else{ var okqty=0;}
  ////// check if entered damage qty is something
  if(document.getElementById(entdamg_qty).value){ var damgqty=document.getElementById(entdamg_qty).value;}else{ var damgqty=0;}
  ////// check if entered missing qty is something
  if(document.getElementById(entmiss_qty).value){ var missqty=document.getElementById(entmiss_qty).value;}else{ var missqty=0;}
  /////  check if entered price is somthing
  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
  
     var total= (parseFloat(okqty) + parseFloat(damgqty) + parseFloat(missqty))*parseFloat(price);
     var var3="linetotal["+ind+"]";
     document.getElementById(var3).value=formatCurrency(total);
     calculatetotal();
}
////// calculate final value of form /////
function calculatetotal(){
    var rowno=(document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
	var sum_discount=0.00;
    for(var i=0;i<=rowno;i++){
		var tempok_qty="ok_qty["+i+"]";
		var tempdamg_qty="damage_qty["+i+"]";
		var tempmiss_qty="missing_qty["+i+"]";
		var temp_total="linetotal["+i+"]";

		var totalamtvar=0.00;
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		///// check if line okqty is something
        if(document.getElementById(tempok_qty).value){ var totalok= document.getElementById(tempok_qty).value;}else{ var totalok=0;}
		///// check if line damage qty is something
        if(document.getElementById(tempdamg_qty).value){ var totaldamg= document.getElementById(tempdamg_qty).value;}else{ var totaldamg=0;}
		///// check if line missing qty is something
        if(document.getElementById(tempmiss_qty).value){ var totalmiss= document.getElementById(tempmiss_qty).value;}else{ var totalmiss=0;}
		
		sum_qty+=(parseFloat(totalok) + parseFloat(totaldamg) + parseFloat(totalmiss));
		sum_total+=parseFloat(totalamtvar);

	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=formatCurrency(sum_total);
	///// check if line total value is something
    /*if(document.getElementById("tax_per").value){ var taxper= document.getElementById("tax_per").value;}else{ var taxper=0.00;}
	var taxamount=(parseFloat(taxper)*parseFloat(sum_total))/100;
	document.getElementById("tax_total").value=formatCurrency(parseFloat(taxamount));
	document.getElementById("grand_total").value=formatCurrency(parseFloat(sum_total)+parseFloat(taxamount));*/
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
      <h2 align="center"><i class="fa fa-cubes"></i> Add Opening Stock </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Location Name <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="locationcode" id="locationcode" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                       if($party_det[id_type]){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
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
                <div class="col-md-10"><label class="col-md-3 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
                    <div class="col-md-9">
                        <select name="stock_in" id="stock_in" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                            <option value="" selected="selected">Please Select </option>
                             <?php                                 
                            $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['locationcode']."'";
                            $smfm_res = mysqli_query($link1,$smfm_sql);
                            while($smfm_row = mysqli_fetch_array($smfm_res)){
                            ?>
                            <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_in'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                            <?php
                            }
                            ?>
                            <?php                                 
                            $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['locationcode']."' AND status='Active'";
                            $smf_res = mysqli_query($link1,$smf_sql);
                            while($smf_row = mysqli_fetch_array($smf_res)){
                            ?>
                            <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_in'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                            <?php
                            }
                            ?>
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
                <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>
                <th class="col-md-1" style="font-size:13px">Ok Qty</th>
                <th class="col-md-1" style="font-size:13px">Damage Qty</th>
                <th class="col-md-1" style="font-size:13px">Missing Qty</th>
                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Price</th>
                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>
              </tr>
            </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-3"><span id="pdtid0">
                  <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="checkDuplicate(0, this.value);">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select productcode,productname,model_name,productcolor from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option value="<?php echo $br['productcode'];?>"><?=$br['productname']." | ".$br['model_name']." | ".$br['productcode']?></option>
                    <?php }?>
                  </select></span></td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="ok_qty[0]" id="ok_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value,0,'ok_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);" style="width:100px;" value="0"></td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="damage_qty[0]" id="damage_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value,0,'damage_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);" style="width:100px;" value="0" ></td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="missing_qty[0]" id="missing_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value,0,'missing_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);" style="width:100px;" value="0" ></td>
                <td class="col-md-1"><input type="text" class="form-control" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required></td>
                <td class="col-md-2"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly style="width:150px;"><input type="hidden" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:130px;" value="0" readonly>
                                     <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                     <input name="holdRate[0]" id="holdRate[0]" type="hidden"/></td>
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
              <label class="col-md-3 control-label">Total Qty</label>
              <div class="col-md-3">
              <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
              <label class="col-md-3 control-label">Total</label>
              <div class="col-md-3">
                <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Ref. No.</label>
              <div class="col-md-3">
                <input type="text" name="refno" id="refno" class="form-control"/>
              </div>
               <label class="col-md-3 control-label">Opening Date</label>
               <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="required form-control span2" name="opendate"  id="opendate" style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
			   </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
              </div>
              <label class="col-md-3 control-label"></label>
              <div class="col-md-3">
                &nbsp;
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Save" title="Save This Challan">
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['locationcode']?>"/>
                <input type="hidden" name="stockin" id="stockin" value="<?=$_REQUEST['stock_in']?>"/>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='openingStockList.php?<?=$pagenav?>'">
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
<?php if($_REQUEST['locationcode']=='' || $_REQUEST['stock_in']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>
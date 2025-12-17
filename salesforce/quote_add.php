<?php
require_once("../config/config.php");

$res = mysqli_fetch_array(mysqli_query($link1,"select * from sf_lead_master where lid = '".$_REQUEST['id']."' "));

////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	/////// Create Quote No. /////////
	$ref=mysqli_query($link1,"select max(quote_id) as cnt from sf_quote_master order by quote_id desc");
	$row = mysqli_fetch_assoc($ref);
	$result=$row['cnt']+1;
	$pad=str_pad($result,3,"0",STR_PAD_LEFT);  
	$auto_quote_no="QT".$pad;
	
	foreach($prod_code as $k=>$val){
			///// insert part wise ///////////
			$qry1 = "INSERT INTO sf_quote_itemsdetail SET quote_no = '".$auto_quote_no."', product_id = '".$prod_code[$k]."', qty = '".$req_qty[$k]."', rate = '".$price[$k]."', amt = '".$tot_val[$k]."',  tax_type = '".$tax_type[$k]."', tax_value = '".$tax_pr[$k]."', tax_amt = '".$tax_am[$k]."', total = '".$total_dt[$k]."', total_amt = '".$sub_total."', total_taxamt = '".$total_tax."', grandtotal = '".$grand_total."', create_dt = '".$today."', create_time = '".$currtime."',  ip = '".$ip."', create_by = '".$_SESSION['userid']."' ";
			
			//echo $qry1."<br><br>";
			
			$result1 = mysqli_query($link1,$qry1)or die ("ER1".mysqli_error($link1));
			//// check if query is not executed
			if (!$result1) {
				 $flag = false;
				 echo "Error details 1: " . mysqli_error($link1) . ".";
			}		
	}
	
	if($party_id != "" && $party_add != "" && $locationstate != "" && $locationcity != "" && $circle != ""){
		/////// insert in master tbl /////
		$qry2 = "INSERT INTO sf_quote_master SET quote_no = '".$auto_quote_no."', party_id = '".$party_id."', sales_executive = '".$designation."', address = '".$party_add."', state = '".$locationstate."',  city = '".$locationcity."', country = '".$circle."',party_contact='".$contact_no."',party_email='".$email."', ref_no = '".$reference_no."', create_dt = '".$today."', remark = '".$remark."', qty = '".$total_qty."', total_amt = '".$sub_total."', total_taxamt = '".$total_tax."',  grandtotal = '".$grand_total."', create_time = '".$currtime."', create_by =  '".$_SESSION['userid']."',  ip = '".$ip."', status = '7' , lead_ref_no = '".$lead_no."'  ";
		
		//echo $qry2." <br><br> ";
		
		$result2 = mysqli_query($link1,$qry2)or die ("ER2".mysqli_error($link1));
		//// check if query is not executed
		if (!$result2) {
			 $flag = false;
			 echo "Error details 2: " . mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = "Please fulfill all required fields.";
		///// move to parent page
		header("location:quote_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
	///////// insert into status history tbl ////////
	$qry3 = "insert into sf_status_history set party_id='".$party_id."', status_id='7', trans_type='add_quote', trans_no='".$auto_quote_no."',update_by='".$_SESSION['userid']."'";	
	
	//echo $qry3."<br><br>";
	
	$result3 = mysqli_query($link1,$qry3)or die ("ER3".mysqli_error($link1));
	//// check if query is not executed
	if (!$result3) {
		 $flag = false;
		 echo "Error details 3: " . mysqli_error($link1) . ".";
	}
	//////////// Daily activity  //////////
	dailyActivity($_SESSION['userid'],$auto_quote_no,"QUOTE","ADD",$ip,$link1,"");
	
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		if($email){
		include "quote_add_email.php";
		}
        $msg = "Quote is successfully added with ref. no. ".$auto_quote_no." ".$err_msg;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:quote_list.php?msg=".$msg."&sts=success".$pagenav);
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
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script language="javascript" type="text/javascript">
/////////// function to get state on the basis of circle
  $(document).ready(function(){
	$('#circle').change(function(){
	  var name=$('#circle').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{circle:name},
		success:function(data){
	    $('#statediv').html(data);
	    }
	  });
    });
  });
 /////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
 }
 
///// function for checking duplicate Product value
function checkDuplicate(fldIndx1, enteredsno) {  
 	document.getElementById("save").disabled = false;
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

////////////////// calulate total ///////////////////////////
function getTotVal(indx){
	var qt = document.getElementById("req_qty["+indx+"]").value;
	var prc = document.getElementById("price["+indx+"]").value;
	if((qt != "") && (prc != "")){
		document.getElementById("tot_val["+indx+"]").value = parseFloat(qt * prc);	
	}else{
		document.getElementById("tot_val["+indx+"]").value = 0.00;
	}
	line_tot_val(indx);
	getTax(indx);
}
/////////////////////////////////////////////////////////////

////////////////////////////calculate tax /////////////////////////////////////////////////////////////
function getTax(indx) {	
	var val = document.getElementById("tax_type["+indx+"]").value;	
	if (val != "") {
		document.getElementById("tax_pr["+indx+"]").value = val;	
		var tot = document.getElementById("tot_val["+indx+"]").value;
		var tx_amt = (val * tot)/100;
		document.getElementById("tax_am["+indx+"]").value = tx_amt;
	} else {										
		document.getElementById("tax_pr["+indx+"]").value = 0.00;
		var tot = document.getElementById("tot_val["+indx+"]").value;	
		var tx_amt = (val * tot)/100;	
		document.getElementById("tax_am["+indx+"]").value = tx_amt;
	}
	line_tot_val(indx);
	calculatetotal();
}
//////////////////////////////////////////// End //////////////////////////////////////////////

/////////////////// Total line value //////////////////////////////////
function line_tot_val(indx){
	var tval = document.getElementById("tot_val["+indx+"]").value;
	var tmt = document.getElementById("tax_am["+indx+"]").value;
	if(tval != "" && tmt !=""){
		document.getElementById("total_dt["+indx+"]").value = (parseFloat(tval) + parseFloat(tmt));
	}else{
		document.getElementById("total_dt["+indx+"]").value = 0.00;
	}
}
///////////////////////////////////////////////////////////////////////
////// delete product row///////////
function deleteRow(ind){  
     var id="addr"+ind; 
     var itemId="prod_code"+"["+ind+"]";
	 var reqQtyId="req_qty"+"["+ind+"]";
	 var pricId="price"+"["+ind+"]";
	 var totValId="tot_val"+"["+ind+"]";
	 var taxTypeId="tax_type["+ind+"]";
	 var taxPrId="tax_pr"+"["+ind+"]";
	 var taxAmId="tax_am"+"["+ind+"]";
	 var totalDtld="total_dt["+ind+"]";
	
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemId).value="";
	document.getElementById(reqQtyId).value="0";
	document.getElementById(pricId).value="0.00";
	document.getElementById(totValId).value="0.00";
	document.getElementById(taxTypeId).value="0.00";
	document.getElementById(taxPrId).value="0.00";
	document.getElementById(taxAmId).value="0.00";
	document.getElementById(totalDtld).value="0.00";
  	getTotVal(ind);
}
/////////////////////////////////////////////

////// calculate final value of form /////
function calculatetotal(){
    var rowno=(document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
	var sum_tax=0.00;
    for(var i=0;i<=rowno;i++){
		var temp_qty="req_qty["+i+"]";
		var temp_total="tot_val["+i+"]";
		var temp_tax="tax_am["+i+"]";
		var taxvar=0.00;
		var totalamtvar=0.00;
		///// check if discount value is something
		if(document.getElementById(temp_tax).value){ taxvar= document.getElementById(temp_tax).value;}else{ taxvar=0.00;}
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}
		
		sum_qty+=parseFloat(totqty);
		sum_total+=parseFloat(totalamtvar);
		sum_tax+=parseFloat(taxvar);
	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=sum_total;
    document.getElementById("total_tax").value=sum_tax;
	document.getElementById("grand_total").value=(sum_total + sum_tax);
}

////////////////////// add more option //////////////////////////////
 
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm="prod_code["+numi.value+"]";
        var qTy="req_qty["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(itm).value!="" && document.getElementById(qTy).value!="" && document.getElementById(qTy).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
     var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:300px"><select class="form-control selectpicker required" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" onChange="checkDuplicate('+num+', this.value);" required ><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" class="digits form-control required"  onBlur="getTotVal('+num+');" onKeyPress="return onlyNumbers(this.value);" required /></td><td><input  name="price['+num+']" id="price['+num+']" type="text" class="required form-control" onBlur="getTotVal('+num+');" onKeyPress="return onlyFloatNum(this.value);" required ></td><td><input type="text" class="form-control" name="tot_val['+num+']" id="tot_val['+num+']" autocomplete="off" readonly></td><td><select name="tax_type['+num+']" id="tax_type['+num+']" class="form-control required" required onChange="getTax('+num+');" ><option value="">--Select Tax--</option><?php $tax=mysqli_query($link1,"select tax_name,tax_per from newtax_master where status ='Active' "); while($row=mysqli_fetch_array($tax)){ ?><option  value="<?=$row[tax_per]?>"><?= $row[tax_name] ?></option> <?php } ?> </select></td><td><input type="text" class="form-control" name="tax_pr['+num+']" id="tax_pr['+num+']" autocomplete="off" readonly></td><td><input name="tax_am['+num+']" id="tax_am['+num+']" type="text" class="form-control" readonly/></td><td><input name="total_dt['+num+']" id="total_dt['+num+']" type="text" class="form-control" readonly/><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');" ></i></div></td></tr>';
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
 
 </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-plus-square-o"></i> Add New Quote </h2><br/><br/>
      <?php if($_REQUEST['msg']!=''){?>
			<h4><span <?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";}?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
            </h4>
        <?php }?>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Party Name(Customer) <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" autocomplete="off" id="basic_autocomplete_field" name="party_id" class="form-control entername required" required value="<?=$res['partyid']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Sales Executive</label>
              <div class="col-md-6">
               <select name="designation" class="form-control" id="designation" >
               		<option value="">--Select Sales Executive--</option>
					<?php 
                    if($childNode){
                        $userList = $childNode.",'".$_SESSION["userid"]."'"; 
                    }else{
                        $userList = "'".$_SESSION["userid"]."'"; 
                    }
                    $sales=mysqli_query($link1,"select username,name from admin_users where username in (".$userList.") and status='active' order by name asc");
                    while($srow=mysqli_fetch_assoc($sales))
                    {
                    ?>
                    <option value="<?php echo $srow['username'];?>"<?php if($res['sales_executive']==$srow['username']){ echo "selected";}?>><?php echo $srow['name'];?></option>
					<?php }?>
				</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="EAST"<?php if($res['party_country']=="EAST"){ echo "selected";}?>>EAST</option>
                  <option value="NORTH"<?php if($res['party_country']=="NORTH"){ echo "selected";}?>>NORTH</option>
                  <option value="SOUTH"<?php if($res['party_country']=="SOUTH"){ echo "selected";}?>>SOUTH</option>
                  <option value="WEST"<?php if($res['party_country']=="WEST"){ echo "selected";}?>>WEST</option>
                </select>           
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                <select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required>
                	<option value=''>--Please Select--</option>
                    <?php
                    $state_query = "select distinct(state) from state_master where zone='" . $res['party_country'] . "' order by state";
                    $state_res = mysqli_query($link1, $state_query);
                    while ($row_res = mysqli_fetch_array($state_res)) {
					?>
                    <option value='<?=$row_res['state']?>'<?php if($res['party_state']==$row_res['state']){ echo "selected";}?>><?php echo $row_res['state'];?></option>
                    <?php }?>
    			</select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
               <select  name='locationcity' id='locationcity' class='form-control required' required>
                    <option value=''>--Please Select--</option>
                    <?php 
                    $city_query = "SELECT distinct city FROM district_master where state='" . $res['party_state'] . "' order by city";
                    $city_res = mysqli_query($link1, $city_query);
                    while ($row_city = mysqli_fetch_array($city_res)) {
                    ?>
                	<option value='<?=$row_city['city']?>'<?php if($res['party_city']==$row_city['city']){ echo "selected";}?>><?php echo $row_city['city'];?></option>
                	<?php }?>
                	<option value='Others'<?php if($res['party_city']=="Others"){ echo "selected";}?>>Others</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Reference No</label>
              <div class="col-md-6">
              <input type="text" autocomplete="off" id="reference_no" name="reference_no" class="form-control" value="<?=$res['reference']?>"/>
              </div>
            </div>
          </div>
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Party Contact <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input name="contact_no" id="contact_no" class="form-control digits required" minlength="10" maxlength="12" required type="text" value="<?php echo $res['party_contact'];?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Party Email</label>
              <div class="col-md-6">
              <input name="email" id="email" class="form-control email" type="email" value="<?php echo $res['party_email'];?>"/>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Party Address <span class="red_small">*</span></label>
              <div class="col-md-6">
               <textarea name="party_add" id="party_add" class="form-control addressfield required" required><?php echo $res['party_address'];?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
              <textarea name="remark" id="remark" class="form-control addressfield"></textarea>
              </div>
            </div>
          </div>
      
          <div class="form-group">
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>
                <th class="col-md-1" style="font-size:13px">Qty</th>
                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Price</th>
                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Tax Type</th>
                <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Tax %</th>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Tax Amt</th>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-3">
                    <div id="pdtid0" style="display:inline-block;float:left; width:300px">
                  <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker required" required data-live-search="true"  onChange="checkDuplicate(0, this.value);"   >
                    <option value="">--None--</option>
                    <?php 
				$model_query="select productcode,productname,productcolor from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option value="<?php echo $br['productcode'];?>"<?php if($res['productcode']==$br['productcode']){ echo "selected";}?>><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>
                    <?php }?>
                  </select>
                    </div>
                    <div id="prd_desc0" style="display:inline-block;float:right"></div>
                </td>
                <td class="col-md-1"><input type="text" class="form-control digits required" name="req_qty[0]" id="req_qty[0]" onBlur="getTotVal(0);"  autocomplete="off" required onKeyPress="return onlyNumbers(this.value);"></td>
                <td class="col-md-1"><input type="text" class="form-control required" name="price[0]" id="price[0]" onBlur="getTotVal(0);" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" required></td>
                <td class="col-md-2"><input type="text" class="form-control" name="tot_val[0]" id="tot_val[0]" autocomplete="off" readonly></td>
                <td class="col-md-1">
                	<select name="tax_type[0]" id="tax_type[0]" class="form-control required" required onChange="getTax(0);" >
                       <option value="">--Select Tax--</option>
                      <?php  
                      $tax=mysqli_query($link1,"select tax_name,tax_per from newtax_master where status ='Active' ");
                      while($row=mysqli_fetch_array($tax)){
                      ?>
                      <option  value="<?=$row[tax_per]?>"><?= $row[tax_name] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                </td>
                <td class="col-md-2"><input type="text" class="form-control" name="tax_pr[0]" id="tax_pr[0]" autocomplete="off" value="" readonly>
				</td>
                <td class="col-md-2"><input name="tax_am[0]" id="tax_am[0]" type="text" class="form-control" style="width:100px;" value="" readonly/></td>
                <td class="col-md-2"><input name="total_dt[0]" id="total_dt[0]" type="text" class="form-control" style="width:100px;" value="" readonly/></td>
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
              <label class="col-md-3 control-label">Total Tax</label>
              <div class="col-md-3">
                <input type="text" name="total_tax" id="total_tax" class="form-control" value="0.00" readonly/>
              </div>
              <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
                    
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='quote_list.php?<?=$pagenav?>'">
              <input type="hidden"  name="lead_no" id="lead_no" value="<?=$res['reference']?>" />
            </div>
          </div>
         	
    	</form>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
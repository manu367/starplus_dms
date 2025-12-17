<?php
////// Function ID ///////
$fun_id = array("u"=>array(92)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
////// we hit save button
if ($_POST['upd']=='Save'){
	/// transcation parameter /////////////////////	
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	$toloctiondet1 = explode("~",getLocationDetails($parentcode,"state,id_type,disp_addrs,city,addrs,name,gstin_no,pincode,email,phone",$link1));
	$frmloctiondet1 = explode("~",getLocationDetails($partycode,"state,id_type,disp_addrs,city,addrs,name,gstin_no,pincode,email,phone",$link1));
	//// Make System generated Invoice no.//////		
    $res_cnt = mysqli_query($link1, "select inv_str,inv_counter , stn_counter , stn_str from document_counter where location_code='" .$partycode . "'");
    if (mysqli_num_rows($res_cnt)) {
    	$row_cnt = mysqli_fetch_array($res_cnt);
        ///// Insert Master Data       
		if($_POST['doctype'] == 'DC'){
			$doctype =  "Delivery Challan";
			$invcnt = $row_cnt['stn_counter'] + 1;
            $pad = str_pad($invcnt, 3, 0, STR_PAD_LEFT);
            $invno = $row_cnt['stn_str'] . $pad;
			$result = mysqli_query($link1, "update document_counter set stn_counter=stn_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $partycode. "'");
            //// check if query is not executed
			if (!$result) {
				$flag = false;
				$err_msg = "Error Code2:";
			}
		}else {
			$doctype =  "INVOICE";
			$invcnt = $row_cnt['inv_counter'] + 1;
			$pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
			$invno = $row_cnt['inv_str'] . $pad;			
			$result = mysqli_query($link1, "update document_counter set inv_counter=inv_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $partycode. "'");
			//// check if query is not executed
			if (!$result) {
				$flag = false;
				$err_msg = "Error Code2:";
			}
		}
		///// Insert in item data by picking each data row one by one
		foreach($prod_code as $k=>$val)
		{   	    
			// checking row value of product and qty should not be blank
			if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0){
				/////////// insert data
				$prodcat=getProductDetails($prod_code[$k],"productcategory",$link1);
		     	$query2 = "insert into billing_model_data set from_location='".$partycode. "',challan_no='" . $invno . "', prod_code='" .$prod_code[$k]. "',prod_cat='" . $prodcat . "', qty='" .$req_qty[$k] . "', mrp='" .$price[$k]. "', price='" .$price[$k]. "', value='" . $linetotal[$k] . "',discount='" .$rowdiscount[$k] . "',sgst_per='" . $sgst_per[$k]. "',sgst_amt='" .$sgst_amt[$k] . "',cgst_per='" .$cgst_per[$k]. "',cgst_amt='" .$cgst_amt[$k]. "',igst_per='" .$igst_per[$k]. "',igst_amt='" .$igst_amt[$k] . "', totalvalue='" . $total_val[$k] . "',sale_date='" . $today . "',entry_date='" . $today . "', disc_amt = '".$rowdiscount[$k]."' ";	
               	$result2 = mysqli_query($link1, $query2);
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$err_msg = "Error Code4:";
				}
		   		////// hold the PO qty in stock ////
		   		$totalcgst_amt+=$cgst_amt[$k];
		   		$totalsgst_amt+=$sgst_amt[$k];
		   		$totaligst_amt+=$igst_amt[$k];
	       		
				$flag=holdStockQty($partycode,$prod_code[$k],$req_qty[$k],$link1,$flag);
			}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
    $tax_amount = $totalcgst_amt+$totalsgst_amt+$totaligst_amt;
  	$to_addr=explode("~",getLocationDetails($parentcode,"addrs",$link1));
    $from_addr =explode("~",getLocationDetails($partycode,"addrs",$link1));
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
    $query1 = "INSERT INTO billing_master set 
	from_location='".$partycode . "',
	to_location='" . $parentcode . "',
	sub_location='".$acstockfrom."',from_gst_no='".$frmloctiondet1[6]."', from_partyname='".$frmloctiondet1[5]."', party_name='".$toloctiondet1[5]."', to_gst_no='".$toloctiondet1[6]."',
	challan_no='" . $invno . "',
	sale_date='" . $today . "',
	entry_date='" . $today . "',
	entry_time='" . $currtime . "',
	type='STN',
	document_type='".$doctype."',
	status='PFA',
	entry_by='" . $_SESSION['userid'] . "',
	basic_cost='" . $sub_total . "',
	discount_amt='" . $total_discount . "',
	tax_cost='" .$tax_amount . "',
	total_sgst_amt='" . $totalsgst_amt . "',
	total_cgst_amt='" . $totalcgst_amt . "',
	total_igst_amt='" . $totaligst_amt . "',
	total_cost='" . $grand_total . "',
	round_off='".$roundoff."',
	bill_from='".$partycode. "',
	bill_topty='".$parentcode. "',
	from_addrs='" .$from_addr[0] . "',
	disp_addrs='" .$from_addr[0] . "',
	to_addrs='" .$to_addr[0]. "',
	deliv_addrs='" . $delivery_address . "',
	billing_rmk='" . $remark . "',
	from_state='".$frmloctiondet1[0]."', to_state='".$toloctiondet1[0]."', from_city='".$frmloctiondet1[3]."', to_city='".$toloctiondet1[3]."', from_pincode='".$frmloctiondet1[7]."', to_pincode='".$toloctiondet1[7]."', from_phone='".$frmloctiondet1[9]."', to_phone='".$toloctiondet1[9]."', from_email='".$frmloctiondet1[8]."', to_email='".$toloctiondet1[8]."'";	
	$result = mysqli_query($link1, $query1);
	//// check if query is not executed
	if (!$result) {
		$flag = false;
		$err_msg = "Error Code1:";
	} 
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$invno,"STN","PFA",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "STN is successfully placed with ref. no.".$invno;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:stock_transferlist.php?msg=".$msg."".$pagenav);
    exit;
 }

}
$toloctiondet=explode("~",getLocationDetails($_REQUEST['stock_to'],"state,id_type,disp_addrs,city,addrs,name,gstin_no,pincode,email,phone",$link1));
$frmloctiondet=explode("~",getLocationDetails($_REQUEST['stock_from'],"state,id_type,disp_addrs,city,addrs,name,gstin_no,pincode,email,phone",$link1));
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

<script type="text/javascript" src="../js/jquery.validate.js"></script>

<script type="text/javascript" src="../js/common_js.js"></script>

<script type="text/javascript" src="../js/ajax.js"></script>

<script type="text/javascript">

///// get product specification devlop by shekhar on 26 march 2019


/////////// function to get available stock of ho

  function getAvlStk(indx){

	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  var doctype = document.getElementById("doc_type").value;

	  var locationCode = $('#stock_to').val();
	  var locationCodefrom = $('#stock_from').val();
	  var aclocationCodefrom = $('#acstock_from').val();
	  
	

	  var stocktype="okqty";

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{locstknew:productCode,loccode:locationCode,fromloc:locationCodefrom,godown:aclocationCodefrom,stktype:stocktype,indxx:indx},

		success:function(data){
			var getdata=data.split("~");

	        document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
			
			if(doctype == 'INV'){
		
			document.getElementById("sgst_per[" + indx + "]").value = getdata[2];
            document.getElementById("cgst_per[" + indx + "]").value = getdata[3];
            document.getElementById("igst_per[" + indx + "]").value = getdata[4];
			}
			else {
		
			document.getElementById("sgst_per[" + indx + "]").value = 0.00;
			document.getElementById("sgst_amt[" + indx + "]").value = 0.00;
            document.getElementById("cgst_per[" + indx + "]").value = 0.00;
			document.getElementById("cgst_amt[" + indx + "]").value = 0.00;
            document.getElementById("igst_per[" + indx + "]").value = 0.00;
			document.getElementById("igst_amt[" + indx + "]").value = 0.00;
			
			 }
            
			
	    }

	  });

  }

///// function to get price of product

function get_price(ind){;

	var productCode=document.getElementById("prod_code["+ind+"]").value;


	var price_pickstr=document.getElementById("pricepickstr").value;
	

	var pricestate=price_pickstr.split("~");

	 	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{product:productCode,locstate:pricestate[0],lctype:pricestate[1]},

		success:function(data){

			var splitprice=data.split("~");

	        document.getElementById("price["+ind+"]").value=(splitprice[0]);

			document.getElementById("holdRate["+ind+"]").value=(splitprice[0]);

		    document.getElementById("mrp["+ind+"]").value=(splitprice[1]);

	    }

	  });

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

     var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:200px"><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);get_price('+num+');"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" onblur=rowTotal('+num+');myFunction(this.value,'+num+',"req_qty"); class="digits form-control" onkeypress="return onlyNumbers(this.value);"/></td><td><input  name="price['+num+']" id="price['+num+']" type="text" onkeypress="return onlyFloatNum(this.value)"  class="required form-control" onblur="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly></td><td><input type="text" class="form-control" name="rowdiscount['+num+']" id="rowdiscount['+num+']" onkeypress="return onlyFloatNum(this.value);" autocomplete="off" onblur="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="rowdiscount_val['+num+']" id="rowdiscount_val['+num+']" onkeypress="return onlyFloatNum(this.value);" autocomplete="off" onblur="rowTotal('+num+');" readonly></td><td><input type="text" name="sgst_per[' + num + ']" id="sgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="sgst_amt[' + num + ']" id="sgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_per[' + num + ']" id="cgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_amt[' + num + ']" id="cgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_per[' + num + ']" id="igst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_amt[' + num + ']" id="igst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 5px;"/></td><td><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td><input type="text" class="form-control" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"  autocomplete="off"  style="width:80px;text-align: right" readonly ></td></tr>';

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

	 var totalid="total_val"+"["+ind+"]";

	 var lineTotal="linetotal["+ind+"]";

	 var mrpid="mrp"+"["+ind+"]";

	 var holdRateid="holdRate"+"["+ind+"]";

	 var discountField="rowdiscount["+ind+"]";
	 
	 var rowdiscount ="rowdiscount_val["+ind+"]";
	 
	  var sgstper ="sgst_per["+ind+"]";
	   var sgstamt ="sgst_amt["+ind+"]";
	   var cgstper ="cgst_per["+ind+"]";
	   var cgstamt ="cgst_amt["+ind+"]";
	    var igstper ="igst_per["+ind+"]";
	   var igstamt ="igst_amt["+ind+"]";

	 var abl_qtyid="avl_stock"+"["+ind+"]";
	 var totalval="total_val"+"["+ind+"]";
	 

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
    
	document.getElementById(rowdiscount).value="0.00";
	
	document.getElementById(sgstper).value="";
	document.getElementById(sgstamt).value="0.00";
	document.getElementById(cgstper).value="";
	document.getElementById(cgstamt).value="0.00";
	document.getElementById(igstper).value="";
	document.getElementById(cgstamt).value="0.00";
	document.getElementById(totalval).value="0.00";
	
	

	document.getElementById(abl_qtyid).value="";
	
	  document.getElementById("upd").disabled = false;
	   document.getElementById("error").innerHTML = "";

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
  var discountval ="rowdiscount_val["+ind+"]";
  
  

  var totalvalField="total_val["+ind+"]";
  
  var sgst_per = "sgst_per[" + ind + "]";
  var sgst_amt = "sgst_amt[" + ind + "]";
  var cgst_per = "cgst_per[" + ind + "]";
  var cgst_amt = "cgst_amt[" + ind + "]";
  var igst_per = "igst_per[" + ind + "]";
  var igst_amt = "igst_amt[" + ind + "]";

  var holdRate=document.getElementById(hold_rate).value;

  ////// check if entered qty is something

  if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}

  /////  check if entered price is somthing

  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}

  ///// check if discount value is something

  if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00; }
  
  
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

  ////// check entered qty should be available

  if(parseFloat(qty) > "0"  ){
  
  
   if((document.getElementById(availableQty).value)==''){

	  document.getElementById("upd").disabled = true;
	  document.getElementById("error").innerHTML = "Stock is not Available";	  
     }
	 else {
	   document.getElementById("upd").disabled = false;
	   document.getElementById("error").innerHTML = "";
	 
	  }
  

    if(parseFloat(price)>=parseFloat(dicountval)){

     var total= parseFloat(qty)*parseFloat(price);

     var totalcost= (parseFloat(qty)*(parseFloat(price))-parseFloat(dicountval));

     var var3="linetotal["+ind+"]";
	 
	 document.getElementById(discountval).value = totalcost;

     document.getElementById(var3).value=formatCurrency(total);
	 
	 var doctype =  document.getElementById("doc_type").value;
	 
	 if(doctype == 'INV'){
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
	 
	 else {

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

  else{


	  document.getElementById(ent_qty).value="";

	  //document.getElementById(availableQty).value="";

	  document.getElementById(ent_rate).value="";

	  document.getElementById(hold_rate).value="";

	  document.getElementById(prodCodeField).value="";

	  document.getElementById(prodmrpField).value="";

	  document.getElementById(prodCodeField).focus();
	  
	  calculatetotal();

  }

}

////// calculate final value of form /////

function calculatetotal(){

    var rowno=(document.getElementById("rowno").value);

	var sum_qty=0;

	var sum_total=0.00; 

	var sum_discount=0.00;
	var sumval = 0.00;
	var sumtotval = 0.00;

    for(var i=0;i<=rowno;i++){

		var temp_qty="req_qty["+i+"]";

		var temp_total="linetotal["+i+"]";

		var temp_discount="rowdiscount["+i+"]";
		
		var subtotal="rowdiscount_val["+i+"]";
		
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
		

		sum_qty+=parseFloat(totqty);

		sum_total+=parseFloat(totalamtvar);

		sum_discount+=parseFloat(discountvar)*parseFloat(totqty);
		sumval+=parseFloat(totsum);
		sumtotval+=parseFloat(totsub)
		

	}/// close for loop

    document.getElementById("total_qty").value=sum_qty;

    document.getElementById("sub_total").value=formatCurrency(sumtotval);

    <?php //if($_REQUEST[discount_type]=="PD"){ ?>	

    document.getElementById("total_discount").value=formatCurrency(sum_discount);

    <?php //} ?>

	document.getElementById("grand_total").value=formatCurrency(parseFloat(sumval));

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

      <h2 align="center"><i class="fa fa-shopping-basket"></i> Add Stock Transfer </h2><br/>

      <div class="form-group" id="page-wrap" style="margin-left:10px;">

          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

          <div class="form-group">

            <div class="col-md-10"><label class="col-md-5 control-label">Stock Transfer From <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">

                    <option value="" selected="selected">Please Select </option>

                    <?php 

					$sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('HO','BR')";

					$res_chl=mysqli_query($link1,$sql_chl);

					while($result_chl=mysqli_fetch_array($res_chl)){
					
					$party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));

                          ?>

                   <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['stock_from'])echo "selected";?> >

                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    <?php

						

					}

                    ?>
					
					
					

                 </select>

              </div>

            </div>

          </div>
		  <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
                <div class="col-md-7">
                    <select name="acstock_from" id="acstock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                        <option value="" selected="selected">Please Select </option>
                         <?php                                 
                        $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['stock_from']."'";
                        $smfm_res = mysqli_query($link1,$smfm_sql);
                        while($smfm_row = mysqli_fetch_array($smfm_res)){
                        ?>
                        <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['acstock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                        <?php
                        }
                        ?>
                        <?php                                 
                        $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['stock_from']."' AND status='Active'";
                        $smf_res = mysqli_query($link1,$smf_sql);
                        while($smf_row = mysqli_fetch_array($smf_res)){
                        ?>
                        <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['acstock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                        <?php
                        }
                        ?>
                    </select>

                </div>
            </div>
        </div>
          <div class="form-group">

            <div class="col-md-10"><label class="col-md-5 control-label">Stock Transfer To <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="stock_to" id="stock_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">

                 <option value="" selected="selected">Please Select </option>

                    <?php 

					 $sql_parent="select mapped_code,uid from mapped_master where uid='" . $_REQUEST['stock_from'] . "' and status='Y'";

					$res_parent=mysqli_query($link1,$sql_parent);

					while($result_parent=mysqli_fetch_array($res_parent)){

	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='" . $result_parent['mapped_code'] . "'"));

                          if ($party_det[id_type] != 'HO') {  
                          ?>

                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['mapped_code']?>" <?php if($result_parent['mapped_code']==$_REQUEST['stock_to'])echo "selected";?> >

                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['mapped_code']?>

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
            <div class="col-md-10"><label class="col-md-5 control-label">Document Type</label>
              <div class="col-md-7">
                 <select name="doc_type" id="doc_type" class="form-control" onChange="document.frm1.submit();">
                 <?php if($frmloctiondet[0]==$toloctiondet[0]){?>
				 <option value="DC" <?php if($_REQUEST['doc_type'] == "DC") { echo "selected"; }?>>Delivery Challan</option>
                 <?php }else{?>
                  <option value="INV" <?php if($_REQUEST['doc_type'] == "INV") { echo "selected";}?>>Invoice</option>
                  <?php }?>
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

                <th data-class="expand" class="col-md-2" style="font-size:13px;">Product</th>

                <th class="col-md-1" style="font-size:13px">Qty</th>

                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Price</th>

                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>

                <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Discount</th>
				
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">After Discount Value</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">SGST<br>(%)</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">SGST <br>Amt</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">CGST<br>(%)</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">CGST <br>Amt</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">IGST<br>(%)</th>
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">IGST <br>Amt</th>
				<th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Total</th>	
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Avl Stock</th>									

               

              </tr>

            </thead>

            <tbody>

              <tr id='addr0'>

                <td class="col-md-2">

                    <div id="pdtid0" style="display:inline-block;float:left; width:200px">

                  <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk(0);get_price(0);checkDuplicate(0, this.value);">

                    <option value="">--None--</option>

                    <?php 

				$model_query="select productcode,productname,productcolor from product_master where status='active'";

			        $check1=mysqli_query($link1,$model_query);

			        while($br = mysqli_fetch_array($check1)){?>

                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>

                    <?php }?>

                  </select>

                    </div>

                    <div id="prd_desc0" style="display:inline-block;float:right"></div>

                </td>

                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  style="width:80px;" autocomplete="off" required onBlur="myFunction(this.value,0,'req_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);"></td>

                <td class="col-md-2"><input type="text" class="form-control" name="price[0]" id="price[0]" style="width:80px;" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" onBlur="rowTotal(0);" required></td>

                <td class="col-md-2"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off"  readonly style="width:100px;"></td>

                <td class="col-md-1"><input type="text" class="form-control" name="rowdiscount[0]" id="rowdiscount[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" onBlur="rowTotal(0);" style="width:80px;"></td>
				
			  <td class="col-md-1"><input type="text" class="form-control" name="rowdiscount_val[0]" id="rowdiscount_val[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:100px;"></td>	
			  
			   <td class="col-md-1"><input type="text" class="form-control" name="sgst_per[0]" id="sgst_per[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;" ></td>
			   
			   <td class="col-md-1"><input type="text" class="form-control" name="sgst_amt[0]" id="sgst_amt[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;"></td>	
			   
			   <td class="col-md-1"><input type="text" class="form-control" name="cgst_per[0]" id="cgst_per[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;"></td>
			   
			   <td class="col-md-1"><input type="text" class="form-control" name="cgst_amt[0]" id="cgst_amt[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;"></td>
			   
			    <td class="col-md-1"><input type="text" class="form-control" name="igst_per[0]" id="igst_per[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;"></td>
			   
			   <td class="col-md-1"><input type="text" class="form-control" name="igst_amt[0]" id="igst_amt[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" readonly style="width:80px;"></td>	
			   
			   
				
                <td class="col-md-2"><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off"  style="width:120px;"readonly>

				  <input name="mrp[0]" id="mrp[0]" type="hidden"/>

                                     <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>

				</td>
<td class="col-md-1"><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:80px;text-align: right;padding: 4px;" value="0" readonly></td>	


         

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

            <div class="col-md-12" align="center">
			<input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['stock_from']?>"/>
            <input type="hidden" name="acstockfrom" id="acstockfrom" value="<?= $_REQUEST['acstock_from'] ?>"/>
              <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['stock_to']?>"/>
                <input type="hidden" name="doctype" id="doctype" value="<?=$_REQUEST['doc_type']?>">
                <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~".$toloctiondet[1]?>"/>
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save">
			  <span id="error" name = "error" class="red_small"></span>

                

              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='stock_transferlist.php?<?=$pagenav?>'">

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

<?php if($_REQUEST['stock_to']=='' || $_REQUEST['stock_from']==''){ ?>

<script>

$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");

</script>

<?php } ?>
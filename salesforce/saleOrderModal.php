<?php
require_once("../config/config.php");
?>
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

	        document.getElementById("deliv_date["+getdata[1]+"]").value = getdata[0];

	    }

	  });

}

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

	        //document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];

	    }

	  });

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

     var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:300px"><select class="selectpicker form-control" data-live-search="true" name="prod_code['+num+']" id="prod_code['+num+']" required onchange="getAvlStk('+num+');checkDuplicate(' + num + ',this.value);get_price('+num+');"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active'";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" name="req_qty['+num+']" id="req_qty['+num+']" onblur=rowTotal('+num+');myFunction(this.value,'+num+',"req_qty"); class="digits form-control" onkeypress="return onlyNumbers(this.value);"/></td><td><input  name="price['+num+']" id="price['+num+']" type="text" onkeypress="return onlyFloatNum(this.value)" class="required form-control" onblur="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" readonly></td><td><input type="text" class="form-control" name="rowdiscount['+num+']" id="rowdiscount['+num+']" onkeypress="return onlyFloatNum(this.value);" autocomplete="off" onblur="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td><input name="warranty_days['+num+']" id="warranty_days['+num+']" type="text" class="form-control" readonly/></td><td><input name="deliv_date['+num+']" id="deliv_date['+num+']" type="text" class="form-control" readonly/></td></tr>';

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

    <div class="col-sm-9">

      <div class="form-group" id="page-wrap" style="margin-left:10px;">

          <form id="frm3" name="frm3" class="form-horizontal" action="" method="post">

          <div class="form-group">

            <div class="col-md-10"><label class="col-md-5 control-label">Purhase Order From <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm3.submit();">

                    <option value="" selected="selected">Please Select </option>

                    <?php 

					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";

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

            <div class="col-md-10"><label class="col-md-5 control-label">Purhase Order To <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm3.submit();">

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
            <div class="col-md-10"><label class="col-md-5 control-label">Sales Executive <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="sales_executive" id="sales_executive" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm3.submit();">
                 <option value="" <?php if($_REQUEST['sales_executive'] == "") { echo "selected"; } ?> >Please Select </option>
                    <?php 
					$sql_se="select name, username, utype from admin_users where utype = '6' ";
					$res_se=mysqli_query($link1,$sql_se);
					while($result_se=mysqli_fetch_array($res_se)){
                    ?>
                    <option data-tokens="<?=$result_se['name']." | ".$result_se['username']?>" value="<?=$result_se['username']?>" <?php if($result_se['username']==$_REQUEST['sales_executive']) { echo "selected"; } ?> >
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

<!--              <select name="discount_type" id="discount_type" required class="form-control required" onChange="document.frm3.submit();">

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

                <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>

                <th class="col-md-1" style="font-size:13px">Qty</th>

                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Price</th>

                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Value</th>

                <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Discount</th>

                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Total</th>

                <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Warranty Days</th>

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

				$model_query="select productcode,productname,productcolor from product_master where status='active'";

			        $check1=mysqli_query($link1,$model_query);

			        while($br = mysqli_fetch_array($check1)){?>

                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>

                    <?php }?>

                  </select>

                    </div>

                    <div id="prd_desc0" style="display:inline-block;float:right"></div>

                </td>

                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value,0,'req_qty');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);"></td>

                <td class="col-md-1"><input type="text" class="form-control" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required></td>

                <td class="col-md-2"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly></td>

                <td class="col-md-1"><input type="text" class="form-control" name="rowdiscount[0]" id="rowdiscount[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" onBlur="rowTotal(0);"></td>

                <td class="col-md-2"><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly>

				  <input name="mrp[0]" id="mrp[0]" type="hidden"/>

                                     <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>

				</td>

                <td class="col-md-2"><input name="warranty_days[0]" id="warranty_days[0]" type="text" class="form-control" style="width:100px;" readonly/></td>

                <td class="col-md-2"><input name="deliv_date[0]" id="deliv_date[0]" type="text" class="form-control" style="width:100px;" readonly/></td>

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

              <label class="col-md-3 control-label"></label>

              <div class="col-md-3">

                

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


            </div>

          </div>

         </form>

      </div>

    </div>

  </div>

</div>

<?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']==''){ ?>

<script>

$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");

</script>

<?php } ?>
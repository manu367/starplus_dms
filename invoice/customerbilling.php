<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$toloctiondet=explode("~",getCustomerDetails($_REQUEST['po_to'],"state,category,address",$link1));
if($toloctiondet[0]=="" && $_REQUEST['po_from']!=''){ $toloctiondet=explode("~",getLocationDetails($_REQUEST['po_from'],"state,city",$link1));}
@extract($_POST);
////// if we hit process button
if($_POST){
 if ($_POST['upd']=='Process'){
   if(($partycode!="" && $parentcode!="")){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	///// get parent location details
	$parentloc=getLocationDetails($parentcode,"addrs,disp_addrs,state",$link1);
	$parentlocdet=explode("~",$parentloc);	
   if($total_qty!='' && $total_qty!=0){
    //// Make System generated Invoice no.//////
	$res_cnt=mysqli_query($link1,"select inv_str,inv_counter from document_counter where location_code='".$parentcode."'");
	if(mysqli_num_rows($res_cnt)){
	$row_cnt=mysqli_fetch_array($res_cnt);
	$invcnt=$row_cnt['inv_counter']+1;
	$pad=str_pad($invcnt,4,0,STR_PAD_LEFT);
	$invno=$row_cnt['inv_str'].$pad;	
	$splitcompltetax=explode("~",$complete_tax);
	///// get customer details details
	  $cust_info = explode("|", $partycode);
	   $childloc=getCustomerDetails($cust_info[0],"customerid,address",$link1);
	   $childlocdet=explode("~",$childloc);
	if($delivery_address){$deli_addrs=$delivery_address;}else{$deli_addrs=$childlocdet[1];}
	///// Insert Master Data
	$query1= "INSERT INTO billing_master set from_location='".$parentcode."', to_location='".$cust_info[0]."', challan_no='".$invno."', sale_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', type='RETAIL', document_type='INVOICE',basic_cost='".$sub_total."',discount_amt='".$total_discount."',tax_cost='".$tax_amount."',total_cost='".$grand_total."',bill_from='".$parentcode."',bill_topty='".$cust_info[0]."',from_addrs='".$parentlocdet[0]."',disp_addrs='".$parentlocdet[1]."',to_addrs='".$childlocdet[1]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."',po_no='FRONT_SCAN', status='Dispatched', dc_date='".$today."',dc_time='".$currtime."',file_name='SCANNED',imei_attach='Y'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:";
    }
	/// update invoice counter /////
	$result=mysqli_query($link1,"update document_counter set inv_counter=inv_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' where location_code='".$parentcode."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code2:";
    }
	///// Insert in item data by picking each data row one by one
	$arr_prodcode=array();
	$arr_qty=array();
	$arr_price=array();
	$arr_mrp=array();
	$arr_holdprice=array();
	$arr_linetotal=array();
	$arr_tax=array();
	$arr_taxamt=array();
	$arr_discount=array();
	$arr_totalval=array();
	
	foreach($prod_code as $k=>$val)
	{   
	
	
	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $bill_qty[$k]!='' && $bill_qty[$k]!=0) {			
			/// check imei is already bill or not
		   $res_imei=mysqli_query($link1,"select owner_code,imei1,imei2 from billing_imei_data where imei1='".$imei[$k]."' or imei2='".$imei[$k]."' order by id desc");
		   $checkimei=mysqli_fetch_assoc($res_imei);
		   if(mysqli_num_rows($res_imei) >0 || $checkimei['owner_code']==$parentcode){						
			  //////////////insert in billing imei data////////////////////////
		 $result=mysqli_query($link1,"insert into billing_imei_data  set from_location='".$parentcode."',to_location='".$cust_info[0]."',owner_code='".$cust_info[0]."',prod_code='".$prod_code[$k]."' ,doc_no='".$invno."',imei1='".$checkimei['imei1']."',imei2='".$checkimei['imei2']."'");
			//// check if query is not executed
		   if (!$result) {
			   $flag = false;
			   $err_msg = "Error Code3.1:". mysqli_error($link1) . ".";
		   }
		    $arr_prodcode[]=$prod_code[$k];
			$arr_qty[]=$bill_qty[$k];
			$arr_price[]=$price[$k];
			$arr_mrp[]=$mrp[$k];
			$arr_holdprice[]=$holdRate[$k];
			$arr_linetotal[]=$linetotal[$k];
			$arr_tax[]=$taxType[$k];
			$arr_taxamt[]=$rowtaxamount[$k];			
			$arr_discount[]=$rowdiscount[$k];
			$arr_sgstper[]=$rowsgstper[$k];
			$arr_sgstamount[]=$rowsgstamount[$k];
			$arr_cgstper[]=$rowcgstper[$k];
			$arr_cgstamount[]=$rowcgstamount[$k];
			$arr_igstper[]=$rowigstper[$k];
			$arr_igstamount[]=$rowigstamount[$k];
			$arr_totalval[]=$total_val[$k];
		   }else{
			   $flag = false;
			   $err_msg = "Error Code3.2: IMEI is not available";
		   }
		}// close if loop of checking row value of product and qty should not be blank
	/// close for loop
	///// apply logic to insert data in data table//
	$uniq_prod=array_unique($arr_prodcode);
	foreach($uniq_prod as $key => $value){
		//// find all key of every product in main array
		$keyarr=array_keys($arr_prodcode, $value);
		$sum_qty=0;
		$sum_price=0.00;
		$sum_mrp=0.00;
		$sum_holdprice=0.00;
		$sum_linetotal=0.00;
		$sum_taxamt=0.00;
		$sum_discount=0.00;
		$sum_taxper=0.00;
		$sum_taxname="";
		$sum_taxhead="";
		$sum_totalval=0.00;
		$denominator=0;
		///// make product wise all deatils in consolidate form
		for($i=0;$i<count($keyarr);$i++){
			$sum_qty+=$arr_qty[$keyarr[$i]];
			$sum_price+=$arr_price[$keyarr[$i]];
			$sum_mrp+=$arr_mrp[$keyarr[$i]];
			$sum_holdprice+=$arr_holdprice[$keyarr[$i]];
			$sum_linetotal+=$arr_linetotal[$keyarr[$i]];
			$sum_taxamt+=$arr_taxamt[$keyarr[$i]];
			$sum_discount+=$arr_discount[$keyarr[$i]];
			$sum_totalval+=$arr_totalval[$keyarr[$i]];
			 $sgstper =$arr_sgstper[$keyarr[$i]];
			 $sgstamt=$arr_sgstamount[$keyarr[$i]];
			$igstper=$arr_igstper[$keyarr[$i]];
			$igstamt=$arr_igstamount[$keyarr[$i]];
			 $cgstper=$arr_cgstper[$keyarr[$i]];
			$cgstamt=$arr_cgstamount[$keyarr[$i]];		
		
		
			 
			//// tax
			$taxpick=explode("~",$arr_tax[$keyarr[$i]]);
			$sum_taxper+=$taxpick[0];
			//if($sum_taxper){$sum_taxper.=",".$taxpick[0];}else{$sum_taxper.=$taxpick[0];}
			if($sum_taxname){$sum_taxname.=",".$taxpick[1];}else{$sum_taxname.=$taxpick[1];}
			if($sum_taxhead){$sum_taxhead.=",".$taxpick[2];}else{$sum_taxhead.=$taxpick[2];}
			$denominator++;
		}
		}
		$unique_taxname = implode(',',array_unique(explode(',', $sum_taxname)));
		$unique_taxhead = implode(',',array_unique(explode(',', $sum_taxhead)));
	// checking row value of product and qty should not be blank
		$getstk=getCurrentStock($parentcode,$value,"okqty",$link1);
		//// check stock should be available ////
		if($getstk < $sum_qty){ 
		   $flag = false;
           $err_msg = "Error Code3: Stock is not available";
		}
	    else{}
	/////////// insert data into data table ////////////////////////////////////
	   $query2="insert into billing_model_data set from_location='".$parentcode."', prod_code='".$value."', qty='".$sum_qty."', okqty='".$sum_qty."',mrp='".$sum_mrp/$denominator."', price='".$sum_price/$denominator."', hold_price='".$sum_holdprice/$denominator."', value='".$sum_linetotal."',tax_name='".$unique_taxname."',tax_per='".$sum_taxper/$denominator."', tax_amt='".$sum_taxamt."',discount='".$sum_discount/$denominator."', totalvalue='".$sum_totalval."',challan_no='".$invno."' ,sale_date='".$today."',entry_date='".$today."' ,sgst_per='".$sgstper."' ,sgst_amt='".$sgstamt."',igst_per='".$igstper."' ,igst_amt='".$igstamt."',cgst_per='".$cgstper."' ,cgst_amt='".$cgstamt."' ";
		 $result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code4:";
           }
		   //// update stock of from loaction
		  $result=mysqli_query($link1, "update stock_status set okqty=okqty-'".$sum_qty."',updatedate='".$datetime."' where asc_code='".$parentcode."' and partcode='".$value."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code5:";
           }
		   ///// update stock ledger table
		   $flag=stockLedger($invno,$today,$value,$parentcode,$cust_info[0],$parentcode,"OUT","OK","Retail Invoice",$sum_qty,$sum_price/$denominator,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   
		  
			////// entry in Customer Reward Ledger /////////////////////////////
		
						 if(($reward_info_chck[$k] == 'Y') && ($reward_point[$k] >0)){
						  ///// entry in customer reward ledger //////////////////////
						   mysqli_query($link1 , " insert into customer_reward_ledger set customer_id = '".$cust_info[0]."' , ref_no = '".$invno."' ,rewards = '".$reward_point[$k]."' , cr_dr_type = 'CR' , remark = 'EARNED' , entry_date = '".$today."' , entry_by = '".$_SESSION['userid']."' , ip = '".$ip."' , product_code = '".$value."'  ");
						 
						  ///////////////////////////////////////////////////////////////////////////
						   }
			
		   
		   
	}

	////// maintain party ledger////
	$flag=partyLedger($parentcode,$cust_info[0],$invno,$today,$today,$currtime,$_SESSION['userid'],"RETAIL INVOICE",$grand_total,"DR",$link1,$flag);
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"RETAIL INVOICE","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Invoice is successfully created with ref. no. ".$invno;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
	}
	else{
		$msg = "Request could not be processed invoice series not found. Please try again.";
	}
   }else{
	 $msg = "Request could not be processed . Please dispatch some qty.";
   }
   }else{
	   $msg = "Request could not be processed . Please enter customer details(Name or Contact no.).";
   }
	///// move to parent page
	  header("location:retailbillinglist.php?msg=".$msg."".$pagenav);
 	  exit;
 }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.min.js"></script>
 <script language="JavaScript" src="../js/ajax.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#myTable').dataTable();
            });
            $(document).ready(function() {
                //$("#frm2").validate();
                $("#frm1").validate();
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
		<script language="JavaScript" src="../js/ajax.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <script>
		/////////////////////////////// getting city dropdown ///////////////////////////////////////////////////////////////////
function getCity(val){
    if(val!="")
	{
	var strSubmit ="action=getCity&value="+val;
	var strURL = "../includes/getField.php";	
	var strResultFunc="displayCity";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
	}	
}
function displayCity(result){
    if(result!="" && result!=0){
	//alert(result);
		document.getElementById('citydiv').innerHTML=result;
		
    }
}
		
            ///// function to get price of product
            function get_price(ind) {
                var productIMEI = document.getElementById("imei[" + ind + "]").value;
                var locationCode = $('#po_from').val();
                var customer_state = $("#state").val();
				 var  exist_custstate  =  $("#po_to").val();	
				 var existcuststate =  exist_custstate.split(" |");	
                var price_pickstr = document.getElementById("pricepickstr").value;
                var pricestate = price_pickstr.split("~");
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {prodimei: productIMEI, loccode: locationCode, locstate: pricestate[0], lctype: pricestate[1]},
                    success: function(data) {
					
                        var splitprice = data.split("~");
                        document.getElementById("price[" + ind + "]").value = formatCurrency(splitprice[0]);
                        document.getElementById("holdRate[" + ind + "]").value = formatCurrency(splitprice[0]);
                        document.getElementById("mrp[" + ind + "]").value = formatCurrency(splitprice[1]);
                        document.getElementById("descrip[" + ind + "]").value = splitprice[4];
                        document.getElementById("avl_stock[" + ind + "]").value = splitprice[2];
                        document.getElementById("prod_code[" + ind + "]").value = splitprice[3];	
						
						document.getElementById("reward_info_chck[" + ind + "]").value = splitprice[9];
						document.getElementById("reward_point[" + ind + "]").value = splitprice[10];
						
						if((splitprice[9] == 'Y') && (splitprice[10] >0)){
						   document.getElementById("prd_desc"+ind+"").innerHTML = "<a href='#' title='Reward Point' style='color:#FF0000;' data-toggle='popover' data-trigger='focus' data-content='"+splitprice[11]+"'><i class='fa fa-th'></i></a>";

			              rePop();
						
						 }
						 else {
						
						  }
										
                        if ((splitprice[8] == customer_state) ){ ///// for new customer //////////////////////////////////////////////////////////
                            document.getElementById("rowsgstper[" + ind + "]").value = splitprice[5];
                            document.getElementById("rowcgstper[" + ind + "]").value = splitprice[6];
                            $("#rowigstper[" + ind + "]").value = '0';
                        }else if (splitprice[8] == $.trim(existcuststate[1])){ ///////////// for existing customer ///////////////////////////////////
						    document.getElementById("rowsgstper[" + ind + "]").value = splitprice[5];
                            document.getElementById("rowcgstper[" + ind + "]").value = splitprice[6];
                            $("#rowigstper[" + ind + "]").value = '0';
						}
						else {
						
                            $("#rowsgstper[" + ind + "]").value = '0';
                            $("#rowcgstper[" + ind + "]").value = '0';
                            document.getElementById("rowigstper[" + ind + "]").value = splitprice[7];
                        }
						
                    }
                });
            }
        </script>
        <script>

            $(document).ready(function() {
                $("#add_row").click(function() {
                    var numi = document.getElementById('rowno');
                    var itm = "imei[" + numi.value + "]";
                    var qTy = "bill_qty[" + numi.value + "]";
                    var preno = document.getElementById('rowno').value;
                    var num = (document.getElementById("rowno").value - 1) + 2;
                    if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
                        numi.value = num;
                        var r = '<tr id="addr' + num + '"><td><input type="text" class="form-control required" name="imei[' + num + ']" id="imei[' + num + ']"  autocomplete="off" required onBlur="get_price(' + num + ');checkDuplicateSerial(' + num + ',this.value);"></td><td><input type="text" class="form-control" name="descrip[' + num + ']" id="descrip[' + num + ']" readonly><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div><input type="hidden" name="reward_info_chck[' + num + ']" id="reward_info_chck[' + num + ']"><input type="hidden" name="reward_point[' + num + ']" id="reward_point[' + num + ']"><input type="hidden" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']" value="1"><input type="hidden" name="prod_code[' + num + ']" id="prod_code[' + num + ']"></td><td><input type="text" class="form-control" name="price[' + num + ']" id="price[' + num + ']" onblur="rowTotal(' + num + ');" autocomplete="off" onkeypress="return IsNumeric(event);" required style="width:50px;text-align:right;padding: 4px"><input type="hidden" class="form-control" name="linetotal[' + num + ']" id="linetotal[' + num + ']"></td><td><input type="text" class="form-control" name="rowdiscount[' + num + ']" id="rowdiscount[' + num + ']" onkeypress="return IsNumeric(event);" autocomplete="off" onblur="rowTotal(' + num + ');"  style="width:50px;text-align:right;padding: 4px" /></td><td><input type="text" class="form-control" name="rowsubtotal[' + num + ']" id="rowsubtotal[' + num + ']" autocomplete="off" value="" style="width:50px;text-align:right" readonly></td><td><input type="text" class="form-control" name="rowsgstper[' + num + ']" id="rowsgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowsgstamount[' + num + ']" id="rowsgstamount[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowcgstper[' + num + ']" id="rowcgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowcgstamount[' + num + ']" id="rowcgstamount[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowigstper[' + num + ']" id="rowigstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowigstamount[' + num + ']" id="rowigstamount[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly style="width:80px;text-align:right"><input name="mrp[' + num + ']" id="mrp[' + num + ']" type="hidden"/><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"></div><div style="display:inline-block;float:right;padding: 4px"><i class="fa fa-close fa-lg" onClick="fun_remove(' + num + ');"></i></div></td></tr>';
                        $('#itemsTable1').append(r);
						
                    }
                });
            });
        </script>
        <script>
		
		  

		
		
		
            function fun_remove(con)
            {
                var c = document.getElementById('addr' + con);
                c.parentNode.removeChild(c);
                con--;
                document.getElementById('rowno').value = con;
                rowTotal(con);

            }
        </script>

        <script type="text/javascript">
            /////// calculate line total /////////////
            function rowTotal(ind) {
                var ent_qty = "bill_qty" + "[" + ind + "]";
                var ent_rate = "price" + "[" + ind + "]";
                var hold_rate = "holdRate" + "[" + ind + "]";
                var availableQty = "avl_stock" + "[" + ind + "]";
                var prodmrpField = "mrp" + "[" + ind + "]";
                var discountField = "rowdiscount" + "[" + ind + "]";
                var rowtax = "taxType" + "[" + ind + "]";
                var totalvalField = "total_val" + "[" + ind + "]";
                var st = "rowsubtotal" + "[" + ind + "]";
                var rowsgstper = "rowsgstper" + "[" + ind + "]";
                var rowcgstper = "rowcgstper" + "[" + ind + "]";
                var rowigstper = "rowigstper" + "[" + ind + "]";
                var rowsgstamount = "rowsgstamount" + "[" + ind + "]";
                var rowcgstamount = "rowcgstamount" + "[" + ind + "]";
                var rowigstamount = "rowigstamount" + "[" + ind + "]";
                // check if entered qty is something

                if (document.getElementById(ent_qty).value) {
                    var qty = document.getElementById(ent_qty).value;
                } else {
                    var qty = 0;
                }
                //  check if entered price is somthing
                if (document.getElementById(ent_rate).value) {
                    var price = document.getElementById(ent_rate).value;
                } else {
                    var price = 0.00;
                }

                // check if discount value is something
                if (document.getElementById(discountField).value) {
                    var dicountval = document.getElementById(discountField).value;
                } else {
                    var dicountval = 0.00;
                }

                // check if row wise tax is something
//                if (document.getElementById(rowtax).value) {
//                    var expldtax = (document.getElementById(rowtax).value).split("~");
//                    var rowtaxval = expldtax[0];
//                } else {
//                    var rowtaxval = 0.00;
//                }
                //  check if sgst per
                if (document.getElementById(rowsgstper).value) {
                    var sgstper = document.getElementById(rowsgstper).value;
                } else {
                    var sgstper = 0.00;
                }
                // check if cgst per
                if (document.getElementById(rowcgstper).value) {
                    var cgstper = document.getElementById(rowcgstper).value;
                } else {
                    var cgstper = 0.00;
                }
                // check if igst per
                if (document.getElementById(rowigstper).value) {
                    var igstper = (document.getElementById(rowigstper).value);
                } else {
                    var igstper = 0.00;
                }

                // check entered qty should be available

                if (document.getElementById(availableQty).value == "Y") {
                    if (parseFloat(price) >= parseFloat(dicountval)) {
                        var total = parseFloat(qty) * parseFloat(price);
                        var totalcost = parseFloat(price) - parseFloat(dicountval);
                        var sgst_amt = ((totalcost * sgstper) / 100);
                        var cgst_amt = ((totalcost * cgstper) / 100);
                        var igst_amt = ((totalcost * igstper) / 100);
						
                        document.getElementById(st).value = formatCurrency(totalcost);
                        if(sgst_amt !='' && cgst_amt !=''){
                        document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
                        document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
                        var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
                        }else{
                         document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
                         var tot = parseFloat(totalcost) + parseFloat(igst_amt);
                        }                        
                        document.getElementById(totalvalField).value = formatCurrency(tot);
                        
                        
                        // calculate row wise discount
                        // var subt = parseFloat(total) - (parseFloat(dicountval) * parseFloat(qty));
                        // document.getElementById(st).value = formatCurrency(subt);
                        // calculate row wise tax
                        //  var taxamount = (parseFloat(totalcost) * parseFloat(rowtaxval)) / 100;
                        // document.getElementById("rowtaxamount" + "[" + ind + "]").value = formatCurrency(taxamount);
                        // line total
                        // document.getElementById(totalvalField).value = formatCurrency(parseFloat(totalcost) + parseFloat(taxamount));
                       
                        calculatetotal();
                        } else {
                        alert("Discount is exceeding from price");
                        var total = parseFloat(qty) * parseFloat(price);
                        var var3 = "linetotal" + "[" + ind + "]";
                        document.getElementById(var3).value = formatCurrency(total);
                        document.getElementById(discountField).value = "0.00";
                        document.getElementById(totalvalField).value = formatCurrency(total);
                        calculatetotal();
                    }
                }
                else {
                    alert("Stock is not Available");
                    document.getElementById(ent_qty).value = "";
                    document.getElementById(availableQty).value = "";
                    document.getElementById(ent_rate).value = "";
                    document.getElementById(hold_rate).value = "";
                    document.getElementById("imei[" + ind + "]").value = "";
                }
            }
            ////// calculate final value of form /////
            function calculatetotal() {
                var rowno1 = (document.getElementById("rowno").value);               
               // var sum_qty = 0;
                var sum_total = 0.00;
                var sum_discount = 0.00;
              //  var sum_tax = 0.00;
                var sum = 0.00;
                var priceVal = 0.00;
                for (var i = 0; i <= rowno1; i++) {
                  //  var temp_qty = "bill_qty" + "[" + i + "]";
                  //  var temp_total = "linetotal" + "[" + i + "]";
                    var temp_discount = "rowdiscount" + "[" + i + "]";
                   // var temp_taxamt = "rowtaxamount" + "[" + i + "]";
                    var total_amt = "total_val" + "[" + i + "]";
                    var price = "price" + "[" + i + "]";
                    var discountvar = 0.00;
                   // var totaltaxamt = 0.00;
                   // var totalamtvar = 0.00;
                    var total = 0.00;
                    var price_val = 0.00;
                    ///// check if discount value is something
                    if (document.getElementById(temp_discount).value) {
                        discountvar = document.getElementById(temp_discount).value;
                    } else {
                        discountvar = 0.00;
                    }
 
                     ///// check if line total price is something
                    if (document.getElementById(price).value) {
                        price_val = document.getElementById(price).value;
                    } else {
                        price_val = 0.00;
                    }
                     
                    ///// check if line total amount is something
                    if (document.getElementById(total_amt).value) {
                        total = document.getElementById(total_amt).value;
                    } else {
                        total = 0.00;
                    }

                    priceVal += parseFloat(price_val);
                    sum_discount += parseFloat(discountvar);
                    sum += parseFloat(total);

                }/// close for loop
                document.getElementById("sub_total").value = formatCurrency(sum);
                var round_off = parseFloat(parseFloat(Math.round(sum)) - parseFloat(sum)).toFixed(2);
                document.getElementById("total_qty").value = formatCurrency(priceVal);   
                document.getElementById("total_discount").value = formatCurrency(sum_discount);
                document.getElementById("round_off").value = formatCurrency(round_off); 
                document.getElementById("grand_total").value = formatCurrency(Math.round(sum));
//<?php if ($_REQUEST['discount_type'] == "PD") { ?>
//                    document.getElementById("total_discount").value = formatCurrency(sum_discount);
//<?php } ?>
//<?php if ($_REQUEST['tax_type'] == "PT") { ?>
//                    document.getElementById("tax_amount").value = formatCurrency(sum_tax);
//<?php } ?>
//                document.getElementById("grand_total").value = formatCurrency(parseFloat(sum_total) - parseFloat(sum_discount) + parseFloat(sum_tax));

            }

            ///// check total discount is exceeding from total minimum price of all product
            function check_total_discount() {
                if (parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)) {
                    alert("Discount is exceeding..!!");
                    document.getElementById("total_discount").value = "0.00";
                    document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value));
                } else {

                    document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value) + parseFloat(document.getElementById("tax_amount").value));
                }
            }
            ///// check total tax of all selling product
            function check_total_tax() {
                if (document.getElementById("complete_tax").value) {
                    var splittax = (document.getElementById("complete_tax").value).split("~");
                    var completeTax = splittax[0];

                } else {
                    var completeTax = 0.00;
                }

                var dis = document.getElementById("total_discount").value;
                if (dis) {
                    var disc = dis
                } else {
                    var disc = 0.00;
                }

                var calculateTax = (parseFloat(completeTax) * (parseFloat(document.getElementById("sub_total").value) - parseFloat(disc))) / 100;
                document.getElementById("tax_amount").value = formatCurrency(calculateTax);

                document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(disc) + parseFloat(calculateTax));
            }
        </script>
        <script type="text/javascript">
            ///// function for checking duplicate IMEI value
            function checkDuplicateSerial(fldIndx1, enteredsno) {   
			 document.getElementById("upd").disabled = false;
                if (enteredsno != '') {
                    var check2 = "imei[" + fldIndx1 + "]";
                    var flag = 1;
                    for (var i = 0; i <= fldIndx1; i++) {
                        var check1 = "imei[" + i + "]";
                        if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
                            if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
                                alert("Duplicate IMEI NO.");
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
            //// function to check whole form//
            function checkdata() {
                var maxno = document.getElementById("rowno").value;
                var flag = 1;
                for (var i = 0; i <= maxno; i++) {
                    var checkval = document.getElementById("imei[" + i + "]").value;
                    for (var j = 0; j <= maxno; j++) {
                        var checkvalin = document.getElementById("imei[" + j + "]").value;
                        if (j != i && checkvalin != '' && checkval != '') {
                            if (checkval == checkvalin) {
                                alert("Duplicate IMEI NO.");
                                document.getElementById("imei[" + j + "]").value = '';
                                document.getElementById("imei[" + j + "]").style.backgroundColor = "#F66";
                                document.getElementById("imei[" + j + "]").style.padding = '4';
                                flag *= 0;
                            } else {
                                document.getElementById("imei[" + j + "]").style.backgroundColor = "#FFFFFF";
                                document.getElementById("imei[" + j + "]").style.padding = '4';
                                flag *= 1;
                            }
                        }
                    }
                    ///// check available stock flag should not be N 
                    if (document.getElementById("avl_stock[" + i + "]").value == "N") {
                        flag *= 0;
                    } else {
                        flag *= 1;
                    }
                }
                if (flag == 0) {
                    document.getElementById("upd").disabled = true;
                    return false;
                } else {
                    document.getElementById("upd").disabled = false;
                    return true;
                }
            }
            // number validation function
            var specialKeys = new Array();
            specialKeys.push(8); //Backspace 
            function IsNumeric(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode
                if (charCode > 31 && (charCode < 48 || specialKeys.indexOf(charCode) != -1 || charCode > 57))
                {
                    alert('Only Numeric Number!');
                    return false;
                } else {
                    return true;
                }
            }
			
			///////////   style for tool tip text //////////////////////////////////////
			<style>
			.tooltipnew {
			  position: relative;
			 display: inline-block;
			  
			}
			
			.tooltipnew .tooltiptext {
			  visibility: hidden;
			  width: 200px;
			  background-color: #0066CC;
			  color: #fff;
			  text-align: center;
			  border-radius: 6px;
			  padding: 5px 0;
			
			  /* Position the tooltip */
			  position: absolute;
			  z-index: 1;
			}
			
			.tooltipnew:hover .tooltiptext {
			  visibility: visible;
			}
         </style>
			
        </script>
    </head>
    <body onKeyPress="return keyPressed(event);">
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-user"></i> Retail Billing </h2>
                    <?php if ($_GET['msg']) { ?><h4 align="center" style="color:#FF0000"><?= $_GET['msg'] ?></h4><?php } ?>
                    <div class="form-group" id="page-wrap" style="margin-left:10px;">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
						<div style="display:inline-block;float:right">
        <button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addCustomer.php?location<?=$pagenav?>'"><span>Add New Customer</span></button>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Billing  From<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                        <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true"  onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while ($result_parent = mysqli_fetch_array($res_parent)) {

                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
                                                ?>
                                                <option data-tokens="<?= $party_det['name'] . " | " . $result_parent['uid'] ?>" value="<?= $result_parent['location_id'] ?>" <?php if ($result_parent['location_id'] == $_REQUEST['po_from']) echo "selected"; ?> >
                                                    <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Customer Name<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                             <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true"  onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from customer_master where mapplocation='".$_REQUEST['po_from']."' and status='Active'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
                          ?>
                    		<option data-tokens="<?=$result_chl['customername']." | ".$result_chl['city']?>" value="<?=$result_chl['customerid']." | ".$result_chl['state']?>" <?php if($result_chl['customerid']." | ".$result_chl['state']==$_REQUEST['po_to'])echo "selected";?> >
                       <?=$result_chl['customername']." | ".$result_chl['city']." | ".$result_chl['state']." | ".$result_chl['contactno']." | ".$result_chl['emailid']?>
                    		</option>
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
                                <table class="table table-bordered" width="100%" id="itemsTable1">
                                    <thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th style="text-align:center" width="20%"><?=$imeitag?></th>
                                            <th style="text-align:center" width="20%">Product</th>
                                            <th style="text-align:center" width="10%">Price</th>
                                            <th style="text-align:center" width="10%">Discount/
                                                Unit</th>
                                            <th style="text-align:center" width="10%">Value After Discount</th>
                                            <th style="text-align:center" width="10%">SGST(%)</th>
                                            <th style="text-align:center" width="10%">SGST Amt</th>
                                            <th style="text-align:center" width="10%">CGST(%)</th>
                                            <th style="text-align:center" width="10%">CGST Amt</th>
                                            <th style="text-align:center" width="10%">IGST(%)</th>
                                            <th style="text-align:center" width="10%">IGST Amt</th>
                                            <th style="text-align:center" width="10%">Total </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="addr0">
                                            <td><input type="text" class="form-control required" name="imei[0]" id="imei[0]"  autocomplete="off" required onBlur="get_price(0);
                                                    checkDuplicateSerial(0, this.value);" style="padding: 4px;"></td>
                                            <td>
                                             <input type="text"  name="descrip[0]" class="form-control" id="descrip[0]" readonly ><div id="prd_desc0" style="display:inline-block;float:right"></div>
												<input type="hidden" name="reward_info_chck[0]" id="reward_info_chck[0]" >
												<input type="hidden" name="reward_point[0]" id="reward_point[0]" >
                                                <input type="hidden" name="bill_qty[0]" id="bill_qty[0]" value="1">
                                                <input type="hidden" name="prod_code[0]" id="prod_code[0]">
                                            </td>
                                            <td><input type="text" class="form-control" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return IsNumeric(event);"  required style="width:71px;text-align:right;padding: 4px">
                                            <input type="hidden" class="form-control" name="linetotal[0]" id="linetotal[0]"></td>
                                            <td><input type="text" class="form-control" name="rowdiscount[0]" id="rowdiscount[0]" onKeyPress="return IsNumeric(event);" autocomplete="off" onBlur="rowTotal(0);" style="width:66px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" value="0" style="width:71px;text-align:right;padding: 4px" readonly></td>
                                            <td><input type="text" class="form-control" name="rowsgstper[0]" id="rowsgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowsgstamount[0]" id="rowsgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowcgstper[0]" id="rowcgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowcgstamount[0]" id="rowcgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowigstper[0]" id="rowigstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowigstamount[0]" id="rowigstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:80px;text-align:right;padding: 4px">
                                                <input type="hidden" name="avl_stock[0]" id="avl_stock[0]">
                                                <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot id='productfooter' style="z-index:-9999;">
                                        <tr class="0">
                                            <td colspan="12" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
                                        </tr>

                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Total Price</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_qty" id="total_qty" class="form-control" value="0.00" readonly style="width:200px;"/>
                                    </div>
                                    <label class="col-md-2 control-label">Discount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_discount" id="total_discount" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                             <div class="form-group">
                                <div class="col-md-12">                                    
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Sub Total </label>
                                    <div class="col-md-2">
                                        <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Round Off </label>
                                    <div class="col-md-2">
                                        <input type="text" name="round_off" id="round_off" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Grand Total</label>
                                    <div class="col-md-2">
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo currencyFormat($po_row['po_value'] - $po_row['discount']); ?>" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>
                                    <div class="col-md-2">
                                        <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:none; width:200px" required><?php echo $toloctiondet[2]; ?></textarea>
                                    </div>
                                    <label class="col-md-2 control-label">Remark</label>
                                    <div class="col-md-2">
                                        <textarea name="remark" id="remark" class="form-control" style="resize:none;width:200px" ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Make Invoice" onClick="return checkdata();">
                                    &nbsp;
                                    <a title="Back"  class="btn btn-primary" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">Back</a>
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST[po_from]?>"/>
              <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST[po_to]?>"/>           
              <input type="hidden" name="disc_type" id="disc_type" value="<?=$_REQUEST[discount_type]?>"/>
              <input type="hidden" name="tx_type" id="tx_type" value="<?=$_REQUEST[tax_type]?>"/>
                <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~RETAIL"?>"/>
                                </div>
                            </div>
                        </form>
                    </div><!--close panel group-->
                </div><!--close col-sm-9-->
            </div><!--close row content-->
        </div><!--close container-fluid-->
        <?php 	
		if ($_REQUEST['po_from'] == '' || $_REQUEST['po_to'] == '' ) { ?>
            <script>
                $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
            </script>
            <?php
        }

        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>
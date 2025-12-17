<?php
require_once("../config/config.php");
$invoice = base64_decode($_REQUEST['invoiceno']);
$prodcode = base64_decode($_REQUEST['prodcode']);
$qty = base64_decode($_REQUEST['qty']);

$data = mysqli_fetch_array(mysqli_query($link1 , "select * from billing_master where challan_no = '".$invoice."' "));

$res_data = mysqli_query($link1,"SELECT prod_code,qty , price  FROM billing_model_data WHERE challan_no='".$invoice."' and prod_code = '".$prodcode."' ");
$ownerloc = mysqli_fetch_array(mysqli_query($link1 , "select owner_code from billing_imei_data where doc_no = '".$invoice."' "));




////// final submit form ////
@extract($_POST);
if($_POST['process'] == "Process"){
	////// make product code and imei array from posted string
	$arr_postmodel = explode(",",base64_decode(urldecode($prod_str)));
	$arr_postimei = explode(",",base64_decode(urldecode($imei_str)));
	$arr_poststktyp = explode(",",base64_decode(urldecode($stktyp_str)));
	$scanqty = count($arr_postimei);
	if(intval($_POST['totchallan_qty']) == $scanqty){
	if(!empty($arr_postimei)){
		////initialize params
		mysqli_autocommit($link1, false);
		$flag = true;
		$error_msg = array();
		$upd_cnt = 1;
		$arr_qty_stk = array();
		for($i = 0; $i < count($arr_postimei); $i++){
		if($arr_poststktyp[$i]=="OK"){ $arr_qty_stk[$arr_postmodel[$i]]["OK"]=+1; }else if($arr_poststktyp[$i]=="DAMAGE"){$arr_qty_stk[$arr_postmodel[$i]]["DAMAGE"]=+1;}else{$arr_qty_stk[$arr_postmodel[$i]]["MISSING"]=+1;}
		/////  update owner_code in billing imei data table //////////////////////////
	     $result_imeidata=mysqli_query($link1,"update billing_imei_data set owner_code ='".$_POST['toloc']."', stock_type = '".$arr_poststktyp[$i]."' where doc_no='".$refno."' and imei1='".$arr_postimei[$i]."' ");
	    //// check if query is not executed
        if (!$result_imeidata) {
	      $flag = false;
	      $error_msg =  "Error details3: " . mysqli_error($link1) . ".";
          }	else{
		  	////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 05 SEP 2022
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$arr_postimei[$i]."'"))>0){
				$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$_POST['toloc']."', prod_code='".$arr_postmodel[$i]."', rem_qty='1', stock_type='".$arr_poststktyp[$i]."', ref_no='".$refno."', ref_date='".$data['sale_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$arr_postimei[$i]."'");
				if (!$res_upd_ss) {
					$flag = false;
					$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
				}
			}else{
				$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$_POST['toloc']."', prod_code='".$arr_postmodel[$i]."', serial_no='".$arr_postimei[$i]."',inside_qty='1', rem_qty='1', stock_type='".$arr_poststktyp[$i]."', ref_no='".$refno."', ref_date='".$data['sale_date']."',import_date='".$data['sale_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
				if (!$res_inst_ss) {
					$flag = false;
					$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
				}
			}
		  
		  }
	}////// close for loop
		$arr_uniq_part = array_unique($arr_postmodel);
		foreach($arr_uniq_part as $partcode){
		  //// update data table /////////////////////////////////////////		  
		   $result_data=mysqli_query($link1,"update billing_model_data set okqty='".$arr_qty_stk[$partcode]["OK"]."', damageqty='".$arr_qty_stk[$partcode]["DAMAGE"]."', missingqty='".$arr_qty_stk[$partcode]["MISSING"]."' , scan = 'Y' where challan_no = '".$refno."' and prod_code = '".$partcode."'");
		   //// check if query is not executed
		   if (!$result_data) {
	           $flag = false;
               $error_msg =  "Error details2: " . mysqli_error($link1) . ".";
		   }		   
		  
		  
		  
		    ///// update stock in inventory //			
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$partcode."' and asc_code='".$_POST['toloc']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			 $result=mysqli_query($link1,"update stock_status set qty=qty+'".$_POST['totchallan_qty']."',okqty=okqty+'".$arr_qty_stk[$partcode]["OK"]."',broken=broken+'".$arr_qty_stk[$partcode]["DAMAGE"]."',missing=missing+'".$arr_qty_stk[$partcode]["MISSING"]."',updatedate='".$datetime."' where partcode='".$partcode."' and asc_code='".$_POST['toloc']."'");
		  }
		  else{
			 //// if product is not exist then add in inventory
		 $result=mysqli_query($link1,"insert into stock_status set asc_code='".$_POST['toloc']."',partcode='".$partcode."',qty=qty+'".$_POST['totchallan_qty']."',okqty='".$arr_qty_stk[$partcode]["OK"]."',broken='".$arr_qty_stk[$partcode]["DAMAGE"]."',missing='".$arr_qty_stk[$partcode]["MISSING"]."',uom='PCS',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg =  "Error details1: " . mysqli_error($link1) . ".";
           }
		   if($arr_qty_stk[$partcode]["OK"]!=0 && $arr_qty_stk[$partcode]["OK"]!="" && $arr_qty_stk[$partcode]["OK"]!=0.00){		  
		  ////// entry in stock ledger /////////////////////////////////////////////////////
		    $flag=stockLedger($refno,$today,$partcode,$_POST['fromloc'],$_POST['toloc'],$_POST['toloc'],"IN","OK","Receive Stock",$arr_qty_stk[$partcode]["OK"],$_POST['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			}
			if($arr_qty_stk[$partcode]["DAMAGE"]!=0 && $arr_qty_stk[$partcode]["DAMAGE"]!="" && $arr_qty_stk[$partcode]["DAMAGE"]!=0.00){		  
		  ////// entry in stock ledger /////////////////////////////////////////////////////
		    $flag=stockLedger($refno,$today,$partcode,$_POST['fromloc'],$_POST['toloc'],$_POST['toloc'],"IN","DAMAGE","Receive Stock",$arr_qty_stk[$partcode]["DAMAGE"],$_POST['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			}
			if($arr_qty_stk[$partcode]["MISSING"]!=0 && $arr_qty_stk[$partcode]["MISSING"]!="" && $arr_qty_stk[$partcode]["MISSING"]!=0.00){		  
		  ////// entry in stock ledger /////////////////////////////////////////////////////
		    $flag=stockLedger($refno,$today,$partcode,$_POST['fromloc'],$_POST['toloc'],$_POST['toloc'],"IN","MISSING","Receive Stock",$arr_qty_stk[$partcode]["MISSING"],$_POST['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			}
		}
		
		//////// update received status only when  no of items  scaned ////////////////////////////////
		 $data= mysqli_fetch_array(mysqli_query($link1 , "select count(id) as vid from billing_model_data where challan_no = '".$refno."' "));
		 $check = mysqli_fetch_array(mysqli_query($link1 , " select count(id) as cid from billing_model_data where challan_no = '".$refno."' and scan = 'Y' "));
		
		if($data['vid'] == $check['cid']){
			//// Update status in  master table						
           $result_master=mysqli_query($link1,"update billing_master set status='Received',receive_date='".$today."',receive_time='".$currtime."',receive_by='".$_SESSION['userid']."',receive_remark='".$_POST['rcv_rmk']."',receive_ip='".$ip."' where challan_no='".$refno."'");
	//// check if query is not executed
          if (!$result_master) {
	      $flag = false;

	      $error_msg =  "Error details3: " . mysqli_error($link1) . ".";
             }
		  }
		  
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$invoice,"SERIAL ATTACH","SCAN",$ip,$link1,$flag);
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Serial nos. are successfully attached with ref. no. ".$invoice;
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed ".implode(" -> ",$error_msg).". Please try again.";
		} 
		mysqli_close($link1);
		///// move to parent page
		header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
		exit;
	}else{
		$msg = "No Serial no. scanned.Request could not be processed.";
		///// move to parent page
	      header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
		 exit;
	    }
	 }
	else {
	    $msg = "No Serial no. scanned and Total challan qty not matched .Request could not be processed.";
   	   header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
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
 <script type="text/javascript" language="javascript">
 $(document).ready(function(){
	$("#frm1").validate();
 });
 </script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-barcode"></i>&nbsp;&nbsp;Select / Scan<?=$imeitag?></h2>
      		<div class="form-group" id="page-wrap" style="margin-left:10px;">
                	<div class="panel-group">
    					<div class="panel panel-default table-responsive">
        					<div class="panel-heading heading1">Invoice Information</div>
         					<div class="panel-body">
          						<table class="table table-bordered" width="100%">
            						<tbody>
              							<tr>
                							<td width="20%"><label class="control-label">Invoice No.</label></td>
                							<td width="30%"><?php echo $data['challan_no'];?></td>
                                            <td width="20%"><label class="control-label">Invoice Date</label></td>
                                            <td width="30%"><?php echo $data['sale_date'];?></td>
              							</tr>
                                        <?php 
										$arr_model = array();
										//$arr_qty = array();
										while($row_data = mysqli_fetch_assoc($res_data)){
										$price = $row_data['price'];
											$proddet=explode("~",getProductDetails($row_data['prod_code'],"productname,productcolor",$link1));
											$arr_model[$row_data['prod_code']] = $proddet[0];
											//$arr_qty[$row_data['prod_code']] = $row_data["qty"];
										?>
                                        <tr>
                							<td colspan="2"><label class="control-label"><?=$proddet[0]." (".$proddet[1].") ".$row_data['prod_code']?></label></td>
                							<td colspan="2"><?=$row_data["qty"]?><input name="<?=$row_data['prod_code']?>" id="<?=$row_data['prod_code']?>" value="<?=$row_data["qty"]?>" type="hidden"/></td>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                               	</table>
                        	</div>
						</div>
						<div class="panel panel-default table-responsive">
        					<div class="panel-heading heading1">Select / Scan<?=$imeitag?></div>
         					<div class="panel-body">
                            	<div id="error_msg" align="center" class="alert-danger" style="display:none"></div><br/>                               
                                <div style="float:left; display:inline-block; width:50%">
                                	<form name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                                    <div style="float:left; display:inline-block">
                                    <select name="modelcode" id="modelcode" class="form-control">
                                    	<?php foreach($arr_model as $prodcode => $prodname){ ?>
                                    	<option value="<?=$prodcode?>"><?=$prodname?></option>
                                        <?php }?>
                                    </select>
                                    <select name="stocktype" id="stocktype" class="form-control">
                                    	<option value="OK" selected>OK</option>
                                        <option value="DAMAGE">DAMAGE</option>
                                        <option value="MISSING">MISSING</option>
                                    </select>&nbsp;
                                    </div>
                                    <div style="float:left; display:inline-block"><input type="text" id="search" class="form-control required alphanumeric" placeholder="enter<?=$imeitag?>here"/></div>
                                    <div style="float:left; display:inline-block">&nbsp;&nbsp;<button id="sbt" class="btn <?=$btncolor?>" onClick="enterIMEI();" type="button">Submit</button></div>
                                    </form>
                                </div>
                                <div style="float:left; display:inline-block;  width:50%; text-align:left">
                                <form name="frm2" id="frm2" class="form-horizontal" action="" method="post">
                                	<p><label class="control-label">Selected <?=$imeitag?>:</label></p>
                                    <ul id="output"></ul>
                                    <button class="btn <?=$btncolor?>" onClick="checkIMEI()" type="button">Validate</button>
                                    <input name="prod_str" id="prod_str" value="" type="hidden"/>
                                    <input name="imei_str" id="imei_str" value="" type="hidden"/>
                                    <input name="stktyp_str" id="stktyp_str" value="" type="hidden"/>
                                    <input type="submit" name="process" id="process" value="Process" class="btn <?=$btncolor?>" disabled/>
							
									<input type="hidden" name="totchallan_qty" id="totchallan_qty" value="<?=$qty?>" >
									<input type="hidden" name="refno" id="refno" value="<?=$invoice?>" >
									<input type="hidden" name="toloc" id="toloc" value="<?=$data['to_location']?>">
									<input type="hidden" name="fromloc" id="fromloc" value="<?=$data['from_location']?>">
								     <input type="hidden" name="price" id="price" value="<?=$price?>">
									<input type="hidden" name="prodcode" id="prodcode" value="<?=$prodcode?>">
                                    <input title="Back" type="button" style="float:right" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='receiveInvoice.php?<?=$pagenav?>'">
                                </form>    
                                </div>
                            </div>
                        </div>
					</div>
                
      		</div>
    	</div> 
  	</div>
</div>
<script>
///// check duplicate IMEI
function checkDuplicate(imei){
	var cnt = ($('ul#output li').length);
	var flag = 1;
    if (cnt > 0) {
    	for (var j = 0; j < cnt; j++) {
			var str2 = $("#"+j).text();
			var splitstr = str2.split(" -> ");
			var check2 = $.trim(splitstr[0]);
            for (var i = 0; i <= cnt; i++) {
            	if (j != i) {
					var str1 = imei;
					//alert(str1);
					var check1 = $.trim(str1);
					//alert(check2 +"=="+ check1);
					if (check2 == check1) {
						//document.getElementById("error_msg").innerHTML = "You are entering Duplicate IMEI";
						flag *= 0;
					} else {
                     	flag *= 1;
					}
				}
			}
		}
        //////////
		return flag;
        /*if (flag == 0) {
        	return flag;
		} else {
        	return flag;
		}*/
	}
}
function enterIMEI(){
	var prodcode = $("#modelcode").val();
	var imei = $("#search").val();
	var stktyp = $("#stocktype").val();
	///// remove entered data from field
	document.getElementById("search").value="";
	//////check duplicate IMEI
	var resp = checkDuplicate(imei);
	if(resp == "0"){
		document.getElementById("error_msg").style.display = "";
		document.getElementById("error_msg").innerHTML = "You are entering Duplicate Serial";
	}else{
		document.getElementById("error_msg").innerHTML = "";
		document.getElementById("error_msg").style.display = "none";
	 var list = $("#output");
	 var nextid = ($('ul#output li').length);
	  $.each(list, function(i) {
		$("<li/>")
		  .text(imei+" -> "+prodcode+" -> "+stktyp)
		  .attr("id",nextid)
		  .append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  //.append($("<button id='cncl_"+nextid+"' class='btn btn-primary' onclick=removeIMEI(this)></button>").text("Remove"))
		  .append($("<i id='cncl_"+nextid+"' class='fa fa-close' onclick=removeIMEI(this)></i>"))
		  //.append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  //.append($("<span id='msg_"+nextid+"'></span>"))
		  .appendTo(list);
		$("<span/>")
		  .attr("id",'msg_'+nextid)
		  //.append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  .appendTo(list);
	  });
	}
}
function removeIMEI(obj){
	var spltid = obj.id.split("_"); 
	var btn = spltid[0];
	//alert(btn);
	var indx = spltid[1];
	//alert(indx);
	var cnt = ($('ul#output li').length);
	$('#'+indx+'').remove();
	$('#msg_'+indx+'').remove();
	if(indx == (cnt -1)){
		//alert("hi");
	}else{
		for(var i = indx; i < cnt-1; i++){
			var current_indx = parseInt(i) + 1;
			var new_indx = parseInt(current_indx) - 1;
			///// change id again
			$('#'+current_indx+'').prop('id', new_indx);
			$('#cncl_'+current_indx+'').prop('id', 'cncl_'+new_indx);
			$('#msg_'+current_indx+'').prop('id', 'msg_'+new_indx);
		}
	}
	document.getElementById("error_msg").innerHTML = "";
}
///// check each serial no.
function checkIMEI(){
	var cnt = ($('ul#output li').length);
	var prodArray = {}; // note this
	var allOk = 1;
	var imei_str = "";
	var prod_str = "";
	var stktyp_str = "";
	//var selmodel = document.getElementById("modelcode").value;
	for(var i=0; i<cnt; i++){
		var str = $("#"+i).text();
		var splitstr = str.split(" -> ");
		var trimStr = $.trim(splitstr[0]);
		var trimProd = $.trim(splitstr[1]);
		var trimStkTyp = $.trim(splitstr[2]);
		///// make imei string
		if(imei_str!=""){
			imei_str += ",".concat(trimStr);
			prod_str += ",".concat(trimProd);
			stktyp_str += ",".concat(trimStkTyp);
		}else{
			imei_str += trimStr;
			prod_str += trimProd;
			stktyp_str += trimStkTyp;
		}
		/// make prod code associate array of prod qty
		if(typeof prodArray[trimProd] === 'undefined') {
			// does not exist
			var ocr = 1;
		}
		else {
			// does exist
			var ocr = prodArray[trimProd] + 1;
		}
		prodArray[trimProd] = ocr;
		$.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{scanimeisale:trimStr, reqowner:'<?=$ownerloc['owner_code']?>', reqmodel:trimProd, indx:i},
			success:function(data){
				//alert(data);
				var result = data.split("~");
				$('#msg_'+result[1]).html('<div class="alert alert-'+result[2]+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Validation <i class="fa '+result[3]+'"></i> </strong>&nbsp;&nbsp;'+result[0]+'.</div>');
				if(result[2] == "success"){
					allOk *= 1;
				}else{
					allOk *= 0;
				}
			}
	  });
	}
	///var allOk;
    ///// check prod array and count each product qty 
	var checkFlag = 1;
	$.each(prodArray, function( i, v ) {
  		//alert( i + ": " + v );
		//alert(document.getElementById(i).value +"=="+ v);
		if(parseInt(document.getElementById(i).value) == parseInt(v)){
			checkFlag *= 1;
		}else{
			checkFlag *= 0;
		}
	});
	
	if(checkFlag == 1){
		if(allOk == 1){
			/////// encode imei string and assign it in form field to post data
			var imeitoken = encodeURIComponent(window.btoa(imei_str));
			var prodtoken = encodeURIComponent(window.btoa(prod_str));
			var stktyptoken = encodeURIComponent(window.btoa(stktyp_str));
			document.getElementById("imei_str").value = imeitoken;
			document.getElementById("prod_str").value = prodtoken;
			document.getElementById("stktyp_str").value = stktyptoken;
			///////
			document.getElementById("error_msg").style.display = "none";
			document.getElementById("error_msg").innerHTML = "";
			document.getElementById("process").disabled = false;
			
		}else{
			document.getElementById("error_msg").style.display = "";
			document.getElementById("error_msg").innerHTML = "Serial no. validation failed";
			document.getElementById("process").disabled = true;
		}
	}else{
		document.getElementById("error_msg").style.display = "";
		document.getElementById("error_msg").innerHTML = "No. of Serial nos. are mismatched with product qty";
		document.getElementById("process").disabled = true;
	}
}
</script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
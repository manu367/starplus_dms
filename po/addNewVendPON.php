<?php
require_once("../config/config.php");
/////// get from party state
$from_ptydet = explode("~",getLocationDetails($_REQUEST["po_from"],"state,addrs",$link1));
$from_state = $from_ptydet[0];
/////// get to party state
$to_state = getVendorDetails($_REQUEST["po_to"],"state",$link1);
@extract($_POST);
////// case 2. if we want to Add new user
if($_POST){
	if($_POST['upd']=='Save'){
 		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($parentcode.$partycode.$_POST['upd']);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgVPO'])?$_SESSION['msgVPO']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgVPO'] = $messageIdent;
			///////
    		mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			/////// define array for update margin after each import
			$arr_imptpart = array();
			///////
			$parentcodenew = base64_decode($parentcode);
			$partycodenew = base64_decode($partycode);
	
     //// Make System generated PO no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from vendor_order_master where po_from='".$partycodenew."' and req_type='VPO'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po[no]+1;
	$po_no=$partycodenew."VPO".$c_nos; 
	
	
	///// Insert Master Data
	$query1= "INSERT INTO vendor_order_master set po_to='".$parentcodenew."',po_from='".$partycodenew."',po_no='".$po_no."',temp_no='".$c_nos."',ref_no='".$ref_no."',requested_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',req_type='VPO',status='Pending',po_value='".$sub_total."',create_by='".$_SESSION['userid']."',ip='".$ip."',taxtype='".$taxinfo[0]."',currency_type='INR',remark='".$remark."',grand_total='".$grand_total."',delivery_address='".$delivery_address."',payment_status='".$payment_terms."', total_sgst_amt = '".$sgst_total."' , total_cgst_amt = '".$cgst_total."' , total_igst_amt = '".$igst_total."' ";
 
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
			/////////// insert data
	  $query2="insert into vendor_order_data set po_no='".$po_no."', prod_code='".$val."', req_qty='".$req_qty[$k]."', pending_qty = '".$req_qty[$k]."', po_price='".$price[$k]."', po_value='".$linetotal[$k]."', mrp='".$mrp[$k]."', totalval='".$total[$k]."',currency_type='INR',uom='PCS', deliv_schedule='".$deliv_date[$k]."', cgst_per ='".$cgst_per[$k]."' , cgst_amt = '".$cgst_amt[$k]."' , sgst_per = '".$sgst_per[$k]."' , sgst_amt = '".$sgst_amt[$k]."' , igst_amt = '".$igst_amt[$k]."' , igst_per = '".$igst_per[$k]."'";	  
		$result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error details2: " . mysqli_error($link1) . ".";
           }else{
				$arr_imptpart[] = $val;
			}
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"VPO","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Vendor Purchase Order is successfully placed with ref. no.".$po_no;
		///// include auto run script for each partcode of this PO so that margin can update accordingly
		include("update_margin_after_import.php");
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$err_msg;
	} 
	}else {
        	//you've sent this already!
			$msg="You have already saved this PO";
			$cflag = "warning";
			$cmsg = "Warning";
    	}
    mysqli_close($link1);
	///// move to parent page
    header("location:vendorPurchaseList.php?msg=".$msg."".$pagenav);
    exit;
 }
}
///get access product
$acc_psc = getAccessProduct($_SESSION['userid'],$link1);
///get access brand
$acc_brd = getAccessBrand($_SESSION['userid'],$link1);
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
	/////////// function to get available stock of ho
  	function getAvlStk(indx){
	  	var productCode=document.getElementById("prod_code["+indx+"]").value;
		var locationCode=$('#po_from').val();
		var vendcode=$('#po_to').val();
		var stocktype="okqty";
		$.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{locstk:productCode,loccode:locationCode,vendorCode:vendcode ,stktype:stocktype,indxx:indx},
			success:function(data){
				//alert(data);
				var getdata=data.split("~");
				document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
				<?php if($from_state == $to_state){ ?>
				document.getElementById("sgst_per["+getdata[1]+"]").value=getdata[2];
				document.getElementById("cgst_per["+getdata[1]+"]").value=getdata[3];
				<?php }else{ ?>
				document.getElementById("igst_per["+getdata[1]+"]").value=getdata[4];
				<?php }?>
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
				var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:300px"><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk('+num+');checkDuplicate('+num+', this.value);"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['productcode'];?>"><?php echo $br['productname']. " | " . $br['productcode'] . " | " . $br['productcolor'];?></option><?php }?></select></div><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div></td><td><input type="text" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']" style="width:80px;" autocomplete="off" required onKeyUp="rowTotal('+num+');"></td><td><input type="text" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" style="width:80px;" value="0" readonly><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/></td><td><input type="text" class="form-control number required" required name="price['+num+']" id="price['+num+']" onKeyUp="rowTotal('+num+');" autocomplete="off" style="width:100px;" required min="1"></td><td><input type="text" class="form-control" name="linetotal['+num+']" id="linetotal['+num+']" autocomplete="off" style="width:130px;" readonly></td><?php if($from_state == $to_state){ ?><td><input type="text" class="form-control" name="sgst_per['+num+']" id="sgst_per['+num+']" style="width:70px;" readonly></td><td><input type="text" class="form-control" name="sgst_amt['+num+']" id="sgst_amt['+num+']" style="width:100px;" readonly></td><td><input type="text" class="form-control" name="cgst_per['+num+']" id="cgst_per['+num+']" style="width:70px;" readonly></td><td><input type="text" class="form-control" name="cgst_amt['+num+']" id="cgst_amt['+num+']" style="width:100px;" readonly></td><?php }else{?><td><input type="text" class="form-control" name="igst_per['+num+']" id="igst_per['+num+']" style="width:70px;" readonly></td><td><input type="text" class="form-control" name="igst_amt['+num+']" id="igst_amt['+num+']"style="width:100px;"  readonly></td><?php }?><td><div style="display:inline-block;float:left"><input type="text" name="total['+num+']" class="form-control" id="total['+num+']" value="" style="width:130px;" readonly/></div><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td></tr>';
				$('#itemsTable1').append(r);
				makeSelect();
				//makeCalDeliv(num);            
			}
  		});
	});
	function makeSelect(){
  		$('.selectpicker').selectpicker({
			liveSearch: true,
			showSubtext: true
  		});
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
		<?php if($from_state == $to_state){ ?>
		var sgatperid="sgst_per"+"["+ind+"]";
		var sgatamtid="sgst_amt"+"["+ind+"]";
		var cgatperid="cgst_per"+"["+ind+"]";
		var cgatamtid="cgst_amt"+"["+ind+"]";
		<?php }else{?>
		var igatperid="igst_per"+"["+ind+"]";
		var igatamtid="igst_amt"+"["+ind+"]";
		<?php }?>
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
		<?php if($from_state == $to_state){ ?>
		document.getElementById(sgatperid).value="0.00";
		document.getElementById(sgatamtid).value="0.00";
		document.getElementById(cgatperid).value="0.00";
		document.getElementById(cgatamtid).value="0.00";
		<?php }else{?>
		document.getElementById(igatperid).value="0.00";
		document.getElementById(igatamtid).value="0.00";
		<?php }?>
	  	rowTotal(ind);
	}
	/////// calculate line total /////////////
	function rowTotal(ind){
		var ent_qty = "req_qty["+ind+"]";
		var ent_rate = "price["+ind+"]";
		var hold_rate = "holdRate["+ind+"]";
		var availableQty = "avl_stock["+ind+"]";
		var prodCodeField = "prod_code["+ind+"]";
		var sgstper = "sgst_per["+ind+"]";
		var cgstper = "cgst_per["+ind+"]";
		var igstper = "igst_per["+ind+"]";
		var prodmrpField = "mrp["+ind+"]";
		var sgstamt = 0.00; var cgstamt = 0.00; var igstamt = 0.00;
		var holdRate = document.getElementById(hold_rate).value;
		////// check if entered qty is something
		if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}
		/////  check if entered price is somthing
		if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
		 var total= parseFloat(qty)*parseFloat(price);
		 var var3="linetotal["+ind+"]";
		 document.getElementById(var3).value=(total);
		 ////// calculate tax
		<?php if($from_state == $to_state){ ?>
		sgstamt = ((parseFloat(document.getElementById(sgstper).value) * total)/100).toFixed(2);
		document.getElementById("sgst_amt["+ind+"]").value=(sgstamt);
		cgstamt = ((parseFloat(document.getElementById(cgstper).value) * total)/100).toFixed(2);
		document.getElementById("cgst_amt["+ind+"]").value=(cgstamt);
		<?php }else{?>
		igstamt = ((parseFloat(document.getElementById(igstper).value) * total)/100).toFixed(2);
		document.getElementById("igst_amt["+ind+"]").value=(igstamt);
		<?php }?>
		var totalval = (parseFloat(total) + parseFloat(sgstamt) + parseFloat(cgstamt) + parseFloat(igstamt)).toFixed(2);
		document.getElementById("total["+ind+"]").value=(totalval);
		calculatetotal();
	}
	////// calculate final value of form /////
	function calculatetotal(){
		var rowno=(document.getElementById("rowno").value);
		var sum_qty=0;
		var sum_ltotal=0.00; 
		var sum_discount=0.00;
		var sum_sgst = 0.00;
		var sum_cgst = 0.00;
		var sum_igst = 0.00;
		var sum_total = 0.00;
    	for(var i=0;i<=rowno;i++){
			var temp_qty="req_qty["+i+"]";
			var temp_ltotal="linetotal["+i+"]";
			var temp_sgst="sgst_amt["+i+"]";
			var temp_cgst="cgst_amt["+i+"]";
			var temp_igst="igst_amt["+i+"]";
			var temp_total="total["+i+"]";
			var ltotalamtvar=0.00;
			var totalamtvar=0.00;
			///// check if line total value is something
        	if(document.getElementById(temp_ltotal).value){ ltotalamtvar= document.getElementById(temp_ltotal).value;}else{ ltotalamtvar=0.00;}
			if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
			////// add taxes
			<?php if($from_state == $to_state){ ?>
			sum_sgst+=parseFloat(document.getElementById(temp_sgst).value);
			sum_cgst+=parseFloat(document.getElementById(temp_cgst).value);
			<?php }else{ ?>
			sum_igst+=parseFloat(document.getElementById(temp_igst).value);
			<?php }?>
			sum_qty+=parseFloat(document.getElementById(temp_qty).value);
			sum_ltotal+=parseFloat(ltotalamtvar);
			sum_total+=parseFloat(totalamtvar);
		}/// close for loop
		document.getElementById("total_qty").value=sum_qty;
		document.getElementById("sub_total").value=(sum_ltotal).toFixed(2);
		<?php if($from_state == $to_state){ ?>
		document.getElementById("sgst_total").value=(sum_sgst).toFixed(2);
		document.getElementById("cgst_total").value=(sum_cgst).toFixed(2);
		<?php }else{?>
		document.getElementById("igst_total").value=(sum_igst).toFixed(2);
		<?php }?>
		document.getElementById("grand_total").value=(sum_total).toFixed(2);
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
				if (fldIndx1 != i && (document.getElementById(check2).value == document.getElementById(check1).value )){
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
                                $sql_chl = "select * from access_location where uid='".$_SESSION['userid']."' AND id_type IN ('HO','BR') and status='Y'";
                                $res_chl = mysqli_query($link1,$sql_chl);
                                while($result_chl = mysqli_fetch_array($res_chl)){
                                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                                      //if($party_det['id_type']=='HO'){
                                ?>
                    			<option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> ><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                    			<?php
						  			//}
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
                    			<option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?> ><?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?></option>
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
          		<table width="200%" id="itemsTable1" class="table table-bordered table-hover ">
            		<thead>
              			<tr class="<?=$tableheadcolor?>" >
                			<th>Product</th>
							<th>Qty</th>
                            <th>Available Stock</th>
							<th>Purchase Price (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                			<th>Value</th>
                            <?php if($from_state == $to_state){ ?>
                			<th>SGST %</th>
                            <th>SGST Amt</th>
                            <th>CGST %</th>
                            <th>CGST Amt</th>
                            <?php }else{?>
                            <th>IGST %</th>
                            <th>IGST Amt</th>
                            <?php }?>
                			<th>Total</th>
              			</tr>
            		</thead>
            		<tbody>
              			<tr id='addr0'>
                			<td>
                				<div id="pdtid0" style="display:inline-block;float:left; width:300px">
                  					<select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk(0);checkDuplicate(0, this.value);">
                    					<option value="">--None--</option>
										<?php 
										$model_query="select productcode,productname,productcolor from product_master where status='active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";
										$check1=mysqli_query($link1,$model_query);
										while($br = mysqli_fetch_array($check1)){?>
										<option value="<?php echo $br['productcode'];?>"><?php echo $br['productname']. " | " . $br['productcode'] . " | " . $br['productcolor'];?></option>
										<?php }?>
                  					</select>
                  				</div>
                  				<div id="prd_desc0" style="display:inline-block;float:right"></div>                  			</td>
				  			<td><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]" style="width:80px;" autocomplete="off" required onKeyUp="rowTotal(0);"></td>
                            <td><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:80px;" value="0" readonly>
                                     <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                            <input name="holdRate[0]" id="holdRate[0]" type="hidden"/></td>
							<td><input type="text" class="form-control number required" required name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" style="width:100px;" required min="1"></td>
                			<td><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" style="width:130px;" readonly></td>
                            <?php if($from_state == $to_state){ ?>
                			<td><input type="text" class="form-control" name="sgst_per[0]" id="sgst_per[0]" style="width:70px;" readonly></td>
                            <td><input type="text" class="form-control" name="sgst_amt[0]" id="sgst_amt[0]" style="width:100px;" readonly></td>
                            <td><input type="text" class="form-control" name="cgst_per[0]" id="cgst_per[0]" style="width:70px;" readonly></td>
                            <td><input type="text" class="form-control" name="cgst_amt[0]" id="cgst_amt[0]" style="width:100px;" readonly></td>
                            <?php }else{?>
                            <td><input type="text" class="form-control" name="igst_per[0]" id="igst_per[0]" style="width:70px;" readonly></td>
                            <td><input type="text" class="form-control" name="igst_amt[0]" id="igst_amt[0]"style="width:100px;"  readonly></td>
                            <?php }?>
                			<td><input type="text" name="total[0]" class="form-control" id="total[0]" value="" style="width:130px;" readonly/></td>
              			</tr>
            		</tbody>
            		<tfoot id='productfooter' style="z-index:-9999;">
              			<tr class="0">
                			<td colspan="10" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              			</tr>
            		</tfoot>
          		</table>
          	</div>
            <div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label">Total Qty</label>
              		<div class="col-md-3">
                		<input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              		</div>
              		<label class="col-md-3 control-label">Sub Total</label>
              		<div class="col-md-3">
                		<input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly/>
              		</div>
            	</div>
          	</div>
            <?php if($from_state == $to_state){ ?>
            <div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label"></label>
              		<div class="col-md-3">
                    
              		</div>
              		<label class="col-md-3 control-label">SGST Total</label>
              		<div class="col-md-3">
                		<input type="text" name="sgst_total" id="sgst_total" class="form-control" value="0.00" readonly/>
              		</div>
            	</div>
          	</div>
            <div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label"></label>
              		<div class="col-md-3">
                    
              		</div>
              		<label class="col-md-3 control-label">CGST Total</label>
              		<div class="col-md-3">
                		<input type="text" name="cgst_total" id="cgst_total" class="form-control" value="0.00" readonly/>
              		</div>
            	</div>
          	</div>
            <?php }else{?>
            <div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label"></label>
              		<div class="col-md-3">
                    
              		</div>
              		<label class="col-md-3 control-label">IGST Total</label>
              		<div class="col-md-3">
                		<input type="text" name="igst_total" id="igst_total" class="form-control" value="0.00" readonly/>
              		</div>
            	</div>
          	</div>
            <?php }?>
            <div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label"></label>
              		<div class="col-md-3">
                    
              		</div>
              		<label class="col-md-3 control-label">Grand Total</label>
              		<div class="col-md-3">
                		<input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              		</div>
            	</div>
          	</div>
          	<div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>
              		<div class="col-md-3">
                		<textarea name="delivery_address" id="delivery_address" class="form-control addressfield required" rows="5"  style="resize:none" required><?=$from_ptydet[1]?></textarea>
              		</div>
              		<label class="col-md-3 control-label">Remark</label>
              		<div class="col-md-3">
                		<textarea name="remark" id="remark" class="form-control addressfield" rows="5" style="resize:none"></textarea>
              		</div>
            	</div>
          	</div>
          	<div class="form-group">
            	<div class="col-md-10"><label class="col-md-3 control-label">Payment Terms</label>
              		<div class="col-md-3">
                		<textarea name="payment_terms" id="payment_terms" class="form-control addressfield" rows="5" style="resize:none"></textarea>
              		</div>
              		<label class="col-md-3 control-label"></label>
              		<div class="col-md-3">   
              		
                    </div>
            	</div>
          	</div>
          	<div class="form-group">
            	<div class="col-md-12" align="center">
              		<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Save" title="Save This PO" <?php if($_POST['upd']=="Save"){ echo "disabled";}?>>
                	<input type="hidden" name="parentcode" id="parentcode" value="<?=base64_encode($_REQUEST['po_to'])?>"/>
                	<input type="hidden" name="partycode" id="partycode" value="<?=base64_encode($_REQUEST['po_from'])?>"/>
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
<?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>
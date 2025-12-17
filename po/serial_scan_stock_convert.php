<?php
require_once("../config/config.php");
$doc_no = base64_decode($_REQUEST['id']);
$doc_date = base64_decode($_REQUEST['docdate']);
$doc_loc = base64_decode($_REQUEST['location']);
$conv_type = base64_decode($_REQUEST['stocktype']);
$convertFrom = explode(" to ",$conv_type);
if($convertFrom[0] == 'okqty'){
	 $typestock = "OK";
}else if ($convertFrom[0] == 'missing'){
	$typestock = "MISSING";
	
}else if($convertFrom[0] == 'damage' || $convertFrom[0] == 'broken'){
	$typestock = "DAMAGE";
}else {
	
}

if($convertFrom[1] == 'okqty'){
	 $contypestock = "OK";
}else if ($convertFrom[1] == 'missing'){
	$contypestock = "MISSING";
	
}else if($convertFrom[1] == 'damage' || $convertFrom[1] == 'broken'){
	$contypestock = "DAMAGE";
}else {
	
}
$res_data = mysqli_query($link1,"SELECT prod_code,to_prod_code,qty FROM stockconvert_data WHERE doc_no='".$doc_no."'");
////// final submit form ////
@extract($_POST);
if($_POST['process'] == "Process"){
	////// make product code and imei array from posted string
	$arr_postmodel = explode(",",base64_decode(urldecode($prod_str)));
	$arr_postimei = explode(",",base64_decode(urldecode($imei_str)));
	if(!empty($arr_postimei)){
		////initialize params
		mysqli_autocommit($link1, false);
		$flag = true;
		$error_msg = array();
		$upd_cnt = 1;
		for($i = 0; $i < count($arr_postimei); $i++){
			/// check imei is in stock or not
			$res_imei = mysqli_query($link1,"SELECT owner_code, prod_code, stock_type,import_date FROM billing_imei_data WHERE imei1='".$arr_postimei[$i]."' ORDER BY id DESC");
			$row_imei = mysqli_fetch_assoc($res_imei);
			///// check owner code should same for having stock
			if($row_imei['owner_code'] == $doc_loc){
				////// check product code should match with stock imei product code
				if($row_imei['prod_code'] == $arr_postmodel[$i]){
					//// check stock type should be matched
					if($row_imei['stock_type']==$typestock){
						/////////get to partcode serial
						$topartcode = mysqli_fetch_assoc(mysqli_query($link1,"SELECT to_prod_code FROM stockconvert_data WHERE doc_no='".$doc_no."' AND prod_code='".$arr_postmodel[$i]."'"));
						$result1 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$doc_loc."',to_location='".$doc_loc."',owner_code='".$doc_loc."',prod_code='".$topartcode["to_prod_code"]."',doc_no='".$doc_no."',imei1='".$arr_postimei[$i]."',stock_type='".$contypestock."',transaction_date='".$doc_date."',import_date='".$row_imei['import_date']."'");
						//// check if query is not executed
						if (!$result1) {
							$flag = false;
							$error_msg[] = "Error Code1: ". mysqli_error($link1);
							$upd_cnt *= 0;
						}else{
							////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
							if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$arr_postimei[$i]."'"))>0){
								$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$doc_loc."', prod_code='".$topartcode["to_prod_code"]."', rem_qty='1', stock_type='".$contypestock."', ref_no='".$doc_no."', ref_date='".$doc_date."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$arr_postimei[$i]."'");
								if (!$res_upd_ss) {
									$flag1 = false;
									$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
									$msg = "2";
								}
							}else{
								$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$doc_loc."', prod_code='".$topartcode["to_prod_code"]."', serial_no='".$arr_postimei[$i]."',inside_qty='1', rem_qty='1', stock_type='".$contypestock."', ref_no='".$doc_no."', ref_date='".$doc_date."',import_date='".$row_imei['import_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
								if (!$res_inst_ss) {
									$flag1 = false;
									$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
									$msg = "2";
								}
							}
							////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
							$upd_cnt *= 1;
						}
						///// update in billing model data
					}else{
						$flag = false;
						$error_msg[] = $arr_postimei[$i]." Stock type is mismatched";
						$upd_cnt *= 0;
					}
				}else{
					$flag = false;
					$error_msg[] = $arr_postimei[$i]." product code is mismatched";
					$upd_cnt *= 0;
				}
			}else{
				$flag = false;
				$error_msg[] = $arr_postimei[$i]." Serial is not in stock";
				$upd_cnt *= 0;
			}
		}////// close for loop
		////// if all scanned Serials are successfully checked
		if($upd_cnt == 1){
			$res_upd_ser = mysqli_query($link1,"SELECT COUNT(imei1) as nos , prod_code FROM billing_imei_data WHERE doc_no='".$doc_no."' GROUP BY prod_code");
			while($row_upd_ser = mysqli_fetch_assoc($res_upd_ser)){
				////// update in model data
				$result2 = mysqli_query($link1,"UPDATE stockconvert_data SET serial_attach='Y', file_name='FRONT' WHERE doc_no='".$doc_no."' and to_prod_code='".$row_upd_ser["prod_code"]."' and qty = '".$row_upd_ser["nos"]."'");
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$error_msg[] = "Error Code2: ". mysqli_error($link1);
				}
			}
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM stockconvert_data WHERE doc_no='".$doc_no."' AND serial_attach=''"))==0){
				////// update in master data
				$result3 = mysqli_query($link1,"UPDATE stockconvert_master SET serial_attach='Y', file_name='FRONT' WHERE doc_no='".$doc_no."'");
				//// check if query is not executed
				if (!$result3) {
					$flag = false;
					$error_msg[] = "Error Code3: ". mysqli_error($link1);
				}
			}
		}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$doc_no,"Serial ATTACH","SCAN",$ip,$link1,$flag);
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Serials are successfully attached with ref. no. ".$doc_no;
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed ".implode(" -> ",$error_msg).". Please try again.";
		} 
		mysqli_close($link1);
		///// move to parent page
		header("location:stockconvert_list.php?msg=".$msg."".$pagenav);
		exit;
	}else{
		$msg = "No Serial scanned.Request could not be processed.";
		///// move to parent page
		header("location:stockconvert_list.php?msg=".$msg."".$pagenav);
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
        					<div class="panel-heading heading1">Stock Conversion Information</div>
         					<div class="panel-body">
          						<table class="table table-bordered" width="100%">
            						<tbody>
                                    	<tr>
                                            <td width="20%"><label class="control-label">Location Name</label></td>
                                            <td width="30%"><?php echo str_replace("~", ",", getLocationDetails($doc_loc, "name,city,state", $link1)); ?></td>
                                            <td width="20%"><label class="control-label">Conversion Type</label></td>
                                            <td width="30%"><?php echo $conv_type;?></td>
                                            
                                        </tr>
              							<tr>
                							<td width="20%"><label class="control-label">Document No.</label></td>
                							<td width="30%"><?php echo $doc_no;?></td>
                                            <td width="20%"><label class="control-label">Document Date</label></td>
                                            <td width="30%"><?php echo $doc_date;?></td>
              							</tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered" width="100%">
                                    <thead>
                                      <tr class="btn-primary">
                                        <th style="text-align:center" width="5%">S.No.</th>
                                        <th style="text-align:center" width="25%">Product</th>
                                        <th style="text-align:center" width="10%">Bill Qty</th>
                                        <th style="text-align:center" width="10%">Scanned Qty</th>                
                                        <th style="text-align:center" width="50%">Scanned Serial Nos.</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
										$arr_model = array();
										//$arr_qty = array();
										$i=1;
										while($row_data = mysqli_fetch_assoc($res_data)){
											$proddet=explode("~",getProductDetails($row_data['prod_code'],"productname,productcolor",$link1));
											$arr_model[$row_data['prod_code']] = $proddet[0];
											//$arr_qty[$row_data['prod_code']] = $row_data["qty"];
											$upd_serial = mysqli_fetch_assoc(mysqli_query($link1,"SELECT GROUP_CONCAT(imei1) as serialstr, COUNT(id) as nos FROM billing_imei_data WHERE doc_no='".$doc_no."' AND prod_code = '".$row_data['prod_code']."' GROUP BY prod_code"));
											//$cnt_serial = explode(",",$upd_serial["serialstr"]);
										?>
                                        <tr>
                                        	<td><?=$i?></td>
                							<td><?=$proddet[0]." (".$proddet[1].") ".$row_data['prod_code']?></td>
                							<td><?=$row_data["qty"]?><input name="<?=$row_data['prod_code']?>" id="<?=$row_data['prod_code']?>" value="<?=$row_data["qty"]?>" type="hidden"/></td>
                                            <td><?=$upd_serial["nos"];?></td>
                                            <td><?=$upd_serial["serialstr"];?></td>
                                        </tr>
                                        <?php $i++;}?>
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
                                    </select>&nbsp;
                                    </div>
                                    <div style="float:left; display:inline-block"><input type="text" id="search" class="form-control required alphanumeric" placeholder="enter<?=$imeitag?>here"/></div>
                                    <div style="float:left; display:inline-block">&nbsp;&nbsp;<button id="sbt" class="btn <?=$btncolor?>" onClick="enterIMEI();" type="button">Submit</button></div>
                                    </form>
                                </div>
                                <div style="float:left; display:inline-block;  width:50%; text-align:left">
                                <form name="frm2" id="frm2" class="form-horizontal" action="" method="post">
                                	<p><label class="control-label">Selected<?=$imeitag?>:</label></p>
                                    <ul id="output"></ul>
                                    <button class="btn <?=$btncolor?>" onClick="checkIMEI()" type="button">Validate</button>
                                    <input name="prod_str" id="prod_str" value="" type="hidden"/>
                                    <input name="imei_str" id="imei_str" value="" type="hidden"/>
                                    <input type="submit" name="process" id="process" value="Process" class="btn <?=$btncolor?>" disabled/>
                                    <input title="Back" type="button" style="float:right" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='stockconvert_list.php?<?=$pagenav?>'">
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
		  .text(imei+" -> "+prodcode)
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
	//var selmodel = document.getElementById("modelcode").value;
	for(var i=0; i<cnt; i++){
		var str = $("#"+i).text();
		var splitstr = str.split(" -> ");
		var trimStr = $.trim(splitstr[0]);
		var trimProd = $.trim(splitstr[1]);
		///// make imei string
		if(imei_str!=""){
			imei_str += ",".concat(trimStr);
			prod_str += ",".concat(trimProd);
		}else{
			imei_str += trimStr;
			prod_str += trimProd;
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
			data:{scanserialstockconvert:trimStr, reqowner:'<?=$doc_loc?>', serialtype:'<?=$typestock?>', reqmodel:trimProd, indx:i},
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
		//alert(allOk+" Checking IMEI validation");
		if(allOk == 1){
			/////// encode imei string and assign it in form field to post data
			var imeitoken = encodeURIComponent(window.btoa(imei_str));
			var prodtoken = encodeURIComponent(window.btoa(prod_str));
			document.getElementById("imei_str").value = imeitoken;
			document.getElementById("prod_str").value = prodtoken;
			///////
			document.getElementById("error_msg").style.display = "none";
			document.getElementById("error_msg").innerHTML = "";
			document.getElementById("process").disabled = false;
			
		}else{
			document.getElementById("error_msg").style.display = "";
			document.getElementById("error_msg").innerHTML = "Serial validation failed";
			document.getElementById("process").disabled = true;
		}
	}else{
		document.getElementById("error_msg").style.display = "";
		document.getElementById("error_msg").innerHTML = "No. of Serial's are mismatched with product qty";
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
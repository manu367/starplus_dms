<?php
session_start(); 
if($_REQUEST['reset']=="1"){
	$_SESSION['snos']="";
	//header("location:partySaleRegistration.php?usercode=".$_REQUEST["usercode"]."&sno=&latitude=".$_REQUEST['latitude']."&longitude=".$_REQUEST['longitude']."&trackaddress=".$_REQUEST['trackaddress']);
	//exit;
}
if($_SESSION['snos']!=''){
	$_SESSION['snos']=$_SESSION['snos'].",".$_REQUEST['sno'];
}else{
	
	$_SESSION['snos']=$_REQUEST['sno'];
}
//echo $_SESSION['snos'];
//print_r($_REQUEST);
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
////// we hit save button
if($_POST){
	if($_POST['Submit']=="Register"){
		@extract($_POST);
		$chkflg = 0;
		$reward_points = 0;
 		$not_process = array();
		$already_reg = array();
		$serial_arr = $_POST['serial_no'];
		$filename = "fileupload0";
		$fname = $_FILES[$filename]["name"];
		if($fname){
			///check directory
			$dirct = "salereg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			if($fname){
				$path = $dirct."/" . $fname;
				move_uploaded_file($_FILES[$filename]["tmp_name"], $path);
			}else{
				$path = "";
			}
		}
		///// get location code user
		$location_code = getAnyDetails($_POST['usercode'],"owner_code","username","admin_users",$link1);
		$location_info = explode("~",getAnyDetails($location_code,"name,id_type,state","asc_code","asc_master",$link1));
		$tolocation_info = explode("~",getAnyDetails($to_location,"name,id_type,state","asc_code","asc_master",$link1));
		///// Insert in item data by picking each data row one by one
		foreach($serial_arr as $k=>$val)
		{ 
			///// check serial no. is in stock or not
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$serial_arr[$k]."' AND location_code='".$location_code."'"))>0){
				
			}else{
				$already_reg[] = $serial_arr[$k];
			}
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM sale_uploader WHERE from_location ='".$location_code."' AND to_location = '".$to_location."' AND doc_no = '".$invoice_no."' AND serial_no1='".$serial_arr[$k]."'"))==0){
			
			}else{
				$not_process[] = $serial_arr[$k];
			}
		}
		///// Insert in item data by picking each data row one by one
		foreach($serial_arr as $k=>$val)
		{   
	    	// checking row value of serial no. should not be blank
			if($serial_arr[$k]!='') {				
				$chkflg++;
				///// check reward is applicable or not
				$res_rwd_point = mysqli_query($link1,"SELECT reward_point FROM reward_points_master WHERE partcode='".$product_code[$k]."' AND state='".$location_info[2]."' AND id_type='".$location_info[1]."' AND status='A'");
				$row_rwd_point = mysqli_fetch_assoc($res_rwd_point);
				if($row_rwd_point['reward_point']!=0){
					$result_ldg = mysqli_query($link1,"INSERT INTO reward_points_ledger SET partcode='".$product_code[$k]."', location_code='".$location_code."', transaction_no='".$serial_arr[$k]."', transaction_date='".$today."', reward_type ='EARN', cr_reward='".$row_rwd_point['reward_point']."', dr_reward='0', update_by='".$_POST['usercode']."', update_on='".$datetime."', update_ip='".$_SERVER['REMOTE_ADDR']."'");
					$result_rwd = mysqli_query($link1,"UPDATE reward_points_balance SET total_reward = total_reward + '".$row_rwd_point['reward_point']."', lastupdate_by='".$_POST['usercode']."', lastupdate_on='".$datetime."' WHERE location_code='".$location_code."'");
					$reward_points+=$row_rwd_point['reward_point'];
				}
				////// insert in sale upload
				$sql = "INSERT INTO sale_uploader SET sale_type  ='SECONDARY', from_location = '".$location_code."', from_location_name = '".$location_info[0]."', to_location = '".$to_location."', to_location_name = '".$tolocation_info[0]."', prod_code = '".$product_code[$k]."', prod_name = '".$product_name[$k]."', disp_qty='1', serial_no1='".$serial_arr[$k]."', serial_no2 = '', doc_no = '".$invoice_no."', doc_date='".$invoice_date."', status='Dispatched', entry_date = '".$datetime."', entry_by = '".$_POST['usercode']."',entry_rmk='".$remark[0]."'";
				$res1 = mysqli_query($link1,$sql);
				if(!$res1){
					$flag = false;
					$err_msg = "Error 1: ". mysqli_error($link1) . ".";
				}
				//////////////insert in billing imei data////////////////////////
				$res2 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$location_code."',to_location='".$to_location."',owner_code='".$to_location."',prod_code='".$product_code[$k]."',doc_no='".$invoice_no."',imei1='".$serial_arr[$k]."',stock_type='OK',transaction_date='".$invoice_date."',import_date='".$invoice_date."'");
				//// check if query is not executed
				if (!$res2) {
					$flag = false;
					$err_msg = "Error 2:". mysqli_error($link1) . ".";
				}else{
					////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$serial_arr[$k]."'"))>0){
						$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$to_location."', prod_code='".$product_code[$k]."', rem_qty='1', stock_type='OK', ref_no='".$invoice_no."', ref_date='".$invoice_date."', update_by='".$_POST['usercode']."', update_date='".$datetime."' WHERE serial_no='".$serial_arr[$k]."'");
						if (!$res_upd_ss) {
							$flag = false;
							$err_msg = "Error 3.1: " . mysqli_error($link1) . ".";
							$msg = "2";
						}
					}else{
						$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$to_location."', prod_code='".$product_code[$k]."', serial_no='".$serial_arr[$k]."',inside_qty='1', rem_qty='1', stock_type='OK', ref_no='".$invoice_no."', ref_date='".$invoice_date."',import_date='".$invoice_date."', update_by='".$_POST['usercode']."', update_date='".$datetime."'");
						if (!$res_inst_ss) {
							$flag = false;
							$err_msg = "Error 3.2: " . mysqli_error($link1) . ".";
							$msg = "2";
						}
					}
				}
			}// close if loop of checking row value of serial no. should not be blank
		}/// close for loop
		if($already_reg){
			$msg = "Serial no. not in stock . ".implode(",",$already_reg);
			$cflag="danger";
			$cmsg = "Failed";
		}else if($not_process){
			$msg = "Request could not be processed . Serial no. are already billed on same invoice ".implode(",",$not_process);
			$cflag="warning";
			$cmsg = "Warning";
		}else{
			$msg2 = "";
			if($chkflg>0){
				$msg = "Secondary Sale is successfully registered.";
				if($reward_points>0){
					$msg2 = "You have earned ".$reward_points." reward points.";
				}
				$cflag="success";
				$cmsg = "Success";
				////// insert in activity table////
				dailyActivity($_POST['usercode'],$invoice_no,"SECONDARY SALE","UPLOAD",$ip,$link1,"");
			}else{
				$msg = "Request could not be processed. Please register atleast one serial.";
				$cflag="warning";
				$cmsg = "Warning";
			}
		}
		$_SESSION['snos'] = "";
		header("location:partySaleRegistration.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&msg2=".$msg2."&usercode=".$_REQUEST['usercode']."&latitude=".$_REQUEST['latitude']."&longitude=".$_REQUEST['longitude']."&trackaddress=".$_REQUEST['trackaddress']);
    	exit;
 	}
}
///// get location code user
$owner_code = getAnyDetails($_REQUEST['usercode'],"owner_code","username","admin_users",$link1);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.2.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<style type="text/css">
-ms-user-select: contain; /*IE,Edge*/
-webkit-user-select: text; /*Webkit*/
-moz-user-select: text; /*Mozilla*/
user-select: all; /*Global, select all with one click*/
body {
  padding: 20px 0;
}

.container {
  /*border: 3px solid #337ab7;*/
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none; 
  -o-user-select: none;
  user-select: none;
}

.canvas {
  position: absolute;
  top: 0;
  margin-left:-20px;
}
.modal-sm{
  position: relative;
  width: 180px;
  height: 150px;
  margin: 0 auto;
 }
</style>
<link href="../css/sparkle.css" rel="stylesheet">
<script type="text/javascript">
$(document).ready(function(){
	$("#frm2").validate({
		submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
				//spinner.show();		  
				form.submit();
			} else {
				return false;
			}
		}
	});
	$('#invoice_date0').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
/////////// function to get city on the basis of state
function get_citydiv(indx){
	var selectstate= document.getElementById('locationstate['+indx+']').value;
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{statemultiline:selectstate,indxx:indx},
		success:function(data){
			//console.log(indx);
			//console.log(data);
			$('#citydiv'+indx).html(data);
		}
	});
}
/////////// function to get product on the basis of product cat
function get_proddiv(indx){
	var selectpc= document.getElementById('psubcategory['+indx+']').value;
	var pc = selectpc.split("~");
	document.getElementById("product_cat["+indx+"]").value=pc[0];
	document.getElementById("product_catname["+indx+"]").value=pc[1];
	document.getElementById("product_subcat["+indx+"]").value=pc[0];
	document.getElementById("product_subcatname["+indx+"]").value=pc[1];
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{prodmultiline:pc[0],indxx:indx},
		success:function(data){
			$('#prddrop'+indx).html(data);
		}
	});
}
///////////////////////////
////// delete product row///////////
function deleteRow(ind){  
  //$("#addr"+(indx)).html(''); 
     var id="addr"+ind; 
     var itemid="serial_"+ind+"";
	 var qtyid="product_code["+ind+"]";
	 var rateid="product_name["+ind+"]";
	 var totalid="model_code["+ind+"]";
	 var lineTotal="product_cat["+ind+"]";
	 var lineTotalname="product_catname["+ind+"]";
	 var mrpid="product_brand["+ind+"]";
	 var mrpidname="product_brandname["+ind+"]";
	 var holdRateid="product_subcat["+ind+"]";
	 var holdRateidname="product_subcatname["+ind+"]";
	 var deletedser = document.getElementById(itemid).value;
	 //var abl_qtyid="avl_stock"+"["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	document.getElementById(itemid).value="";
	document.getElementById(lineTotal).value="";
	document.getElementById(lineTotalname).value="";
	document.getElementById(qtyid).value="";
	document.getElementById(rateid).value="";
	document.getElementById(totalid).value="";
	document.getElementById(mrpid).value="";
	document.getElementById(mrpidname).value="";
	document.getElementById(holdRateid).value="";
	document.getElementById(holdRateidname).value="";
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{sessionstr:deletedser,indxx:ind},
		success:function(data){
			//alert(data);
		}
	});
}
</script>
<script type="text/javascript">
function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}
function ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function strtolower (str) {
    return (str+'').toLowerCase();
}
function openGiftCard(textmsg){
	 $('#myGiftModal .modal-body #cardmsg').html(textmsg);
	 $('#myGiftModal').modal({
		show: true,
		backdrop:"static"
	});	 
}
</script>
<!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body <?php if($_REQUEST['msg2']!="" && $_REQUEST['chkflag']=="success"){?> onLoad="openGiftCard('<?=$_REQUEST['msg2']?>');"<?php }?>>
	<div class="container-fluid">
  		<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      			<!--<h2 align="center"><i class="fa fa-book"></i> Sale Registration</h2>-->
              <!--  <div align="center"><button title="Reset" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='partySaleRegistration.php?reset=1&usercode=<?=$_REQUEST["usercode"]?>&sno=&latitude=<?=$_REQUEST['latitude']?>&longitude=<?=$_REQUEST['longitude']?>&trackaddress=<?=$_REQUEST['trackaddress']?>'"><i class="fa fa-refresh fa-lg" title="Reset"></i>&nbsp;&nbsp;<span>Reset</span></button></div>-->
      			<div class="form-group" id="page-wrap" style="margin-left:10px;">
                <?php if($_REQUEST['msg']){?>
                    <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']." ".$_REQUEST['msg2']?>.
                    </div>
                    <?php 
                        unset($_POST);
                     }?>
<div class="confettiHerePlease dollarDollarBillYall moreMoneyMoreProblems" id="spark" style="display:none">
  <?php for($i=50; $i>=0; $i--){ ?>
  <div class="dollar-<?=$i?>"></div>
  <?php }?>
  <?php for($j=30; $j>=0; $j--){ ?>
  <div class="coin-<?=$j?>"></div>
  <?php }?>
  <?php for($k=400; $k>=0; $k--){ ?>
  <div class="confetti-<?=$k?>"></div>
  <?php }?>
</div>
        		<form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <div class="form-group" style="padding-top: 8px; padding-bottom: 8px; border:ridge; background-color: aliceblue; margin-right: 0px">
            <?php
			///// get serial nos. string
			$serialarr2 = explode(",",$_SESSION['snos']);
			$serialarr = array_values(array_unique($serialarr2));
			//print_r($serialarr);
			for($s=0; $s<count($serialarr); $s++){
				//// check serial no. is in DB or not
				$res_indb = mysqli_query($link1,"SELECT prod_code,doc_no,transaction_date FROM billing_imei_data WHERE imei1='".$serialarr[$s]."' ORDER BY id DESC");
				if(mysqli_num_rows($res_indb)>0){
					////// check serial stock
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$serialarr[$s]."' AND location_code='".$owner_code."'"))>0){
						$row_indb = mysqli_fetch_assoc($res_indb);
						$partcode = $row_indb['prod_code'];
						/////get product details
						$res_prod = mysqli_query($link1,"SELECT productname,model_name,productcategory,brand,productsubcat FROM product_master WHERE productcode='".$partcode."'");
						$row_prod = mysqli_fetch_assoc($res_prod);
						$explprodcat = explode("~",getAnyDetails($row_prod['productsubcat'],"prod_sub_cat,product_category","psubcatid","product_sub_category",$link1));
						$errmsg = "";
						$prodname = $row_prod['productname'];
						$prodcat = $row_prod['productcategory'];
						$prodcatname = $explprodcat[1];
						$prodsubcat = $row_prod['productsubcat'];
						$prodsubcatname = $explprodcat[0];
						$prodbrand = $row_prod['brand'];
						$prodbrandname = getAnyDetails($row_prod['brand'],"make","id","make_master",$link1);
						$prodmodel = $row_prod['model_name'];
					}else{
						$partcode = "";
						$prodname = "";
						$prodcat = "";
						$prodcatname = "";
						$prodsubcat = "";
						$prodsubcatname = "";
						$prodbrand = "";
						$prodbrandname = "";
						$prodmodel = "";
						$errmsg = "Serial no. not in stock";
					}
				}else{
					/////// check from CRM server
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://crm.eaplworld.com/Serial_API/getValidateSerial_crm.php',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS =>'{"accessKey":"4ecebd9cd9bca77c98c4624019631415a660254ef08401bf3dd7392b563e124b","serialNumber":"'.$serialarr[$s].'","product_id":""}',
					  CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json'
					  ),
					));
					$response = curl_exec($curl);
					curl_close($curl);
					$data = json_decode($response);
					if($data->response->code==200 && $data->response->message->responseStatus==1){
						$prodmodel = $data->response->message->responseData->model_id;
						////// get partcode details
						$curl2 = curl_init();
						curl_setopt_array($curl2, array(
						  CURLOPT_URL => 'https://crm.eaplworld.com/Serial_API/getPartcodeInfoForDMS.php?modelid='.$prodmodel,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'GET',
						));
						
						$response2 = curl_exec($curl2);
						curl_close($curl2);
						$data2 = json_decode($response2);
						//////
						$partcode = $data2->response->message->partcode;
						$prodname = $data->response->message->responseData->model_name;
						$prodcat = $data->response->message->responseData->product_id;
						$prodcatname = $data->response->message->responseData->product;
						$prodsubcat = $data->response->message->responseData->product_id;
						$prodsubcatname = $data->response->message->responseData->product;
						$prodbrand = $data->response->message->responseData->brand_id;
						$prodbrandname = $data->response->message->responseData->brand;
						
					}else{
						$partcode = "";
						$prodname = "";
						$prodcat = "";
						$prodcatname = "";
						$prodsubcat = "";
						$prodsubcatname = "";
						$prodbrand = "";
						$prodbrandname = "";
						$prodmodel = "";
						$errmsg = "Serial no. not found in DB";
					}
				}
			?>
            <div id='addr<?=$s?>'>
            	<div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
                    <div class="col-md-6" id="err_msg<?=$s?>" style="color:#FF0000">
                    	<?php echo $errmsg; ?>
                    </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label">Serial No. <span class="red_small">*</span></label>
                    <div class="col-md-6">
                          <input type="text" name="serial_no[<?=$s?>]" id="serial_<?=$s?>" class="form-control required alphanumeric" required minlength="17" maxlength="17" autocomplete="off" value="<?=$serialarr[$s]?>" readonly/>
                          
                    </div>
    
                </div>
                <?php if($partcode){ ?>
                <div class="col-md-6"><label class="col-md-5 control-label">Product Name<span class="red_small">*</span></label>
                    <div class="col-md-6">
                        <input type="text" name="product_name[<?=$s?>]" id="product_name[<?=$s?>]" class="form-control required" required autocomplete="off" readonly value="<?=$prodname?>"/>
                        <input type="hidden" name="product_code[<?=$s?>]" id="product_code[<?=$s?>]" class="form-control required" required autocomplete="off" readonly value="<?=$partcode?>"/>
                        <input type="hidden" name="product_cat[<?=$s?>]" id="product_cat[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodcat?>"/>
                        <input type="hidden" name="product_catname[<?=$s?>]" id="product_catname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodcatname?>"/>
                        <input type="hidden" name="product_subcat[<?=$s?>]" id="product_subcat[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodsubcat?>"/>
                        <input type="hidden" name="product_subcatname[<?=$s?>]" id="product_subcatname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodsubcatname?>"/>
                        <input type="hidden" name="product_brand[<?=$s?>]" id="product_brand[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodbrand?>"/>
                        <input type="hidden" name="product_brandname[<?=$s?>]" id="product_brandname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodbrandname?>"/>
                    </div>
                </div>
                <div class="col-md-6"><label class="col-md-5 control-label">Model Code <span class="red_small">*</span></label>
                    <div class="col-md-6">
                        <input type="text" name="model_code[<?=$s?>]" id="model_code[<?=$s?>]" class="form-control required" required autocomplete="off" readonly value="<?=$prodmodel?>"/>
                    </div>
                </div>
				<?php }else{?>
                <div class="col-md-6"><label class="col-md-5 control-label">Product Cat<span class="red_small">*</span></label>
                    <div class="col-md-6">
                    	<select name="psubcategory[<?=$s?>]"  id= "psubcategory[<?=$s?>]" class="form-control required" required onChange="get_proddiv(<?=$s?>);">
                               <option value="">--Please Select--</option>
                          		<?php
                              	$pcat=mysqli_query($link1,"SELECT psubcatid,prod_sub_cat FROM product_sub_category WHERE status = '1' ORDER BY prod_sub_cat");
                              	while($row_pcat=mysqli_fetch_array($pcat)){
                              	?>
                              	<option value="<?=$row_pcat['psubcatid']."~".$row_pcat['prod_sub_cat']?>"<?php if($_REQUEST['psubcategory['.$s.']']==$row_pcat['psubcatid']."~".$row_pcat['prod_sub_cat']){ echo "selected";}?>><?=$row_pcat['prod_sub_cat']?></option>
                              	<?php
                              	}
                              	?>
                        </select>
                        <input type="hidden" name="product_name[<?=$s?>]" id="product_name[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodname?>"/>
                        <input type="hidden" name="product_cat[<?=$s?>]" id="product_cat[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodcat?>"/>
                        <input type="hidden" name="product_catname[<?=$s?>]" id="product_catname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodcatname?>"/>
                        <input type="hidden" name="product_subcat[<?=$s?>]" id="product_subcat[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodsubcat?>"/>
                        <input type="hidden" name="product_subcatname[<?=$s?>]" id="product_subcatname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodsubcatname?>"/>
                        <input type="hidden" name="product_brand[<?=$s?>]" id="product_brand[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodbrand?>"/>
                        <input type="hidden" name="product_brandname[<?=$s?>]" id="product_brandname[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodbrandname?>"/>
                        <input type="hidden" name="model_code[<?=$s?>]" id="model_code[<?=$s?>]" class="form-control" autocomplete="off" readonly value="<?=$prodmodel?>"/>
                    </div>
                </div>
                <div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
                    <div class="col-md-6" id="prddrop<?=$s?>">
                        <select  name="product_code<?=$s?>" id="product_code<?=$s?>" class='form-control selectpicker required' required data-live-search="true">
                         
                    	</select>
                    </div>
                </div>
                <?php }?>
                <div class="col-md-6"><label class="col-md-6 control-label"><br/></label>
                	<div class="col-md-6"><i class="fa fa-close fa-lg" onClick="deleteRow('<?=$s?>');"></i>
                    </div>
            	</div>

            </div>
			<br/>  
            <?php }?>
            <div class="col-md-6"><label class="col-md-5 control-label">Invoice No. <span class="red_small">*</span></label>
                <div class="col-md-6">
                    <input type="text" name="invoice_no" id="invoice_no" class="form-control mastername required" required autocomplete="off"/>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Invoice Date <span class="red_small">*</span></label>
                <div class="col-md-6">
                    <input type="text" name="invoice_date" id="invoice_date0" class="form-control required" required autocomplete="off" value="<?=$today?>"/>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Dealer/Party Name</label>
                <div class="col-md-6">
                    <select name="to_location" id="to_location" required class="form-control selectpicker required" data-live-search="true">
                        <option value="" selected="selected">Please Select </option>
                        <?php
                    	$sql_chl = "select asc_code,name , city, state,id_type from asc_master where asc_code IN (select mapped_code from mapped_master where uid='" . $owner_code. "' and status='Y')";
                    
                        $res_chl = mysqli_query($link1, $sql_chl);
                        while ($result_chl = mysqli_fetch_array($res_chl)) {
                            
                            if ($result_chl['id_type'] != 'HO') {
                                ?>
                                <option value="<?= $result_chl['asc_code']?>" <?php if ($result_chl['asc_code'] == $_REQUEST['to_location']) echo "selected"; ?>>
                                    <?= $result_chl['name'] . " | " . $result_chl['city'] . " | " . $result_chl['state'] . " | " . $result_chl['asc_code'] ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Remark</label>
                <div class="col-md-6">
                	<textarea name="remark[0]" class="form-control addressfield" id="remark[0]"></textarea>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Photo</label>
                <div class="col-md-6">
                	<div style="display:inline-block; float:left; width:200px">
                    <input type="file" class="" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept="image/*"/>
                    <input type="text" id="filename0" readonly class="form-control"/>
                    </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                    <input type="button" value="Click" id="fakeBrowse0" onClick="HandleBrowseClick(0);" class="btn btn-warning"/>
                    </div>
                </div>
            </div>	
        </div>
       	<div class="form-group">
            <div class="col-md-12" align="center">
              	<button class="btn btn-success" id="save" type="submit" name="Submit" value="Register" <?php if(isset($_POST['Submit'])){ if($_POST['Submit']=='Register'){?>disabled<?php }}?>><i class="fa fa-floppy-o fa-lg"></i>&nbsp;&nbsp;Register</button>&nbsp;&nbsp;
               <?php /*?> <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='myStockTask.php?&usercode=<?=$uid?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button><?php */?>
              	<input type="hidden" class="form-control" name="usercode" id="usercode" value="<?=$_REQUEST['usercode']?>">
                <input type="hidden" class="form-control" name="latitude" id="latitude" value="<?=$_REQUEST['latitude']?>">
                <input type="hidden" class="form-control" name="longitude" id="longitude" value="<?=$_REQUEST['longitude']?>">
                <input type="hidden" class="form-control" name="trackaddress" id="trackaddress" value="<?=$_REQUEST['trackaddress']?>">
              	<input name="theValue" type="hidden" id="theValue" value="0" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myGiftModal" role="dialog">
	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
        	<div class="modal-body">
          		<div class="container" id="js-container">
              		<canvas class="canvas" id="js-canvas" width="150" height="150"></canvas>
                	<div align="center" id="cardmsg" style="margin-top:50px; margin-left:-35px font-family:Arial, Helvetica, sans-serif"></div>
            	</div>
        	</div>
      	</div>
	</div>
</div>
<script>
(function() {
  
  'use strict';
  
  var isDrawing, lastPoint;
  var container    = document.getElementById('js-container'),
      canvas       = document.getElementById('js-canvas'),
      canvasWidth  = canvas.width,
      canvasHeight = canvas.height,
      ctx          = canvas.getContext('2d'),
      image        = new Image(),
      brush        = new Image();
      
  // base64 Workaround because Same-Origin-Policy
  image.src = 'data:image/png;base64,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';
  image.onload = function() {
    ctx.drawImage(image, 0, 0);
    // Show the form when Image is loaded.
   // document.querySelectorAll('.form')[0].style.visibility = 'visible';
  };
  brush.src = 'data:image/png;base64,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';
  
  canvas.addEventListener('mousedown', handleMouseDown, false);
  canvas.addEventListener('touchstart', handleMouseDown, false);
  canvas.addEventListener('mousemove', handleMouseMove, false);
  canvas.addEventListener('touchmove', handleMouseMove, false);
  canvas.addEventListener('mouseup', handleMouseUp, false);
  canvas.addEventListener('touchend', handleMouseUp, false);
  
  function distanceBetween(point1, point2) {
    return Math.sqrt(Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2));
  }
  
  function angleBetween(point1, point2) {
    return Math.atan2( point2.x - point1.x, point2.y - point1.y );
  }
  
  // Only test every `stride` pixel. `stride`x faster,
  // but might lead to inaccuracy
  function getFilledInPixels(stride) {
    if (!stride || stride < 1) { stride = 1; }
    
    var pixels   = ctx.getImageData(0, 0, canvasWidth, canvasHeight),
        pdata    = pixels.data,
        l        = pdata.length,
        total    = (l / stride),
        count    = 0;
    
    // Iterate over all pixels
    for(var i = count = 0; i < l; i += stride) {
      if (parseInt(pdata[i]) === 0) {
        count++;
      }
    }
    
    return Math.round((count / total) * 100);
  }
  
  function getMouse(e, canvas) {
    var offsetX = 0, offsetY = 0, mx, my;

    if (canvas.offsetParent !== undefined) {
      do {
        offsetX += canvas.offsetLeft;
        offsetY += canvas.offsetTop;
      } while ((canvas = canvas.offsetParent));
    }

    mx = (e.pageX || e.touches[0].clientX) - offsetX;
    my = (e.pageY || e.touches[0].clientY) - offsetY;

    return {x: mx, y: my};
  }
  
  function handlePercentage(filledInPixels) {
    filledInPixels = filledInPixels || 0;
    //console.log(filledInPixels + '%');
    if (filledInPixels > 50) {
	  if(canvas.parentNode){
      	canvas.parentNode.removeChild(canvas);
	  }
	  $('#myGiftModal').modal('hide');
	  $('#spark').css("display","");
	  setTimeout(() => {
		   $('#spark').css("display","none");
		}, 2000);
    }
  }
  
  function handleMouseDown(e) {
    isDrawing = true;
    lastPoint = getMouse(e, canvas);
  }

  function handleMouseMove(e) {
    if (!isDrawing) { return; }
    
    e.preventDefault();

    var currentPoint = getMouse(e, canvas),
        dist = distanceBetween(lastPoint, currentPoint),
        angle = angleBetween(lastPoint, currentPoint),
        x, y;
    
    for (var i = 0; i < dist; i++) {
      x = lastPoint.x + (Math.sin(angle) * i) - 25;
      y = lastPoint.y + (Math.cos(angle) * i) - 25;
      ctx.globalCompositeOperation = 'destination-out';
      ctx.drawImage(brush, x, y);
    }
    
    lastPoint = currentPoint;
    handlePercentage(getFilledInPixels(32));
  }

  function handleMouseUp(e) {
    isDrawing = false;
  }
  
})();
</script>
</body>
</html>
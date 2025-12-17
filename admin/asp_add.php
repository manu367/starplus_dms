<?php
////// Function ID ///////
$fun_id = array("a"=>array(24)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
function isExist($link1, $fy, $ranstr){

	$indb = mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$fy."' and doc_code='".$ranstr."'"));
	if($indb > 0){
		return true;
	}else{
		return false;
	}
}
function generateRandomString($length,$fy,$link1){

	$x = true;
	while($x){
		$ranstr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"), 0, $length);
		$x = isExist($link1, $fy, $ranstr);
	}
	return $ranstr;
}
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
	//////// start
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	///// get 3 charcter random string
	$prefixdocstr = $fy;
	$docstr = generateRandomString(3,$prefixdocstr,$link1);
   	///// first of all we will check document string ///
  	$prefixdocstr = strtoupper($prefixdocstr);
   	if(mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$prefixdocstr."' and doc_code='".$docstr."'"))==0){
   		//// get state code from statemaster
	   $res_state=mysqli_query($link1,"select code from state_master where state='".$locationstate."'")or die("ER1".mysqli_error($link1));
	   $row_state=mysqli_fetch_array($res_state);
	   $statecode=$row_state['code'];
	   /// explode location type value
	   $expld_loctyp=explode("~",$locationtype);
	   ////// count max no. of location in selected state
	   $query_code="select MAX(code_id) as qa from asc_master where state='".$locationstate."' and id_type='".$expld_loctyp[0]."'";
	   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
	   $arr_result2=mysqli_fetch_array($result_code);
	   $code_id=$arr_result2[0];
	   /// make 3 digit padding
	   $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
	   //// make logic of location code
	   $newlocationcode=substr(strtoupper(BRANDNAME),0,2).$expld_loctyp[0].$statecode.$pad;
	   ///// check generated id should not be in system
	   if(mysqli_num_rows(mysqli_query($link1,"SELECT sno from asc_master where uid='".$newlocationcode."'"))==0){
   			// insert all details of location //
   			if($dd_address){ $del_addrs = $dd_address;}else{$del_addrs = $comm_address;}
			///// get TCS % 
			if($tcs_applicable=="" || $tcs_applicable=="N"){ $tcs_per="";$tcs_apl="N";}else{ $tcs_per=$tcs_applicable;$tcs_apl="Y";}
  			$sql="INSERT INTO asc_master set uid='".$newlocationcode."',pwd='".$newlocationcode."',name='".$locationname."',asc_code='".$newlocationcode."',sap_code='".$newlocationcode."',code_id='".$pad."', user_level='".$expld_loctyp[1]."',shop_name='".$locationname."',id_type='".$expld_loctyp[0]."',contact_person='".$contact_person."',landline='".$landline."',email='".$email."',phone='".$phone."',addrs='".$comm_address."',disp_addrs='".$del_addrs."',landmark='".$landmark."',city='".$locationcity."',state='".$locationstate."',pincode='".$pincode."',vat_no='".$tin_no."',pan_no='".$pan_no."',cst_no='".$cst_no."',st_no='".$st_no."',circle='".$circle."',status='Active',login_status='Active',start_date='".$today."',update_date='".$datetime."',remark='".$remark."',proprietor_type='".$proprietor."',tdsper='".$tdsper."',tcs_applicable='".$tcs_apl."',tcs_per='".$tcs_per."',account_holder='".$accountholder."',account_no='".$accountno."',bank_name='".$bankname."',bank_city='".$bankcity."',ifsc_code='".$ifsccode."',gstin_no= '$_POST[gst_no]' ,create_by='".$_SESSION['userid']."', segment='".$segment."',underuser='".implode(",",$underuser)."'";
   			$res1 = mysqli_query($link1,$sql);
		   //// check if query is not executed
			if (!$res1) {
				$flag = false;
				$err_msg = "Error Code1:".mysqli_error($link1);
			}
		   //insert into credit bal////////////////////////////
		   $res2 = mysqli_query($link1,"insert into current_cr_status set parent_code='".$parentid."', asc_code='".$newlocationcode."',cr_abl='0',cr_limit='0',total_cr_limit='0'");
		   //// check if query is not executed
			if (!$res2) {
				$flag = false;
				$err_msg = "Error Code2:".mysqli_error($link1);
			}
		   ///insert into mapping table/////////////////////////
		   $res3 = mysqli_query($link1,"insert into mapped_master set uid='".$parentid."',mapped_code='".$newlocationcode."',status='Y',update_date='".$today."'");
		   //// check if query is not executed
			if (!$res3) {
				$flag = false;
				$err_msg = "Error Code3:".mysqli_error($link1);
			}
		   ///insert into document string table/////////////////////////
		   $invstr="INV/".$prefixdocstr."/".$docstr."/";
		   $stnstr="STN/".$prefixdocstr."/".$docstr."/";
		   $prnstr="PRN/".$prefixdocstr."/".$docstr."/";
		   $srnstr="SRN/".$prefixdocstr."/".$docstr."/";
		   $rcvpaystr="RECP/".$prefixdocstr."/".$docstr."/";
		   $res4 = mysqli_query($link1,"insert into document_counter set location_code='".$newlocationcode."',financial_year='".$prefixdocstr."',doc_code='".$docstr."',inv_str='".$invstr."',stn_str='".$stnstr."',prn_str='".$prnstr."',srn_str='".$srnstr."',rcvpay_str='".$rcvpaystr."',create_on='".$today."'");
		   //// check if query is not executed
			if (!$res4) {
				$flag = false;
				$err_msg = "Error Code4:".mysqli_error($link1);
			}
		   //////////////////////////////////////////////////////////////
		   //// create a user corresponding to this location
		   
			$query_code="select MAX(uid) as qc from admin_users";
			$result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
			$arr_result2=mysqli_fetch_array($result_code);
			$code_id=$arr_result2[0];
			$pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
			$admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$expld_loctyp[0].$statecode.$pad;
			$pwd=$admiCode."@321";
			//// insert in user table
			$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',owner_code='".$newlocationcode."',user_level='".$expld_loctyp[1]."',name= '".$locationname."',utype='8',phone='".$phone."',emailid= '".$email."',create_by='".$_SESSION['userid']."' ,status='active',createdate='".date("Y-m-d H:i:s")."'";
			$res_add=mysqli_query($link1,$usr_add);
			//// check if query is not executed
			if (!$res_add) {
				$flag = false;
				$err_msg = "Error Code5:".mysqli_error($link1);
			}
			//// give auto basic permission ////
			$res6 = mysqli_query($link1,"insert into access_region set uid='".$admiCode."',region='".$circle."',status='Y'");
			//// check if query is not executed
			if (!$res6) {
				$flag = false;
				$err_msg = "Error Code6:".mysqli_error($link1);
			}
			$res7 = mysqli_query($link1,"insert into access_state set uid='".$admiCode."',state='".$locationstate."',status='Y'");
			//// check if query is not executed
			if (!$res7) {
				$flag = false;
				$err_msg = "Error Code7:".mysqli_error($link1);
			}
			$res8 = mysqli_query($link1,"insert into access_role set uid='".$admiCode."',role_id='".$expld_loctyp[0]."',status='Y'");
			//// check if query is not executed
			if (!$res8) {
				$flag = false;
				$err_msg = "Error Code8:".mysqli_error($link1);
			}
			$res9 = mysqli_query($link1,"insert into access_location set uid='".$admiCode."',location_id='".$newlocationcode."',status='Y'");
			//// check if query is not executed
			if (!$res9) {
				$flag = false;
				$err_msg = "Error Code9:".mysqli_error($link1);
			}
			
		   	$res9 = mysqli_query($link1,"insert into access_location set uid='".$_SESSION['userid']."',location_id='".$newlocationcode."',status='Y'");
			//// check if query is not executed
			if (!$res9) {
				$flag = false;
				$err_msg = "Error Code9:".mysqli_error($link1);
			}
		   ////// if location is distributor written by shekhar on 09 july 2025
		  if($expld_loctyp[0]=="DS" || $expld_loctyp[0]=="DL"){
			$res11 = mysqli_query($link1,"INSERT INTO `access_function` (`id`, `uid`, `function_id`, `status`, `updatedate`) VALUES ('', '".$admiCode."', '43', 'Y', ''),('', '".$admiCode."', '2', 'Y', ''),('', '".$admiCode."', '10', 'Y', ''),('', '".$admiCode."', '6', 'Y', ''),('', '".$admiCode."', '130', 'Y', ''),('', '".$admiCode."', '121', 'Y', ''),('', '".$admiCode."', '44', 'Y', ''),('', '".$admiCode."', '154', 'Y', '')");
			//// check if query is not executed
			if (!$res11) {
			  $flag = false;
			  $err_msg = "Error Code11:".mysqli_error($link1);
			}
			$res12 = mysqli_query($link1,"INSERT INTO `mapped_brand` (`id`, `userid`, `brand`, `status`) VALUES ('', '".$admiCode."', '11', 'Y')");
			//// check if query is not executed
			if (!$res12) {
			  $flag = false;
			  $err_msg = "Error Code12:".mysqli_error($link1);
			}
			$res14 = mysqli_query($link1,"INSERT INTO `access_report` (`uid`, `report_id`, `status`) VALUES ('".$admiCode."', '12', 'Y'),('".$admiCode."', '13', 'Y'),('".$admiCode."', '15', 'Y'),('".$admiCode."', '90', 'Y'),('".$admiCode."', '1', 'Y'),('".$admiCode."', '5', 'Y'),('".$admiCode."', '6', 'Y'),('".$admiCode."', '20', 'Y'),('".$admiCode."', '82', 'Y')");
			//// check if query is not executed
			if (!$res14) {
			  $flag = false;
			  $err_msg = "Error Code14:".mysqli_error($link1);
			}
		  }
			////// insert in activity table////
			$flag = dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION","ADD",$ip,$link1,$flag);
			$flag = dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
			//add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022////// SR condition is updated on 02 jan 2023 by shekhar
			/*if($expld_loctyp[0]=="DL" || $expld_loctyp[0]=="DS" || $expld_loctyp[0]=="SR" || $expld_loctyp[0]=="RT"){
				//// pick all users which are having same state rights
				$res_same_state = mysqli_query($link1,"SELECT uid FROM access_state WHERE state = '".$locationstate."' AND status ='Y'");
				while($row_same_state = mysqli_fetch_assoc($res_same_state)){
					/// check user is activated or not
					if(mysqli_num_rows(mysqli_query($link1,"SELECT uid FROM admin_users WHERE username='".$row_same_state["uid"]."' AND utype IN ('3','5','6','7') AND `status` LIKE 'Active'"))>0){
						if(mysqli_num_rows(mysqli_query($link1,"SELECT id_type FROM access_location WHERE uid='".$row_same_state["uid"]."' AND id_type='".$expld_loctyp[0]."' AND status='Y'"))>0){
							$res10 = mysqli_query($link1,"insert into access_location set uid='".$row_same_state["uid"]."',location_id='".$newlocationcode."',state='".$locationstate."',id_type='".$expld_loctyp[0]."',status='Y'");
							//// check if query is not executed
							if (!$res10) {
								$flag = false;
								$err_msg = "Error Code10:".mysqli_error($link1);
							}
						}
					}
				}
			}*/
			//end add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
			////// return message
			//$msg="You have successfully created a new location with ref. no. ".$newlocationcode." and location user id is ".$admiCode;
		   $msg="You have successfully created a new location with ref. no. ".$newlocationcode;
   		}else{
			////// return message
			$flag = false;
			$err_msg="Something went wrong. Please try again.";
   		}
   	}else{
		////// return message
		$err_msg="Something went wrong like document code was already in DB. Please try again.";
		$flag = false;
   	}
	///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		//$msg = "You have successfully created a new location with ref. no. ".$newlocationcode." and location user id is ".$admiCode;
		//$pagenavi="&pid=22&hid=2";
		//$urlvrbl=base64_encode("userid=".$admiCode."&userlevel=7&u_name=".$locationname."".$pagenavi);
		//header("Location:urlfwd.php?msg=".base64_encode($msg)."&pagename=update_permission&urlparameter=".$urlvrbl);
		$msg = "You have successfully created a new location with ref. no. ".$newlocationcode;
		header("Location:asp_details.php?msg=".$msg."".$pagenav);
		exit;
	} else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed " . $err_msg . ". Please try again.";
		///// move to parent page
    	header("Location:asp_details.php?msg=".$msg."".$pagenav);
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
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
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
/*	$('#frm1').on('submit',function(e){
		console.log('ffg');
		
	});*/
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
 /////////// function to get Parent Location on the basis of location Type
 function getParentLocation(){
	  var name=$('#locationtype').val();
	  var stat=$('#locationstate').val();
	  var splitval=name.split("~");
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{loctype:splitval[1],loctypstr:splitval[0],locstate:stat},
		success:function(data){
	    $('#parentdiv').html(data);
	    }
	  });
   
 }
 /////////// function to get city on the basis of state
 function checkDupliDoccode(val){
	  var fyr=$('#prefixdocstr').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{fcyear:fyr,doccode:val},
		success:function(data){
	      //// if string found then alert
		  if(data>0){
			  alert("Duplicate document code");
			  $('#docstr').val('');
		  }
	    }
	  });
   
 }

    $(document).ready(function() {
        $('.multiselect-ui').multiselect({
			includeSelectAllOption: false,
			buttonWidth:"230",
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			maxHeight: 300,
            onChange: function(option, checked) {
                // Get selected options.
                var selectedOptions = $('.multiselect-ui option:selected');
 
                if (selectedOptions.length >= 3) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = $('.multiselect-ui option').filter(function() {
                        return !$(this).is(':selected');
                    });
 
                    nonSelectedOptions.each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    $('.multiselect-ui option').each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            }
        });
    });
 </script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bank"></i> Add New Customer/Location (DMS)</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Region/Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="EAST">EAST</option>
                  <option value="NORTH">NORTH</option>
                  <option value="SOUTH">SOUTH</option>
                  <option value="WEST">WEST</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Segment <span class="red_small">*</span></label>
              <div class="col-md-6">
               <select name="segment" id="segment" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$seg_sql = "SELECT * FROM segment_master WHERE status='A' ORDER BY segment";
					$seg_res = mysqli_query($link1,$seg_sql);
					while($seg_row = mysqli_fetch_array($seg_res)){
					?>
                	<option value="<?=$seg_row['segment']?>"<?php if($_REQUEST['segment']==$seg_row['segment']){ echo "selected";}?>><?php echo $seg_row['segment']?></option>
                	<?php }?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                
                </select>               
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control required" required>
               <option value=''>--Please Select-</option>
               </select>  
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Location Type  <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="locationtype" id="locationtype" class="form-control required" required onChange="return getParentLocation(this.value);">
                  <option value="">--Please Select--</option>
                  <?php
				  
				///// check only one id of HO is in system  
				$checkhoid=mysqli_num_rows(mysqli_query($link1,"select sno from asc_master where id_type='HO' or user_level='1'"));
				if($checkhoid>0){$typelist=" and locationtype!='HO'";}else{$typelist="";}
				if($_SESSION["userid"]=="admin" || $_SESSION['utype']=="1"){
					$type_query="SELECT * FROM location_type where status='A' $typelist and seq_id >  '".$_SESSION['user_level']."' order by seq_id";
				  }else{
				 	$acc_type = getAccessLocationType($_SESSION['userid'],$link1);   
				 	$minsqnc = mysqli_fetch_assoc(mysqli_query($link1,"SELECT MIN(seq_id) as minsq FROM location_type WHERE locationtype IN (".$acc_type.")"));
					$type_query="SELECT * FROM location_type where status='A' $typelist and seq_id >  '".$minsqnc['minsq']."' order by seq_id";
					}
				
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']."~".$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Organization Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="locationname" id="locationname" required class="form-control required">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Person <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="contact_person" type="text" class="form-control required" required id="contact_person">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email </label>
              <div class="col-md-6">
                <input name="email" type="email" class="email form-control" id="email" onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Landline Number </label>
              <div class="col-md-6">
              <input name="landline" type="text" class="form-control" id="landline" maxlength="12" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Landmark </label>
              <div class="col-md-6">
                <input type="text" name="landmark" id="landmark" class="form-control">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="pincode" maxlength="6"  required class="digits form-control" onBlur="return pincodeV(this);" onKeyPress="return onlyNumbers(this.value);" id="pincode">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Service Tax No. </label>
              <div class="col-md-6">
                <input name="st_no" type="text" class="form-control"  id="st_no">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">LST / CST Number</label>
              <div class="col-md-6">
              <input type="text" name="cst_no" class="form-control"  id="cst_no">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">PAN Number <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="pan_no" type="text" class="form-control required" required id="pan_no">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">GST Number</label>
              <div class="col-md-6">
              <input type="text" name="gst_no" id="gst_no" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Proprietor Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="proprietor" class="form-control required" required id="proprietor">
                   <option value="">--Please Select--</option>
                   <option value="OWNED">OWNED</option>
                   <option value="PARTNERSHIP">PARTNERSHIP</option>
                   <option value="NOPAN">NO PAN NUMBER</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">TDS %</label>
              <div class="col-md-6">
              <select name="tdsper" class="form-control" id="tdsper">
                   <option value="">--Please Select--</option>
            
				  <option value="1.00">1%</option>
                   <option value="2.00">2%</option>
                   <option value="10.00">10%</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account Holder Name </label>
              <div class="col-md-6">
                <input name="accountholder" type="text" class="form-control" id="accountholder">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account No. </label>
              <div class="col-md-6">
              <input type="text" name="accountno" id="accountno" class="form-control" >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Name </label>
              <div class="col-md-6">
                <input name="bankname" type="text" class="form-control" id="bankname">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank City </label>
              <div class="col-md-6">
              <input type="text" name="bankcity" id="bankcity" class="form-control" >
              </div>
            </div>
          </div>
		  <!--<div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">CC Day </label>
              <div class="col-md-6">
                <input type="text" name="cc_day" id="cc_day" class="form-control"   value="0" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">CC Limit</label>
              <div class="col-md-6">
              <input type="text" name="cc_limit" id="cc_limit" class="form-control" value="0.00" onKeyPress="return onlyFloat(this.value);">
              </div>
            </div>
          </div>-->
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Billing Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="comm_address" id="comm_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Delivery/Shipping Address </label>
              <div class="col-md-6">
               <textarea name="dd_address" id="dd_address" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">TCS Applicable <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="tcs_applicable" id="tcs_applicable" required class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="N" selected>NO</option>
                    <option value="0.1">0.1%</option>
					 <option value="1">1%</option>
                 </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Parent Location <span class="red_small">*</span></label>
              <div class="col-md-6" id="parentdiv">
                 <select name="parentid" id="parentid" required class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="NONE">NONE</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Remark </label>
              <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">IFSC Code </label>
               <div class="col-md-6">
               <input name="ifsccode" type="text" class="digits form-control" id="ifsccode">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Under User</label>
              <div class="col-md-6">
				  <!--only designations are ASM/RSM/Sales Head-->
                <select name='underuser[]' id='underuser' class="form-control multiselect-ui" multiple="multiple">
                 <?php 
				 $res_underuser = mysqli_query($link1,"SELECT  name, username FROM admin_users WHERE designationid IN ('16', '10', '6','25','9')");	
				 while($row_underuser = mysqli_fetch_assoc($res_underuser)){
				 ?>
                 <option value="<?=$row_underuser["username"]?>"><?=$row_underuser["name"]." | ".$row_underuser["username"]?></option>
                 <?php 
				 } 
				 ?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
               <div class="col-md-6">
               &nbsp;
              </div>
            </div>
          </div>
		  
		  <br><br>
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
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
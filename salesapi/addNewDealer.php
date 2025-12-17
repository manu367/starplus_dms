<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
//$today=date("Y-m-d");
//$todayt=date("Ymd");
//$datetime=date("Y-m-d H:i:s");
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
$lat = $_REQUEST['latitude'];
$long = $_REQUEST['longitude'];
$trackaddrs = $_REQUEST['trackaddress'];
$trackdistc = $_REQUEST['trackdistance'];
if($_POST['Submit']=='Save'){
	if(mysqli_num_rows(mysqli_query($link1,"SELECT sno from asc_master where phone LIKE '".$phone."'"))==0){
	///// get 3 charcter random string
	$prefixdocstr = $fy;
	$docstr = generateRandomString(3,$prefixdocstr,$link1);
   ///// first of all we will check document string ///
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
   	$proprietor="OWNED";
   // insert all details of location //
	   if($expld_loctyp[0]=="DL"){ $sts="Active";}else{$sts="Deactive";}
  $sql="INSERT INTO asc_master set uid='".$newlocationcode."',pwd='".$newlocationcode."',name='".$locationname."',asc_code='".$newlocationcode."',code_id='".$pad."', user_level='".$expld_loctyp[1]."',shop_name='".$locationname."',id_type='".$expld_loctyp[0]."',contact_person='".$contact_person."',landline='".$landline."',email='".$email."',phone='".$phone."',addrs='".$comm_address."',disp_addrs='".$dd_address."',landmark='".$landmark."',city='".$locationcity."',state='".$locationstate."',pincode='".$pincode."',vat_no='".$tin_no."',pan_no='".$pan_no."',cst_no='".$cst_no."',st_no='".$st_no."',circle='".$circle."',status='".$sts."',login_status='".$sts."',start_date='".$today."',update_date='".$datetime."',remark='".$remark."',proprietor_type='".$proprietor."',tdsper='".$tdsper."',account_holder='".$accountholder."',account_no='".$accountno."',bank_name='".$bankname."',bank_city='".$bankcity."',ifsc_code='".$ifsccode."',gstin_no= '".$_POST['gst_no']."' ,create_by='".$_REQUEST['usercode']."' , segment='".$segment."'";

   mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
   $query1 = "INSERT INTO dealer_visit set userid='".$_REQUEST['usercode']."',party_code='".$newlocationcode."', remark='".$remark."',visit_date='".$today."',visit_city='".$comm_address."',dealer_type='New',address='".$_REQUEST['trackaddress']."',latitude='".$_REQUEST['latitude']."',longitude='".$_REQUEST['longitude']."',pjp_id='".$_REQUEST['taskid']."',ip='".$_SERVER['REMOTE_ADDR']."'";
   $result1 = mysqli_query($link1,$query1);
   $result2 = mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['taskid']."'");
   //insert into credit bal////////////////////////////
   if($parentid){
   mysqli_query($link1,"insert into current_cr_status set parent_code='".$parentid."', asc_code='".$newlocationcode."',cr_abl='0',cr_limit='0',total_cr_limit='0'")or die("ER4".mysqli_error($link1));
   ///insert into mapping table/////////////////////////
   mysqli_query($link1,"insert into mapped_master set uid='".$parentid."',mapped_code='".$newlocationcode."',status='Y',update_date='".$today."'")or die("ER5".mysqli_error($link1));
   }
   ///insert into document string table/////////////////////////
   $invstr=$docstr."/".$prefixdocstr."/";
   $stnstr="STN/".$prefixdocstr."/".$docstr."/";
   $prnstr="PRN/".$prefixdocstr."/".$docstr."/";
   $srnstr="SRN/".$prefixdocstr."/".$docstr."/";
   $rcvpaystr="RECP/".$prefixdocstr."/".$docstr."/";
   mysqli_query($link1,"insert into document_counter set location_code='".$newlocationcode."',financial_year='".$prefixdocstr."',doc_code='".$docstr."',inv_str='".$invstr."',stn_str='".$stnstr."',prn_str='".$prnstr."',srn_str='".$srnstr."',rcvpay_str='".$rcvpaystr."',create_on='".$today."'")or die("ER6".mysqli_error($link1));
   //////////////////////////////////////////////////////////////
   //// create a user corresponding to this location
	/*
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
    $admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$expld_loctyp[0].$statecode.$pad;
	$pwd=$admiCode."@321";
	//// insert in user table
	$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',owner_code='".$newlocationcode."',user_level='".$expld_loctyp[1]."',name= '".$locationname."',utype='7',phone='".$phone."',emailid= '".$email."',create_by='".$_REQUEST['usercode']."' ,status='active',createdate='".date("Y-m-d H:i:s")."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	//// give auto basic permission ////
	mysqli_query($link1,"insert into access_region set uid='".$admiCode."',region='".$circle."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_state set uid='".$admiCode."',state='".$locationstate."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_role set uid='".$admiCode."',role_id='".$expld_loctyp[0]."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_location set uid='".$admiCode."',location_id='".$newlocationcode."',status='Y'")or die(mysqli_error($link1));
	*/
	$result = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_REQUEST['usercode']."', task_name='New Dealer', task_action='Create', ref_no='".$newlocationcode."', latitude='".$_REQUEST['latitude']."', longitude='".$_REQUEST['longitude']."', address='".$_REQUEST['trackaddress']."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
   ////// insert in activity table////
	dailyActivity($_REQUEST['usercode'],$newlocationcode,"LOCATION","ADD",$ip,$link1,"");
	//dailyActivity($_REQUEST['usercode'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	//add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	if($expld_loctyp[0]=="DL" || $expld_loctyp[0]=="DS"){
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
	}
	//end add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	////// return message
	//$msg="You have successfully created a new location with ref. no. ".$newlocationcode." and location user id is ".$admiCode;
	$msg="You have successfully created a new location with ref. no. ".$newlocationcode;
	$respheadmsg = "Success";
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
	$respheadmsg = "Failed";
   }
   }else{
	////// return message
	$msg="Something went wrong like document code was already in DB. Please try again.";
	$respheadmsg = "Failed";
   }
   }else{
	////// return message
	$msg="Request could not process. Dealer is already registered with ".$phone." this mobile no.";
	$respheadmsg = "Failed";
   }
   $headerline = "Location Creation";
	///// move to parent page
    //header("Location:asp_details.php?msg=".$msg."".$pagenav);
	//$pagenavi="&pid=22&hid=2";
	//$urlvrbl=base64_encode("userid=".$admiCode."&userlevel=7&u_name=".$locationname."".$pagenavi);
	header("Location:processpage.php?respmsg=".base64_encode($msg)."&respheadmsg=".$respheadmsg."&headerline=".$headerline."&usercode=".$_REQUEST['usercode']."&latitude=".$_REQUEST['latitude']."&longitude=".$_REQUEST['longitude']."&trackaddress=".$_REQUEST['trackaddress']."&taskid=".$_REQUEST['taskid']);
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>	
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<style type="text/css">
-ms-user-select: contain; /*IE,Edge*/
-webkit-user-select: text; /*Webkit*/
-moz-user-select: text; /*Mozilla*/
user-select: all; /*Global, select all with one click*/
</style>
 <script>
	$(document).ready(function(){
        //$("#frm1").validate();
		$("#frm1").validate({
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
	  var ptystate=$('#locationstate').val();
	  var splitval=name.split("~");
   	  $("#parentid").select2({
  		ajax: { 
   			url: "getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchParent: params.term, // search term
					requestFor: "parent",
					loctype: splitval[1],
					locstate:ptystate,
					loctypstr:splitval[0]
    			};
   			},
   			processResults: function (response) {
     			return {
        			results: response
     			};
   			},
   			cache: true

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
  </script>
<body>
<div class="container-fluid">
  <div class="row content">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <h2 align="center"><i class="fa fa-bank"></i> Add New Location</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Region/Circle <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                 <select name="circle" id="circle" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="EAST">EAST</option>
                  <option value="NORTH">NORTH</option>
                  <option value="SOUTH">SOUTH</option>
                  <option value="WEST">WEST</option>
                </select>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Segment <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">State <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                
                </select>               
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">City <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control required" required>
               <option value=''>--Please Select-</option>
               </select>  
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Location Type  <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <select name="locationtype" id="locationtype" class="form-control required" required onChange="return getParentLocation(this.value);">
                  <option value="">--Please Select--</option>
                  <?php
				///// check only one id of HO is in system  
				$checkhoid=mysqli_num_rows(mysqli_query($link1,"select sno from asc_master where id_type='HO' or user_level='1'"));
				if($checkhoid>0){$typelist=" and locationtype!='HO'";}else{$typelist="";}
				$type_query="SELECT * FROM location_type where status='A' $typelist and seq_id =  '5' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($_REQUEST[locationtype]==$br_type[locationtype]."~".$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Organization Name <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <input type="text" name="locationname" id="locationname" required class="form-control required">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Contact Person <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="contact_person" type="text" class="form-control required" required id="contact_person">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Contact Number<span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Email</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="email" type="email" class="email form-control" id="email" onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Landline Number </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input name="landline" type="text" class="form-control" id="landline" maxlength="12" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Area <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="landmark" id="landmark" class="form-control required" required>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Pincode <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="pincode" maxlength="6"  required class="digits form-control" onBlur="return pincodeV(this);" onKeyPress="return onlyNumbers(this.value);" id="pincode">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Service Tax No. </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="st_no" type="text" class="form-control"  id="st_no">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">LST / CST Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="cst_no" class="form-control"  id="cst_no">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">PAN Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="pan_no" type="text" class="form-control" id="pan_no">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">GST Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="gst_no" id="gst_no" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><!--Proprietor Type <span class="red_small">*</span>--></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!--<select name="proprietor" class="form-control required" required id="proprietor">
                   <option value="">--Please Select--</option>
                   <option value="OWNED" selected>OWNED</option>
                   <option value="PARTNERSHIP">PARTNERSHIP</option>
                   <option value="NOPAN">NO PAN NUMBER</option>
                </select>-->
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">TDS %</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Account Holder Name </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="accountholder" type="text" class="form-control" id="accountholder">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Account No. </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="accountno" id="accountno" class="form-control" >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Name </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="bankname" type="text" class="form-control" id="bankname">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank City </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="bankcity" id="bankcity" class="form-control" >
              </div>
            </div>
          </div>
		  <!--<div class="form-group">
           <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">CC Day </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="cc_day" id="cc_day" class="form-control"   value="0" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">CC Limit</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="cc_limit" id="cc_limit" class="form-control" value="0.00" onKeyPress="return onlyFloat(this.value);">
              </div>
            </div>
          </div>-->
          <div class="form-group">
           <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Billing Address <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <textarea name="comm_address" id="comm_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Delivery/Shipping Address</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <textarea name="dd_address" id="dd_address" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><!--Unique Document Code <span class="red_small">*</span><br/><span style="font-size:11px;color:#FF0000">(Please enter a 3 character code)</span>--></label>
              <div class="col-md-3">
                <!--<input type="text" name="prefixdocstr" id="prefixdocstr" class="form-control" style="width:120px;" value="<?=$fy?>" readonly>-->
              </div>
              <div class="col-md-3">
                
              </div><!--<input type="text" name="docstr" id="docstr" class="required form-control" required style="width:95px;text-transform:uppercase" maxlength="3" minlength="3" onKeyUp="onlyCharcter(this.value,'docstr');" onBlur="checkDupliDoccode(this.value);">-->
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Under Distributor</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="parentdiv">
                 <select name="parentid" id="parentid" class="form-control">
                    <option value="">--Please Select--</option>
                    <option value="NONE">NONE</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Remark </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">IFSC Code </label>
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <input name="ifsccode" type="text" class="digits form-control" id="ifsccode">
              </div>
            </div>
          </div>
		  
		  <br><br>
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <?php /*?><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'"><?php */?>
            </div>
          </div>
    </form>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
</body>
</html>
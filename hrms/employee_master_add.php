<?php
require_once("../config/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	////// count max no. of location in selected state
    $query_code="select MAX(temp_no) as tn from hrms_employe_master  ";
    $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2['tn']+1;
	/// make 4 digit padding
    $pad=str_pad($code_id,4,"0",STR_PAD_LEFT);
    /// make logic of location code
    $newemployeecode=substr(strtoupper(BRANDNAME),0,2)."EMP".$pad;
	 
	if(mysqli_num_rows(mysqli_query($link1,"SELECT empid from hrms_employe_master where loginid='".$newemployeecode."' "))==0){
		
		  $sql1 = "INSERT INTO hrms_employe_master SET empname = '".$emp_name."', loginid = '".$newemployeecode."', temp_no = '".$code_id."', password = '".$newemployeecode."', date_of_birth = '".$dob."', aletrnate_email = '".$email1."', alternate_no = '".$phone1."', remark = '".$remark."', address = '".$address."', city = '".$locationcity."', state = '".$locationstate."', circle = '".$circle."', phone = '".$phone."', email = '".$email."', utype = '".$emptype."', createby = '".$_SESSION['userid']."', createdate = '".date("Y-m-d H:i:s")."', status = '".$status."', emp_type = '".$jobtype."' , mapped_loc = '".$location."' , married = '".$married."' , marriage_date = '".$marriage_date."' , contact_person_no = '".$contact_person_no."' , contact_person = '".$contact_person."' , gender= '".$gender."',permanent_address = '".$permanent_address."' ,mother_name = '".$mother_name."' ,father_name = '".$father_name."' , pincode = '".$pincode."'  ,contact_relation = '".$contact_relation."'         ";
		
		
		$res1=mysqli_query($link1,$sql1);	
		/// check if query is execute or not//
		if(!$res1){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
		
		//// create password  ///
		$pwd=$newemployeecode."@321";
		
		///// find utype of Employee ///////
		$ETyp = mysqli_fetch_assoc(mysqli_query($link1, "SELECT refid FROM usertype_master WHERE  ( typename = 'Employee' OR typename = 'employee' ) "));
		if($ETyp['refid']!=""){ $EP = $ETyp['refid']; }else{ $EP = ""; }
		
		$sql2 = "INSERT INTO admin_users SET username = '".$newemployeecode."', password = '".$pwd."', name = '".$emp_name."', utype = '11', phone = '".$phone."', emailid = '".$email."', city = '".$locationcity."', state = '".$locationstate."', status = '".$status."', create_by = '".$_SESSION['userid']."', createdate = '".date("Y-m-d H:i:s")."' , map_emp_loc = '".$location."'  ";
		
		$res2=mysqli_query($link1,$sql2);	
		/// check if query is execute or not//
		if(!$res2){
			$flag = false;
			$err_msg = "Error 2". mysqli_error($link1) . ".";
		}
		
	}else{
		////// return message
		$msg="Something went wrong like employee code was already in DB. Please try again.";
	}
	
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$newemployeecode,"EMPLOYEE","ADD",$ip,$link1,"");
		dailyActivity($_SESSION['userid'],$newemployeecode,"ADMIN USER","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
		$msg="You have successfully created a new employee with ref. no. and employee user id is ".$newemployeecode;
		///// move to parent page
		//header("location:employee_master_list.php?msg=".$msg."&sts=success".$pagenav);
		//exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:employee_master_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script>
 	$(document).ready(function() {
		$('#dob').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			autoclose: true
		});
	});

/*$(document).ready(function() {
		$('#resign_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			autoclose: true
		});
	});*/

$(document).ready(function() {
		$('#marriage_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			autoclose: true
		});
	});
	$(document).ready(function() {
		$('#jd').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			autoclose: true
		});
	});
	function makeSelect(){
	  $('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	  });
	}
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

function checkPWD(val){
  var val;
  var upperCase= new RegExp('[A-Z]');
  var lowerCase= new RegExp('[a-z]');
  var numbers = new RegExp('[0-9]');
 
  if(val.match(upperCase) && val.match(lowerCase) &&  val.match(numbers)){
	  $("#passwordErrorMsg").html("")
  }
  else{
	  $("#passwordErrorMsg").html("Your password must be between 6 and 20 characters. It must contain a mixture of upper and lower case letters, and at least one number or symbol.");
  }
}
//////// Enter Number Only/////////
function onlyNumbers(evt){  
	var e = event || evt; // for trans-browser compatibility
	var charCode = e.which || e.keyCode;  
	if (charCode > 31 && (charCode < 48 || charCode > 57) &&  charCode!=43){
		return false;
	}
	return true;
}
///////Phone No. length////
function phoneN(){
	doc1=document.frm1.phone;
	doc2=document.frm1.phone1;
	if(doc1.value!=''){
	   if((isNaN(doc1.value)) || (doc1.value.length !=10)){
		  alert("Enter Valid Mobile No. Mobile No. must be in 10 digit.");
		  doc1.value='';
		  doc1.focus();
		  doc1.select();
	   }
	}
	if(doc2.value!=''){
	   if((isNaN(doc2.value)) || (doc2.value.length !=10)){
		  alert("Enter Valid Mobile No. Mobile No. must be in 10 digit.");
		  doc2.value='';
		  doc2.focus();
		  doc2.select();
	   }
	}
}

  </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-users"></i> Add Employee</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Employee Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="emp_name" type="text" class="form-control required" required id="emp_name">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Region/Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control selectpicker required" data-live-search="true" required>
                  <option value=""> -- Please Select -- </option>
                  <option value="EAST">EAST</option>
                  <option value="NORTH">NORTH</option>
                  <option value="SOUTH">SOUTH</option>
                  <option value="WEST">WEST</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control selectpicker required" data-live-search="true" required>
                  <option value=''> -- Please Select -- </option>
                
                </select>               
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control selectpicker required" data-live-search="true" required>
               <option value=''> -- Please Select -- </option>
               </select>  
              </div>
            </div>
          </div>
          
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Father Name </label>
              <div class="col-md-6">
                <input name="father_name" type="text" class="form-control" id="father_name">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Mother Name</label>
              <div class="col-md-6">
              	<input name="mother_name" type="text" class="form-control" id="mother_name">
              </div>
            </div>
          </div>
          
          
        		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact  Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              	<input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Alternate Email </label>
              <div class="col-md-6">
                <input name="email1" type="email" class="email form-control" id="email1" onBlur="return checkEmail(this.value,'email1');">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Alternate Mo. Number </label>
              <div class="col-md-6">
              <input name="phone1" type="text" class="digits form-control"  id="phone1"  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>
          
          <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">DOB <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="dob"  id="dob" required>
                        </div>
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Gender<span class="red_small">*</span></label> 
                  <div class="col-md-6 ">
                  <select  name="gender" id="gender" class="form-control required" required	>
                  <option value="">Please Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  </select>	
                      
                        
                  </div>    
              </div>  
          </div>  
           <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Marital Status </label> 
                  <div class="col-md-6 ">
                  <select  name="married" id="married" class="form-control"	>
                  <option value="">Please Select</option>
                  <option value="Married">Married</option>
                  <option value="Unmarried">Unmarried</option>
                  </select>	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Marriage Date </label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 " name="marriage_date"  id="marriage_date" >
                        </div>
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Job Type </label>
              <div class="col-md-6">
                <select name="jobtype" id="jobtype" data-live-search="true" class="form-control selectpicker"  >
                		<option value="" > -- Please Select -- </option>
                        <option value="Full Time" > Full Time</option>
                        <option value="Part Time" > Part Time </option>
                        <option value="Permanent" > Permanent </option>
                        <option value="Contractual" > Contractual</option>
                  </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Employee Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                  <select name="emptype" id="emptype" data-live-search="true" class="form-control selectpicker required" required >
                        <option value="" > -- Please Select -- </option>
                        <option value="EMP" > Employee </option>
                        <option value="MEMP" > Manager </option>
                  </select>
              </div>
            </div>
          </div>
          
          
 
		
       <!--  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account Details <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input name = "account_no"    id= "account_no" class="form-control required" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">IFSC Code <span class="red_small">*</span></label>
              <div class="col-md-6">
                  <input name = "ifsc_code"    id= "ifsc_code" class="form-control required" >
              </div>
            </div>
          </div>  -->
		  		  
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Present Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="address" id="address" class="form-control required addressfield" required  style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Permanent Address <span class="red_small">*</span> </label>
              <div class="col-md-6">
               <textarea name="permanent_address" id="permanent_address" class="form-control required addressfield" required  style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Status <span class="red_small">*</span></label>
              <div class="col-md-6">
                  <select name="status" id="status" data-live-search="true" class="form-control selectpicker required" required >
                        <option value="active" > Active </option>
                        <option value="deactive" > Deactive </option>
                  </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">Mapped Location<span class="red_small">*</span></label>
              <div class="col-md-6">
                  <select name="location" id="location" required class="form-control selectpicker required" data-live-search="true" >
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while ($result_parent = mysqli_fetch_array($res_parent)) {

                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
                                                ?>
                                                <option data-tokens="<?= $party_det['name'] . " | " . $result_parent['uid'] ?>" value="<?= $result_parent['location_id'] ?>" >
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
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact Person</label>
              <div class="col-md-6">
               <input name = "contact_person"  id= "contact_person" class="form-control " >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact No.</label>
              <div class="col-md-6">
               <input name = "contact_person_no"  id= "contact_person_no" class="digits form-control" maxlength="10" >   
              </div>
            </div>
          </div> 
          
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact Relation</label>
              <div class="col-md-6">
               <input name = "contact_relation"  id= "contact_relation" class="form-control " >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode</label>
              <div class="col-md-6">
               <input name = "pincode"  id= "pincode" class="digits form-control" maxlength="6" >    
              </div>
            </div>
          </div> 
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>  
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> </label>
              <div class="col-md-6">
              
                
              </div>
            </div>
          </div>
		  
          <br><br>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
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
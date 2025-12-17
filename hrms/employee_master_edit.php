<?php
require_once("../config/config.php");
$refno = base64_decode($_REQUEST['id']);
$empno = base64_decode($_REQUEST['EmpId']);
$info = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_employe_master WHERE  empid = '".$refno."' "));

$info_family = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM emp_familydetails WHERE  loginid = '".$empno."' "));

$info_salary = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_salary_details WHERE  empid = '".$empno."' "));


////// final submit form ////
@extract($_POST);
if($_POST){
	mysqli_autocommit($link1, false);
	$flag = true;
    if($_POST['Submit1']=='Update'){
		
	 $sql1 = "UPDATE hrms_employe_master SET empname = '".$emp_name."', companyid = '".$comp_name."', departmentid = '".$deprt_name."', designationid = '".$desig_name."', managerid = '".$mngr_name."', joining_date = '".$jd."', date_of_birth = '".$dob."', aletrnate_email = '".$email1."', alternate_no = '".$phone1."', remark = '".$remark."', address = '".$address."', city = '".$locationcity."', state = '".$locationstate."', circle = '".$circle."', phone = '".$phone."', email = '".$email."', utype = '".$emptype."', updateby = '".$_SESSION['userid']."', updatedate = '".date("Y-m-d H:i:s")."', status = '".$status."', emp_type = '".$jobtype."', reliving_date = '".$reliving_date."' , resign_date = '".$resign_date."'  , married ='".$married."' , contact_person_no = '".$contact_person_no."' , contact_person = '".$contact_person."' , gender= '".$gender."',permanent_address = '".$permanent_address."' ,mother_name = '".$mother_name."' ,father_name = '".$father_name."' , pincode = '".$pincode."'  ,contact_relation = '".$contact_relation."'   WHERE empid = '".$refno."' and loginid = '".$empno."' ";
	
	$res1=mysqli_query($link1,$sql1);	
	/// check if query is execute or not//
	if(!$res1){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	
	$sql2 = "UPDATE admin_users SET name = '".$emp_name."', phone = '".$phone."', emailid = '".$email."', city = '".$locationcity."', state = '".$locationstate."', status = '".$status."', updatedate = '".date("Y-m-d H:i:s")."' WHERE username = '".$empno."' ";
	
	$res2=mysqli_query($link1,$sql2);	
	/// check if query is execute or not//
	if(!$res2){
		$flag = false;
		$err_msg = "Error 2". mysqli_error($link1) . ".";
	}
	  ////// return message
		$msg="You have successfully updated general details of Empid ".$empno;
	}
	else if($_POST['Submit2']=='Update'){
		///// Update EMP details if needed
    	 $sql="update hrms_employe_master set bank_accno ='".$bank_accno."', ifsc_code ='".$ifsc_code."'  , bank_name = '".$bank_name."' ,beneficiary = '".$beneficiary."' , bank_branch= '".$bank_branch."' , pan_no = '".$pan_no."' , adhar_no = '".$adhar_no."'   WHERE empid = '".$refid."' and loginid = '".$loccode."' ";
		
    	 mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		////// return message
		$msg="You have successfully updated general details of empid ".$loccode;
	}else if($_POST['Submit3']=='Update'){
		///// Insert in document attach detail by picking each data row one by one
		foreach($degree_name as $k=>$val){
			
		 	$sql_inst = "INSERT INTO hrms_empeducation set loginid ='".$loccode."', degree = '".$degree_name[$k]."' , degree_name = '".$document_name[$k]."', completion_yr = '".$completion[$k]."' , university = '".$board[$k]."' , marks= '".$marks[$k]."' , update_by = '".$_SESSION['userid']."' ";
		
			$res_inst = mysqli_query($link1,$sql_inst);
		}
		
		////// return message
		$msg="You have successfully updated the education details of Empid ".$loccode;
	}
	else if($_POST['Submit4']=='Update'){	
			 ///// Update EMP details if needed
		foreach($relation as $k=>$val){	 
    	 $sql="insert into  emp_familydetails set relation = '".$relation[$k]."' , dob = '".$dob[$k]."' , name = '".$name[$k]."' , adhar_no = '".$adhar_no[$k]."' ,contact_no = '".$contact_no[$k]."' ,address = '".$address[$k]."'   ,update_by = '".$_SESSION['userid']."' , loginid = '".$loccode."' ";		
    	 mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		}
			////// return message
		$msg="You have successfully updated family details of empid ".$loccode;
	}
		else if($_POST['Submit5']=='Update'){
		
	      ///// Update EMP details if needed
		//  $sql1 = "UPDATE hrms_employe_master SET  companyid = '".$comp_name."', departmentid = '".$deprt_name."', designationid = '".$desig_name."', managerid = '".$mngr_name."', joining_date = '".$jd."'  , reliving_date = '".$reliving_date."' , resign_date = '".$resign_date."' ,service_period = '".$service_period."' , fdesignation_on_join = '".$fdesignation_on_join."' , hrdesign_onjoining = '".$hrdesign_onjoining."' , hrgrade_onjoining = '".$hrgrade_onjoining."' , ctcjoining = '".$ctcjoining."' ,current_funcdesignation = '".$current_funcdesignation."' , current_hrdesignation = '".$current_hrdesignation."' , current_hrgrade = '".$current_hrgrade."' , current_ctc = '".$current_ctc."' ,  confirmation_due_on = '".$due_on."' , 	status_of_emp = '".$status_of_emp."' , uan = '".$uan."' , epf_no = '".$epf_no."'  where  loginid = '".$loccode."' ";
		  
		    $sql1 = "UPDATE hrms_employe_master SET  companyid = '".$comp_name."', departmentid = '".$deprt_name."', designationid = '".$desig_name."', managerid = '".$mngr_name."', joining_date = '".$jd."'  , reliving_date = '".$reliving_date."' , resign_date = '".$resign_date."' , uan = '".$uan."' , epf_no = '".$epf_no."'  where  loginid = '".$loccode."' ";
		  		
    	 mysqli_query($link1,$sql1)or die("ER1".mysqli_error($link1));
		 
		 
		  $sql2 = "UPDATE admin_users SET designationid = '".$deprt_name."' WHERE username = '".$loccode."' ";
		
	
	    $res2=mysqli_query($link1,$sql2);
		 
			////// return message
		$msg="You have successfully updated official details of empid ".$loccode;
	}
	else if($_POST['Submit6']=='Update'){
		
	     	 ///// Update EMP details if needed
		foreach($company_name as $k=>$val){	 
    	 $sql="insert into  emp_previousemploymentdet set company_name = '".$company_name[$k]."' , start_date = '".$start_date[$k]."' , end_date = '".$end_date[$k]."' , address = '".$address[$k]."' ,contact_person = '".$contact_person[$k]."' ,email = '".$email[$k]."'  , website = '".$website[$k]."' ,update_by = '".$_SESSION['userid']."' , loginid = '".$loccode."' ";		
    	 mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		}
			////// return message
		$msg="You have successfully updated previous employment details of empid ".$loccode;
	}
	else if($_POST['Submit7']=='Update'){
		
	    ///// Insert in document attach detail by picking each data row one by one
		foreach($document_name as $k=>$val){
			////////////////upload file
			$filename = "fileupload".$k;
			$file_name = $_FILES[$filename]["name"];
			//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
			$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
			//////upload image
			if ($_FILES[$filename]["error"] > 0){
				$code=$_FILES[$filename]["error"];
			}
			else{
				// Rename file
				$newfilename = $loccode."_".$todayt.$now.$file_ext;
				move_uploaded_file($_FILES[$filename]["tmp_name"],"../doc_attach/location/".$newfilename);
				$file="../doc_attach/location/".$newfilename;
				//chmod ($file, 0755);
			}
			$sql_inst = "INSERT INTO document_attachment set ref_no='".$loccode."', ref_type='EMPLOYEE MASTER',document_name='".ucwords($document_name[$k])."', document_path='".$file."', updatedate='".$datetime."'";
			$res_inst = mysqli_query($link1,$sql_inst);
		}
		
		////// return message
		$msg="You have successfully attached the document of Empid ".$loccode;
	}
	else if($_POST['Submit8']=='Update'){
		
		///check empid already exit or not ////////////////////
		 $check = mysqli_query($link1,"select id from hrms_salary_details where empid = '".$loccode."'  ");
		 if(mysqli_num_rows($check) ==0){
	
	      ///// Update EMP details if needed
		  $sql1 = "insert into  hrms_salary_details SET  final_grossamt = '".$grossfinalamt."', gross_deduction = '".$gross_deduction."', gross_earnings = '".$gross_earning."', others_deduction = '".$others_deduction."', deduct_pf = '".$deduct_pf."'  , deduct_esi = '".$deduct_esi."' , deduct_misc = '".$deduct_misc."' ,deduct_absent = '".$deduct_absent."' , deduct_vehcile_loan = '".$deduct_vehcile_loan."' , deduct_latemark = '".$deduct_latemark."' , deduct_installment_advance_taken = '".$deduct_installment_advance_taken."' , deduct_loan = '".$deduct_loan."' ,deduct_advances = '".$deduct_advances."' , others_pay = '".$others_pay."' , medical_exp_payable = '".$medical_exp_payable."' , family_allowance = '".$family_allowance."' ,  special_furn_allowance = '".$special_furn_allowance."' , 	maintainence_allowance = '".$maintainence_allowance."' , special_pay = '".$special_pay."' , dearness_allowances = '".$dearness_allowances."' ,travelling_allowances = '".$travelling_allowances."', medical_reimbursement = '".$medical_reimbursement."', incentive = '".$incentive."', leave_reimbursement = '".$leave_reimbursement."', previous_claims = '".$previous_claims."'  , out_station_lodging = '".$out_station_lodging."' , out_station_travelling_claims = '".$out_station_travelling_claims."' ,performance_bonus = '".$performance_bonus."' , birthday_bonus = '".$birthday_bonus."' , bonus = '".$bonus."' , award = '".$award."' , local_travelling_claims = '".$local_travelling_claims."' ,mobile_expenses = '".$mobile_expenses."' , conveyance_allowances = '".$conveyance_allowances."' , medi_educ_allowances = '".$medi_educ_allowances."' , special_allowances = '".$special_allowances."' ,  hra = '".$hra."' , 	basic_pay = '".$basic_pay."' ,create_by = '".$_SESSION['userid']."' ,  empid = '".$loccode."' ";		
		   mysqli_query($link1,$sql1)or die("ER1".mysqli_error($link1));	
		   ////// return message
		$msg="You have successfully added salary details of empid ".$loccode;	  
		 }else {
			  $sql1 = "update  hrms_salary_details SET  final_grossamt = '".$grossfinalamt."', gross_deduction = '".$gross_deduction."', gross_earnings = '".$gross_earning."', others_deduction = '".$others_deduction."', deduct_pf = '".$deduct_pf."'  , deduct_esi = '".$deduct_esi."' , deduct_misc = '".$deduct_misc."' ,deduct_absent = '".$deduct_absent."' , deduct_vehcile_loan = '".$deduct_vehcile_loan."' , deduct_latemark = '".$deduct_latemark."' , deduct_installment_advance_taken = '".$deduct_installment_advance_taken."' , deduct_loan = '".$deduct_loan."' ,deduct_advances = '".$deduct_advances."' , others_pay = '".$others_pay."' , medical_exp_payable = '".$medical_exp_payable."' , family_allowance = '".$family_allowance."' ,  special_furn_allowance = '".$special_furn_allowance."' , 	maintainence_allowance = '".$maintainence_allowance."' , special_pay = '".$special_pay."' , dearness_allowances = '".$dearness_allowances."' ,travelling_allowances = '".$travelling_allowances."', medical_reimbursement = '".$medical_reimbursement."', incentive = '".$incentive."', leave_reimbursement = '".$leave_reimbursement."', previous_claims = '".$previous_claims."'  , out_station_lodging = '".$out_station_lodging."' , out_station_travelling_claims = '".$out_station_travelling_claims."' ,performance_bonus = '".$performance_bonus."' , birthday_bonus = '".$birthday_bonus."' , bonus = '".$bonus."' , award = '".$award."' , local_travelling_claims = '".$local_travelling_claims."' ,mobile_expenses = '".$mobile_expenses."' , conveyance_allowances = '".$conveyance_allowances."' , medi_educ_allowances = '".$medi_educ_allowances."' , special_allowances = '".$special_allowances."' ,  hra = '".$hra."' , 	basic_pay = '".$basic_pay."' ,update_by = '".$_SESSION['userid']."' where  empid = '".$loccode."' ";		
			 mysqli_query($link1,$sql1)or die("ER1".mysqli_error($link1));	
			 
			 ////// return message
		$msg="You have successfully updated salary details of empid ".$loccode; 
			 
		 }
			
	}
	else{
		////// return message
		$msg="Something went wrong. Please try again.";
	}    
	
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$empno,"EMPLOYEE","UPDATE",$ip,$link1,"");
		dailyActivity($_SESSION['userid'],$empno,"ADMIN USER","UPDATE",$ip,$link1,"");
		
        mysqli_commit($link1);
		
		///// move to parent page
		header("location:employee_master_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
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
		$("#frm2").validate();
		$("#frm3").validate();
		$("#frm4").validate();
		$("#frm5").validate();
		$("#frm6").validate();
		$("#frm7").validate();
    
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
	
	$(document).ready(function() {
		$('#start_date').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
		});
	});
	
	$(document).ready(function() {
		$('#end_date').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
		});
	});
	
	$(document).ready(function() {
		$('#resign_date').datepicker({
			format: "yyyy-mm-dd",
			//startDate: "<?=$todayt?>",
			todayHighlight: true,
			autoclose: true
		});
	});

$(document).ready(function() {
		$('#reliving_date').datepicker({
			format: "yyyy-mm-dd",
			//startDate: "<?=$todayt?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	$(document).ready(function() {
		$('#jd').datepicker({
			format: "yyyy-mm-dd",
			//startDate: "<?=$todayt?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	
	$(document).ready(function() {
		$('#dobemp').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
			//todayHighlight: true,
			//autoclose: true
		});
	});
	
	function makeDate(){	 
     $('#datepicker').datepicker(
       {	  
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
			//todayHighlight: true,
			//autoclose: true
		});	
     }
 
    function makeDatenew(){	 
     $('#datepicker1').datepicker(
       {	  
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
			//todayHighlight: true,
			//autoclose: true
		});	
		
		 $('#datepicker2').datepicker(
          {	  
			format: "yyyy-mm-dd",
			endDate: "<?=$todayt?>",
			//todayHighlight: true,
			//autoclose: true
		});	
     }
 
	
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

/*$(document).ready(function() {
		$('#resign_date').datepicker({
			format: "yyyy-mm-dd",
                        // startDate: "<?=$todayt?>",
			todayHighlight: true,
			autoclose: true
		});
	});

$(document).ready(function() {
		$('#reliving_date').datepicker({
			format: "yyyy-mm-dd",
                      //  startDate: "<?=$todayt?>",
			todayHighlight: true,
			autoclose: true
		});
	});*/
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

 function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}

///// add new row for document attachment
  $(document).ready(function() {
	$("#add_row7").click(function() {		
		var numi = document.getElementById('rowno7');
		var itm = "document_name[" + numi.value+"]";
		var preno=document.getElementById('rowno7').value;
		var num = (document.getElementById("rowno7").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr7_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr7_doc'+num+'"><td width="30%"><div style="display:inline-block;float:right"><input type="text" class="form-control entername required cp" name="document_name['+num+']"  id="document_name['+num+']" value="" style="width:270px;"></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove7('+num+');"></i></div></td><td width="70%"><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable7').append(r);
		}
	});
});
function fun_remove7(con){
	var c = document.getElementById('addr7_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function confirmDel(store){
var where_to= confirm("Are you sure to delete this document?");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $url ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}

///// add new row for degree_name attachment
  $(document).ready(function() {
	$("#add_row3").click(function() {		
		var numi = document.getElementById('rowno3');
		var itm = "degree_name[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td width="10%"><select  class="form-control required " name="degree_name['+num+']"  id="degree_name['+num+']" required><option value="">Please Select</option><option value="10th">10th</option><option value="12th">12th</option><option value="Graduation">Graduation</option><option value="Post Graduation">Post Graduation</option><option value="Diploma">Diploma</option><option value="Others">Others</option></select><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i></div></td><td width="10%"><input type="text" class="form-control entername required cp" name="document_name['+num+']"  id="document_name['+num+']" value=""></td><td width="10%"><input type="text" class="digits form-control" name="completion['+num+']"  id="completion['+num+']"  maxlength="4" ></td><td width="10%"><input type="text" class="number form-control" name="marks['+num+']"  id="marks['+num+']" maxlength="4" ></td><td width="10%"><input type="text" class="form-control" name="board['+num+']"  id="board['+num+']"></td></tr>';
			$('#itemsTable3').append(r);
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}

 

///// add new row for relation attachment
  $(document).ready(function() {
	$("#add_row4").click(function() {	
		
		var numi = document.getElementById('rowno4');
		var itm = "relation[" + numi.value+"]";
		var preno=document.getElementById('rowno4').value;
		var num = (document.getElementById("rowno4").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr1_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr1_doc'+num+'"><td width="10%"><select  class="form-control required " name="relation['+num+']"  id="relation['+num+']" required><option value="">Please Select</option><option value="Spouse">Spouse</option><option value="Son">Son</option><option value="Daughter">Daughter</option><option value="Father">Father</option><option value="Mother">Mother</option><option value="Brother">Brother</option><option value="Sister">Sister</option></select><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove4('+num+');"></i></div></td><td width="10%"><input type="text" class="form-control entername required cp" name="name['+num+']"  id="name['+num+']" value=""></td><td width="10%"><input type="text" class="form-control " id="datepicker" name="dobemp['+num+']"   ><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td><td width="10%"><input type="text" class=" form-control" name="adhar_no['+num+']"  id="adhar_no['+num+']"  ></td><td width="10%"><input type="text" class="digits form-control" name="contact_no['+num+']"  id="contact_no['+num+'] maxlength="10"></td><td width="10%"><input   name="address['+num+']"  id="address['+num+']" type="text"  class="form-control"></td></tr>';
			$('#itemsTable4').append(r);
			makeDate();
		}
	});
});
function fun_remove4(con){
	var c = document.getElementById('addr1_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno4').value = con;
}

///// add new row for document attachment
  $(document).ready(function() {
	$("#add_row6").click(function() {	
		
		var numi = document.getElementById('rowno6');
		var itm = "company_name[" + numi.value+"]";
		var preno=document.getElementById('rowno6').value;
		var num = (document.getElementById("rowno6").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr2_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr2_doc'+num+'"><td width="10%"><input  class="form-control required " name="company_name['+num+']"  id="company_name['+num+']" required><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove6('+num+');"></i></div></td><td width="10%"><input type="text" class=" form-control" name="start_date['+num+']"  id="datepicker1" ><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td><td width="10%"><input type="text" class=" form-control " name="end_date['+num+']"  id="datepicker2" ><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td><td width="10%"><input type="text" class=" form-control" name="address['+num+']"  id="address['+num+']" ></td><td width="10%"><input   name="contact_person['+num+']"  id="contact_person['+num+'] type="text"  class="form-control"></td><td width="10%"><input type="text" class="email form-control" name="email['+num+']"  id="email['+num+']" ></td><td width="10%"><input type="text"  name="website['+num+']"  id="website['+num+']" class="form-control" /></td></tr>';
			$('#itemsTable6').append(r);
			makeDatenew();
		}
	});
});
function fun_remove6(con){
	var c = document.getElementById('addr2_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno6').value = con;
}

////////// function for salary structure //////////////////
function grossearning(){
	
	if(document.getElementById("basic_pay").value){
		var basicpay = document.getElementById("basic_pay").value;
		}else{
		var basicpay = 0.00;
		}
	if(document.getElementById("hra").value){
		var hrapay = document.getElementById("hra").value;
		} else{
		var hrapay = 0.00;
		}	
	if(document.getElementById("medical_exp_payable").value){
		var medicalexp = document.getElementById("medical_exp_payable").value;
		} else{
		var medicalexp = 0.00;
		}	
	if(document.getElementById("conveyance_allowances").value){
		var conveyance = document.getElementById("conveyance_allowances").value;
		} else{
		var conveyance = 0.00;
		}		
	if(document.getElementById("family_allowance").value){
		var familyallowance = document.getElementById("family_allowance").value;
		} else{
		var familyallowance = 0.00;
		}		
	if(document.getElementById("special_furn_allowance").value){
		var furallowance = document.getElementById("special_furn_allowance").value;
		} else{
		var furallowance = 0.00;
		}	
	if(document.getElementById("maintainence_allowance").value){
		var mainallowance = document.getElementById("maintainence_allowance").value;
		} else{
		var mainallowance = 0.00;
		}
	if(document.getElementById("others_pay").value){
		var otherspay = document.getElementById("others_pay").value;
		} else{
		var otherspay = 0.00;
		}		
	if(document.getElementById("special_pay").value){
		var specialpay = document.getElementById("special_pay").value;
		} else{
		var specialpay = 0.00;
		}
	if(document.getElementById("dearness_allowances").value){
		var dearnessallowances = document.getElementById("dearness_allowances").value;
		} else{
		var dearnessallowances = 0.00;
		}
	if(document.getElementById("travelling_allowances").value){
		var travel = document.getElementById("travelling_allowances").value;
		} else{
		var travel = 0.00;
		}
	if(document.getElementById("medical_reimbursement").value){
		var medical = document.getElementById("medical_reimbursement").value;
		} else{
		var medical = 0.00;
		}
	if(document.getElementById("incentive").value){
		var incentivepay = document.getElementById("incentive").value;
		} else{
		var incentivepay = 0.00;
		}	
	if(document.getElementById("leave_reimbursement").value){
		var leaevepay = document.getElementById("leave_reimbursement").value;
		} else{
		var leaevepay = 0.00;
		}
	if(document.getElementById("previous_claims").value){
		var claim = document.getElementById("previous_claims").value;
		} else{
		var claim = 0.00;
		}	
	if(document.getElementById("out_station_lodging").value){
		var lodging = document.getElementById("out_station_lodging").value;
		} else{
		var lodging = 0.00;
		}	
	if(document.getElementById("out_station_travelling_claims").value){
		var travelclaim = document.getElementById("out_station_travelling_claims").value;
		} else{
		var travelclaim = 0.00;
		}	
	if(document.getElementById("performance_bonus").value){
		var performance = document.getElementById("performance_bonus").value;
		} else{
		var performance = 0.00;
		}	
	if(document.getElementById("birthday_bonus").value){
		var birthdaypay = document.getElementById("birthday_bonus").value;
		} else{
		var birthdaypay = 0.00;
		}	
	if(document.getElementById("bonus").value){
		var bonuspay = document.getElementById("bonus").value;
		} else{
		var bonuspay = 0.00;
		}
	if(document.getElementById("award").value){
		var awardpay = document.getElementById("award").value;
		} else{
		var awardpay = 0.00;
		}	
	if(document.getElementById("local_travelling_claims").value){
		var localtravel = document.getElementById("local_travelling_claims").value;
		} else{
		var localtravel = 0.00;
		}		
	if(document.getElementById("mobile_expenses").value){
		var mobile = document.getElementById("mobile_expenses").value;
		} else{
		var mobile = 0.00;
		}	
	
	if(document.getElementById("medi_educ_allowances").value){
		var education = document.getElementById("medi_educ_allowances").value;
		} else{
		var education = 0.00;
		}
	if(document.getElementById("special_allowances").value){
		var special = document.getElementById("special_allowances").value;
		} else{
		var special = 0.00;
		}	
		
	/*if(document.getElementById("deduct_advances").value){
		var advances = document.getElementById("deduct_advances").value;
		} else{
		var advances = 0.00;
		}	
	if(document.getElementById("others_deduction").value){
		var other = document.getElementById("others_deduction").value;
		} else{
		var other = 0.00;
		}	
	if(document.getElementById("deduct_pf").value){
		var pf = document.getElementById("deduct_pf").value;
		} else{
		var pf = 0.00;
		}
	if(document.getElementById("deduct_esi").value){
		var esi = document.getElementById("deduct_esi").value;
		} else{
		var esi = 0.00;
		}
	if(document.getElementById("deduct_misc").value){
		var misc = document.getElementById("deduct_misc").value;
		} else{
		var misc = 0.00;
		}
	if(document.getElementById("deduct_absent").value){
		var absent = document.getElementById("deduct_absent").value;
		} else{
		var absent = 0.00;
		}
	if(document.getElementById("deduct_vehcile_loan").value){
		var vechloan = document.getElementById("deduct_vehcile_loan").value;
		} else{
		var vechloan = 0.00;
		}
	if(document.getElementById("deduct_latemark").value){
		var latemark = document.getElementById("deduct_latemark").value;
		} else{
		var latemark = 0.00;
		}
	if(document.getElementById("deduct_installment_advance_taken").value){
		var installation = document.getElementById("deduct_installment_advance_taken").value;
		} else{
		var installation = 0.00;
		}
		if(document.getElementById("deduct_loan").value){
		var loan = document.getElementById("deduct_loan").value;
		} else{
		var loan = 0.00;
		}	
		*/
			
	var sum  = parseFloat(special)+parseFloat(education)+ parseFloat(basicpay)+parseFloat(hrapay)+parseFloat(conveyance)+parseFloat(mobile)+parseFloat(localtravel)+parseFloat(awardpay)+parseFloat(bonuspay)+parseFloat(birthdaypay)+parseFloat(performance)+parseFloat(travelclaim)+parseFloat(lodging)+parseFloat(claim)+parseFloat(leaevepay)+parseFloat(incentivepay)+parseFloat(medical)+parseFloat(travel)+parseFloat(dearnessallowances)+parseFloat(specialpay)+parseFloat(otherspay)+parseFloat(furallowance)+parseFloat(mainallowance)+parseFloat(familyallowance)+parseFloat(medicalexp);
	document.getElementById("gross_earning").value = sum;
	
	document.getElementById("grossfinalamt").value = parseFloat(document.getElementById("gross_earning").value) - parseFloat(document.getElementById("gross_deduction").value) ;
	
	
		
////////////////////////////////////////////////////////////////////
		
		/*var deduction_sum = parseFloat(vechloan)+parseFloat(installation)+parseFloat(loan)+parseFloat(advances)+parseFloat(latemark)+parseFloat(absent)++parseFloat(misc)+parseFloat(esi)+parseFloat(pf)+parseFloat(pf)+parseFloat(other);
		
		document.getElementById("gross_deduction").value = deduction_sum;*/
		
		
			
		
	}
	
	function grossdeduct(){
		
		if(document.getElementById("deduct_advances").value){
		var advances = document.getElementById("deduct_advances").value;
		} else{
		var advances = 0.00;
		}	
	if(document.getElementById("others_deduction").value){
		var other = document.getElementById("others_deduction").value;
		} else{
		var other = 0.00;
		}	
	if(document.getElementById("deduct_pf").value){
		var pf = document.getElementById("deduct_pf").value;
		} else{
		var pf = 0.00;
		}
	if(document.getElementById("deduct_esi").value){
		var esi = document.getElementById("deduct_esi").value;
		} else{
		var esi = 0.00;
		}
	if(document.getElementById("deduct_misc").value){
		var misc = document.getElementById("deduct_misc").value;
		} else{
		var misc = 0.00;
		}
	if(document.getElementById("deduct_absent").value){
		var absent = document.getElementById("deduct_absent").value;
		} else{
		var absent = 0.00;
		}
	if(document.getElementById("deduct_vehcile_loan").value){
		var vechloan = document.getElementById("deduct_vehcile_loan").value;
		} else{
		var vechloan = 0.00;
		}
	if(document.getElementById("deduct_latemark").value){
		var latemark = document.getElementById("deduct_latemark").value;
		} else{
		var latemark = 0.00;
		}
	if(document.getElementById("deduct_installment_advance_taken").value){
		var installation = document.getElementById("deduct_installment_advance_taken").value;
		} else{
		var installation = 0.00;
		}
		if(document.getElementById("deduct_loan").value){
		var loan = document.getElementById("deduct_loan").value;
		} else{
		var loan = 0.00;
		}	
		
		
		var deduction_sum = parseFloat(vechloan)+parseFloat(installation)+parseFloat(loan)+parseFloat(advances)+parseFloat(latemark)+parseFloat(absent)+parseFloat(misc)+parseFloat(esi)+parseFloat(pf)+parseFloat(other);
		
		document.getElementById("gross_deduction").value = deduction_sum;
		
		document.getElementById("grossfinalamt").value = parseFloat(document.getElementById("gross_earning").value) - parseFloat(document.getElementById("gross_deduction").value) ;
		
		
		
		}

  </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
     <h2 align="center"><i class="fa fa-bank"></i> Edit Employee Details</h2>
      <h4 align="center">
          <?=$info['empname']."  (".$info['loginid'].")";?>
          <?php 
		  if(isset($_POST['Submit1']) || isset($_POST['Submit2']) || isset($_POST['Submit3']) || isset($_POST['Submit4']) || isset($_POST['Submit5']) || isset($_POST['Submit6']) || isset($_POST['Submit7']) || isset($_POST['Submit8'])){
				if($_POST['Submit1']=='Update' || $_POST['Submit2']=='Update' || $_POST['Submit3']=='Update' || $_POST['Submit4']=='Update' || $_POST['Submit5']=='Update' || $_POST['Submit6']=='Update' || $_POST['Submit7']=='Update' || $_POST['Submit8']=='Update'){ ?>
          <br/>
          <span style="color:#FF0000"><?php echo $msg; ?></span>
          <?php }
		  }
		   ?>
        </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      	<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-bank"></i>Bank Details</a></li>
            <li><a data-toggle="tab" href="#menu5"><i class="fa fa-bank"></i>Oficial Details</a></li>
            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-users"></i> Family Details</a></li>
			<li><a data-toggle="tab" href="#menu3"><i class="fa fa-puzzle-piece"></i> Educational Details</a></li>
            <li><a data-toggle="tab" href="#menu6"><i class="fa fa-puzzle-piece"></i>Previous Employment</a></li>
            <li><a data-toggle="tab" href="#menu7"><i class="fa fa-puzzle-piece"></i>Document</a></li>
             <li><a data-toggle="tab" href="#menu8"><i class="fa fa-puzzle-piece"></i>Salary Details</a></li>
          </ul>
           <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
           <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
           <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Employee Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" class="form-control required" required name="emp_name" id="emp_name" value="<?=$info['empname'];?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Region/Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control selectpicker required" data-live-search="true" required>
                  <option value="" <?php if($info['circle'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                  <option value="EAST" <?php if($info['circle'] == "EAST"){ echo "selected"; } ?> >EAST</option>
                  <option value="NORTH" <?php if($info['circle'] == "NORTH"){ echo "selected"; } ?> >NORTH</option>
                  <option value="SOUTH" <?php if($info['circle'] == "SOUTH"){ echo "selected"; } ?> >SOUTH</option>
                  <option value="WEST" <?php if($info['circle'] == "WEST"){ echo "selected"; } ?> >WEST</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control selectpicker required" data-live-search="true" required>
                  <option value='<?=$info['state']?>' > <?=$info['state']?> </option>
                
                </select>               
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control selectpicker required" data-live-search="true" required>
               <option value='<?=$info['city']?>'> <?=$info['city']?> </option>
               </select>  
              </div>
            </div>
          </div>
          
         <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Father Name </label>
              <div class="col-md-6">
                <input name="father_name" type="text" class="form-control" id="father_name" value="<?=$info['father_name']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Mother Name</label>
              <div class="col-md-6">
              	<input name="mother_name" type="text" class="form-control" id="mother_name" value="<?=$info['mother_name']?>">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');" value="<?=$info['email']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$info['phone']?>" >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Alternate Email </label>
              <div class="col-md-6">
                <input name="email1" type="email" class="email form-control" id="email1" onBlur="return checkEmail(this.value,'email1');" value="<?=$info['aletrnate_email']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Alternate Mo. Number </label>
              <div class="col-md-6">
              <input name="phone1" type="text" class="digits form-control"  id="phone1"  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$info['alternate_no']?>" >
              </div>
            </div>
          </div>
          
          <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">DOB <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="dob"  id="dob" value="<?=$info['date_of_birth']?>" required>
                        </div>
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Gender</label> 
                  <div class="col-md-6 ">
                         
                            <select name="gender" id="gender" data-live-search="true" class="form-control selectpicker"  >
                		<option value="" <?php if($info['gender'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                        <option value="Male"  <?php if($info['gender'] == "Male"){ echo "selected"; } ?> > Male </option>
                        <option value="Female"  <?php if($info['gender'] == "Female"){ echo "selected"; } ?> > Female </option>
                
                  </select>
                       
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Job Type </label>
              <div class="col-md-6">
                <select name="jobtype" id="jobtype" data-live-search="true" class="form-control selectpicker"  >
                		<option value="" <?php if($info['emp_type'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                        <option value="Permanent"  <?php if($info['emp_type'] == "Permanent"){ echo "selected"; } ?> > Permanent </option>
                        <option value="Part Time"  <?php if($info['emp_type'] == "Part Time"){ echo "selected"; } ?> > Part Time </option>
                         <option value="Full Time"  <?php if($info['emp_type'] == "Full Time"){ echo "selected"; } ?> > Full Time</option>
                        <option value="Contractual"  <?php if($info['emp_type'] == "Contractual"){ echo "selected"; } ?> > Contractual </option>
                  </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Employee Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                  <select name="emptype" id="emptype" data-live-search="true" class="form-control selectpicker required" required >
                        <option value="" <?php if($info['utype'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                        <option value="EMP" <?php if($info['utype'] == "EMP"){ echo "selected"; } ?> > Employee </option>
                        <option value="MEMP" <?php if($info['utype'] == "MEMP"){ echo "selected"; } ?> > Manager </option>
                  </select>
              </div>
            </div>
          </div>

		  		  
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Presnt Address <span class="red_small">*</span></label>
              <div class="col-md-6">

                <textarea name="address" id="address" class="form-control required addressfield" required  style="resize:vertical"><?=$info['address']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Permanent Address </label>
              <div class="col-md-6">
              <textarea name="address" id="address" class="form-control required addressfield" required style="resize:vertical"><?=$info['permanent_address']?></textarea>
                
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Status <span class="red_small">*</span></label>
              <div class="col-md-6">
                  <select name="status" id="status" data-live-search="true" class="form-control selectpicker required" required >
                        <option value="active" <?php if($info['status'] == "active"){ echo "selected"; } ?> > Active </option>
                        <option value="deactive" <?php if($info['status'] == "deactive"){ echo "selected"; } ?> > Deactive </option>
                  </select>
              </div>
            </div>
			 <div class="col-md-6"><label class="col-md-6 control-label">Mapped Location</label>
              <div class="col-md-6">
			  <input type="text" name="map_loc" id="map_loc" class="form-control" value="<?=getLocationDetails($info['mapped_loc'],"name",$link1);?>" readonly >
			  </div>
			  </div>
		  </div>
          
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact Person</label>
              <div class="col-md-6">
               <input name = "contact_person"  id= "contact_person" class="form-control " value="<?=$info['contact_person']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact No.</label>
              <div class="col-md-6">
               <input name = "contact_person_no"  id= "contact_person_no" class="digits form-control" maxlength="10" value="<?=$info['contact_person_no']?>" >   
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Emergency Contact Relation</label>
              <div class="col-md-6">
               <input name = "contact_person"  id= "contact_person" class="form-control " value="<?=$info['contact_relation']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode</label>
              <div class="col-md-6">
                <input name = "pincode"  id= "pincode" class="digits form-control" maxlength="6" value="<?=$info['pincode']?>">   
              </div>
            </div>
          </div> 
          
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"><?=$info['remark']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Marrital Status</label>
              <div class="col-md-6">
               <select  name="married" id="married" class="form-control"	>
                  <option value="">Please Select</option>
                  <option value="Married" <?php if($info['married'] == 'Married'){echo "selected";}?>>Married</option>
                  <option value="Unmarried" <?php if($info['married'] == 'Unmarried'){echo "selected";}?>>Unmarried</option>
                  </select>	
                
              </div>
            </div>
          </div>
		  

       
          
          
          
		  
          <br><br>
          
          <div class="form-group">
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit1" id="save1" value="Update" title="" <?php if($_POST['Submit1']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
    </form>
      </div>
            <div id="menu2" class="tab-pane fade"> <br/>
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
               <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">PAN No.</label>
                      <div class="col-md-6">
                         <input type="text" name="pan_no" class="form-control" id="pan_no" value="<?php if(!empty($info['pan_no'])){ echo $info['pan_no'];}?>"/>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Aadhar No.<span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input type="text" name="adhar_no" id="adhar_no" class="form-control required"  required value="<?php if(!empty($info['adhar_no'])){ echo $info['adhar_no'];}?>" maxlength="30"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Beneficiary Name</label>
                      <div class="col-md-6">
                         <input type="text" name="beneficiary" class="form-control" id="beneficiary" value="<?php if(!empty($info['pan_no'])){ echo $info['beneficiary'];}?>"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Bank Branch</label>
                      <div class="col-md-6">
                        <input type="text" name="bank_branch" id="bank_branch" class="form-control" value="<?php if(!empty($info['bank_branch'])){ echo $info['bank_branch'];}?>" />
                      </div>
                    </div>
                  </div>
                   
              	<div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Bank Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <input type="text" name="bank_name" class="form-control required"  required id="bank_name" value="<?php if(!empty($info['bank_name'])){ echo $info['bank_name'];}?>"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Bank Account No.<span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input type="text" name="bank_accno" id="bank_accno" class="form-control required" required value="<?php if(!empty($info['bank_accno'])){ echo $info['bank_accno'];}?>" maxlength="30"/>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">IFSC Code <span class="red_small">*</span></label>
                      <div class="col-md-6">
                       <input type="text" name="ifsc_code" id="ifsc_code" class="form-control required"  required value="<?php if(!empty($info['ifsc_code'])){ echo $info['ifsc_code'];}?>" maxlength="15"/>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit2" id="save2" value="Update" title="" <?php if($_POST['Submit2']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
            </div>
            <div id="menu3" class="tab-pane fade"><br/>
            	<form  name="frm3" id="frm3" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable3">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                                <th width="10%">Degree Name</th>
                            	<th width="10%">Name</th>
                            	<th width="10%">Year of Completion</th>
                                <th width="10%">Marks</th>
                                <th width="10%">Board/University</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr_doc0">
                               <td><select  class="form-control  required " name="degree_name[0]"  id="degree_name[0]" required>
                                <option value="">Please Select</option>
                                <option value="10th">10th</option>
                                <option value="12th">12th</option>
                                <option value="Graduation">Graduation</option>
                                <option value="Post Graduation">Post Graduation</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Others">Others</option>
                               </select></td>
                        		<td><input type="text" class="form-control entername  cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                                <td><input type="text" class="digits form-control" name="completion[0]"  id="completion[0]" maxlength="4" ></td>
                            	<td><input type="text" class="number form-control" name="marks[0]"  id="marks[0]" maxlength="4"></td>
                                <td><input type="text" class="form-control" name="board[0]"  id="board[0]" ></td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                <div class="form-group">
           			<div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add </a><input type="hidden" name="rowno3" id="rowno3" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit3" id="save3" value="Update" title="" <?php if($_POST['Submit3']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
            		</div>
          		</div>
              	</form>
                <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;" colspan="4">Highest Qualification</th>                
                </tr>
            </thead>
            <tbody>
              <?php
			  	  $res4 = mysqli_query($link1,"select * from hrms_empeducation where loginid='".$info['loginid']."' order by id desc");
                  $pro_desc_img=  mysqli_fetch_assoc($res4)
                     
                 
              ?>              
              <tr> 
              <td class="col-md-2" align="left"><?=$pro_desc_img['degree'];?></td>                                   
                          
              </tr>
                  <?php  ?>
            </tbody>
          </table>
            </form>
            </div>
            <div id="menu4" class="tab-pane fade"> <br/>
              <form  name="frm4" id="frm4" class="form-horizontal" action="" method="post">
              	<div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable4">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                                <th width="10%">Relation</th>
                            	<th width="10%">Name</th>
                            	<th width="10%">DOB</th>
                                <th width="10%">Adhar No.</th>
                                <th width="10%">Contatct No.</th>
                                <th width="10%">Address</th>
                              
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr1_doc0">
                               <td><select  class="form-control  required " name="relation[0]"  id="relation[0]" required>
                                <option value="">Please Select</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Son">Son</option>
                                <option value="Daughter">Daughter</option>
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Brother">Brother</option>
                                <option value="Sister">Sister</option>
                               </select></td>
                        		<td><input type="text" class="form-control entername  cp" name="name[0]"  id="name[0]" value=""></td>
                                <td><div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="dobemp[0]"  id="dobemp"  style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
                            	<td><input type="text" class="form-control" name="adhar_no[0]"  id="adhar_no[0]" maxlength="15"></td>
                                <td><input type="text" class="digits form-control" name="contact_no[0]"  id="contact_no[0]" maxlength="10"></td>
                                 <td><input type="text" class="form-control" name="address[0]"  id="address[0]" ></td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                   <div class="form-group">
                   <div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row4" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Family Member </a><input type="hidden" name="rowno4" id="rowno4" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit4" id="save4" value="Update" title="" <?php if($_POST['Submit4']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
               <form id="frm21" name="frm21" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;">Relation</th> 
                  <th style="font-size:13px;">Name</th>     
                  <th style="font-size:13px;">DOB</th> 
                  <th style="font-size:13px;">Adhar No.</th>
                  <th style="font-size:13px;">Contact No.</th> 
                  <th style="font-size:13px;">Address</th>         
                </tr>
            </thead>
            <tbody>
              <?php
			  	 $res_emp = mysqli_query($link1,"select * from emp_familydetails where loginid='".$info['loginid']."' order by id desc");
                 while( $res_empdet=  mysqli_fetch_assoc($res_emp)){
                     
                 
              ?>              
              <tr> 
              <td  align="left"><?=$res_empdet['relation'];?></td> 
              <td  align="left"><?=$res_empdet['name'];?></td> 
              <td  align="left"><?=$res_empdet['dob'];?></td>   
              <td  align="left"><?=$res_empdet['adhar_no'];?></td>   
              <td  align="left"><?=$res_empdet['contact_no'];?></td>                                   
              <td  align="left"><?=$res_empdet['address'];?></td>           
              </tr>
                  <?php } ?>
            </tbody>
          </table>
            </form>
            </div>
            
            <div id="menu5" class="tab-pane fade"> <br/>
              <form  name="frm5" id="frm5" class="form-horizontal" action="" method="post">
              	<div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Company Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <select id="comp_name" name="comp_name" required data-live-search="true" class="form-control selectpicker required" >
               		<option value="" <?php if($info['companyid'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                    <?php
						$qr3 = mysqli_query($link1, "SELECT companyid,cname FROM hrms_company_master WHERE  status = '1'  ORDER BY cname ");
						while($row3 = mysqli_fetch_array($qr3)){
					?>
                    <option value="<?=$row3[0]?>" <?php if($info['companyid'] == $row3[0]){ echo "selected"; } ?> > <?=$row3[1]?> </option>
                    <?php } ?>
               </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Department Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <select id="deprt_name" name="deprt_name" required data-live-search="true" class="form-control selectpicker required" >
               		<option value="" <?php if($info['departmentid'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                    <?php
						$qr1 = mysqli_query($link1, "SELECT departmentid,dname FROM hrms_department_master WHERE  status = '1'  ORDER BY dname ");
						while($row1 = mysqli_fetch_array($qr1)){
					?>
                    <option value="<?=$row1[0]?>" <?php if($info['departmentid'] == $row1[0]){ echo "selected"; } ?> > <?=$row1[1]?> </option>
                    <?php } ?>
               </select>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Designation <span class="red_small">*</span></label>
              <div class="col-md-6">
               <select id="desig_name" name="desig_name" required data-live-search="true" class="form-control selectpicker required" >
               		<option value="" <?php if($info['designationid'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                    <?php
						$qr2 = mysqli_query($link1, "SELECT designationid,designame FROM hrms_designation_master WHERE  status = '1'  ORDER BY designame ");
						while($row2 = mysqli_fetch_array($qr2)){
					?>
                    <option value="<?=$row2[0]?>" <?php if($info['designationid'] == $row2[0]){ echo "selected"; } ?> > <?=$row2[1]?> </option>
                    <?php } ?>
               </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Reporting Manager <span class="red_small">*</span> </label>
              <div class="col-md-6">
               <select id="mngr_name" name="mngr_name" data-live-search="true" class="form-control selectpicker" required >
               		<option value="" <?php if($info['managerid'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                    <?php
						$qr4 = mysqli_query($link1, "SELECT loginid,empname FROM hrms_employe_master WHERE  status = 'active' and utype = 'MEMP'  ORDER BY empname ");
						while($row4 = mysqli_fetch_array($qr4)){
					?>
                    <option value="<?=$row4[0]?>" <?php if($info['managerid'] == $row4[0]){ echo "selected"; } ?> > <?=$row4[1]?> </option>
                    <?php } ?>
               		</select>
             		 </div>
           		   </div>            
         	      </div>
                  <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label"> </label> 
                  <div class="col-md-6 input-append date">
                  		
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Joining Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="jd"  id="jd" value="<?php if($info['joining_date'] != '0000-00-00'){echo $info['joining_date'];} ?>" required>
                        </div>
                  </div>    
              </div>  
          </div>  
          
            <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Date of Resignation</label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 " name="resign_date"  id="resign_date" value="<?php if($info['resign_date'] != '0000-00-00'){echo $info['resign_date'];} ?>"  >
                        </div>
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Relieving Date </label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 " name="reliving_date"  id="reliving_date" value="<?php if($info['reliving_date'] != '0000-00-00'){echo $info['reliving_date'];} ?>" >
                        </div>
                      </div>    
                   </div>  
                  </div> 
             <!-- <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">Service Period </label>
              <div class="col-md-6">
               <input name = "service_period"    id= "service_period"  value="<?=$info['service_period']?>" class="form-control " >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Functional Designation on Joining</label>
              <div class="col-md-6">
                  <input name = "fdesignation_on_join"  id= "fdesignation_on_join" value="<?=$info['fdesignation_on_join']?>"  class="form-control " >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">HR Designtion ON Joining</label>
              <div class="col-md-6">
               <input name = "hrdesign_onjoining"    id= "hrdesign_onjoining" class="form-control" value="<?=$info['hrdesign_onjoining']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">HR Grade on Joining</label>
              <div class="col-md-6">
                  <input name = "hrgrade_onjoining" id= "hrgrade_onjoining" class="form-control" value="<?=$info['hrgrade_onjoining']?>" >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">P/M CTC on Joining</label>
              <div class="col-md-6">
               <input name = "ctcjoining"  id= "ctcjoining" class="form-control" value="<?=$info['ctcjoining']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Current Functional Designtion</label>
              <div class="col-md-6">
                  <input name = "current_funcdesignation"  id= "current_funcdesignation" class="form-control"  value="<?=$info['current_funcdesignation']?>" >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Current HR Designtion </label>
              <div class="col-md-6">
               <input name = "current_hrdesignation"  id= "current_hrdesignation" class="form-control"  value="<?=$info['current_hrdesignation']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Current HR GRADE</label>
              <div class="col-md-6">
                  <input name = "current_hrgrade"  id= "current_hrgrade" class="form-control"  value="<?=$info['current_hrgrade']?>">
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Current CTC P/M</label>
              <div class="col-md-6">
               <input name = "current_ctc" id= "current_ctc" class="form-control" value="<?=$info['current_ctc']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Confirmation Due On</label>
              <div class="col-md-6">
                  <input name = "due_on"  id= "due_on" class="form-control" value="<?=$info['confirmation_due_on']?>" >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Status Of Employeement </label>
              <div class="col-md-6">
               <input name = "status_of_emp"  id= "status_of_emp" class="form-control" value="<?=$info['status_of_emp']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> </label>
              <div class="col-md-6">
                  
              </div>
            </div>
          </div> -->
           
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">UAN</label>
              <div class="col-md-6">
              <input name = "uan"  id= "uan" class="form-control" value="<?=$info['uan']?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">EPF no.</label>
              <div class="col-md-6">
                  <input name = "epf_no"  id= "epf_no" class="form-control" value="<?=$info['epf_no']?>" > 
              </div>
             </div>
            </div> 
                   <div class="form-group">
                   
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit5" id="save5" value="Update" title="" <?php if($_POST['Submit5']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
            </div>
            
             <div id="menu6" class="tab-pane fade"> <br/>
              <form  name="frm6" id="frm6" class="form-horizontal" action="" method="post">
              	<div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable6">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                                <th width="10%">Company Name</th>
                            	<th width="10%">Work Start Date</th>
                            	<th width="10%">Work End Date</th>
                                <th width="10%">Address</th>
                                <th width="10%">Contatct Person</th>
                                <th width="10%">Email</th>
                                <th width="10%">Website</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr2_doc0">
                               <td><input type="text"  class="form-control  required " name="company_name[0]"  id="company_name[0]" required>
                               </td>
                        		<td><div style="display:inline-block;float:left;"><input type="text" class="required form-control span2" name="start_date[0]"  id="start_date" style="width:160px;" ></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
                                <td><div style="display:inline-block;float:left;"><input type="text" class="required form-control span2" name="end_date[0]"  id="end_date" style="width:160px;" ></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
                                <td><input type="text" class="form-control" name="address[0]"  id="address[0]" ></td>
                            	<td><input type="text" class="form-control" name="contact_person[0]"  id="contact_person[0]"></td>

                                <td><input type="text" class="email form-control" name="email[0]"  id="email[0]" ></td>
                                <td><input type="text" class="email form-control" name="website[0]"  id="website[0]" ></td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                   <div class="form-group">
                   <div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row6" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add  </a><input type="hidden" name="rowno6" id="rowno6" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit6" id="save6" value="Update" title="" <?php if($_POST['Submit6']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
               <form id="frm211" name="frm211" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;">Company Name</th>      
                  <th style="font-size:13px;">Work Start Date</th> 
                  <th style="font-size:13px;">Work End Date</th>
                  <th style="font-size:13px;">Address</th>
                  <th style="font-size:13px;">Contact Person</th> 
                  <th style="font-size:13px;">Email</th>  
                  <th style="font-size:13px;">Website</th>       
                </tr>
            </thead>
            <tbody>
              <?php
			  	 $res1_emp = mysqli_query($link1,"select * from emp_previousemploymentdet where loginid='".$info['loginid']."' order by id desc");
                 while( $row_empdet=  mysqli_fetch_assoc($res1_emp)){
                     
                 
              ?>              
              <tr> 
              <td  align="left"><?=$row_empdet['company_name'];?></td> 
              <td  align="left"><?=$row_empdet['start_date'];?></td> 
              <td  align="left"><?=$row_empdet['end_date'];?></td>   
              <td  align="left"><?=$row_empdet['address'];?></td>   
              <td  align="left"><?=$row_empdet['contact_person'];?></td>                                   
              <td  align="left"><?=$row_empdet['email'];?></td>  
              <td  align="left"><?=$row_empdet['website'];?></td>          
              </tr>
                  <?php } ?>
            </tbody>
          </table>
            </form>
            </div>
            
            <div id="menu7" class="tab-pane fade"><br/>
            	<form  name="frm7" id="frm7" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable7">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                            	<th width="30%">Document Name</th>
                            	<th width="70%">Attachment</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr7_doc0">
                        		<td><input type="text" class="form-control entername required cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                            	<td>
                                	<div style="display:inline-block; float:left">
                                    <input type="file" class="required" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                    <input type="text" id="filename0" readonly style="width:300px;" class="form-control required"/>
                                    </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                    <input type="button" value="Click to upload attachment" id="fakeBrowse0" onClick="HandleBrowseClick(0);" class="btn btn-warning"/>
                            		</div>
                            	</td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                <div class="form-group">
           			<div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row7" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a><input type="hidden" name="rowno7" id="rowno7" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit7" id="save7" value="Update" title="" <?php if($_POST['Submit7']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
            		</div>
          		</div>
              	</form>
                <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;" colspan="4">Uploaded Document</th>                
                </tr>
            </thead>
            <tbody>
              <?php
			  	  $res4 = mysqli_query($link1,"select * from document_attachment where ref_no='".$info['loginid']."' order by document_name");
                  while ($pro_desc_img=  mysqli_fetch_assoc($res4)){ 
                     
                  if($pro_desc_img['document_path'] !="") {  
              ?>              
              <tr> 
              <td class="col-md-2" align="left"><?=$pro_desc_img['document_name'];?></td>                                   
              <td class="col-md-6" align="center"><?php /*?><img src="<?=$pro_desc_img['document_path'];?>" alt="" width="100" height="200" class="img-responsive" /><?php */?><a href="<?=$pro_desc_img['document_path'];?>" target="_blank"><i class="fa fa-download fa-lg" title="view document"></i></a></td>
              <td class="col-md-2" align="center"><a onClick="confirmDel('&delete_prodesc_id=<?=$pro_desc_img['id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete document"></i></a></td>                  
              </tr>
                  <?php }} ?>
            </tbody>
          </table>
            </form>
            </div>
            
            <div id="menu8" class="tab-pane fade"> <br/>
             <form  name="frm8" id="frm8" class="form-horizontal" action="" method="post">
            <div class="panel panel-default table-responsive">
      		 <div class="panel-heading heading1">Salary Pay</div>
      		 <div class="panel-body">
             
              	<div class="form-group">
           		 <div class="col-md-6"><label class="col-md-6 control-label">Basic Pay</label>
            	  <div class="col-md-6">
              	  <input name="basic_pay" id="basic_pay" class="number form-control"  value="<?=$info_salary['basic_pay']?>" onblur="grossearning();"/>
              	</div>
           	 </div>
            <div class="col-md-6"><label class="col-md-6 control-label">HRA</label>
              <div class="col-md-6">
                <input name="hra" id="hra" class="number form-control" value="<?=$info_salary['hra']?>" onblur="grossearning();" />
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Special Allowances</label>
              <div class="col-md-6">
               <input name="special_allowances" id="special_allowances" class="number form-control" value="<?=$info_salary['special_allowances']?>" onblur="grossearning();"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Medical & Eductional Allowances </label>
              <div class="col-md-6">
               <input name="medi_educ_allowances" id="medi_educ_allowances" class="number form-control"  value="<?=$info_salary['medi_educ_allowances']?>" onblur="grossearning();"/>
             		 </div>
           		   </div>            
         	      </div>
             <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Conveyance Allowances</label> 
                  <div class="col-md-6 ">
                  	<input name="conveyance_allowances" id="conveyance_allowances" class="number form-control" value="<?=$info_salary['conveyance_allowances']?>" onblur="grossearning();" />	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label"> Mobile Expenses</label> 
                  <div class="col-md-6 ">
                  	<input name="mobile_expenses" id="mobile_expenses" class="number form-control"  value="<?=$info_salary['mobile_expenses']?>" onblur="grossearning();"/>	
                  </div>    
              </div>  
          </div>  
          
            <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Local Travelling Claims</label> 
                  <div class="col-md-6 ">
                  	<input name="local_travelling_claims" id="local_travelling_claims" class="number form-control" value="<?=$info_salary['local_travelling_claims']?>" onblur="grossearning();" />	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                <label class="col-md-6 control-label">Award </label> 
                <div class="col-md-6">
                  <input name="award" id="award" class="number form-control" value="<?=$info_salary['award']?>"  onblur="grossearning();"/>
                    </div>    
                 </div>  
               </div> 
            <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">Bonus</label>
              <div class="col-md-6">
               <input name = "bonus"  id= "bonus"  value="<?=$info_salary['bonus']?>" class="number form-control " onblur="grossearning();" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Birthday Bonus</label>
              <div class="col-md-6">
                  <input name = "birthday_bonus"  id= "birthday_bonus" value="<?=$info_salary['birthday_bonus']?>"  class="number form-control " onblur="grossearning();" >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Performance Bonus</label>
              <div class="col-md-6">
               <input name = "performance_bonus"    id= "performance_bonus" class="number form-control" value="<?=$info_salary['performance_bonus']?>" onblur="grossearning();" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Out Station Travelling Claims</label>
              <div class="col-md-6">
                  <input name = "out_station_travelling_claims" id= "out_station_travelling_claims" class="number form-control" value="<?=$info_salary['out_station_travelling_claims']?>" onblur="grossearning();" >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Out Station Lodging</label>
              <div class="col-md-6">
               <input name = "out_station_lodging"  id= "out_station_lodging" class="number form-control" value="<?=$info_salary['out_station_lodging']?>"  onblur="grossearning();">
              </div>
            </div>
           
            <div class="col-md-6"><label class="col-md-6 control-label">Previous Claims</label>
              <div class="col-md-6">
                  <input name = "previous_claims"  id= "previous_claims" class="number form-control"  value="<?=$info_salary['previous_claims']?>"  onblur="grossearning();">
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Leave Reimbursement</label>
              <div class="col-md-6">
               <input name = "leave_reimbursement"  id= "leave_reimbursement" class="number form-control"  value="<?=$info_salary['leave_reimbursement']?>" onblur="grossearning();" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Incentive</label>
              <div class="col-md-6">
                  <input name = "incentive"  id= "incentive" class="number form-control"  value="<?=$info_salary['incentive']?>" onblur="grossearning();">
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Medical Reimbursement</label>
              <div class="col-md-6">
               <input name = "medical_reimbursement" id= "medical_reimbursement" class="number form-control" value="<?=$info_salary['medical_reimbursement']?>" onblur="grossearning();">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Travelling Allowances</label>
              <div class="col-md-6">
                  <input name = "travelling_allowances"  id= "travelling_allowances" class="number form-control" value="<?=$info_salary['travelling_allowances']?>" onblur="grossearning();">
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Dearness Allowances </label>
              <div class="col-md-6">
               <input name = "dearness_allowances"  id= "dearness_allowances" class="number form-control" value="<?=$info_salary['dearness_allowances']?>" onblur="grossearning();">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Special Pay </label>
              <div class="col-md-6">
                  <input name = "special_pay"  id= "special_pay" class="number form-control" value="<?=$info_salary['special_pay']?>"  onblur="grossearning();">
              </div>
            </div>
          </div> 
           
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Maintainence Allowance</label>
              <div class="col-md-6">
               <input name = "maintainence_allowance"  id= "maintainence_allowance" class="number form-control" value="<?=$info_salary['maintainence_allowance']?>" onblur="grossearning();">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Special Furniture Allowance</label>
              <div class="col-md-6">
                 <input name = "special_furn_allowance"  id= "special_furn_allowance" class="number form-control" value="<?=$info_salary['special_furn_allowance']?>" onblur="grossearning();"> 
              </div>
             </div>
            </div> 
            
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Family Allowance</label>
              <div class="col-md-6">
               <input name = "family_allowance"  id= "family_allowance" class="number form-control" value="<?=$info_salary['family_allowance']?>" onblur="grossearning();">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Medical Exp Payable</label>
              <div class="col-md-6">
                 <input name = "medical_exp_payable"  id= "medical_exp_payable" class="number form-control" value="<?=$info_salary['medical_exp_payable']?>" onblur="grossearning();"> 
              </div>
             </div>
            </div> 
            
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Others Pay</label>
              <div class="col-md-6">
               <input name = "others_pay"  id= "others_pay" class="number form-control" value="<?=$info_salary['others_pay']?>" onblur="grossearning();">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Gross Earning</label>
              <div class="col-md-6">
                <input name = "gross_earning"  id= "gross_earning" class="form-control" value="<?php if($info_salary['gross_earnings']) {echo$info_salary['gross_earnings'];}else {echo "0.00";}?>" readonly  >
              </div>
             </div>
            </div> 
            
             </div>
           </div>
              
        
            <div class="panel panel-default table-responsive">
      		 <div class="panel-heading heading1">Deduction</div>
      		 <div class="panel-body">
             
              	<div class="form-group">
           		 <div class="col-md-6"><label class="col-md-6 control-label">Advances</label>
            	  <div class="col-md-6">
              	  <input name="deduct_advances" id="deduct_advances" class="number form-control"  value="<?=$info_salary['deduct_advances']?>" onblur="grossdeduct();"/>
              	</div>
           	 </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Loan</label>
              <div class="col-md-6">
                <input name="deduct_loan" id="deduct_loan" class="number form-control" value="<?=$info_salary['deduct_loan']?>" onblur="grossdeduct();" />
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Installment Advance Taken</label>
              <div class="col-md-6">
               <input name="deduct_installment_advance_taken" id="deduct_installment_advance_taken" class="number form-control" value="<?=$info_salary['deduct_installment_advance_taken']?>" onblur="grossdeduct();" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Late Mark</label>
              <div class="col-md-6">
               <input name="deduct_latemark" id="deduct_latemark" class="number form-control"  value="<?=$info_salary['deduct_latemark']?>" onblur="grossdeduct();"/>
             		 </div>
           		   </div>            
         	      </div>
             <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Vehcile_Loan</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_vehcile_loan" id="deduct_vehcile_loan" class="number form-control" value="<?=$info_salary['deduct_vehcile_loan']?>" onblur="grossdeduct();"/>	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Absent Deduction</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_absent" id="deduct_absent" class="number form-control"  value="<?=$info_salary['deduct_absent']?>" onblur="grossdeduct();"/>	
                  </div>    
              </div>  
          </div>  
          
            <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Miscellanous Deduction</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_misc" id="deduct_misc" class="number form-control" value="<?=$info_salary['deduct_misc']?>" onblur="grossdeduct();"/>	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                <label class="col-md-6 control-label">ESI </label> 
                <div class="col-md-6">
                  <input name="deduct_esi" id="deduct_esi" class="number form-control" value="<?=$info_salary['deduct_esi']?>" onblur="grossdeduct();"/>
                    </div>    
                 </div>  
               </div> 
            <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">PF</label>
              <div class="col-md-6">
               <input name = "deduct_pf"  id= "deduct_pf"  value="<?=$info_salary['deduct_pf']?>" class="number form-control "  onblur="grossdeduct();"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Others Deduction</label>
              <div class="col-md-6">
                  <input name = "others_deduction"  id= "others_deduction" value="<?=$info_salary['others_deduction']?>"  class="number form-control " onblur="grossdeduct();">
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Gross Deduction</label>
              <div class="col-md-6">
               <input name = "gross_deduction"    id= "gross_deduction" class="form-control" value="<?php if($info_salary['gross_deduction']) {echo$info_salary['gross_deduction'];}else {echo "0.00";}?>" readonly>

              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
                  
              </div>
            </div>
          </div>            
             </div>
           </div>  
           
           <div class="panel panel-default table-responsive">
      		 <div class="panel-heading heading1"></div>
      		 <div class="panel-body">
             
              	<div class="form-group">
           		 <div class="col-md-6"><label class="col-md-6 control-label">Gross Final Amount</label>
            	  <div class="col-md-6">
              	  <input name="grossfinalamt" id="grossfinalamt" class="form-control" value="<?php if($info_salary['final_grossamt']){ echo $info_salary['final_grossamt']; } else {echo "0.00";}?>" readonly  />
              	</div>
           	 </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
               
              </div>
            </div>
          </div>
          </div>
          </div>
           
              <div class="form-group">
                   
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit8" id="save8" value="Update" title="" <?php if($_POST['Submit8']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$info['empid']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$info['loginid']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='employee_master_list.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
             
            </div>
          </div>
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
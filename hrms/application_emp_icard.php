<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Apply"){
	mysqli_autocommit($link1, false);
	$flag = true;
		
	if($emp_no != "" && $emp_name != "" && $emp_phone != ""){		
		// get some employee info //
		$info = explode("~",getAnyDetails($emp_no,'managerid,designationid','loginid','hrms_employe_master',$link1));		
		// insert all details in table //
		$sql="INSERT INTO hrms_request_icard set emp_id ='".$emp_no."', mgr_id='".$info[0]."', designation = '".$info[1]."' , emergency_no = '".$emp_phone."' , update_date = '".$today."' ,  status  = 'Pending' ,  type = 'IR' ,remark = '".$emp_reason."' ";
		
		$res_qr =  mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		/// check if query is execute or not//
		if(!$res_qr){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
		
		$rid=mysqli_insert_id($link1);
		$sql1="INSERT INTO hrms_request_master set emp_id ='".$emp_no."', name='Apply for ID Card', type = 'IR' , update_date = '".$today."' , mgr_id = '".$info[0]."' ,  request_no  = '".$rid."' ,  status = 'Pending' ";
		
		$res_qr1 =  mysqli_query($link1,$sql1)or die("ER2".mysqli_error($link1));
		/// check if query is execute or not//
		if(!$res_qr1){
			$flag = false;
			$err_msg = "Error 2". mysqli_error($link1) . ".";
		}
		
	}else{
		$msg = "Some required fields are not fulfilled.";
		///// move to parent page
		header("location:application_emp_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
	}
					
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$rid,"ID CARD REQUEST","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Application of ID Card is successfully requested.";
		///// move to parent page
		header("location:application_emp_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:application_emp_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 
 <script>
 	$(document).ready(function(){
        $("#frm1").validate();
    });
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-align-justify"></i> Apply For ID Card </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
      
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Employee No. <span class="red_small">*</span></label>
              <div class="col-md-6" >
              	<input type="text" name="emp_no" id="emp_no" class="form-control" value="<?=$_SESSION['userid'];?>" readonly >					
              </div>
            </div>
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Employee Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="emp_name" id="emp_name" class="form-control" value="<?=$_SESSION['uname'];?>" readonly >			
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Emergency No. <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <input type="text" class="digits form-control" name="emp_phone"  id="emp_phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">	
                  </div>   
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Remark </label> 
                  <div class="col-md-6">
                  	<textarea class="form-control addressfield" id="emp_reason" name="emp_reason" rows="10" ></textarea>
                  </div>    
              </div>  
          </div>
          
         
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Apply"> Apply </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='application_emp_list.php?<?=$pagenav?>'">
              </div>  
          </div>
         
      </form>                      
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
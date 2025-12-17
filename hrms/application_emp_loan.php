<?php
require_once("../config/config.php");
// get some employee info //
$info = explode("~",getAnyDetails($_SESSION['userid'],'managerid,designationid,email,phone,joining_date','loginid','hrms_employe_master',$link1));	
////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Apply"){
	mysqli_autocommit($link1, false);
	$flag = true;
		
	if($emp_no != "" && $emp_name != "" && $emp_phone != ""){		
		
		// insert all details in table //
		$sql="INSERT INTO hrms_request_loan set emp_id ='".$emp_no."', mgr_id='".$info[0]."', designation = '".$info[1]."', doj = '".$emp_doj."', phone = '".$emp_phone."', email = '".$emp_email."', requested_amt = '".$emp_amount."', update_date = '".$today."' ,  status  = 'Pending' ,  type = 'LR' , remark = '".$emp_reason."' ";
		
		$res_qr =  mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		/// check if query is execute or not//
		if(!$res_qr){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
		
		$rid=mysqli_insert_id($link1);
		$sql1="INSERT INTO hrms_request_master set emp_id ='".$emp_no."', name='Apply for Loan', type = 'LR' , update_date = '".$today."' , mgr_id = '".$info[0]."' ,  request_no  = '".$rid."' ,  status = 'Pending' ";
		
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
		dailyActivity($_SESSION['userid'],$rid,"V CARD REQUEST","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Application of Loan is successfully requested.";
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
	$(document).ready(function(){
		$("#frm1").validate();
	});
 </script>
  
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-align-justify"></i> Apply For Loan </h2><br><br>
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
                  <label class="col-md-4 control-label">Designation <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<?php $des = getAnyDetails($info[1],'designame','designationid','hrms_designation_master',$link1); ?>
                  	<input type="text" name="emp_desig" id="emp_desig" class="form-control" value="<?=$des?>" readonly >			
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Mobile No. <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <input type="text" class="form-control" name="emp_phone"  id="emp_phone" value="<?=$info[3];?>" readonly >	
                  </div>   
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Email ID. <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="emp_email" id="emp_email" class="form-control" value="<?=$info[2];?>" readonly >			
                  </div>    
              </div>  
          </div> 
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Date of Joining <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="emp_doj" id="emp_doj" class="form-control" value="<?=$info[4];?>" readonly >			
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Requested Amount <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="emp_amount" id="emp_amount" class="form-control required" required >			
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
<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Request"){
	mysqli_autocommit($link1, false);
	$flag = true;
		
	//// first check whether requested leave is eligble for employee or not //////
    $emp_type	 = getAnyDetails($_SESSION['userid'],"emp_type" ,"loginid" ,"hrms_employe_master" ,$link1);
	//// calulate duration /////
	$leave_time = 0;
	if($duration_of_leave != '' && $duration_of_leave != '0'){
		$leave_time = $duration_of_leave." Mint";
	}else{
		if($to_date != $from_date){
			$leave_time = (daysDifference($to_date,$from_date)+1)." Day";
		}else{
			$leave_time = "1 Day";
		}
	}
			
	if($emp_type == 'Permanent' ||  $emp_type == 'Bench'){	
		// insert all details of leave into leave request table //
		$sql="INSERT INTO hrms_leave_request set empid ='".$_SESSION['userid']."', 	leave_type='".$leave_type."', from_date = '".$from_date."' , to_date = '".$to_date."' , leave_duration = '".$leave_time."' ,  purpose  = '".$purpose ."' ,  description = '".$description."' ,status = '3' , entry_date  = '".$today."'  ,  entry_time  = '".$currtime."' ";
		
		$res_leave =  mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		/// check if query is execute or not//
		if(!$res_leave){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else if ($emp_type == 'Probation'){
		if($leave_type == 'Earn Leave'){
			$flag = false ;
			$msg = "Earn Leave is not eligble for Probation type Employee";
			///// move to parent page
			header("location:leave_apply_list.php?msg=".$msg."&sts=fail".$pagenav);
			exit;
		} else {
			// insert all details of leave into leave request table //
			$sql="INSERT INTO hrms_leave_request set empid ='".$_SESSION['userid']."', 	leave_type='".$leave_type."', from_date = '".$from_date."' , to_date = '".$to_date."' , leave_duration = '".$leave_time."' ,  purpose  = '".$purpose ."' ,  description = '".$description."' ,status = '3' , entry_date  = '".$today."'  ,  entry_time  = '".$currtime."' ";
			
			$res_leave =  mysqli_query($link1,$sql)or die("ER2".mysqli_error($link1));
			/// check if query is execute or not//
			if(!$res_leave){
				$flag = false;
				$err_msg = "Error 2". mysqli_error($link1) . ".";
			}
		}
	}else if ($emp_type == 'Training' || $emp_type == 'Stipend') {
		if($leave_type == 'Causal Leave/SL'){
			// insert all details of leave into leave request table //
			$sql="INSERT INTO hrms_leave_request set empid ='".$_SESSION['userid']."', 	leave_type='".$leave_type."', from_date = '".$from_date."' , to_date = '".$to_date."' , leave_duration = '".$leave_time."' ,  purpose  = '".$purpose ."' ,  description = '".$description."' ,status = '3' , entry_date  = '".$today."'  ,  entry_time  = '".$currtime."' ";
				
			$res_leave =  mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
			/// check if query is execute or not//
			if(!$res_leave){
				$flag = false;
				$err_msg = "Error 3". mysqli_error($link1) . ".";
			}
		}
		else {
			$flag = false;
			$msg = "You are not eligible for taken" .$leave_type;
			///// move to parent page
			header("location:leave_apply_list.php?msg=".$msg."&sts=fail".$pagenav);
			exit;
		}
	} else {
		$flag = false;
	}
					
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"LEAVE REQUEST","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Leave is successfully requested.";
		///// move to parent page
		header("location:leave_apply_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:leave_apply_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 <script>
 	$(document).ready(function(){
		$('#from_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	$(document).ready(function(){
		$('#to_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	function getDisplay(val){	
		if(val == 'Short Leave' || val == 'Half Day'){
			document.getElementById("duration").style.display = ""; 
		}else{
			document.getElementById("duration").style.display = "none"; 
			document.getElementById("duration_of_leave").value = "";
		}
	}
	function date_range(){
		var fdate = document.getElementById("from_date").value;
		var tdate = document.getElementById("to_date").value;
		if(fdate != '' && tdate != ''){
			if(fdate <= tdate){
			}else{
				alert("To Date is greater then or equal to From Date.");
				document.getElementById("to_date").value = '';
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
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-lightbulb-o"></i> Leave Apply </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
      
      	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Type of Leave <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  <select name="leave_type" id="leave_type" class="form-control required"  onChange="getDisplay(this.value);" required >
                      <option value="">--Please Select--</option>
                      <?php
                      $leave_query="SELECT distinct(type)  FROM hrms_leave_master order by type";
                      $check_leave=mysqli_query($link1,$leave_query);
                      while($br_leave = mysqli_fetch_array($check_leave)){
                      ?>
                      <option value="<?=$br_leave['type']?>"<?php if(isset($_REQUEST['type']) == $br_leave['type']) { echo 'selected'; }?>><?php echo $br_leave['type']?></option>
                      <?php }?>
                  </select>
                  </div>    
              </div>  
          </div>
        
          <div class="form-group"  id="duration" style="display:none">
            <div class="col-md-12"><label class="col-md-4 control-label">Duration Of Leave</label>
              <div class="col-md-6" >
              	<input type="text" name="duration_of_leave" id="duration_of_leave" class="form-control" value="" ><span class="red_small">*(enter minutes only)</span>					
              </div>
            </div>
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">From Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2" name="from_date"  id="from_date" onChange="date_range();" autocomplete="off"/>
                        </div>
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">To Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2" name="to_date"  id="to_date" onChange="date_range();" autocomplete="off"/>
                        </div>
                  </div>    
              </div>  
          </div>  
          
           <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Purpose <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="purpose" id="purpose" class="form-control required" required />
                  </div>    
              </div>  
          </div>    
     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Description <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<textarea class="form-control required" rows="10" id="description" name="description" required ></textarea>   
                  </div>    
              </div>  
          </div>
         
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Request"> Request </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='leave_apply_list.php?<?=$pagenav?>'">
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
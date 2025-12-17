<?php
require_once("../config/config.php");
$entryno = mysqli_num_rows(mysqli_query($link1,"SELECT empid from hrms_emp_resign where empid='".$_SESSION['userid']."'"));
//// get information trom leave request table/////
$info = mysqli_fetch_array(mysqli_query($link1,"select * from hrms_emp_resign where empid = '".$_SESSION['userid']."' "));

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Request"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// Check duplicate department ////
	if($entryno == 0){
		$file_path = "";
		if($_FILES['resign_doc']["name"]!=''){	
		   //// upload doc into folder ////
		 	$file_name =$_FILES['resign_doc']['name'];
			$file_tmp =$_FILES['resign_doc']['tmp_name'];
			$file_path="../doc_attach/resign_doc/$today.$file_name";
			move_uploaded_file($file_tmp,$file_path);	
		}	
			
		////// get employee manger id and employee email id /////
		$empdetails = explode("~" ,getAnyDetails($_SESSION['userid'],"managerid,email","loginid","hrms_employe_master",$link1));
		
		///// INSERT INTO emp_resign  TABLE////
		$usr_add="insert into hrms_emp_resign set empid  ='".$_SESSION['userid']."', managerid ='".$empdetails[0]."', reason='".$reason."',  reason_desc ='".$reason_details."', offer_lastdate ='".$last_date."',  document = '".$file_path."'  , 	enter_date = '".$today."' , entry_by = '".$_SESSION['userid']."'  ,status = 'PFA'  ";

		$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));

		/// check if query is execute or not//
		if(!$res_add){
			$flag = false;
			$err_msg = "Error2221". mysqli_error($link1) . ".";
		}	
				
		//////// get manager email id ////////////////////////////////////////////////////////////////////////////
		$mangeremail	 = getAnyDetails($empdetails[0] ,"email" ,"loginid","hrms_employe_master",$link1);		
	  
		///// mail send code start ////
		$to = "sulakshna.bhardwaj@gmail.com"; //// fixed hr  email id ///
		$cc= $mangeremail;  /// employee manager id ////
		$from= $empdetails[1];  //// emp email id //////
		
		$subject = $reason;	
		$message = $reason_details;	
		$headers1 = "MIME-Version: 1.0\r\n";
		$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers1 .= "X-Priority: 3\r\n";
		$headers1 .= "X-Mailer: PHP". phpversion() ."\r\n";
		$headers1 .= "From:" .$empdetails[1] . "\r\n" ."CC:" .$cc;
		
		$checkmail = mail($to,$subject,$message, $headers1);	
		if($checkmail) {
		$msg = "Mail is successfully sent";	
		} else {
		$msg = "Mail is unsuccessfully sent";	
		}	
					
	}else{
		$flag = false;
		$msg = "You already apply for Resignation.";
		///// move to parent page
		header("location:resign_request.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"RESIGN","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Resignation is successfully requested.";
		///// move to parent page
		header("location:resign_request.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:resign_request.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 <script>
 	$(document).ready(function() {
		$('#last_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
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
      <h2 align="center"><i class="fa fa-puzzle-piece"></i> Resign Request </h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br><br>
      <?php if($entryno == 0){ ?>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Reason <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="reason" id="reason" class="form-control required" required />
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Reason Details <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<textarea class="form-control required" id="reason_details" name="reason_details" rows="10" required ></textarea>
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Offer Last Working Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="last_date"  id="last_date" required>
                        </div>
                  </div>    
              </div>  
          </div>      
     
    	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Resign Document </label> 
                  <div class="col-md-6">
                  	<input type="file" name="resign_doc" id="resign_doc" class="form-control" />
                  	<!--accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,image/*,application/pdf,.xls,.doc,.docx,.txt "-->
                  </div>    
              </div>  
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Request"> Request </button>  
              </div>  
          </div>
         
      </form>   
      <?php }else{ ?>    
      
      <div class="panel-group">
        <div class="panel panel-info table-responsive">
            <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Resignation Details</div>
             <div class="panel-body">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%"><label class="control-label">Employee Name</label></td>
                    <td width="30%"><?php echo getAnyDetails($info['empid'],"empname","loginid","hrms_employe_master",$link1);?></td>
                    <td width="20%"><label class="control-label">Employee Id</label></td>
                    <td width="30%"><?php echo $info['empid'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Resign Date</label></td>
                    <td><?php echo $info['offer_lastdate'];?></td>
                    <td><label class="control-label">Manager Name</label></td>
                    <td><?= getAnyDetails($info['managerid'],"empname","loginid","hrms_employe_master",$link1);?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Reason</label></td>
                    <td><?php echo $info['reason'];?></td>
                    <td><label class="control-label">Reason Description</label></td>
                    <td><?php echo $info["reason_desc"];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">EMP Code</label></td>
                    <td><?php echo getAnyDetails($info['empid'],"sapid","loginid","hrms_employe_master",$link1);?></td>
                    <td><label class="control-label">Attached Document</label></td>
                    <td><?php if($info['document']) {?><a href='<?=$info['document']?>' target='_blank' title='download'><i class='fa fa-download ' title='Download Document'></i></a><?php }?></td>
                  </tr>
                </tbody>
              </table>
            </div><!--close panel body-->
        </div><!--close panel-->
		  <br><br>
          <div class="panel panel-info table-responsive">
         <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Approve / Reject</div>
             <div class="panel-body">
           <table class="table table-bordered" width="100%">
            <tbody>
             <tr>
                    <td width="20%" align="center"><label class="control-label">HR Approve Offer Date</label></td>
                    <td width="30%"><input name="last_date1" id="last_date1" type="text" value="<?php if($info['approve_date']!='0000-00-00'){ $info['approve_date']; }else{ echo ""; }?>"  readonly class=" form-control"/></td>
                    <td width="20%" align="center"><label class="control-label">Status</label></td>
                    <td width="30%"><input name="status" id="status" type="text" value="<?php if($info['status'] == '4') { echo "Approve";} else if($info['status'] == '5') { echo "Reject" ;} else {  echo "";}?>"  readonly class=" form-control"/></td>
                  </tr>
                  <tr>
                    <td width="20%" align="center"><label class="control-label">Approval Remark</label></td>
                    <td width="30%"><textarea id="remark" name="remark" class="form-control required" required readonly="readonly"><?=$info['appr_remark']?></textarea></td>
                    <td width="20%" align="center"><label class="control-label"></label></td>
                    <td width="30%"></td>
                  </tr>
                
                </tbody>
              </table>
            </div><!--close panel body-->
        </div><!--close panel-->
		  <br><br>
        </div>
      
      <?php } ?>               
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
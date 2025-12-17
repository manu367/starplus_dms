<?php
require_once("../config/config.php");

//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
   if ($_FILES["att_file"]["name"]) {
	$check_flag=0;   
	require_once "../includes/simplexlsx.class.php";
	$xlsx = new SimpleXLSX( $_FILES['att_file']['tmp_name'] );	
	move_uploaded_file($_FILES["att_file"]["tmp_name"],"../upload/lead_upload/".$now.$_FILES["att_file"]["name"]);
	$f_name=$today.$currtime.$_FILES["att_file"]["name"];
	
	list($cols) = $xlsx->dimension();	
	foreach( $xlsx->rows() as $k => $r) {
	 if ($k == 0 || $k == 1) continue; // skip first row 
	  for( $i = 0; $i < count($k); $i++)
	  {
		  /// check excel row data
	      if($r[0]=='' && $r[1]=='' && $r[2]==''){
			  
		  }
		  else if($r[0]=="EOF"){
		       $eof="1";
		  }else{
			  ////Make Variable for each element of excel//////
			  $customer=$r[0];
			  $sales_executive=$r[1];
			  $state=$r[2];
			  $city=$r[3];
			  $address=$r[4];
			  $priority=$r[5];
			  $remark=$r[6];
			  $transfer_to=$r[7];
			  $source=$r[8];
			  
			  if($customer !=''  && $sales_executive != '' && $state != '' && $city != '' && $address != '' && $priority != ''  && $remark != '' && $transfer_to !='' && $source !=''){
	          #################### first check whether transfer to exist in employe master table or not #########################3
			   $check_emp= mysqli_query($link1 ,"select empid from hrms_employe_master where empname = '".$transfer_to."' ");
			  ###### fetch sales executive id ###############################33
			  $userid = mysqli_fetch_array(mysqli_query($link1,"select username from admin_users where name = '".$sales_executive."' ")); 
			  if($userid['username']){
				  $sale_userid = $userid['username'];
				  }else {
				  $sale_userid = $_SESSION['userid']; 	  
			   }
			   if(mysqli_num_rows($check_emp)>0) {
				   $check_flag = 1;
				    $emp_det = mysqli_fetch_array($check_emp);
					$ref=mysqli_query($link1,"select max(lid) as cnt from sf_lead_master order by lid desc");
					$row = mysqli_fetch_assoc($ref);
					$result=$row[cnt]+1;
					$pad=str_pad($result,3,"0",STR_PAD_LEFT);  
					$reference="LD".$pad;
					mysqli_query($link1,"insert into sf_lead_master set partyid='".$customer."', party_address='".cleanData($address)."', intial_remark='".$remark."', priority='".$priority."', reference='".$reference."', type='Lead', category='', tdate='".$today."', status='7', ip='".$ip."', sales_executive='".$sale_userid."', dept_id='".$emp_det['empid']."', party_state='".$state."', party_city='".$city."', lead_source='".$source."', create_location='".$_SESSION['mapped_location']."', create_by='".$_SESSION['userid']."'");
			   }####################  employee check ondition 
			   else {
				    $msg="Transfer To Not exist in Employe Master.Please Upload it again.";	
					header("Location:lead_add.php?msg=$msg&sts=fail".$pagenav);
					exit;
				   
				   }
			  }############# empty file check condition  
			  else {				  
				    $msg="Excel cannot be empty.Please Upload it again.";	
					header("Location:lead_add.php?msg=$msg&sts=fail".$pagenav);
					exit;
				  
				  }
					
		  }
	  }
	}//Close For loop
	//// check excel file is completely uploaded///
	if($check_flag=='1'){
	    $msg="File is  uploaded Successfully.";	
		header("Location:lead_add.php?msg=$msg&sts=success".$pagenav);
        exit;
    }
	else{
		$msg="File is not uploaded Properly.Please Upload it again.";	
		header("Location:lead_add.php?msg=$msg&sts=fail".$pagenav);
        exit;
	}
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

 <script type="text/javascript">

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
      <h2 align="center"><i class="fa fa-upload"></i> Lead Data Upload  </h2>
      <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_LEAD.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/><br/><br/><br/>
    
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">   
   <div class="panel-group">
     <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Upload</div>
      <div class="panel-body">
      	<div class="form-group">
            <div class="col-md-10"><label class="col-md-8 control-label">Attach File (Attach only <strong>.xlsx (Excel Workbook)</strong> file)  <span class="red_small">*</span></label>
              <div class="col-md-4">              
               <input type="file" class="form-control required" name="att_file" id="att_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required/>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-success" name="Submit" id="upload" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='lead_add.php?<?=$pagenav?>'">
            </div>
          </div>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
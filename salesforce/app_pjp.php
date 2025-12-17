<?php
require_once("../config/config.php");
@extract($_POST);
$date=date("Y-m-d");
$date2=date("Y-m-01");
////// final submit form ////

if($_POST['Submit']=='Save'){
	if(!empty($assign_to)){
		
		$res_ref = mysqli_query($link1,"SELECT MAX(ref_id) as mno FROM pjp_master");
			$row_ref = mysqli_fetch_assoc($res_ref);
			$next_mno = $row_ref["mno"] + 1;
			$ref_no = "PJP/".date("Ymd")."/".$next_mno;
		#######
		$datediff=daysDifference($tdate,$fdate);
		
		for($i=0; $i <= $datediff; $i++){
			$makedate = date('Y-m-d', strtotime($fdate. ' + '.$i.' days'));
			//$is_sunday = date('l', strtotime($makedate));

		foreach($assign_to as  $value){  ####### Loop with Assigned 
			///// make FB ref no
			
			foreach($task as $value2){  ####### Loop with Task 
			//echo "INSERT INTO pjp_data SET document_no  = '".$ref_no."', pjp_name ='".$_POST['pjp_name']."',plan_date='".$makedate."',task='".$value2."', entry_date ='".$datetime."',entry_by='".$_SESSION['userid']."', assigned_user='".$value."' ";
			
			$sql2=mysqli_query($link1,"INSERT INTO pjp_data SET document_no  = '".$ref_no."', pjp_name ='".$_POST['pjp_name']."',plan_date='".$makedate."',task='".$value2."', entry_date ='".$datetime."',entry_by='".$_SESSION['userid']."',task_count='".$_POST["targt"]."', assigned_user='".$value."', visit_area='".$_POST["visit_area"]."'")or die("ER4".mysqli_error($link1)); 
			
			}
		}
		
		}
		
		$sql=mysqli_query($link1,"INSERT INTO pjp_master SET document_no  = '".$ref_no."', ref_id = '".$next_mno."', pjp_name ='".$_POST['pjp_name']."',start_date='".$fdate."',end_date='".$tdate."', entry_date ='".$datetime."',entry_by='".$_SESSION['userid']."' ")or die("ER4".mysqli_error($link1)); 
    	//return message
		$msg="You have successfully Created PJP ";
	}else{
		$msg="Assign to was not selected";
	}
	///// move to parent page
    header("Location:pjp_master.php?msg=".$msg."".$pagenav);
	exit;
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
	$(document).ready(function() {
		$('#assign_to').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"250",
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			maxHeight: 300	
		});
	});

 </script>
<script>
$(document).ready(function() {
	$('#task').multiselect({
		includeSelectAllOption: true,
		buttonWidth:"250",
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		maxHeight: 300	
	});
	
});
function checkval(){
	var pc = $('#task').val();
//	var br = $('#brand_name').val();
	if(pc==null){
		$('#tsermsg').html("Please select");
		//$('#brermsg').html("");
		return false;
	}/*else if(br==null){
		$('#brermsg').html("Please select");
		$('#pcermsg').html("");
		return false;
	}*/else{
		$('#tsermsg').html("");
		//$('#brermsg').html("");
		return true;
	}
}
</script>

<style>
.red_small{
	color:red;
}
.multiselect-container {
	width: 250px;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
     		<h2 align="center"><i class="fa fa-hourglass-half"></i>&nbsp;&nbsp;Add PJP</h2><br/><br/> 
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          	<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">   
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label">PJP Name <span class="red_small">*</span></label>
              <div class="col-md-6">
			  <input name="pjp_name" type="text"   id="pjp_name" class="form-control required">              
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Task<span class="red_small">*</span></label>
              <div class="col-md-6">
              <select name='task[]' id='task' class="form-control required" multiple="multiple"  required>
                 <?php
				 $res_task = mysqli_query($link1,"SELECT task_name FROM pjptask_master WHERE status='A' order by task_name");	
				 while($row_task = mysqli_fetch_assoc($res_task)){
				 ?>
                 <option value="<?=$row_task["task_name"]?>"><?=$row_task["task_name"]?></option>
                 <?php 
				 }
				 ?>
              </select>
               <span id="tsermsg" style="color:#FF0000"></span>
              </div>
            </div>
          </div>
		   <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">Start Date<span class="red_small">*</span></label>
               <div class="col-md-3" style="display:inline-block;float:left;"><input type="date" class="form-control" name="fdate" autocomplete="off" id="fdate" style="width:250px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date2;}?>"></div><!--<div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>-->
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">End Date<span class="red_small">*</span></label>
              <div class="col-md-3" style="display:inline-block;float:left;"><input type="date" class="form-control" name="tdate" autocomplete="off" id="tdate" style="width:250px;" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"></div><!--<div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>-->
            </div>		
			</div>
            <div class="form-group">
			<div class="col-md-6"><label class="col-md-5 control-label">Team<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name='assign_to[]' id='assign_to' class="form-control required" required multiple="multiple">
                	 <?php
					 $res_user = mysqli_query($link1,"SELECT username,name,oth_empid FROM admin_users WHERE status='active' and oth_empid!='' order by name");	
					 while($row_user = mysqli_fetch_assoc($res_user)){
					 ?>
					 <option value="<?=$row_user["username"]?>" data-token="<?=$row_user["name"]?>"><?=$row_user["name"]." | ".$row_user["oth_empid"]?></option>
					 <?php 
					 }
					 ?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Task Target Count</label>
              <div class="col-md-6">
              		<input class="form-control digits" type="text" name="targt"/>
              </div>
            </div>
			</div>
            <div class="form-group">
			<div class="col-md-6"><label class="col-md-5 control-label">Visit Area</label>
              <div class="col-md-6">
                <input class="form-control" type="text" name="visit_area"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-6">
              		
              </div>
            </div>
			</div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="submit">
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='pjp_master.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
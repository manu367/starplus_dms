<?php
////// Function ID ///////
$fun_id = array("a"=>array(85));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$id = base64_decode($_REQUEST['id']);
////// Fetch informations //////
$sel_usr="select * from hrms_subdepartment_master where subdeptid='".$id."' ";
$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
$sel_result=mysqli_fetch_assoc($sel_res12);

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// Check duplicate department ////
	$name = ucwords($sub_department);
	if((mysqli_num_rows(mysqli_query($link1,"select subdeptid from hrms_subdepartment_master where subdept='".$name."' ")) == 0) || $name==$sel_result['subdept']){
		///////// main queries //////////
		$add_dept="UPDATE hrms_subdepartment_master set  subdept ='".$name."',status='".$status."',updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' WHERE subdeptid ='".$id."' ";
		$res_dept=mysqli_query($link1,$add_dept);	
		$sdptid = mysqli_insert_id($link1); 
		/// check if query is execute or not//
		if(!$res_dept){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = $name." Department Name is already exist.";
		///// move to parent page
		header("location:subdepartment_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$sdptid,"SUB DEPARTMENT","UPDATE",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Sub Department is successfully updated.";
		///// move to parent page
		header("location:subdepartment_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:subdepartment_list.php?msg=".$msg."&sts=fail".$pagenav);
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

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-building-o"></i> Edit Department </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
     	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Department Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<select name="department" id="department" class="form-control required" disabled>
                  <option value="">Please Select</option>
                  <?php
                $res_pro = mysqli_query($link1,"select departmentid ,dname from hrms_department_master WHERE status='1' order by dname"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['departmentid']."~".$row_pro['dname']?>"<?php if($sel_result["departmentid"]==$row_pro['departmentid']){ echo "selected";}?>><?=$row_pro['dname']?></option>
                  <?php } ?>
                </select>
                  </div>    
              </div>  
          </div>
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Sub Department Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <input type="text" name="sub_department" id="sub_department" value="<?=$sel_result['subdept']?>" class="form-control required" required />
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Status</label> 
                  <div class="col-md-6">
                  	<select name="status" id="status" class="form-control"  >
                        <option value="1"<?php if("1"==$sel_result['status']){ echo "selected";}?>>Active</option>
                        <option value="2"<?php if("2"==$sel_result['status']){ echo "selected";}?>>Deactive</option>
                    </select>
                  </div>    
              </div>  
          </div>
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Save"> Save </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='subdepartment_list.php?<?=$pagenav?>'">
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
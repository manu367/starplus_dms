<?php
require_once("../config/config.php");
////// final submit form ////
if($_POST['Submit']=="Save"){
	if(mysqli_num_rows(mysqli_query($link1,"select parameter_id from pr_parameter_master where parameter_name='".$_POST['parameter_name']."' and sub_categaory_id='".$_POST['product_subcat']."'"))==0){
		$res = mysqli_query($link1,"insert into pr_parameter_master set sub_categaory_id='".$_POST['product_subcat']."',parameter_name='".ucwords($_POST['parameter_name'])."',status='".$_POST['status']."',update_date='".date("Y-m-d")."'");
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$_POST['parameter_name'],"PARAMETER","ADD",$ip,$link1,"");	
		//return message
		$msg="You have successfully saved a new parameter like ".$_POST['parameter_name'];
	}
	else{
		////// return message
		$msg="Entered parameter is already exist. Please add new parameter only.";
	}
	///// move to parent page
	header("Location:parameter_master.php?msg=".$msg."".$pagenav);
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
 
<script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-list-ul"></i>&nbsp;&nbsp;Add New Parameter</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product Sub Category<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="product_subcat" id="product_subcat" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                  <?php
				$circlequery="select psubcatid,prod_sub_cat from product_sub_category where status='1' order by prod_sub_cat";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$circlearr['psubcatid']){ echo "selected";}?>><?=ucwords($circlearr['prod_sub_cat'])?></option>
				<?php 
				}
                ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Parameter Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="parameter_name" class="form-control required mastername"  id="parameter_name" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name='status' id='status' class="form-control required"  required/>
				 <option value="">--Please Select--</option>
                    <option value="1">Active</option>
                    <option value="2">Deactive</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="Add Parameter">
             
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='parameter_master.php?<?=$pagenav?>'">
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
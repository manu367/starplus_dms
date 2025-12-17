<?php
require_once("../config/config.php");
$getid=$_REQUEST['id'];
////// get details of selected city////

$rs=mysqli_query($link1,"select * from make_master where id='$getid'")or die(mysqli_error());
  $row2=mysqli_fetch_array($rs);
////// final submit form ////
if($_POST['Submit']=="Update"){
///// Update tax details if needed
	if(mysqli_query($link1,"update make_master set make='".ucwords($_POST['brand_name'])."',status='".$_POST['status']."',updatedate='".$datetime."' where  id='".$getid."' ")or die("ER1".mysqli_error($link1)))

{
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST['brand_name'],"BRAND","UPDATE",$ip,$link1,"");
	//return message
	$msg="You have successfully update brand status details";
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
	///// move to parent page
   header("Location:brand_master.php?msg=".$msg."".$pagenav);
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
 </script>
 <style>
.red_small{
	color:red;
}
</style>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-tag"></i>&nbsp;&nbsp;Edit Brand</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Brand Name<span class="red_small">*</span></label>
            <div class="col-md-4">
                <input type="text" name="brand_name" class="form-control required" value="<?php echo $row2['make']; ?>" id="brand_name" required/>
	    </div>
            </div>
          </div>
           <div class="form-group"> 
		   <div class="col-md-12"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name='status' id='status' class="form-control required"  required/>		
                    <option value="1"<?php if($row2['status']=="1"){ echo "selected";}?>>Active</option>
                    <option value="2"<?php if($row2['status']=="2"){ echo "selected";}?>>Deactive</option>
                 </select>
              </div>
            </div>
           </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
            <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="Update Status">             
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='brand_master.php?<?=$pagenav?>'">
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
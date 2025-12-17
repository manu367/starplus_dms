<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected color////
$rs=mysqli_query($link1,"select color from colour_master where id='$getid'")or die(mysqli_error($link1));
  $row2=mysqli_fetch_array($rs);
  ////// final submit form ////
if($_POST['Submit']=="Update"){
if(mysqli_num_rows(mysqli_query($link1,"select id from colour_master where color='".$_POST['color_name']."' "))<1){	
///// Update tax details if needed
	if(mysqli_query($link1,"update colour_master set color='".$_POST['color_name']."' where  id='".$getid."' ")or die("ER1".mysqli_error($link1))){
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$row2['color'],"COLOR","UPDATE",$ip,$link1,"");
	//return message
	$msg="You have successfully update color name ".$row2['color'];
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
   
   }
else{
	////// return message
	$msg="Entered color is already exist. Please add new color only.";
}
	///// move to parent page
    header("Location:color_master.php?msg=".$msg."".$pagenav);
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
 <style>
.red_small{
	color:red;
}
</style>
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
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
      <h2 align="center"><i class="fa fa-adjust"></i>&nbsp;&nbsp;Color Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Color Name<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="color_name" class="form-control required"  id="color_name" value="<?php echo $row2['color']; ?>" required/>
                 
              </div>
            </div>
           
          </div>
        
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="Update Status">
             
              
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='color_master.php?<?=$pagenav?>'">
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